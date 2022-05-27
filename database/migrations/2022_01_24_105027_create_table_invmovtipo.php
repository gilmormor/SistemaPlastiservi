<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableInvmovtipo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invmovtipo', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('nombre',100)->comment('Nombre');
            $table->string('desc',300)->comment('Descripcion');
            $table->tinyInteger('tipomov')->comment('Tipo de movimiento 1=Entrada, -1=Salida, la cantidad en movimiento se multiplica por este valor.');
            $table->tinyInteger('stacieinimes')->comment('Estatus para controlar si a traves de este tipo de movimiento se crea el registro en  tabla de control inventario (invcontrol).');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario quien creo el registro');
            $table->foreign('usuario_id','fk_invmovtipo_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('invmovtipo');
    }
}
