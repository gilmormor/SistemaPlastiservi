<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddNotaventadetalleidToInvmovdetOpdetregprod extends Migration
{
    /**
     * Agrega notaventadetalle_id (nullable) a invmovdet_opdetregprod.
     *
     * Permite saber, para un ítem de NV (notaventadetalle), cuánto se ha
     * fabricado y está en bodega de producción, sumando los invmovdet
     * relacionados sin necesidad de hacer JOINs a través de toda la cadena
     * productiva.
     *
     * NULL cuando la OT no proviene de una NV (producción para stock).
     *
     * Cadena de resolución:
     *   invmovdet_opdetregprod → opdetregprod → opdet → otdet → otdetnvdet → notaventadetalle
     */
    public function up()
    {
        Schema::table('invmovdet_opdetregprod', function (Blueprint $table) {
            $table->unsignedBigInteger('notaventadetalle_id')
                  ->nullable()
                  ->after('opdetregprod_id')
                  ->comment('FK a notaventadetalle; NULL cuando la OT no viene de NV');

            $table->foreign('notaventadetalle_id')
                  ->references('id')->on('notaventadetalle')
                  ->onDelete('set null');

            $table->index('notaventadetalle_id');
        });

        // Poblar registros existentes resolviendo la cadena completa por SQL:
        //   invmovdet_opdetregprod
        //     JOIN opdetregprod  ON opdetregprod.id     = invmovdet_opdetregprod.opdetregprod_id
        //     JOIN opdet         ON opdet.id            = opdetregprod.opdet_id
        //     JOIN op            ON op.id               = opdet.op_id          ← otdet_id está en op
        //     JOIN otdetnvdet    ON otdetnvdet.otdet_id = op.otdet_id
        DB::statement("
            UPDATE invmovdet_opdetregprod iodr
            JOIN opdetregprod  odrp ON odrp.id       = iodr.opdetregprod_id
                                    AND odrp.deleted_at IS NULL
            JOIN opdet         od   ON od.id         = odrp.opdet_id
                                    AND od.deleted_at   IS NULL
            JOIN op            op   ON op.id         = od.op_id
                                    AND op.deleted_at   IS NULL
            JOIN otdetnvdet    nvd  ON nvd.otdet_id  = op.otdet_id
            SET iodr.notaventadetalle_id = nvd.notaventadetalle_id
            WHERE nvd.notaventadetalle_id IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('invmovdet_opdetregprod', function (Blueprint $table) {
            $table->dropForeign(['notaventadetalle_id']);
            $table->dropIndex(['notaventadetalle_id']);
            $table->dropColumn('notaventadetalle_id');
        });
    }
}
