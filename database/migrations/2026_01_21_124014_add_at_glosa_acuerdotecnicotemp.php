<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAtGlosaAcuerdotecnicotemp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acuerdotecnicotemp', function (Blueprint $table) {
            $table->string('at_glosa', 255)->comment('Nombre compuesto por otros datos del producto y acuerdo tecnico temp.')->nullable()->after('at_grupoprod_id');
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
            $table->dropColumn('at_glosa');
        });
    }
}
