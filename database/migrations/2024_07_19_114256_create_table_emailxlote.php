<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEmailxlote extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emailxlote', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('nombre',50)->comment('Nombre');
            $table->string('desc',150)->comment('Descripcion');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario quien creo el registro');
            $table->foreign('usuario_id','fk_emailxlote_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('emailxlote');
    }
}
