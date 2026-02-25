<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGlosaProducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->string('glosa', 255)->comment('Nombre compuesto por otros datos del producto y acuerdo tecnico.')->nullable()->after('sku');
            $table->tinyInteger('glosaaut')->comment('0=No, 1=Si. = 0 no permite modificar la glosa del producto (Toma el valor generado de forma automatica), 1 permite modificar la glosa y tomar el valor ingresado por el usuario.')->default(1)->after('glosa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->dropColumn('glosa');
            $table->dropColumn('glosaaut');
        });
    }
}
