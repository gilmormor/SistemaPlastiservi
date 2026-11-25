-- Reporte SALDO POR PRODUCIR — permiso y menu
-- Generado el 25/11/2026
--
-- Explica el numero que ve el vendedor en la nota de venta:
--     stock vendible - pendiente por despachar - reservado
-- Un negativo significa que el producto esta sobrevendido. Al vendedor se le da
-- solo ese numero; esta pantalla muestra de donde sale, bodega por bodega y
-- venta por venta.
--
-- Permiso PROPIO y no el de reportinvstock: ese lo tienen tambien los vendedores
-- y aqui se ve el stock completo, que es justo lo que no deben ver.
--
-- No usa ids fijos: inserta por slug / por url solo si falta. Idempotente.

INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Reporte Saldo por Producir','reporte-saldo-producir',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'reporte-saldo-producir');

-- Cuelga del mismo menu que Inv Stock (se resuelve por su menu_id para no
-- depender de un id fijo, que puede diferir entre bases).
INSERT INTO `menu` (`menu_id`,`nombre`,`url`,`orden`,`icono`,`created_at`,`updated_at`)
SELECT (SELECT `menu_id` FROM (SELECT `menu_id` FROM `menu` WHERE `url` = 'reportinvstock' LIMIT 1) AS t),
       'Saldo por Producir','reportsaldoproducir',21,'fa fa-balance-scale',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM (SELECT 1 FROM `menu` WHERE `url` = 'reportsaldoproducir' LIMIT 1) AS x)
  AND EXISTS (SELECT 1 FROM (SELECT 1 FROM `menu` WHERE `url` = 'reportinvstock' LIMIT 1) AS y);

INSERT INTO `permiso_rol` (`permiso_id`,`rol_id`,`created_at`,`updated_at`)
SELECT p.id, 1, NOW(), NOW() FROM `permiso` p
WHERE p.slug = 'reporte-saldo-producir'
  AND NOT EXISTS (SELECT 1 FROM (SELECT permiso_id, rol_id FROM `permiso_rol`) AS pr
                  WHERE pr.permiso_id = p.id AND pr.rol_id = 1);

INSERT INTO `menu_rol` (`menu_id`,`rol_id`,`created_at`,`updated_at`)
SELECT m.id, 1, NOW(), NOW() FROM `menu` m
WHERE m.url = 'reportsaldoproducir'
  AND NOT EXISTS (SELECT 1 FROM (SELECT menu_id, rol_id FROM `menu_rol`) AS mr
                  WHERE mr.menu_id = m.id AND mr.rol_id = 1);
