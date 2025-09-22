<?php

use App\Models\AcuerdoTecnico;
use App\Models\AcuerdotecnicoAPEtapaprod;
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
            ['nombre' => 'Programación', 'desc' => 'Planificación de la producción', 'orden' => 1],
            ['nombre' => 'Mezclas', 'desc' => 'Selección y preparación de materias primas', 'orden' => 2],
            ['nombre' => 'Mezclado', 'desc' => 'Proceso de mezclado de materiales', 'orden' => 3],
            ['nombre' => 'Extrusión', 'desc' => 'Extrusión de materiales plásticos', 'orden' => 4],
            ['nombre' => 'Impresión', 'desc' => 'Impresión sobre film o material', 'orden' => 5],
            ['nombre' => 'Sellado', 'desc' => 'Sellado final del producto', 'orden' => 6],
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

                DB::table('areaproduccionetapaprod')->insert([
                    'areaproduccion_id' => 5,
                    'etapaprod_id' => $etapa_id,
                    'orden' => $etapa['orden'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
        /** ------------------------------------------------------------------
        * 2) LLENAR acuerdotecnicoapetapaprod POR ACUERDO TÉCNICO
        * -----------------------------------------------------------------*/
        $acuerdotecnicos = AcuerdoTecnico::orderBy('id')->get();
        foreach ($acuerdotecnicos as $acuerdotecnico) {
            $aux_obsestusion = null;
            AcuerdotecnicoAPEtapaprod::create(
                [
                    "acuerdotecnico_id" => $acuerdotecnico->id,
                    "apetapaprod_id" => 1, // Asignar la etapa de produccion por defecto Programacion
                ]
            );
            AcuerdotecnicoAPEtapaprod::create(
                [
                    "acuerdotecnico_id" => $acuerdotecnico->id,
                    "apetapaprod_id" => 2, // Asignar la etapa de produccion Mezclas
                ]
            );
            AcuerdotecnicoAPEtapaprod::create(
                [
                    "acuerdotecnico_id" => $acuerdotecnico->id,
                    "apetapaprod_id" => 3, // Asignar la etapa de produccion Mezclado
                ]
            );
            AcuerdotecnicoAPEtapaprod::create(
                [
                    "acuerdotecnico_id" => $acuerdotecnico->id,
                    "apetapaprod_id" => 4, // Asignar la etapa de produccion Extrusion
                    "obs" => $acuerdotecnico->at_materiaprimaobs
                ]
            );
            if($acuerdotecnico->at_impreso == 1){
                AcuerdotecnicoAPEtapaprod::create(
                    [
                        "acuerdotecnico_id" => $acuerdotecnico->id,
                        "apetapaprod_id" => 5, // Asignar la etapa de produccion Impresion
                        "obs" => $acuerdotecnico->at_impresoobs
                    ]
                );
            }
            $aux_clanom = $acuerdotecnico->claseprod->cla_nombre;
            $at_tiposelloobs = $acuerdotecnico->claseprod->at_tiposelloobs;
            if(($aux_clanom != "Sin Sello" and $aux_clanom != "Sin Manga") or ($at_tiposelloobs != null and $at_tiposelloobs != "")){
                AcuerdotecnicoAPEtapaprod::create(
                    [
                        "acuerdotecnico_id" => $acuerdotecnico->id,
                        "apetapaprod_id" => 6, // Asignar la etapa de produccion Impresion
                        "obs" => $acuerdotecnico->at_tiposelloobs
                    ]
                );
            }

        }
    }
}
