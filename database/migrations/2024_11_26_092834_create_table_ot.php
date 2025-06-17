<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ot', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->dateTime('fechahora')->comment('Fecha y hora');
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id','fk_ot_cliente')->references('id')->on('cliente')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('sucursal_id')->comment("Id de Sucursal");
            $table->foreign('sucursal_id','fk_ot_sucursal')->references('id')->on('sucursal')->onDelete('restrict')->onUpdate('restrict');
            $table->date('fechaestdesp')->comment('Fecha estimada de Despacho.');
            $table->string('obs',100)->comment('Observacion')->nullable();
            $table->string('oc_id',18)->comment('Numero de Orden de Compra')->nullable();
            $table->string('oc_file',100)->comment('Archivo o imagen de Orden de Compra')->nullable();
            $table->unsignedBigInteger('vendedor_id');
            $table->foreign('vendedor_id','fk_ot_vendedor')->references('id')->on('vendedor')->onDelete('restrict')->onUpdate('restrict');
            $table->float('kg',18,2)->comment('Total Kg')->nullable();
            $table->float('kgprod',18,2)->comment('Total Kg para produccion')->nullable();
            $table->float('neto',18,2)->comment('Total neto, Valor sin IVA');
            $table->float('iva',18,2)->comment('Total IVA');
            $table->float('total',18,2)->comment('Total incluye IVA');
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id','fk_ot_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->boolean('aprobstatus')->comment('Status de aprobacion (null o 0)=Sin aprobar, 1=Enviado aprobacion, 2=Aprobado, 3=Rechazado')->default(0)->nullable();
            $table->unsignedBigInteger('aprobusu_id')->comment('Usuario quien aprobo')->nullable();
            $table->foreign('aprobusu_id','fk_ot_aprobusu')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->string('obsrechazo',40)->comment('Observacion rechazo de aprobacion')->nullable();
            $table->dateTime('aprobfechahora')->comment('Fecha y hora cuando fue aprobada.')->nullable();
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
        Schema::dropIfExists('ot');
    }
}
