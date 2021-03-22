<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBitacora extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bitacora', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->comment('Codigo de Empresa');
            $table->foreign('empresa_id','fk_bitacora_empresa')->references('id')->on('empresa')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('menu_id')->comment('Codigo de Menu')->nullable();
            $table->foreign('menu_id','fk_bitacora_menu')->references('id')->on('menu')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuario_id')->comment('Codigo de Empresa');
            $table->foreign('usuario_id','fk_bitacora_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->string('codmov',3)->comment('Codigo de Movimiento Ejm: IS=Inicio de Sesion, FS=Finde Sesion.');
            $table->string('desc',250)->comment('Descripcion de movimiento.');
            $table->string('ip',16)->comment('Dirección IP');
            $table->string('dispositivo',16)->comment('Dispositivo')->nullable();
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
        Schema::dropIfExists('bitacora');
    }
}
