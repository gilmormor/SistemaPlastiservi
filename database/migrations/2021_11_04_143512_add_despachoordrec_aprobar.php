<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDespachoordrecAprobar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('despachoordrec', function (Blueprint $table) {
            $table->boolean('aprobstatus')->comment('Status de aprobacion (null o 0)=Sin aprobar, 1=enviar a aprobacion, 2=aprobada, 3=Rechazada')->nullable()->after('anulada');
            $table->unsignedBigInteger('aprobusu_id')->comment('Usuario quien aprobo, este es el estatus de aprovacion del rechazo')->nullable()->after('aprobstatus');
            $table->foreign('aprobusu_id','fk_despachoordrec_aprobusu')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->dateTime('aprobfechahora')->comment('Fecha y hora cuando fue aprobada.')->nullable()->after('aprobusu_id');
            $table->string('aprobobs',300)->comment('Observación aprobacion')->nullable()->after('aprobfechahora');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('despachoordrec', function (Blueprint $table) {
            $table->dropColumn('aprobstatus');
            $table->dropForeign('fk_despachoordrec_aprobusu');
            $table->dropColumn('aprobusu_id');
            $table->dropColumn('aprobfechahora');
            $table->dropColumn('aprobobs');
        });
    }
}
