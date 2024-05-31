<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableClientedesbloqueadomodulodel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientedesbloqueadomodulodel', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('clientedesbloqueado_id');
            $table->foreign('clientedesbloqueado_id','fk_clientedesbloqueadomodulodel_clientedesbloqueado')->references('id')->on('clientedesbloqueado')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('modulo_id');
            $table->foreign('modulo_id','fk_clientedesbloqueadomodulodel_modulo')->references('id')->on('modulo')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('clientedesbloqueadomodulo_id');
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id','fk_clientedesbloqueadomodulodel_cliente')->references('id')->on('cliente')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('notaventa_id')->nullable();
            $table->foreign('notaventa_id','fk_clientedesbloqueadomodulodel_notaventa')->references('id')->on('notaventa')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id','fk_clientedesbloqueadomodulodel_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('clientedesbloqueadomodulodel');
    }
}
