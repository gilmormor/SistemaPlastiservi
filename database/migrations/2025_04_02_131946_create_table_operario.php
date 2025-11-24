<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOperario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operario', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre',100)->comment('Nombre Operario')->nullable();
            $table->string('desc',200)->comment('Descripcion')->nullable();
            $table->tinyInteger('activo')->default(1)->comment('Estatus activo o inactivo.');
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id','fk_operario_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('operario');
    }
}
