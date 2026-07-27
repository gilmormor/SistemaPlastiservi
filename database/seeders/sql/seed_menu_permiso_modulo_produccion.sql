-- Seed: permisos y menús del Módulo Producción faltantes en destino
-- Generado comparando biblioteca (origen) vs plxrevisarcierregd (referencia) el 2026-07-27
-- Idempotente: usa NOT EXISTS por slug (permiso) y nombre+url (menu). No incluye permiso_rol/menu_rol ni el rol 'Produccion SE'.

SET NAMES utf8mb4;

-- ============ PERMISOS ============
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar OT', 'listar-ot', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-ot');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Crear OT', 'crear-ot', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'crear-ot');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Editar OT', 'editar-ot', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'editar-ot');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Guardar OT', 'guardar-ot', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'guardar-ot');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Eliminar OT', 'eliminar-ot', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'eliminar-ot');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar OTNV', 'listar-otnv', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-otnv');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Crear OTNV', 'crear-otnv', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'crear-otnv');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Editar OTNV', 'editar-otnv', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'editar-otnv');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Guardar OTNV', 'guardar-otnv', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'guardar-otnv');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Eliminar OTNV', 'eliminar-otnv', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'eliminar-otnv');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Ver pdf ot', 'ver-pdf-ot', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'ver-pdf-ot');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar Reporte OT', 'listar-reporte-ot', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-reporte-ot');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar Aprobar OT', 'listar-aprobar-ot', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-aprobar-ot');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar Envia Item OT Prog', 'listar-envia-item-ot-prog', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-envia-item-ot-prog');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar Programacion Item OT', 'listar-programacion-item-ot', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-programacion-item-ot');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar Maquina Grupo', 'listar-maquina-grupo', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-maquina-grupo');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Crear Maquina Grupo', 'crear-maquina-grupo', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'crear-maquina-grupo');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Editar Maquina Grupo', 'editar-maquina-grupo', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'editar-maquina-grupo');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Guardar Maquina Grupo', 'guardar-maquina-grupo', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'guardar-maquina-grupo');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Eliminar Maquina Grupo', 'eliminar-maquina-grupo', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'eliminar-maquina-grupo');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar Maquina', 'listar-maquina', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-maquina');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Crear Maquina', 'crear-maquina', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'crear-maquina');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Editar Maquina', 'editar-maquina', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'editar-maquina');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Guardar Maquina', 'guardar-maquina', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'guardar-maquina');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Eliminar Maquina', 'eliminar-maquina', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'eliminar-maquina');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar Atributo', 'listar-atributo', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-atributo');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Crear Atributo', 'crear-atributo', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'crear-atributo');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Editar Atributo', 'editar-atributo', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'editar-atributo');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Guardar Atributo', 'guardar-atributo', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'guardar-atributo');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Eliminar Atributo', 'eliminar-atributo', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'eliminar-atributo');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar Operario', 'listar-operario', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-operario');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Crear Operario', 'crear-operario', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'crear-operario');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Editar Operario', 'editar-operario', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'editar-operario');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Guardar Operario', 'guardar-operario', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'guardar-operario');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Eliminar Operario', 'eliminar-operario', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'eliminar-operario');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar Area Produccion Suc Etapa Prod', 'listar-area-produccion-suc-etapa-prod', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-area-produccion-suc-etapa-prod');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar Etapa de Produccion', 'listar-etapa-de-produccion', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-etapa-de-produccion');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Crear Etapa de Produccion', 'crear-etapa-de-produccion', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'crear-etapa-de-produccion');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Editar Etapa de Produccion', 'editar-etapa-de-produccion', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'editar-etapa-de-produccion');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Guardar Etapa de Produccion', 'guardar-etapa-de-produccion', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'guardar-etapa-de-produccion');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Eliminar Etapa de Produccion', 'eliminar-etapa-de-produccion', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'eliminar-etapa-de-produccion');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Editar Area Produccion Suc Fase', 'editar-area-produccion-suc-fase', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'editar-area-produccion-suc-fase');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Editar Area Produccion Suc Etapa Prod', 'editar-area-produccion-suc-etapa-prod', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'editar-area-produccion-suc-etapa-prod');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Guardar Area Produccion Suc Etapa Prod', 'guardar-area-produccion-suc-etapa-prod', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'guardar-area-produccion-suc-etapa-prod');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar Acuerdo Tecnico Etapa de Produccion', 'listar-acuerdo-tecnico-etapa-de-produccion', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-acuerdo-tecnico-etapa-de-produccion');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Editar Acuerdo Tecnico Etapa de Produccion', 'editar-acuerdo-tecnico-etapa-de-produccion', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'editar-acuerdo-tecnico-etapa-de-produccion');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Guardar Acuerdo Tecnico Etapa de Produccion', 'guardar-acuerdo-tecnico-etapa-de-produccion', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'guardar-acuerdo-tecnico-etapa-de-produccion');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Editar etapas productivas en Programar produccion', 'editar-etapas-productivas-en-programar-produccion', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'editar-etapas-productivas-en-programar-produccion');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar Registro Produccion', 'listar-registro-produccion', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-registro-produccion');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Crear Registro Produccion', 'crear-registro-produccion', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'crear-registro-produccion');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Editar Registro Produccion', 'editar-registro-produccion', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'editar-registro-produccion');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Guardar Registro Produccion', 'guardar-registro-produccion', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'guardar-registro-produccion');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Eliminar Registro Produccion', 'eliminar-registro-produccion', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'eliminar-registro-produccion');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar Persona EtapaProd', 'listar-persona-etapaprod', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-persona-etapaprod');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Editar Persona EtapaProd', 'editar-persona-etapaprod', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'editar-persona-etapaprod');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Guardar Persona EtapaProd', 'guardar-persona-etapaprod', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'guardar-persona-etapaprod');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar EtapasProd x Persona', 'listar-etapasprod-x-persona', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-etapasprod-x-persona');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar invbodegatipo', 'listar-invbodegatipo', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-invbodegatipo');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Crear invbodegatipo', 'crear-invbodegatipo', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'crear-invbodegatipo');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Editar invbodegatipo', 'editar-invbodegatipo', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'editar-invbodegatipo');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Guardar invbodegatipo', 'guardar-invbodegatipo', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'guardar-invbodegatipo');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Eliminar invbodegatipo', 'eliminar-invbodegatipo', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'eliminar-invbodegatipo');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar Registro Produccion Aprobar Supervidor', 'listar-registro-produccion-aprobar-supervidor', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-registro-produccion-aprobar-supervidor');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Ver pdf OP', 'ver-pdf-op', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'ver-pdf-op');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Cambiar estatus Requiere Fabricacion Nota de venta', 'cambiar-estatus-requiere-fabricacion-nota-de-venta', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'cambiar-estatus-requiere-fabricacion-nota-de-venta');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar  ccparam', 'listar--ccparam', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar--ccparam');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Crear ccparam', 'crear-ccparam', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'crear-ccparam');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Editar ccparam', 'editar-ccparam', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'editar-ccparam');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Guardar ccparam', 'guardar-ccparam', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'guardar-ccparam');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Eliminar ccparam', 'eliminar-ccparam', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'eliminar-ccparam');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar Parametros CC', 'listar-ccparam', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-ccparam');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Actualizar Parametros CC', 'actualizar-ccparam', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'actualizar-ccparam');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar Param CC por Etapa', 'listar-ccparam-apsucetapaprod', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-ccparam-apsucetapaprod');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Guardar Param CC por Etapa', 'guardar-ccparam-apsucetapaprod', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'guardar-ccparam-apsucetapaprod');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Actualizar Param CC por Etapa', 'actualizar-ccparam-apsucetapaprod', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'actualizar-ccparam-apsucetapaprod');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Eliminar Param CC por Etapa', 'eliminar-ccparam-apsucetapaprod', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'eliminar-ccparam-apsucetapaprod');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Ver PDF Muestra CC', 'ver-pdf-muestra-cc', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'ver-pdf-muestra-cc');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Ver Etiqueta Registro Producción', 'ver-etiqueta-regprod', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'ver-etiqueta-regprod');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Supervisar CC Muestras', 'supervisar-ccregistmuestra', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'supervisar-ccregistmuestra');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar CC Desbloqueo Muestras', 'listar-cc-muestra-desbloqueo', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-cc-muestra-desbloqueo');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Guardar CC Desbloqueo Muestras', 'guardar-cc-muestra-desbloqueo', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'guardar-cc-muestra-desbloqueo');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Listar Reporte CC Muestra', 'listar-reporte-cc-muestra', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'listar-reporte-cc-muestra');
INSERT INTO permiso (nombre, slug, created_at, updated_at)
SELECT 'Reporte CC por Lote', 'reporte-cc-lote', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE slug = 'reporte-cc-lote');

-- ============ MENU ============
-- Orden topológico: nivel 1 (Produccion, padre=185 ya existente) -> nivel 2 (hijos directos de Produccion, incluye los 3 padres intermedios Maquinas/Registro de Produccion/Control de Calidad) -> nivel 3 (hijos de esos 3 padres intermedios). Mas 4 filas independientes (hijas de menus ya existentes 58/107, y una raiz nueva).

INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT 58, 'Bloquear Cliente', 'clientebloqueado', 3, 'fa-lock', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Bloquear Cliente' AND url = 'clientebloqueado');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT 107, 'Inv Stock Vend', 'reportinvstockvend', 5, 'fa-map-o', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Inv Stock Vend' AND url = 'reportinvstockvend');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT 58, 'Desbloquear Cliente', 'clientedesbloqueado', 7, 'fa-unlock', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Desbloquear Cliente' AND url = 'clientedesbloqueado');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT 58, 'Desbloquear Cliente Pro', 'clientedesbloqueadopro', 8, 'fa-unlock text-blue', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Desbloquear Cliente Pro' AND url = 'clientedesbloqueadopro');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT 0, 'Tipo Bodega', 'invbodegatipo', 1, 'fa-square-o', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Tipo Bodega' AND url = 'invbodegatipo');

-- Nivel 1: Produccion (padre=185, ya existe igual en ambos ambientes)
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT 185, 'Produccion', '#', 3, 'fa-industry', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Produccion' AND url = '#');
SET @id_produccion = (SELECT id FROM menu WHERE nombre='Produccion' AND url='#' LIMIT 1);

-- Nivel 2: hijos directos de Produccion (usa @id_produccion como menu_id)
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_produccion, 'Orden Trabajo NV', 'otnv', 7, 'fa-cog', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Orden Trabajo NV' AND url = 'otnv');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_produccion, 'Programacion', 'otitemprogramacion', 12, 'fa-calendar', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Programacion' AND url = 'otitemprogramacion');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_produccion, 'Cargar Materia Prima', '#', 16, 'fa-dropbox', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Cargar Materia Prima' AND url = '#');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_produccion, 'Extrusión', '#', 14, 'fa-cogs', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Extrusión' AND url = '#');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_produccion, 'Orden Trabajo', 'ot', 6, 'fa-cog text-aqua', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Orden Trabajo' AND url = 'ot');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_produccion, 'Orden Trabajo Rep', 'reportot', 8, 'fa-file-pdf-o', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Orden Trabajo Rep' AND url = 'reportot');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_produccion, 'Aprobar OT', 'otaprobar', 9, 'fa-thumbs-o-up', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Aprobar OT' AND url = 'otaprobar');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_produccion, 'Envia Item OT a Prog.', 'otitemenvprogprod', 10, 'fa-industry', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Envia Item OT a Prog.' AND url = 'otitemenvprogprod');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_produccion, 'Operario', 'operario', 5, 'fa-odnoklassniki', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Operario' AND url = 'operario');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_produccion, 'Etapa Prod x Area Produc', 'areaproduccionsucetapaprod', 2, 'fa-sort-amount-asc', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Etapa Prod x Area Produc' AND url = 'areaproduccionsucetapaprod');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_produccion, 'Etapas de Produccion', 'etapaprod', 1, 'fa-list-ol', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Etapas de Produccion' AND url = 'etapaprod');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_produccion, 'AT Etapas Produccion', 'acuerdotecnicoetapaprod', 3, 'fa-list-ol', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'AT Etapas Produccion' AND url = 'acuerdotecnicoetapaprod');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_produccion, 'Persona Etapa Produccion', 'personaetapaprod', 4, 'fa-user', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Persona Etapa Produccion' AND url = 'personaetapaprod');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_produccion, 'Aprobar Registro Produccion', 'opdetregprodtempaprobsup', 14, 'fa-thumbs-o-up', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Aprobar Registro Produccion' AND url = 'opdetregprodtempaprobsup');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_produccion, 'Trazabilidad OP', 'op/seguimiento', 15, 'fa-share-alt', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Trazabilidad OP' AND url = 'op/seguimiento');

-- Nivel 2 (padres intermedios, también hijos de Produccion): Maquinas, Registro de Produccion, Control de Calidad
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_produccion, 'Maquinas', '#', 11, 'fa-stack-exchange', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Maquinas' AND url = '#');
SET @id_maquinas = (SELECT id FROM menu WHERE nombre='Maquinas' AND url='#' LIMIT 1);
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_produccion, 'Registro de Produccion', 'opdetregprodtemp', 13, 'fa-industry', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Registro de Produccion' AND url = 'opdetregprodtemp');
SET @id_regprod = (SELECT id FROM menu WHERE nombre='Registro de Produccion' AND url='opdetregprodtemp' LIMIT 1);
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_produccion, 'Control de Calidad', '#', 17, 'fa-certificate', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Control de Calidad' AND url = '#');
SET @id_cc = (SELECT id FROM menu WHERE nombre='Control de Calidad' AND url='#' LIMIT 1);

-- Nivel 3: hijos de Maquinas
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_maquinas, 'Maquina Grupo', 'maquinagrupo', 1, 'fa-trello', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Maquina Grupo' AND url = 'maquinagrupo');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_maquinas, 'Maquina', 'maquina', 2, 'fa-simplybuilt', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Maquina' AND url = 'maquina');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_maquinas, 'Atributos', 'atributo', 3, 'fa-stack-overflow', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Atributos' AND url = 'atributo');

-- Nivel 3: hijos de Registro de Produccion
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_regprod, 'Mezclas', 'opdetregprodtemp/set/1', 1, 'fa-magic', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Mezclas' AND url = 'opdetregprodtemp/set/1');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_regprod, 'Mezclado', 'opdetregprodtemp/set/2', 2, 'fa-retweet', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Mezclado' AND url = 'opdetregprodtemp/set/2');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_regprod, 'Extrusion.', 'opdetregprodtemp/set/3', 3, 'fa-opera', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Extrusion.' AND url = 'opdetregprodtemp/set/3');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_regprod, 'Impresion', 'opdetregprodtemp/set/4', 4, 'fa-print', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Impresion' AND url = 'opdetregprodtemp/set/4');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_regprod, 'Sellado', 'opdetregprodtemp/set/5', 5, 'fa-flickr', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Sellado' AND url = 'opdetregprodtemp/set/5');

-- Nivel 3: hijos de Control de Calidad
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_cc, '🔬 Reg Muestra', 'ccregistmuestra', 1, 'fa fa-flask', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = '🔬 Reg Muestra' AND url = 'ccregistmuestra');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_cc, 'Supervisar Muestra CC', 'ccmuestrasuper', 2, 'fa fa-check-square-o', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Supervisar Muestra CC' AND url = 'ccmuestrasuper');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_cc, 'Desbloquear CC Muestra', 'ccdesbloqueo', 3, 'fa fa-unlock text-blue', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Desbloquear CC Muestra' AND url = 'ccdesbloqueo');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_cc, 'CC Muestra', 'reportccmuestra', 4, 'fa-file-pdf-o', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'CC Muestra' AND url = 'reportccmuestra');
INSERT INTO menu (menu_id, nombre, url, orden, icono, created_at, updated_at)
SELECT @id_cc, 'Cobertura CC', 'reportcoberturacc', 5, 'fa fa-shield', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM menu WHERE nombre = 'Cobertura CC' AND url = 'reportcoberturacc');
