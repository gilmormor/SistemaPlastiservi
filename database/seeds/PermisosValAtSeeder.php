<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Habilita al rol "Val AT" la asignación de etapas de producción a un producto,
 * tanto desde Programación como desde la pantalla de AT Etapas Producción.
 *
 * Editar las etapas de un producto necesita TRES permisos encadenados, no uno:
 * uno dibuja el botón, otro deja cargar el modal y otro deja guardar. Faltando
 * el del medio, el botón aparece pero el modal responde 403 y en pantalla se ve
 * "Error al cargar las etapas. Intente nuevamente.", que no dice que sea un
 * problema de permisos.
 *
 * Ejecutar: php artisan db:seed --class=PermisosValAtSeeder --force
 * (si da "Target class does not exist", correr antes `composer dump-autoload -o`)
 *
 * Idempotente: verifica por rol_id+permiso_id y rol_id+menu_id antes de insertar.
 * El rol se busca por nombre porque su id difiere entre ambientes; los permisos y
 * el menú ya existen, este seeder solo los vincula.
 */
class PermisosValAtSeeder extends Seeder
{
    /** Se busca por nombre: el id del rol varía entre ambientes */
    const ROL = 'Val AT';

    /**
     * Los tres permisos del flujo de edición de etapas, más el de listar la
     * pantalla propia de AT Etapas Producción.
     */
    private $permisos = [
        // otitemprogramacion: decide si se dibuja el botón "+ Editar etapas"
        'editar-etapa-de-produccion',
        // acuerdotecnicoetapaprod: listado de la pantalla propia
        'listar-acuerdo-tecnico-etapa-de-produccion',
        // modalData() — carga las etapas en el modal. Sin este, "Error al cargar las etapas"
        'editar-acuerdo-tecnico-etapa-de-produccion',
        // actualizarAjax() — guarda la selección de etapas
        'guardar-acuerdo-tecnico-etapa-de-produccion',
    ];

    /** Menús que debe ver el rol, por nombre + url */
    private $menus = [
        ['AT Etapas Produccion', 'acuerdotecnicoetapaprod'],
    ];

    public function run()
    {
        $rol = DB::table('rol')->where('nombre', self::ROL)->first();
        if (!$rol) {
            $this->command->error('No existe el rol "' . self::ROL . '" en esta base. No se hizo nada.');
            return;
        }
        $this->command->line('  rol ' . self::ROL . ' encontrado (id=' . $rol->id . ')');

        DB::transaction(function () use ($rol) {
            $this->asignarPermisos($rol->id);
            $this->asignarMenus($rol->id);
        });

        $this->verificar($rol->id);
    }

    private function asignarPermisos($rol_id)
    {
        $nuevos = 0;
        foreach ($this->permisos as $slug) {
            $permiso = DB::table('permiso')->where('slug', $slug)->first();
            if (!$permiso) {
                $this->command->warn('  ' . $slug . ': el permiso no existe en esta base, se omite');
                continue;
            }
            $existe = DB::table('permiso_rol')
                ->where('rol_id', $rol_id)
                ->where('permiso_id', $permiso->id)
                ->exists();
            if ($existe) {
                $this->command->line('  ya tenia ' . $slug);
                continue;
            }
            DB::table('permiso_rol')->insert([
                'rol_id'     => $rol_id,
                'permiso_id' => $permiso->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $this->command->line('  asignado ' . $slug);
            $nuevos++;
        }
        $this->command->line('  permisos nuevos: ' . $nuevos);
    }

    private function asignarMenus($rol_id)
    {
        foreach ($this->menus as [$nombre, $url]) {
            $menu = DB::table('menu')->where('nombre', $nombre)->where('url', $url)
                ->whereNull('deleted_at')->first();
            if (!$menu) {
                $this->command->warn('  menú "' . $nombre . '" no existe en esta base, se omite');
                continue;
            }
            $existe = DB::table('menu_rol')
                ->where('rol_id', $rol_id)
                ->where('menu_id', $menu->id)
                ->exists();
            if ($existe) {
                $this->command->line('  ya veia el menú ' . $nombre);
                continue;
            }
            DB::table('menu_rol')->insert([
                'rol_id'     => $rol_id,
                'menu_id'    => $menu->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $this->command->line('  menú asignado: ' . $nombre);
        }
    }

    /**
     * Los tres permisos del flujo tienen que estar los tres: con dos de tres, el
     * botón aparece pero falla al usarlo, que es peor que no verlo.
     */
    private function verificar($rol_id)
    {
        $faltan = [];
        foreach ($this->permisos as $slug) {
            $p = DB::table('permiso')->where('slug', $slug)->first();
            if (!$p || !DB::table('permiso_rol')->where('rol_id', $rol_id)->where('permiso_id', $p->id)->exists()) {
                $faltan[] = $slug;
            }
        }

        if (empty($faltan)) {
            $this->command->info('Rol ' . self::ROL . ' habilitado para asignar etapas de producción.');
            return;
        }

        $this->command->error('Quedaron sin asignar: ' . implode(', ', $faltan));
        $this->command->error('Con permisos incompletos el botón aparece pero da error al abrirlo.');
    }
}
