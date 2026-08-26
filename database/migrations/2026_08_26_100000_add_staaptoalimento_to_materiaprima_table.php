<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Indica si una materia prima es apta para contacto con alimentos.
 *
 * Pedido por Control de Calidad: la etiqueta de cada lote debe declarar esta
 * condición. El dato es de la materia prima, no del producto, y lo carga CC una
 * sola vez por material; el vendedor no lo elige ni lo ve al armar el acuerdo
 * técnico, se arrastra solo desde la materia prima que seleccionó.
 *
 * Nullable a propósito, con tres estados:
 *   1     APTO PARA CONTACTO CON ALIMENTOS
 *   0     NO APTO PARA CONTACTO CON ALIMENTOS
 *   null  SIN DEFINIR  (aún no clasificada por CC)
 *
 * Sin el tercer estado, las materias primas ya cargadas arrancarían afirmando
 * "no apto" sin que nadie lo haya evaluado, y eso se imprime en una etiqueta que
 * llega al cliente. El null deja claro que todavía no se determinó.
 */
class AddStaaptoalimentoToMateriaprimaTable extends Migration
{
    public function up()
    {
        Schema::table('materiaprima', function (Blueprint $table) {
            $table->tinyInteger('staaptoalimento')->nullable()->after('pe');
        });
    }

    public function down()
    {
        Schema::table('materiaprima', function (Blueprint $table) {
            $table->dropColumn('staaptoalimento');
        });
    }
}
