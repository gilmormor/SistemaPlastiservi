<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAtClaseprodIdAcuerdotecnicotemp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acuerdotecnicotemp', function (Blueprint $table) {
            $table->unsignedBigInteger('at_cotizaciondetalle_id')->nullable()->after('id');
            $table->foreign('at_cotizaciondetalle_id','fk_acuerdotecnicotemp_cotizaciondetalle')->references('id')->on('cotizaciondetalle')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('at_claseprod_id')->nullable()->after('at_cotizaciondetalle_id');
            $table->foreign('at_claseprod_id','fk_acuerdotecnicotemp_claseprod')->references('id')->on('claseprod')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('at_grupoprod_id')->nullable()->after('at_claseprod_id');
            $table->foreign('at_grupoprod_id','fk_acuerdotecnicotemp_grupoprod')->references('id')->on('grupoprod')->onDelete('restrict')->onUpdate('restrict');
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
            $table->dropForeign('fk_acuerdotecnicotemp_claseprod');
            $table->dropForeign('fk_acuerdotecnicotemp_grupoprod');
            $table->dropForeign('fk_acuerdotecnicotemp_cotizaciondetalle');
            $table->dropColumn('at_claseprod_id');
            $table->dropColumn('at_grupoprod_id');
            $table->dropColumn('at_cotizaciondetalle_id');
        });
    }
}
