<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStockDespachosoldetInvbodegaproducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('despachosoldet_invbodegaproducto', function (Blueprint $table) {
            $table->float('stock',18,2)->comment('Stock unidades.')->default(0)->after('staex');
            $table->float('stockkg',18,2)->comment('Stock kg.')->default(0)->after('stock');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('despachosoldet_invbodegaproducto', function (Blueprint $table) {
            $table->dropColumn('stock');
            $table->dropColumn('stockkg');
        });
    }
}
