<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableFactura extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factura', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('fechahora')->comment('Fecha y hora de Nota de venta');
            $table->unsignedBigInteger('nrodocto')->comment('Numero de Documento Folio')->nullable();
            $table->dateTime('fchemis')->comment('Fecha de emisión contable del docto (AAAA-MM-DD) Fecha válida entre 2003-04-01 y 2050-12-31')->nullable();
            $table->string('termpagoglosa',100)->comment('Términos del Pago glosa Glosa que describe las condiciones del pago del documento, codificado en el campo: Términos del pago-Código')->nullable();
            $table->dateTime('fchvenc')->comment('Fecha vencimiento')->nullable();
            $table->unsignedBigInteger('sucursal_id')->comment("Id de Sucursal");
            $table->foreign('sucursal_id','fk_factura_sucursal')->references('id')->on('sucursal')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id','fk_factura_cliente')->references('id')->on('cliente')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('comuna_id');
            $table->foreign('comuna_id','fk_factura_comuna')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('vendedor_id');
            $table->foreign('vendedor_id','fk_factura_vendedor')->references('id')->on('vendedor')->onDelete('restrict')->onUpdate('restrict');
            $table->string('obs',200)->comment('Observaciones')->nullable();
            $table->string('tipodespacho',1)->comment('1: Despacho por cuenta del receptor del documento (cliente o vendedor en caso de Facturas de compra.) 2: Despacho por cuenta del emisor a instalaciones del cliente 3: Despacho por cuenta del emisor a otras instalaciones (Ejemplo: entrega en Obra)')->nullable();
            $table->string('indtraslado',1)->comment('Indicador Tipo de traslado de bienes 1: Operación constituye venta1 2: Ventas por efectuar 3: Consignaciones 4: Entrega gratuita 5: Traslados internos 6: Otros traslados no venta 7: Guía de devolución 8: Traslado para exportación. (no venta) 9: Venta para exportación')->nullable();
            $table->double('mntneto',18)->comment('Monto neto Suma de valores total de ítems afectos -descuentos globales + recargos globales (Asignados a ítems afectos). Si está encendido el Indicador de Montos Brutos (=1) entonces el resultado anterior se debe dividir por (1 + tasa de IVA)');
            $table->double('tasaiva',6,2)->comment('Tasa IVA En Porcentaje (Ej: 19.5)');
            $table->double('iva',18)->comment('Monto Iva Valor num.= a Monto neto * tasa IVA Mayor o igual a 0 excepto en Liquidaciones-Factura, en que puede tomar valor negativo')->nullable();
            $table->double('mnttotal',18)->comment('Monto Total')->nullable();
            $table->double('kgtotal',18)->comment('Total Kilogramos.')->nullable();
            $table->unsignedBigInteger('tipoentrega_id');
            $table->foreign('tipoentrega_id','fk_factura_tipoentrega')->references('id')->on('tipoentrega')->onDelete('restrict')->onUpdate('restrict');
            $table->string('lugarentrega',100)->comment('Lugar de entrega');
            $table->unsignedBigInteger('comunaentrega_id')->comment('Comuna de entrega');
            $table->foreign('comunaentrega_id','fk_factura_comunaentrega')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict');

            $table->unsignedBigInteger('centroeconomico_id');
            $table->foreign('centroeconomico_id','fk_factura_centroeconomico')->references('id')->on('centroeconomico')->onDelete('restrict')->onUpdate('restrict');

            $table->boolean('statusgen')->comment('Status Factura generada (null o 0)=No Generada, 1=Generada')->nullable();
            $table->boolean('aprobstatus')->comment('Status de aprobacion (null o 0)=Sin aprobar, 1=Aprobada')->nullable();
            $table->unsignedBigInteger('aprobusu_id')->comment('Usuario quien aprobo la Guis Despacho, este es el estatus de aprovacion de cotizacion')->nullable();
            $table->foreign('aprobusu_id','fk_factura_aprobusu')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->dateTime('aprobfechahora')->comment('fecha y hora cuando fue aprobada la cotización.')->nullable();
            $table->unsignedBigInteger('usuario_id')->comment('Usuario quien creo el registro');
            $table->foreign('usuario_id','fk_factura_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('factura');
    }
}
