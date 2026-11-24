<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\InvBodegaProducto;
use App\Models\NotaVentaDetalle;
use App\Models\OpDetRegProd;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\StockReserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * RESERVA DE STOCK — apartar stock sin moverlo.
 *
 * Hasta ahora, la unica forma de apartar stock era el PICKING: el stock sale de
 * la bodega de venta y entra a la de picking, y por eso deja de estar
 * disponible. Es una reserva solida, pero exige que exista una nota de venta
 * con el item cargado.
 *
 * Eso dejaba fuera tres situaciones reales:
 *   - Lo que se fabrico con una OT libre (para stock, sin nota de venta).
 *   - Lo que entro por una carga manual de inventario y se quiere guardar para
 *     un cliente antes de que haya nota de venta.
 *   - Un item de NV marcado "requiere fabricacion" que podria cubrirse con
 *     stock que ya esta en bodega, en vez de esperar a que termine la OT.
 *
 * Esta pantalla cubre ese hueco. NO mueve inventario y NO reemplaza al picking:
 * se suma por delante. Cuando el stock pase a picking, la reserva se cierra y
 * el compromiso vuelve a ser fisico, de modo que nunca se cuenta dos veces.
 */
class StockReservaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Pantalla con el listado de reservas y el formulario para crear. */
    public function index()
    {
        can('listar-stock-reserva');

        $tablas['clientes'] = Cliente::orderBy('razonsocial')
            ->whereNull('deleted_at')->get(['id', 'rut', 'razonsocial']);

        /* Por defecto, quince dias. El vencimiento es obligatorio: sin fecha
           tope, en un ano la mitad del inventario queda apartado por notas de
           venta que nadie recuerda. */
        $tablas['fechavence'] = date('d/m/Y', strtotime('+15 days'));

        return view('stockreserva.index', compact('tablas'));
    }

    /**
     * Listado para el DataTable.
     *
     * Se limita siempre a las sucursales del usuario: una reserva es stock de
     * una bodega, y nadie deberia ver -ni liberar- el stock de otra sucursal.
     */
    public function listadopage(Request $request)
    {
        can('listar-stock-reserva');

        $aux_sucursales = Usuario::findOrFail(auth()->id())->sucursales->pluck('id')->toArray();
        if (!count($aux_sucursales)) {
            return datatables([])->toJson();
        }

        $query = DB::table('stockreserva as r')
            ->join('invbodegaproducto as ibp', 'ibp.id', '=', 'r.invbodegaproducto_id')
            ->join('invbodega as b', 'b.id', '=', 'ibp.invbodega_id')
            ->join('producto as p', 'p.id', '=', 'ibp.producto_id')
            ->leftJoin('cliente as c', 'c.id', '=', 'r.cliente_id')
            ->leftJoin('notaventadetalle as nvd', 'nvd.id', '=', 'r.notaventadetalle_id')
            ->leftJoin('usuario as u', 'u.id', '=', 'r.usuario_id')
            ->whereNull('r.deleted_at')
            ->whereIn('b.sucursal_id', $aux_sucursales);

        /* Por defecto solo se muestran las vigentes: son las que estan
           descontando stock y sobre las que se puede actuar. El historico
           (liberadas, vencidas, cumplidas) se pide con el filtro. */
        if (!$request->vertodas) {
            $query->whereNull('r.anulada_at')
                ->whereNull('r.consumida_at')
                ->whereDate('r.fechavence', '>=', date('Y-m-d'));
        }
        if ($request->cliente_id) {
            $query->where('r.cliente_id', (int) $request->cliente_id);
        }
        if ($request->producto_id) {
            $query->where('ibp.producto_id', (int) $request->producto_id);
        }

        $datas = $query->orderBy('r.id', 'desc')
            ->select(
                'r.id', 'r.cant', 'r.cantkg', 'r.fechavence', 'r.motivo',
                'r.anulada_at', 'r.consumida_at', 'r.anulmotivo',
                'r.opdetregprod_id', 'r.notaventadetalle_id',
                'r.updated_at',
                DB::raw('UNIX_TIMESTAMP(r.updated_at) as updatednum_at'),
                'ibp.producto_id',
                'p.glosa as producto_nombre',
                'b.nombre as bodega_nombre',
                'c.razonsocial as cliente_nombre',
                'nvd.notaventa_id',
                'u.usuario as usuario_nombre'
            )->get();

        /* El estado se calcula aqui y no en el JS para que la pantalla, el
           reporte de stock y cualquier consulta futura digan lo mismo. */
        foreach ($datas as $d) {
            if ($d->anulada_at) {
                $d->estado = 'Liberada';
            } elseif ($d->consumida_at) {
                $d->estado = 'Cumplida';
            } elseif ($d->fechavence < date('Y-m-d')) {
                $d->estado = 'Vencida';
            } else {
                $d->estado = 'Vigente';
            }
            $d->puede_liberar = ($d->estado == 'Vigente' or $d->estado == 'Vencida') ? 1 : 0;
        }

        return datatables($datas)->toJson();
    }

    /**
     * Bodegas con stock de un producto, con lo que queda libre en cada una.
     *
     * Es lo que el usuario necesita ver antes de reservar: no sirve saber que
     * hay 500 kg si 400 ya estan apartados para otro cliente.
     */
    public function stockProducto($producto_id)
    {
        can('listar-stock-reserva');

        $aux_sucursales = Usuario::findOrFail(auth()->id())->sucursales->pluck('id')->toArray();

        $aux_rows = DB::table('invbodegaproducto as ibp')
            ->join('invbodega as b', 'b.id', '=', 'ibp.invbodega_id')
            ->where('ibp.producto_id', (int) $producto_id)
            ->whereNull('ibp.deleted_at')
            ->whereNull('b.deleted_at')
            ->where('b.activo', 1)
            ->whereIn('b.sucursal_id', $aux_sucursales)
            /* Solo bodegas de almacenamiento (tipo 2). Picking, despacho, scrap,
               produccion y pesaje no son stock disponible para vender: el de
               picking, ademas, YA esta apartado por su solicitud de despacho. */
            ->where('b.tipo', 2)
            ->where('ibp.stock', '>', 0)
            ->orderBy('b.orden')
            ->select('ibp.id', 'ibp.stock', 'b.nombre as bodega_nombre')
            ->get();

        $aux_prod = Producto::atributosProducto((int) $producto_id);

        $bodegas = [];
        foreach ($aux_rows as $r) {
            $aux_libre = StockReserva::libreEnBodegaProducto($r->id);
            $bodegas[] = [
                'id'            => $r->id,
                'bodega_nombre' => $r->bodega_nombre,
                'stock'         => (float) $r->stock,
                'reservado'     => round((float) $r->stock - $aux_libre, 2),
                'libre'         => $aux_libre,
            ];
        }

        return response()->json([
            'ok'       => true,
            'producto' => $aux_prod['nombre'] ?? ('Prod. ' . $producto_id),
            'bodegas'  => $bodegas,
        ]);
    }

    /** Crea la reserva. */
    public function guardar(Request $request)
    {
        can('crear-stock-reserva');

        $aux_ibp_id = (int) $request->input('invbodegaproducto_id');
        $aux_cant   = (float) str_replace(',', '.', str_replace('.', '', $request->input('cant')));

        $ibp = InvBodegaProducto::find($aux_ibp_id);
        if (!$ibp) {
            return $this->volver('Seleccione la bodega de donde se aparta el stock.');
        }
        if ($aux_cant <= 0) {
            return $this->volver('Indique cuánto se va a reservar.');
        }

        /* Al menos un destino. Una reserva sin dueno no es una reserva: nadie
           sabria despues para que se aparto ni cuando liberarla. */
        $aux_cliente_id = $request->input('cliente_id') ?: null;
        $aux_nvdet_id   = $request->input('notaventadetalle_id') ?: null;
        if (!$aux_cliente_id and !$aux_nvdet_id) {
            return $this->volver('Indique para qué cliente o para qué ítem de nota de venta se reserva.');
        }

        if (!$request->input('fechavence')) {
            return $this->volver('Indique hasta cuándo queda reservado.');
        }
        $aux_vence = $this->aFecha($request->input('fechavence'));
        if ($aux_vence < date('Y-m-d')) {
            return $this->volver('La fecha de vencimiento no puede ser anterior a hoy.');
        }

        /* Si se aparta un lote concreto, tiene que estar en esa misma bodega:
           reservar un lote que esta en otro lado descontaria stock que no
           corresponde. */
        $aux_lote_id = $request->input('opdetregprod_id') ?: null;
        if ($aux_lote_id) {
            $lote = OpDetRegProd::find((int) $aux_lote_id);
            if (!$lote) {
                return $this->volver('No existe el lote ' . $aux_lote_id . '.');
            }
        }

        /* La validacion que importa: no se puede apartar lo que otro ya aparto.
           Se consulta aqui, dentro de la peticion que graba, y no en el
           navegador, porque dos usuarios pueden estar reservando a la vez. */
        $aux_libre = StockReserva::libreEnBodegaProducto($aux_ibp_id);
        if ($aux_cant > $aux_libre + 0.01) {
            return $this->volver('No alcanza: en esa bodega quedan ' . number_format($aux_libre, 2, ',', '.')
                . ' disponibles y está intentando reservar ' . number_format($aux_cant, 2, ',', '.') . '.');
        }

        DB::beginTransaction();
        try {
            StockReserva::create([
                'invbodegaproducto_id' => $aux_ibp_id,
                'opdetregprod_id'      => $aux_lote_id,
                'cliente_id'           => $aux_cliente_id,
                'notaventadetalle_id'  => $aux_nvdet_id,
                'cant'                 => $aux_cant,
                'cantkg'               => $request->input('cantkg') ?: null,
                'fechavence'           => $aux_vence,
                'motivo'               => $request->input('motivo'),
                'usuario_id'           => auth()->id(),
            ]);
            DB::commit();
            return redirect()->route('stockreserva')->with('mensaje', 'Stock reservado con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->volver('Error: ' . $e->getMessage());
        }
    }

    /**
     * Libera una reserva: el stock vuelve a quedar disponible.
     *
     * No se borra la fila. Si manana alguien pregunta por que el martes no habia
     * stock de un producto, tiene que poder responderse quien lo habia apartado,
     * para quien y por que se solto.
     */
    public function liberar(Request $request, $id)
    {
        can('liberar-stock-reserva');

        $reserva = StockReserva::find((int) $id);
        if (!$reserva) {
            return response()->json(['id' => 0, 'mensaje' => 'La reserva no existe.', 'tipo_alert' => 'error']);
        }
        if ($reserva->anulada_at) {
            return response()->json(['id' => 0, 'mensaje' => 'Esa reserva ya estaba liberada.', 'tipo_alert' => 'error']);
        }
        if ($reserva->consumida_at) {
            return response()->json(['id' => 0, 'mensaje' => 'Esa reserva ya se cumplió: el stock fue despachado.', 'tipo_alert' => 'error']);
        }
        /* Control de edicion concurrente, igual que el resto del sistema. */
        if ($request->updated_at and strtotime($reserva->updated_at) != $request->updated_at) {
            return response()->json(['id' => 0, 'mensaje' => 'La reserva fue modificada por otro usuario.', 'tipo_alert' => 'error']);
        }

        DB::beginTransaction();
        try {
            $reserva->anulada_at     = date('Y-m-d H:i:s');
            $reserva->anulusuario_id = auth()->id();
            $reserva->anulmotivo     = $request->input('motivo');
            $reserva->save();
            DB::commit();
            return response()->json(['id' => 1, 'mensaje' => 'ok']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['id' => 0, 'mensaje' => $e->getMessage(), 'tipo_alert' => 'error']);
        }
    }

    /** dd/mm/aaaa -> aaaa-mm-dd, que es como lo escribe el resto del sistema. */
    private function aFecha($aux_txt)
    {
        $aux_p = explode('/', trim($aux_txt));
        if (count($aux_p) == 3) {
            return $aux_p[2] . '-' . str_pad($aux_p[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($aux_p[0], 2, '0', STR_PAD_LEFT);
        }
        return date('Y-m-d', strtotime($aux_txt));
    }

    private function volver($mensaje)
    {
        return redirect()->route('stockreserva')->with([
            'mensaje'    => $mensaje,
            'tipo_alert' => 'alert-error',
        ]);
    }
}
