<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCcregistmuestradetTable extends Migration
{
    public function up()
    {
        Schema::create('ccregistmuestradet', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ccregistmuestra_id');
            $table->foreign('ccregistmuestra_id', 'fk_ccregistmuestradet_ccregistmuestra')
                  ->references('id')->on('ccregistmuestra')
                  ->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('ccparam_apsucetapaprod_id')->comment('Parámetro configurado para esta etapa');
            $table->foreign('ccparam_apsucetapaprod_id', 'fk_ccregistmuestradet_ccparamapsuc')
                  ->references('id')->on('ccparam_apsucetapaprod')
                  ->onDelete('restrict')->onUpdate('restrict');
            $table->string('valor', 500)->nullable()->comment('Valor medido por el inspector');
            $table->tinyInteger('resultado')->default(1)->comment('1=Ok (dentro de rango), 2=Observación, 3=Fuera de rango. Calculado automáticamente vs min/max.');
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
        });
    }

    public function down()
    {
        Schema::dropIfExists('ccregistmuestradet');
    }
}
