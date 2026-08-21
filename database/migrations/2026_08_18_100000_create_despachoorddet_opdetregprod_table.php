<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla intermedia que vincula un ítem de orden de despacho (despachoorddet)
 * con el lote de producción específico (opdetregprod) que se está despachando.
 * Extiende hasta la Orden de Despacho la misma trazabilidad granular que ya
 * existe entre Solicitud de Despacho y lote (despachosoldet_opdetregprod).
 * Se puebla al aprobar la guía (guardarguiadesp, copiando desde
 * despachosoldet_opdetregprod) y se usa al devolver (devolverguiadesp) para
 * mantener el vínculo InvMovDetOpDetRegProd en los movimientos de reversión.
 */
class CreateDespachoorddetOpdetregprodTable extends Migration
{
    public function up()
    {
        Schema::create('despachoorddet_opdetregprod', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('despachoorddet_id');
            $table->unsignedBigInteger('opdetregprod_id');
            $table->double('cant',   10, 2)->default(0);  // unidades del lote asignadas a esta orden
            $table->double('cantkg', 18, 2)->default(0);  // kg del lote asignados a esta orden
            $table->timestamps();

            $table->foreign('despachoorddet_id')->references('id')->on('despachoorddet');
            $table->foreign('opdetregprod_id')->references('id')->on('opdetregprod');
        });
    }

    public function down()
    {
        Schema::dropIfExists('despachoorddet_opdetregprod');
    }
}
