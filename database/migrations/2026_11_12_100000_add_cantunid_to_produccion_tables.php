<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddCantunidToProduccionTables extends Migration
{
    /**
     * Permite contar en unidades lo que una etapa entrega en kilos.
     *
     * Hay productos que se venden en kilos y otros en unidades. Cuando una
     * etapa entrega en kilos, cant y cantkg del movimiento de inventario
     * quedan con el mismo valor y no hay donde anotar cuantas unidades habia
     * en esos kilos, que es un dato que igual interesa.
     *
     * invmovdet ya recibio cantunid en una migracion anterior. Aqui se agrega
     * el resto del camino:
     *
     *   acuerdotecnicoapsucetapaprod.unidadmedidacant_id
     *       Configuracion: en que unidad hay que contar ADEMAS de la cantidad,
     *       por producto y etapa, al lado de la unidad de salida que ya existe.
     *       En NULL -como queda en todas las filas actuales- no se pide nada,
     *       que es el comportamiento de hoy.
     *
     *   opdetregprodtemp.cantunid / cantunid_unidadmedida_id
     *   opdetregprod.cantunid     / cantunid_unidadmedida_id
     *       Captura: lo que el operario registra y lo que queda aprobado. De
     *       aca sale el valor que el aprobador lleva a invmovdet.
     *
     * Ninguna de estas columnas mueve stock: las existencias las siguen
     * manejando cant y cantkg, que son las que leen los triggers.
     *
     * @return void
     */
    public function up()
    {
        if (!$this->existeColumna('acuerdotecnicoapsucetapaprod', 'unidadmedidacant_id')) {
            DB::statement("ALTER TABLE `acuerdotecnicoapsucetapaprod`
                ADD COLUMN `unidadmedidacant_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL
                    COMMENT 'Unidad en que se cuenta ademas de la cantidad de salida; NULL = no se pide'
                    AFTER `unidadmedidasal_id`");

            DB::statement("ALTER TABLE `acuerdotecnicoapsucetapaprod`
                ADD CONSTRAINT `atapsucetapaprod_unidadmedidacant_id_foreign`
                FOREIGN KEY (`unidadmedidacant_id`) REFERENCES `unidadmedida` (`id`)");
        }

        foreach (['opdetregprodtemp', 'opdetregprod'] as $aux_tabla) {
            if (!$this->existeColumna($aux_tabla, 'cantunid')) {
                DB::statement("ALTER TABLE `{$aux_tabla}`
                    ADD COLUMN `cantunid` DOUBLE(18,2) NULL DEFAULT NULL
                        COMMENT 'Unidades que representan los kilos producidos (informativo)'
                        AFTER `cantprod`");
            }

            if (!$this->existeColumna($aux_tabla, 'cantunid_unidadmedida_id')) {
                DB::statement("ALTER TABLE `{$aux_tabla}`
                    ADD COLUMN `cantunid_unidadmedida_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL
                        COMMENT 'Unidad en que se cuenta cantunid'
                        AFTER `cantunid`");

                DB::statement("ALTER TABLE `{$aux_tabla}`
                    ADD CONSTRAINT `{$aux_tabla}_cantunid_unidadmedida_id_foreign`
                    FOREIGN KEY (`cantunid_unidadmedida_id`) REFERENCES `unidadmedida` (`id`)");
            }
        }
    }

    /**
     * @return void
     */
    public function down()
    {
        foreach (['opdetregprodtemp', 'opdetregprod'] as $aux_tabla) {
            if ($this->existeColumna($aux_tabla, 'cantunid_unidadmedida_id')) {
                DB::statement("ALTER TABLE `{$aux_tabla}`
                    DROP FOREIGN KEY `{$aux_tabla}_cantunid_unidadmedida_id_foreign`");
                DB::statement("ALTER TABLE `{$aux_tabla}` DROP COLUMN `cantunid_unidadmedida_id`");
            }
            if ($this->existeColumna($aux_tabla, 'cantunid')) {
                DB::statement("ALTER TABLE `{$aux_tabla}` DROP COLUMN `cantunid`");
            }
        }

        if ($this->existeColumna('acuerdotecnicoapsucetapaprod', 'unidadmedidacant_id')) {
            DB::statement("ALTER TABLE `acuerdotecnicoapsucetapaprod`
                DROP FOREIGN KEY `atapsucetapaprod_unidadmedidacant_id_foreign`");
            DB::statement("ALTER TABLE `acuerdotecnicoapsucetapaprod`
                DROP COLUMN `unidadmedidacant_id`");
        }
    }

    /**
     * @param  string  $aux_tabla
     * @param  string  $aux_columna
     * @return bool
     */
    private function existeColumna($aux_tabla, $aux_columna)
    {
        $aux_filas = DB::select(
            "SELECT COLUMN_NAME FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME   = ?
               AND COLUMN_NAME  = ?",
            [$aux_tabla, $aux_columna]
        );

        return count($aux_filas) > 0;
    }
}
