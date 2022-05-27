<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDespachoordrecdetInvbodegaproducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('despachoordrecdet_invbodegaproducto', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('despachoordrecdet_id');
            $table->foreign('despachoordrecdet_id','fk_despachoordrecdet_invbodegaproducto_despachoordrecdet')->references('id')->on('despachoordrecdet')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('invbodegaproducto_id');
            $table->foreign('invbodegaproducto_id','fk_despachoordrecdet_invbodegaproducto_invbodegaproducto')->references('id')->on('invbodegaproducto')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cant',10,2)->comment('Cantidad');
            $table->float('cantkg',10,2)->comment('Cantidad Kilos');
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
        Schema::dropIfExists('despachoordrecdet_invbodegaproducto');
    }
}
