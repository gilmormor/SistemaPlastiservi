<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCcparamTable extends Migration
{
    public function up()
    {
        Schema::create('ccparam', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('nombre', 50)->comment('Nombre interno del parámetro, ej: espesor');
            $table->string('etiqueta', 100)->comment('Etiqueta visible al usuario, ej: Espesor (µm)');
            $table->enum('tipo', ['number', 'text', 'boolean'])->default('number');
            $table->string('unidad', 20)->nullable()->comment('Unidad de medida: µm, mm, kg/cm², etc.');
            $table->tinyInteger('decimales')->default(2)->comment('Decimales para tipo number');
            $table->tinyInteger('orden')->default(0);
            $table->unsignedBigInteger('usuariodel_id')->nullable()->comment('ID Usuario que eliminó el registro');
            $table->timestamps();
            $table->softDeletes();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
        });
    }

    public function down()
    {
        Schema::dropIfExists('ccparam');
    }
}
