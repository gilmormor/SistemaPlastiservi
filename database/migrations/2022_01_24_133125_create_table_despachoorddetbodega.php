<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDespachoorddetbodega extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('despachoorddetbodega', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('despachoorddet_id');
            $table->foreign('despachoorddet_id','fk_despachoorddetbodega_despachoorddet')->references('id')->on('despachoorddet')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cant',10,2)->comment('Cantidad');
            $table->unsignedBigInteger('invstock_id');
            $table->foreign('invstock_id','fk_despachoorddetbodega_invstock')->references('id')->on('invstock')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('despachoorddetbodega');
    }
}
