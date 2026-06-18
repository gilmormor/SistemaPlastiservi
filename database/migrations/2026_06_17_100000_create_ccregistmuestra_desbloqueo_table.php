<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCcregistmuestraDesbloqueoTable extends Migration
{
    public function up()
    {
        Schema::create('ccregistmuestra_desbloqueo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ccregistmuestra_id');
            $table->text('observacion');
            $table->unsignedBigInteger('usuario_id');
            $table->timestamps();

            $table->foreign('ccregistmuestra_id')->references('id')->on('ccregistmuestra');
            $table->foreign('usuario_id')->references('id')->on('usuario');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ccregistmuestra_desbloqueo');
    }
}
