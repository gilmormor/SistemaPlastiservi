<?php

use App\Models\AcuerdoTecnico;
use App\Models\AcuerdotecnicoEtapaprod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAcuerdotecnicoetapaprod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acuerdotecnicoetapaprod', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('acuerdotecnico_id');
            $table->foreign('acuerdotecnico_id','fk_acuerdotecnicoetapaprod_acuerdotecnico')->references('id')->on('acuerdotecnico')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedBigInteger('etapaprod_id');
            $table->foreign('etapaprod_id','fk_acuerdotecnicoetapaprod_etapaprod')->references('id')->on('etapaprod')->onDelete('restrict')->onUpdate('restrict');
            $table->string('obs',300)->comment('Observacion')->nullable();
            $table->engine = 'InnoDB';
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
        });
        $acuerdotecnicos = AcuerdoTecnico::orderBy('id')->get();
        foreach ($acuerdotecnicos as $acuerdotecnico) {
            $aux_obsestusion = null;
            AcuerdotecnicoEtapaprod::create(
                [
                    "acuerdotecnico_id" => $acuerdotecnico->id,
                    "etapaprod_id" => 1, // Asignar la etapa de produccion por defecto Programacion
                ]
            );
            AcuerdotecnicoEtapaprod::create(
                [
                    "acuerdotecnico_id" => $acuerdotecnico->id,
                    "etapaprod_id" => 2, // Asignar la etapa de produccion Mezclas
                ]
            );
            AcuerdotecnicoEtapaprod::create(
                [
                    "acuerdotecnico_id" => $acuerdotecnico->id,
                    "etapaprod_id" => 3, // Asignar la etapa de produccion Mezclado
                ]
            );
            AcuerdotecnicoEtapaprod::create(
                [
                    "acuerdotecnico_id" => $acuerdotecnico->id,
                    "etapaprod_id" => 4, // Asignar la etapa de produccion Extrusion
                    "obs" => $acuerdotecnico->at_materiaprimaobs
                ]
            );
            if($acuerdotecnico->at_impreso == 1){
                AcuerdotecnicoEtapaprod::create(
                    [
                        "acuerdotecnico_id" => $acuerdotecnico->id,
                        "etapaprod_id" => 5, // Asignar la etapa de produccion Impresion
                        "obs" => $acuerdotecnico->at_impresoobs
                    ]
                );
            }
            $aux_clanom = $acuerdotecnico->claseprod->cla_nombre;
            $at_tiposelloobs = $acuerdotecnico->claseprod->at_tiposelloobs;
            if(($aux_clanom != "Sin Sello" and $aux_clanom != "Sin Manga") or ($at_tiposelloobs != null and $at_tiposelloobs != "")){
                AcuerdotecnicoEtapaprod::create(
                    [
                        "acuerdotecnico_id" => $acuerdotecnico->id,
                        "etapaprod_id" => 6, // Asignar la etapa de produccion Impresion
                        "obs" => $acuerdotecnico->at_tiposelloobs
                    ]
                );
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
        Schema::dropIfExists('acuerdotecnicoetapaprod');
    }
}
