<?php

use App\Models\AcuerdoTecnico;
use App\Models\NotaVentaDetalle;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddAtPesoToAcuerdotecnicoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acuerdotecnico', function (Blueprint $table) {
            $table->double('at_peso', 15, 10)->comment('Peso unitario')->nullable()->after('at_formatofilm');
            $table->integer('at_cantxunimed')->comment('Cantidad por unidad de medida, Ejemplo: 100 bolsas por paquete.')->default(1)->after('at_peso');
        });

        $sql = "SELECT *,0.0000000000 at_peso
        FROM acuerdotecnico
        WHERE isnull(acuerdotecnico.deleted_at)";
        $ats = DB::select($sql);
        foreach ($ats as &$at1) {
            //dd($at->at_ancho);
            $at = AcuerdoTecnico::findOrFail($at1->id);
            $aux_pesounit = pesounitat($at);
            $at1->at_peso = $aux_pesounit;
            $at->at_peso = $aux_pesounit;
            $at->save();
        }

        $sql = "SELECT id
        FROM notaventadetalle
        WHERE totalkilos = 0
        AND isnull(notaventadetalle.deleted_at)";
        $nvdets = DB::select($sql);
        foreach ($nvdets as $nvdet) {
            $nvdetr = NotaVentaDetalle::findOrFail($nvdet->id);
            if(isset($nvdetr->producto) and isset($nvdetr->producto->acuerdotecnico) and $nvdetr->producto->acuerdotecnico->at_peso > 0){
                $nvdetr->totalkilos = $nvdetr->cant * $nvdetr->producto->acuerdotecnico->at_peso;
                //dd($nvdetr->totalkilos);
                $nvdetr->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acuerdotecnico', function (Blueprint $table) {
            $table->dropColumn('at_peso');
            $table->dropColumn('at_cantxunimed');
        });
    }
}
