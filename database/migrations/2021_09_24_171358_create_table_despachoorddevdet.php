<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDespachoorddevdet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('despachoorddevdet', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('despachoorddev_id');
            $table->foreign('despachoorddev_id','fk_despachoorddevdet_despachoorddev')->references('id')->on('despachoorddev')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('despachoorddet_id');
            $table->foreign('despachoorddet_id','fk_despachoorddevdet_despachoorddet')->references('id')->on('despachoorddet')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cantdev',10,2)->comment('Cantidad devuelta.')->nullable();
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
        Schema::dropIfExists('despachoorddevdet');
    }
}
