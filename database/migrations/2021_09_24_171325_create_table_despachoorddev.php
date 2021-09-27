<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDespachoorddev extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('despachoorddev', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('despachoord_id');
            $table->foreign('despachoord_id','fk_despachoorddev_despachoord')->references('id')->on('despachoord')->onDelete('restrict')->onUpdate('restrict');
            $table->dateTime('fechahora')->comment('Fecha y hora.');
            $table->string('obs',200)->comment('Observaciones')->nullable();
            $table->dateTime('anulada')->comment('Fecha cuando fue anulada la devolucion de orden de despacho.')->nullable();;
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
        Schema::dropIfExists('despachoorddev');
    }
}
