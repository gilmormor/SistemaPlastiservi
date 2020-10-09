<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDespachosol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('despachosol', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('notaventa_id');
            $table->foreign('notaventa_id','fk_despachosol_notaventa')->references('id')->on('notaventa')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario que creo el registro');
            $table->foreign('usuario_id','fk_despachosol_usuario')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->dateTime('fecha')->comment('Fecha y hora.');
            $table->string('obs',250)->comment('Observación.')->nullable();
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
        Schema::dropIfExists('despachosol');
    }
}
