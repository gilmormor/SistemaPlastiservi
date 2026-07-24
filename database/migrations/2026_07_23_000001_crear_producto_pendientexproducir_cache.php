<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrearProductoPendientexproducirCache extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Caché de "pendiente por producir" (stock - pendiente por despachar) por producto y sucursal.
        // Se recalcula por completo cada 5 minutos vía comando programado (producto:actualizar-cache-pendientexproducir),
        // ya que la consulta directa (usada antes en pendientePorProducirXProducto) tarda ~12 segundos por producto
        // debido a la materialización de la vista vista_sumorddespxnvdetid, lo que hacía lenta la carga de
        // cotización/nota de venta al ingresar varios productos.
        Schema::create('producto_pendientexproducir_cache', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('sucursal_id');
            $table->double('difcantpend', 15, 3)->default(0)->comment('stock - pendiente por despachar, informativo');
            $table->timestamps();
            $table->unique(['producto_id', 'sucursal_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('producto_pendientexproducir_cache');
    }
}
