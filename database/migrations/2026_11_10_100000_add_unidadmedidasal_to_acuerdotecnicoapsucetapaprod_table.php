<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddUnidadmedidasalToAcuerdotecnicoapsucetapaprodTable extends Migration
{
    /**
     * Unidad de medida en que el producto sale de cada etapa de produccion.
     *
     * Hasta ahora la unidad salia de areaproduccionsucetapaprod, es decir de la
     * etapa, y era la misma para todos los productos. Pero depende del producto:
     * de Sellado un producto puede salir en metros y otro en rollos, aunque los
     * dos entren en kilos.
     *
     * La regla del modulo es que la bodega de cada etapa tiene una sola unidad:
     * en esa unidad ingresa lo que la etapa produce y en esa misma unidad se
     * descuenta cuando la etapa siguiente lo consume. Lo que ingresa a la bodega
     * de la etapa siguiente puede ser otra unidad, porque el producto se
     * transforma. Por eso la unidad de entrada no se configura: siempre es la de
     * salida de la etapa anterior.
     *
     * Como apsucetapaprod ya distingue la sucursal, la unidad puede variar entre
     * sucursales para el mismo producto sin cambios adicionales.
     *
     * La columna admite nulo porque las filas ya cargadas no la tienen. El modal
     * de asignacion de etapas la exige al guardar, y el registro de produccion
     * avisa cuando falta en vez de suponer una unidad.
     *
     * @return void
     */
    public function up()
    {
        if ($this->existeColumna('unidadmedidasal_id')) {
            return;
        }

        DB::statement("ALTER TABLE `acuerdotecnicoapsucetapaprod`
            ADD COLUMN `unidadmedidasal_id` BIGINT(20) UNSIGNED NULL
                COMMENT 'Unidad en que el producto sale de esta etapa'
                AFTER `apsucetapaprod_id`");

        DB::statement("ALTER TABLE `acuerdotecnicoapsucetapaprod`
            ADD CONSTRAINT `fk_acuerdotecnicoapsucetapaprod_unidadmedida`
            FOREIGN KEY (`unidadmedidasal_id`) REFERENCES `unidadmedida` (`id`)");
    }

    /**
     * @return void
     */
    public function down()
    {
        if (!$this->existeColumna('unidadmedidasal_id')) {
            return;
        }

        if ($this->existeClave('fk_acuerdotecnicoapsucetapaprod_unidadmedida')) {
            DB::statement("ALTER TABLE `acuerdotecnicoapsucetapaprod`
                DROP FOREIGN KEY `fk_acuerdotecnicoapsucetapaprod_unidadmedida`");
        }

        DB::statement("ALTER TABLE `acuerdotecnicoapsucetapaprod`
            DROP COLUMN `unidadmedidasal_id`");
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
               AND TABLE_NAME = 'acuerdotecnicoapsucetapaprod'
               AND COLUMN_NAME = ?",
            [$aux_nombre]
        );

        return count($aux_filas) > 0;
    }

    /**
     * @param  string  $aux_nombre
     * @return bool
     */
    private function existeClave($aux_nombre)
    {
        $aux_filas = DB::select(
            "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'acuerdotecnicoapsucetapaprod'
               AND CONSTRAINT_TYPE = 'FOREIGN KEY'
               AND CONSTRAINT_NAME = ?",
            [$aux_nombre]
        );

        return count($aux_filas) > 0;
    }
}
