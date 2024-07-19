<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableClientedesbloqueado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientedesbloqueado', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('obs',100)->comment('Observacion desbloqueo Cliente.');
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id','fk_clientedesbloqueado_cliente')->references('id')->on('cliente')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario creó el registro');
            $table->foreign('usuario_id','fk_clientedesbloqueado_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('clientedesbloqueado');
    }
}
