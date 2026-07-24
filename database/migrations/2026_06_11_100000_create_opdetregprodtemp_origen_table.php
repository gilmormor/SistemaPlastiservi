<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Trazabilidad entre etapas de producción — RESERVA (lado temp).
 * Vincula un registro de producción temporal (opdetregprodtemp) con los lotes
 * aprobados (opdetregprod) de la etapa ANTERIOR de los que consumió material.
 * Se llena automáticamente por FIFO al guardar el registro del operario.
 * Al aprobar el temp, estas filas se copian a opdetregprod_origen (definitiva).
 */
class CreateOpdetregprodtempOrigenTable extends Migration
{
    public function up()
    {
        Schema::create('opdetregprodtemp_origen', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opdetregprodtemp_id'); // registro temp de la etapa actual (hijo)
            $table->unsignedBigInteger('opdetregprod_id');     // lote aprobado de la etapa anterior (padre/origen)
            $table->double('kg',   18, 2)->default(0);         // kg tomados de ese lote
            $table->double('cant', 10, 2)->default(0);         // unidades tomadas de ese lote
            $table->timestamps();

            $table->foreign('opdetregprodtemp_id', 'fk_tmporigen_temp')
                  ->references('id')->on('opdetregprodtemp');
            $table->foreign('opdetregprod_id', 'fk_tmporigen_lote')
                  ->references('id')->on('opdetregprod');
        });
    }

    public function down()
    {
        Schema::dropIfExists('opdetregprodtemp_origen');
    }
}
