<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLogcambio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logcambio', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('tabla',50)->comment('Nombre tabla afectada');
            $table->unsignedBigInteger('tabla_id')->comment('Id de registro afectado');
            $table->string('operacion',2)->comment('Tipo de operacion: creación,modificación,eliminación'); // 'creación', 'modificación', 'eliminación'
            $table->json('cambios')->comment('Cambios en formato json'); // cambios realizados
            $table->string('ip',16)->comment('Direccion IP')->nullable();
            $table->unsignedBigInteger('usuario_id')->comment('Usuario que hizo el cambio');
            $table->foreign('usuario_id','fk_logcambio_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('logcambio');
    }
}
