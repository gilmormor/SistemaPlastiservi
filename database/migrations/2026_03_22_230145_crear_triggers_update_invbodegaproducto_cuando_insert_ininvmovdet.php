<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CrearTriggersUpdateInvbodegaproductoCuandoInsertIninvmovdet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER vtrg_stock_after_insert
        AFTER INSERT ON invmovdet
        FOR EACH ROW
        BEGIN
            UPDATE invbodegaproducto
            SET stockprueba = stockprueba + NEW.cant, stockkgprueba = stockkgprueba + NEW.cantkg
            WHERE id = NEW.invbodegaproducto_id;
        END
        ');

        DB::unprepared('
            CREATE TRIGGER vtrg_stock_after_delete
            AFTER DELETE ON invmovdet
            FOR EACH ROW
            BEGIN
                UPDATE invbodegaproducto
                SET stockprueba = stockprueba - OLD.cant, stockkgprueba = stockkgprueba - OLD.cantkg
                WHERE id = OLD.invbodegaproducto_id;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER vtrg_stock_after_update
            AFTER UPDATE ON invmovdet
            FOR EACH ROW
            BEGIN
                -- Revertir el anterior
                UPDATE invbodegaproducto
                SET stockprueba = stockprueba - OLD.cant, stockkgprueba = stockkgprueba - OLD.cantkg
                WHERE id = OLD.invbodegaproducto_id;

                -- Aplicar el nuevo
                UPDATE invbodegaproducto
                SET stock = stockprueba + NEW.cant, stockkgprueba = stockkgprueba + NEW.cantkg
                WHERE id = NEW.invbodegaproducto_id;
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
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_stock_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_stock_after_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_stock_after_update');
    }
}