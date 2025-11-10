<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CrearTriggersActualizarKgregprodTableOpdetregprod extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER vtrg_kgregprod_after_insert
            AFTER INSERT ON opdetregprod
            FOR EACH ROW
            BEGIN
                UPDATE opdet
                SET cantprod = cantprod + NEW.cant, kgprod = kgprod + NEW.kg, kgscrap = kgscrap + NEW.kgscrap, mtslineal = mtslineal + NEW.mtslineal, saldokg = saldokg - (NEW.kg + NEW.kgscrap)
                WHERE id = NEW.opdet_id;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER vtrg_kgregprod_after_delete
            AFTER DELETE ON opdetregprod
            FOR EACH ROW
            BEGIN
                UPDATE opdet
                SET cantprod = cantprod - OLD.cant, kgprod = kgprod - OLD.kg, kgscrap = kgscrap - OLD.kgscrap, mtslineal = mtslineal - OLD.mtslineal, saldokg = saldokg + (OLD.kg + OLD.kgscrap)
                WHERE id = OLD.opdet_id;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER vtrg_kgregprod_after_update
            AFTER UPDATE ON opdetregprod
            FOR EACH ROW
            BEGIN
                -- Revertir el anterior
                UPDATE opdet
                SET cantprod = cantprod - OLD.cant, kgprod = kgprod - OLD.kg, kgscrap = kgscrap - OLD.kgscrap, mtslineal = mtslineal - OLD.mtslineal, saldokg = saldokg + (OLD.kg + OLD.kgscrap)
                WHERE id = OLD.opdet_id;

                -- Aplicar el nuevo
                UPDATE opdet
                SET cantprod = cantprod + NEW.cant, kgprod = kgprod + NEW.kg, kgscrap = kgscrap + NEW.kgscrap, mtslineal = mtslineal + NEW.mtslineal, saldokg = saldokg - (NEW.kg + NEW.kgscrap)
                WHERE id = NEW.opdet_id;
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_kgregprod_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_kgregprod_after_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_kgregprod_after_update');
    }
}