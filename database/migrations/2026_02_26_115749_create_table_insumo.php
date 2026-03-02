<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableInsumo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insumo', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('nombre')->comment('Nombre');
            $table->string('desc')->comment('Descripcion');
            $table->unsignedBigInteger('tipocosto_id');
            $table->foreign('tipocosto_id','fk_insumo_tipocosto')->references('id')->on('tipocosto')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('unidadmedida_id');
            $table->foreign('unidadmedida_id','fk_insumo_unidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->decimal('costounitario',12,4)->comment('Costo unitario del insumo');
            $table->tinyInteger('activo')->default(1)->comment('Estatus activo o inactivo.');
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id','fk_insumo_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('insumo');
    }
}
