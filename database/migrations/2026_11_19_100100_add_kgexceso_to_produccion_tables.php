<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kg de este registro que NO tienen respaldo en los lotes de origen.
 *
 * Van incluidos en kgprod: si el operario produjo 472,86 kg y el lote solo
 * tenia 452,86, kgprod es 472,86 -esos kilos existen y pasan completos a la
 * etapa siguiente- y kgexceso guarda los 20 que salieron del lote que ya
 * estaba en el deposito (ver la migracion de maquina.kgexceso_permitido).
 *
 * No resta nada: es la nota al margen que permite saber despues por que a otro
 * lote le faltan kilos, y en que maquina y con que operario paso.
 *
 * El registro definitivo se crea copiando el temp con toArray(), asi que basta
 * con que la columna exista en las dos tablas y en el fillable del modelo.
 */
class AddKgexcesoToProduccionTables extends Migration
{
    public function up()
    {
        Schema::table('opdetregprodtemp', function (Blueprint $table) {
            $table->double('kgexceso', 18, 2)->default(0)->after('kgscrap');
        });
        Schema::table('opdetregprod', function (Blueprint $table) {
            $table->double('kgexceso', 18, 2)->default(0)->after('kgscrap');
        });
    }

    public function down()
    {
        Schema::table('opdetregprodtemp', function (Blueprint $table) {
            $table->dropColumn('kgexceso');
        });
        Schema::table('opdetregprod', function (Blueprint $table) {
            $table->dropColumn('kgexceso');
        });
    }
}
