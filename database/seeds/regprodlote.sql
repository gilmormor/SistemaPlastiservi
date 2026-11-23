-- Registro de Produccion por Lote — menu
-- Generado el 23/11/2026
--
-- Entrada rapida al registro de produccion: el operario escribe el numero de
-- lote y el sistema deduce la etapa y el item de la OP.
--
-- NO crea permisos: usa los del registro de produccion que ya existen
-- (crear-registro-produccion), porque es la misma accion por otro camino. Quien
-- puede registrar produccion puede usar esta entrada, y el acceso a cada etapa
-- lo sigue gobernando personaetapaprod.
--
-- Idempotente: se puede correr mas de una vez sin duplicar.

INSERT INTO `menu` (`menu_id`,`nombre`,`url`,`orden`,`icono`,`created_at`,`updated_at`)
SELECT (SELECT `menu_id` FROM (SELECT `menu_id` FROM `menu` WHERE `url` = 'opdetregprodtemp' LIMIT 1) AS t),
       'Registrar Produccion x Lote','regprodlote',8,'fa fa-barcode',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM (SELECT 1 FROM `menu` WHERE `url` = 'regprodlote' LIMIT 1) AS x)
  AND EXISTS (SELECT 1 FROM (SELECT 1 FROM `menu` WHERE `url` = 'opdetregprodtemp' LIMIT 1) AS y);
