<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use App\Models\StockReserva;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * SALDO POR PRODUCIR — de donde sale el numero que ve el vendedor.
 *
 * En la nota de venta y en la cotizacion, el campo de stock del modal de producto
 * NO muestra el stock: muestra cuanto falta fabricar para cubrir todo lo
 * comprometido. Un negativo significa que el producto esta SOBREVENDIDO.
 *
 *     stock vendible - pendiente por despachar - reservado
 *
 * Al vendedor se le da solo ese numero, a proposito: si viera que hay 750 en
 * bodega pensaria que puede venderlos y agravaria la sobreventa.
 *
 * Pero entonces nadie puede explicar el numero. Un -29.450 no se reconstruye
 * desde el reporte de stock, porque ese muestra lo que hay HOY (150 libres) y no
 * incluye las 30.000 unidades ya vendidas. Son dos preguntas distintas:
 *
 *     reportinvstock      cuanto hay y cuanto esta libre        -> presente fisico
 *     esta pantalla       cuanto falta fabricar para cumplir    -> saldo comprometido
 *
 * Esta pantalla existe para responder la segunda con el detalle completo: cada
 * bodega, cada nota de venta y cada reserva que forman el numero. No es para
 * vendedores; tiene su propio permiso.
 */
class ReportSaldoProducirController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        can('reporte-saldo-producir');

        $users = Usuario::findOrFail(auth()->id());
        $sucurArray = $users->sucursales->pluck('id')->toArray();
        $tablashtml['sucursales'] = Sucursal::orderBy('id')
            ->whereIn('sucursal.id', $sucurArray)->get();

        $selecmultprod = true;
        return view('reportsaldoproducir.index', compact('tablashtml', 'selecmultprod'));
    }

    /**
     * Una fila por producto: las tres piezas y el saldo.
     *
     * Acepta varios productos -el buscador devuelve una lista separada por
     * comas- porque comparar es justo lo que hace falta: ver de un vistazo
     * cuales estan sobrevendidos. El desglose de cada uno se pide aparte.
     */
    public function listadopage(Request $request)
    {
        can('reporte-saldo-producir');

        $aux_sucursal = (int) $request->sucursal_id;
        if (!$aux_sucursal) {
            return datatables([])->toJson();
        }

        $aux_productos = $this->productosPedidos($request);
        if (!count($aux_productos)) {
            return datatables([])->toJson();
        }

        $datas = [];
        foreach ($aux_productos as $aux_prod_id) {
            $aux_d = $this->calcular($aux_prod_id, $aux_sucursal);
            if ($aux_d) {
                $datas[] = $aux_d;
            }
        }

        return datatables($datas)->toJson();
    }

    /** El desglose completo de un producto: bodega por bodega y venta por venta. */
    public function detalle($producto_id, Request $request)
    {
        can('reporte-saldo-producir');

        $aux_sucursal = (int) $request->sucursal_id;
        $aux_prod = Producto::atributosProducto((int) $producto_id);

        return response()->json([
            'ok'       => true,
            'producto' => $aux_prod['nombre'] ?? ('Prod. ' . $producto_id),
            'resumen'  => $this->calcular((int) $producto_id, $aux_sucursal),
            'bodegas'  => $this->detalleBodegas((int) $producto_id, $aux_sucursal),
            'ventas'   => $this->detalleVentas((int) $producto_id, $aux_sucursal),
            'reservas' => $this->detalleReservas((int) $producto_id, $aux_sucursal),
            'lotes'    => $this->detalleLotes((int) $producto_id, $aux_sucursal),
        ]);
    }

    /**
     * Las tres piezas del saldo.
     *
     * Se calculan aqui, en vivo y por bodega, en vez de leer la cache que usa el
     * campo del vendedor: esta pantalla existe justamente para explicar el
     * numero, y una cache de hace cinco minutos no explica nada. Si el saldo de
     * aqui no coincide con el de la nota de venta, es que la cache esta vieja
     * -se refresca con producto:actualizar-cache-pendientexproducir-.
     */
    private function calcular($producto_id, $sucursal_id)
    {
        /* Stock vendible: TODAS las bodegas de tipo 1 (picking) y 2 (venta) de la
           sucursal, no una. Si manana se agrega otra bodega, entra sola. */
        $aux_stock = (float) DB::table('invbodegaproducto as ibp')
            ->join('invbodega as b', 'b.id', '=', 'ibp.invbodega_id')
            ->where('ibp.producto_id', $producto_id)
            ->whereIn('b.tipo', [1, 2])
            ->where('b.sucursal_id', $sucursal_id)
            ->where('b.activo', 1)
            ->whereNull('ibp.deleted_at')
            ->whereNull('b.deleted_at')
            ->sum('ibp.stock');

        $aux_pend = (float) DB::table('notaventadetalle as nvd')
            ->join('notaventa as nv', 'nv.id', '=', 'nvd.notaventa_id')
            ->leftJoin('vista_sumorddespxnvdetid as od', 'od.notaventadetalle_id', '=', 'nvd.id')
            ->where('nvd.producto_id', $producto_id)
            ->where('nv.sucursal_id', $sucursal_id)
            ->whereNull('nvd.deleted_at')
            ->whereNull('nv.deleted_at')
            ->whereNull('nv.anulada')
            ->whereNull('nv.findespacho')
            ->whereIn('nv.aprobstatus', [1, 3])
            ->whereRaw('nvd.notaventa_id NOT IN (SELECT notaventa_id FROM notaventacerrada WHERE ISNULL(deleted_at))')
            ->whereRaw('nvd.cant > IFNULL(od.cantdesp, 0)')
            ->sum(DB::raw('nvd.cant - IFNULL(od.cantdesp, 0)'));

        /* Misma regla que el campo del vendedor: solo restan las reservas cuyo
           compromiso no este ya dentro del pendiente por despachar. Si los dos
           numeros no coincidieran, esta pantalla dejaria de servir para explicar
           el otro, que es justo para lo que existe. */
        $aux_res = StockReserva::reservadoQueResta($producto_id, $sucursal_id);

        if ($aux_stock == 0 and $aux_pend == 0 and $aux_res == 0) {
            return null;   // nada que contar de este producto en esta sucursal
        }

        $aux_prod = Producto::atributosProducto($producto_id);

        /* Para quien esta reservado, en el propio listado: ver "200 reservado"
           sin saber de quien obliga a abrir el detalle para algo que cabe en una
           linea, y es justo el dato que explica por que falta ese material. */
        $aux_resPara = [];
        if ($aux_res > 0) {
            foreach ($this->detalleReservas($producto_id, $sucursal_id) as $aux_r) {
                $aux_txt = $aux_r->razonsocial ?: ('NV ' . $aux_r->notaventa_id);
                $aux_resPara[] = $aux_txt . ' (' . number_format($aux_r->cant, 2, ',', '.') . ')';
            }
        }

        /* Y de quien es lo pendiente: con una sola nota de venta se nombra, y con
           varias se dice cuantas son, que es lo que se puede leer de un vistazo. */
        $aux_ventas = $this->detalleVentas($producto_id, $sucursal_id);
        $aux_pendPara = '';
        if (count($aux_ventas) == 1) {
            $aux_pendPara = 'NV ' . $aux_ventas[0]->notaventa_id . ' — ' . $aux_ventas[0]->razonsocial;
        } elseif (count($aux_ventas) > 1) {
            $aux_pendPara = count($aux_ventas) . ' notas de venta';
        }

        $aux_libre = $this->libreDelProducto($producto_id, $sucursal_id, $aux_stock);

        return (object) [
            'producto_id'      => $producto_id,
            'producto_nombre'  => $aux_prod['nombre'] ?? ('Prod. ' . $producto_id),
            'stock'            => round($aux_stock, 2),
            'libre'            => round($aux_libre, 2),
            'pendiente'        => round($aux_pend, 2),
            'pendiente_para'   => $aux_pendPara,
            'reservado'        => round($aux_res, 2),
            'reservado_para'   => implode('; ', $aux_resPara),
            'saldo'            => round($aux_stock - $aux_pend - $aux_res, 2),
        ];
    }

    private function detalleBodegas($producto_id, $sucursal_id)
    {
        $aux_filas = DB::table('invbodegaproducto as ibp')
            ->join('invbodega as b', 'b.id', '=', 'ibp.invbodega_id')
            ->where('ibp.producto_id', $producto_id)
            ->whereIn('b.tipo', [1, 2])
            ->where('b.sucursal_id', $sucursal_id)
            ->where('b.activo', 1)
            ->whereNull('ibp.deleted_at')
            ->whereNull('b.deleted_at')
            ->orderBy('b.orden')
            ->select('ibp.id as ibp_id', 'b.nombre as bodega', 'b.tipo', 'ibp.stock')
            ->get();

        /* Cuanto de ese stock esta libre para vender.
           En una bodega de venta es el stock menos lo apartado y lo ya entregado
           a otra nota de venta. En picking (tipo 1) no hay nada libre por
           definicion: ese material salio de la bodega hacia una solicitud de
           despacho concreta y ya tiene destino. */
        foreach ($aux_filas as $aux_b) {
            $aux_b->libre = $aux_b->tipo == 1
                ? 0
                : round(StockReserva::libreEnBodegaProducto($aux_b->ibp_id), 2);
        }

        return $aux_filas;
    }

    private function detalleVentas($producto_id, $sucursal_id)
    {
        return DB::table('notaventadetalle as nvd')
            ->join('notaventa as nv', 'nv.id', '=', 'nvd.notaventa_id')
            ->join('cliente as c', 'c.id', '=', 'nv.cliente_id')
            ->leftJoin('vista_sumorddespxnvdetid as od', 'od.notaventadetalle_id', '=', 'nvd.id')
            ->where('nvd.producto_id', $producto_id)
            ->where('nv.sucursal_id', $sucursal_id)
            ->whereNull('nvd.deleted_at')
            ->whereNull('nv.deleted_at')
            ->whereNull('nv.anulada')
            ->whereNull('nv.findespacho')
            ->whereIn('nv.aprobstatus', [1, 3])
            ->whereRaw('nvd.notaventa_id NOT IN (SELECT notaventa_id FROM notaventacerrada WHERE ISNULL(deleted_at))')
            ->whereRaw('nvd.cant > IFNULL(od.cantdesp, 0)')
            ->orderBy('nvd.notaventa_id', 'desc')
            ->select(
                'nvd.notaventa_id', 'c.razonsocial', 'nvd.cant',
                DB::raw('IFNULL(od.cantdesp,0) as despachado'),
                DB::raw('nvd.cant - IFNULL(od.cantdesp,0) as pendiente')
            )->get();
    }

    private function detalleReservas($producto_id, $sucursal_id)
    {
        return StockReserva::vigentes()
            ->join('invbodegaproducto as ibp', 'ibp.id', '=', 'stockreserva.invbodegaproducto_id')
            ->join('invbodega as b', 'b.id', '=', 'ibp.invbodega_id')
            ->leftJoin('cliente as c', 'c.id', '=', 'stockreserva.cliente_id')
            ->leftJoin('notaventadetalle as nvd', 'nvd.id', '=', 'stockreserva.notaventadetalle_id')
            ->where('ibp.producto_id', $producto_id)
            ->where('b.sucursal_id', $sucursal_id)
            ->orderBy('stockreserva.id', 'desc')
            ->select(
                'stockreserva.id', 'stockreserva.cant', 'stockreserva.fechavence',
                'stockreserva.motivo', 'b.nombre as bodega',
                'c.razonsocial', 'nvd.notaventa_id'
            )->get();
    }

    /**
     * Stock libre para vender: la suma de lo que queda libre en cada bodega de
     * venta. El material que esta en picking no entra: ya salio de la bodega
     * hacia una solicitud de despacho y tiene destino, aunque siga siendo stock.
     */
    private function libreDelProducto($producto_id, $sucursal_id, $stock)
    {
        $aux_libre = 0;
        foreach ($this->detalleBodegas($producto_id, $sucursal_id) as $aux_b) {
            $aux_libre += (float) $aux_b->libre;
        }

        return round(min($aux_libre, $stock), 2);
    }

    /**
     * Produccion entregada a otra nota de venta, lote por lote.
     *
     * Un lote se fabrica para alguien -una OT nacida de una nota de venta- o para
     * stock -una OT libre, sin cliente-. Cuando se entrega a otro, queda aqui:
     * de que lote salio, para quien se fabrico, a quien se le dio, cuanto se le
     * dio y cuanto de eso ya se llevo en una solicitud de despacho.
     *
     * No suma ni resta al saldo: ese material ya esta contado en el stock de la
     * bodega. Lo que explica es POR QUE una nota de venta no puede tomar todo lo
     * que ve en bodega, que es la pregunta que trae a esta pantalla.
     */
    private function detalleLotes($producto_id, $sucursal_id)
    {
        $aux_filas = DB::table('prodreasignacion as r')
            ->join('opdetregprod as p', 'p.id', '=', 'r.opdetregprod_id')
            ->leftJoin('notaventadetalle as nvo', 'nvo.id', '=', 'r.nvdet_origen_id')
            ->leftJoin('notaventa as nvoo', 'nvoo.id', '=', 'nvo.notaventa_id')
            ->leftJoin('cliente as co', 'co.id', '=', 'nvoo.cliente_id')
            ->leftJoin('notaventadetalle as nvd', 'nvd.id', '=', 'r.nvdet_destino_id')
            ->leftJoin('notaventa as nvdd', 'nvdd.id', '=', 'nvd.notaventa_id')
            ->leftJoin('cliente as cd', 'cd.id', '=', 'nvdd.cliente_id')
            ->leftJoin('cliente as cdir', 'cdir.id', '=', 'r.cliente_destino_id')
            ->where('p.producto_id', $producto_id)
            ->where('p.sucursal_id', $sucursal_id)
            ->whereNull('r.deleted_at')
            ->whereNull('r.anulada_at')
            ->orderBy('r.opdetregprod_id')
            ->orderBy('r.id')
            ->select(
                'r.id', 'r.opdetregprod_id as lote', 'r.cant', 'r.created_at', 'r.motivo',
                'r.nvdet_destino_id',
                'nvoo.id as nv_origen', 'co.razonsocial as cliente_origen',
                'nvdd.id as nv_destino',
                DB::raw('COALESCE(cd.razonsocial, cdir.razonsocial) as cliente_destino')
            )->get();

        foreach ($aux_filas as $aux_f) {
            /* Lo que el destinatario ya se llevo de ese lote: mismo criterio que
               usa la solicitud de despacho para decidir cuanto le queda. Cuenta
               desde que se guarda la solicitud, no desde que se aprueba, para que
               dos solicitudes no puedan cargar el mismo material. */
            $aux_f->usado = (float) DB::table('despachosoldet_opdetregprod as dsop')
                ->join('despachosoldet as dsd', 'dsd.id', '=', 'dsop.despachosoldet_id')
                ->join('despachosol as ds', 'ds.id', '=', 'dsd.despachosol_id')
                ->where('dsop.opdetregprod_id', $aux_f->lote)
                ->where('dsd.notaventadetalle_id', $aux_f->nvdet_destino_id)
                ->whereNull('dsd.deleted_at')
                ->whereNull('ds.deleted_at')
                ->whereRaw('ds.id NOT IN (SELECT despachosol_id FROM despachosolanul WHERE ISNULL(deleted_at))')
                ->sum('dsop.cant');

            $aux_f->usado      = round($aux_f->usado, 2);
            $aux_f->disponible = round(max($aux_f->cant - $aux_f->usado, 0), 2);

            /* Cuanto queda del lote FISICAMENTE en las bodegas de venta y picking.
               No es lo mismo que el saldo de la asignacion: de un lote de 600 se
               pueden haber entregado 400 a un cliente y las otras 200 siguen en
               bodega sin dueno. Si este numero es menor que el disponible, el
               material ya salio y la asignacion quedo sin respaldo. */
            $aux_f->en_bodega = round((float) DB::table('invmovdet_opdetregprod as l')
                ->join('invmovdet as d', 'd.id', '=', 'l.invmovdet_id')
                ->join('invbodega as b', 'b.id', '=', 'd.invbodega_id')
                ->where('l.opdetregprod_id', $aux_f->lote)
                ->whereIn('b.tipo', [1, 2])
                ->where('b.sucursal_id', $sucursal_id)
                ->whereNull('d.deleted_at')
                ->sum('d.cant'), 2);

            /* Para quien se fabrico. Sin nota de venta de origen es una OT libre:
               se produjo para stock y no se le quito a ningun cliente. */
            $aux_f->origen_txt = $aux_f->nv_origen
                ? ('NV ' . $aux_f->nv_origen . ($aux_f->cliente_origen ? ' — ' . $aux_f->cliente_origen : ''))
                : 'OT libre (para stock)';

            $aux_f->destino_txt = $aux_f->nv_destino
                ? ('NV ' . $aux_f->nv_destino . ($aux_f->cliente_destino ? ' — ' . $aux_f->cliente_destino : ''))
                : ($aux_f->cliente_destino ?: '—');
        }

        return $aux_filas;
    }

    /** Los productos que pidio el usuario: uno, varios o ninguno. */
    private function productosPedidos(Request $request)
    {
        if (empty($request->producto_id)) {
            return [];
        }
        $aux_ids = is_array($request->producto_id)
            ? $request->producto_id
            : explode(',', $request->producto_id);

        return array_values(array_unique(array_filter(array_map('intval', $aux_ids))));
    }
}
