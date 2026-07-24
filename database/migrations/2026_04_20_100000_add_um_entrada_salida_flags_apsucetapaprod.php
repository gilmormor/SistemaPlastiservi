<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fase 0 — Plan producción:
 * Agrega flags de configuración por etapa y prepara el terreno para
 * MP, CC y obligatoriedad de kg.
 *
 * - requiere_kg   : si la etapa debe capturar kg producidos (default true)
 * - requiere_cc   : si la etapa requiere paso por Control de Calidad
 * - usa_matprima  : si la etapa consume materia prima (habilita grid de MP)
 *
 * Nota: `unidadmedida_id` existente se sigue usando como UM de SALIDA de la
 * etapa. La UM de ENTRADA se deduce en tiempo de ejecución a partir de la
 * UM de salida de la etapa anterior (por `orden` dentro del mismo
 * areaproduccionsuc_id).
 */
class AddUmEntradaSalidaFlagsApsucetapaprod extends Migration
{
    public function up()
    {
        Schema::table('areaproduccionsucetapaprod', function (Blueprint $table) {
            $table->boolean('requiere_kg')->default(1)->after('unidadmedida_id');
            $table->boolean('requiere_cc')->default(0)->after('requiere_kg');
            $table->boolean('usa_matprima')->default(0)->after('requiere_cc');
        });
    }

    public function down()
    {
        Schema::table('areaproduccionsucetapaprod', function (Blueprint $table) {
            $table->dropColumn(['requiere_kg', 'requiere_cc', 'usa_matprima']);
        });
    }
}
