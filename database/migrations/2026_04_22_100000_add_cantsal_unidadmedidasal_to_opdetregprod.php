<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Agrega a opdetregprodtemp y opdetregprod los campos:
 *   - cantsal             : cantidad en UM de SALIDA (unidades completas cerradas en esta etapa)
 *   - unidadmedidasal_id  : UM de salida (copia de la UM configurada en la etapa al momento del registro)
 *
 * Motivacion: el campo `cant` conflaba entrada y salida. Ahora:
 *   - cant            = cantidad en UM de ENTRADA (kg procesados, metros, etc)
 *   - cantsal         = cantidad en UM de SALIDA (rollos, bolsas, etc) — puede ser 0 si no se cerro unidad
 *   - unidadmedida_id = UM de entrada (se mantiene como ya estaba)
 *
 * Backfill: para registros historicos, cantsal = cant y unidadmedidasal_id = unidadmedida_id
 * (asunto: historicamente siempre cerraban unidad y la UM registrada era la de salida).
 */
class AddCantsalUnidadmedidasalToOpdetregprod extends Migration
{
    public function up()
    {
        Schema::table('opdetregprodtemp', function (Blueprint $t) {
            $t->double('cantsal', 10, 2)->nullable()->after('cant');
            $t->unsignedBigInteger('unidadmedidasal_id')->nullable()->after('cantsal');
            $t->foreign('unidadmedidasal_id')->references('id')->on('unidadmedida');
        });

        Schema::table('opdetregprod', function (Blueprint $t) {
            $t->double('cantsal', 10, 2)->nullable()->after('cant');
            $t->unsignedBigInteger('unidadmedidasal_id')->nullable()->after('cantsal');
            $t->foreign('unidadmedidasal_id')->references('id')->on('unidadmedida');
        });

        // Backfill
        DB::statement("UPDATE opdetregprodtemp SET cantsal = cant, unidadmedidasal_id = unidadmedida_id WHERE cantsal IS NULL");
        DB::statement("UPDATE opdetregprod    SET cantsal = cant, unidadmedidasal_id = unidadmedida_id WHERE cantsal IS NULL");
    }

    public function down()
    {
        Schema::table('opdetregprodtemp', function (Blueprint $t) {
            $t->dropForeign(['unidadmedidasal_id']);
            $t->dropColumn(['cantsal', 'unidadmedidasal_id']);
        });
        Schema::table('opdetregprod', function (Blueprint $t) {
            $t->dropForeign(['unidadmedidasal_id']);
            $t->dropColumn(['cantsal', 'unidadmedidasal_id']);
        });
    }
}
