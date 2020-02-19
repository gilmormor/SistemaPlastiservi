<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNotaventa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notaventa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sucursal_id')->comment("Id de Sucursal");
            $table->foreign('sucursal_id','fk_notaventa_sucursal')->references('id')->on('sucursal')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('cotizacion_id')->nullable();
            $table->foreign('cotizacion_id','fk_notaventa_cotizacion')->references('id')->on('cotizacion')->onDelete('restrict')->onUpdate('restrict');
            $table->dateTime('fechahora')->comment('Fecha y hora de Nota de venta');
            $table->string('direccioncot',200)->comment('Direccion de Nota de venta. Se guarda para tener la direccion con la que fue hecha la Nota de Venta esto es por si cambia la direccion en clientes.');
            $table->string('email',50)->comment('Correo electronico');
            $table->string('telefono',50)->comment('Numero de teléfono o celular');
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id','fk_notaventa_cliente')->references('id')->on('cliente')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('clientedirec_id');
            $table->foreign('clientedirec_id','fk_notaventa_clientedirec')->references('id')->on('clientedirec')->onDelete('restrict')->onUpdate('restrict');
            $table->string('contacto',50)->comment('Nombre de contacto');
            $table->string('contactoemail',50)->comment('Email de contacto de entrega')->nullable();
            $table->string('contactotelf',50)->comment('Telefono de contacto de entregao')->nullable();
            $table->string('observacion',200)->comment('Observaciones')->nullable();
            $table->unsignedBigInteger('formapago_id');
            $table->foreign('formapago_id','fk_notaventa_formapago')->references('id')->on('formapago')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('vendedor_id');
            $table->foreign('vendedor_id','fk_notaventa_vendedor')->references('id')->on('vendedor')->onDelete('restrict')->onUpdate('restrict');
            $table->date('plazoentrega')->comment('Plazo de entrega fecha');
            $table->string('lugarentrega',100)->comment('Lugar de entrega');
            $table->unsignedBigInteger('plazopago_id');
            $table->foreign('plazopago_id','fk_notaventa_plazopago')->references('id')->on('plazopago')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('tipoentrega_id');
            $table->foreign('tipoentrega_id','fk_notaventa_tipoentrega')->references('id')->on('tipoentrega')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('region_id');
            $table->foreign('region_id','fk_notaventa_region')->references('id')->on('region')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('provincia_id');
            $table->foreign('provincia_id','fk_notaventa_provincia')->references('id')->on('provincia')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('comuna_id');
            $table->foreign('comuna_id','fk_notaventa_comuna')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('comunaentrega_id')->comment('Comuna de entrega');
            $table->foreign('comunaentrega_id','fk_notaventa_comunaentrega')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('giro_id');
            $table->foreign('giro_id','fk_notaventa_giro')->references('id')->on('giro')->onDelete('restrict')->onUpdate('restrict');
            $table->float('neto',10,2)->comment('Total neto, Valor sin IVA');
            $table->float('iva',10,2)->comment('Total IVA');
            $table->float('total',10,2)->comment('Total incluye IVA');
            $table->string('oc_id',10)->comment('Numero de Orden de Compra')->nullable();
            $table->string('oc_file',100)->comment('Archivo o imagen de Orden de Compra')->nullable();
            $table->unsignedBigInteger('usuario_id')->comment('Usuario quien creo el registro');
            $table->foreign('usuario_id','fk_notaventa_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->boolean('aprobstatus')->comment('Status de aprobacion (null o 0)=Sin aprobar, 1=Nota de Venta cerrada por el vendedor para hacer NV, 2=Nota de Venta cerrada por vendedor pero no cumple, debe pasar por aprobacion, 3=Aprobada por supervisor, 4=Rechazada por Supersivor')->nullable();
            $table->unsignedBigInteger('aprobusu_id')->comment('Usuario quien aprobo la Nota de Venta, este es el estatus de aprovacion de cotizacion')->nullable();
            $table->foreign('aprobusu_id','fk_notaventa_aprobusu')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->dateTime('aprobfechahora')->comment('fecha y hora cuando fue aprobada la cotización.')->nullable();
            $table->string('aprobobs',300)->comment('Observación aprobacion de Nota de Venta')->nullable();
            $table->dateTime('anulada')->comment('Fecha cuando fue anulada la nota de venta, si esta anulada se puede hacer otra nota de venta con la misma cotización')->nullable();;
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
        Schema::dropIfExists('notaventa');
    }
}
