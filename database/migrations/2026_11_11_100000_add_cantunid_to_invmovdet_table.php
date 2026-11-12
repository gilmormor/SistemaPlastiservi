<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddCantunidToInvmovdetTable extends Migration
{
    /**
     * Guarda cuantas unidades representan los kilos de un movimiento de inventario.
     *
     * invmovdet ya tiene dos cantidades: cant -en unidadmedida_id- y cantkg -en
     * kilos-. Cuando el producto se mueve en kilos las dos quedan con el mismo
     * valor y no hay donde anotar cuantas unidades habia en esos kilos: hoy pasa
     * en 235.913 de las 716.313 filas vigentes. Para los productos que se venden
     * en kilos igual interesa saber cuantas unidades salieron, asi que se agrega
     * una cantidad informativa con su propia unidad.
     *
     * No reemplaza a cant ni a cantkg: el stock lo siguen moviendo esas dos, que
     * son las que leen los triggers vtrg_stock_after_insert/update/delete.
     *
     * cantunid_unidadmedida_id dice EN QUE se cuenta -unidades, rollos, bultos-,
     * porque una cantidad suelta no se puede interpretar despues.
     *
     * Las filas existentes quedan en NULL a proposito: esas unidades nunca se
     * contaron y no hay de donde deducirlas. El dato se empieza a llenar desde
     * los movimientos nuevos.
     *
     * Nota de despliegue: la tabla pesa unos 235 MB entre datos e indices, asi
     * que el ALTER tarda decenas de segundos. Conviene correrlo fuera del
     * horario de trabajo aunque MySQL permita leer y escribir mientras tanto.
     *
     * @return void
     */
    public function up()
    {
        if (!$this->existeColumna('cantunid')) {
            DB::statement("ALTER TABLE `invmovdet`
                ADD COLUMN `cantunid` DOUBLE(18,2) NULL DEFAULT NULL
                    COMMENT 'Unidades que representan los kilos del movimiento (informativo)'
                    AFTER `cantkg`");
        }

        if (!$this->existeColumna('cantunid_unidadmedida_id')) {
            DB::statement("ALTER TABLE `invmovdet`
                ADD COLUMN `cantunid_unidadmedida_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL
                    COMMENT 'Unidad en que se cuenta cantunid'
                    AFTER `cantunid`");

            DB::statement("ALTER TABLE `invmovdet`
                ADD CONSTRAINT `invmovdet_cantunid_unidadmedida_id_foreign`
                FOREIGN KEY (`cantunid_unidadmedida_id`) REFERENCES `unidadmedida` (`id`)");
        }
    }

    /**
     * @return void
     */
    public function down()
    {
        if ($this->existeColumna('cantunid_unidadmedida_id')) {
            DB::statement("ALTER TABLE `invmovdet`
                DROP FOREIGN KEY `invmovdet_cantunid_unidadmedida_id_foreign`");
            DB::statement("ALTER TABLE `invmovdet` DROP COLUMN `cantunid_unidadmedida_id`");
        }

        if ($this->existeColumna('cantunid')) {
            DB::statement("ALTER TABLE `invmovdet` DROP COLUMN `cantunid`");
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
               AND TABLE_NAME = 'invmovdet'
               AND COLUMN_NAME = ?",
            [$aux_nombre]
        );

        return count($aux_filas) > 0;
    }
}
