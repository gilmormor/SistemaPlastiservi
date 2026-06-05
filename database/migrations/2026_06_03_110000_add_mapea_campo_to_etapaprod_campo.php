<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega mapea_campo a etapaprod_campo.
 *
 * Cuando está definido, el valor del campo adicional se copia automáticamente
 * al campo estándar de opdetregprodtemp al guardar el registro.
 *
 * Ejemplo: total_unidades → mapea_campo = 'cantprod'
 * Así el cálculo cantidad_sacos × unidades_por_saco alimenta opdetregprodtemp.cantprod
 * sin que el operario lo ingrese manualmente.
 *
 * Valores permitidos: cantprod, kgprod, kgscrap (campos numéricos de opdetregprodtemp).
 */
class AddMapeaCampoToEtapaprodCampo extends Migration
{
    public function up()
    {
        Schema::table('etapaprod_campo', function (Blueprint $table) {
            $table->string('mapea_campo', 50)->nullable()->after('orden')
                  ->comment('Campo estándar de opdetregprodtemp que recibe este valor al guardar');
        });
    }

    public function down()
    {
        Schema::table('etapaprod_campo', function (Blueprint $table) {
            $table->dropColumn('mapea_campo');
        });
    }
}
