<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOpdetmaquina extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opdetmaquina', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opdet_id');
            $table->foreign('opdet_id','fk_opdetmaquina_opdet')->references('id')->on('opdet')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('maquina_id');
            $table->foreign('maquina_id','fk_opdetmaquina_maquina')->references('id')->on('maquina')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('opdetmaquina');
    }
}
