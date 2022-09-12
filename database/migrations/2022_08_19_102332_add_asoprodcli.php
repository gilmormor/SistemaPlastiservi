<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAsoprodcli extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categoriaprod', function (Blueprint $table) {
            $table->tinyInteger('asoprodcli')->comment('Asociar producto a cliente. Con este estatus solo se muestran al cliente los productos que tenga asociados en la tabla cliente_producto. Esto para mostrar solo los productos que el cliente tenga asociados.')->default(0)->after('mostunimed');
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
            $table->dropColumn('asoprodcli');
        });
    }
}
