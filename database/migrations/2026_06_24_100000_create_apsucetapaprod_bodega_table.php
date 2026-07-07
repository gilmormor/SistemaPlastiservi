<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApsucetapaprodBodegaTable extends Migration
{
    public function up()
    {
        Schema::create('apsucetapaprod_bodega', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Etapa de producción en el área (areaproduccionsucetapaprod)
            $table->unsignedBigInteger('apsucetapaprod_id');
            // Bodega de inventario asignada a esta etapa
            $table->unsignedBigInteger('invbodega_id');
            $table->timestamps();

            // Una bodega no puede repetirse para la misma etapa de área
            $table->unique(['apsucetapaprod_id', 'invbodega_id'], 'uq_apsucbod_etapa_bodega');
        });
    }

    public function down()
    {
        Schema::dropIfExists('apsucetapaprod_bodega');
    }
}
