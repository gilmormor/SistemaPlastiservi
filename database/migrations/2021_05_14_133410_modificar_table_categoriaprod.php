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
            $table->integer('mostdatosad')->comment('Estatus para mostrar o no datos adicionales en la pantalla seleccionas productos calcprecioprodsn. Ancho, Largo, Espesor y observaciones. Para el caso de productos Santa Ester. Esto es al momento de agregar productos a cotizaciones, nota de venta, facturas etc.')->after('unidadmedidafact_id');
            $table->integer('mostunimed')->comment('Estatus para mostrar y editar unidad de medida en la pantalla seleccionar productos calcprecioprodsn. Ancho, Largo, Espesor y observaciones. Esto es al momento de agregar productos a cotizaciones, nota de venta, facturas etc.')->after('mostdatosad');
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
