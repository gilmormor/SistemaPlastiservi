<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDespachosoldev extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('despachosoldev', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('despachosol_id');
            $table->foreign('despachosol_id','fk_despachosoldev_despachosol')->references('id')->on('despachosol')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuario_id')->comment('Id Usuario');
            $table->foreign('usuario_id','fk_despchosoldev_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->string('obs',250)->comment('Observacion.');
            $table->integer('status')->comment('Estatus de devolucion. 1=Devolucion Total, 2=Devolucion Parcial. Si es total, cambio el estatus de aprobacion, Si es parcial le asigno al campo cantdev el valor del item que no ha sido despachado.');
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
        Schema::dropIfExists('despachosoldev');
    }
}
