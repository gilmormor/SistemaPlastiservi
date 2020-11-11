<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDespachoordanulguiafact extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('despachoordanulguiafact', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('despachoord_id');
            $table->foreign('despachoord_id','fk_despachoordanulgf_despachoord')->references('id')->on('despachoord')->onDelete('restrict')->onUpdate('restrict');
            $table->string('guiadespacho',50)->comment('Guia despacho')->nullable();
            $table->dateTime('guiadespachofec')->comment('Fecha inclusion guia despacho.')->nullable();
            $table->string('numfactura',50)->comment('Número de Factura')->nullable();
            $table->date('fechafactura')->comment('Fecha de factura.')->nullable();
            $table->dateTime('numfacturafec')->comment('Fecha inclusion numero de factura.')->nullable();
            $table->string('observacion',200)->comment('Observaciones');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario que creo el registro');
            $table->foreign('usuario_id','fk_despachoordanulgf_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->integer('status')->comment('1 = Anulado y devuelto a guia despacho, 2= Anulado y devuelto a orden despacho.');
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
        Schema::dropIfExists('despachoordanulguiafact');
    }
}
