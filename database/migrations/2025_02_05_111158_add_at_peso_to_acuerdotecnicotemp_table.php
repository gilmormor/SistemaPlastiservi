<?php

use App\Models\AcuerdoTecnicoTemp;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddAtPesoToAcuerdotecnicotempTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acuerdotecnicotemp', function (Blueprint $table) {
            $table->double('at_peso', 15, 10)->comment('Peso unitario')->nullable()->after('at_formatofilm');
            $table->integer('at_cantxunimed')->comment('Cantidad por unidad de medida, Ejemplo: 100 bolsas por paquete.')->default(1)->after('at_peso');
        });
        $sql = "SELECT *,0.0000000000 at_peso
        FROM acuerdotecnicotemp
        WHERE isnull(acuerdotecnicotemp.deleted_at)";
        $ats = DB::select($sql);
        foreach ($ats as &$at1) {
            //dd($at->at_ancho);
            $at = AcuerdoTecnicoTemp::findOrFail($at1->id);
            $aux_pesounit = pesounitat($at);
            $at1->at_peso = $aux_pesounit;
            $at->at_peso = $aux_pesounit;
            $at->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acuerdotecnicotemp', function (Blueprint $table) {
            $table->dropColumn('at_peso');
            $table->dropColumn('at_cantxunimed');
        });
    }
}
