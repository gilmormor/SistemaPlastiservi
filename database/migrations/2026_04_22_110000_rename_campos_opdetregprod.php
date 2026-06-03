<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Refactor de nombres de campos en opdetregprodtemp y opdetregprod.
 *
 * Objetivo: eliminar la ambiguedad entre "cantidad entrada al proceso"
 * y "cantidad salida/producida", sobre todo cuando la UM entrada != UM salida
 * (ej. en etapa de corte: entra rollo, sale bolsa).
 *
 * Renombres (ambas tablas):
 *   cant              -> cantent              (cant. entrada en UM entrada)
 *   kg                -> kgent                (kg entrada al proceso)
 *   cantsal           -> cantprod             (cant. producida en UM salida)
 *   unidadmedida_id   -> unidadmedidaent_id   (UM entrada)
 *
 * Nuevo:
 *   kgprod  (kg efectivamente producidos = kgent - kgscrap)
 *
 * Se mantienen sin cambio: kgscrap, unidadmedidasal_id, mtslineal.
 * Invariante: kgent = kgprod + kgscrap
 *
 * Esta migracion es idempotente: verifica el estado actual de cada columna
 * y FK antes de cada operacion, por lo que puede re-ejecutarse sin error.
 */
class RenameCamposOpdetregprod extends Migration
{
    public function up()
    {
        // 0) Dropear triggers que referencian los nombres viejos.
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_kgregprod_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_kgregprod_after_update');
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_kgregprod_after_delete');

        foreach (['opdetregprodtemp', 'opdetregprod'] as $tabla) {
            $fkUM = ($tabla === 'opdetregprodtemp')
                ? 'fk_opdetregprodtemp_unidadmedida'
                : 'fk_opdetregprod_unidadmedida';

            // 1) Drop FK sobre unidadmedida_id si todavia existe.
            if ($this->fkExiste($tabla, $fkUM)) {
                DB::statement("ALTER TABLE {$tabla} DROP FOREIGN KEY {$fkUM}");
            }

            // 2) Renombres con CHANGE COLUMN (solo si el nombre viejo todavia existe).
            if (Schema::hasColumn($tabla, 'cant')) {
                DB::statement("ALTER TABLE {$tabla}
                    CHANGE COLUMN `cant` `cantent` DOUBLE(10,2) NULL
                    COMMENT 'Cantidad entrada al proceso, expresada en unidadmedidaent_id'");
            }

            if (Schema::hasColumn($tabla, 'kg')) {
                DB::statement("ALTER TABLE {$tabla}
                    CHANGE COLUMN `kg` `kgent` DOUBLE(18,2) NULL
                    COMMENT 'Kilogramos entrada al proceso (materia prima / input de la etapa anterior)'");
            }

            if (Schema::hasColumn($tabla, 'cantsal')) {
                DB::statement("ALTER TABLE {$tabla}
                    CHANGE COLUMN `cantsal` `cantprod` DOUBLE(10,2) NULL
                    COMMENT 'Cantidad producida (salida), expresada en unidadmedidasal_id. 0 mientras el rollo/bolsa/pieza no se cierra.'");
            }

            if (Schema::hasColumn($tabla, 'unidadmedida_id')) {
                DB::statement("ALTER TABLE {$tabla}
                    CHANGE COLUMN `unidadmedida_id` `unidadmedidaent_id` BIGINT(20) UNSIGNED NULL
                    COMMENT 'Unidad de medida de ENTRADA al proceso (tipicamente kg).'");
            }

            // 3) Agregar kgprod si no existe.
            if (!Schema::hasColumn($tabla, 'kgprod')) {
                DB::statement("ALTER TABLE {$tabla}
                    ADD COLUMN `kgprod` DOUBLE(18,2) NULL
                    COMMENT 'Kilogramos producidos (salida real) = kgent - kgscrap. Lo que pasa a la siguiente etapa o a bodega.'
                    AFTER `kgent`");
            }

            // 4) Comentarios en campos que se mantienen.
            DB::statement("ALTER TABLE {$tabla}
                MODIFY COLUMN `kgscrap` DOUBLE(18,2) NOT NULL DEFAULT 0
                COMMENT 'Kilogramos de scrap/descarte generados en este registro. Invariante: kgent = kgprod + kgscrap'");

            DB::statement("ALTER TABLE {$tabla}
                MODIFY COLUMN `unidadmedidasal_id` BIGINT(20) UNSIGNED NULL
                COMMENT 'Unidad de medida de SALIDA del proceso (rollo, bolsa, pieza, kg, etc. segun la etapa).'");

            // 5) Backfill kgprod para registros historicos.
            DB::statement("UPDATE {$tabla}
                SET kgprod = IFNULL(kgent, 0) - IFNULL(kgscrap, 0)
                WHERE kgprod IS NULL");

            // 6) Recrear FK sobre el nuevo nombre de columna si no existe.
            if (!$this->fkExiste($tabla, $fkUM)) {
                DB::statement("ALTER TABLE {$tabla}
                    ADD CONSTRAINT {$fkUM}
                    FOREIGN KEY (unidadmedidaent_id) REFERENCES unidadmedida(id)
                    ON DELETE RESTRICT ON UPDATE RESTRICT");
            }
        }
    }

    public function down()
    {
        foreach (['opdetregprodtemp', 'opdetregprod'] as $tabla) {
            $fkUM = ($tabla === 'opdetregprodtemp')
                ? 'fk_opdetregprodtemp_unidadmedida'
                : 'fk_opdetregprod_unidadmedida';

            if ($this->fkExiste($tabla, $fkUM)) {
                DB::statement("ALTER TABLE {$tabla} DROP FOREIGN KEY {$fkUM}");
            }

            if (Schema::hasColumn($tabla, 'kgprod')) {
                DB::statement("ALTER TABLE {$tabla} DROP COLUMN `kgprod`");
            }
            if (Schema::hasColumn($tabla, 'unidadmedidaent_id')) {
                DB::statement("ALTER TABLE {$tabla}
                    CHANGE COLUMN `unidadmedidaent_id` `unidadmedida_id` BIGINT(20) UNSIGNED NULL
                    COMMENT 'Unidad de Medida.'");
            }
            if (Schema::hasColumn($tabla, 'cantprod')) {
                DB::statement("ALTER TABLE {$tabla}
                    CHANGE COLUMN `cantprod` `cantsal` DOUBLE(10,2) NULL");
            }
            if (Schema::hasColumn($tabla, 'kgent')) {
                DB::statement("ALTER TABLE {$tabla}
                    CHANGE COLUMN `kgent` `kg` DOUBLE(18,2) NULL
                    COMMENT 'Total Kg producidos'");
            }
            if (Schema::hasColumn($tabla, 'cantent')) {
                DB::statement("ALTER TABLE {$tabla}
                    CHANGE COLUMN `cantent` `cant` DOUBLE(10,2) NULL
                    COMMENT 'Cantidad producida'");
            }

            if (!$this->fkExiste($tabla, $fkUM)) {
                DB::statement("ALTER TABLE {$tabla}
                    ADD CONSTRAINT {$fkUM}
                    FOREIGN KEY (unidadmedida_id) REFERENCES unidadmedida(id)
                    ON DELETE RESTRICT ON UPDATE RESTRICT");
            }
        }
    }

    private function fkExiste($tabla, $nombreFK)
    {
        $r = DB::select("
            SELECT COUNT(*) AS c
            FROM information_schema.table_constraints
            WHERE table_schema = DATABASE()
              AND table_name = ?
              AND constraint_name = ?
              AND constraint_type = 'FOREIGN KEY'
        ", [$tabla, $nombreFK]);
        return $r[0]->c > 0;
    }
}
