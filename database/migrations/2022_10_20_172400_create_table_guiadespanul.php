<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableGuiadespanul extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guiadespanul', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('guiadesp_id');
            $table->foreign('guiadesp_id','fk_guiadespanul_guiadesp')->references('id')->on('guiadesp')->onDelete('restrict')->onUpdate('restrict');
            $table->string('obs',200)->comment('Observaciones');
            $table->tinyInteger('motanul_id')->comment('Motivo anular Guia Despacho. 1=Por fecha, 2=Por Precio, 3=Por solicitud del cliente, 4=Anular Registro GuiaDesp sin asignar Guia SII, 5=Anulada por Usuario');
            $table->string('moddevgiadesp_id',2)->comment("Modulo a donde se devuelve. GD=Guia Despacho, OD=Orden Despacho, SD=Solicitud Despacho, FC=Factura");
            $table->unsignedBigInteger('usuario_id')->comment('Usuario que creo registro');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino registro')->nullable();
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
        Schema::dropIfExists('guiadespanul');
    }
}
