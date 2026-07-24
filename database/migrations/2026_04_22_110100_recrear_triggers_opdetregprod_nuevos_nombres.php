<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Recrea los triggers de opdetregprod usando los nuevos nombres de columna
 * (cantent/kgent/cantprod/kgprod/kgscrap) que se renombraron en la migracion
 * 2026_04_22_110000_rename_campos_opdetregprod.
 *
 * Mapeo al actualizar opdet (totales agregados por etapa):
 *   opdet.cantprod   <- suma de opdetregprod.cantprod   (cantidad producida en UM salida)
 *   opdet.kgprod     <- suma de opdetregprod.kgprod     (kg realmente producidos)
 *   opdet.kgscrap    <- suma de opdetregprod.kgscrap
 *   opdet.mtslineal  <- suma de opdetregprod.mtslineal
 *   opdet.saldokg    <- kgrec - (kgprod + kgscrap) acumulado
 *
 * Nota: antes los triggers sumaban opdet.kgprod = opdet.kgprod + NEW.kg, donde
 * NEW.kg era el "total producido"; ahora ese valor vive en NEW.kgprod y es
 * explicito que no incluye scrap.
 */
class RecrearTriggersOpdetregprodNuevosNombres extends Migration
{
    public function up()
    {
        // Seguridad: si quedaron creados por una ejecucion previa, los eliminamos.
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_kgregprod_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_kgregprod_after_update');
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_kgregprod_after_delete');

        DB::unprepared('
            CREATE TRIGGER vtrg_kgregprod_after_insert
            AFTER INSERT ON opdetregprod
            FOR EACH ROW
            BEGIN
                UPDATE opdet
                SET cantprod  = cantprod  + IFNULL(NEW.cantprod, 0),
                    kgprod    = kgprod    + IFNULL(NEW.kgprod, 0),
                    kgscrap   = kgscrap   + IFNULL(NEW.kgscrap, 0),
                    mtslineal = mtslineal + IFNULL(NEW.mtslineal, 0),
                    saldokg   = saldokg   - (IFNULL(NEW.kgprod, 0) + IFNULL(NEW.kgscrap, 0))
                WHERE id = NEW.opdet_id;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER vtrg_kgregprod_after_delete
            AFTER DELETE ON opdetregprod
            FOR EACH ROW
            BEGIN
                UPDATE opdet
                SET cantprod  = cantprod  - IFNULL(OLD.cantprod, 0),
                    kgprod    = kgprod    - IFNULL(OLD.kgprod, 0),
                    kgscrap   = kgscrap   - IFNULL(OLD.kgscrap, 0),
                    mtslineal = mtslineal - IFNULL(OLD.mtslineal, 0),
                    saldokg   = saldokg   + (IFNULL(OLD.kgprod, 0) + IFNULL(OLD.kgscrap, 0))
                WHERE id = OLD.opdet_id;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER vtrg_kgregprod_after_update
            AFTER UPDATE ON opdetregprod
            FOR EACH ROW
            BEGIN
                -- Revertir valores viejos
                UPDATE opdet
                SET cantprod  = cantprod  - IFNULL(OLD.cantprod, 0),
                    kgprod    = kgprod    - IFNULL(OLD.kgprod, 0),
                    kgscrap   = kgscrap   - IFNULL(OLD.kgscrap, 0),
                    mtslineal = mtslineal - IFNULL(OLD.mtslineal, 0),
                    saldokg   = saldokg   + (IFNULL(OLD.kgprod, 0) + IFNULL(OLD.kgscrap, 0))
                WHERE id = OLD.opdet_id;

                -- Aplicar valores nuevos
                UPDATE opdet
                SET cantprod  = cantprod  + IFNULL(NEW.cantprod, 0),
                    kgprod    = kgprod    + IFNULL(NEW.kgprod, 0),
                    kgscrap   = kgscrap   + IFNULL(NEW.kgscrap, 0),
                    mtslineal = mtslineal + IFNULL(NEW.mtslineal, 0),
                    saldokg   = saldokg   - (IFNULL(NEW.kgprod, 0) + IFNULL(NEW.kgscrap, 0))
                WHERE id = NEW.opdet_id;
            END
        ');
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_kgregprod_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_kgregprod_after_update');
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_kgregprod_after_delete');
    }
}
