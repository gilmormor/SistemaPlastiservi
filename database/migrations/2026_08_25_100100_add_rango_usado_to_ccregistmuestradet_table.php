<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Congela en cada detalle de muestra el rango contra el que se validó.
 *
 * Cuando el rango sale del acuerdo técnico, no basta con guardar el valor medido:
 * si más adelante alguien corrige el AT del producto, una muestra tomada hoy
 * debe seguir mostrando contra qué se midió cuando se midió. Mismo criterio que
 * usa una factura al congelar el precio.
 *
 * Quedan en null para los parámetros de rango fijo, que siguen leyendo
 * valor_min / valor_max de la configuración de la etapa.
 */
class AddRangoUsadoToCcregistmuestradetTable extends Migration
{
    public function up()
    {
        Schema::table('ccregistmuestradet', function (Blueprint $table) {
            $table->decimal('valor_objetivo', 12, 4)->nullable()->after('valor');
            $table->decimal('tolerancia', 12, 4)->nullable()->after('valor_objetivo');
            $table->decimal('rango_min', 12, 4)->nullable()->after('tolerancia');
            $table->decimal('rango_max', 12, 4)->nullable()->after('rango_min');
        });
    }

    public function down()
    {
        Schema::table('ccregistmuestradet', function (Blueprint $table) {
            $table->dropColumn(['valor_objetivo', 'tolerancia', 'rango_min', 'rango_max']);
        });
    }
}
