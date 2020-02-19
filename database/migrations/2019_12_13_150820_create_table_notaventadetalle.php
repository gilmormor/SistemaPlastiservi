<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNotaventadetalle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notaventadetalle', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id','fk_notaventadetalle_producto')->references('id')->on('producto')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('notaventa_id');
            $table->foreign('notaventa_id','fk_notaventadetalle_notaventa')->references('id')->on('notaventa')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('cotizaciondetalle_id')->nullable()->comment("ID Cotización Detalle");
            $table->foreign('cotizaciondetalle_id','fk_notaventadetalle_cotizaciondetalle')->references('id')->on('cotizaciondetalle')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cant',10,2)->comment('Cantidad de producto');
            $table->unsignedBigInteger('unidadmedida_id');
            $table->foreign('unidadmedida_id','fk_notaventadetalle_unidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->float('descuento',5,2)->comment('Porcentaje Descuento por renglon.');
            $table->float('preciounit',10,2)->comment('Precio Unitario sin IVA');
            $table->float('precioxkilo',10,2)->comment('Precio por Kilo');
            $table->float('precioxkiloreal',10,2)->comment('Precio por Kilo real. Precio fijado en categoria.');
            $table->float('totalkilos',10,2)->comment('Total Kilos');
            $table->float('subtotal',10,2)->comment('SubTotal Precio neto (cant x preciounit) sin IVA');
            $table->engine = 'InnoDB';
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
        Schema::dropIfExists('notaventadetalle');
    }
}
