<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStakilosCategoriaprod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categoriaprod', function (Blueprint $table) {
            $table->boolean('stakilos')->default(0)->comment('Estatus para determinar si el producto que pertenece a la categoria debe guardar los kilos totales y precio por Kilo en cotizacion, nota de venta, despacho, Inventario etc. ')->after('asoprodcli');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categoriaprod', function (Blueprint $table) {
            $table->dropColumn('stakilos');
        });
    }
}
