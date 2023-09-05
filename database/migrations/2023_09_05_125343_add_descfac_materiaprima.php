<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescfacMateriaprima extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('materiaprima', function (Blueprint $table) {
            $table->string('descfact',20)->nullable()->comment('Descripcion que se va a imprimir en factura.')->after('desc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('materiaprima', function (Blueprint $table) {
            $table->dropColumn('descfact');
        });
    }
}
