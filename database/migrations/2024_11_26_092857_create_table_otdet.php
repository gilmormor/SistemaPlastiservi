<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOtdet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otdet', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ot_id');
            $table->foreign('ot_id','fk_otdet_ot')->references('id')->on('ot')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id','fk_otdet_producto')->references('id')->on('producto')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cant',10,2)->comment('Cantidad de producto');
            $table->float('cantprod',10,2)->comment('Cantidad de producto produccion');
            $table->unsignedBigInteger('unidadmedida_id');
            $table->foreign('unidadmedida_id','fk_otdet_unidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->double('espesorprod',4,3)->comment('Espesor para producción');
            $table->float('kg',18,2)->comment('Total Kg')->nullable();
            $table->float('kgprod',18,2)->comment('Total Kg a produccion')->nullable();
            $table->float('preciounit',18,2)->comment('Precio Unitario sin IVA');
            $table->float('precioxkilo',10,2)->comment('Precio por Kilo');
            $table->float('precioxkiloreal',10,2)->comment('Precio por Kilo real. Precio fijado en categoria.');
            $table->float('subtotal',18,2)->comment('SubTotal Precio neto (cant x preciounit) sin IVA');
            $table->boolean('requiere_fabricacion')->comment('Status para identificar si el producto requiere fabricacion previa, 1=producto se fabrica contra pedido 0=producto no se envia a proceso de fabricacion.')->default(0);
            $table->string('obs',100)->comment('Observacion')->nullable();
            $table->boolean('sta_envprog')->comment('Estatus enviado a programacion de produccion.')->default(0);
            $table->dateTime('envprogfecha')->comment('Fecha de envio a programacion');
            $table->unsignedBigInteger('envprogusu_id')->comment('Usuario que envio a programacion');
            $table->foreign('envprogusu_id','fk_otdet_envprogusu')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');

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
        Schema::dropIfExists('otdet');
    }
}
