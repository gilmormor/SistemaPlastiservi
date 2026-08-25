<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Permite que un parámetro CC de una etapa se valide contra el acuerdo técnico
 * del producto en vez de contra un rango fijo.
 *
 * Hasta ahora el rango era valor_min / valor_max, igual para todos los productos.
 * Eso no sirve para espesor, ancho y largo: el valor correcto es el que dice el
 * acuerdo técnico de cada producto, y lo que se valida es que la medición no se
 * aleje más allá de la tolerancia que el propio AT ya tiene guardada
 * (at_espesordesv, at_anchodesv, at_largodesv).
 *
 * at_campo indica qué campo del AT es el objetivo. Vacío = comportamiento actual
 * con rango fijo, por lo que ningún parámetro ya configurado cambia de conducta.
 */
class AddAtCampoToCcparamApsucetapaprodTable extends Migration
{
    public function up()
    {
        Schema::table('ccparam_apsucetapaprod', function (Blueprint $table) {
            $table->string('at_campo', 30)->nullable()->after('valor_max');
        });
    }

    public function down()
    {
        Schema::table('ccparam_apsucetapaprod', function (Blueprint $table) {
            $table->dropColumn('at_campo');
        });
    }
}
