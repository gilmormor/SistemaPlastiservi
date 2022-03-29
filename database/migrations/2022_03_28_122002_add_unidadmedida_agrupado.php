<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnidadmedidaAgrupado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unidadmedida', function (Blueprint $table) {
            $table->tinyInteger('agrupado')->comment('Estatus si agrupa el producto en Cajas, Paquetes etc')->after('mostrarfact');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unidadmedida', function (Blueprint $table) {
            $table->dropColumn('agrupado');
        });
    }
}
