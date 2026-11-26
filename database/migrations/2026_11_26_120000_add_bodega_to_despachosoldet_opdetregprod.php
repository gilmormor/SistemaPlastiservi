<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * El detalle por lote de una solicitud de despacho no guardaba de que bodega
 * salia cada lote. Como un item puede repartirse entre varias bodegas, eso
 * obligaba a deducirlo repartiendo en orden, y hacia imposible atribuir por
 * bodega la cantidad que NO viene de un lote (material cargado a mano en el
 * inventario, que no tiene lote de produccion).
 *
 * Esta migracion:
 *   1. Agrega invbodegaproducto_id, para saber de que bodega sale cada fila.
 *   2. Permite opdetregprod_id nulo, para poder guardar tambien la cantidad
 *      "sin lote" de cada bodega en vez de deducirla restando.
 *
 * Las filas que ya existen se rellenan con la bodega del propio item cuando
 * tiene una sola (que es el caso de todas hoy); si tuviera varias se deja en
 * nulo y el codigo sigue repartiendo en orden, como hasta ahora.
 */
class AddBodegaToDespachosoldetOpdetregprod extends Migration
{
    public function up()
    {
        Schema::table('despachosoldet_opdetregprod', function (Blueprint $table) {
            $table->unsignedBigInteger('invbodegaproducto_id')->nullable()->after('despachosoldet_id');
            $table->foreign('invbodegaproducto_id')->references('id')->on('invbodegaproducto');
        });

        /* opdetregprod_id pasa a admitir nulo: la fila "sin lote" no tiene lote
           que declarar. La clave foranea sigue vigente y MySQL no la aplica a
           los nulos. */
        DB::statement('ALTER TABLE despachosoldet_opdetregprod MODIFY opdetregprod_id BIGINT UNSIGNED NULL');

        /* Relleno: la bodega del item, solo cuando el item tiene una sola. Con
           varias no se puede saber de cual salio cada lote y se deja nulo. */
        DB::statement("
            UPDATE despachosoldet_opdetregprod dsop
            JOIN (
                SELECT b.despachosoldet_id, MIN(b.invbodegaproducto_id) AS invbodegaproducto_id
                FROM   despachosoldet_invbodegaproducto b
                GROUP  BY b.despachosoldet_id
                HAVING COUNT(DISTINCT b.invbodegaproducto_id) = 1
            ) uni ON uni.despachosoldet_id = dsop.despachosoldet_id
            SET dsop.invbodegaproducto_id = uni.invbodegaproducto_id
            WHERE dsop.invbodegaproducto_id IS NULL
        ");
    }

    public function down()
    {
        /* Antes de volver a NOT NULL hay que sacar las filas "sin lote": en el
           esquema anterior no tienen donde vivir. */
        DB::table('despachosoldet_opdetregprod')->whereNull('opdetregprod_id')->delete();

        Schema::table('despachosoldet_opdetregprod', function (Blueprint $table) {
            $table->dropForeign(['invbodegaproducto_id']);
            $table->dropColumn('invbodegaproducto_id');
        });

        DB::statement('ALTER TABLE despachosoldet_opdetregprod MODIFY opdetregprod_id BIGINT UNSIGNED NOT NULL');
    }
}
