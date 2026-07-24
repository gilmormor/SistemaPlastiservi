<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMaquinaetapaprod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maquinaetapaprod', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('maquina_id');
            $table->foreign('maquina_id','fk_maquinaetapaprod_maquina')->references('id')->on('maquina')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('etapaprod_id');
            $table->foreign('etapaprod_id','fk_maquinaetapaprod_etapaprod')->references('id')->on('etapaprod')->onDelete('restrict')->onUpdate('restrict');
            $table->engine = 'InnoDB';
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maquinaetapaprod');
    }
}
