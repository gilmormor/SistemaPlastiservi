<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddIndexAnnomesFechahoraToInvmovTable extends Migration
{
    /**
     * Run the migrations.
     *
     * invmov solo tenia indice en su PK y en las claves foraneas, de modo que
     * los reportes que filtran por periodo -consultainvmov() de
     * ReportInvMovController, InvControlController y el modelo InvMov- no
     * tenian por donde entrar. Sin un camino barato hacia el filtro, el
     * optimizador descartaba empezar por invmov: arrancaba por las tablas
     * chicas (sucursal, invbodega, invmovmodulo, categoriaprod...) y dejaba
     * invmov para el final, expandiendo millones de combinaciones antes de
     * aplicar el filtro del mes.
     *
     * Medido en local sobre copia de produccion, reporte de un mes
     * (4.824 movimientos, 18.308 detalles de 230.442 y 709.823 totales):
     * la consulta pasa de 169 s a 11 s. El indice por si solo no alcanza:
     * va junto con el STRAIGHT_JOIN de consultainvmov(), que obliga a
     * respetar el orden escrito y a entrar por invmov.
     *
     * Ojo al desplegar: crear el indice tardo 77 s sobre las 230.442 filas
     * de invmov y bloquea la tabla mientras tanto, asi que conviene correrlo
     * fuera del horario de trabajo.
     *
     * @return void
     */
    public function up()
    {
        // se valida antes porque el indice puede haberse creado a mano en la BD
        if (!$this->existeIndice('idx_invmov_annomes')) {
            Schema::table('invmov', function (Blueprint $table) {
                $table->index('annomes', 'idx_invmov_annomes');
            });
        }
        // los mismos reportes filtran por rango de fechas cuando se informan
        if (!$this->existeIndice('idx_invmov_fechahora')) {
            Schema::table('invmov', function (Blueprint $table) {
                $table->index('fechahora', 'idx_invmov_fechahora');
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
        if ($this->existeIndice('idx_invmov_fechahora')) {
            Schema::table('invmov', function (Blueprint $table) {
                $table->dropIndex('idx_invmov_fechahora');
            });
        }
        if ($this->existeIndice('idx_invmov_annomes')) {
            Schema::table('invmov', function (Blueprint $table) {
                $table->dropIndex('idx_invmov_annomes');
            });
        }
    }

    private function existeIndice($nombre)
    {
        $indices = DB::select("SHOW INDEX FROM invmov WHERE Key_name = ?", [$nombre]);
        return count($indices) > 0;
    }
}
