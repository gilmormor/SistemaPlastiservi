<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableInvstock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invstock', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->float('stock',10,2)->comment('Stock');
            $table->float('stockmin',10,2)->comment('Stock minimo');
            $table->float('stockmax',10,2)->comment('Stock maximo');
            $table->string('stockubi',100)->comment('Ubicación del producto dentro de la bodega');
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id','fk_invstock_producto')->references('id')->on('producto')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('invbodega_id');
            $table->foreign('invbodega_id','fk_invstock_invbodega')->references('id')->on('invbodega')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('invstock');
    }
}
