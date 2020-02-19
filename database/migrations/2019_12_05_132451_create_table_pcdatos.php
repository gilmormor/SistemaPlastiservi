<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePcdatos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pcdatos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ipv4',10)->comment('IPV4 de computador');
            $table->string('nombreso',150)->comment('Nombtre del Sistema Operativo');
            $table->string('procesador',150)->comment('Procesador');
            $table->string('nombreequipo',150)->comment('Nombre de Equipo');
            $table->string('nombreusuario',150)->comment('Nombre de Usuario');
            $table->string('grupotrabajo',150)->comment('Grupo de trabajo');
            $table->string('discocap',150)->comment('Capacidad Disco Duro');
            $table->engine = 'InnoDB';
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
        Schema::dropIfExists('pcdatos');
    }
}
