<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModificarTableNoconformidad extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('noconformidad', function (Blueprint $table) {
            $table->dateTime('accioninmediatafec')->comment('Fecha Accion inmediata.')->after('accioninmediata')->nullable();
            $table->dateTime('analisisdecausafec')->comment('Fecha Análisis de causa.')->after('analisisdecausa')->nullable();
            $table->dateTime('accorrecfec')->comment('Fecha Acción correctiva.')->after('accorrec')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('noconformidad', function (Blueprint $table) {
            //
        });
    }
}
