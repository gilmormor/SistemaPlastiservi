<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddUsaProduccionToUnidadmedidaTable extends Migration
{
    /**
     * Marca que unidades de medida sirven como salida de una etapa de produccion.
     *
     * La tabla mezcla dos cosas distintas: unidades que cuentan cantidad -Unidades,
     * Metros, Kilos, Rollos, Cajas- y unidades que miden el producto -Centimetros,
     * Micras, Pulgadas, Milimetros-. Las segundas no tienen sentido como unidad de
     * inventario: de hecho Pulgadas llego a quedar grabada como unidad de un
     * movimiento, porque categoriaprod.unidadmedida_id la usa para indicar en que
     * unidad esta el diametro del producto.
     *
     * Con esta marca, el combo de unidad de salida por etapa ofrece solo las que
     * corresponden, y se puede ajustar desde la pantalla de unidades de medida sin
     * tocar codigo.
     *
     * Se deja en 1 lo que cuenta cantidad y en 0 lo dimensional. Contenedor queda
     * en 1 porque cuenta bultos, aunque hoy no se use en produccion.
     *
     * @return void
     */
    public function up()
    {
        if (!$this->existeColumna('usa_produccion')) {
            DB::statement("ALTER TABLE `unidadmedida`
                ADD COLUMN `usa_produccion` TINYINT(1) NOT NULL DEFAULT 0
                    COMMENT 'Sirve como unidad de salida de una etapa de produccion'
                    AFTER `agrupado`");
        }

        /* Valores iniciales por nombre y no por id, para no depender de que los
           ids sean los mismos en todos los ambientes. */
        DB::table('unidadmedida')
            ->whereIn('nombre', ['Unid', 'Mts', 'Kgs', 'Pqts', 'Rollos', 'Cajas', 'Contenedor'])
            ->update(['usa_produccion' => 1]);

        DB::table('unidadmedida')
            ->whereIn('nombre', ['Cm.', 'Mc.', 'Pulgadas', 'Milimetros'])
            ->update(['usa_produccion' => 0]);
    }

    /**
     * @return void
     */
    public function down()
    {
        if ($this->existeColumna('usa_produccion')) {
            DB::statement("ALTER TABLE `unidadmedida` DROP COLUMN `usa_produccion`");
        }
    }

    /**
     * @param  string  $aux_nombre
     * @return bool
     */
    private function existeColumna($aux_nombre)
    {
        $aux_filas = DB::select(
            "SELECT COLUMN_NAME FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'unidadmedida'
               AND COLUMN_NAME = ?",
            [$aux_nombre]
        );

        return count($aux_filas) > 0;
    }
}
