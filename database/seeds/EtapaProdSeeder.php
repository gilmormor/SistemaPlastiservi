<?php

use App\Models\AcuerdoTecnico;
use App\Models\AcuerdotecnicoAPSucEtapaprod;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EtapaProdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usuario_id = 1; // Ajusta si corresponde a otro usuario

        $etapas = [
            ['nombre' => 'Mezclas', 'desc' => 'Selección y preparación de materias primas', 'orden' => 1],
            ['nombre' => 'Mezclado', 'desc' => 'Proceso de mezclado de materiales', 'orden' => 2],
            ['nombre' => 'Extrusión', 'desc' => 'Extrusión de materiales plásticos', 'orden' => 3],
            ['nombre' => 'Impresión', 'desc' => 'Impresión sobre film o material', 'orden' => 4],
            ['nombre' => 'Sellado', 'desc' => 'Sellado final del producto', 'orden' => 5],
        ];

        foreach ($etapas as $etapa) {
            // Verifica si ya existe una etapa con el mismo nombre (opcional pero recomendable)
            $existe = DB::table('etapaprod')->where('nombre', $etapa['nombre'])->first();
            if (!$existe) {
                $etapa_id = DB::table('etapaprod')->insertGetId([
                    'nombre' => $etapa['nombre'],
                    'desc' => $etapa['desc'],
                    'usuario_id' => $usuario_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                DB::table('areaproduccionsucetapaprod')->insert([
                    'areaproduccionsuc_id' => 4,
                    'etapaprod_id' => $etapa_id,
                    'orden' => $etapa['orden'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
        /** ------------------------------------------------------------------
        * 2) LLENAR acuerdotecnicoapsucetapaprod POR ACUERDO TÉCNICO
        * -----------------------------------------------------------------*/
        $acuerdotecnicos = AcuerdoTecnico::orderBy('id')->get();
        foreach ($acuerdotecnicos as $acuerdotecnico) {
            $aux_obsestusion = null;
            AcuerdotecnicoAPSucEtapaprod::create(
                [
                    "acuerdotecnico_id" => $acuerdotecnico->id,
                    "apsucetapaprod_id" => 1, // Asignar la etapa de produccion Mezclas
                ]
            );
            AcuerdotecnicoAPSucEtapaprod::create(
                [
                    "acuerdotecnico_id" => $acuerdotecnico->id,
                    "apsucetapaprod_id" => 2, // Asignar la etapa de produccion Mezclado
                ]
            );
            AcuerdotecnicoAPSucEtapaprod::create(
                [
                    "acuerdotecnico_id" => $acuerdotecnico->id,
                    "apsucetapaprod_id" => 3, // Asignar la etapa de produccion Extrusion
                    "obs" => $acuerdotecnico->at_materiaprimaobs
                ]
            );
            if($acuerdotecnico->at_impreso == 1){
                AcuerdotecnicoAPSucEtapaprod::create(
                    [
                        "acuerdotecnico_id" => $acuerdotecnico->id,
                        "apsucetapaprod_id" => 4, // Asignar la etapa de produccion Impresion
                        "obs" => $acuerdotecnico->at_impresoobs
                    ]
                );
            }
            $aux_clanom = $acuerdotecnico->claseprod->cla_nombre;
            $at_tiposelloobs = $acuerdotecnico->claseprod->at_tiposelloobs;
            if(($aux_clanom != "Sin Sello" and $aux_clanom != "Sin Manga") or ($at_tiposelloobs != null and $at_tiposelloobs != "")){
                AcuerdotecnicoAPSucEtapaprod::create(
                    [
                        "acuerdotecnico_id" => $acuerdotecnico->id,
                        "apsucetapaprod_id" => 5, // Asignar la etapa de produccion Impresion
                        "obs" => $acuerdotecnico->at_tiposelloobs
                    ]
                );
            }

        }
    }
}
