<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableGuiadesp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guiadesp', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('nrodocto')->comment('Numero de Documento Folio')->nullable();
            $table->dateTime('fchemisgen')->comment('Fecha hora cuando fue generada la Guia de despacho.')->nullable();
            $table->date('fchemis')->comment('Fecha de emisión contable del docto (AAAA-MM-DD) Fecha válida entre 2003-04-01 y 2050-12-31.');
            $table->dateTime('fechahora')->comment('Fecha y hora de Nota de venta');
            $table->unsignedBigInteger('despachoord_id')->nullable();
            $table->foreign('despachoord_id','fk_guiadesp_despachoord')->references('id')->on('despachoord')->onDelete('restrict')->onUpdate('restrict')->nullable();
            $table->unsignedBigInteger('notaventa_id')->nullable();
            $table->foreign('notaventa_id','fk_guiadesp_notaventa')->references('id')->on('notaventa')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('sucursal_id')->comment("Id de Sucursal");
            $table->foreign('sucursal_id','fk_guiadesp_sucursal')->references('id')->on('sucursal')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id','fk_guiadesp_cliente')->references('id')->on('cliente')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('comuna_id');
            $table->foreign('comuna_id','fk_guiadesp_comuna')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('vendedor_id');
            $table->foreign('vendedor_id','fk_guiadesp_vendedor')->references('id')->on('vendedor')->onDelete('restrict')->onUpdate('restrict');
            $table->string('obs',200)->comment('Observaciones')->nullable();
            $table->string('ot',5)->comment('Orden de trabajo')->nullable();
            $table->string('oc_id',18)->comment('Numero de Orden de Compra')->nullable();
            $table->string('oc_file',100)->comment('Archivo o imagen de Orden de Compra')->nullable();
            $table->string('tipodespacho',1)->comment('1: Despacho por cuenta del receptor del documento (cliente o vendedor en caso de Facturas de compra.) 2: Despacho por cuenta del emisor a instalaciones del cliente 3: Despacho por cuenta del emisor a otras instalaciones (Ejemplo: entrega en Obra)')->nullable();
            $table->string('indtraslado',1)->comment('Indicador Tipo de traslado de bienes 1: Operación constituye venta1 2: Ventas por efectuar 3: Consignaciones 4: Entrega gratuita 5: Traslados internos 6: Otros traslados no venta 7: Guía de devolución 8: Traslado para exportación. (no venta) 9: Venta para exportación')->nullable();
            $table->double('mntneto',18)->comment('Monto neto Suma de valores total de ítems afectos -descuentos globales + recargos globales (Asignados a ítems afectos). Si está encendido el Indicador de Montos Brutos (=1) entonces el resultado anterior se debe dividir por (1 + tasa de IVA)');
            $table->double('tasaiva',6,2)->comment('Tasa IVA En Porcentaje (Ej: 19.5)');
            $table->double('iva',18)->comment('Monto Iva Valor num.= a Monto neto * tasa IVA Mayor o igual a 0 excepto en Liquidaciones-Factura, en que puede tomar valor negativo')->nullable();
            $table->double('mnttotal',18)->comment('Monto Total')->nullable();
            $table->double('kgtotal',18)->comment('Total Kilogramos.')->nullable();
            $table->unsignedBigInteger('tipoentrega_id');
            $table->foreign('tipoentrega_id','fk_guiadesp_tipoentrega')->references('id')->on('tipoentrega')->onDelete('restrict')->onUpdate('restrict');
            $table->string('lugarentrega',100)->comment('Lugar de entrega');
            $table->unsignedBigInteger('comunaentrega_id')->comment('Comuna de entrega');
            $table->foreign('comunaentrega_id','fk_guiadesp_comunaentrega')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict');

            $table->unsignedBigInteger('centroeconomico_id');
            $table->foreign('centroeconomico_id','fk_guiadesp_centroeconomico')->references('id')->on('centroeconomico')->onDelete('restrict')->onUpdate('restrict');

            $table->boolean('statusgen')->comment('Status Guia Despacho generada (null o 0)=No Generada, 1=Generada')->nullable();
            $table->boolean('aprobstatus')->comment('Status de aprobacion (null o 0)=Sin aprobar, 1=Aprobada')->nullable();
            $table->unsignedBigInteger('aprobusu_id')->comment('Usuario quien aprobo la Guis Despacho, este es el estatus de aprovacion de cotizacion')->nullable();
            $table->foreign('aprobusu_id','fk_guiadesp_aprobusu')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->dateTime('aprobfechahora')->comment('fecha y hora cuando fue aprobada la cotización.')->nullable();
            $table->unsignedBigInteger('usuario_id')->comment('Usuario quien creo el registro');
            $table->foreign('usuario_id','fk_guiadesp_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('guiadesp');
    }
}


            /*
            $table->string('rut',12)->comment('RUT Cliente');
            $table->string('razonsocial',70)->comment('Nombre cliente');
            $table->string('giro',100)->comment('Giro');
            $table->string('clidir',200)->comment('Direccion Cliente.');
            $table->string('comuna',100)->comment('Comuna.');
            $table->string('ciudad',100)->comment('Ciudad.');
            $table->string('email',50)->comment('Correo electronico');
            $table->string('telefono',50)->comment('Numero de teléfono o celular');
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id','fk_guiadesp_cliente')->references('id')->on('cliente')->onDelete('restrict')->onUpdate('restrict');
            $table->string('contacto',50)->comment('Nombre de contacto');
            $table->string('contactoemail',50)->comment('Email de contacto de entrega')->nullable();
            $table->string('contactotelf',50)->comment('Telefono de contacto de entregao')->nullable();
            $table->string('obs',200)->comment('Observaciones')->nullable();
            $table->unsignedBigInteger('formapago_id');
            $table->foreign('formapago_id','fk_guiadesp_formapago')->references('id')->on('formapago')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('vendedor_id');
            $table->foreign('vendedor_id','fk_guiadesp_vendedor')->references('id')->on('vendedor')->onDelete('restrict')->onUpdate('restrict');
            $table->date('plazoentrega')->comment('Plazo de entrega fecha');
            $table->date('fechaestdesp')->comment('Fecha estimada de Despacho.');
            $table->string('lugarentrega',100)->comment('Lugar de entrega');
            $table->unsignedBigInteger('plazopago_id');
            $table->foreign('plazopago_id','fk_guiadesp_plazopago')->references('id')->on('plazopago')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('tipoentrega_id');
            $table->foreign('tipoentrega_id','fk_guiadesp_tipoentrega')->references('id')->on('tipoentrega')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('comuna_id');
            $table->foreign('comuna_id','fk_guiadesp_comuna')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('comunaentrega_id')->comment('Comuna de entrega');
            $table->foreign('comunaentrega_id','fk_guiadesp_comunaentrega')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict');
            $table->float('neto',18,2)->comment('Total neto, Valor sin IVA');
            $table->float('piva',5,2)->comment('Porcentaje IVA')->nullable();
            $table->float('iva',18,2)->comment('Total IVA');
            $table->float('total',18,2)->comment('Total incluye IVA');
            */