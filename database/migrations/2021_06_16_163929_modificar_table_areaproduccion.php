<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModificarTableAreaproduccion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('areaproduccion', function (Blueprint $table) {
            $table->integer('stapromkg')->comment('Estatus para mostrar o no las columnas de promedio por kilo en consuta y reportes. 0 no muestra columnas, 1 si muestra')->nullable()->after('descripcion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('areaproduccion', function (Blueprint $table) {
            //
        });
    }
}
