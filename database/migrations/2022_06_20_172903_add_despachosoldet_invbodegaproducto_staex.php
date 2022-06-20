<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDespachosoldetInvbodegaproductoStaex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('despachosoldet_invbodegaproducto', function (Blueprint $table) {
            $table->tinyInteger('staex')->comment('Estatus para marcar todo lo solicitado como exeso, es decir sin tocar el inv')->default(0)->after('cantex');
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
            $table->dropColumn('staex');
        });
    }
}
