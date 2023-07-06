<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAtUnidadmedidaIdAcuerdotecnico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acuerdotecnico', function (Blueprint $table) {
            $table->unsignedBigInteger('at_unidadmedida_id')->nullable()->comment('Codigo unidad de medida Base. De aqui se toma para las notas de venta y todo lo demas.')->after('at_espesordesv');
            $table->foreign('at_unidadmedida_id','fk_acuerdotecnico_unidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acuerdotecnico', function (Blueprint $table) {
            $table->dropForeign('fk_acuerdotecnico_unidadmedida');
            $table->dropColumn('at_unidadmedida_id');
        });
    }
}
