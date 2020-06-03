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
            $table->unsignedBigInteger('unidadmedidafact_id')->comment("Unidad de Medida para Cotizacion, Nota de Venta y Facturas")->nullable()->after('unidadmedida_id');
            $table->foreign('unidadmedidafact_id','fk_categoriaprod_unidadmedidafact')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
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
            /*
            Schema::disableForeignKeyConstraints();
            $table->dropForeign(['fk_categoriaprod_unidadmedidafact']);
            $table->dropColumn('unidadmedidafact_id');
            */
        });
    }
}
