-- Modulo REASIGNAR PRODUCCION — permisos y menu
-- Generado el 24/11/2026
--
-- Un lote fabricado para una nota de venta se entrega a otro cliente o a otra
-- nota de venta. Nada se borra: el vinculo con la NV que lo origino queda
-- intacto y se registra el hecho nuevo.
-- Ver docs/reasignacion-produccion.md y la migracion create_prodreasignacion_table.
--
-- No usa ids fijos: inserta por slug / por url solo si falta. Idempotente.

-- ---------------------------------------------------------------- permisos
INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Listar Reasignacion Produccion','listar-prod-reasignacion',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'listar-prod-reasignacion');

-- Crear y deshacer van juntos a proposito: quien puede quitarle una produccion
-- a un cliente tiene que poder devolversela.
INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Crear Reasignacion Produccion','crear-prod-reasignacion',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'crear-prod-reasignacion');

-- ------------------------------------------------------------------- menu
-- Cuelga del menu de Produccion, el mismo padre que Registro de Produccion.
INSERT INTO `menu` (`menu_id`,`nombre`,`url`,`orden`,`icono`,`created_at`,`updated_at`)
SELECT (SELECT `menu_id` FROM (SELECT `menu_id` FROM `menu` WHERE `url` = 'opdetregprodtemp' LIMIT 1) AS t),
       'Reasignar Produccion','prodreasignacion',21,'fa fa-exchange',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM (SELECT 1 FROM `menu` WHERE `url` = 'prodreasignacion' LIMIT 1) AS x)
  AND EXISTS (SELECT 1 FROM (SELECT 1 FROM `menu` WHERE `url` = 'opdetregprodtemp' LIMIT 1) AS y);

-- -------------------------------------------------------- asignar al rol 1
INSERT INTO `permiso_rol` (`permiso_id`,`rol_id`,`created_at`,`updated_at`)
SELECT p.id, 1, NOW(), NOW() FROM `permiso` p
WHERE p.slug IN ('listar-prod-reasignacion','crear-prod-reasignacion')
  AND NOT EXISTS (SELECT 1 FROM (SELECT permiso_id, rol_id FROM `permiso_rol`) AS pr
                  WHERE pr.permiso_id = p.id AND pr.rol_id = 1);

INSERT INTO `menu_rol` (`menu_id`,`rol_id`,`created_at`,`updated_at`)
SELECT m.id, 1, NOW(), NOW() FROM `menu` m
WHERE m.url = 'prodreasignacion'
  AND NOT EXISTS (SELECT 1 FROM (SELECT menu_id, rol_id FROM `menu_rol`) AS mr
                  WHERE mr.menu_id = m.id AND mr.rol_id = 1);
