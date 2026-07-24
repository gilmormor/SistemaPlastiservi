<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMaquina extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maquina', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sucursal_id')->comment("Id de Sucursal");
            $table->foreign('sucursal_id','fk_maquina_sucursal')->references('id')->on('sucursal')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('maquinagrupo_id')->comment("Id de Maquina Grupo");
            $table->foreign('maquinagrupo_id','fk_maquina_maquinagrupo')->references('id')->on('maquinagrupo')->onDelete('restrict')->onUpdate('restrict');
            $table->string('nombre',100)->comment('Nombre')->nullable();
            $table->string('desc',200)->comment('Descripcion')->nullable();
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id','fk_maquina_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('maquina');
    }
}
