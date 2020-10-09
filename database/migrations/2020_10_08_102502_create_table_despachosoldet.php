<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDespachosoldet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('despachosoldet', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('despachosol_id');
            $table->foreign('despachosol_id','fk_despachosoldet_despachosol')->references('id')->on('despachosol')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('notaventadetalle_id');
            $table->foreign('notaventadetalle_id','fk_despachosoldet_notaventadetalle')->references('id')->on('notaventadetalle')->onDelete('restrict')->onUpdate('restrict');
            $table->string('obs',250)->comment('Observación.')->nullable();
            $table->float('cantsoldesp',10,2)->comment('Cantidad solicitada para despacho.')->nullable();
            $table->float('cantdesp',10,2)->comment('Suma total Cantidad Despachada.')->nullable();
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
        Schema::dropIfExists('despachosoldet');
    }
}
