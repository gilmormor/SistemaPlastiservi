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
            $table->integer('sta_precioxkilo')->comment('Estatus precio por kilo. 0=El precio no es por kilo el precio es asignado directamente en la categoria o producto,1=el precio es por kilo, 2=el precio lo asigna el vendedor al momento de vender, hacer cotizacion o nota de venta.')->after('precio');
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
