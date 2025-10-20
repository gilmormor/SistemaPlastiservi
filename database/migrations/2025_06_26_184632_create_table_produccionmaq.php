<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProduccionmaq extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produccionmaq', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('produccion_id');
            $table->foreign('produccion_id','fk_produccionmaq_produccion')->references('id')->on('produccion')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('maquina_id');
            $table->foreign('maquina_id','fk_produccionmaq_maquina')->references('id')->on('maquina')->onDelete('restrict')->onUpdate('restrict');
            $table->engine = 'InnoDB';
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
        Schema::dropIfExists('produccionmaq');
    }
}
