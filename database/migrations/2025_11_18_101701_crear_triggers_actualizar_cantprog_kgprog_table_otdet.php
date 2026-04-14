<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CrearTriggersActualizarCantprogKgprogTableOtdet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER vtrg_otdet_op_after_insert
            AFTER INSERT ON op
            FOR EACH ROW
            BEGIN
                UPDATE otdet
                SET cantprog = cantprog + NEW.cantprod, kgprog = kgprog + NEW.kgprod
                WHERE id = NEW.otdet_id;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER vtrg_otdet_op_after_delete
            AFTER DELETE ON op
            FOR EACH ROW
            BEGIN
                UPDATE otdet
                SET cantprog = cantprog - OLD.cantprod, kgprog = kgprog - OLD.kgprod
                WHERE id = OLD.otdet_id;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER vtrg_otdet_op_after_update
            AFTER UPDATE ON op
            FOR EACH ROW
            BEGIN
                -- Revertir el anterior
                UPDATE otdet
                SET cantprog = cantprog - OLD.cantprod, kgprog = kgprog - OLD.kgprod
                WHERE id = OLD.otdet_id;

                -- Aplicar el nuevo
                UPDATE otdet
                SET cantprog = cantprog + NEW.cantprod, kgprog = kgprog + NEW.kgprod
                WHERE id = NEW.otdet_id;
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
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_otdet_op_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_otdet_op_after_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS vtrg_otdet_op_after_update');
    }
}
