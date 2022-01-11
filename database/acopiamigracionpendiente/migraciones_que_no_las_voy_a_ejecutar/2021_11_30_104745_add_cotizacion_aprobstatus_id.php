<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCotizacionAprobstatusId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cotizacion', function (Blueprint $table) {
            $table->unsignedBigInteger('aprobstatus_id')->comment('Estatus de aprobacion.')->nullable()->after('aprobstatus');
            $table->foreign('aprobstatus_id','fk_cotizacion_aprobstatus')->references('id')->on('aprobstatus')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cotizacion', function (Blueprint $table) {
            $table->dropForeign('fk_cotizacion_aprobstatus');
            $table->dropColumn('aprobstatus_id');
        });
    }
}
