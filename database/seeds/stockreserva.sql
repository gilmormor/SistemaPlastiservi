-- Modulo RESERVA DE STOCK — permisos y menu
-- Generado el 24/11/2026
--
-- Apartar stock sin moverlo, para lo que el picking no puede cubrir: stock de
-- una OT libre o de una carga de inventario que se quiere guardar para un
-- cliente antes de que exista la nota de venta.
-- Ver la migracion create_stockreserva_table para el contexto completo.
--
-- No usa ids fijos: inserta por slug / por url solo si falta, y resuelve el
-- menu padre por nombre. Se puede correr mas de una vez sin duplicar.

-- ---------------------------------------------------------------- permisos
INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Listar Reserva de Stock','listar-stock-reserva',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'listar-stock-reserva');

INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Crear Reserva de Stock','crear-stock-reserva',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'crear-stock-reserva');

-- Liberar va aparte de crear a proposito: soltar stock apartado para un cliente
-- es una decision comercial, y puede querer limitarse a menos gente que la de
-- reservar.
INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Liberar Reserva de Stock','liberar-stock-reserva',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'liberar-stock-reserva');

-- ------------------------------------------------------------------- menu
-- Cuelga del menu de Inventario, el mismo padre que Inventario Control (se
-- resuelve por el menu_id de esa entrada para no depender de un id fijo, que
-- puede diferir entre la base local y la del hosting).
INSERT INTO `menu` (`menu_id`,`nombre`,`url`,`orden`,`icono`,`created_at`,`updated_at`)
SELECT (SELECT `menu_id` FROM (SELECT `menu_id` FROM `menu` WHERE `url` = 'invcontrol' LIMIT 1) AS t),
       'Reserva de Stock','stockreserva',20,'fa fa-lock',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM (SELECT 1 FROM `menu` WHERE `url` = 'stockreserva' LIMIT 1) AS x)
  AND EXISTS (SELECT 1 FROM (SELECT 1 FROM `menu` WHERE `url` = 'invcontrol' LIMIT 1) AS y);

-- -------------------------------------------------------- asignar al rol 1
-- Administrador, para poder probar. Los demas roles se asignan desde la
-- pantalla de roles segun quien deba reservar y quien liberar.
INSERT INTO `permiso_rol` (`permiso_id`,`rol_id`,`created_at`,`updated_at`)
SELECT p.id, 1, NOW(), NOW() FROM `permiso` p
WHERE p.slug IN ('listar-stock-reserva','crear-stock-reserva','liberar-stock-reserva')
  AND NOT EXISTS (SELECT 1 FROM (SELECT permiso_id, rol_id FROM `permiso_rol`) AS pr
                  WHERE pr.permiso_id = p.id AND pr.rol_id = 1);

INSERT INTO `menu_rol` (`menu_id`,`rol_id`,`created_at`,`updated_at`)
SELECT m.id, 1, NOW(), NOW() FROM `menu` m
WHERE m.url = 'stockreserva'
  AND NOT EXISTS (SELECT 1 FROM (SELECT menu_id, rol_id FROM `menu_rol`) AS mr
                  WHERE mr.menu_id = m.id AND mr.rol_id = 1);
