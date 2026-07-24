<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla intermedia que vincula un ítem de solicitud de despacho (despachosoldet)
 * con el lote de producción específico (opdetregprod) que se está despachando.
 * Permite trazabilidad granular: saber de qué lote exacto provienen las unidades despachadas.
 */
class CreateDespachosoldetOpdetregprodTable extends Migration
{
    public function up()
    {
        Schema::create('despachosoldet_opdetregprod', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('despachosoldet_id');
            $table->unsignedBigInteger('opdetregprod_id');
            $table->double('cant',   10, 2)->default(0);  // unidades del lote asignadas a este despacho
            $table->double('cantkg', 18, 2)->default(0);  // kg del lote asignados a este despacho
            $table->timestamps();

            $table->foreign('despachosoldet_id')->references('id')->on('despachosoldet');
            $table->foreign('opdetregprod_id')->references('id')->on('opdetregprod');
        });
    }

    public function down()
    {
        Schema::dropIfExists('despachosoldet_opdetregprod');
    }
}
