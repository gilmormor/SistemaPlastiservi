-- Permisos de administracion que faltan en produccion
-- Generado el 09/11/2026 15:25
--
-- Estas pantallas (admin/permiso, admin/rol, admin/permiso-rol, admin/menu-rol)
-- piden un permiso que nunca se creo en produccion. Hoy solo se entra por ser
-- administrador, porque can() deja pasar cuando el rol activo se llama
-- 'administrador'; con cualquier otro rol la pantalla redirige al inicio.
--
-- No usa ids fijos: inserta por slug solo si falta, y resuelve el rol por
-- nombre. Se puede correr mas de una vez sin duplicar.

INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Listar admin Permiso Rol','listar-admin-permiso-rol',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'listar-admin-permiso-rol');
INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Guardar Admin Permiso Rol','guardar-admin-permiso-rol',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'guardar-admin-permiso-rol');
INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Listar Admin Permiso','listar-admin-permiso',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'listar-admin-permiso');
INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Crear Admin Permiso','crear-admin-permiso',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'crear-admin-permiso');
INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Guardar Admin Permiso','guardar-admin-permiso',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'guardar-admin-permiso');
INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Editar Admin Permiso','editar-admin-permiso',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'editar-admin-permiso');
INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Eliminar Admin Permiso','eliminar-admin-permiso',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'eliminar-admin-permiso');
INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Listar Admin Rol','listar-admin-rol',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'listar-admin-rol');
INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Crear Admin Rol','crear-admin-rol',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'crear-admin-rol');
INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Guardar Admin Rol','guardar-admin-rol',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'guardar-admin-rol');
INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Editar Admin Rol','editar-admin-rol',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'editar-admin-rol');
INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Eliminar Admin Rol','eliminar-admin-rol',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'eliminar-admin-rol');
INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Listar Admin Menu Rol','listar-admin-menu-rol',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'listar-admin-menu-rol');
INSERT INTO `permiso` (`nombre`,`slug`,`created_at`,`updated_at`)
SELECT 'Guardar Admin Menu Rol','guardar-admin-menu-rol',NOW(),NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `permiso` WHERE `slug` = 'guardar-admin-menu-rol');

-- Vinculo con el rol Administrador
INSERT INTO `permiso_rol` (`permiso_id`,`rol_id`,`created_at`,`updated_at`)
SELECT p.id, r.id, NOW(), NOW()
  FROM `permiso` p, `rol` r
 WHERE p.slug = 'listar-admin-permiso-rol' AND LOWER(r.nombre) = 'administrador'
   AND NOT EXISTS (SELECT 1 FROM `permiso_rol` pr WHERE pr.permiso_id = p.id AND pr.rol_id = r.id);
INSERT INTO `permiso_rol` (`permiso_id`,`rol_id`,`created_at`,`updated_at`)
SELECT p.id, r.id, NOW(), NOW()
  FROM `permiso` p, `rol` r
 WHERE p.slug = 'guardar-admin-permiso-rol' AND LOWER(r.nombre) = 'administrador'
   AND NOT EXISTS (SELECT 1 FROM `permiso_rol` pr WHERE pr.permiso_id = p.id AND pr.rol_id = r.id);
INSERT INTO `permiso_rol` (`permiso_id`,`rol_id`,`created_at`,`updated_at`)
SELECT p.id, r.id, NOW(), NOW()
  FROM `permiso` p, `rol` r
 WHERE p.slug = 'listar-admin-permiso' AND LOWER(r.nombre) = 'administrador'
   AND NOT EXISTS (SELECT 1 FROM `permiso_rol` pr WHERE pr.permiso_id = p.id AND pr.rol_id = r.id);
INSERT INTO `permiso_rol` (`permiso_id`,`rol_id`,`created_at`,`updated_at`)
SELECT p.id, r.id, NOW(), NOW()
  FROM `permiso` p, `rol` r
 WHERE p.slug = 'crear-admin-permiso' AND LOWER(r.nombre) = 'administrador'
   AND NOT EXISTS (SELECT 1 FROM `permiso_rol` pr WHERE pr.permiso_id = p.id AND pr.rol_id = r.id);
INSERT INTO `permiso_rol` (`permiso_id`,`rol_id`,`created_at`,`updated_at`)
SELECT p.id, r.id, NOW(), NOW()
  FROM `permiso` p, `rol` r
 WHERE p.slug = 'guardar-admin-permiso' AND LOWER(r.nombre) = 'administrador'
   AND NOT EXISTS (SELECT 1 FROM `permiso_rol` pr WHERE pr.permiso_id = p.id AND pr.rol_id = r.id);
INSERT INTO `permiso_rol` (`permiso_id`,`rol_id`,`created_at`,`updated_at`)
SELECT p.id, r.id, NOW(), NOW()
  FROM `permiso` p, `rol` r
 WHERE p.slug = 'editar-admin-permiso' AND LOWER(r.nombre) = 'administrador'
   AND NOT EXISTS (SELECT 1 FROM `permiso_rol` pr WHERE pr.permiso_id = p.id AND pr.rol_id = r.id);
INSERT INTO `permiso_rol` (`permiso_id`,`rol_id`,`created_at`,`updated_at`)
SELECT p.id, r.id, NOW(), NOW()
  FROM `permiso` p, `rol` r
 WHERE p.slug = 'eliminar-admin-permiso' AND LOWER(r.nombre) = 'administrador'
   AND NOT EXISTS (SELECT 1 FROM `permiso_rol` pr WHERE pr.permiso_id = p.id AND pr.rol_id = r.id);
INSERT INTO `permiso_rol` (`permiso_id`,`rol_id`,`created_at`,`updated_at`)
SELECT p.id, r.id, NOW(), NOW()
  FROM `permiso` p, `rol` r
 WHERE p.slug = 'listar-admin-rol' AND LOWER(r.nombre) = 'administrador'
   AND NOT EXISTS (SELECT 1 FROM `permiso_rol` pr WHERE pr.permiso_id = p.id AND pr.rol_id = r.id);
INSERT INTO `permiso_rol` (`permiso_id`,`rol_id`,`created_at`,`updated_at`)
SELECT p.id, r.id, NOW(), NOW()
  FROM `permiso` p, `rol` r
 WHERE p.slug = 'crear-admin-rol' AND LOWER(r.nombre) = 'administrador'
   AND NOT EXISTS (SELECT 1 FROM `permiso_rol` pr WHERE pr.permiso_id = p.id AND pr.rol_id = r.id);
INSERT INTO `permiso_rol` (`permiso_id`,`rol_id`,`created_at`,`updated_at`)
SELECT p.id, r.id, NOW(), NOW()
  FROM `permiso` p, `rol` r
 WHERE p.slug = 'guardar-admin-rol' AND LOWER(r.nombre) = 'administrador'
   AND NOT EXISTS (SELECT 1 FROM `permiso_rol` pr WHERE pr.permiso_id = p.id AND pr.rol_id = r.id);
INSERT INTO `permiso_rol` (`permiso_id`,`rol_id`,`created_at`,`updated_at`)
SELECT p.id, r.id, NOW(), NOW()
  FROM `permiso` p, `rol` r
 WHERE p.slug = 'editar-admin-rol' AND LOWER(r.nombre) = 'administrador'
   AND NOT EXISTS (SELECT 1 FROM `permiso_rol` pr WHERE pr.permiso_id = p.id AND pr.rol_id = r.id);
INSERT INTO `permiso_rol` (`permiso_id`,`rol_id`,`created_at`,`updated_at`)
SELECT p.id, r.id, NOW(), NOW()
  FROM `permiso` p, `rol` r
 WHERE p.slug = 'eliminar-admin-rol' AND LOWER(r.nombre) = 'administrador'
   AND NOT EXISTS (SELECT 1 FROM `permiso_rol` pr WHERE pr.permiso_id = p.id AND pr.rol_id = r.id);
INSERT INTO `permiso_rol` (`permiso_id`,`rol_id`,`created_at`,`updated_at`)
SELECT p.id, r.id, NOW(), NOW()
  FROM `permiso` p, `rol` r
 WHERE p.slug = 'listar-admin-menu-rol' AND LOWER(r.nombre) = 'administrador'
   AND NOT EXISTS (SELECT 1 FROM `permiso_rol` pr WHERE pr.permiso_id = p.id AND pr.rol_id = r.id);
INSERT INTO `permiso_rol` (`permiso_id`,`rol_id`,`created_at`,`updated_at`)
SELECT p.id, r.id, NOW(), NOW()
  FROM `permiso` p, `rol` r
 WHERE p.slug = 'guardar-admin-menu-rol' AND LOWER(r.nombre) = 'administrador'
   AND NOT EXISTS (SELECT 1 FROM `permiso_rol` pr WHERE pr.permiso_id = p.id AND pr.rol_id = r.id);

-- Comprobacion: deben aparecer los 14 con su vinculo
SELECT p.slug, IF(pr.rol_id IS NULL,'SIN VINCULO','ok') AS estado
  FROM `permiso` p
  LEFT JOIN `permiso_rol` pr ON pr.permiso_id = p.id
   AND pr.rol_id = (SELECT id FROM `rol` WHERE LOWER(nombre)='administrador' LIMIT 1)
 WHERE p.slug IN ('listar-admin-permiso-rol','guardar-admin-permiso-rol','listar-admin-permiso','crear-admin-permiso','guardar-admin-permiso','editar-admin-permiso','eliminar-admin-permiso','listar-admin-rol','crear-admin-rol','guardar-admin-rol','editar-admin-rol','eliminar-admin-rol','listar-admin-menu-rol','guardar-admin-menu-rol');