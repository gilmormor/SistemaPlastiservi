<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla intermedia que vincula un item de rechazo/devolucion de orden de despacho
 * (despachoordrecdet) con el lote de produccion (opdetregprod) del que proviene
 * la mercaderia devuelta por el cliente.
 *
 * Cierra la trazabilidad en el retorno: al aprobar el rechazo (aprorecod) el
 * reingreso a bodega -o a Scrap si el motivo es material defectuoso- queda
 * asociado al lote real, permitiendo el analisis de calidad por lote.
 *
 * Es opcional: si un rechazo no tiene lotes asignados (datos historicos o
 * productos que no vienen de produccion), el flujo se comporta como siempre.
 */
class CreateDespachoordrecdetOpdetregprodTable extends Migration
{
    public function up()
    {
        Schema::create('despachoordrecdet_opdetregprod', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('despachoordrecdet_id');
            $table->unsignedBigInteger('opdetregprod_id');
            $table->double('cant',   10, 2)->default(0);  // unidades devueltas de ese lote
            $table->double('cantkg', 18, 2)->default(0);  // kg devueltos de ese lote
            $table->timestamps();

            $table->foreign('despachoordrecdet_id')->references('id')->on('despachoordrecdet');
            $table->foreign('opdetregprod_id')->references('id')->on('opdetregprod');
        });
    }

    public function down()
    {
        Schema::dropIfExists('despachoordrecdet_opdetregprod');
    }
}
