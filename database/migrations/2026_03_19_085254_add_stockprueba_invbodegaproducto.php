<?php

use App\Models\InvBodegaProducto;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStockpruebaInvbodegaproducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invbodegaproducto', function (Blueprint $table) {
            $table->float('stockprueba',18,2)->comment('Stock unidades')->default(0)->after('stockkg');
            $table->float('stockkgprueba',18,2)->comment('Stock kg')->default(0)->after('stockprueba');
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
            $table->dropColumn('stockprueba');
            $table->dropColumn('stockkgprueba');
        });
    }
}
