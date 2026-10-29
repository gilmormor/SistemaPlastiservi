# Manual de Usuario — Sistema Plastiservi

**Versión:** 1.0  
**Fecha:** Abril 2026  
**Sistema:** ERP Plastiservi (Laravel 6.2)

---

## Tabla de Contenidos

1. [Introducción](#1-introducción)
2. [Acceso al Sistema](#2-acceso-al-sistema)
3. [Panel de Inicio (Dashboard)](#3-panel-de-inicio-dashboard)
4. [Módulo de Clientes](#4-módulo-de-clientes)
5. [Módulo de Productos](#5-módulo-de-productos)
6. [Módulo de Cotizaciones](#6-módulo-de-cotizaciones)
7. [Módulo de Acuerdos Técnicos (AT)](#7-módulo-de-acuerdos-técnicos-at)
8. [Módulo de Notas de Venta (NV)](#8-módulo-de-notas-de-venta-nv)
9. [Módulo de Despachos](#9-módulo-de-despachos)
10. [Módulo de Inventario](#10-módulo-de-inventario)
11. [Módulo DTE — Documentos Tributarios Electrónicos](#11-módulo-dte--documentos-tributarios-electrónicos)
12. [Módulo de Reportes](#12-módulo-de-reportes)
13. [Módulo de No Conformidades](#13-módulo-de-no-conformidades)
14. [Administración del Sistema](#14-administración-del-sistema)
15. [Preguntas Frecuentes](#15-preguntas-frecuentes)

---

## 1. Introducción

El sistema **Plastiservi ERP** es una plataforma de gestión empresarial que cubre el ciclo completo de ventas y operaciones: desde la cotización hasta la facturación electrónica. Incluye control de inventario, despachos, documentos tributarios electrónicos (DTE) y reportes de gestión.

### Flujo Principal del Sistema

```
Cotización
    └──► Acuerdo Técnico (AT)
              └──► Nota de Venta (NV)
                        └──► Orden de Despacho
                                  └──► Guía de Despacho DTE
                                            └──► Factura DTE
```

> **Nota:** También es posible emitir **Facturas Directas** o **Guías Directas** sin pasar por el flujo completo de cotización.

---

## 2. Acceso al Sistema

### 2.1 Iniciar Sesión

1. Abra su navegador web e ingrese la dirección del sistema.
2. Ingrese su **RUT** (o nombre de usuario) y **contraseña**.
3. Haga clic en **Ingresar**.

### 2.2 Recuperar Contraseña

1. En la pantalla de login, haga clic en **"¿Olvidó su contraseña?"**.
2. Ingrese su correo electrónico registrado.
3. Recibirá un correo con instrucciones para restablecer su clave.

### 2.3 Cambiar Contraseña

1. Haga clic en su nombre de usuario en la esquina superior derecha.
2. Seleccione **"Cambiar Contraseña"**.
3. Ingrese la contraseña actual y la nueva contraseña dos veces.
4. Haga clic en **Guardar**.

### 2.4 Cerrar Sesión

Haga clic en su nombre de usuario (esquina superior derecha) y seleccione **"Cerrar Sesión"**.

---

## 3. Panel de Inicio (Dashboard)

Al ingresar al sistema, verá el **Panel de Inicio** con:

- **Resumen de cobranza**: Estado de créditos y deudas de clientes.
- **Indicadores comerciales**: Estadísticas de ventas y cotizaciones.
- **Indicadores operacionales**: Estado de despachos e inventario.
- **Notificaciones**: Alertas del sistema (cotizaciones pendientes de aprobación, documentos por revisar, etc.).

### Notificaciones

El ícono de campana en la barra superior muestra notificaciones activas. Al hacer clic puede ver el detalle y navegar directamente al documento relacionado.

---

## 4. Módulo de Clientes

### 4.1 Buscar un Cliente

1. En el menú principal, vaya a **Clientes**.
2. Utilice los filtros disponibles: RUT, nombre, ciudad, etc.
3. Haga clic en **Buscar**.
4. En los resultados, haga clic en el nombre del cliente para ver su ficha completa.

### 4.2 Crear un Cliente Nuevo

1. Vaya a **Clientes** → **Nuevo Cliente**.
2. Complete los datos requeridos:
   - RUT
   - Razón Social
   - Giro
   - Dirección principal
   - Teléfono y correo electrónico
   - Forma y plazo de pago
3. Haga clic en **Guardar**.

### 4.3 Editar un Cliente

1. Busque el cliente y abra su ficha.
2. Haga clic en **Editar**.
3. Modifique los datos necesarios y haga clic en **Guardar**.

### 4.4 Gestionar Direcciones de un Cliente

Cada cliente puede tener múltiples direcciones de entrega.

1. Abra la ficha del cliente.
2. En la sección **Direcciones**, haga clic en **Agregar Dirección**.
3. Ingrese la dirección, ciudad, comuna y cualquier referencia adicional.
4. Haga clic en **Guardar**.

### 4.5 Clientes Bloqueados

Algunos clientes pueden quedar bloqueados por deuda o decisión comercial. Si al intentar crear un documento para un cliente aparece el aviso de **"Cliente bloqueado"**, debe contactar al área de cobranza o administración para solicitar el desbloqueo.

---

## 5. Módulo de Productos

### 5.1 Buscar un Producto

1. En el menú, vaya a **Productos**.
2. Ingrese el nombre, código o categoría del producto.
3. Haga clic en **Buscar**.

### 5.2 Ver Ficha de un Producto

Al hacer clic en un producto, podrá ver:

- Código y nombre
- Categoría y giro
- Unidad de medida
- Descripción técnica (glosa)
- Stock actual por bodega
- Colores disponibles

### 5.3 Crear un Producto Nuevo

1. Vaya a **Productos** → **Nuevo Producto**.
2. Complete:
   - Código
   - Nombre y descripción (glosa)
   - Categoría
   - Unidad de medida
   - Color (si aplica)
3. Haga clic en **Guardar**.

> **Nota:** La asignación de precios se gestiona desde las tablas de valores por categoría (módulo de configuración).

---

## 6. Módulo de Cotizaciones

El módulo de cotizaciones es el punto de entrada del flujo de ventas.

### 6.1 Crear una Cotización

1. Vaya a **Cotizaciones** → **Nueva Cotización**.
2. Seleccione el **cliente** (use el buscador por RUT o nombre).
3. Complete los datos generales:
   - Fecha
   - Forma de pago
   - Plazo de pago
   - Condiciones de transporte (si aplica)
4. Agregue los productos:
   - Busque el producto por código o nombre.
   - Ingrese la cantidad y precio unitario.
   - Haga clic en **Agregar**.
5. Revise el total y haga clic en **Guardar**.

### 6.2 Enviar Cotización a Aprobación

Una vez creada la cotización, debe pasar por el flujo de aprobación:

1. Abra la cotización creada.
2. Haga clic en **Enviar a Aprobación**.
3. La cotización quedará en estado **"Pendiente de Aprobación"**.

### 6.3 Aprobar una Cotización

*Solo usuarios con rol de aprobador pueden realizar este paso.*

1. Vaya a **Cotizaciones** → **Cotizaciones a Aprobar**.
2. Seleccione la cotización pendiente.
3. Revise los datos y haga clic en **Aprobar** o **Rechazar**.
4. Si se rechaza, debe ingresar el motivo.

### 6.4 Aprobación del Cliente

Tras la aprobación interna, la cotización puede requerir aceptación del cliente:

1. Vaya a **Cotizaciones** → **Aprobadas por Cliente**.
2. Registre la aprobación del cliente (fecha y forma de confirmación).

### 6.5 Consultar Cotizaciones

1. Vaya a **Cotizaciones** → **Consulta de Cotizaciones**.
2. Filtre por fecha, cliente, estado o número de cotización.
3. Puede exportar los resultados o imprimir la cotización en PDF.

---

## 7. Módulo de Acuerdos Técnicos (AT)

El Acuerdo Técnico es un documento formal que respalda las condiciones comerciales y técnicas acordadas con el cliente.

### 7.1 Crear un Acuerdo Técnico

Los AT se generan a partir de una cotización aprobada.

1. Desde la cotización aprobada, haga clic en **Generar Acuerdo Técnico**.
2. Revise y complete los campos técnicos requeridos.
3. Haga clic en **Guardar**.

### 7.2 Enviar a Revisión

1. Abra el AT creado.
2. Haga clic en **Enviar a Revisión**.
3. El sistema notificará al revisor correspondiente.

### 7.3 Aprobar o Rechazar un AT

*Solo usuarios con rol de revisor de AT.*

1. Vaya a **Acuerdos Técnicos** → **AT a Aprobar**.
2. Revise el documento y haga clic en **Aprobar** o **Rechazar**.

### 7.4 Subir AT Firmado

Una vez que el AT ha sido firmado físicamente o digitalmente:

1. Abra el AT aprobado.
2. Haga clic en **Subir AT Firmado**.
3. Seleccione el archivo (PDF u otro formato) desde su equipo.
4. Haga clic en **Cargar**.

---

## 8. Módulo de Notas de Venta (NV)

La Nota de Venta formaliza el pedido del cliente y da inicio al proceso de despacho.

### 8.1 Crear una Nota de Venta

Las NV se generan a partir de una cotización con AT firmado.

1. Desde la cotización o el AT finalizado, haga clic en **Generar Nota de Venta**.
2. Revise los datos importados automáticamente (cliente, productos, precios).
3. Confirme las condiciones de transporte.
4. Haga clic en **Guardar**.

### 8.2 Aprobar una Nota de Venta

1. Vaya a **Notas de Venta** → **NV a Aprobar**.
2. Seleccione la NV pendiente.
3. Haga clic en **Aprobar** o **Rechazar** (con motivo en caso de rechazo).

### 8.3 Cerrar una Nota de Venta

Una NV se cierra cuando todos sus productos han sido despachados y facturados.

1. Vaya a la NV correspondiente.
2. Haga clic en **Cerrar NV**.

> **Atención:** El cierre es una acción definitiva. Verifique que todos los despachos y documentos asociados estén correctos antes de cerrar.

### 8.4 Anular una Nota de Venta

1. Abra la NV que desea anular.
2. Haga clic en **Anular**.
3. Ingrese el motivo de la anulación.
4. Confirme la operación.

### 8.5 Consultar Notas de Venta

1. Vaya a **Notas de Venta** → **Consulta de NV**.
2. Filtre por fecha, cliente, estado o número de NV.

---

## 9. Módulo de Despachos

### 9.1 Solicitud de Despacho

El proceso de despacho comienza con una solicitud generada desde la Nota de Venta.

1. Desde la NV aprobada, haga clic en **Generar Solicitud de Despacho**.
2. Indique los productos y cantidades a despachar.
3. Haga clic en **Guardar**.

### 9.2 Orden de Despacho

La Solicitud de Despacho aprobada genera una Orden de Despacho (OD).

1. Vaya a **Despachos** → **Órdenes de Despacho**.
2. Busque la OD correspondiente.
3. Puede ver el detalle de productos, destino y condiciones de entrega.

### 9.3 Recepción de Orden de Despacho

Cuando el cliente recibe la mercadería, se registra la recepción:

1. Abra la Orden de Despacho.
2. Haga clic en **Recepcionar**.
3. Confirme las cantidades recibidas o indique observaciones.
4. Haga clic en **Guardar**.

### 9.4 Anular una Orden de Despacho

1. Abra la OD que desea anular.
2. Haga clic en **Anular**.
3. Seleccione el motivo y confirme.

> **Nota:** No se pueden anular OD que ya tienen una Guía de Despacho o Factura DTE asociada.

### 9.5 Picking

El módulo de **Picking** permite al personal de bodega seleccionar y preparar los productos de una OD:

1. Vaya a **Despachos** → **Picking**.
2. Seleccione la OD asignada.
3. Marque los productos recolectados y sus cantidades.
4. Finalice el picking para que la OD quede lista para despachar.

---

## 10. Módulo de Inventario

### 10.1 Consultar Stock en Bodega

1. Vaya a **Inventario** → **Inventario en Bodega**.
2. Filtre por producto, categoría o bodega.
3. Verá el stock actual disponible.

### 10.2 Movimientos de Inventario

Todos los ingresos y salidas quedan registrados en el historial de movimientos:

1. Vaya a **Inventario** → **Movimientos**.
2. Filtre por fecha, tipo de movimiento, producto o bodega.
3. Puede ver el detalle de cada movimiento (quién lo hizo, cuándo y por qué razón).

**Tipos de movimiento comunes:**
- Entrada por compra o producción
- Salida por venta o despacho
- Ajuste de inventario
- Transferencia entre bodegas

### 10.3 Pesaje

El módulo de pesaje registra el peso de los productos al momento del despacho o recepción.

1. Vaya a **Inventario** → **Pesaje**.
2. Cree un nuevo registro de pesaje seleccionando el producto y la OD correspondiente.
3. Ingrese el peso medido.
4. Haga clic en **Guardar**.

### 10.4 Pesaje a Aprobar

Los registros de pesaje pueden requerir aprobación:

1. Vaya a **Inventario** → **Pesaje a Aprobar**.
2. Revise el registro y haga clic en **Aprobar** o **Rechazar**.

### 10.5 Apertura de Inventario por Mes

Al inicio de cada período contable se debe realizar la apertura de inventario:

1. Vaya a **Inventario** → **Apertura de Mes**.
2. Seleccione el período y la bodega.
3. Confirme la apertura.

> **Atención:** Consulte con el área de administración antes de realizar esta operación.

### 10.6 Insumos y Materia Prima

- **Insumos**: Materiales de apoyo utilizados en procesos.
- **Materia Prima**: Materiales principales para fabricación.
- **Materiales de Fabricación**: Control de materiales usados en producción.

Todos se gestionan de manera similar: búsqueda, creación, edición y consulta de stock.

---

## 11. Módulo DTE — Documentos Tributarios Electrónicos

> Los DTE son documentos con validez legal emitidos al SII (Servicio de Impuestos Internos de Chile).

### 11.1 Facturas DTE

#### Crear una Factura desde una Guía de Despacho

1. Vaya a **DTE** → **Facturas**.
2. Haga clic en **Nueva Factura**.
3. Seleccione la Guía de Despacho asociada.
4. Revise los datos importados automáticamente.
5. Haga clic en **Emitir Factura**.

#### Factura Directa (sin guía previa)

1. Vaya a **DTE** → **Facturas Directas**.
2. Haga clic en **Nueva Factura Directa**.
3. Seleccione el cliente y agregue los productos manualmente.
4. Haga clic en **Emitir Factura**.

> **Importante:** Los folios de documentos tributarios son limitados. Úselos con cuidado y evite emitir documentos de prueba en el ambiente de producción.

#### Factura Exenta

Para ventas sin IVA:

1. Vaya a **DTE** → **Facturas Exentas**.
2. El proceso es similar al de la Factura Directa, pero el sistema aplicará exención de IVA automáticamente.

### 11.2 Guías de Despacho DTE

#### Crear una Guía de Despacho

1. Vaya a **DTE** → **Guías de Despacho**.
2. Haga clic en **Nueva Guía**.
3. Seleccione la Orden de Despacho asociada.
4. Verifique los datos del transportista:
   - RUT del chofer
   - Patente del vehículo
   - Nombre del chofer
5. Haga clic en **Emitir Guía**.

#### Guía de Despacho Directa

1. Vaya a **DTE** → **Guías Directas**.
2. Complete los datos del cliente, productos y transportista.
3. Haga clic en **Emitir Guía**.

### 11.3 Notas de Crédito (NC)

Las Notas de Crédito corrigen o anulan facturas ya emitidas (por devoluciones, descuentos, errores).

1. Vaya a **DTE** → **Notas de Crédito**.
2. Haga clic en **Nueva Nota de Crédito**.
3. Seleccione la Factura a la que corresponde la NC.
4. Ingrese el motivo y el monto a revertir.
5. Haga clic en **Emitir**.

### 11.4 Notas de Débito (ND)

Las Notas de Débito aumentan el valor de una factura ya emitida (por ajustes de precio, cargos adicionales, etc.).

1. Vaya a **DTE** → **Notas de Débito**.
2. Haga clic en **Nueva Nota de Débito**.
3. Seleccione la Factura asociada.
4. Ingrese el motivo y el monto adicional.
5. Haga clic en **Emitir**.

### 11.5 Anular Documentos DTE

> **Atención:** La anulación de documentos DTE tiene implicancias legales y tributarias. Realice esta operación solo cuando sea estrictamente necesario y con autorización.

1. Abra el documento DTE que desea anular.
2. Haga clic en **Anular**.
3. Ingrese el motivo de anulación.
4. Confirme la operación.

> El sistema generará automáticamente la NC o ND correspondiente según el tipo de documento.

### 11.6 Reimprimir un DTE

1. Busque el documento en el listado correspondiente.
2. Haga clic en el ícono de impresión o en **Reimprimir**.
3. Se generará una copia en PDF para descargar o imprimir.

---

## 12. Módulo de Reportes

### 12.1 Reportes DTE

Acceda a **Reportes** → **DTE** para obtener listados de:

- **Facturas emitidas** por período, cliente o monto.
- **Guías de Despacho** emitidas.
- **Notas de Crédito** emitidas.
- **Notas de Débito** emitidas.

Todos los reportes pueden filtrarse por rango de fechas y exportarse a **PDF** o **Excel**.

### 12.2 Reportes de Despacho

- **Órdenes de Despacho**: Estado y detalle de OD por período.
- **Solicitudes de Despacho**: Solicitudes pendientes y procesadas.

### 12.3 Reportes de Inventario

- **Stock actual** por producto y bodega.
- **Movimientos de inventario** por período.
- **Pendientes de inventario**: Productos con stock bajo o sin movimiento.

### 12.4 Reportes de Ventas

- **Ventas por vendedor**: Resumen de ventas agrupado por ejecutivo de ventas.
- **Estadísticas de venta**: Gráficos y tablas de tendencias.
- **Deuda de clientes**: Estado de cuentas por cobrar.

### 12.5 Reportes de Pesaje

Listado de registros de pesaje por período, producto u OD.

### 12.6 Indicadores de Gestión

El módulo de **Indicadores** muestra dashboards con:

- Indicadores comerciales (cotizaciones, ventas, clientes nuevos).
- Indicadores operacionales (despachos, inventario, tiempos de proceso).

---

## 13. Módulo de No Conformidades

Las No Conformidades registran problemas de calidad detectados en cualquier etapa del proceso.

### 13.1 Registrar una No Conformidad

1. Vaya a **Calidad** → **No Conformidades** → **Nueva NC**.
2. Complete:
   - Fecha de detección
   - Forma de detección
   - Motivo / categoría del problema
   - Descripción del problema
   - Área o proceso involucrado
3. Si corresponde, adjunte imágenes del problema.
4. Haga clic en **Guardar**.

### 13.2 Validar una No Conformidad

*Solo usuarios con rol de validador de calidad.*

1. Vaya a **No Conformidades** → **NC a Validar**.
2. Revise el registro y haga clic en **Validar** o **Rechazar**.

### 13.3 Verificar / Cerrar una No Conformidad

Una vez resuelta la causa del problema:

1. Abra la NC correspondiente.
2. Haga clic en **Verificar** y registre las acciones correctivas tomadas.
3. Haga clic en **Cerrar NC**.

### 13.4 No Conformidades en Recepción

Si el problema se detecta al recibir mercadería de un proveedor:

1. Vaya a **No Conformidades** → **NC en Recepción**.
2. Complete los datos del proveedor, producto y descripción del problema.

---

## 14. Administración del Sistema

> Esta sección es solo para usuarios con rol de **Administrador**.

### 14.1 Gestión de Usuarios

1. Vaya a **Administración** → **Usuarios**.
2. Puede **crear**, **editar** o **desactivar** usuarios.
3. Al crear un usuario, asigne un **rol** que define los accesos disponibles.

**Campos requeridos al crear un usuario:**
- RUT
- Nombre completo
- Correo electrónico
- Rol
- Contraseña inicial

### 14.2 Gestión de Roles

Los roles agrupan permisos y accesos al menú del sistema.

1. Vaya a **Administración** → **Roles**.
2. Cree o edite roles según las necesidades de la organización.
3. Asigne los menús y permisos correspondientes a cada rol.

### 14.3 Menú del Sistema

El menú es dinámico y configurable por rol:

1. Vaya a **Administración** → **Menú**.
2. Configure la estructura de menú (hasta 5 niveles jerárquicos).
3. En **Menú por Rol**, asigne qué opciones de menú puede ver cada rol.

### 14.4 Configuración General

Opciones disponibles en **Configuración**:

- **Empresa**: Datos de la empresa (RUT, razón social, dirección, logo).
- **Sucursales**: Crear y gestionar sucursales.
- **Áreas y Jefaturas**: Organigrama del sistema.
- **Formas y Plazos de Pago**: Catálogos de condiciones comerciales.
- **Unidades de Medida**: Definición de unidades.
- **Tipos de Traslado**: Formas de transporte disponibles.
- **Foliocontrol**: Control y asignación de folios DTE.

### 14.5 Bitácora del Sistema

Para auditoría y control:

1. Vaya a **Administración** → **Bitácora**.
2. Consulte el registro de acciones de los usuarios: quién hizo qué y cuándo.

---

## 15. Preguntas Frecuentes

**¿Qué hago si un cliente está bloqueado y necesito emitirle un documento?**  
Contacte al área de administración o cobranza para solicitar el desbloqueo. Solo usuarios autorizados pueden desbloquear clientes.

**¿Puedo editar una factura DTE ya emitida?**  
No es posible modificar un DTE ya emitido al SII. Si hay un error, debe emitir una **Nota de Crédito** para anular el documento y luego emitir uno nuevo con los datos correctos.

**¿Qué pasa si se agotan los folios DTE?**  
Contacte al administrador del sistema. Los folios deben solicitarse al SII con anticipación.

**¿Por qué no puedo cerrar una Nota de Venta?**  
Verifique que todos los despachos asociados estén completos y que todos los documentos DTE (guías y facturas) hayan sido emitidos correctamente.

**¿Cómo sé si una cotización fue aprobada?**  
Puede consultar el estado en **Cotizaciones** → **Consulta de Cotizaciones**. El estado mostrará: Pendiente, Aprobada o Rechazada.

**¿Puedo exportar los reportes a Excel?**  
Sí. La mayoría de los reportes tienen un botón de exportación a Excel (`.xlsx`) o PDF.

**¿Qué es el Asistente IA?**  
Es una herramienta de consulta integrada al sistema que permite hacer preguntas sobre los datos del sistema en lenguaje natural. Acceda desde el menú **Herramientas** → **Asistente IA**.

---

*Manual generado para uso interno de Plastiservi. Para soporte técnico, contacte al administrador del sistema.*
