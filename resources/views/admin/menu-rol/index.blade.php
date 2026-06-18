@extends("theme.$theme.layout")
@section("titulo")
Menú - Rol
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/admin/menu-rol/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        @csrf

        {{-- Panel de selección de roles --}}
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-shield"></i> Asignar Menús por Rol</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-8 col-md-9">
                        <div class="form-group">
                            <label>Seleccione uno o más roles:</label>
                            <select name="rol_id" id="rol_id"
                                class="selectpicker form-control"
                                data-live-search="true"
                                multiple
                                data-actions-box="true"
                                data-selected-text-format="count > 2"
                                title="-- Seleccione roles --">
                                @foreach ($rols as $rol)
                                    <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-3" style="padding-top: 25px;">
                        <button type="button" id="btnconsultar" class="btn btn-success btn-block">
                            <i class="fa fa-search"></i> Consultar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Barra sticky con nombres de roles (visible al hacer scroll) --}}
        <div id="contenedor-sticky" style="display:none;">
            <div class="sticky-roles-bar" id="sticky-roles">
                <i class="fa fa-lock"></i>
                <strong> Asignando acceso a: </strong>
                <span id="roles-badges"></span>
            </div>
        </div>

        {{-- Árbol de menú --}}
        <div id="contenedor-arbol" class="box box-primary" style="display:none;">
            <div class="box-header with-border">
                <h3 class="box-title" id="arbol-titulo">Estructura del Menú</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-xs btn-default" id="btn-expandir-todo" title="Expandir todo">
                        <i class="fa fa-expand"></i> Expandir todo
                    </button>
                    <button type="button" class="btn btn-xs btn-default" id="btn-colapsar-todo" title="Colapsar todo">
                        <i class="fa fa-compress"></i> Colapsar todo
                    </button>
                </div>
            </div>
            <div class="box-body" style="padding: 0;" id="arbol-menu">
                {{-- Se llena por JavaScript --}}
            </div>
        </div>

    </div>
</div>

<style>
/* --- Barra sticky --- */
.sticky-roles-bar {
    position: sticky;
    top: 50px; /* Altura del navbar AdminLTE */
    z-index: 200;
    background: #2c3e50;
    color: #fff;
    padding: 9px 15px;
    margin-bottom: 0;
    box-shadow: 0 3px 6px rgba(0,0,0,0.35);
    font-size: 13px;
    line-height: 1.6;
}
.sticky-roles-bar .label {
    font-size: 12px;
    margin-right: 4px;
    padding: 3px 7px;
}

/* --- Filas del árbol --- */
.menu-fila {
    display: flex;
    align-items: center;
    padding: 7px 15px;
    border-bottom: 1px solid #e8e8e8;
    min-height: 44px;
    flex-wrap: wrap;
    gap: 6px;
}
.menu-fila:hover {
    background-color: #f0f7ff !important;
}

/* Niveles con distintos fondos */
.bg-nivel-0 {
    background-color: #d2d6de;
    font-weight: bold;
    font-size: 13px;
    color: #1a252f;
}
.bg-nivel-1 {
    background-color: #ecf0f5;
    font-size: 13px;
}
.bg-nivel-2 {
    background-color: #f8f8f8;
    font-size: 12px;
}
.bg-nivel-3 {
    background-color: #ffffff;
    font-size: 12px;
}

/* --- Botón toggle acordeón --- */
.toggle-menu-btn {
    cursor: pointer;
    color: #3c8dbc;
    min-width: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 4px;
    flex-shrink: 0;
}
.toggle-menu-btn:hover {
    color: #1a6084;
}
.toggle-placeholder {
    display: inline-block;
    min-width: 18px;
    margin-right: 4px;
    flex-shrink: 0;
}

/* --- Nombre del menú --- */
.menu-nombre-wrap {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 6px;
    min-width: 150px;
}

/* --- Checkboxes por rol --- */
.checks-rol-container {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    align-items: center;
    flex-shrink: 0;
}
.check-rol-label {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-weight: normal;
    cursor: pointer;
    white-space: nowrap;
    margin: 0;
    padding: 3px 8px;
    border-radius: 3px;
    border: 1px solid #ccd;
    background: #fff;
    transition: background 0.15s, border-color 0.15s;
}
.check-rol-label:hover {
    background: #e8f4fd;
    border-color: #3c8dbc;
}
.check-rol-label input[type=checkbox] {
    cursor: pointer;
    flex-shrink: 0;
    margin: 0;
}
.check-rol-nombre {
    font-size: 11px;
    color: #444;
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Checkbox indeterminado (visual extra para Firefox/IE) */
.menu_rol_check:indeterminate + .check-rol-nombre {
    color: #888;
    font-style: italic;
}
</style>
@endsection
