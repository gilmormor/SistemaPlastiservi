<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAtFirmadoTableAcuerdotecnicotemp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acuerdotecnicotemp', function (Blueprint $table) {
            $table->string('at_firmado')->comment('Nombre de archivo de acuerdotecnico firmado.')->after('at_aprobado')->nullable();
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
            $table->dropColumn('at_firmado');
        });
    }
}
