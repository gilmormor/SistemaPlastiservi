-- Seed: vincula al rol Administrador (id=1) los permisos/menus del Módulo Producción
-- Requiere haber corrido antes seed_tablas_maestras_modulo_produccion.sql y seed_menu_permiso_modulo_produccion.sql
-- Generado 2026-07-27 comparando biblioteca (origen) vs plxrevisarcierregd. Idempotente.

SET NAMES utf8mb4;
SET @rol_admin = 1; -- Administrador, id confirmado igual en biblioteca y produccion

-- ============ PERMISO_ROL ============
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-ot'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'crear-ot'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'editar-ot'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'guardar-ot'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'eliminar-ot'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-otnv'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'crear-otnv'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'editar-otnv'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'guardar-otnv'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'eliminar-otnv'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'ver-pdf-ot'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-reporte-ot'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-aprobar-ot'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-envia-item-ot-prog'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-programacion-item-ot'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-maquina-grupo'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'crear-maquina-grupo'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'editar-maquina-grupo'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'guardar-maquina-grupo'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'eliminar-maquina-grupo'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-maquina'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'crear-maquina'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'editar-maquina'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'guardar-maquina'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'eliminar-maquina'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-atributo'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'crear-atributo'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'editar-atributo'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'guardar-atributo'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'eliminar-atributo'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-operario'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'crear-operario'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'editar-operario'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'guardar-operario'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'eliminar-operario'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-area-produccion-suc-etapa-prod'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-etapa-de-produccion'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'crear-etapa-de-produccion'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'editar-etapa-de-produccion'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'guardar-etapa-de-produccion'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'eliminar-etapa-de-produccion'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'editar-area-produccion-suc-fase'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'editar-area-produccion-suc-etapa-prod'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'guardar-area-produccion-suc-etapa-prod'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-acuerdo-tecnico-etapa-de-produccion'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'editar-acuerdo-tecnico-etapa-de-produccion'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'guardar-acuerdo-tecnico-etapa-de-produccion'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'editar-etapas-productivas-en-programar-produccion'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-registro-produccion'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'crear-registro-produccion'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'editar-registro-produccion'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'guardar-registro-produccion'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'eliminar-registro-produccion'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-persona-etapaprod'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'editar-persona-etapaprod'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'guardar-persona-etapaprod'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-etapasprod-x-persona'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-invbodegatipo'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'crear-invbodegatipo'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'editar-invbodegatipo'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'guardar-invbodegatipo'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'eliminar-invbodegatipo'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-registro-produccion-aprobar-supervidor'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'ver-pdf-op'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'cambiar-estatus-requiere-fabricacion-nota-de-venta'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar--ccparam'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'crear-ccparam'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'editar-ccparam'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'guardar-ccparam'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'eliminar-ccparam'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-ccparam'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'actualizar-ccparam'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-ccparam-apsucetapaprod'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'guardar-ccparam-apsucetapaprod'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'actualizar-ccparam-apsucetapaprod'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'eliminar-ccparam-apsucetapaprod'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'ver-pdf-muestra-cc'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'ver-etiqueta-regprod'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'supervisar-ccregistmuestra'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-cc-muestra-desbloqueo'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'guardar-cc-muestra-desbloqueo'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'listar-reporte-cc-muestra'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);
INSERT INTO permiso_rol (rol_id, permiso_id, created_at, updated_at)
SELECT @rol_admin, p.id, NOW(), NOW() FROM permiso p WHERE p.slug = 'reporte-cc-lote'
AND NOT EXISTS (SELECT 1 FROM permiso_rol WHERE rol_id = @rol_admin AND permiso_id = p.id);

-- ============ MENU_ROL ============
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Bloquear Cliente' AND m.url = 'clientebloqueado'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Inv Stock Vend' AND m.url = 'reportinvstockvend'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Desbloquear Cliente' AND m.url = 'clientedesbloqueado'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Desbloquear Cliente Pro' AND m.url = 'clientedesbloqueadopro'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Tipo Bodega' AND m.url = 'invbodegatipo'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Produccion' AND m.url = '#'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Orden Trabajo NV' AND m.url = 'otnv'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Programacion' AND m.url = 'otitemprogramacion'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Cargar Materia Prima' AND m.url = '#'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Extrusión' AND m.url = '#'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Orden Trabajo' AND m.url = 'ot'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Orden Trabajo Rep' AND m.url = 'reportot'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Aprobar OT' AND m.url = 'otaprobar'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Envia Item OT a Prog.' AND m.url = 'otitemenvprogprod'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Operario' AND m.url = 'operario'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Etapa Prod x Area Produc' AND m.url = 'areaproduccionsucetapaprod'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Etapas de Produccion' AND m.url = 'etapaprod'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'AT Etapas Produccion' AND m.url = 'acuerdotecnicoetapaprod'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Persona Etapa Produccion' AND m.url = 'personaetapaprod'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Aprobar Registro Produccion' AND m.url = 'opdetregprodtempaprobsup'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Trazabilidad OP' AND m.url = 'op/seguimiento'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Maquinas' AND m.url = '#'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Registro de Produccion' AND m.url = 'opdetregprodtemp'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Control de Calidad' AND m.url = '#'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Maquina Grupo' AND m.url = 'maquinagrupo'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Maquina' AND m.url = 'maquina'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Atributos' AND m.url = 'atributo'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Mezclas' AND m.url = 'opdetregprodtemp/set/1'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Mezclado' AND m.url = 'opdetregprodtemp/set/2'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Extrusion.' AND m.url = 'opdetregprodtemp/set/3'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Impresion' AND m.url = 'opdetregprodtemp/set/4'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Sellado' AND m.url = 'opdetregprodtemp/set/5'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = '🔬 Reg Muestra' AND m.url = 'ccregistmuestra'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Supervisar Muestra CC' AND m.url = 'ccmuestrasuper'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Desbloquear CC Muestra' AND m.url = 'ccdesbloqueo'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'CC Muestra' AND m.url = 'reportccmuestra'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
INSERT INTO menu_rol (rol_id, menu_id, created_at, updated_at)
SELECT @rol_admin, m.id, NOW(), NOW() FROM menu m WHERE m.nombre = 'Cobertura CC' AND m.url = 'reportcoberturacc'
AND NOT EXISTS (SELECT 1 FROM menu_rol WHERE rol_id = @rol_admin AND menu_id = m.id);
