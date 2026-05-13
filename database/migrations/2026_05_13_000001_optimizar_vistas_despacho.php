<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Optimización de vistas que causaban 104% CPU en MySQL del hosting.
// Reemplaza NOT IN (subquery) por LEFT JOIN ... IS NULL (más eficiente en MySQL 5.7).
// Agrega índices para los filtros más frecuentes en despachoord y despachoordrec.
class OptimizarVistasDespacho extends Migration
{
    public function up()
    {
        // Índice compuesto para filtro deleted_at IS NULL AND guiadespacho IS NOT NULL en despachoord
        DB::statement('ALTER TABLE despachoord ADD INDEX idx_despachoord_del_guia (deleted_at, guiadespacho), ALGORITHM=INPLACE, LOCK=NONE');

        // Índice compuesto para filtro aprobstatus = 2 AND anulada IS NULL AND deleted_at IS NULL en despachoordrec
        DB::statement('ALTER TABLE despachoordrec ADD INDEX idx_despachoordrec_filtro (aprobstatus, anulada, deleted_at), ALGORITHM=INPLACE, LOCK=NONE');

        // Reemplaza NOT IN por LEFT JOIN...IS NULL y usa COALESCE en lugar de IF(ISNULL(...))
        DB::statement("CREATE OR REPLACE VIEW vista_sumorddespxnvdetid AS
            SELECT nvd.producto_id,
                   dod.notaventadetalle_id,
                   nvd.cant,
                   SUM(dod.cantdesp)                             AS cantrespreal,
                   rec.cantrec,
                   (SUM(dod.cantdesp) - COALESCE(rec.cantrec, 0)) AS cantdesp,
                   SUM(nvd.totalkilos)                           AS totalkilos,
                   SUM(nvd.totalkilos / nvd.cant)                AS pesoXunitKG,
                   SUM((nvd.totalkilos / nvd.cant) * dod.cantdesp) AS totalkilos1,
                   GROUP_CONCAT(od.guiadespacho SEPARATOR ',')   AS group_guiadespacho
            FROM despachoord od
            JOIN despachoorddet dod   ON dod.despachoord_id = od.id
            JOIN notaventadetalle nvd ON nvd.id = dod.notaventadetalle_id
                                     AND nvd.deleted_at IS NULL
            LEFT JOIN vista_sumrecxnotaventadet_id rec ON rec.id = dod.notaventadetalle_id
            LEFT JOIN despachoordanul da              ON da.despachoord_id = od.id
            WHERE od.guiadespacho IS NOT NULL
              AND od.deleted_at   IS NULL
              AND da.despachoord_id IS NULL
            GROUP BY dod.notaventadetalle_id"
        );

        // Reemplaza NOT IN por LEFT JOIN...IS NULL y mueve condiciones de JOIN a WHERE
        DB::statement("CREATE OR REPLACE VIEW vista_sumrecxdespachosoldet_id AS
            SELECT dod.despachosoldet_id,
                   nvd.notaventa_id,
                   SUM(drdet.cantrec)                                     AS cantrec,
                   SUM(nvd.totalkilos)                                    AS totalkilos,
                   SUM(nvd.totalkilos / nvd.cant)                         AS pesoXunitKG,
                   SUM((nvd.totalkilos / nvd.cant) * dod.cantdesp)        AS totalkilos1,
                   SUM((nvd.totalkilos / nvd.cant) * drdet.cantrec)       AS totalkilosrec,
                   SUM((nvd.subtotal   / nvd.cant) * drdet.cantrec)       AS subtotalrec
            FROM despachoordrec dr
            JOIN despachoordrecdet drdet ON drdet.despachoordrec_id = dr.id
                                        AND drdet.deleted_at IS NULL
            JOIN despachoord od          ON od.id = dr.despachoord_id
            JOIN despachoorddet dod      ON dod.id = drdet.despachoorddet_id
                                        AND dod.deleted_at IS NULL
            JOIN notaventadetalle nvd    ON nvd.id = dod.notaventadetalle_id
                                        AND nvd.deleted_at IS NULL
            LEFT JOIN despachoordanul da ON da.despachoord_id = od.id
            WHERE dr.aprobstatus    = 2
              AND dr.anulada        IS NULL
              AND dr.deleted_at     IS NULL
              AND od.deleted_at     IS NULL
              AND da.despachoord_id IS NULL
            GROUP BY dod.despachosoldet_id, nvd.notaventa_id"
        );
    }

    public function down()
    {
        // Eliminar índices agregados
        try {
            DB::statement('ALTER TABLE despachoord DROP INDEX idx_despachoord_del_guia');
        } catch (\Exception $e) {
            // Índice no existe, se ignora
        }
        try {
            DB::statement('ALTER TABLE despachoordrec DROP INDEX idx_despachoordrec_filtro');
        } catch (\Exception $e) {
            // Índice no existe, se ignora
        }

        // Restaurar vista original con NOT IN
        DB::statement("CREATE OR REPLACE VIEW vista_sumorddespxnvdetid AS
            SELECT `notaventadetalle`.`producto_id` AS `producto_id`,
                   `despachoorddet`.`notaventadetalle_id` AS `notaventadetalle_id`,
                   `notaventadetalle`.`cant` AS `cant`,
                   sum(`despachoorddet`.`cantdesp`) AS `cantrespreal`,
                   `vista_sumrecxnotaventadet_id`.`cantrec` AS `cantrec`,
                   (sum(`despachoorddet`.`cantdesp`) - if(isnull(`vista_sumrecxnotaventadet_id`.`cantrec`),0,`vista_sumrecxnotaventadet_id`.`cantrec`)) AS `cantdesp`,
                   sum(`notaventadetalle`.`totalkilos`) AS `totalkilos`,
                   sum((`notaventadetalle`.`totalkilos` / `notaventadetalle`.`cant`)) AS `pesoXunitKG`,
                   sum(((`notaventadetalle`.`totalkilos` / `notaventadetalle`.`cant`) * `despachoorddet`.`cantdesp`)) AS `totalkilos1`,
                   group_concat(`despachoord`.`guiadespacho` separator ',') AS `group_guiadespacho`
            FROM ((`despachoord`
                JOIN `despachoorddet` ON (`despachoord`.`id` = `despachoorddet`.`despachoord_id`))
                JOIN `notaventadetalle` ON ((`despachoorddet`.`notaventadetalle_id` = `notaventadetalle`.`id`) AND isnull(`notaventadetalle`.`deleted_at`))
                LEFT JOIN `vista_sumrecxnotaventadet_id` ON (`vista_sumrecxnotaventadet_id`.`id` = `despachoorddet`.`notaventadetalle_id`))
            WHERE (`despachoord`.`guiadespacho` IS NOT NULL
                AND not(`despachoord`.`id` in (select `despachoordanul`.`despachoord_id` from `despachoordanul`))
                AND isnull(`despachoord`.`deleted_at`))
            GROUP BY `despachoorddet`.`notaventadetalle_id`"
        );

        // Restaurar vista original con NOT IN
        DB::statement("CREATE OR REPLACE VIEW vista_sumrecxdespachosoldet_id AS
            SELECT `despachoorddet`.`despachosoldet_id` AS `despachosoldet_id`,
                   `notaventadetalle`.`notaventa_id` AS `notaventa_id`,
                   sum(`despachoordrecdet`.`cantrec`) AS `cantrec`,
                   sum(`notaventadetalle`.`totalkilos`) AS `totalkilos`,
                   sum((`notaventadetalle`.`totalkilos` / `notaventadetalle`.`cant`)) AS `pesoXunitKG`,
                   sum(((`notaventadetalle`.`totalkilos` / `notaventadetalle`.`cant`) * `despachoorddet`.`cantdesp`)) AS `totalkilos1`,
                   sum(((`notaventadetalle`.`totalkilos` / `notaventadetalle`.`cant`) * `despachoordrecdet`.`cantrec`)) AS `totalkilosrec`,
                   sum(((`notaventadetalle`.`subtotal` / `notaventadetalle`.`cant`) * `despachoordrecdet`.`cantrec`)) AS `subtotalrec`
            FROM ((((`despachoordrec`
                JOIN `despachoordrecdet` ON ((`despachoordrec`.`id` = `despachoordrecdet`.`despachoordrec_id`) AND isnull(`despachoordrec`.`anulada`) AND isnull(`despachoordrec`.`deleted_at`) AND isnull(`despachoordrecdet`.`deleted_at`)))
                JOIN `despachoord` ON ((`despachoord`.`id` = `despachoordrec`.`despachoord_id`) AND isnull(`despachoord`.`deleted_at`)))
                JOIN `despachoorddet` ON ((`despachoorddet`.`id` = `despachoordrecdet`.`despachoorddet_id`) AND isnull(`despachoorddet`.`deleted_at`)))
                JOIN `notaventadetalle` ON ((`notaventadetalle`.`id` = `despachoorddet`.`notaventadetalle_id`) AND isnull(`notaventadetalle`.`deleted_at`)))
            WHERE (`despachoordrec`.`aprobstatus` = 2
                AND not(`despachoord`.`id` in (select `despachoordanul`.`despachoord_id` from `despachoordanul`)))
            GROUP BY `despachoorddet`.`despachosoldet_id`, `notaventadetalle`.`notaventa_id`"
        );
    }
}
