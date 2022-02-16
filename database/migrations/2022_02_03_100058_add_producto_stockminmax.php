<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductoStockminmax extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->float('stockmin',18,2)->comment('Stock Minimo')->nullable()->after('tipoprod');
            $table->float('stockmax',18,2)->comment('Stock Maximo')->nullable()->after('stockmin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->dropColumn('stockmin');
            $table->dropColumn('stockmax');
        });
    }
}
