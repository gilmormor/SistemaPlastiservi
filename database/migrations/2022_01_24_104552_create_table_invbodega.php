<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableInvbodega extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invbodega', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('nombre',100)->comment('Nombre');
            $table->string('nomabre',5)->comment('Nombre Abreviado');
            $table->string('desc',300)->comment('Descripcion');
            $table->unsignedBigInteger('sucursal_id');
            $table->foreign('sucursal_id','fk_invbodega_sucursal')->references('id')->on('sucursal')->onDelete('restrict')->onUpdate('restrict');
            $table->tinyInteger('activo')->default(1)->comment('Estatus para saber si la bodega esta activa. Esto es para en algun momento inactivar y no mostrarla al momento de hacer movimientos.');
            $table->tinyInteger('tipo')->comment('Tipo de bodega. 1=Bodega antes de la orden despacho es decir solo para movimiento interno antes del despacho (Ingresos y egresos del inventario), 2=Bodega de despacho es decir solo es tocado por la guia de despacho');
            $table->tinyInteger('orden')->comment('Orden');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario quien creo el registro');
            $table->foreign('usuario_id','fk_invbodega_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('invbodega');
    }
}
