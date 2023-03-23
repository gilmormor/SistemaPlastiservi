<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePesajedet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pesajedet', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pesaje_id');
            $table->foreign('pesaje_id','fk_pesajedet_pesaje')->references('id')->on('pesaje')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('invbodegaproducto_id');
            $table->foreign('invbodegaproducto_id','fk_pesajedet_invbodegaproducto')->references('id')->on('invbodegaproducto')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id','fk_pesajedet_producto')->references('id')->on('producto')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('invbodega_id');
            $table->foreign('invbodega_id','fk_pesajedet_invbodega')->references('id')->on('invbodega')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('sucursal_id');
            $table->foreign('sucursal_id','fk_pesajedet_sucursal')->references('id')->on('sucursal')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('unidadmedida_id');
            $table->foreign('unidadmedida_id','fk_pesajedet_unidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('invmovtipo_id');
            $table->foreign('invmovtipo_id','fk_pesajedet_invmovtipo')->references('id')->on('invmovtipo')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('turno_id');
            $table->foreign('turno_id','fk_pesajedet_turno')->references('id')->on('turno')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('pesajecarro_id');
            $table->foreign('pesajecarro_id','fk_pesajedet_pesajecarro')->references('id')->on('pesajecarro')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cant',18,2)->comment('Cantidad');
            $table->float('cantgrupo',10,2)->comment('Cantidad agrupada ejemplo: 1 paquete, 1 rollo, 1 caja');
            $table->float('cantxgrupo',10,2)->comment('Cantidad por Grupo');
            $table->float('pesounitnom',10,4)->comment('Peso unitario Producto Nominal segun tabla');
            $table->float('peso',10,4)->comment('Peso unitario Producto')->nullable();
            $table->float('cantkg',18,2)->comment('Cantidad en kg');
            $table->float('tara',10,2)->comment('Tara, Peso carro');
            $table->float('pesobaltotal',10,2)->comment('Peso en balanza total = pesocarro + pesoproducto');
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
        Schema::dropIfExists('pesajedet');
    }
}
