<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRechazonc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rechazonc', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('fecha')->comment('Fecha y hora rechazo.');
            $table->string('accioninmediata',250)->comment('Observación Accion inmediata No Conformidad.');
            $table->string('analisiscausa',250)->comment('Observación Analisis de Causa No Conformidad.');
            $table->string('accioncorrectiva',250)->comment('Observacion Accion Correctiva.');
            $table->unsignedBigInteger('noconformidad_id')->comment('Id No conformidad.');
            $table->foreign('noconformidad_id','fk_rechazonc_noconformidad')->references('id')->on('noconformidad')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('rechazonc');
    }
}
