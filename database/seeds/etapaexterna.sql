-- Modulo ETAPA EXTERNA — permisos y menu
-- Generado el 18/11/2026
--
-- Planilla y etiquetas del proceso que se ejecuta fuera del sistema entre una
-- etapa de produccion y la siguiente (hoy, entre extrusion/impresion y
-- sellado). Ver la migracion create_etapaextplanilla_table para el contexto.
--
-- No usa ids fijos: inserta por slug / por url solo si falta, y resuelve el
-- menu padre por nombre. Se puede correr mas de una vez sin duplicar.

-- ---------------------------------------------------------------- permisos
INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Listar Etapa Externa','listar-etapa-externa',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'listar-etapa-externa');

INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Crear Etapa Externa','crear-etapa-externa',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'crear-etapa-externa');

-- ------------------------------------------------------------------- menus
-- Cuelgan del menu de Produccion, el mismo padre que Registro de Produccion y
-- Aprobar Registro Produccion (se resuelve por el menu_id de esas entradas,
-- para no depender de un id fijo que puede diferir entre bases).
INSERT INTO `menu` (`menu_id`,`nombre`,`url`,`orden`,`icono`,`created_at`,`updated_at`)
SELECT (SELECT `menu_id` FROM (SELECT `menu_id` FROM `menu` WHERE `url` = 'opdetregprodtemp' LIMIT 1) AS t),
       'Etapa Externa','etapaexterna',11,'fa fa-print',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM (SELECT 1 FROM `menu` WHERE `url` = 'etapaexterna' LIMIT 1) AS x)
  AND EXISTS (SELECT 1 FROM (SELECT 1 FROM `menu` WHERE `url` = 'opdetregprodtemp' LIMIT 1) AS y);

INSERT INTO `menu` (`menu_id`,`nombre`,`url`,`orden`,`icono`,`created_at`,`updated_at`)
SELECT (SELECT `menu_id` FROM (SELECT `menu_id` FROM `menu` WHERE `url` = 'opdetregprodtemp' LIMIT 1) AS t),
       'Trazabilidad Etapa Externa','etapaexterna/listado',12,'fa fa-list',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM (SELECT 1 FROM `menu` WHERE `url` = 'etapaexterna/listado' LIMIT 1) AS x)
  AND EXISTS (SELECT 1 FROM (SELECT 1 FROM `menu` WHERE `url` = 'opdetregprodtemp' LIMIT 1) AS y);

-- -------------------------------------------------- asignacion a los roles
-- Deliberadamente vacio: la asignacion de estos permisos y menus a cada rol se
-- hace desde las pantallas de administracion (admin/permiso-rol y
-- admin/menu-rol), que es donde se decide quien ve que. El rol
-- 'administrador' entra igual, porque can() lo deja pasar por nombre.
