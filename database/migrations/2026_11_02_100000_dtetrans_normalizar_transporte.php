<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class DtetransNormalizarTransporte extends Migration
{
    /**
     * Normaliza los datos de transporte de dtetrans.
     *
     * Hasta ahora la tabla guardaba el texto -ruttrans, rutchofer, nombrechofer,
     * patente, patentecarro y cmnadest- aunque esos mismos datos ya viven en los
     * maestros transemp, transchofer, transveh y comuna, que DteTransService crea
     * con firstOrCreate al emitir. Quedaba el dato duplicado y sin vinculo.
     *
     * Se reemplazan por las claves foraneas transemp_id, transchofer_id,
     * transveh_id y transvehcarro_id. El nombre de la comuna no necesita columna
     * nueva: ya esta comuna_id, de donde el XML tambien deriva <CiudadDest>.
     *
     * El orden importa: primero se agregan las columnas, luego se rellenan
     * cruzando los textos con los maestros, despues se comprueba que no haya
     * quedado ninguna fila con texto pero sin vinculo, y solo entonces se
     * eliminan los campos. Si algo no cuadra la migracion se detiene antes de
     * borrar, de modo que no se pierde informacion.
     */
    public function up()
    {
        DB::statement("ALTER TABLE `dtetrans`
            ADD COLUMN `transemp_id`      BIGINT(20) UNSIGNED NULL AFTER `cmnadest`,
            ADD COLUMN `transchofer_id`   BIGINT(20) UNSIGNED NULL AFTER `transemp_id`,
            ADD COLUMN `transveh_id`      BIGINT(20) UNSIGNED NULL AFTER `transchofer_id`,
            ADD COLUMN `transvehcarro_id` BIGINT(20) UNSIGNED NULL AFTER `transveh_id`");

        /* El cruce es por los mismos valores con que DteTransService crea los
           maestros: el RUT ya limpio y la patente en mayusculas. */
        DB::statement("UPDATE `dtetrans` t
            LEFT JOIN `transemp`    e  ON e.`rut`     = t.`ruttrans`
            LEFT JOIN `transchofer` c  ON c.`rut`     = t.`rutchofer`
            LEFT JOIN `transveh`    v  ON v.`patente` = t.`patente`
            LEFT JOIN `transveh`    vc ON vc.`patente` = t.`patentecarro`
            SET t.`transemp_id`      = e.`id`,
                t.`transchofer_id`   = c.`id`,
                t.`transveh_id`      = v.`id`,
                t.`transvehcarro_id` = vc.`id`");

        /* Control: ninguna fila puede quedar con el texto informado y el vinculo
           en nulo. Si ocurre, falta el registro en el maestro y hay que revisarlo
           antes de borrar nada. */
        $aux_huerfanas = DB::table('dtetrans')
            ->where(function ($q) {
                $q->where(function ($s) { $s->whereNotNull('ruttrans')->where('ruttrans', '<>', '')->whereNull('transemp_id'); })
                  ->orWhere(function ($s) { $s->whereNotNull('rutchofer')->where('rutchofer', '<>', '')->whereNull('transchofer_id'); })
                  ->orWhere(function ($s) { $s->whereNotNull('patente')->where('patente', '<>', '')->whereNull('transveh_id'); })
                  ->orWhere(function ($s) { $s->whereNotNull('patentecarro')->where('patentecarro', '<>', '')->whereNull('transvehcarro_id'); });
            })
            ->count();

        if ($aux_huerfanas > 0) {
            throw new \Exception(
                "No se eliminan los campos de texto: hay $aux_huerfanas fila(s) de dtetrans " .
                "con datos de transporte que no tienen registro en transemp, transchofer o " .
                "transveh. Revisar esas filas y crear los maestros antes de volver a migrar."
            );
        }

        DB::statement("ALTER TABLE `dtetrans`
            ADD CONSTRAINT `dtetrans_transemp_id_foreign`      FOREIGN KEY (`transemp_id`)      REFERENCES `transemp` (`id`),
            ADD CONSTRAINT `dtetrans_transchofer_id_foreign`   FOREIGN KEY (`transchofer_id`)   REFERENCES `transchofer` (`id`),
            ADD CONSTRAINT `dtetrans_transveh_id_foreign`      FOREIGN KEY (`transveh_id`)      REFERENCES `transveh` (`id`),
            ADD CONSTRAINT `dtetrans_transvehcarro_id_foreign` FOREIGN KEY (`transvehcarro_id`) REFERENCES `transveh` (`id`)");

        DB::statement("ALTER TABLE `dtetrans`
            DROP COLUMN `ruttrans`,
            DROP COLUMN `rutchofer`,
            DROP COLUMN `nombrechofer`,
            DROP COLUMN `patente`,
            DROP COLUMN `patentecarro`,
            DROP COLUMN `cmnadest`");
    }

    /**
     * Devuelve los campos de texto y los rellena desde los maestros, de modo que
     * al revertir no se pierda informacion.
     */
    public function down()
    {
        DB::statement("ALTER TABLE `dtetrans`
            ADD COLUMN `cmnadest`     VARCHAR(255) NULL AFTER `comuna_id`,
            ADD COLUMN `ruttrans`     VARCHAR(255) NULL AFTER `cmnadest`,
            ADD COLUMN `rutchofer`    VARCHAR(255) NULL AFTER `ruttrans`,
            ADD COLUMN `nombrechofer` VARCHAR(255) NULL AFTER `rutchofer`,
            ADD COLUMN `patente`      VARCHAR(255) NULL AFTER `nombrechofer`,
            ADD COLUMN `patentecarro` VARCHAR(255) NULL AFTER `patente`");

        DB::statement("UPDATE `dtetrans` t
            LEFT JOIN `comuna`      co ON co.`id` = t.`comuna_id`
            LEFT JOIN `transemp`    e  ON e.`id`  = t.`transemp_id`
            LEFT JOIN `transchofer` c  ON c.`id`  = t.`transchofer_id`
            LEFT JOIN `transveh`    v  ON v.`id`  = t.`transveh_id`
            LEFT JOIN `transveh`    vc ON vc.`id` = t.`transvehcarro_id`
            SET t.`cmnadest`     = co.`nombre`,
                t.`ruttrans`     = e.`rut`,
                t.`rutchofer`    = c.`rut`,
                t.`nombrechofer` = c.`nombre`,
                t.`patente`      = v.`patente`,
                t.`patentecarro` = vc.`patente`");

        DB::statement("ALTER TABLE `dtetrans` MODIFY `cmnadest` VARCHAR(255) NOT NULL");

        DB::statement("ALTER TABLE `dtetrans`
            DROP FOREIGN KEY `dtetrans_transemp_id_foreign`,
            DROP FOREIGN KEY `dtetrans_transchofer_id_foreign`,
            DROP FOREIGN KEY `dtetrans_transveh_id_foreign`,
            DROP FOREIGN KEY `dtetrans_transvehcarro_id_foreign`");

        DB::statement("ALTER TABLE `dtetrans`
            DROP COLUMN `transemp_id`,
            DROP COLUMN `transchofer_id`,
            DROP COLUMN `transveh_id`,
            DROP COLUMN `transvehcarro_id`");
    }
}
