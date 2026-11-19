<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Forma de embalar fijada en el detalle de la OT.
 *
 * Complementa cliente_producto.formaembalar: alli esta lo acordado con el
 * cliente para ese producto (lo que vale a futuro), y aqui queda congelado lo
 * que se definio para ESTA orden de trabajo en particular. La planilla del
 * area lee primero otdet y solo cae a cliente_producto si otdet esta vacio.
 *
 * Es ademas el unico origen posible en una OT libre (produccion para stock),
 * que no tiene cliente y por lo tanto no tiene fila en cliente_producto.
 *
 * Columna nueva, nullable, que nadie lee fuera del modulo de Etapa Externa:
 * ningun flujo existente de OT cambia con esto.
 */
class AddFormaembalarToOtdetTable extends Migration
{
    public function up()
    {
        Schema::table('otdet', function (Blueprint $table) {
            $table->string('formaembalar', 200)->nullable()->after('obs');
        });
    }

    public function down()
    {
        Schema::table('otdet', function (Blueprint $table) {
            $table->dropColumn('formaembalar');
        });
    }
}
