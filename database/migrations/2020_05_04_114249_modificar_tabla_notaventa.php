<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModificarTablaNotaventa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notaventa', function (Blueprint $table) {
            $table->dateTime('visto')->comment('Estatus Visto por el jefe de Vendedores. Fecha hora')->nullable()->after('anulada');
            $table->dateTime('inidespacho')->comment('Fecha Inicio de despacho')->nullable();
            $table->string('guiasdespacho',100)->comment('Guias de despacho ')->nullable()->after('inidespacho');
            $table->dateTime('findespacho')->comment('Fecha Fin de despacho')->nullable()->after('guiasdespacho');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notaventa', function (Blueprint $table) {
            //
        });
    }
}
