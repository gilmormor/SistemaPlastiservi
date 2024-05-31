<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableClientedesbloqueadomodulo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientedesbloqueadomodulo', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('clientedesbloqueado_id');
            $table->foreign('clientedesbloqueado_id','fk_clientedesbloqueadomodulo_clientedesbloqueado')->references('id')->on('clientedesbloqueado')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('modulo_id');
            $table->foreign('modulo_id','fk_clientedesbloqueadomodulo_modulo')->references('id')->on('modulo')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('clientedesbloqueadomodulo');
    }
}
