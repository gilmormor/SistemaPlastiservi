<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProduccionregmaq extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produccionregmaq', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('produccionreg_id');
            $table->foreign('produccionreg_id','fk_produccionregmaq_produccion')->references('id')->on('produccionreg')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('maquina_id');
            $table->foreign('maquina_id','fk_produccionregmaq_maquina')->references('id')->on('maquina')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('produccionregmaq');
    }
}
