<?php

namespace App\Http\Controllers;

use App\Models\OpDetRegProd;
use App\Models\Producto;
use App\Services\TrazabilidadLoteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Reporte de trazabilidad por documento: partiendo de un N° de Factura, N° de
 * Guía, Código de Producto (+ rango de fechas) o N° de Lote, reconstruye la
 * cadena completa: Nota de Venta → OT → OP → lotes de producción (etapa por
 * etapa, hacia atrás) → Solicitud → Orden de Despacho → Guía → Factura → NC/ND
 * (hacia adelante). Los filtros son combinables (AND).
 */
class TrazabilidadDocumentoController extends Controller
{
    public function index()
    {
        can('listar-registro-produccion');
        return view('trazabilidaddocumento.index');
    }

    public function buscar(Request $request)
    {
        can('listar-registro-produccion');

        $sets = [];
        $usoAlgunFiltro = false;

        if (!empty($request->nrofactura)) {
            $usoAlgunFiltro = true;
            $factura = DB::table('dte')
                ->where('nrodocto', $request->nrofactura)
                ->where('foliocontrol_id', 1)
                ->whereNull('deleted_at')
                ->first();
            $sets[] = $factura ? $this->lotesDesdeFacturaId($factura->id) : [];
        }

        if (!empty($request->nroguia)) {
            $usoAlgunFiltro = true;
            $guia = DB::table('dte')
                ->where('nrodocto', $request->nroguia)
                ->where('foliocontrol_id', 2)
                ->whereNull('deleted_at')
                ->first();
            $sets[] = $guia ? $this->lotesDesdeGuiaId($guia->id) : [];
        }

        if (!empty($request->producto_id) && !empty($request->fechad) && !empty($request->fechah)) {
            $usoAlgunFiltro = true;
            $fd = date_format(date_create_from_format('d/m/Y', $request->fechad), 'Y-m-d') . ' 00:00:00';
            $fh = date_format(date_create_from_format('d/m/Y', $request->fechah), 'Y-m-d') . ' 23:59:59';
            $rows = DB::select("
                SELECT id FROM opdetregprod
                WHERE producto_id = ? AND es_muestra = 0 AND ISNULL(deleted_at)
                  AND aprobfechahora BETWEEN ? AND ?
            ", [$request->producto_id, $fd, $fh]);
            $sets[] = array_map(function ($r) { return (int) $r->id; }, $rows);
        }

        if (!empty($request->notaventa_id)) {
            $usoAlgunFiltro = true;
            $sets[] = $this->lotesDesdeNotaVenta($request->notaventa_id);
        }

        if (!empty($request->lote_id)) {
            $usoAlgunFiltro = true;
            $sets[] = [intval($request->lote_id)];
        }

        if (!$usoAlgunFiltro) {
            return response()->json(['error' => 'Debe indicar al menos un filtro de búsqueda.'], 422);
        }

        // Intersección (AND) de todos los filtros llenados
        $ids = array_shift($sets);
        foreach ($sets as $s) {
            $ids = array_intersect($ids, $s);
        }
        $ids = array_values(array_unique($ids));

        $items = [];
        foreach ($ids as $id) {
            $item = $this->armarItem($id);
            if ($item) $items[] = $item;
        }

        // Estado de la NV: si no tiene OT creada no habra produccion ni lotes, y
        // la busqueda saldria vacia sin explicar el motivo. Se informa el estado
        // para distinguir "la NV no existe" de "existe pero aun no tiene OT".
        $nvInfo = null;
        if (!empty($request->notaventa_id)) {
            $nvInfo = $this->estadoNotaVenta($request->notaventa_id);
        }

        return response()->json(['items' => $items, 'nv_info' => $nvInfo]);
    }

    /**
     * Estado de una Nota de Venta respecto a produccion: si existe, si tiene OT
     * creada y en que estado esta cada una. Permite explicar por que una NV no
     * arroja lotes (no existe / no tiene OT / la OT esta pendiente de aprobar).
     */
    private function estadoNotaVenta($notaventa_id)
    {
        $nv = DB::table('notaventa')->where('id', $notaventa_id)->whereNull('deleted_at')->first();
        if (!$nv) {
            return ['existe' => false, 'notaventa_id' => $notaventa_id, 'ots' => []];
        }

        $ots = DB::select("
            SELECT ot.id,
                   ot.aprobstatus,
                   (SELECT COUNT(*) FROM otanul WHERE otanul.ot_id = ot.id AND ISNULL(otanul.deleted_at)) AS anulada,
                   (SELECT COUNT(*) FROM otdet od
                     INNER JOIN op ON op.otdet_id = od.id AND ISNULL(op.deleted_at)
                     WHERE od.ot_id = ot.id AND ISNULL(od.deleted_at))                                    AS ops
            FROM   otnotaventa otnv
            INNER  JOIN ot ON ot.id = otnv.ot_id AND ISNULL(ot.deleted_at)
            WHERE  otnv.notaventa_id = ?
              AND  ISNULL(otnv.deleted_at)
            ORDER  BY ot.id
        ", [$notaventa_id]);

        return [
            'existe'       => true,
            'notaventa_id' => (int) $notaventa_id,
            'ots'          => array_map(function ($o) {
                // aprobstatus: 1 = aprobada, 2 = pendiente/rechazada segun el flujo de otaprobar
                return [
                    'id'          => (int) $o->id,
                    'aprobada'    => ((int) $o->aprobstatus === 1),
                    'anulada'     => ((int) $o->anulada > 0),
                    'con_op'      => ((int) $o->ops > 0),
                ];
            }, $ots),
        ];
    }

    /**
     * Nota de Venta → notaventadetalle → despachosoldet →
     * despachosoldet_opdetregprod → opdetregprod_id.
     *
     * Devuelve los lotes de TODOS los items de la NV que ya fueron asignados a
     * alguna solicitud de despacho. Se suman ademas los lotes producidos para
     * esa NV que aun no se despachan (via otnotaventa → ot → otdet → op), para
     * que la busqueda por NV muestre la trazabilidad completa aunque el despacho
     * todavia no exista.
     */
    private function lotesDesdeNotaVenta($notaventa_id)
    {
        // 1) Lotes ya comprometidos en solicitudes de despacho de esta NV
        $despachados = DB::select("
            SELECT DISTINCT dsop.opdetregprod_id
            FROM   despachosol ds
            INNER  JOIN despachosoldet dsd ON dsd.despachosol_id = ds.id
            INNER  JOIN despachosoldet_opdetregprod dsop ON dsop.despachosoldet_id = dsd.id
            WHERE  ds.notaventa_id = ?
              AND  ISNULL(ds.deleted_at)
              AND  ISNULL(dsd.deleted_at)
        ", [$notaventa_id]);

        // 2) Lotes producidos para esta NV (aunque no esten despachados aun)
        $producidos = DB::select("
            SELECT DISTINCT odrp.id
            FROM   otnotaventa otnv
            INNER  JOIN otdet od   ON od.ot_id = otnv.ot_id AND ISNULL(od.deleted_at)
            INNER  JOIN op         ON op.otdet_id = od.id   AND ISNULL(op.deleted_at)
            INNER  JOIN opdet      ON opdet.op_id = op.id   AND ISNULL(opdet.deleted_at)
            INNER  JOIN opdetregprod odrp ON odrp.opdet_id = opdet.id
                                          AND odrp.es_muestra = 0
                                          AND ISNULL(odrp.deleted_at)
            WHERE  otnv.notaventa_id = ?
              AND  ISNULL(otnv.deleted_at)
        ", [$notaventa_id]);

        $ids = array_map(function ($r) { return (int) $r->opdetregprod_id; }, $despachados);
        foreach ($producidos as $r) {
            $ids[] = (int) $r->id;
        }
        return array_values(array_unique($ids));
    }

    /**
     * Guía (dte.id) → despachoord → despachoorddet → despachosoldet →
     * despachosoldet_opdetregprod → opdetregprod_id (lotes finales despachados
     * en esa guía, uno o más por cada ítem/producto).
     */
    private function lotesDesdeGuiaId($guia_dte_id)
    {
        $rows = DB::select("
            SELECT DISTINCT dsop.opdetregprod_id
            FROM   dteguiadesp dtg
            INNER  JOIN despachoorddet dod ON dod.despachoord_id = dtg.despachoord_id
            INNER  JOIN despachosoldet dsd ON dsd.id = dod.despachosoldet_id
            INNER  JOIN despachosoldet_opdetregprod dsop ON dsop.despachosoldet_id = dsd.id
            WHERE  dtg.dte_id = ?
              AND  ISNULL(dod.deleted_at)
              AND  ISNULL(dsd.deleted_at)
        ", [$guia_dte_id]);
        return array_map(function ($r) { return (int) $r->opdetregprod_id; }, $rows);
    }

    /**
     * Factura (dte.id) → dtedte (guías vinculadas) → lotesDesdeGuiaId de cada una.
     * Una factura puede tener más de una guía asociada.
     */
    private function lotesDesdeFacturaId($factura_dte_id)
    {
        $guias = DB::select("
            SELECT dter_id FROM dtedte WHERE dte_id = ? AND ISNULL(deleted_at)
        ", [$factura_dte_id]);

        $ids = [];
        foreach ($guias as $g) {
            $ids = array_merge($ids, $this->lotesDesdeGuiaId($g->dter_id));
        }
        return array_values(array_unique($ids));
    }

    /**
     * Arma el item completo para un lote: contexto (NV/OT/OP/producto/etapa),
     * cadena hacia atrás (origenes, recursiva) y cadena hacia adelante (despacho,
     * vacía si el lote aún no fue despachado — no necesariamente es un error,
     * puede ser un lote de etapa intermedia).
     */
    private function armarItem($opdetregprod_id)
    {
        $lote = OpDetRegProd::find($opdetregprod_id);
        if (!$lote || !$lote->opdet) return null;

        $opdet = $lote->opdet;
        $op    = $opdet->op;
        $otdet = $op->otdet;
        $ot    = $otdet->ot;

        $notaventa_id = null;
        if ($otdet->otdetnvdet && $otdet->otdetnvdet->notaventadetalle) {
            $notaventa_id = $otdet->otdetnvdet->notaventadetalle->notaventa_id;
        }

        $productoData = Producto::atributosProducto($lote->producto_id);

        return [
            'lote_id'         => $lote->id,
            'producto_id'     => $lote->producto_id,
            'producto_nombre' => $productoData['nombre'] ?? ('Prod. ' . $lote->producto_id),
            'kgprod'          => $lote->kgprod,
            'cantprod'        => $lote->cantprod,
            'etapa_nombre'    => optional(optional($opdet->areaproduccionsucetapaprod)->etapaprod)->nombre,
            'aprobfechahora'  => $lote->aprobfechahora,
            'notaventa_id'    => $notaventa_id,
            'ot_id'           => $ot->id,
            'op_id'           => $op->id,
            'origenes'        => TrazabilidadLoteService::origenesLote($lote->id),
            'destinos'        => TrazabilidadLoteService::destinosLote($lote->id),
            'despacho'        => TrazabilidadLoteService::trazaDespacho($lote->id),
        ];
    }
}
