<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductoTipoprod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->tinyInteger('tipoprod')->comment('0=producto normal, 1=producto para hacer acuerdo tecnico temporal no se muestra en las busquedas normales de productos')->default(0)->after('color_id');
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
            $table->dropColumn('tipoprod');
        });
    }
}
