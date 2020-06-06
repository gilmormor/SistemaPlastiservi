<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModificarTableTipoentrega extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tipoentrega', function (Blueprint $table) {
            $table->string('abrev',5)->comment('Abreviatura nombre.')->after('nombre');
            $table->string('icono',30)->comment('Icono.')->after('abrev');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tipoentrega', function (Blueprint $table) {
            //
        });
    }
}
