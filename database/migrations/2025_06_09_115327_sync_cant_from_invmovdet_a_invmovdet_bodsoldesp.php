<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncCantFromInvmovdetAInvmovdetBodsoldesp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            UPDATE invmovdet_bodsoldesp AS bsd
            JOIN invmovdet AS md ON md.id = bsd.invmovdet_id
            JOIN invbodega AS ib ON ib.id = md.invbodega_id
            SET 
                bsd.cant = md.cant,
                bsd.cantkg = md.cantkg,
                bsd.tipo = ib.tipo
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Si quieres revertir, podrías dejar los campos en null o en 0
        DB::statement("
            UPDATE invmovdet_bodsoldesp
            SET cant = NULL, cantkg = NULL, tipo = NULL
        ");
    }
}
