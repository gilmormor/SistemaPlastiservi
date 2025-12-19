<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOmitiracutecTableCategoriaprod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categoriaprod', function (Blueprint $table) {
            $table->tinyInteger('omitiracutec')->comment('Lo uso para tener un estatus para omitir acuerdo tecnico, inicialmente para omitir el acuerdo tecnico en Cotizacion y Nota de venta buscarUnProducto() en la pantalla de calcular precio y total Kg 1=Si, 0=No. o en algunas consultas o reportes, .')->default(0)->after('stadespsinstock');
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
            $table->dropColumn('omitiracutec');
        });
    }
}
