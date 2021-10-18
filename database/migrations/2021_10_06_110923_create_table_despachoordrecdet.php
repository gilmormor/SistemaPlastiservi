<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDespachoordrecdet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('despachoordrecdet', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('despachoordrec_id');
            $table->foreign('despachoordrec_id','fk_despachoordrecdet_despachoordrec')->references('id')->on('despachoordrec')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('despachoorddet_id');
            $table->foreign('despachoorddet_id','fk_despachoordrecdet_despachoorddet')->references('id')->on('despachoorddet')->onDelete('restrict')->onUpdate('restrict');
            $table->float('cantrec',10,2)->comment('Cantidad recuelta.')->nullable();
            $table->string('obsdet',50)->comment('Observaciones Detalle')->nullable();
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
        Schema::dropIfExists('despachoordrecdet');
    }
}
