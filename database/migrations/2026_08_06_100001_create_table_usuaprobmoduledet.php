<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsuaprobmoduledet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuaprobmoduledet', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('usuaprobmodulo_id');
            $table->foreign('usuaprobmodulo_id','fk_usuaprobmoduledet_usuaprobmodulo')->references('id')->on('usuaprobmodulo')->onDelete('cascade')->onUpdate('restrict');
            $table->unsignedBigInteger('usuario_creador_id')->comment('Usuario cuyos registros puede aprobar el usuario restringido');
            $table->foreign('usuario_creador_id','fk_usuaprobmoduledet_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('usuaprobmoduledet');
    }
}
