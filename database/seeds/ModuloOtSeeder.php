<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Inserta en la tabla `modulo` las filas id=31 a 35, usadas por
 * clienteBloqueado() (app/Helpers/biblioteca.php) para filtrar desbloqueos
 * de cliente por notaventa_id. El codigo del Modulo Produccion (OtController,
 * OtNVController, OtAprobarController, OtItemEnvProgProdController,
 * OtItemProgramacionController) referencia estos ids de forma fija desde
 * hace tiempo, pero nunca se creo el script que insertara las filas —
 * causaba ModelNotFoundException (404) en varias rutas de OT/OTNV
 * (crearot, otaprobar/rechazar, etc.), detectado 2026-08-07.
 *
 * Ejecutar: php artisan db:seed --class=ModuloOtSeeder --force
 *
 * Idempotente: verifica existencia por id antes de insertar.
 */
class ModuloOtSeeder extends Seeder
{
    public function run()
    {
        $modulos = [
            ['id' => 31, 'nombre' => 'OT', 'desc' => 'Orden de Trabajo (OT libre, sin Nota de Venta)', 'stamodapl' => 1],
            ['id' => 32, 'nombre' => 'OT NV', 'desc' => 'Orden de Trabajo desde Nota de Venta', 'stamodapl' => 1],
            ['id' => 33, 'nombre' => 'OT Aprob', 'desc' => 'Aprobar/Rechazar Orden de Trabajo', 'stamodapl' => 1],
            ['id' => 34, 'nombre' => 'OT Envia Item Prog', 'desc' => 'Envia Item OT a Programacion', 'stamodapl' => 1],
            ['id' => 35, 'nombre' => 'OT Programacion Item', 'desc' => 'Programacion de Item OT', 'stamodapl' => 1],
        ];

        // Usuario "creador" para las filas: id=1, existe en todos los ambientes (usuario admin original).
        $usuarioId = DB::table('usuario')->orderBy('id')->value('id');

        foreach ($modulos as $modulo) {
            $existe = DB::table('modulo')->where('id', $modulo['id'])->exists();
            if ($existe) {
                continue;
            }
            DB::table('modulo')->insert([
                'id' => $modulo['id'],
                'nombre' => $modulo['nombre'],
                'desc' => $modulo['desc'],
                'stamodapl' => $modulo['stamodapl'],
                'usuario_id' => $usuarioId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        $this->command->info('Modulos OT (31 a 35): verificados/creados correctamente.');
    }
}
