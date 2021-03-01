<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEstadisticaventa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estadisticaventa', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sucursal_id')->comment("Id de Sucursal");
            $table->foreign('sucursal_id','fk_estadisticaventa_sucursal')->references('id')->on('sucursal')->onDelete('restrict')->onUpdate('restrict');
            $table->integer('tipofact')->comment('Tipo de factura: 1=Factura eProPlas, 2=Guia externa.');
            $table->date('fechadocumento')->comment('Fecha Documento.');
            $table->string('tipodocumento',5)->comment('Tipo Documento');
            $table->string('numerodocumento',10)->comment('Número Documento');
            $table->integer('item')->comment('item dentro del detalle de la factura.')->nullable();
            $table->string('rut',12);
            $table->string('razonsocial',50)->comment('Razon Social');
            $table->string('producto',4)->comment('Codigo producto')->nullable();
            $table->string('descripcion',100)->comment('Descripcion Producto');
            $table->float('ancho',7,2)->comment('Ancho')->nullable();
            $table->float('largo',7,2)->comment('Largo')->nullable();
            $table->float('espesor',7,3)->comment('Espesor')->nullable();
            $table->string('espesorc',6)->comment('Espesor caracter')->nullable();
            $table->string('medidas',20)->comment('Medidas');
            $table->string('materiaprima',4)->comment('Codigo materia prima.')->nullable();
            $table->string('matprimdesc',20)->comment('Descripcion materia prima.')->nullable();
            $table->string('descr_prod_mp',20)->comment('Descripcion Producto materia prima.')->nullable();
            $table->float('unidades',12,2)->comment('Unidades o cantidad');
            $table->float('subtotal',12,2)->comment('Subtotal');
            $table->float('kilos',12,2)->comment('Subtotal');
            $table->unsignedBigInteger('unidadmedida_id');
            $table->foreign('unidadmedida_id','fk_estadisticaventa_unidadmedida')->references('id')->on('unidadmedida')->onDelete('restrict')->onUpdate('restrict');
            $table->float('factorconversion',12,2)->comment('Factor de conversion');
            $table->float('diferenciakilos',12,2)->comment('Diferencia Kilos');
            $table->float('conversionkilos',12,2)->comment('Conversion Kilos');
            $table->float('precioxkilo',12,2)->comment('Precio x kilo');
            $table->float('valorcosto',12,2)->comment('Valor Costo');
            $table->float('diferenciaprecio',12,2)->comment('Diferencia Precio');
            $table->float('diferenciaval',12,2)->comment('Diferencia Valorizada');
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
        Schema::dropIfExists('estadisticaventa');
    }
}
