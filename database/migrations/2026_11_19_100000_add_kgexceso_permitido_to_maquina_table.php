<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kg que una maquina puede procesar POR ENCIMA de lo que tiene el lote.
 *
 * Caso real: las extrusoras sin cuenta metros. El deposito no puede quedarse
 * vacio, asi que antes de terminar el lote que estan procesando le echan el
 * siguiente -que suele ser la misma materia prima pero de otra OT y otra nota
 * de venta-. Como la maquina no avisa cuando se llego a los kilos del lote, el
 * operario corta el rollo a ojo y siempre se pasa un poco: esos kilos de mas
 * salieron fisicamente del lote siguiente.
 *
 * El valor es por maquina porque el problema es de la maquina, no de la etapa:
 * las que tienen cuenta metros no lo necesitan. Nace en 0, asi que mientras no
 * se configure ninguna, el sistema se comporta exactamente igual que antes.
 */
class AddKgexcesoPermitidoToMaquinaTable extends Migration
{
    public function up()
    {
        Schema::table('maquina', function (Blueprint $table) {
            $table->double('kgexceso_permitido', 18, 2)->default(0)->after('desc');
        });
    }

    public function down()
    {
        Schema::table('maquina', function (Blueprint $table) {
            $table->dropColumn('kgexceso_permitido');
        });
    }
}
