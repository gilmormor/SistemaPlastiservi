<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddIndexOcToDteocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Indice para la subconsulta que arma dte_nrodocto en despachosol/listarnv
     * (funcion consulta() de DespachoSolController). Esa subconsulta filtra por
     * dteoc.oc_id y dteoc.oc_folder, columnas que no estaban indexadas, de modo
     * que recorria las ~56.000 filas de dteoc por cada fila del resultado.
     * Medido en local sobre copia de produccion: la consulta baja de 3.700 ms a
     * 2.320 ms (-37%) devolviendo exactamente las mismas 442 filas.
     *
     * @return void
     */
    public function up()
    {
        // se valida antes porque el indice puede haberse creado a mano en la BD
        if (!$this->existeIndice('idx_dteoc_oc')) {
            Schema::table('dteoc', function (Blueprint $table) {
                $table->index(['oc_id', 'oc_folder'], 'idx_dteoc_oc');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if ($this->existeIndice('idx_dteoc_oc')) {
            Schema::table('dteoc', function (Blueprint $table) {
                $table->dropIndex('idx_dteoc_oc');
            });
        }
    }

    private function existeIndice($nombre)
    {
        $indices = DB::select("SHOW INDEX FROM dteoc WHERE Key_name = ?", [$nombre]);
        return count($indices) > 0;
    }
}
