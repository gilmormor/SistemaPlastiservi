-- ---------------------------------------------------------------------------
-- Permisos de las pantallas de administración de seguridad, para el rol Administrador.
--
-- Equivalente exacto de database/seeds/PermisosAdminSeeder.php, en SQL puro, para
-- ambientes donde no se puede regenerar el autoload de composer.
--
-- POR QUÉ ES URGENTE: los controladores Admin\RolController, Admin\MenuRolController,
-- Admin\PermisoController y Admin\PermisoRolController validan permiso con can(),
-- pero esos permisos no existen en la tabla `permiso`. Mientras falten, esas cuatro
-- pantallas quedan inaccesibles para TODOS, incluido el Administrador: can() solo
-- hace bypass cuando el nombre del rol en sesión es exactamente 'administrador' en
-- minúscula, y el rol 1 se llama 'Administrador'. Como son justamente las pantallas
-- donde se administran roles y permisos, el bloqueo NO se puede resolver desde la
-- interfaz. Si el código llega a producción sin esto, hay que correr este archivo.
--
-- Idempotente: se puede ejecutar varias veces, no duplica nada.
-- Los ids de permiso los asigna el autoincremento (difieren entre ambientes); no
-- importa, porque can() resuelve por slug. El rol Administrador sí es id=1 fijo,
-- confirmado igual en desarrollo y producción.
--
-- Ejecutar:  mysql -u USUARIO -p BASE < seed_permisos_admin.sql
-- o pegar el contenido completo en la pestaña SQL de phpMyAdmin.
-- ---------------------------------------------------------------------------

START TRANSACTION;

-- 1. Crear los 14 permisos que no existan ------------------------------------
-- La tabla derivada (SELECT ... ) t es necesaria: MySQL no permite leer la tabla
-- destino directamente en el SELECT de un INSERT sobre ella misma.
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT t.nombre, t.slug, NOW(), NOW()
FROM (
    SELECT 'Listar Admin Rol'          AS nombre, 'listar-admin-rol'          AS slug
    UNION ALL SELECT 'Crear Admin Rol',            'crear-admin-rol'
    UNION ALL SELECT 'Guardar Admin Rol',          'guardar-admin-rol'
    UNION ALL SELECT 'Editar Admin Rol',           'editar-admin-rol'
    UNION ALL SELECT 'Eliminar Admin Rol',         'eliminar-admin-rol'
    UNION ALL SELECT 'Listar Admin Menu Rol',      'listar-admin-menu-rol'
    UNION ALL SELECT 'Guardar Admin Menu Rol',     'guardar-admin-menu-rol'
    UNION ALL SELECT 'Listar Admin Permiso',       'listar-admin-permiso'
    UNION ALL SELECT 'Crear Admin Permiso',        'crear-admin-permiso'
    UNION ALL SELECT 'Guardar Admin Permiso',      'guardar-admin-permiso'
    UNION ALL SELECT 'Editar Admin Permiso',       'editar-admin-permiso'
    UNION ALL SELECT 'Eliminar Admin Permiso',     'eliminar-admin-permiso'
    UNION ALL SELECT 'Listar Admin Permiso Rol',   'listar-admin-permiso-rol'
    UNION ALL SELECT 'Guardar Admin Permiso Rol',  'guardar-admin-permiso-rol'
) t
WHERE t.slug NOT IN (
    SELECT slug FROM (SELECT slug FROM permiso) ya
);

-- 2. Vincularlos al rol Administrador (id=1) ---------------------------------
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT 1, t.id, NOW(), NOW()
FROM (
    SELECT p.id
    FROM   permiso p
    WHERE  p.slug IN (
              'listar-admin-rol', 'crear-admin-rol', 'guardar-admin-rol',
              'editar-admin-rol', 'eliminar-admin-rol',
              'listar-admin-menu-rol', 'guardar-admin-menu-rol',
              'listar-admin-permiso', 'crear-admin-permiso', 'guardar-admin-permiso',
              'editar-admin-permiso', 'eliminar-admin-permiso',
              'listar-admin-permiso-rol', 'guardar-admin-permiso-rol'
           )
      AND  p.id NOT IN (
              SELECT permiso_id FROM (
                  SELECT permiso_id FROM permiso_rol WHERE rol_id = 1
              ) ya
           )
) t;

COMMIT;

-- ---------------------------------------------------------------------------
-- Verificación: debe devolver 14 y 14. Si alguno da menos, revisar.
-- ---------------------------------------------------------------------------
SELECT
    (SELECT COUNT(*) FROM permiso p
      WHERE p.slug IN ('listar-admin-rol','crear-admin-rol','guardar-admin-rol',
                       'editar-admin-rol','eliminar-admin-rol','listar-admin-menu-rol',
                       'guardar-admin-menu-rol','listar-admin-permiso','crear-admin-permiso',
                       'guardar-admin-permiso','editar-admin-permiso','eliminar-admin-permiso',
                       'listar-admin-permiso-rol','guardar-admin-permiso-rol')) AS permisos_creados,
    (SELECT COUNT(*) FROM permiso_rol pr
      INNER JOIN permiso p ON p.id = pr.permiso_id
      WHERE pr.rol_id = 1
        AND p.slug IN ('listar-admin-rol','crear-admin-rol','guardar-admin-rol',
                       'editar-admin-rol','eliminar-admin-rol','listar-admin-menu-rol',
                       'guardar-admin-menu-rol','listar-admin-permiso','crear-admin-permiso',
                       'guardar-admin-permiso','editar-admin-permiso','eliminar-admin-permiso',
                       'listar-admin-permiso-rol','guardar-admin-permiso-rol')) AS asignados_al_rol_1;
