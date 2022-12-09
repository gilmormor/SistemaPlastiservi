<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDteguiausada extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dteguiausada', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dte_id');
            $table->foreign('dte_id','fk_dteguiausada_dte')->references('id')->on('dte')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario que creo registro');
            $table->unsignedBigInteger('usuariodel_id')->comment('ID Usuario que elimino registro')->nullable();
            $table->engine = 'InnoDB';
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
        Schema::dropIfExists('dteguiausada');
    }
}
