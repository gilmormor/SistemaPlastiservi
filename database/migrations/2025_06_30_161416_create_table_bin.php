<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableBin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bin', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre',100)->comment('Nombre')->nullable();
            $table->string('desc',200)->comment('Descripcion')->nullable();
            $table->float('capkg',18,2)->comment('Capacidad Kg.')->nullable();
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id','fk_bin_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
            $table->softDeletes();
            $table->timestamps();
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
        Schema::dropIfExists('bin');
    }
}
