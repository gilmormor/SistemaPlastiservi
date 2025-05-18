<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStockInvbodegaproducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invbodegaproducto', function (Blueprint $table) {
            $table->float('stock',18,2)->comment('Stock unidades')->default(0)->after('invbodega_id');
            $table->float('stockkg',18,2)->comment('Stock kg')->default(0)->after('stock');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invbodegaproducto', function (Blueprint $table) {
            $table->dropColumn('stock');
            $table->dropColumn('stockkg');
        });
    }
}
