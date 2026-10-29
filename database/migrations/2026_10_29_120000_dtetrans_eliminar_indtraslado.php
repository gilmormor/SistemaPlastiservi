<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class DtetransEliminarIndtraslado extends Migration
{
    /**
     * Elimina dtetrans.indtraslado, una columna que nunca se llena ni se lee.
     *
     * El indicador de traslado del documento vive en dte.indtraslado, que es de
     * donde lo toma el XML: el Formato DTE 2.5 ubica <IndTraslado> en el bloque
     * <IdDoc> del encabezado, junto al folio y la fecha de emision, y no dentro
     * de <Transporte>, de modo que no es un dato del transporte.
     *
     * Al momento de esta migracion las 93 filas de dtetrans tenian la columna en
     * null, mientras que sus DTE si tenian el valor en dte.indtraslado. Se quita
     * para que nadie la tome por la fuente de verdad y la lea por error.
     */
    public function up()
    {
        DB::statement("ALTER TABLE `dtetrans` DROP COLUMN `indtraslado`");
    }

    /**
     * La devuelve tal como estaba: tinyint unsigned, nullable y sin datos, que
     * es el estado en que se encontraba.
     */
    public function down()
    {
        DB::statement("ALTER TABLE `dtetrans` ADD COLUMN `indtraslado` TINYINT(3) UNSIGNED NULL AFTER `patentecarro`");
    }
}
