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
            $table->dateTime('fechahora')->comment('Fecha y hora de Nota de venta');
            $table->unsignedBigInteger('despachoord_id');
            $table->foreign('despachoord_id','fk_guiadesp_despachoord')->references('id')->on('despachoord')->onDelete('restrict')->onUpdate('restrict')->nullable();
            $table->unsignedBigInteger('notaventa_id');
            $table->foreign('notaventa_id','fk_guiadesp_notaventa')->references('id')->on('notaventa')->onDelete('restrict')->onUpdate('restrict');
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
            $table->boolean('traslado')->comment('Estatus si es guia de despacho de traslado.')->nullable();
            $table->string('ot',5)->comment('Orden de trabajo');
            $table->boolean('aprobstatus')->comment('Status de aprobacion (null o 0)=Sin aprobar, 1=Aprobada')->nullable();
            $table->unsignedBigInteger('aprobusu_id')->comment('Usuario quien aprobo la Nota de Venta, este es el estatus de aprovacion de cotizacion')->nullable();
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
