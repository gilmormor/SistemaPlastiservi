-- ---------------------------------------------------------------------------
-- Permiso propio para la visibilidad de OT: 'ver-ot-de-todos-los-usuarios'
--
-- Equivalente exacto de database/seeds/PermisoVerOtTodosUsuariosSeeder.php,
-- en SQL puro, para ambientes donde no se puede regenerar el autoload de
-- composer (el seeder de PHP no se descubre si no se corre dump-autoload).
--
-- Que hace:
--   1. Crea el permiso 'ver-ot-de-todos-los-usuarios' si no existe.
--   2. Se lo asigna a los mismos roles que hoy tienen
--      'ver-facturas-de-todos-los-usuarios', para que ningun rol pierda
--      visibilidad de OT al cambiar el permiso en Ot::reportot/reportOtItem.
--   3. Se lo asigna al rol 'Val AT'.
--   4. Le da al rol 'Val AT' los permisos de entrada a las 4 pantallas de OT.
--
-- Idempotente: se puede ejecutar varias veces, no duplica nada.
-- Los ids de rol y de permiso se resuelven por nombre/slug, nunca fijos,
-- porque difieren entre ambientes.
--
-- OJO: 'listar-aprobar-ot' habilita de hecho aprobar y rechazar OT, porque
-- OtAprobarController solo valida permiso en index(), no en aprobar()/rechazar().
-- Si Val AT solo debe consultar, elimina ese slug del paso 4.
--
-- Ejecutar:  mysql -u USUARIO -p BASE < seed_permiso_ver_ot_todos_usuarios.sql
-- o pegar el contenido completo en la pestana SQL de phpMyAdmin.
-- ---------------------------------------------------------------------------

START TRANSACTION;

-- 1. Crear el permiso si no existe -------------------------------------------
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Ver OT de todos los usuarios', 'ver-ot-de-todos-los-usuarios', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM permiso WHERE slug = 'ver-ot-de-todos-los-usuarios'
);

SET @permiso_ot = (SELECT id FROM permiso WHERE slug = 'ver-ot-de-todos-los-usuarios');

-- 2. Heredar los roles que ya veian todas las OT ------------------------------
-- La tabla derivada (SELECT ... ) t es necesaria: MySQL no permite leer la
-- tabla destino directamente en el SELECT de un INSERT sobre ella misma.
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT t.rol_id, @permiso_ot, NOW(), NOW()
FROM (
    SELECT DISTINCT pr.rol_id
    FROM   permiso_rol pr
    INNER  JOIN permiso p ON p.id = pr.permiso_id
    WHERE  p.slug = 'ver-facturas-de-todos-los-usuarios'
      AND  pr.rol_id NOT IN (
              SELECT rol_id FROM (
                  SELECT rol_id FROM permiso_rol WHERE permiso_id = @permiso_ot
              ) ya
           )
) t;

-- 3. Asignar el permiso al rol 'Val AT' ---------------------------------------
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT t.id, @permiso_ot, NOW(), NOW()
FROM (
    SELECT r.id
    FROM   rol r
    WHERE  r.nombre = 'Val AT'
      AND  r.id NOT IN (
              SELECT rol_id FROM (
                  SELECT rol_id FROM permiso_rol WHERE permiso_id = @permiso_ot
              ) ya
           )
) t;

-- 4. Permisos de entrada a las pantallas de OT para 'Val AT' -------------------
-- Sin estos, can() redirige al inicio antes de llegar a la consulta.
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT t.rol_id, t.permiso_id, NOW(), NOW()
FROM (
    SELECT r.id AS rol_id, p.id AS permiso_id
    FROM   rol r
    CROSS  JOIN permiso p
    WHERE  r.nombre = 'Val AT'
      AND  p.slug IN (
              'listar-reporte-ot',           -- reportot
              'listar-envia-item-ot-prog',   -- otitemenvprogprod
              'listar-aprobar-ot',           -- otaprobar (ver nota de arriba)
              'listar-programacion-item-ot'  -- otitemprogramacion
           )
      AND  NOT EXISTS (
              SELECT 1 FROM (
                  SELECT rol_id, permiso_id FROM permiso_rol
              ) ya
              WHERE ya.rol_id = r.id AND ya.permiso_id = p.id
           )
) t;

COMMIT;

-- ---------------------------------------------------------------------------
-- Verificacion: ejecutar despues del COMMIT para confirmar el resultado.
-- 'roles_ot' debe ser igual a 'roles_facturas' + 1 (el rol Val AT).
-- ---------------------------------------------------------------------------
SELECT
    (SELECT COUNT(*) FROM permiso_rol pr INNER JOIN permiso p ON p.id = pr.permiso_id
      WHERE p.slug = 'ver-ot-de-todos-los-usuarios')       AS roles_ot,
    (SELECT COUNT(*) FROM permiso_rol pr INNER JOIN permiso p ON p.id = pr.permiso_id
      WHERE p.slug = 'ver-facturas-de-todos-los-usuarios') AS roles_facturas;

-- Detalle de lo que quedo asignado al rol 'Val AT'
SELECT r.id AS rol_id, r.nombre AS rol, p.slug AS permiso
FROM   rol r
INNER  JOIN permiso_rol pr ON pr.rol_id = r.id
INNER  JOIN permiso p      ON p.id = pr.permiso_id
WHERE  r.nombre = 'Val AT'
  AND  p.slug IN ('ver-ot-de-todos-los-usuarios', 'listar-reporte-ot',
                  'listar-envia-item-ot-prog', 'listar-aprobar-ot',
                  'listar-programacion-item-ot')
ORDER  BY p.slug;
