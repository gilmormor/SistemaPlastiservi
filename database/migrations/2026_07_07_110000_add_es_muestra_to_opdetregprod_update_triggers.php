<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * R1 — Opción B:
 * - Agrega es_muestra a opdetregprod (espejo del campo en opdetregprodtemp)
 * - Actualiza los triggers para que los registros con es_muestra=1 NO acumulen
 *   kgprod/cantprod en opdet (la muestra no es producción real)
 */
class AddEsMuestraToOpdetregprodUpdateTriggers extends Migration
{
    public function up()
    {
        Schema::table('opdetregprod', function (Blueprint $table) {
            $table->tinyInteger('es_muestra')->default(0)->after('mtslineal');
        });

        // Recrear los 3 triggers con el chequeo de es_muestra
        DB::unprepared("DROP TRIGGER IF EXISTS vtrg_kgregprod_after_insert");
        DB::unprepared("
            CREATE TRIGGER vtrg_kgregprod_after_insert
            AFTER INSERT ON opdetregprod
            FOR EACH ROW
            BEGIN
                IF IFNULL(NEW.es_muestra, 0) = 0 THEN
                    UPDATE opdet
                    SET cantprod  = cantprod  + IFNULL(NEW.cantprod, 0),
                        kgprod    = kgprod    + IFNULL(NEW.kgprod, 0),
                        kgscrap   = kgscrap   + IFNULL(NEW.kgscrap, 0),
                        mtslineal = mtslineal + IFNULL(NEW.mtslineal, 0),
                        saldokg   = saldokg   - (IFNULL(NEW.kgprod, 0) + IFNULL(NEW.kgscrap, 0))
                    WHERE id = NEW.opdet_id;
                END IF;
            END
        ");

        DB::unprepared("DROP TRIGGER IF EXISTS vtrg_kgregprod_after_update");
        DB::unprepared("
            CREATE TRIGGER vtrg_kgregprod_after_update
            AFTER UPDATE ON opdetregprod
            FOR EACH ROW
            BEGIN
                -- Revertir valores viejos (solo si no era muestra)
                IF IFNULL(OLD.es_muestra, 0) = 0 THEN
                    UPDATE opdet
                    SET cantprod  = cantprod  - IFNULL(OLD.cantprod, 0),
                        kgprod    = kgprod    - IFNULL(OLD.kgprod, 0),
                        kgscrap   = kgscrap   - IFNULL(OLD.kgscrap, 0),
                        mtslineal = mtslineal - IFNULL(OLD.mtslineal, 0),
                        saldokg   = saldokg   + (IFNULL(OLD.kgprod, 0) + IFNULL(OLD.kgscrap, 0))
                    WHERE id = OLD.opdet_id;
                END IF;

                -- Aplicar valores nuevos (solo si no es muestra)
                IF IFNULL(NEW.es_muestra, 0) = 0 THEN
                    UPDATE opdet
                    SET cantprod  = cantprod  + IFNULL(NEW.cantprod, 0),
                        kgprod    = kgprod    + IFNULL(NEW.kgprod, 0),
                        kgscrap   = kgscrap   + IFNULL(NEW.kgscrap, 0),
                        mtslineal = mtslineal + IFNULL(NEW.mtslineal, 0),
                        saldokg   = saldokg   - (IFNULL(NEW.kgprod, 0) + IFNULL(NEW.kgscrap, 0))
                    WHERE id = NEW.opdet_id;
                END IF;
            END
        ");

        DB::unprepared("DROP TRIGGER IF EXISTS vtrg_kgregprod_after_delete");
        DB::unprepared("
            CREATE TRIGGER vtrg_kgregprod_after_delete
            AFTER DELETE ON opdetregprod
            FOR EACH ROW
            BEGIN
                IF IFNULL(OLD.es_muestra, 0) = 0 THEN
                    UPDATE opdet
                    SET cantprod  = cantprod  - IFNULL(OLD.cantprod, 0),
                        kgprod    = kgprod    - IFNULL(OLD.kgprod, 0),
                        kgscrap   = kgscrap   - IFNULL(OLD.kgscrap, 0),
                        mtslineal = mtslineal - IFNULL(OLD.mtslineal, 0),
                        saldokg   = saldokg   + (IFNULL(OLD.kgprod, 0) + IFNULL(OLD.kgscrap, 0))
                    WHERE id = OLD.opdet_id;
                END IF;
            END
        ");
    }

    public function down()
    {
        Schema::table('opdetregprod', function (Blueprint $table) {
            $table->dropColumn('es_muestra');
        });

        // Restaurar triggers originales (sin chequeo es_muestra)
        DB::unprepared("DROP TRIGGER IF EXISTS vtrg_kgregprod_after_insert");
        DB::unprepared("
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
        ");

        DB::unprepared("DROP TRIGGER IF EXISTS vtrg_kgregprod_after_update");
        DB::unprepared("
            CREATE TRIGGER vtrg_kgregprod_after_update
            AFTER UPDATE ON opdetregprod
            FOR EACH ROW
            BEGIN
                UPDATE opdet
                SET cantprod  = cantprod  - IFNULL(OLD.cantprod, 0),
                    kgprod    = kgprod    - IFNULL(OLD.kgprod, 0),
                    kgscrap   = kgscrap   - IFNULL(OLD.kgscrap, 0),
                    mtslineal = mtslineal - IFNULL(OLD.mtslineal, 0),
                    saldokg   = saldokg   + (IFNULL(OLD.kgprod, 0) + IFNULL(OLD.kgscrap, 0))
                WHERE id = OLD.opdet_id;

                UPDATE opdet
                SET cantprod  = cantprod  + IFNULL(NEW.cantprod, 0),
                    kgprod    = kgprod    + IFNULL(NEW.kgprod, 0),
                    kgscrap   = kgscrap   + IFNULL(NEW.kgscrap, 0),
                    mtslineal = mtslineal + IFNULL(NEW.mtslineal, 0),
                    saldokg   = saldokg   - (IFNULL(NEW.kgprod, 0) + IFNULL(NEW.kgscrap, 0))
                WHERE id = NEW.opdet_id;
            END
        ");

        DB::unprepared("DROP TRIGGER IF EXISTS vtrg_kgregprod_after_delete");
        DB::unprepared("
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
        ");
    }
}
