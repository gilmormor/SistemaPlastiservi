<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModificarTablaCategoriaprod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categoriaprod', function (Blueprint $table) {
            $table->integer('sta_precioxkilo')->comment('Estatus precio por kilo. 0=El precio no es por kilo el precio es asignado directamente en la categoria o producto,1=el precio es por kilo, 2=el precio lo asigna el vendedor al momento de vender, hacer cotizacion o nota de venta.')->after('areaproduccion_id');
            $table->unsignedBigInteger('unidadmedida_id')->comment("Unidad de Medida")->nullable()->after('sta_precioxkilo');
            $table->foreign('unidadmedida_id','fk_categoriaprod_unidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');

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
