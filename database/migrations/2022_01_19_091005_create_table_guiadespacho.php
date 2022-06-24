<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableGuiadespacho extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guiadespacho', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('despachoord_id');
            $table->foreign('despachoord_id','fk_guiadespacho_despachoord')->references('id')->on('despachoord')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('notaventa_id');
            $table->foreign('notaventa_id','fk_guiadespacho_notaventa')->references('id')->on('notaventa')->onDelete('restrict')->onUpdate('restrict');
            $table->dateTime('fechahora')->comment('Fecha y hora.');
            $table->unsignedBigInteger('comunaentrega_id')->comment('Comuna de entrega');
            $table->foreign('comunaentrega_id','fk_guiadespacho_comunaentrega')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('tipoentrega_id');
            $table->foreign('tipoentrega_id','fk_guiadespacho_tipoentrega')->references('id')->on('tipoentrega')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('sucursal_id')->comment("Id de Sucursal");
            $table->foreign('sucursal_id','fk_guiadespacho_sucursal')->references('id')->on('sucursal')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id','fk_guiadespacho_cliente')->references('id')->on('cliente')->onDelete('restrict')->onUpdate('restrict');
            $table->string('rut',12)->comment('RUT Cliente');
            $table->string('razonsocial',70)->comment('Nombre cliente');
            $table->string('giro',100)->comment('Giro');
            $table->string('dircliente',200)->comment('Direccion cliente.');
            $table->string('comuna',100)->comment('Comuna.');
            $table->string('ciudad',100)->comment('Ciudad.');
            $table->unsignedBigInteger('comuna_id');
            $table->foreign('comuna_id','fk_guiadespacho_comuna')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict');

            /*$table->date('plazoentrega')->comment('Plazo de entrega fecha');*/
            $table->string('lugarentrega',100)->comment('Lugar de entrega');
            $table->string('contacto',50)->comment('Nombre de contacto');
            $table->string('contactoemail',50)->comment('Email de contacto de entrega')->nullable();
            $table->string('contactotelf',50)->comment('Telefono de contacto de entregao')->nullable();
            $table->string('oc_id',15)->comment('Numero de Orden de Compra')->nullable();
            $table->string('oc_file',20)->comment('Archivo o imagen de Orden de Compra')->nullable();
            $table->string('obs',200)->comment('Observaciones')->nullable();
            $table->dateTime('anulada')->comment('Fecha de anulación')->nullable();
            $table->float('piva',5,2)->comment('Porcentaje IVA')->nullable()->after('neto');
            $table->float('neto',18,2)->comment('Total neto, Valor sin IVA');
            $table->float('iva',18,2)->comment('Total IVA');
            $table->float('total',18,2)->comment('Total incluye IVA');

            /*
            $table->date('fechaestdesp')->comment('Fecha estimada de Despacho.');
            $table->string('guiadespacho',50)->comment('Guia despacho')->nullable();
            $table->dateTime('guiadespachofec')->comment('Fecha inclusion guia despacho.')->nullable();
            $table->string('numfactura',50)->comment('Número de Factura')->nullable();
            $table->date('fechafactura')->comment('Fecha de factura.')->nullable();
            $table->dateTime('numfacturafec')->comment('Fecha inclusion numero de factura.')->nullable();
            $table->unsignedBigInteger('despachoobs_id')->nullable();
            $table->foreign('despachoobs_id','fk_guiadespacho_despachoobs')->references('id')->on('despachoobs')->onDelete('restrict')->onUpdate('restrict');
            $table->boolean('aprobstatus')->comment('Status de aprobacion (null o 0)=Sin aprobar, 1=enviar a aprobacion, 2=aprobada, 3=Rechazada')->nullable();
            $table->dateTime('aprobfechahora')->comment('Fecha y hora cuando fue aprobada.')->nullable();
            */
            $table->unsignedBigInteger('usuario_id')->comment('Usuario que creo el registro');
            $table->foreign('usuario_id','fk_guiadespacho_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('guiadespacho');
    }
}
