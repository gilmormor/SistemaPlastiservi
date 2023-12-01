<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCodigodet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('codigodet', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('codigo_id')->comment('Descripcion de Codigos para tablas varias');
            $table->foreign('codigo_id','fk_codigodet_codigo')->references('id')->on('codigo')->onDelete('restrict')->onUpdate('restrict');
            $table->string('descdet',100)->comment('Descripcion');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario creó el registro');
            $table->foreign('usuario_id','fk_codigodet_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('codigodet');
    }
}
