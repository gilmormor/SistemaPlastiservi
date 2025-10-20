<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProducciontransfer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producciontransfer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opdet_id');
            $table->foreign('opdet_id','fk_producciontransfer_opdet')->references('id')->on('opdet')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('produccion_id')->comment('Id produccion donde se origino la transferencia de produccion a la siguiente etapa.');
            $table->foreign('produccion_id','fk_producciontransfer_produccion')->references('id')->on('produccion')->onDelete('restrict')->onUpdate('restrict');
            $table->engine = 'InnoDB';
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
        Schema::dropIfExists('producciontransfer');
    }
}
