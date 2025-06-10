<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCantInvmovdetBodsoldesp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invmovdet_bodsoldesp', function (Blueprint $table) {
            $table->float('cant',18,2)->comment('Cantidad')->default(0)->after('despachosoldet_invbodegaproducto_id');
            $table->float('cantkg',18,2)->comment('Cantidad en kg')->default(0)->after('cant');
            $table->tinyInteger('tipo')->comment('Tipo de bodega. 1=Bodega solo para apartado Solicitud de despacho 2=Bodega antes de la orden despacho es decir solo para movimiento interno antes del despacho (Ingresos y egresos del inventario), 3=Bodega de despacho es decir solo es tocado por la guia de despacho no se toca en entrada y salidas Inv, 4=Bodega Scrap')->default(0)->after('cantkg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invmovdet_bodsoldesp', function (Blueprint $table) {
            $table->dropColumn('cant');
            $table->dropColumn('cantkg');
            $table->dropColumn('tipo');
        });
    }
}
