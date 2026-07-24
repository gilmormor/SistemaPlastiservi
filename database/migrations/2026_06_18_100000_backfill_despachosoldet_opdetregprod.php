<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Backfill de trazabilidad de despacho para solicitudes creadas antes del 2026-06-09.
 *
 * La tabla despachosoldet_opdetregprod no existía cuando se crearon esas SD.
 * Se reconstruye la relación a través de:
 *   despachosoldet.notaventadetalle_id → invmovdet_opdetregprod → opdetregprod_id
 *
 * Solo se procesan líneas (nvdet) con UN ÚNICO lote de producción asociado,
 * ya que los casos multi-lote son ambiguos (no se puede determinar qué fracción
 * de cada lote fue despachada sin los datos del formulario original).
 *
 * cant   = cantsoldesp del despachosoldet (unidades despachadas en esa línea)
 * cantkg = suma ABS(invmovdet.cantkg) de salidas SOLDESP para ese (despachosol, nvdet)
 */
class BackfillDespachosoldetOpdetregprod extends Migration
{
    public function up()
    {
        $now = now()->format('Y-m-d H:i:s');

        // Obtener registros históricos faltantes
        $rows = DB::select("
            SELECT
                dsd.id               AS despachosoldet_id,
                lote_unico.opdetregprod_id,
                dsd.cantsoldesp      AS cant,
                kg_sub.cantkg_total  AS cantkg
            FROM despachosoldet dsd
            -- Solo nvdet con exactamente 1 lote de producción (evita ambigüedad)
            INNER JOIN (
                SELECT notaventadetalle_id, MAX(opdetregprod_id) AS opdetregprod_id
                FROM invmovdet_opdetregprod
                GROUP BY notaventadetalle_id
                HAVING COUNT(DISTINCT opdetregprod_id) = 1
            ) lote_unico ON lote_unico.notaventadetalle_id = dsd.notaventadetalle_id
            -- Suma de kg de salidas SOLDESP para ese (despachosol, nvdet)
            -- GROUP BY maneja SD con múltiples invmov
            INNER JOIN (
                SELECT  im.idmovmod               AS despachosol_id,
                        imdn.notaventadetalle_id,
                        SUM(ABS(imd.cantkg))      AS cantkg_total
                FROM    invmov im
                INNER JOIN invmovdet imd   ON imd.invmov_id = im.id
                                          AND imd.deleted_at IS NULL
                                          AND imd.cant < 0
                INNER JOIN invmovdetnvdet imdn ON imdn.invmovdet_id = imd.id
                WHERE   im.invmovmodulo_id = 5
                  AND   im.deleted_at IS NULL
                GROUP BY im.idmovmod, imdn.notaventadetalle_id
            ) kg_sub ON kg_sub.despachosol_id    = dsd.despachosol_id
                    AND kg_sub.notaventadetalle_id = dsd.notaventadetalle_id
            -- Excluir los que ya están registrados
            LEFT JOIN despachosoldet_opdetregprod dsop
                   ON dsop.despachosoldet_id  = dsd.id
                  AND dsop.opdetregprod_id    = lote_unico.opdetregprod_id
            WHERE dsd.deleted_at IS NULL
              AND dsop.id IS NULL
        ");

        foreach ($rows as $row) {
            DB::table('despachosoldet_opdetregprod')->insert([
                'despachosoldet_id' => $row->despachosoldet_id,
                'opdetregprod_id'   => $row->opdetregprod_id,
                'cant'              => $row->cant,
                'cantkg'            => $row->cantkg,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);
        }
    }

    public function down()
    {
        // Eliminar solo los registros insertados por este backfill
        // (los que no existían antes del 2026-06-09 y tienen nvdet con 1 solo lote)
        DB::statement("
            DELETE dsop
            FROM despachosoldet_opdetregprod dsop
            INNER JOIN (
                SELECT notaventadetalle_id, MAX(opdetregprod_id) AS opdetregprod_id
                FROM invmovdet_opdetregprod
                GROUP BY notaventadetalle_id
                HAVING COUNT(DISTINCT opdetregprod_id) = 1
            ) lote_unico ON lote_unico.opdetregprod_id = dsop.opdetregprod_id
            INNER JOIN despachosoldet dsd ON dsd.id = dsop.despachosoldet_id
            WHERE dsd.created_at < '2026-06-09 00:00:00'
        ");
    }
}
