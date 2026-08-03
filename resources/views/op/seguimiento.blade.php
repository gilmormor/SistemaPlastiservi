@extends("theme.$theme.layout")
@section('titulo') Seguimiento de Producción @endsection

@section('scripts')
    <script src="{{autoVer('assets/pages/scripts/general.js')}}" type="text/javascript"></script>
    <script src="{{autoVer('assets/pages/scripts/op/seguimiento.js')}}" type="text/javascript"></script>
@endsection

@section('contenido')

{{-- ── ESTILOS DE PANTALLA ───────────────────────────────────────────────────── --}}
<style>
/* ── Cabecera de página ── */
.seg-header {
    background: linear-gradient(135deg, #1a3a5c 0%, #2c5f8a 100%);
    border-radius: 6px;
    padding: 16px 22px;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.seg-header h2 {
    color: #fff;
    font-size: 17px;
    font-weight: 600;
    margin: 0;
    letter-spacing: .3px;
}
.seg-header .seg-breadcrumb {
    color: rgba(255,255,255,.65);
    font-size: 12px;
    margin-top: 3px;
}

/* ── Tarjeta de filtros ── */
.seg-filtros {
    background: #fff;
    border: 1px solid #e3e8ef;
    border-radius: 6px;
    padding: 14px 18px 10px;
    margin-bottom: 16px;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
}
.seg-filtros label {
    font-size: 11px;
    font-weight: 600;
    color: #5a6a7e;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 4px;
    display: block;
}
.seg-filtros .form-control {
    border-radius: 4px;
    border-color: #d0d9e4;
    font-size: 13px;
    height: 32px;
    padding: 4px 10px;
}
.seg-filtros .btn-buscar {
    background: #2c5f8a;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 6px 20px;
    font-size: 13px;
    font-weight: 600;
    height: 32px;
    line-height: 1;
    transition: background .2s;
}
.seg-filtros .btn-buscar:hover { background: #1a3a5c; }

/* ── KPIs rápidos ── */
.seg-kpis { margin-bottom: 16px; }
.seg-kpi-card {
    background: #fff;
    border: 1px solid #e3e8ef;
    border-radius: 6px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    transition: box-shadow .2s;
}
.seg-kpi-card:hover { box-shadow: 0 3px 10px rgba(0,0,0,.1); }
.seg-kpi-icon {
    width: 38px; height: 38px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; color: #fff;
    flex-shrink: 0;
}
.seg-kpi-icon.blue   { background: #2c5f8a; }
.seg-kpi-icon.green  { background: #27ae60; }
.seg-kpi-icon.orange { background: #e67e22; }
.seg-kpi-icon.red    { background: #e74c3c; }
.seg-kpi-val  { font-size: 22px; font-weight: 700; color: #1a3a5c; line-height: 1; }
.seg-kpi-lbl  { font-size: 11px; color: #8a99aa; font-weight: 500; margin-top: 2px; }

/* ── Leyenda de estados ── */
.seg-leyenda {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 12px;
    align-items: center;
}
.seg-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 500;
    color: #4a5568;
    background: #f7f9fc;
    border: 1px solid #e3e8ef;
    border-radius: 20px;
    padding: 3px 10px;
}
.seg-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}

/* ── Tabla principal ── */
.seg-tabla-wrap {
    background: #fff;
    border: 1px solid #e3e8ef;
    border-radius: 6px;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    overflow: hidden;
}
#tabla-seguimiento thead th {
    background: #f0f4f8;
    color: #3a4a5c;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    border-bottom: 2px solid #d0d9e4;
    padding: 10px 12px;
    white-space: nowrap;
}
#tabla-seguimiento tbody td {
    font-size: 12px;
    color: #2d3748;
    padding: 9px 12px;
    vertical-align: middle;
    border-bottom: 1px solid #f0f4f8;
}
#tabla-seguimiento tbody tr:hover td { background: #f8fafd; }
#tabla-seguimiento tbody tr.odd td  { background: #fefefe; }
#tabla-seguimiento tbody tr.even td { background: #f9fbfd; }

/* Barra de progreso de etapas */
.seg-progress-wrap { min-width: 120px; }
.seg-progress-bar-bg {
    height: 6px; border-radius: 3px; background: #e3e8ef;
    overflow: hidden; margin-bottom: 3px;
}
.seg-progress-bar-fill {
    height: 100%; border-radius: 3px;
    transition: width .4s ease;
}
.seg-progress-label { font-size: 10px; color: #6b7a8d; line-height: 1; }

/* Chips de estado */
.seg-chip {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 10px; font-weight: 600;
    border-radius: 12px; padding: 2px 8px;
    white-space: nowrap;
    transition: box-shadow 0.15s ease, filter 0.15s ease, transform 0.15s ease;
}
.seg-chip.green  { background: #e8f8ee; color: #1e8449; }
.seg-chip.orange { background: #fef3e2; color: #b7600a; }
.seg-chip.blue   { background: #eaf3fb; color: #1a5e9a; }
.seg-chip.red    { background: #fde8e8; color: #a93226; }
.seg-chip.gray   { background: #f0f4f8; color: #6b7a8d; }

/* ── Efecto cadena al hacer hover sobre un chip de trazabilidad ── */
/* Chip directamente hovered */
/* data-chain: Trazabilidad Despacho | data-lote: Trazabilidad Etapas (mismo efecto).
   [data-lote] cubre los chips Viene de/Alimenta a y el enlace apr-XX del registro. */
/* Manita (pointer) en todos los elementos que participan del spotlight de cadena */
[data-lote] { cursor: pointer; }
.seg-chip[data-chain]:hover,
[data-lote]:hover {
    filter: brightness(1.18);
    transform: translateY(-2px) scale(1.04);
    box-shadow: 0 4px 12px rgba(0,0,0,0.28);
    z-index: 2; position: relative;
}
/* Chips de la cadena activa (antecesores y sucesores) */
.seg-chip[data-chain].traz-activo,
[data-lote].traz-activo {
    filter: brightness(1.12);
    transform: translateY(-1px);
    box-shadow: 0 0 0 2.5px rgba(0,0,0,0.45), 0 2px 8px rgba(0,0,0,0.2);
    z-index: 1; position: relative;
}
/* Chips fuera de la cadena — se atenúan (efecto spotlight) */
.seg-chip[data-chain].traz-inactivo,
[data-lote].traz-inactivo {
    opacity: 0.28;
    filter: grayscale(0.5);
}
/* El enlace apr-XX (no es chip): redondeo y aire para que el anillo se vea bien */
a[data-lote].traz-activo,
a[data-lote]:hover {
    border-radius: 10px;
    padding: 1px 5px;
    background: #fff;
    display: inline-block;
}

/* ── Detalle expandido por etapa ── */
.seg-etapa-card {
    background: #fff;
    border: 1px solid #e3e8ef;
    border-left: 4px solid #ccc;
    border-radius: 0 6px 6px 0;
    margin: 6px 0;
    overflow: hidden;
}
.seg-etapa-header {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 14px;
    background: #f7f9fc;
    border-bottom: 1px solid #e8edf3;
    flex-wrap: wrap;
}
.seg-etapa-nombre { font-weight: 700; font-size: 13px; color: #1a3a5c; }
.seg-etapa-meta   { font-size: 11px; color: #8a99aa; }
.seg-etapa-body   { padding: 8px 14px; }

/* Timeline de registros */
.seg-timeline { list-style: none; padding: 0; margin: 0; }
.seg-timeline li {
    display: grid;
    grid-template-columns: 80px 110px 90px 80px 80px 80px 140px 1fr;
    gap: 4px;
    align-items: center;
    padding: 5px 8px;
    border-radius: 4px;
    font-size: 11px;
    margin-bottom: 2px;
}
.seg-timeline li:hover { background: #f5f8fc; }
.seg-timeline li.aprobado { background: #f0fff4; }
.seg-tl-header {
    font-size: 10px; font-weight: 700;
    color: #8a99aa; text-transform: uppercase;
    letter-spacing: .4px;
    padding: 3px 8px;
    display: grid;
    grid-template-columns: 80px 110px 90px 80px 80px 80px 140px 1fr;
    gap: 4px;
}

/* ── Modal etiqueta ── */
.modal-etiqueta .modal-header {
    background: linear-gradient(135deg, #1a3a5c, #2c5f8a);
    color: #fff; border-radius: 6px 6px 0 0;
}
.modal-etiqueta .modal-header .close { color: #fff; opacity: .8; }

/* ── Responsive ── */
@media (max-width: 768px) {
    .seg-kpi-card { padding: 10px 12px; }
    .seg-kpi-val  { font-size: 18px; }
    .seg-timeline li, .seg-tl-header { grid-template-columns: 60px 1fr 70px 60px 110px; }
}
</style>

{{-- ── CABECERA ─────────────────────────────────────────────────────────────── --}}
<div class="seg-header">
    <div>
        <h2><i class="fa fa-bar-chart"></i>&nbsp; Seguimiento de Órdenes de Producción</h2>
        <div class="seg-breadcrumb">
            <i class="fa fa-home"></i> Inicio &rsaquo; Producción &rsaquo; Seguimiento OPs
        </div>
    </div>
    <a href="{{route('otitemprogramacion')}}" class="btn btn-sm"
       style="background:rgba(255,255,255,.15); color:#fff; border:1px solid rgba(255,255,255,.3); border-radius:4px; font-size:12px;">
        <i class="fa fa-reply"></i> Volver a Programación
    </a>
</div>

{{-- ── KPIs ─────────────────────────────────────────────────────────────────── --}}
<div class="row seg-kpis">
    <div class="col-xs-6 col-sm-3">
        <div class="seg-kpi-card">
            <div class="seg-kpi-icon blue"><i class="fa fa-list-alt"></i></div>
            <div>
                <div class="seg-kpi-val" id="kpi-total">—</div>
                <div class="seg-kpi-lbl">Total OPs</div>
            </div>
        </div>
    </div>
    <div class="col-xs-6 col-sm-3">
        <div class="seg-kpi-card">
            <div class="seg-kpi-icon green"><i class="fa fa-check-circle"></i></div>
            <div>
                <div class="seg-kpi-val" id="kpi-completas">—</div>
                <div class="seg-kpi-lbl">Completas</div>
            </div>
        </div>
    </div>
    <div class="col-xs-6 col-sm-3">
        <div class="seg-kpi-card">
            <div class="seg-kpi-icon orange"><i class="fa fa-spinner"></i></div>
            <div>
                <div class="seg-kpi-val" id="kpi-parciales">—</div>
                <div class="seg-kpi-lbl">En proceso</div>
            </div>
        </div>
    </div>
    <div class="col-xs-6 col-sm-3">
        <div class="seg-kpi-card">
            <div class="seg-kpi-icon red"><i class="fa fa-clock-o"></i></div>
            <div>
                <div class="seg-kpi-val" id="kpi-pendientes">—</div>
                <div class="seg-kpi-lbl">Con pendientes</div>
            </div>
        </div>
    </div>
</div>

{{-- ── FILTROS ──────────────────────────────────────────────────────────────── --}}
@csrf
<div class="seg-filtros">
    <div class="row" style="align-items:flex-end; display:flex; flex-wrap:wrap;">
        <div class="col-xs-6 col-sm-2">
            <label><i class="fa fa-calendar"></i> Fecha inicio</label>
            <input type="text" class="form-control datepicker" id="fechad" placeholder="DD/MM/AAAA" readonly/>
        </div>
        <div class="col-xs-6 col-sm-2">
            <label><i class="fa fa-calendar"></i> Fecha fin</label>
            <input type="text" class="form-control datepicker" id="fechah" placeholder="DD/MM/AAAA" readonly/>
        </div>
        <div class="col-xs-4 col-sm-1">
            <label>Nº OP</label>
            <input type="text" class="form-control numerico-entero" id="op_id" placeholder="OP"/>
        </div>
        <div class="col-xs-4 col-sm-1">
            <label>Nº OT</label>
            <input type="text" class="form-control numerico-entero" id="ot_id" placeholder="OT"/>
        </div>
        <div class="col-xs-4 col-sm-1">
            <label>Nº NV</label>
            <input type="text" class="form-control numerico-entero" id="nv_id" placeholder="NV"/>
        </div>
        <div class="col-xs-6 col-sm-2">
            <label>Cód. Producto</label>
            <input type="text" class="form-control numerico-entero" id="producto_id" placeholder="Código"/>
        </div>
        <div class="col-xs-6 col-sm-3" style="display:flex; align-items:flex-end; padding-top:10px;">
            <button type="button" id="btnconsultar" class="btn-buscar" style="width:100%;">
                <i class="fa fa-search"></i>&nbsp; Consultar
            </button>
        </div>
    </div>
</div>

{{-- ── LEYENDA + TABLA ──────────────────────────────────────────────────────── --}}
<div class="seg-tabla-wrap">
    <div style="padding:12px 16px 4px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
        <div class="seg-leyenda">
            <span class="seg-badge"><span class="seg-dot" style="background:#27ae60;"></span>Completa</span>
            <span class="seg-badge"><span class="seg-dot" style="background:#f39c12;"></span>En proceso</span>
            <span class="seg-badge"><span class="seg-dot" style="background:#3498db;"></span>Esp. supervisor</span>
            <span class="seg-badge"><span class="seg-dot" style="background:#e74c3c;"></span>Rechazado</span>
            <span class="seg-badge"><span class="seg-dot" style="background:#bdc3c7;"></span>Sin iniciar</span>
        </div>
        {{-- Check modo fijado del spotlight de trazabilidad (preferencia en localStorage) --}}
        <label for="chk-fijar-traz" style="font-size:11px; color:#5a6a7e; font-weight:normal; cursor:pointer; margin:0 10px 0 0;"
               title="Marcado: la iluminación de la cadena queda fija al quitar el mouse (clic en zona vacía para limpiar). Desmarcado: se limpia automáticamente al quitar el mouse.">
            <input type="checkbox" id="chk-fijar-traz" style="cursor:pointer; vertical-align:middle; margin:0 3px 2px 0;">
            <i class="fa fa-thumb-tack"></i> Fijar iluminación trazabilidad
        </label>
        <small style="color:#aaa; font-size:11px;"><i class="fa fa-info-circle"></i> Clic en <i class="fa fa-plus-circle text-primary"></i> para ver etapas</small>
    </div>
    <div class="table-responsive" style="padding:0 4px 12px;">
        <table id="tabla-seguimiento"
               class="table display table-hover table-condensed"
               data-page-length="25"
               style="width:100%;">
            <tfoot></tfoot>
        </table>
    </div>
</div>

{{-- ── MODAL ETIQUETA ───────────────────────────────────────────────────────── --}}
@include('generales.modaletiquetaetapa')

@include('generales.modalpdf')

@endsection
