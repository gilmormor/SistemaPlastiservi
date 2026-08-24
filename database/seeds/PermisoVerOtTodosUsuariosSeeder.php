<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Crea el permiso propio 'ver-ot-de-todos-los-usuarios' y lo asigna.
 *
 * Motivo: las consultas de OT (Ot::reportot y Ot::reportOtItem) filtraban por
 * 'ver-facturas-de-todos-los-usuarios'. Quien no lo tuviera solo veia las OT que
 * el mismo habia creado, y para darle visibilidad de todas las OT habia que
 * darle tambien acceso a las facturas de todos los usuarios. Se separan ambas
 * cosas: las OT pasan a gobernarse por su propio permiso.
 *
 * Para NO cambiar el comportamiento de nadie, el permiso nuevo se asigna a los
 * mismos roles que hoy tienen el de facturas (se resuelven dinamicamente, porque
 * los ids de rol difieren entre ambientes) y ademas al rol 'Val AT', que es el
 * que se pidio habilitar. El permiso de facturas queda intacto.
 *
 * Ejecutar: php artisan db:seed --class=PermisoVerOtTodosUsuariosSeeder --force
 *
 * Idempotente: verifica existencia por slug en permiso y por rol_id+permiso_id
 * en permiso_rol, asi que puede correrse varias veces sin duplicar.
 */
class PermisoVerOtTodosUsuariosSeeder extends Seeder
{
    /** Permiso nuevo que gobierna la visibilidad de OT de otros usuarios */
    const SLUG_OT_TODOS = 'ver-ot-de-todos-los-usuarios';

    /** Permiso historico del que se hereda la lista de roles */
    const SLUG_FACTURAS_TODOS = 'ver-facturas-de-todos-los-usuarios';

    /** Rol que se pidio habilitar; se busca por nombre porque el id varia por ambiente */
    const ROL_VAL_AT = 'Val AT';

    /**
     * Permisos de entrada a las pantallas de OT que se le habilitan a Val AT.
     * Sin estos, can() redirige al inicio antes de llegar a la consulta, por lo
     * que darle 'ver-ot-de-todos-los-usuarios' no tendria ningun efecto visible.
     *
     * OJO con 'listar-aprobar-ot': OtAprobarController solo valida permiso en
     * index(), no en aprobar()/rechazar(), asi que este permiso habilita de hecho
     * la aprobacion y el rechazo de OT, no solo la consulta.
     */
    private $pantallasValAt = [
        'listar-reporte-ot',           // reportot
        'listar-envia-item-ot-prog',   // otitemenvprogprod
        'listar-aprobar-ot',           // otaprobar (ver nota arriba)
        'listar-programacion-item-ot', // otitemprogramacion
    ];

    public function run()
    {
        DB::transaction(function () {
            $permisoOtId = $this->crearPermisoOt();
            $this->asignarPermisoOtARoles($permisoOtId);
            $this->habilitarPantallasOtAValAt();
        });

        $this->command->info('Permiso ver-ot-de-todos-los-usuarios creado y asignado correctamente.');
    }

    /**
     * Crea el permiso si no existe y devuelve su id (sea nuevo o preexistente).
     */
    private function crearPermisoOt()
    {
        $permiso = DB::table('permiso')->where('slug', self::SLUG_OT_TODOS)->first();
        if ($permiso) {
            $this->command->line('  permiso ' . self::SLUG_OT_TODOS . ' ya existia (id=' . $permiso->id . ')');
            return $permiso->id;
        }

        $id = DB::table('permiso')->insertGetId([
            'nombre'     => 'Ver OT de todos los usuarios',
            'slug'       => self::SLUG_OT_TODOS,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $this->command->line('  permiso ' . self::SLUG_OT_TODOS . ' creado (id=' . $id . ')');
        return $id;
    }

    /**
     * Asigna el permiso nuevo a los roles que ya veian todas las OT (los que
     * tienen el permiso de facturas) mas el rol Val AT.
     */
    private function asignarPermisoOtARoles($permisoOtId)
    {
        $permisoFacturas = DB::table('permiso')->where('slug', self::SLUG_FACTURAS_TODOS)->first();

        $rolIds = [];
        if ($permisoFacturas) {
            // Heredar exactamente los roles que hoy ven todas las OT, para que
            // el cambio de permiso en el modelo no le quite visibilidad a nadie.
            $rolIds = DB::table('permiso_rol')
                ->where('permiso_id', $permisoFacturas->id)
                ->pluck('rol_id')
                ->toArray();
        } else {
            $this->command->warn('  ATENCION: no existe ' . self::SLUG_FACTURAS_TODOS . ', no se heredaron roles');
        }

        $rolValAt = DB::table('rol')->where('nombre', self::ROL_VAL_AT)->first();
        if ($rolValAt) {
            $rolIds[] = $rolValAt->id;
        } else {
            $this->command->warn('  ATENCION: no existe el rol "' . self::ROL_VAL_AT . '" en esta base');
        }

        $agregados = 0;
        foreach (array_unique($rolIds) as $rolId) {
            $existe = DB::table('permiso_rol')
                ->where('rol_id', $rolId)
                ->where('permiso_id', $permisoOtId)
                ->exists();
            if ($existe) {
                continue;
            }
            DB::table('permiso_rol')->insert([
                'rol_id'     => $rolId,
                'permiso_id' => $permisoOtId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $agregados++;
        }
        $this->command->line('  roles con ' . self::SLUG_OT_TODOS . ': ' . count(array_unique($rolIds)) . ' (' . $agregados . ' nuevos)');
    }

    /**
     * Habilita a Val AT la entrada a las pantallas de OT listadas en
     * $pantallasValAt. Sin estos permisos el controlador lo redirige al inicio
     * antes siquiera de ejecutar la consulta.
     */
    private function habilitarPantallasOtAValAt()
    {
        $rolValAt = DB::table('rol')->where('nombre', self::ROL_VAL_AT)->first();
        if (!$rolValAt) {
            $this->command->warn('  no se asignaron permisos de pantalla (no existe el rol ' . self::ROL_VAL_AT . ')');
            return;
        }

        foreach ($this->pantallasValAt as $slug) {
            $permiso = DB::table('permiso')->where('slug', $slug)->first();
            if (!$permiso) {
                $this->command->warn('  ' . $slug . ': el permiso no existe en esta base, se omite');
                continue;
            }

            $existe = DB::table('permiso_rol')
                ->where('rol_id', $rolValAt->id)
                ->where('permiso_id', $permiso->id)
                ->exists();
            if ($existe) {
                $this->command->line('  rol ' . self::ROL_VAL_AT . ' ya tenia ' . $slug);
                continue;
            }

            DB::table('permiso_rol')->insert([
                'rol_id'     => $rolValAt->id,
                'permiso_id' => $permiso->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $this->command->line('  rol ' . self::ROL_VAL_AT . ': ' . $slug . ' asignado');
        }
    }
}
