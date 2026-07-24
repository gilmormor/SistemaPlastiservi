<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCcparamApsucetapaprodTable extends Migration
{
    public function up()
    {
        Schema::create('ccparam_apsucetapaprod', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('apsucetapaprod_id')->comment('Etapa de producción');
            $table->foreign('apsucetapaprod_id', 'fk_ccparam_ap_apsucetapaprod')
                  ->references('id')->on('areaproduccionsucetapaprod')
                  ->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('ccparam_id')->comment('Parámetro CC del catálogo');
            $table->foreign('ccparam_id', 'fk_ccparam_ap_ccparam')
                  ->references('id')->on('ccparam')
                  ->onDelete('restrict')->onUpdate('restrict');
            $table->decimal('valor_min', 10, 4)->nullable()->comment('Valor mínimo aceptable');
            $table->decimal('valor_max', 10, 4)->nullable()->comment('Valor máximo aceptable');
            $table->boolean('requerido')->default(1)->comment('Si es obligatorio ingresar este parámetro');
            $table->tinyInteger('orden')->default(0);
            $table->unsignedBigInteger('usuariodel_id')->nullable()->comment('ID Usuario que eliminó el registro');
            $table->timestamps();
            $table->softDeletes();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
        });
    }

    public function down()
    {
        Schema::dropIfExists('ccparam_apsucetapaprod');
    }
}
