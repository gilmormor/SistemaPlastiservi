-- Fix: envprogfecha y envprogusu_id en otdet quedaron NOT NULL sin default,
-- pero solo se llenan más adelante (en OtItemEnvProgProdController.php al enviar
-- el ítem a programación), no al crear la OT. Esto rompe la creación de cualquier
-- OT nueva con error 1452 (FK envprogusu_id=0 no existe en usuario).
--
-- La migración fuente (2024_11_26_092857_create_table_otdet.php) ya se corrigió
-- para instalaciones nuevas; este ALTER es para la tabla que ya existe en producción.

ALTER TABLE otdet MODIFY envprogfecha DATETIME NULL COMMENT 'Fecha de envio a programacion';
ALTER TABLE otdet MODIFY envprogusu_id BIGINT UNSIGNED NULL COMMENT 'Usuario que envio a programacion';
