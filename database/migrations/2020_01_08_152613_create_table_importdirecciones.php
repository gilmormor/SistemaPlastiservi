<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableImportdirecciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('importdirecciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('rut',30);
            $table->string('direccion',256);
            $table->string('comuna_id',5);
            $table->string('formadepago_id',20);
            $table->string('formadepago',20);
            $table->string('plazodepago_id',20);
            $table->string('plazodepago',20);
            $table->string('nombrecontacto',50);
            $table->string('emailcontacto',90);
            $table->string('telefonocontacto',90);
            $table->string('nombrecontactofinanzas',90);
            $table->string('emailContactofinanzas',90);
            $table->string('telefonocontactofinanzas',90);
            $table->string('nombrefantasia',90);
            $table->string('mostrarguiasfacturas',3);
            $table->string('observaciones',20);
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
            $table->engine = 'InnoDB';
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
        Schema::dropIfExists('importdirecciones');
    }
}
