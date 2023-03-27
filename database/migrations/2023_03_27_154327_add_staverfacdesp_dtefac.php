<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStaverfacdespDtefac extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dtefac', function (Blueprint $table) {
            $table->tinyInteger('staverfacdesp')->comment('Estatus para permitir ver la factura en despacho No=0, Si=1')->default(0)->after('fchvenc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dtefac', function (Blueprint $table) {
            $table->dropColumn('staverfacdesp');
        });
    }
}
