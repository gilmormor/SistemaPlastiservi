<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAtUnidadmedidaIdAcuerdotecnicotemp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acuerdotecnicotemp', function (Blueprint $table) {
            $table->unsignedBigInteger('at_unidadmedida_id')->nullable()->comment('Codigo unidad de medida.')->after('at_espesordesv');
            $table->foreign('at_unidadmedida_id','fk_acuerdotecnicotemp_unidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acuerdotecnicotemp', function (Blueprint $table) {
            $table->dropForeign('fk_acuerdotecnicotemp_unidadmedida');
            $table->dropColumn('at_unidadmedida_id');
        });
    }
}
