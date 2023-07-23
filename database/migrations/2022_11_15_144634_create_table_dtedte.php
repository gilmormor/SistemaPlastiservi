<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDtedte extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dtedte', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dte_id')->comment("Id DTE");
            $table->foreign('dte_id','fk_dtedte_dte')->references('id')->on('dte')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('dter_id')->comment("Id DTEr relacion con otros registros de la misma tabla. en el caso de las facturas que esta relacionada con 1 o mas Guias de despacho.");
            $table->foreign('dter_id','fk_dtedte_dter')->references('id')->on('dte')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('dtefac_id')->nullable()->comment("Id DTE Relacion con documento raiz Factura. Con este campo relaciono todos los movimientos de una factura, NC y ND asociados a una factura.");
            $table->foreign('dtefac_id','fk_dtedte_dtefac')->references('id')->on('dte')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('dtedte');
    }
}
