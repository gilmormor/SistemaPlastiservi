<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableFacturadet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facturadet', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('factura_id');
            $table->foreign('factura_id','fk_facturadet_factura')->references('id')->on('factura')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('despachoorddet_id')->nullable();
            $table->foreign('despachoorddet_id','fk_facturadet_despachoorddet')->references('id')->on('despachoorddet')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('notaventadetalle_id')->nullable();
            $table->foreign('notaventadetalle_id','fk_facturadet_notaventadetalle')->references('id')->on('notaventadetalle')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('producto_id')->nullable();
            $table->foreign('producto_id','fk_facturadet_producto')->references('id')->on('producto')->onDelete('restrict')->onUpdate('restrict');
            $table->tinyInteger('nrolindet')->comment('N° de Línea o N° Secuencia (desde 1 a 60)');
            $table->string('vlrcodigo',45)->comment('Código del Ítem.')->nullable();
            $table->string('nmbitem',60)->comment('Nombre del producto o servicio.')->nullable();
            $table->string('dscitem',200)->comment('Descripción Adicional del producto o servicio. Se utiliza para pack, servicios con detalle.')->nullable();
            $table->double('qtyitem',18,2)->comment('Cantidad del ítem en 12 enteros y 6 decimales Obligatorio para facturas de venta, compra o notas que indican emisor opera como Agente Retenedor.');
            $table->string('unmditem',4)->comment('Unidad de Medida Obligatorio para facturas de venta, compra o notas que indican emisor opera como Agente Retenedor Obligatorio en Guías de Despacho con Indicadorde tipo de Traslado de Bienes = 8 y 9.');
            $table->unsignedBigInteger('unidadmedida_id')->comment('Codigo unidad de medida.');
            $table->foreign('unidadmedida_id','fk_facturadet_unidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->double('prcitem',18)->comment('PrcItemPrecio Unitario del Ítem.');
            $table->double('montoitem',18)->comment('Monto Total Linea (Precio Unitario * Cantidad ) Monto Descuento + Monto Recargo.');
            $table->string('obsdet',200)->comment('Observaciones detalle')->nullable();
            $table->double('itemkg',10)->comment('Total Kilogramos por item.')->nullable();
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
        Schema::dropIfExists('facturadet');
    }
}
