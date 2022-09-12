<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCotizaciondetalleAcuerdotecnicotempId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cotizaciondetalle', function (Blueprint $table) {
            $table->unsignedBigInteger('acuerdotecnicotemp_id')->comment('Id Acuerdo tecnico Temporal')->nullable()->after('obs');
            $table->foreign('acuerdotecnicotemp_id','fk_cotizaciondetalle_acuerdotecnicotemp')->references('id')->on('acuerdotecnicotemp')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cotizaciondetalle', function (Blueprint $table) {
            $table->dropForeign('fk_cotizaciondetalle_acuerdotecnicotemp');
            $table->dropColumn('acuerdotecnicotemp_id');
        });
    }
}
