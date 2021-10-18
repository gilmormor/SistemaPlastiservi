<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDespachoordrec extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('despachoordrec', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('despachoord_id');
            $table->foreign('despachoord_id','fk_despachoordrec_despachoord')->references('id')->on('despachoord')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario que creo el registro');
            $table->foreign('usuario_id','fk_despachoordrec_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->dateTime('fechahora')->comment('Fecha y hora.');
            $table->unsignedBigInteger('despachoordrecmotivo_id')->comment('Motivo de la rechazo');
            $table->foreign('despachoordrecmotivo_id','fk_despachoordrec_despachoordrecmotivo')->references('id')->on('despachoordrecmotivo')->onDelete('restrict')->onUpdate('restrict');
            $table->string('obs',200)->comment('Observaciones')->nullable();
            $table->tinyInteger('solnotacred')->comment('Solicitud de nota de credito Si=1, No=0')->default(0);
            $table->string('documento_id',10)->comment('Numero documento de rechazo')->nullable();
            $table->string('documento_file',100)->comment('Archivo o imagen documento de rechazo')->nullable();
            $table->dateTime('anulada')->comment('Fecha cuando fue anulada la rechazo orden de despacho.')->nullable();;
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
        Schema::dropIfExists('despachoordrec');
    }
}
