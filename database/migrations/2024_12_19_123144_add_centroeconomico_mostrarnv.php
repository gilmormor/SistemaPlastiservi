<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCentroeconomicoMostrarnv extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('centroeconomico', function (Blueprint $table) {
            $table->tinyInteger('mostrarnv')->comment('Estatus para mostra o no registro en nota Venta.')->default(0)->after('desc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('centroeconomico', function (Blueprint $table) {
            $table->dropColumn('mostrarnv');
        });
    }
}
