<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class DtetransChoferPatenteNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * Permite guardar dtetrans sin chofer ni patente.
     *
     * La Resolucion Ex. SII N°144, resolutivo 15°, no exige los datos del
     * transporte cuando el comprador retira los bienes en el establecimiento del
     * vendedor, caso que corresponde al tipo de despacho 1. El Formato DTE 2.5 lo
     * confirma en los campos Patente, PatenteCarro y RUTTrans: "Relevante si
     * Indicador Tipo de Despacho 2 o 3".
     *
     * El resto de la fila sigue siendo obligatorio y por eso no se toca: las
     * fechas de salida y llegada y la direccion y comuna de destino estan marcadas
     * como de impresion obligatoria y su condicion es "Obligatorio para documentos
     * que acompañan traslado de bienes", sin depender del tipo de despacho. Una
     * guia con retiro del cliente ampara un traslado, de modo que esos datos se
     * siguen registrando.
     *
     * Se usa ALTER TABLE en vez de ->change() para no depender de como doctrine/dbal
     * reinterpreta el tipo y el collation de la columna.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `dtetrans`
            MODIFY `rutchofer` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NULL,
            MODIFY `nombrechofer` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NULL,
            MODIFY `patente` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NULL");
    }

    /**
     * Reverse the migrations.
     *
     * Devuelve las tres columnas a NOT NULL. Si ya existen filas con nulos -guias
     * emitidas con retiro del cliente- el motor rechazaria el cambio, de modo que
     * primero se rellenan con cadena vacia: es la unica forma de revertir sin
     * perder la fila completa, y deja el dato igual de ausente que antes.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dtetrans')->whereNull('rutchofer')->update(['rutchofer' => '']);
        DB::table('dtetrans')->whereNull('nombrechofer')->update(['nombrechofer' => '']);
        DB::table('dtetrans')->whereNull('patente')->update(['patente' => '']);

        DB::statement("ALTER TABLE `dtetrans`
            MODIFY `rutchofer` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
            MODIFY `nombrechofer` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL,
            MODIFY `patente` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL");
    }
}
