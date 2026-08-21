@extends("theme.$theme.layout")
@section('titulo') Trazabilidad por Documento @endsection

@section('scripts')
    <script src="{{autoVer('assets/pages/scripts/general.js')}}" type="text/javascript"></script>
    <script src="{{autoVer('assets/pages/scripts/trazabilidaddocumento/index.js')}}" type="text/javascript"></script>
@endsection

@section('contenido')

<style>
.trz-header {
    background: linear-gradient(135deg, #1a3a5c 0%, #2c5f8a 100%);
    border-radius: 6px; padding: 16px 22px; margin-bottom: 18px;
}
.trz-header h2 { color: #fff; font-size: 17px; font-weight: 600; margin: 0; }
.trz-header .trz-breadcrumb { color: rgba(255,255,255,.65); font-size: 12px; margin-top: 3px; }

.trz-filtros {
    background: #fff; border: 1px solid #e3e8ef; border-radius: 6px;
    padding: 14px 18px 10px; margin-bottom: 16px; box-shadow: 0 1px 4px rgba(0,0,0,.06);
}
.trz-filtros label {
    font-size: 11px; font-weight: 600; color: #5a6a7e; text-transform: uppercase;
    letter-spacing: .5px; margin-bottom: 4px; display: block;
}
.trz-filtros .form-control { border-radius: 4px; border-color: #d0d9e4; font-size: 13px; height: 32px; padding: 4px 10px; }
.trz-btn-buscar {
    background: #2c5f8a; color: #fff; border: none; border-radius: 4px;
    padding: 6px 20px; font-size: 13px; font-weight: 600; height: 32px; width: 100%;
}
.trz-btn-buscar:hover { background: #1a3a5c; color:#fff; }

.trz-item {
    background: #fff; border: 1px solid #e3e8ef; border-radius: 6px;
    margin-bottom: 14px; box-shadow: 0 1px 4px rgba(0,0,0,.06); overflow: hidden;
}
.trz-item-header {
    background: #f7f9fc; border-bottom: 1px solid #e8edf3; padding: 10px 16px;
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
}
.trz-item-header .lote-tag {
    background: #2c5f8a; color: #fff; font-weight: 700; font-size: 12px;
    border-radius: 12px; padding: 3px 10px;
}
.trz-item-body { padding: 12px 16px; }

.trz-chip {
    display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600;
    border-radius: 12px; padding: 3px 9px; white-space: nowrap; margin: 2px 3px 2px 0;
}
.trz-chip.blue   { background: #eaf3fb; color: #1a5e9a; }
.trz-chip.green  { background: #e8f8ee; color: #1e8449; }
.trz-chip.orange { background: #fef3e2; color: #b7600a; }
.trz-chip.red    { background: #fde8e8; color: #a93226; }
.trz-chip.gray   { background: #f0f4f8; color: #6b7a8d; }
.trz-chip.purple { background: #f1e9fb; color: #6f42c1; }

.trz-flow { display: flex; align-items: center; flex-wrap: wrap; gap: 4px; }
.trz-flow .trz-arrow { color: #b0bac6; font-size: 12px; }

.trz-lotes-title { font-size: 11px; font-weight: 700; color: #8a99aa; text-transform: uppercase; margin: 10px 0 4px; }
.trz-tree { margin-left: 14px; border-left: 2px dashed #d0d9e4; padding-left: 12px; }
.trz-tree-node { margin: 4px 0; }
.trz-tree-node.trz-actual { background: #eaf3fb; border-radius: 4px; padding: 3px 6px; margin: 4px -6px; }

.trz-empty { color: #aaa; font-size: 12px; font-style: italic; padding: 6px 0; }
</style>

<div class="trz-header">
    <h2><i class="fa fa-sitemap"></i>&nbsp; Trazabilidad por Documento</h2>
    <div class="trz-breadcrumb"><i class="fa fa-home"></i> Inicio &rsaquo; Reportes &rsaquo; Trazabilidad por Documento</div>
</div>

<div class="trz-filtros">
    <div class="row" style="align-items:flex-end; display:flex; flex-wrap:wrap;">
        <div class="col-xs-6 col-sm-2">
            <label>N° Factura</label>
            <input type="text" class="form-control numerico-entero" id="nrofactura" placeholder="Ej: 250302"/>
        </div>
        <div class="col-xs-6 col-sm-2">
            <label>N° Guía</label>
            <input type="text" class="form-control numerico-entero" id="nroguia" placeholder="Ej: 176662"/>
        </div>
        <div class="col-xs-6 col-sm-2">
            <label>N° Nota de Venta</label>
            <input type="text" class="form-control numerico-entero" id="notaventa_id" placeholder="Ej: 33545"/>
        </div>
        <div class="col-xs-6 col-sm-2">
            <label>Lote</label>
            <input type="text" class="form-control numerico-entero" id="lote_id" placeholder="Ej: 43"/>
        </div>
        <div class="col-xs-6 col-sm-2">
            <label>Cód. Producto</label>
            <input type="text" class="form-control numerico-entero" id="producto_id" placeholder="Código"/>
        </div>
        <div class="col-xs-6 col-sm-2">
            <label><i class="fa fa-calendar"></i> Fecha desde</label>
            <input type="text" class="form-control datepicker" id="fechad" placeholder="DD/MM/AAAA" readonly/>
        </div>
        <div class="col-xs-6 col-sm-2">
            <label><i class="fa fa-calendar"></i> Fecha hasta</label>
            <input type="text" class="form-control datepicker" id="fechah" placeholder="DD/MM/AAAA" readonly/>
        </div>
    </div>
    <div class="row" style="margin-top:8px;">
        <div class="col-xs-12 col-sm-3">
            <button type="button" id="btnbuscartraz" class="trz-btn-buscar">
                <i class="fa fa-search"></i>&nbsp; Buscar trazabilidad
            </button>
        </div>
        <div class="col-xs-12 col-sm-9" style="padding-top:8px;">
            <small style="color:#8a99aa;">Complete uno o más filtros. Si combina varios, se cruzan entre sí (ej. Cód. Producto + Fecha + Lote).
            Cód. Producto requiere indicar Fecha desde/hasta.</small>
        </div>
    </div>
</div>

<div id="trz-resultados"></div>

@include('generales.modaletiquetaetapa')
@include('generales.modalpdf')

@endsection
