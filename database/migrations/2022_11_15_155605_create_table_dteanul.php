<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDteanul extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dteanul', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dte_id');
            $table->foreign('dte_id','fk_dteanul_dte')->references('id')->on('dte')->onDelete('restrict')->onUpdate('restrict');
            $table->string('obs',200)->comment('Observaciones');
            $table->tinyInteger('motanul_id')->comment('Motivo anular Guia Despacho. 1=Por fecha, 2=Por Precio, 3=Por solicitud del cliente, 4=Anular Registro GuiaDesp sin asignar Guia SII, 5=Anulada por Usuario');
            $table->string('moddevgiadesp_id',2)->comment("Modulo a donde se devuelve. GD=Guia Despacho, OD=Orden Despacho, SD=Solicitud Despacho, FC=Factura");
            $table->unsignedBigInteger('usuario_id')->comment('Usuario que creo registro');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino registro')->nullable();
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
        Schema::dropIfExists('dteanul');
    }
}
