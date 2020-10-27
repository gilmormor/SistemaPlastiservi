<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDespachoord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('despachoord', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('despachosol_id');
            $table->foreign('despachosol_id','fk_despachoord_despachosol')->references('id')->on('despachosol')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('notaventa_id');
            $table->foreign('notaventa_id','fk_despachoord_notaventa')->references('id')->on('notaventa')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario que creo el registro');
            $table->foreign('usuario_id','fk_despachoord_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->dateTime('fechahora')->comment('Fecha y hora.');
            $table->unsignedBigInteger('comunaentrega_id')->comment('Comuna de entrega');
            $table->foreign('comunaentrega_id','fk_despachoord_comunaentrega')->references('id')->on('comuna')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('tipoentrega_id');
            $table->foreign('tipoentrega_id','fk_despachoord_tipoentrega')->references('id')->on('tipoentrega')->onDelete('restrict')->onUpdate('restrict');
            $table->date('plazoentrega')->comment('Plazo de entrega fecha');
            $table->string('lugarentrega',100)->comment('Lugar de entrega');
            $table->string('contacto',50)->comment('Nombre de contacto');
            $table->string('contactoemail',50)->comment('Email de contacto de entrega')->nullable();
            $table->string('contactotelf',50)->comment('Telefono de contacto de entregao')->nullable();
            $table->string('observacion',200)->comment('Observaciones')->nullable();
            $table->string('guiadespacho',50)->comment('Guia despacho')->nullable();
            $table->string('numfactura',50)->comment('Número de Factura')->nullable();
            $table->dateTime('fechguiafac')->comment('Fecha inclusion guia despacho y Numero de Factura');
            $table->date('fechaestdesp')->comment('Fecha estimada de Despacho.');
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
        Schema::dropIfExists('despachoord');
    }
}
