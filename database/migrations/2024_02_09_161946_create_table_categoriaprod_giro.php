<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCategoriaprodGiro extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categoriaprod_giro', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('categoriaprod_id');
            $table->foreign('categoriaprod_id','fk_categoriaprod_giro_categoriaprod')->references('id')->on('categoriaprod')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('giro_id');
            $table->foreign('giro_id','fk_categoriaprod_giro_giro')->references('id')->on('giro')->onDelete('restrict')->onUpdate('restrict');
            $table->float('preciokg',10,2)->comment('Precio por kilo');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario creó el registro');
            $table->foreign('usuario_id','fk_categoriaprod_giro_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino el registro')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('categoriaprod_giro');
    }
}
