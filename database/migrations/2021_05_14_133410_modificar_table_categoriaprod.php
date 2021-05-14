<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModificarTableCategoriaprod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categoriaprod', function (Blueprint $table) {
            $table->integer('mostdatosad')->comment('Estatus para mostrar los datos adicionales para cada item de los productos. Ancho, Largo, Espesor y observaciones.')->after('unidadmedidafact_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categoriaprod', function (Blueprint $table) {
            //
        });
    }
}
