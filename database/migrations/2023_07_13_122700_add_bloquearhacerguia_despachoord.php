<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBloquearhacerguiaDespachoord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('despachoord', function (Blueprint $table) {
            $table->tinyInteger('bloquearhacerguia')->comment('Estatus que permite bloquera para hacer una guia de despacho No=0, Si=1')->default(0)->after('aprguiadespfh');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('despachoord', function (Blueprint $table) {
            $table->dropColumn('bloquearhacerguia');
        });
    }
}
