<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStadespsinstockCategoriaprod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categoriaprod', function (Blueprint $table) {
            $table->boolean('stadespsinstock')->default(0)->comment('0=Debe existir Stock mayor a cero para despachar, 1=Se puede despachar con stock menor o igual a cero. Estatus para despachar sin stock, es decir que permita que el inv quede en negativo. Si es = 1 permitira despachar con el stock <= a 0 cero.')->after('categoriaprodgrupo_id');
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
            $table->dropColumn('stadespsinstock');
        });
    }
}
