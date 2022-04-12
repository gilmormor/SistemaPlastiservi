<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDespachosoldetInvbodegaproducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('despachosoldet_invbodegaproducto', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('despachosoldet_id');
            $table->foreign('despachosoldet_id','fk_despachosoldet_invbodegaproducto_despachosoldet')->references('id')->on('despachosoldet')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('invbodegaproducto_id');
            $table->foreign('invbodegaproducto_id','fk_despachosoldet_invbodegaproducto_invbodegaproducto')->references('id')->on('invbodegaproducto')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cant',10,2)->comment('Cantidad');
            $table->float('cantkg',10,2)->comment('Cantidad Kilos');
            $table->float('cantex',10,2)->comment('Cantidad exceso de stock, solicitado por encima del stock.');
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
        Schema::dropIfExists('despachosoldet_invbodegaproducto');
    }
}
