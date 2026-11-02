<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDtetrans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dtetrans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dte_id');
            $table->foreign('dte_id','fk_dtetrans_dte')->references('id')->on('dte')->onDelete('restrict')->onUpdate('restrict')->nullable();
            $table->dateTime('fchsalida')->comment('Fecha y hora efectiva de inicio del traslado');
            $table->date('fchllegada')->comment('Fecha de llegada del traslado');
            $table->string('dirdest')->comment('Dato correspondiente a dirección de destino que acompaña productos o a la Dirección que se otorga el servicio');
            $table->unsignedBigInteger('comuna_id');
            $table->foreign('comuna_id','fk_dtetrans_comuna')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict')->nullable();
            $table->string('cmnadest')->comment('Comuna de la dirección de destino');
            $table->string('ruttrans')->comment('RUT de la empresa de transporte')->nullable();
            $table->string('rutchofer')->comment('Rut del Chofer que realiza el transporte de mercadería');
            $table->string('nombrechofer')->comment('Nombre del Chofer que realiza el transporte de mercaderia');
            $table->string('patente')->comment('Patente del vehículo donde se transporta mercaderia');
            $table->string('patentecarro')->comment('Patente de carro anexo al vehículo de transporte')->nullable();
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
        Schema::dropIfExists('dtetrans');
    }
}
