<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoguiadespDespachosol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('despachosol', function (Blueprint $table) {
            $table->tinyInteger('tipoguiadesp')->default("0")->comment('Tipo Guia despacho para emitir guia SII en despacho 1=Precio, 2=Traslado, 20=Traslado + Precio')->after('aprorddespfh');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('despachosol', function (Blueprint $table) {
            $table->dropColumn('tipoguiadesp');
        });
    }
}
