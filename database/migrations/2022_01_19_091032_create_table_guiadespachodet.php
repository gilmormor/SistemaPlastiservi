<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableGuiadespachodet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guiadespachodet', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('guiadespacho_id');
            $table->foreign('guiadespacho_id','fk_guiadespachodet_guiadespacho')->references('id')->on('guiadespacho')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('despachoorddet_id');
            $table->foreign('despachoorddet_id','fk_guiadespachodet_despachoorddet')->references('id')->on('despachoorddet')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('notaventadetalle_id');
            $table->foreign('notaventadetalle_id','fk_guiadespachodet_notaventadetalle')->references('id')->on('notaventadetalle')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id','fk_guiadespachodet_producto')->references('id')->on('producto')->onDelete('restrict')->onUpdate('restrict');
            $table->string('desc1',100)->comment('Descripcion 1');
            $table->string('desc2',100)->comment('Descripcion 2');
            $table->float('cant',10,2)->comment('Cantidad despacho.')->nullable();
            $table->float('cantdesp',10,2)->comment('Cantidad despacho.')->nullable();
            $table->string('unimed',10)->comment('Unidad de medida.');
            $table->float('preciounit',18,2)->comment('Precio Unitario sin IVA');
            $table->string('adicional',10)->comment('Adicional.');
            $table->float('descuento',5,2)->comment('Porcentaje Descuento por renglon.');
            $table->float('subtotal',18,2)->comment('SubTotal Precio neto (cant x preciounit) sin IVA');
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
        Schema::dropIfExists('guiadespachodet');
    }
}
