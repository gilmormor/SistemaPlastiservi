<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAtributo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atributo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre',100)->comment('Nombre')->nullable();
            $table->string('desc',200)->comment('Descripcion')->nullable();
            $table->tinyInteger('tipodato')->comment('Tipo de dato Ejemplo: 1=String,2=Numerico')->nullable();
            $table->string('longitud',10)->comment('Longitud Ejemplo: 10 caracteres. 10,2 (8 enteros 1 con 2 decimales)')->nullable();
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id','fk_atributo_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
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
        Schema::dropIfExists('atributo');
    }
}
