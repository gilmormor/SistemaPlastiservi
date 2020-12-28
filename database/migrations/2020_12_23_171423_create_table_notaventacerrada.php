<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNotaventacerrada extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notaventacerrada', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('notaventa_id');
            $table->foreign('notaventa_id','fk_notaventacerrada_notaventa')->references('id')->on('notaventa')->onDelete('restrict')->onUpdate('restrict');
            $table->string('observacion',200)->comment('Observaciones');
            $table->boolean('motcierre_id')->comment('Motivo cierre NotaVenta. 1=Por fecha, 2=Por Precio, 3=Por solicitud del cliente');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario que creo el registro');
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
        Schema::dropIfExists('notaventacerrada');
    }
}
