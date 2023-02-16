<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTiposelloIdAcuerdotecnicotemp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acuerdotecnicotemp', function (Blueprint $table) {
            $table->unsignedBigInteger('at_tiposello_id')->comment('Tipo de sellado')->default(1)->after('at_impresoobs');
            $table->foreign('at_tiposello_id','fk_acuerdotecnicotemp_tiposello')->references('id')->on('tiposello')->onDelete('restrict')->onUpdate('restrict');
            $table->string('at_tiposelloobs',200)->nullable()->comment('Observacion tipo sello')->default("")->after('at_tiposello_id');
            $table->tinyInteger('at_pigmentacion')->nullable()->comment('% Pigmentacion materia prima.')->default(0)->after('at_usoprevisto');
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
            $table->dropForeign('fk_acuerdotecnicotemp_tiposello');
            $table->dropColumn('at_tiposello_id');
            $table->dropColumn('at_tiposelloobs');
            $table->dropColumn('at_pigmentacion');
        });
    }
}
