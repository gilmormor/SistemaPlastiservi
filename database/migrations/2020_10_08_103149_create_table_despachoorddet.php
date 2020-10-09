<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDespachoorddet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('despachoorddet', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('despachoord_id');
            $table->foreign('despachoord_id','fk_despachoorddet_despachoord')->references('id')->on('despachoord')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('despachosoldet_id');
            $table->foreign('despachosoldet_id','fk_despachoorddet_despachosoldet')->references('id')->on('despachosoldet')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('notaventadetalle_id');
            $table->foreign('notaventadetalle_id','fk_despachoorddet_notaventadetalle')->references('id')->on('notaventadetalle')->onDelete('restrict')->onUpdate('restrict');
            $table->string('obs',250)->comment('Observación.')->nullable();
            $table->float('cantdesp',10,2)->comment('Cantidad despacho.')->nullable();
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
        Schema::dropIfExists('despachoorddet');
    }
}
