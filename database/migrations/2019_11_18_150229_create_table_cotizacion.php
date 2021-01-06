<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCotizacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sucursal_id')->comment("Id de Sucursal");
            $table->foreign('sucursal_id','fk_cotizacion_sucursal')->references('id')->on('sucursal')->onDelete('restrict')->onUpdate('restrict');
            $table->dateTime('fechahora')->comment('Fecha y hora de cotización');
            $table->string('direccioncot',200)->comment('Direccion de cotización. Se guarda para tener la direccion con la que fue hecha la cotizacion esto es por si cambioan la direccion en clientes.');
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->foreign('cliente_id','fk_cotizacion_cliente')->references('id')->on('cliente')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('clientetemp_id')->nullable();
            $table->foreign('clientetemp','fk_cotizacion_clientetemp')->references('id')->on('clientetemp')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('clientedirec_id');
            $table->foreign('clientedirec_id','fk_cotizacion_clientedirec')->references('id')->on('clientedirec')->onDelete('restrict')->onUpdate('restrict');
            $table->string('contacto',50)->comment('Nombre de contacto');
            $table->string('email',50)->comment('Correo electronico');
            $table->string('telefono',50)->comment('Numero de teléfono o celular');
            $table->string('observacion',200)->comment('Observaciones')->nullable();
            $table->unsignedBigInteger('formapago_id');
            $table->foreign('formapago_id','fk_cotizacion_formapago')->references('id')->on('formapago')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('vendedor_id');
            $table->foreign('vendedor_id','fk_cotizacion_vendedor')->references('id')->on('vendedor')->onDelete('restrict')->onUpdate('restrict');
            $table->date('plazoentrega')->comment('Plazo de entrega fecha');
            $table->string('lugarentrega',100)->comment('Lugar de entrega');
            $table->unsignedBigInteger('plazopago_id');
            $table->foreign('plazopago_id','fk_cotizacion_plazopago')->references('id')->on('plazopago')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('tipoentrega_id');
            $table->foreign('tipoentrega_id','fk_cotizacion_tipoentrega')->references('id')->on('tipoentrega')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('region_id');
            $table->foreign('region_id','fk_cotizacion_region')->references('id')->on('region')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('provincia_id');
            $table->foreign('provincia_id','fk_cotizacion_provincia')->references('id')->on('provincia')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('comuna_id');
            $table->foreign('comuna_id','fk_cotizacion_comuna')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('giro_id');
            $table->foreign('giro_id','fk_cotizacion_giro')->references('id')->on('giro')->onDelete('restrict')->onUpdate('restrict');
            $table->float('neto',15,2)->comment('Total neto, Valor sin IVA');
            $table->float('iva',15,2)->comment('Total IVA');
            $table->float('total',15,2)->comment('Total incluye IVA');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario quien creo el registro');
            $table->foreign('usuario_id','fk_cotizacion_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->boolean('aprobstatus')->comment('Status de aprobacion (null o 0)=Sin aprobar, 1=Cotizacion cerrada por el vendedor para hacer NV, 2=Cotizacion cerrada por vendedor pero no cumple, debe pasar por aprobacion, 3=Aprobada por supervisor, 4=Rechazada por Supersivor')->nullable();
            $table->unsignedBigInteger('aprobusu_id')->comment('Usuario quien aprobo la cotización, este es el estatus de aprovacion de cotizacion')->nullable();
            $table->foreign('aprobusu_id','fk_cotizacion_aprobusu')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->dateTime('aprobfechahora')->comment('fecha y hora cuando fue aprobada la cotización.')->nullable();
            $table->string('aprobobs',300)->comment('Observacion aprobacion de Cotizacion')->nullable();
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
        Schema::dropIfExists('cotizacion');
    }
}
