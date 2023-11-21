<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsuarioeditIdAcuerdotecnicotemp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acuerdotecnicotemp', function (Blueprint $table) {
            $table->unsignedBigInteger('usuarioedit_id')->comment('Usuario Supervisor que editó el registro')->nullable()->after('usuariodel_id');
            $table->foreign('usuarioedit_id','fk_acuerdotecnicotemp_usuarioedit')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
            $table->dropForeign('fk_acuerdotecnicotemp_usuarioedit');
            $table->dropColumn('usuarioedit_id');
        });
    }
}
