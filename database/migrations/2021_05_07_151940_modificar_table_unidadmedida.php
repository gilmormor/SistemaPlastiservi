<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModificarTableUnidadmedida extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unidadmedida', function (Blueprint $table) {
            $table->boolean('mostrarfact')->comment('Estatus para mostrar en cotizacion, notas de venta, facturacion.')->default(0)->nullable()->after('descripcion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unidadmedida', function (Blueprint $table) {
            //
        });
    }
}
