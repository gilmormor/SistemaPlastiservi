<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CrearTriggersActualizarStockTableDespachosoldetInvbodegaproducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER vtrg_stock_picking_after_insert
        AFTER INSERT ON invmovdet_bodsoldesp
        FOR EACH ROW
        BEGIN
            UPDATE despachosoldet_invbodegaproducto
            SET stock = stock + NEW.cant, stockkg = stockkg + NEW.cantkg
            WHERE id = NEW.despachosoldet_invbodegaproducto_id;
        END
        ');

        DB::unprepared('
            CREATE TRIGGER vtrg_stock_picking_after_delete
            AFTER DELETE ON invmovdet_bodsoldesp
            FOR EACH ROW
            BEGIN
                UPDATE despachosoldet_invbodegaproducto
                SET stock = stock - OLD.cant, stockkg = stockkg - OLD.cantkg
                WHERE id = OLD.despachosoldet_invbodegaproducto_id;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER vtrg_stock_picking_after_update
            AFTER UPDATE ON invmovdet_bodsoldesp
            FOR EACH ROW
            BEGIN
                -- Revertir el anterior
                UPDATE despachosoldet_invbodegaproducto
                SET stock = stock - OLD.cant, stockkg = stockkg - OLD.cantkg
                WHERE id = OLD.despachosoldet_invbodegaproducto_id;

                -- Aplicar el nuevo
                UPDATE despachosoldet_invbodegaproducto
                SET stock = stock + NEW.cant, stockkg = stockkg + NEW.cantkg
                WHERE id = NEW.despachosoldet_invbodegaproducto_id;
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
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_stock_picking_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_stock_picking_after_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_stock_picking_after_update');
    }
}
