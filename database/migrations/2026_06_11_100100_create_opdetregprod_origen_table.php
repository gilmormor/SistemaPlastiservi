<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Trazabilidad entre etapas de producción — DEFINITIVA (lado aprobado).
 * Vincula un registro de producción aprobado (opdetregprod) con los lotes
 * aprobados de la etapa ANTERIOR de los que consumió material.
 * Se llena al aprobar el supervisor (copia de opdetregprodtemp_origen),
 * espejo del patrón opdetregprodtemp_campoval → opdetregprod_campoval.
 */
class CreateOpdetregprodOrigenTable extends Migration
{
    public function up()
    {
        Schema::create('opdetregprod_origen', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opdetregprod_id');        // registro aprobado de la etapa actual (hijo)
            $table->unsignedBigInteger('opdetregprod_origen_id'); // lote aprobado de la etapa anterior (padre/origen)
            $table->double('kg',   18, 2)->default(0);            // kg tomados de ese lote
            $table->double('cant', 10, 2)->default(0);            // unidades tomadas de ese lote
            $table->timestamps();

            $table->foreign('opdetregprod_id', 'fk_origen_hijo')
                  ->references('id')->on('opdetregprod');
            $table->foreign('opdetregprod_origen_id', 'fk_origen_padre')
                  ->references('id')->on('opdetregprod');
        });
    }

    public function down()
    {
        Schema::dropIfExists('opdetregprod_origen');
    }
}
