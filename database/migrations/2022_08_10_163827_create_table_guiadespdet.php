<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableGuiadespdet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guiadespdet', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('guiadesp_id');
            $table->foreign('guiadesp_id','fk_guiadespdet_guiadesp')->references('id')->on('guiadesp')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('despachoorddet_id');
            $table->foreign('despachoorddet_id','fk_guiadespdet_despachoorddet')->references('id')->on('despachoorddet')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('notaventadetalle_id');
            $table->foreign('notaventadetalle_id','fk_guiadespdet_notaventadetalle')->references('id')->on('notaventadetalle')->onDelete('restrict')->onUpdate('restrict');

            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id','fk_guiadespdet_producto')->references('id')->on('producto')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('notaventa_id');
            $table->foreign('notaventa_id','fk_guiadespdet_notaventa')->references('id')->on('notaventa')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('cotizaciondetalle_id')->nullable()->comment("ID Cotización Detalle");
            $table->foreign('cotizaciondetalle_id','fk_guiadespdet_cotizaciondetalle')->references('id')->on('cotizaciondetalle')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cant',10,2)->comment('Cantidad de producto');
            $table->float('cantgrupo',10,2)->comment('Cantidad agrupada ejemplo: 1 paquete, 1 rollo, 1 caja');
            $table->float('cantxgrupo',10,2)->comment('Cantidad por Grupo');
            $table->string('desc1',100)->comment('Descripcion 1');
            $table->string('desc2',100)->comment('Descripcion 2');
            $table->unsignedBigInteger('unidadmedida_id');
            $table->foreign('unidadmedida_id','fk_guiadespdet_unidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->string('unimed',10)->comment('Unidad de medida.');
            $table->string('adicional',10)->comment('Adicional.');
            $table->float('descuento',5,2)->comment('Porcentaje Descuento por renglon.');
            $table->float('preciounit',18,2)->comment('Precio Unitario sin IVA');
            $table->float('peso',8,3)->comment('Peso Producto')->nullable();
            $table->float('precioxkilo',10,2)->comment('Precio por Kilo');
            $table->float('precioxkiloreal',10,2)->comment('Precio por Kilo real. Precio fijado en categoria.');
            $table->float('totalkilos',10,2)->comment('Total Kilos');
            $table->float('subtotal',18,2)->comment('SubTotal Precio neto (cant x preciounit) sin IVA');
            $table->string('producto_nombre',100)->comment('Nombre producto.')->nullable();
            $table->float('ancho',10,2)->comment('Ancho')->nullable();
            $table->float('largo',10,2)->comment('Largo')->nullable();
            $table->float('espesor',10,4)->comment('espesor')->nullable();
            $table->string('diametro',25)->comment('Diametro')->nullable();
            $table->unsignedBigInteger('categoriaprod_id')->nullable();
            $table->foreign('categoriaprod_id','fk_guiadespdet_categoriaprod')->references('id')->on('categoriaprod')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('claseprod_id')->nullable();
            $table->foreign('claseprod_id','fk_guiadespdet_claseprod')->references('id')->on('claseprod')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('grupoprod_id')->nullable();
            $table->foreign('grupoprod_id','fk_guiadespdet_grupoprod')->references('id')->on('grupoprod')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('color_id')->comment("Color")->nullable();
            $table->foreign('color_id','fk_guiadespdet_color')->references('id')->on('color')->onDelete('restrict')->onUpdate('restrict');
            $table->string('obs',200)->comment('Observaciones')->nullable();
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
        Schema::dropIfExists('guiadespdet');
    }
}
