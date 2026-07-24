<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCcregistmuestraanulTable extends Migration
{
    public function up()
    {
        Schema::create('ccregistmuestraanul', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ccregistmuestra_id')->comment('Muestra anulada');
            $table->foreign('ccregistmuestra_id', 'fk_ccregistmuestraanul_ccregistmuestra')
                  ->references('id')->on('ccregistmuestra')
                  ->onDelete('restrict')->onUpdate('restrict');
            $table->string('motivo', 200)->nullable()->comment('Motivo de la anulación');
            $table->unsignedBigInteger('usuario_id')->comment('Usuario que anuló');
            $table->foreign('usuario_id', 'fk_ccregistmuestraanul_usuario')
                  ->references('id')->on('usuario')
                  ->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('usuariodel_id')->nullable()->comment('ID Usuario que eliminó el registro');
            $table->timestamps();
            $table->softDeletes();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
        });
    }

    public function down()
    {
        Schema::dropIfExists('ccregistmuestraanul');
    }
}
