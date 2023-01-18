<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAtClaseprodIdAcuerdotecnico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acuerdotecnico', function (Blueprint $table) {
            $table->unsignedBigInteger('at_notaventadetalle_id')->nullable()->after('id');
            $table->foreign('at_notaventadetalle_id','fk_acuerdotecnico_notaventadetalle')->references('id')->on('notaventadetalle')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('at_claseprod_id')->nullable()->after('producto_id');
            $table->foreign('at_claseprod_id','fk_acuerdotecnico_claseprod')->references('id')->on('claseprod')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('at_grupoprod_id')->nullable()->after('at_claseprod_id');
            $table->foreign('at_grupoprod_id','fk_acuerdotecnico_grupoprod')->references('id')->on('grupoprod')->onDelete('restrict')->onUpdate('restrict');
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
            $table->dropForeign('fk_acuerdotecnico_notaventadetalle');
            $table->dropForeign('fk_acuerdotecnico_claseprod');
            $table->dropForeign('fk_acuerdotecnico_grupoprod');
            $table->dropColumn('at_notaventadetalle_id');
            $table->dropColumn('at_claseprod_id');
            $table->dropColumn('at_grupoprod_id');
        });
    }
}
