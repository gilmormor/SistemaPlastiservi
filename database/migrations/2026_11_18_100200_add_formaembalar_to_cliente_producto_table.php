<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Forma de embalar acordada con el cliente para un producto.
 *
 * Es el "Detalle de Embalaje" de la planilla del area de sellado (ej: "BULTOS
 * DE 4,00"). Se define una vez por cliente+producto y queda para todas las
 * producciones siguientes; solo cambia si se acuerda otra forma de embalar
 * para ese cliente y ese producto en particular.
 *
 * Columna nueva sobre una tabla que hoy es solo pivote (cliente_id,
 * producto_id). Nace nullable y nadie la lee todavia fuera del modulo de
 * Etapa Externa, asi que ningun flujo existente cambia.
 *
 * La captura en los formularios de OT y OTNV va en una entrega aparte.
 */
class AddFormaembalarToClienteProductoTable extends Migration
{
    public function up()
    {
        Schema::table('cliente_producto', function (Blueprint $table) {
            $table->string('formaembalar', 200)->nullable()->after('producto_id');
        });
    }

    public function down()
    {
        Schema::table('cliente_producto', function (Blueprint $table) {
            $table->dropColumn('formaembalar');
        });
    }
}
