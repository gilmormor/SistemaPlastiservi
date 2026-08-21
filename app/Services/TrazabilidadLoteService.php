<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Trazabilidad completa de un lote de producción (opdetregprod):
 *  - Hacia atrás: de qué lote(s) de la etapa anterior vino (opdetregprod_origen),
 *    recursivo hasta la primera etapa.
 *  - Hacia adelante: árbol de despacho (Sol → Ord → Guía → Factura → NC/ND).
 * Compartido entre op/seguimiento y el reporte de trazabilidad por documento,
 * para no duplicar esta lógica en ambos controladores.
 */
class TrazabilidadLoteService
{
    /**
     * Cadena de lotes de origen (hacia atrás), recursiva. Puede ramificarse si
     * un lote consumió de más de un lote de la etapa anterior.
     * Retorna: [ ['lote_id'=>, 'kg'=>, 'cant'=>, 'origenes'=>[...]], ... ]
     */
    public static function origenesLote($opdetregprod_id, array &$visitados = [])
    {
        $opdetregprod_id = intval($opdetregprod_id);
        if (!$opdetregprod_id || isset($visitados[$opdetregprod_id])) return [];
        $visitados[$opdetregprod_id] = true;

        $filas = DB::select("
            SELECT o.opdetregprod_origen_id AS lote_id, o.kg, o.cant, ep.nombre AS etapa_nombre
            FROM   opdetregprod_origen o
            INNER  JOIN opdetregprod rp ON rp.id = o.opdetregprod_origen_id
            INNER  JOIN opdet od ON od.id = rp.opdet_id
            LEFT   JOIN areaproduccionsucetapaprod aps ON aps.id = od.apsucetapaprod_id
            LEFT   JOIN etapaprod ep ON ep.id = aps.etapaprod_id
            WHERE  o.opdetregprod_id = ?
            ORDER  BY o.opdetregprod_origen_id
        ", [$opdetregprod_id]);

        $result = [];
        foreach ($filas as $f) {
            $result[] = [
                'lote_id'      => $f->lote_id,
                'kg'           => $f->kg,
                'cant'         => $f->cant,
                'etapa_nombre' => $f->etapa_nombre,
                'origenes'     => self::origenesLote($f->lote_id, $visitados),
            ];
        }
        return $result;
    }

    /**
     * Cadena de lotes que CONSUMIERON de este lote (hacia adelante, etapa
     * siguiente), recursiva hasta el lote final. Puede ramificarse si el lote
     * alimentó a más de un registro de la etapa siguiente. Cada nodo trae
     * embebido su propio árbol de despacho (trazaDespacho): vacío si ese lote
     * en particular aún no fue despachado, poblado si sí — así el despacho
     * queda ligado al lote final real, no forzado en el lote consultado si
     * este es intermedio.
     * Retorna: [ ['lote_id'=>, 'kg'=>, 'cant'=>, 'despacho'=>[...], 'destinos'=>[...]], ... ]
     */
    public static function destinosLote($opdetregprod_id, array &$visitados = [])
    {
        $opdetregprod_id = intval($opdetregprod_id);
        if (!$opdetregprod_id || isset($visitados[$opdetregprod_id])) return [];
        $visitados[$opdetregprod_id] = true;

        $filas = DB::select("
            SELECT o.opdetregprod_id AS lote_id, o.kg, o.cant, ep.nombre AS etapa_nombre
            FROM   opdetregprod_origen o
            INNER  JOIN opdetregprod rh ON rh.id = o.opdetregprod_id
            INNER  JOIN opdet od ON od.id = rh.opdet_id
            LEFT   JOIN areaproduccionsucetapaprod aps ON aps.id = od.apsucetapaprod_id
            LEFT   JOIN etapaprod ep ON ep.id = aps.etapaprod_id
            WHERE  o.opdetregprod_origen_id = ?
            ORDER  BY o.opdetregprod_id
        ", [$opdetregprod_id]);

        $result = [];
        foreach ($filas as $f) {
            $result[] = [
                'lote_id'      => $f->lote_id,
                'kg'           => $f->kg,
                'cant'         => $f->cant,
                'etapa_nombre' => $f->etapa_nombre,
                'despacho'     => self::trazaDespacho($f->lote_id),
                'destinos'     => self::destinosLote($f->lote_id, $visitados),
            ];
        }
        return $result;
    }

    /**
     * Construye el árbol jerárquico de trazabilidad de despacho para un lote de
     * producción (hacia adelante): Sol → Ord (+ anulada) → Guía (+ anulada) →
     * Factura → NC/ND. Se busca vía despachosoldet_opdetregprod (trazabilidad
     * granular por lote). Si el lote no fue despachado (o es un lote histórico
     * sin ese detalle), retorna array vacío.
     */
    public static function trazaDespacho($opdetregprod_id)
    {
        $opdetregprod_id = intval($opdetregprod_id);
        if (!$opdetregprod_id) return [];

        // ── 1. Solicitudes de despacho vía tabla de lotes (trazabilidad granular) ──
        $sols = DB::select("
            SELECT DISTINCT
                dsd.despachosol_id   AS id,
                ds.fechahora,
                ds.aprorddesp,
                ds.aprorddespfh,
                u.nombre             AS usuario_nombre,
                (SELECT COUNT(*) FROM despachosolanul
                 WHERE  despachosol_id = ds.id)       AS anulada
            FROM   despachosoldet_opdetregprod dsop
            INNER  JOIN despachosoldet dsd ON dsd.id  = dsop.despachosoldet_id
            INNER  JOIN despachosol    ds  ON ds.id   = dsd.despachosol_id
            LEFT   JOIN usuario        u   ON u.id    = ds.usuario_id
            WHERE  dsop.opdetregprod_id = ?
              AND  ISNULL(dsd.deleted_at)
            ORDER  BY dsd.despachosol_id
        ", [$opdetregprod_id]);

        if (empty($sols)) return [];

        // ── 2. Órdenes de despacho (con flag anulada) ─────────────────────────
        $ords = DB::select("
            SELECT DISTINCT
                dod.despachoord_id                                           AS id,
                dsd.despachosol_id                                           AS sol_id,
                (SELECT COUNT(*) FROM despachoordanul
                 WHERE  despachoord_id = dod.despachoord_id)                AS anulada,
                dor.fechahora,
                dor.aprguiadesp,
                dor.aprguiadespfh,
                u.nombre                                                     AS usuario_nombre
            FROM   despachosoldet_opdetregprod dsop
            INNER  JOIN despachosoldet dsd ON dsd.id         = dsop.despachosoldet_id
            INNER  JOIN despachoorddet dod ON dod.despachosoldet_id = dsd.id
            INNER  JOIN despachoord    dor ON dor.id         = dod.despachoord_id
            LEFT   JOIN usuario        u   ON u.id           = dor.usuario_id
            WHERE  dsop.opdetregprod_id = ?
              AND  ISNULL(dsd.deleted_at)
              AND  ISNULL(dod.deleted_at)
            ORDER  BY dod.despachoord_id
        ", [$opdetregprod_id]);

        if (empty($ords)) {
            return array_map(function ($s) {
                return [
                    'id'             => $s->id,
                    'fechahora'      => $s->fechahora,
                    'aprorddesp'     => $s->aprorddesp,
                    'aprorddespfh'   => $s->aprorddespfh,
                    'usuario_nombre' => $s->usuario_nombre,
                    'anulada'        => $s->anulada,
                    'ords'           => [],
                ];
            }, $sols);
        }

        $ordIds = array_unique(array_column($ords, 'id'));
        $ordIdsStr = implode(',', $ordIds);

        // ── 3. Guías de despacho por orden (con flag anulada) ─────────────────
        $guias = DB::select("
            SELECT
                dtg.dte_id              AS id,
                dtg.despachoord_id      AS ord_id,
                dt.nrodocto,
                dt.fechahora,
                dt.aprobstatus,
                dt.aprobfechahora,
                (SELECT COUNT(*) FROM dteanul
                 WHERE  dte_id = dtg.dte_id)  AS anulada,
                ucrea.nombre            AS usuario_nombre,
                uapro.nombre            AS aprobador_nombre
            FROM   dteguiadesp dtg
            INNER  JOIN dte     dt    ON dt.id    = dtg.dte_id
            LEFT   JOIN usuario ucrea ON ucrea.id = dt.usuario_id
            LEFT   JOIN usuario uapro ON uapro.id = dt.aprobusu_id
            WHERE  dtg.despachoord_id IN ($ordIdsStr)
              AND  ISNULL(dt.deleted_at)
            ORDER  BY dtg.dte_id
        ");

        $guiaIds = array_column($guias, 'id');

        // ── 4. Facturas vinculadas a cada guía ────────────────────────────────
        $facturas = [];
        if (!empty($guiaIds)) {
            $guiaIdsStr = implode(',', $guiaIds);
            $facturas = DB::select("
                SELECT
                    dd.dte_id           AS id,
                    dd.dter_id          AS guia_id,
                    dt.nrodocto,
                    dt.fechahora,
                    dt.aprobstatus,
                    dt.aprobfechahora,
                    ucrea.nombre        AS usuario_nombre,
                    uapro.nombre        AS aprobador_nombre
                FROM   dtedte dd
                INNER  JOIN dte     dt    ON dt.id    = dd.dte_id
                LEFT   JOIN usuario ucrea ON ucrea.id = dt.usuario_id
                LEFT   JOIN usuario uapro ON uapro.id = dt.aprobusu_id
                WHERE  dd.dter_id IN ($guiaIdsStr)
                  AND  dt.foliocontrol_id = 1
                  AND  ISNULL(dt.deleted_at)
                  AND  ISNULL(dd.deleted_at)
                ORDER  BY dd.dte_id
            ");
        }

        // ── 5. NC / ND por factura ────────────────────────────────────────────
        $ncnd = [];
        if (!empty($facturas)) {
            $facIds    = array_column($facturas, 'id');
            $facIdsStr = implode(',', $facIds);
            $ncnd = DB::select("
                SELECT
                    dd.dter_id          AS id,
                    dd.dte_id           AS factura_id,
                    dt.nrodocto,
                    dt.foliocontrol_id,
                    dt.fechahora,
                    dt.aprobstatus,
                    dt.aprobfechahora,
                    ucrea.nombre        AS usuario_nombre,
                    uapro.nombre        AS aprobador_nombre
                FROM   dtedte dd
                INNER  JOIN dte     dt    ON dt.id    = dd.dter_id
                LEFT   JOIN usuario ucrea ON ucrea.id = dt.usuario_id
                LEFT   JOIN usuario uapro ON uapro.id = dt.aprobusu_id
                WHERE  dd.dte_id IN ($facIdsStr)
                  AND  dt.foliocontrol_id IN (5, 6)
                  AND  ISNULL(dt.deleted_at)
                  AND  ISNULL(dd.deleted_at)
                ORDER  BY dd.dter_id
            ");
        }

        // ── Construir árbol ───────────────────────────────────────────────────
        $ncndXFac = [];
        foreach ($ncnd as $n) {
            $ncndXFac[$n->factura_id][] = [
                'id'               => $n->id,
                'nrodocto'         => $n->nrodocto,
                'foliocontrol_id'  => $n->foliocontrol_id,
                'fechahora'        => $n->fechahora,
                'aprobstatus'      => $n->aprobstatus,
                'aprobfechahora'   => $n->aprobfechahora,
                'usuario_nombre'   => $n->usuario_nombre,
                'aprobador_nombre' => $n->aprobador_nombre,
            ];
        }

        $facXGuia = [];
        foreach ($facturas as $f) {
            $facXGuia[$f->guia_id][] = [
                'id'               => $f->id,
                'nrodocto'         => $f->nrodocto,
                'fechahora'        => $f->fechahora,
                'aprobstatus'      => $f->aprobstatus,
                'aprobfechahora'   => $f->aprobfechahora,
                'usuario_nombre'   => $f->usuario_nombre,
                'aprobador_nombre' => $f->aprobador_nombre,
                'ncnd'             => $ncndXFac[$f->id] ?? [],
            ];
        }

        $guiasXOrd = [];
        foreach ($guias as $g) {
            $guiasXOrd[$g->ord_id][] = [
                'id'               => $g->id,
                'nrodocto'         => $g->nrodocto,
                'anulada'          => (int)$g->anulada > 0,
                'fechahora'        => $g->fechahora,
                'aprobstatus'      => $g->aprobstatus,
                'aprobfechahora'   => $g->aprobfechahora,
                'usuario_nombre'   => $g->usuario_nombre,
                'aprobador_nombre' => $g->aprobador_nombre,
                'facturas'         => $facXGuia[$g->id] ?? [],
            ];
        }

        $ordsXSol = [];
        foreach ($ords as $o) {
            $ordsXSol[$o->sol_id][] = [
                'id'             => $o->id,
                'anulada'        => (int)$o->anulada > 0,
                'fechahora'      => $o->fechahora,
                'aprguiadesp'    => $o->aprguiadesp,
                'aprguiadespfh'  => $o->aprguiadespfh,
                'usuario_nombre' => $o->usuario_nombre,
                'guias'          => $guiasXOrd[$o->id] ?? [],
            ];
        }

        $tree = [];
        foreach ($sols as $s) {
            $tree[] = [
                'id'             => $s->id,
                'fechahora'      => $s->fechahora,
                'aprorddesp'     => $s->aprorddesp,
                'aprorddespfh'   => $s->aprorddespfh,
                'usuario_nombre' => $s->usuario_nombre,
                'anulada'        => (int)$s->anulada > 0,
                'ords'           => $ordsXSol[$s->id] ?? [],
            ];
        }

        return $tree;
    }
}
