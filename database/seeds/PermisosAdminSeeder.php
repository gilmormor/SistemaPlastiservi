<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Crea los permisos de las pantallas de administración de seguridad y se los
 * asigna al rol Administrador.
 *
 * Los controladores Admin\RolController, Admin\MenuRolController,
 * Admin\PermisoController y Admin\PermisoRolController pasaron a validar permiso
 * con can(), pero esos permisos nunca se crearon en la tabla `permiso`.
 *
 * OJO — sin este seeder esas cuatro pantallas quedan inaccesibles para todos,
 * incluido el Administrador: can() solo hace bypass cuando el nombre del rol en
 * sesión es exactamente 'administrador' en minúscula, y el rol 1 se llama
 * 'Administrador'. Como son justamente las pantallas donde se administran roles y
 * permisos, el bloqueo no se puede resolver desde la interfaz: hay que correr esto.
 *
 * Ejecutar: php artisan db:seed --class=PermisosAdminSeeder --force
 * (si da "Target class does not exist", correr antes `composer dump-autoload -o`)
 *
 * Idempotente: verifica por slug en `permiso` y por rol_id+permiso_id en
 * `permiso_rol`, así que puede correrse varias veces sin duplicar.
 */
class PermisosAdminSeeder extends Seeder
{
    /** Rol Administrador, id confirmado igual en desarrollo y producción */
    const ROL_ADMIN = 1;

    /**
     * Permisos a crear, por controlador. El nombre se deriva del slug: cada palabra
     * separada por guiones se capitaliza (listar-admin-rol => "Listar Admin Rol").
     *
     * `guardar-admin-rol` y `guardar-admin-permiso` aparecen dos veces en cada
     * controlador (en guardar() y en actualizar()), pero son un solo permiso.
     */
    private $permisos = [
        // Admin\RolController
        'listar-admin-rol',
        'crear-admin-rol',
        'guardar-admin-rol',
        'editar-admin-rol',
        'eliminar-admin-rol',
        // Admin\MenuRolController
        'listar-admin-menu-rol',
        'guardar-admin-menu-rol',
        // Admin\PermisoController
        'listar-admin-permiso',
        'crear-admin-permiso',
        'guardar-admin-permiso',
        'editar-admin-permiso',
        'eliminar-admin-permiso',
        // Admin\PermisoRolController
        'listar-admin-permiso-rol',
        'guardar-admin-permiso-rol',
    ];

    public function run()
    {
        $creados = 0;
        $vinculados = 0;

        DB::transaction(function () use (&$creados, &$vinculados) {
            foreach ($this->permisos as $slug) {
                $permiso = DB::table('permiso')->where('slug', $slug)->first();

                if (!$permiso) {
                    $id = DB::table('permiso')->insertGetId([
                        'nombre'     => $this->nombreDesdeSlug($slug),
                        'slug'       => $slug,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                    $creados++;
                } else {
                    $id = $permiso->id;
                }

                $existe = DB::table('permiso_rol')
                    ->where('rol_id', self::ROL_ADMIN)
                    ->where('permiso_id', $id)
                    ->exists();

                if (!$existe) {
                    DB::table('permiso_rol')->insert([
                        'rol_id'     => self::ROL_ADMIN,
                        'permiso_id' => $id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                    $vinculados++;
                }
            }
        });

        $this->command->line('  permisos creados: ' . $creados . ' de ' . count($this->permisos));
        $this->command->line('  vinculados al rol ' . self::ROL_ADMIN . ': ' . $vinculados);

        // Verificación: los 14 deben existir y estar vinculados al rol Administrador.
        $faltan = 0;
        foreach ($this->permisos as $slug) {
            $p = DB::table('permiso')->where('slug', $slug)->first();
            if (!$p || !DB::table('permiso_rol')->where('rol_id', self::ROL_ADMIN)->where('permiso_id', $p->id)->exists()) {
                $this->command->warn('  SIN ACCESO: ' . $slug);
                $faltan++;
            }
        }

        if ($faltan === 0) {
            $this->command->info('Permisos de administración creados y asignados al rol Administrador.');
        } else {
            $this->command->error('Quedaron ' . $faltan . ' permisos sin asignar. Revisar.');
        }
    }

    /**
     * listar-admin-menu-rol => "Listar Admin Menu Rol"
     */
    private function nombreDesdeSlug($slug)
    {
        return implode(' ', array_map('ucfirst', explode('-', $slug)));
    }
}
