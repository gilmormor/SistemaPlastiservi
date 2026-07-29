<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Puebla permiso, menu, permiso_rol y menu_rol con lo que falta del Módulo Producción,
 * comparando la BD de desarrollo `biblioteca` contra la de producción (generado 2026-07-27).
 * Reemplaza a los 3 .sql equivalentes en database/seeders/sql/ (idéntico contenido, en PHP).
 *
 * Ejecutar: php artisan db:seed --class=ModuloProduccionMenuPermisoSeeder --force
 *
 * Idempotente: cada inserción verifica existencia antes (por slug en permiso,
 * por nombre+url en menu, por rol_id+permiso_id/menu_id en las tablas pivote).
 * No incluye el rol "Produccion SE" (no existe en producción, se decidió no crearlo)
 * ni acuerdotecnicoapsucetapaprod (depende de IDs de acuerdotecnico que no coinciden
 * entre ambientes, requiere remapeo manual por producto_id si se hace en el futuro).
 */
class ModuloProduccionMenuPermisoSeeder extends Seeder
{
    /** @var int Rol Administrador, id confirmado igual en biblioteca y producción */
    const ROL_ADMIN = 1;

    public function run()
    {
        DB::transaction(function () {
            $this->seedPermisos();
            $this->seedMenus();
            $this->vincularAdministrador();
        });

        $this->command->info('Módulo Producción: permisos, menús y vínculos con Administrador poblados correctamente.');
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
            ['Listar OT', 'listar-ot'],
            ['Crear OT', 'crear-ot'],
            ['Editar OT', 'editar-ot'],
            ['Guardar OT', 'guardar-ot'],
            ['Eliminar OT', 'eliminar-ot'],
            ['Listar OTNV', 'listar-otnv'],
            ['Crear OTNV', 'crear-otnv'],
            ['Editar OTNV', 'editar-otnv'],
            ['Guardar OTNV', 'guardar-otnv'],
            ['Eliminar OTNV', 'eliminar-otnv'],
            ['Ver pdf ot', 'ver-pdf-ot'],
            ['Listar Reporte OT', 'listar-reporte-ot'],
            ['Listar Aprobar OT', 'listar-aprobar-ot'],
            ['Listar Envia Item OT Prog', 'listar-envia-item-ot-prog'],
            ['Listar Programacion Item OT', 'listar-programacion-item-ot'],
            ['Listar Maquina Grupo', 'listar-maquina-grupo'],
            ['Crear Maquina Grupo', 'crear-maquina-grupo'],
            ['Editar Maquina Grupo', 'editar-maquina-grupo'],
            ['Guardar Maquina Grupo', 'guardar-maquina-grupo'],
            ['Eliminar Maquina Grupo', 'eliminar-maquina-grupo'],
            ['Listar Maquina', 'listar-maquina'],
            ['Crear Maquina', 'crear-maquina'],
            ['Editar Maquina', 'editar-maquina'],
            ['Guardar Maquina', 'guardar-maquina'],
            ['Eliminar Maquina', 'eliminar-maquina'],
            ['Listar Atributo', 'listar-atributo'],
            ['Crear Atributo', 'crear-atributo'],
            ['Editar Atributo', 'editar-atributo'],
            ['Guardar Atributo', 'guardar-atributo'],
            ['Eliminar Atributo', 'eliminar-atributo'],
            ['Listar Operario', 'listar-operario'],
            ['Crear Operario', 'crear-operario'],
            ['Editar Operario', 'editar-operario'],
            ['Guardar Operario', 'guardar-operario'],
            ['Eliminar Operario', 'eliminar-operario'],
            ['Listar Area Produccion Suc Etapa Prod', 'listar-area-produccion-suc-etapa-prod'],
            ['Listar Etapa de Produccion', 'listar-etapa-de-produccion'],
            ['Crear Etapa de Produccion', 'crear-etapa-de-produccion'],
            ['Editar Etapa de Produccion', 'editar-etapa-de-produccion'],
            ['Guardar Etapa de Produccion', 'guardar-etapa-de-produccion'],
            ['Eliminar Etapa de Produccion', 'eliminar-etapa-de-produccion'],
            ['Editar Area Produccion Suc Fase', 'editar-area-produccion-suc-fase'],
            ['Editar Area Produccion Suc Etapa Prod', 'editar-area-produccion-suc-etapa-prod'],
            ['Guardar Area Produccion Suc Etapa Prod', 'guardar-area-produccion-suc-etapa-prod'],
            ['Listar Acuerdo Tecnico Etapa de Produccion', 'listar-acuerdo-tecnico-etapa-de-produccion'],
            ['Editar Acuerdo Tecnico Etapa de Produccion', 'editar-acuerdo-tecnico-etapa-de-produccion'],
            ['Guardar Acuerdo Tecnico Etapa de Produccion', 'guardar-acuerdo-tecnico-etapa-de-produccion'],
            ['Editar etapas productivas en Programar produccion', 'editar-etapas-productivas-en-programar-produccion'],
            ['Listar Registro Produccion', 'listar-registro-produccion'],
            ['Crear Registro Produccion', 'crear-registro-produccion'],
            ['Editar Registro Produccion', 'editar-registro-produccion'],
            ['Guardar Registro Produccion', 'guardar-registro-produccion'],
            ['Eliminar Registro Produccion', 'eliminar-registro-produccion'],
            ['Listar Persona EtapaProd', 'listar-persona-etapaprod'],
            ['Editar Persona EtapaProd', 'editar-persona-etapaprod'],
            ['Guardar Persona EtapaProd', 'guardar-persona-etapaprod'],
            ['Listar EtapasProd x Persona', 'listar-etapasprod-x-persona'],
            ['Listar invbodegatipo', 'listar-invbodegatipo'],
            ['Crear invbodegatipo', 'crear-invbodegatipo'],
            ['Editar invbodegatipo', 'editar-invbodegatipo'],
            ['Guardar invbodegatipo', 'guardar-invbodegatipo'],
            ['Eliminar invbodegatipo', 'eliminar-invbodegatipo'],
            ['Listar Registro Produccion Aprobar Supervidor', 'listar-registro-produccion-aprobar-supervidor'],
            ['Ver pdf OP', 'ver-pdf-op'],
            ['Cambiar estatus Requiere Fabricacion Nota de venta', 'cambiar-estatus-requiere-fabricacion-nota-de-venta'],
            ['Listar  ccparam', 'listar--ccparam'],
            ['Crear ccparam', 'crear-ccparam'],
            ['Editar ccparam', 'editar-ccparam'],
            ['Guardar ccparam', 'guardar-ccparam'],
            ['Eliminar ccparam', 'eliminar-ccparam'],
            ['Listar Parametros CC', 'listar-ccparam'],
            ['Actualizar Parametros CC', 'actualizar-ccparam'],
            ['Listar Param CC por Etapa', 'listar-ccparam-apsucetapaprod'],
            ['Guardar Param CC por Etapa', 'guardar-ccparam-apsucetapaprod'],
            ['Actualizar Param CC por Etapa', 'actualizar-ccparam-apsucetapaprod'],
            ['Eliminar Param CC por Etapa', 'eliminar-ccparam-apsucetapaprod'],
            ['Ver PDF Muestra CC', 'ver-pdf-muestra-cc'],
            ['Ver Etiqueta Registro Producción', 'ver-etiqueta-regprod'],
            ['Supervisar CC Muestras', 'supervisar-ccregistmuestra'],
            ['Listar CC Desbloqueo Muestras', 'listar-cc-muestra-desbloqueo'],
            ['Guardar CC Desbloqueo Muestras', 'guardar-cc-muestra-desbloqueo'],
            ['Listar Reporte CC Muestra', 'listar-reporte-cc-muestra'],
            ['Reporte CC por Lote', 'reporte-cc-lote'],
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

    private function seedMenus()
    {
        // Filas independientes: hijas de menús ya existentes (58 Clientes, 107 Inventario),
        // y una raíz nueva (Tipo Bodega, menu_id=0)
        $this->insertarSiNoExisteMenu(58, 'Bloquear Cliente', 'clientebloqueado', 3, 'fa-lock');
        $this->insertarSiNoExisteMenu(107, 'Inv Stock Vend', 'reportinvstockvend', 5, 'fa-map-o');
        $this->insertarSiNoExisteMenu(58, 'Desbloquear Cliente', 'clientedesbloqueado', 7, 'fa-unlock');
        $this->insertarSiNoExisteMenu(58, 'Desbloquear Cliente Pro', 'clientedesbloqueadopro', 8, 'fa-unlock text-blue');
        $this->insertarSiNoExisteMenu(0, 'Tipo Bodega', 'invbodegatipo', 1, 'fa-square-o');

        // Nivel 1: Produccion (padre=185, "New", ya existe igual en ambos ambientes)
        $idProduccion = $this->insertarSiNoExisteMenu(185, 'Produccion', '#', 3, 'fa-industry');

        // Nivel 2: hijos directos de Produccion
        $nivel2 = [
            ['Orden Trabajo NV', 'otnv', 7, 'fa-cog'],
            ['Programacion', 'otitemprogramacion', 12, 'fa-calendar'],
            ['Cargar Materia Prima', '#', 16, 'fa-dropbox'],
            ['Extrusión', '#', 14, 'fa-cogs'],
            ['Orden Trabajo', 'ot', 6, 'fa-cog text-aqua'],
            ['Orden Trabajo Rep', 'reportot', 8, 'fa-file-pdf-o'],
            ['Aprobar OT', 'otaprobar', 9, 'fa-thumbs-o-up'],
            ['Envia Item OT a Prog.', 'otitemenvprogprod', 10, 'fa-industry'],
            ['Operario', 'operario', 5, 'fa-odnoklassniki'],
            ['Etapa Prod x Area Produc', 'areaproduccionsucetapaprod', 2, 'fa-sort-amount-asc'],
            ['Etapas de Produccion', 'etapaprod', 1, 'fa-list-ol'],
            ['AT Etapas Produccion', 'acuerdotecnicoetapaprod', 3, 'fa-list-ol'],
            ['Persona Etapa Produccion', 'personaetapaprod', 4, 'fa-user'],
            ['Aprobar Registro Produccion', 'opdetregprodtempaprobsup', 14, 'fa-thumbs-o-up'],
            ['Trazabilidad OP', 'op/seguimiento', 15, 'fa-share-alt'],
        ];
        foreach ($nivel2 as [$nombre, $url, $orden, $icono]) {
            $this->insertarSiNoExisteMenu($idProduccion, $nombre, $url, $orden, $icono);
        }

        // Nivel 2 (padres intermedios, también hijos de Produccion)
        $idMaquinas = $this->insertarSiNoExisteMenu($idProduccion, 'Maquinas', '#', 11, 'fa-stack-exchange');
        $idRegProd = $this->insertarSiNoExisteMenu($idProduccion, 'Registro de Produccion', 'opdetregprodtemp', 13, 'fa-industry');
        $idCC = $this->insertarSiNoExisteMenu($idProduccion, 'Control de Calidad', '#', 17, 'fa-certificate');

        // Nivel 3: hijos de Maquinas
        $this->insertarSiNoExisteMenu($idMaquinas, 'Maquina Grupo', 'maquinagrupo', 1, 'fa-trello');
        $this->insertarSiNoExisteMenu($idMaquinas, 'Maquina', 'maquina', 2, 'fa-simplybuilt');
        $this->insertarSiNoExisteMenu($idMaquinas, 'Atributos', 'atributo', 3, 'fa-stack-overflow');

        // Nivel 3: hijos de Registro de Produccion
        $this->insertarSiNoExisteMenu($idRegProd, 'Mezclas', 'opdetregprodtemp/set/1', 1, 'fa-magic');
        $this->insertarSiNoExisteMenu($idRegProd, 'Mezclado', 'opdetregprodtemp/set/2', 2, 'fa-retweet');
        $this->insertarSiNoExisteMenu($idRegProd, 'Extrusion.', 'opdetregprodtemp/set/3', 3, 'fa-opera');
        $this->insertarSiNoExisteMenu($idRegProd, 'Impresion', 'opdetregprodtemp/set/4', 4, 'fa-print');
        $this->insertarSiNoExisteMenu($idRegProd, 'Sellado', 'opdetregprodtemp/set/5', 5, 'fa-flickr');

        // Nivel 3: hijos de Control de Calidad
        $this->insertarSiNoExisteMenu($idCC, '🔬 Reg Muestra', 'ccregistmuestra', 1, 'fa fa-flask');
        $this->insertarSiNoExisteMenu($idCC, 'Supervisar Muestra CC', 'ccmuestrasuper', 2, 'fa fa-check-square-o');
        $this->insertarSiNoExisteMenu($idCC, 'Desbloquear CC Muestra', 'ccdesbloqueo', 3, 'fa fa-unlock text-blue');
        $this->insertarSiNoExisteMenu($idCC, 'CC Muestra', 'reportccmuestra', 4, 'fa-file-pdf-o');
        $this->insertarSiNoExisteMenu($idCC, 'Cobertura CC', 'reportcoberturacc', 5, 'fa fa-shield');
    }

    /**
     * Vincula al rol Administrador los permisos y menús del Módulo Producción que en
     * biblioteca (origen) ya estaban vinculados a Administrador. Incluye tanto los que
     * insertó seedPermisos()/seedMenus() como algunos que ya existían en destino con
     * otro id (creados por una migración anterior sin vincularlos a ningún rol).
     */
    private function vincularAdministrador()
    {
        $slugsPermisos = [
            'listar-ot', 'crear-ot', 'editar-ot', 'guardar-ot', 'eliminar-ot',
            'listar-otnv', 'crear-otnv', 'editar-otnv', 'guardar-otnv', 'eliminar-otnv',
            'ver-pdf-ot', 'listar-reporte-ot', 'listar-aprobar-ot', 'listar-envia-item-ot-prog',
            'listar-programacion-item-ot', 'listar-maquina-grupo', 'crear-maquina-grupo',
            'editar-maquina-grupo', 'guardar-maquina-grupo', 'eliminar-maquina-grupo',
            'listar-maquina', 'crear-maquina', 'editar-maquina', 'guardar-maquina', 'eliminar-maquina',
            'listar-atributo', 'crear-atributo', 'editar-atributo', 'guardar-atributo', 'eliminar-atributo',
            'listar-operario', 'crear-operario', 'editar-operario', 'guardar-operario', 'eliminar-operario',
            'listar-area-produccion-suc-etapa-prod', 'listar-etapa-de-produccion', 'crear-etapa-de-produccion',
            'editar-etapa-de-produccion', 'guardar-etapa-de-produccion', 'eliminar-etapa-de-produccion',
            'editar-area-produccion-suc-fase', 'editar-area-produccion-suc-etapa-prod',
            'guardar-area-produccion-suc-etapa-prod', 'listar-acuerdo-tecnico-etapa-de-produccion',
            'editar-acuerdo-tecnico-etapa-de-produccion', 'guardar-acuerdo-tecnico-etapa-de-produccion',
            'editar-etapas-productivas-en-programar-produccion', 'listar-registro-produccion',
            'crear-registro-produccion', 'editar-registro-produccion', 'guardar-registro-produccion',
            'eliminar-registro-produccion', 'listar-persona-etapaprod', 'editar-persona-etapaprod',
            'guardar-persona-etapaprod', 'listar-etapasprod-x-persona', 'listar-invbodegatipo',
            'crear-invbodegatipo', 'editar-invbodegatipo', 'guardar-invbodegatipo', 'eliminar-invbodegatipo',
            'listar-registro-produccion-aprobar-supervidor', 'ver-pdf-op',
            'cambiar-estatus-requiere-fabricacion-nota-de-venta', 'listar--ccparam', 'crear-ccparam',
            'editar-ccparam', 'guardar-ccparam', 'eliminar-ccparam', 'listar-ccparam', 'actualizar-ccparam',
            'listar-ccparam-apsucetapaprod', 'guardar-ccparam-apsucetapaprod', 'actualizar-ccparam-apsucetapaprod',
            'eliminar-ccparam-apsucetapaprod', 'ver-pdf-muestra-cc', 'ver-etiqueta-regprod',
            'supervisar-ccregistmuestra', 'listar-cc-muestra-desbloqueo', 'guardar-cc-muestra-desbloqueo',
            'listar-reporte-cc-muestra', 'reporte-cc-lote',
            // Estos ya existían en producción con un id distinto al de biblioteca
            // (creados por una migración anterior que solo insertaba el permiso,
            // sin vincularlo a ningún rol). Detectado 2026-07-29 al probar en producción.
            'listar-ccregistmuestra', 'crear-ccregistmuestra', 'ver-ccregistmuestra',
            'liberar-ccregistmuestra', 'anular-ccregistmuestra', 'reporte-cobertura-cc',
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

        $menusNuevos = [
            ['Bloquear Cliente', 'clientebloqueado'],
            ['Inv Stock Vend', 'reportinvstockvend'],
            ['Desbloquear Cliente', 'clientedesbloqueado'],
            ['Desbloquear Cliente Pro', 'clientedesbloqueadopro'],
            ['Tipo Bodega', 'invbodegatipo'],
            ['Produccion', '#'],
            ['Orden Trabajo NV', 'otnv'],
            ['Programacion', 'otitemprogramacion'],
            ['Cargar Materia Prima', '#'],
            ['Extrusión', '#'],
            ['Orden Trabajo', 'ot'],
            ['Orden Trabajo Rep', 'reportot'],
            ['Aprobar OT', 'otaprobar'],
            ['Envia Item OT a Prog.', 'otitemenvprogprod'],
            ['Operario', 'operario'],
            ['Etapa Prod x Area Produc', 'areaproduccionsucetapaprod'],
            ['Etapas de Produccion', 'etapaprod'],
            ['AT Etapas Produccion', 'acuerdotecnicoetapaprod'],
            ['Persona Etapa Produccion', 'personaetapaprod'],
            ['Aprobar Registro Produccion', 'opdetregprodtempaprobsup'],
            ['Trazabilidad OP', 'op/seguimiento'],
            ['Maquinas', '#'],
            ['Registro de Produccion', 'opdetregprodtemp'],
            ['Control de Calidad', '#'],
            ['Maquina Grupo', 'maquinagrupo'],
            ['Maquina', 'maquina'],
            ['Atributos', 'atributo'],
            ['Mezclas', 'opdetregprodtemp/set/1'],
            ['Mezclado', 'opdetregprodtemp/set/2'],
            ['Extrusion.', 'opdetregprodtemp/set/3'],
            ['Impresion', 'opdetregprodtemp/set/4'],
            ['Sellado', 'opdetregprodtemp/set/5'],
            ['🔬 Reg Muestra', 'ccregistmuestra'],
            ['Supervisar Muestra CC', 'ccmuestrasuper'],
            ['Desbloquear CC Muestra', 'ccdesbloqueo'],
            ['CC Muestra', 'reportccmuestra'],
            ['Cobertura CC', 'reportcoberturacc'],
        ];

        foreach ($menusNuevos as [$nombre, $url]) {
            $menu = DB::table('menu')->where('nombre', $nombre)->where('url', $url)->first();
            if (!$menu) {
                continue;
            }
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
