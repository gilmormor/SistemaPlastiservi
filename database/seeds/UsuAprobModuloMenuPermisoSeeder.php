<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Crea permisos y menu de la nueva pantalla "Restriccion Aprobacion por Modulo"
 * (usuaprobmodulo), y los vincula al rol Administrador. Generado 2026-08-06.
 *
 * Ejecutar: php artisan db:seed --class=UsuAprobModuloMenuPermisoSeeder --force
 *
 * Idempotente: cada insercion verifica existencia antes (por slug en permiso,
 * por nombre+url en menu, por rol_id+permiso_id/menu_id en las tablas pivote).
 */
class UsuAprobModuloMenuPermisoSeeder extends Seeder
{
    /** @var int Rol Administrador */
    const ROL_ADMIN = 1;

    public function run()
    {
        DB::transaction(function () {
            $this->seedPermisos();
            $this->seedMenu();
            $this->vincularAdministrador();
        });

        $this->command->info('Restriccion Aprobacion por Modulo: permisos, menu y vinculos con Administrador poblados correctamente.');
    }

    private function insertarSiNoExistePermiso($nombre, $slug)
    {
        $existe = DB::table('permiso')->where('slug', $slug)->exists();
        if ($existe) {
            return;
        }
        DB::table('permiso')->insert([
            'nombre' => $nombre,
            'slug' => $slug,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    private function seedPermisos()
    {
        $permisos = [
            ['Listar Usu Aprob Modulo', 'listar-usuaprobmodulo'],
            ['Crear Usu Aprob Modulo', 'crear-usuaprobmodulo'],
            ['Editar Usu Aprob Modulo', 'editar-usuaprobmodulo'],
            ['Guardar Usu Aprob Modulo', 'guardar-usuaprobmodulo'],
            ['Eliminar Usu Aprob Modulo', 'eliminar-usuaprobmodulo'],
        ];

        foreach ($permisos as [$nombre, $slug]) {
            $this->insertarSiNoExistePermiso($nombre, $slug);
        }
    }

    private function insertarSiNoExisteMenu($menu_id, $nombre, $url, $orden, $icono)
    {
        $existente = DB::table('menu')->where('nombre', $nombre)->where('url', $url)->first();
        if ($existente) {
            return $existente->id;
        }
        return DB::table('menu')->insertGetId([
            'menu_id' => $menu_id,
            'nombre' => $nombre,
            'url' => $url,
            'orden' => $orden,
            'icono' => $icono,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    private function seedMenu()
    {
        // Menu padre "Admin" (menu_id=0, url='#'), ya existe en produccion.
        $idAdmin = DB::table('menu')->where('nombre', 'Admin')->where('menu_id', 0)->value('id');
        if (!$idAdmin) {
            $this->command->error('No se encontro el menu padre "Admin" (menu_id=0). No se creo el submenu.');
            return;
        }
        $this->insertarSiNoExisteMenu($idAdmin, 'Restricción Aprobación por Módulo', 'usuaprobmodulo', 99, 'fa fa-user-times');
    }

    private function vincularAdministrador()
    {
        $slugsPermisos = [
            'listar-usuaprobmodulo', 'crear-usuaprobmodulo', 'editar-usuaprobmodulo',
            'guardar-usuaprobmodulo', 'eliminar-usuaprobmodulo',
        ];

        foreach ($slugsPermisos as $slug) {
            $permiso = DB::table('permiso')->where('slug', $slug)->first();
            if (!$permiso) {
                continue;
            }
            $existe = DB::table('permiso_rol')
                ->where('rol_id', self::ROL_ADMIN)
                ->where('permiso_id', $permiso->id)
                ->exists();
            if (!$existe) {
                DB::table('permiso_rol')->insert([
                    'rol_id' => self::ROL_ADMIN,
                    'permiso_id' => $permiso->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        $menu = DB::table('menu')->where('nombre', 'Restricción Aprobación por Módulo')->where('url', 'usuaprobmodulo')->first();
        if ($menu) {
            $existe = DB::table('menu_rol')
                ->where('rol_id', self::ROL_ADMIN)
                ->where('menu_id', $menu->id)
                ->exists();
            if (!$existe) {
                DB::table('menu_rol')->insert([
                    'rol_id' => self::ROL_ADMIN,
                    'menu_id' => $menu->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
