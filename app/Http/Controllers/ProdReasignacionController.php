<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\NotaVentaDetalle;
use App\Models\OpDetRegProd;
use App\Models\ProdReasignacion;
use App\Models\Producto;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * REASIGNAR UNA PRODUCCION a otro cliente o a otra nota de venta.
 *
 * Se fabrica para un pedido y, antes de despacharlo, otro cliente lo necesita
 * con urgencia. Hasta ahora eso se resolvia fuera del sistema.
 *
 * El principio: nada se borra. El lote se fabrico para su nota de venta de
 * origen -la OT y la OP nacieron de ese pedido- y ese vinculo no se toca. Lo
 * que se registra es el hecho nuevo: a quien se le entrego finalmente.
 *
 * Se puede entregar el lote completo o solo una parte: de 600 unidades, 400 a una
 * nota de venta y el resto queda disponible para otra. Lo que limita es lo que de
 * verdad esta libre, descontando reservas y entregas anteriores.
 *
 * Pendiente mientras se define con produccion:
 *   - La nota de venta que queda descubierta NO se relanza a fabricacion sola;
 *     se avisa en la solicitud de despacho y la decision es de planta.
 * Ver docs/reasignacion-produccion.md.
 */
class ProdReasignacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        can('listar-prod-reasignacion');

        $tablas['clientes'] = Cliente::orderBy('razonsocial')
            ->whereNull('deleted_at')->get(['id', 'razonsocial']);

        return view('prodreasignacion.index', compact('tablas'));
    }

    /**
     * Datos de un lote para decidir si se reasigna.
     *
     * Muestra lo que hace falta para no equivocarse: que producto es, cuanto
     * tiene, para quien se fabrico y si ya fue reasignado antes.
     */
    public function consultar($id)
    {
        can('listar-prod-reasignacion');

        $lote = OpDetRegProd::find((int) $id);
        if (!$lote) {
            return response()->json(['ok' => false, 'mensaje' => 'No existe el lote ' . $id . '.']);
        }
        if ($lote->aprobstatus != 2) {
            return response()->json(['ok' => false, 'mensaje' => 'El lote ' . $lote->id . ' todavía no está aprobado.']);
        }

        /* Para quien se fabrico y donde esta el material. Las dos cosas salen de
           invmovdet_opdetregprod: el vinculo con la nota de venta y el movimiento
           de inventario que ingreso el lote a bodega. */
        $aux_mov = DB::table('invmovdet_opdetregprod as l')
            ->join('invmovdet as d', 'd.id', '=', 'l.invmovdet_id')
            ->join('invbodega as b', 'b.id', '=', 'd.invbodega_id')
            ->leftJoin('notaventadetalle as nvd', 'nvd.id', '=', 'l.notaventadetalle_id')
            ->leftJoin('notaventa as nv', 'nv.id', '=', 'nvd.notaventa_id')
            ->leftJoin('cliente as c', 'c.id', '=', 'nv.cliente_id')
            ->where('l.opdetregprod_id', $lote->id)
            ->whereNull('d.deleted_at')
            ->where('d.invmovtipo_id', 1)
            /* Bodega de almacenamiento (tipo 2). Esto es lo que distingue un lote
               terminado de uno a medio hacer: las etapas intermedias ingresan a
               bodegas de produccion (tipo 5) y ese material todavia se va a
               seguir transformando. Reasignar un lote de Mezclado no tiene
               sentido: no es un producto que se le pueda entregar a nadie. */
            ->where('b.tipo', 2)
            ->select('l.notaventadetalle_id', 'nvd.notaventa_id', 'c.razonsocial',
                'd.cant', 'd.cantkg', 'b.nombre as bodega', 'b.tipo as bodega_tipo')
            ->first();

        if (!$aux_mov) {
            /* Se distingue el motivo: no es lo mismo un lote que todavia esta en
               produccion que uno que nunca ingreso a ningun lado. */
            $aux_etapa = DB::table('opdetregprod as p')
                ->join('opdet as od', 'od.id', '=', 'p.opdet_id')
                ->join('areaproduccionsucetapaprod as a', 'a.id', '=', 'od.apsucetapaprod_id')
                ->join('etapaprod as e', 'e.id', '=', 'a.etapaprod_id')
                ->where('p.id', $lote->id)
                ->value('e.nombre');

            return response()->json(['ok' => false,
                'mensaje' => 'El lote ' . $lote->id . ' no ingresó a stock'
                    . ($aux_etapa ? ' (quedó en la etapa ' . $aux_etapa . ')' : '')
                    . '. Solo se puede reasignar producción terminada.']);
        }

        /* Si ya salio de bodega no hay nada que hacer: el material esta en el
           camion o en el cliente. El saldo se mide sobre los movimientos del
           lote, no sobre el ingreso inicial. */
        $aux_saldo = (float) DB::table('invmovdet_opdetregprod as l')
            ->join('invmovdet as d', 'd.id', '=', 'l.invmovdet_id')
            ->join('invbodega as b', 'b.id', '=', 'd.invbodega_id')
            ->where('l.opdetregprod_id', $lote->id)
            ->whereNull('d.deleted_at')
            ->whereIn('b.tipo', [2])
            ->sum('d.cant');

        $aux_prod = Producto::atributosProducto($lote->producto_id);
        $aux_reasig = ProdReasignacion::vigentesDeLote($lote->id);

        /* Lo reservado en la MISMA bodega-producto donde esta el lote.
           Una reserva puede no estar anclada a un lote -es una cantidad sobre la
           bodega-producto- pero el material es el mismo: si el lote 66 aporta los
           600 que hay en esa bodega y 200 estan apartados para otro cliente,
           reasignar el lote completo dejaria esas 200 sin respaldo fisico y el
           sistema diria que el mismo material es de dos clientes. */
        $aux_reservado = $this->reservadoDondeEstaElLote($lote->id);

        /* Lo que ya se entrego de ESTE lote a otros. Un lote se puede repartir
           entre varias notas de venta, asi que lo disponible baja con cada
           reasignacion. */
        $aux_yaReasignado = ProdReasignacion::cantReasignadaDeLote($lote->id);

        return response()->json([
            'ok'            => true,
            'lote_id'       => $lote->id,
            'producto_id'   => $lote->producto_id,
            'producto'      => $aux_prod['nombre'] ?? ('Prod. ' . $lote->producto_id),
            'cant'          => (float) $aux_mov->cant,
            'cantkg'        => (float) $aux_mov->cantkg,
            'saldo'         => round($aux_saldo, 2),
            'bodega'        => $aux_mov->bodega,
            'reservado'     => $aux_reservado['cant'],
            'reservado_de'  => $aux_reservado['para'],
            'ya_entregado'  => round($aux_yaReasignado, 2),
            /* Lo que de verdad se puede reasignar: el saldo del lote, menos lo que
               ya esta apartado para otro en esa misma bodega, menos lo que ya se
               entrego de este mismo lote. */
            'disponible'    => round(max($aux_saldo - $aux_reservado['cant'] - $aux_yaReasignado, 0), 2),
            'nvdet_origen'  => $aux_mov->notaventadetalle_id,
            'nv_origen'     => $aux_mov->notaventa_id,
            'cliente_origen' => $aux_mov->razonsocial,
            /* Texto de a quien se le entrego ya, con la cantidad de cada parte. */
            'ya_reasignado' => count($aux_reasig)
                ? implode('; ', $aux_reasig->map(function ($x) {
                    return number_format($x->cant, 2, ',', '.') . ' a ' . $x->destino;
                })->toArray())
                : null,
        ]);
    }

    /**
     * Items de nota de venta que pueden recibir este lote.
     *
     * Solo del mismo producto: entregarle a un cliente algo distinto de lo que
     * pidio no es una reasignacion, es un error.
     */
    public function destinos($producto_id, Request $request)
    {
        can('listar-prod-reasignacion');

        /* Mismos criterios que usa el reporte de Pendientes por Producto para
           decidir que item sigue esperando material. No basta con que la nota de
           venta exista: puede estar cerrada por un usuario, o ya despachada por
           completo, y en esos casos ofrecerla seria un error -el material
           terminaria en una venta que ya no necesita nada-.

           Se descarta:
             - NO APROBADA. Solo aprobstatus 1 o 3: una nota de venta que todavia
               no se aprueba puede cambiar o rechazarse, y entregarle produccion
               seria comprometer material a algo que aun no existe como venta
               firme. Es la misma equivalencia que usan los reportes para el
               filtro "aprobadas".
             - anulada, con despacho terminado (findespacho) o borrada
             - CERRADA por un usuario (tabla notaventacerrada)
             - ya DESPACHADA por completo        (cant <= cantdesp)
             - ya SOLICITADA por completo        (cant <= cantsoldesp), porque su
               material ya quedo apartado en una solicitud de despacho */
        $q = DB::table('notaventadetalle as nvd')
            ->join('notaventa as nv', 'nv.id', '=', 'nvd.notaventa_id')
            ->join('cliente as c', 'c.id', '=', 'nv.cliente_id')
            ->leftJoin('vista_sumorddespxnvdetid as od', 'od.notaventadetalle_id', '=', 'nvd.id')
            ->leftJoin('vista_sumsoldespdet as sd', 'sd.notaventadetalle_id', '=', 'nvd.id')
            ->where('nvd.producto_id', (int) $producto_id)
            ->whereNull('nvd.deleted_at')
            ->whereNull('nv.deleted_at')
            ->whereNull('nv.anulada')
            ->whereNull('nv.findespacho')
            ->whereIn('nv.aprobstatus', [1, 3])
            ->whereRaw('nvd.notaventa_id NOT IN (SELECT notaventa_id FROM notaventacerrada WHERE ISNULL(deleted_at))')
            ->whereRaw('nvd.cant > IFNULL(od.cantdesp, 0)')
            ->whereRaw('nvd.cant > IFNULL(sd.cantsoldesp, 0)')
            ->orderBy('nvd.notaventa_id', 'desc')
            ->select('nvd.id', 'nvd.notaventa_id', 'nvd.cant', 'c.razonsocial',
                'nvd.requiere_fabricacion',
                DB::raw('IFNULL(od.cantdesp,0) as cantdesp'),
                DB::raw('IFNULL(sd.cantsoldesp,0) as cantsoldesp'),
                DB::raw('nvd.cant - IFNULL(sd.cantsoldesp,0) as pendiente'));

        if ($request->nvdet_excluir) {
            $q->where('nvd.id', '<>', (int) $request->nvdet_excluir);
        }

        return response()->json(['ok' => true, 'items' => $q->limit(50)->get()]);
    }

    /**
     * Cuanto esta reservado en la bodega-producto donde vive este lote.
     *
     * Devuelve la cantidad y para quien, para poder decirlo en el mensaje: "libere
     * la reserva de X" es accionable; "no se puede" no lo es.
     */
    private function reservadoDondeEstaElLote($opdetregprod_id)
    {
        $aux_ibp = DB::table('invmovdet_opdetregprod as l')
            ->join('invmovdet as d', 'd.id', '=', 'l.invmovdet_id')
            ->join('invbodega as b', 'b.id', '=', 'd.invbodega_id')
            ->where('l.opdetregprod_id', (int) $opdetregprod_id)
            ->whereNull('d.deleted_at')
            ->where('b.tipo', 2)
            ->value('d.invbodegaproducto_id');

        if (!$aux_ibp) {
            return ['cant' => 0, 'para' => ''];
        }

        $aux_filas = \App\Models\StockReserva::vigentes()
            ->leftJoin('cliente as c', 'c.id', '=', 'stockreserva.cliente_id')
            ->leftJoin('notaventadetalle as nvd', 'nvd.id', '=', 'stockreserva.notaventadetalle_id')
            ->where('stockreserva.invbodegaproducto_id', $aux_ibp)
            ->select('stockreserva.cant', 'stockreserva.fechavence', 'c.razonsocial', 'nvd.notaventa_id')
            ->get();

        $aux_cant = 0;
        $aux_para = [];
        foreach ($aux_filas as $f) {
            $aux_cant += (float) $f->cant;
            $aux_txt = $f->razonsocial ?: ('NV ' . $f->notaventa_id);
            $aux_para[] = $aux_txt . ' (' . number_format($f->cant, 2, ',', '.')
                . ', vence ' . date('d/m/Y', strtotime($f->fechavence)) . ')';
        }

        return ['cant' => round($aux_cant, 2), 'para' => implode('; ', $aux_para)];
    }

    public function guardar(Request $request)
    {
        can('crear-prod-reasignacion');

        $aux_lote_id = (int) $request->input('opdetregprod_id');
        $lote = OpDetRegProd::find($aux_lote_id);
        if (!$lote) {
            return $this->volver('No existe el lote ' . $aux_lote_id . '.');
        }

        /* Cantidad a reasignar. Puede ser una parte del lote: de 600 unidades,
           400 a una nota de venta y el resto queda disponible. Antes se exigia el
           lote completo y eso bloqueaba casos legitimos -hay 400 libres y no hay
           razon para no darlos-. */
        $aux_cant = (float) str_replace(',', '.', str_replace('.', '', $request->input('cant')));

        $aux_nvdet_destino = $request->input('nvdet_destino_id') ?: null;
        $aux_cliente       = $request->input('cliente_destino_id') ?: null;
        if (!$aux_nvdet_destino and !$aux_cliente) {
            return $this->volver('Indique a qué nota de venta o a qué cliente se entrega el lote.');
        }
        if (!trim($request->input('motivo'))) {
            return $this->volver('El motivo es obligatorio: alguien va a preguntar por qué se reasignó.');
        }

        /* Mismo producto. Entregarle a un cliente algo distinto de lo que pidio
           no es una reasignacion, es un error de carga. */
        if ($aux_nvdet_destino) {
            $nvdet = NotaVentaDetalle::find((int) $aux_nvdet_destino);
            if (!$nvdet) {
                return $this->volver('No existe el ítem de nota de venta indicado.');
            }
            if ($nvdet->producto_id != $lote->producto_id) {
                return $this->volver('El ítem de destino es de otro producto: no se puede reasignar.');
            }
            /* Se revalida en el servidor que el destino siga necesitando material.
               Entre que la pantalla cargo la lista y el usuario confirmo, otro
               pudo cerrar esa nota de venta o despacharla. */
            $aux_valido = DB::table('notaventadetalle as nvd')
                ->join('notaventa as nv', 'nv.id', '=', 'nvd.notaventa_id')
                ->leftJoin('vista_sumorddespxnvdetid as od', 'od.notaventadetalle_id', '=', 'nvd.id')
                ->leftJoin('vista_sumsoldespdet as sd', 'sd.notaventadetalle_id', '=', 'nvd.id')
                ->where('nvd.id', $nvdet->id)
                ->whereNull('nvd.deleted_at')
                ->whereNull('nv.deleted_at')
                ->whereNull('nv.anulada')
                ->whereNull('nv.findespacho')
                ->whereIn('nv.aprobstatus', [1, 3])
                ->whereRaw('nvd.notaventa_id NOT IN (SELECT notaventa_id FROM notaventacerrada WHERE ISNULL(deleted_at))')
                ->whereRaw('nvd.cant > IFNULL(od.cantdesp, 0)')
                ->whereRaw('nvd.cant > IFNULL(sd.cantsoldesp, 0)')
                ->exists();
            if (!$aux_valido) {
                return $this->volver('La nota de venta ' . $nvdet->notaventa_id
                    . ' no puede recibir material: no está aprobada, o está cerrada, anulada, o ya fue despachada o solicitada por completo.');
            }
        }

        /* No se reasigna lo ya despachado: si el material salio de bodega esta en
           el camion o en el cliente. */
        $aux_saldo = (float) DB::table('invmovdet_opdetregprod as l')
            ->join('invmovdet as d', 'd.id', '=', 'l.invmovdet_id')
            ->join('invbodega as b', 'b.id', '=', 'd.invbodega_id')
            ->where('l.opdetregprod_id', $lote->id)
            ->whereNull('d.deleted_at')
            ->where('b.tipo', 2)
            ->sum('d.cant');
        if ($aux_saldo <= 0) {
            /* b.tipo = 2 arriba: si no hay saldo en bodega de almacenamiento, o
               se despacho, o el lote nunca llego a stock porque es de una etapa
               intermedia. */
            return $this->volver('El lote ' . $lote->id . ' no tiene material en stock: fue despachado, o es de una etapa intermedia de producción.');
        }

        /* Cuanto se puede entregar de este lote: su saldo en bodega, menos lo
           reservado para otro cliente en esa misma bodega -el material es el
           mismo aunque la reserva no este anclada al lote- y menos lo que ya se
           entrego de este lote en reasignaciones anteriores. */
        $aux_reservado  = $this->reservadoDondeEstaElLote($lote->id);
        $aux_yaEntregado = ProdReasignacion::cantReasignadaDeLote($lote->id);
        $aux_disponible  = round($aux_saldo - $aux_reservado['cant'] - $aux_yaEntregado, 2);

        if ($aux_disponible <= 0) {
            $aux_motivo = [];
            if ($aux_reservado['cant'] > 0) {
                $aux_motivo[] = number_format($aux_reservado['cant'], 2, ',', '.')
                    . ' reservados para ' . $aux_reservado['para'];
            }
            if ($aux_yaEntregado > 0) {
                $aux_motivo[] = number_format($aux_yaEntregado, 2, ',', '.') . ' ya entregados en otra reasignación';
            }
            return $this->volver('El lote ' . $lote->id . ' no tiene nada disponible: de '
                . number_format($aux_saldo, 2, ',', '.') . ' en bodega hay '
                . implode(' y ', $aux_motivo) . '.');
        }

        if ($aux_cant <= 0) {
            return $this->volver('Indique cuánto del lote se entrega. Disponible: '
                . number_format($aux_disponible, 2, ',', '.') . '.');
        }
        if ($aux_cant > $aux_disponible + 0.01) {
            return $this->volver('No alcanza: del lote ' . $lote->id . ' solo hay '
                . number_format($aux_disponible, 2, ',', '.') . ' disponibles y está intentando entregar '
                . number_format($aux_cant, 2, ',', '.') . '.');
        }

        /* De quien era: el vinculo original, que NO se modifica. */
        $aux_origen = DB::table('invmovdet_opdetregprod as l')
            ->join('invmovdet as d', 'd.id', '=', 'l.invmovdet_id')
            ->where('l.opdetregprod_id', $lote->id)
            ->whereNull('d.deleted_at')
            ->whereNotNull('l.notaventadetalle_id')
            ->value('l.notaventadetalle_id');

        DB::beginTransaction();
        try {
            ProdReasignacion::create([
                'opdetregprod_id'    => $lote->id,
                'nvdet_origen_id'    => $aux_origen,
                'nvdet_destino_id'   => $aux_nvdet_destino,
                'cliente_destino_id' => $aux_cliente,
                'cant'               => $aux_cant,
                'motivo'             => $request->input('motivo'),
                'usuario_id'         => auth()->id(),
            ]);
            DB::commit();
            return redirect()->route('prodreasignacion')
                ->with('mensaje', number_format($aux_cant, 2, ',', '.') . ' del lote ' . $lote->id
                    . ' entregados. La nota de venta de origen deja de contar con esa cantidad y se le avisará a quien haga su solicitud de despacho.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->volver('Error: ' . $e->getMessage());
        }
    }

    /** Deshacer: el lote vuelve a su nota de venta de origen. */
    public function anular(Request $request, $id)
    {
        can('crear-prod-reasignacion');

        $r = ProdReasignacion::find((int) $id);
        if (!$r) {
            return response()->json(['id' => 0, 'mensaje' => 'La reasignación no existe.', 'tipo_alert' => 'error']);
        }
        if ($r->anulada_at) {
            return response()->json(['id' => 0, 'mensaje' => 'Esa reasignación ya estaba deshecha.', 'tipo_alert' => 'error']);
        }

        DB::beginTransaction();
        try {
            $r->anulada_at     = date('Y-m-d H:i:s');
            $r->anulusuario_id = auth()->id();
            $r->anulmotivo     = $request->input('motivo');
            $r->save();
            DB::commit();
            return response()->json(['id' => 1, 'mensaje' => 'ok']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['id' => 0, 'mensaje' => $e->getMessage(), 'tipo_alert' => 'error']);
        }
    }

    public function listadopage(Request $request)
    {
        can('listar-prod-reasignacion');

        $aux_sucursales = Usuario::findOrFail(auth()->id())->sucursales->pluck('id')->toArray();

        $query = DB::table('prodreasignacion as r')
            ->join('opdetregprod as p', 'p.id', '=', 'r.opdetregprod_id')
            ->join('producto as prod', 'prod.id', '=', 'p.producto_id')
            ->leftJoin('notaventadetalle as nvo', 'nvo.id', '=', 'r.nvdet_origen_id')
            ->leftJoin('notaventa as nvoo', 'nvoo.id', '=', 'nvo.notaventa_id')
            ->leftJoin('cliente as co', 'co.id', '=', 'nvoo.cliente_id')
            ->leftJoin('notaventadetalle as nvd', 'nvd.id', '=', 'r.nvdet_destino_id')
            ->leftJoin('notaventa as nvdd', 'nvdd.id', '=', 'nvd.notaventa_id')
            ->leftJoin('cliente as cd', 'cd.id', '=', 'nvdd.cliente_id')
            ->leftJoin('cliente as cdir', 'cdir.id', '=', 'r.cliente_destino_id')
            ->leftJoin('usuario as u', 'u.id', '=', 'r.usuario_id')
            ->whereNull('r.deleted_at')
            ->whereIn('p.sucursal_id', $aux_sucursales);

        if (!$request->vertodas) {
            $query->whereNull('r.anulada_at');
        }

        $datas = $query->orderBy('r.id', 'desc')
            ->select(
                'r.id', 'r.opdetregprod_id', 'r.cant', 'r.motivo', 'r.created_at',
                'r.anulada_at', 'r.anulmotivo',
                'p.producto_id',
                'prod.glosa as producto_nombre',
                'nvoo.id as nv_origen', 'co.razonsocial as cliente_origen',
                'nvdd.id as nv_destino',
                DB::raw('COALESCE(cd.razonsocial, cdir.razonsocial) as cliente_destino'),
                'u.usuario as usuario_nombre'
            )->get();

        foreach ($datas as $d) {
            $d->estado = $d->anulada_at ? 'Deshecha' : 'Vigente';
        }

        return datatables($datas)->toJson();
    }

    private function volver($mensaje)
    {
        return redirect()->route('prodreasignacion')->with([
            'mensaje'    => $mensaje,
            'tipo_alert' => 'alert-error',
        ]);
    }
}
