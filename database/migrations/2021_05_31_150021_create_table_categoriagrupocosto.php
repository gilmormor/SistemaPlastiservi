<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCategoriagrupocosto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categoriagrupocosto', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('grupoprod_id');
            $table->foreign('grupoprod_id','fk_categoriagrupocosto_grupoprod')->references('id')->on('grupoprod')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('unidadmedida_id')->comment('Unidad de medida para calcular el costo.');
            $table->foreign('unidadmedida_id','fk_categoriagrupocosto_unidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->char('annomes', 6)->comment('Año y mes a que corresponde el costo.');
            $table->double('costo',18,2)->comment('Costo');
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
        Schema::dropIfExists('categoriagrupocosto');
    }
}
