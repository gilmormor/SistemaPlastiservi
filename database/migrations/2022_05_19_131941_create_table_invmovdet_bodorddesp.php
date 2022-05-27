<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableInvmovdetBodorddesp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invmovdet_bodorddesp', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invmovdet_id')->comment("Id persona");
            $table->foreign('invmovdet_id','fk_invmovdet_bodorddesp_invmovdet')->references('id')->on('invmovdet')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('despachoorddet_invbodegaproducto_id')->comment("Id persona");
            $table->foreign('despachoorddet_invbodegaproducto_id','fk_invmovdet_bodorddesp_dsd_invbodpro')->references('id')->on('despachoorddet_invbodegaproducto')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('invmovdet_bodorddesp');
    }
}
