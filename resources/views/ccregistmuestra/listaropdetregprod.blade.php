@extends("theme.$theme.layout")
@section('titulo')
CC — Buscar Registro de Producción
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/cliente/buscar.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/ccregistmuestra/listaropdetregprod.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Buscar Registro de Producción para Muestra CC</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('ccregistmuestra')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <div class="box-body">
                {{-- Filtros de búsqueda --}}
                <form id="form-filtros" autocomplete="off">
                    @csrf
                    <div class="row">
                        <div class="col-xs-12 col-md-9 col-sm-9">
                            <div class="row">
                                <div class="col-xs-12 col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Fecha inicio:</label>
                                        <input type="text" class="form-control datepicker" name="fechad" id="fechad" placeholder="DD/MM/AAAA" readonly>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Fecha fin:</label>
                                        <input type="text" class="form-control datepicker" name="fechah" id="fechah" placeholder="DD/MM/AAAA" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-md-3 col-sm-12">
                                    <div class="form-group" data-toggle="tooltip" title="RUT del cliente">
                                        <label for="rut">RUT:</label>
                                        <div class="input-group">
                                            <input type="text" name="rut" id="rut" class="form-control"
                                                placeholder="F2 Buscar" onkeyup="llevarMayus(this);"
                                                onfocus="eliminarFormatoRut($(this));"
                                                maxlength="12" data-toggle="tooltip"/>
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" id="btnbuscarcliente" name="btnbuscarcliente" data-toggle="tooltip" title="Buscar cliente">Buscar</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-2 col-sm-2">
                                    <div class="form-group">
                                        <label>ID Reg. Prod.:</label>
                                        <input type="number" class="form-control" name="opdetregprod_id" id="opdetregprod_id" placeholder="Ej: 126">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-3 col-sm-3">
                                    <div class="form-group">
                                        <label>Etapa de Producción:</label>
                                        <select name="etapaprod_id" id="etapaprod_id" class="form-control selectpicker" data-live-search="true">
                                            <option value="">Todas</option>
                                            @foreach($etapaprods as $ep)
                                                <option value="{{ $ep->id }}">{{ $ep->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-3 col-sm-3">
                                    <div class="form-group">
                                        <label>Máquina:</label>
                                        <select name="maquina_id" id="maquina_id" class="form-control selectpicker" data-live-search="true">
                                            <option value="">Todas</option>
                                            @foreach($maquinas as $maq)
                                                <option value="{{ $maq->id }}">
                                                    {{ $maq->nombre }}{{ $maq->etapaprod_nombre ? ' — ' . $maq->etapaprod_nombre : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-2 col-sm-2">
                                    <div class="form-group">
                                        <label>N° OP:</label>
                                        <input type="number" class="form-control" name="op_id" id="op_id" placeholder="Ej: 123">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-2 col-sm-2">
                                    <div class="form-group">
                                        <label>N° OT:</label>
                                        <input type="number" class="form-control" name="ot_id" id="ot_id" placeholder="Ej: 45">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-3 col-sm-3">
                                    <div class="form-group">
                                        <label>Muestras CC:</label>
                                        <select name="con_muestra" id="con_muestra" class="form-control">
                                            <option value="">Todos</option>
                                            <option value="0">Sin muestra CC</option>
                                            <option value="1">Con muestra CC</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-xs-12 col-md-3 col-sm-3">
                            <div class="text-center" style="margin-top:5px;">
                                <button type="button" id="btnconsultar" class="btn btn-success">
                                    <i class="fa fa-search"></i> Consultar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <hr>

                {{-- Tabla de resultados (vacía hasta que el usuario consulte) --}}
                <div class="table-responsive" id="contenedor-tabla" style="display:none;">
                    <table class="table display table-condensed table-hover table-bordered" id="tabla-opdetregprod" data-page-length="25">
                        <thead>
                            <tr>
                                <th class="width70 tooltipsC" title="ID Registro Producción">ID Reg</th>
                                <th>Fecha</th>
                                <th class="tooltipsC" title="Orden de Producción / Orden de Trabajo">OP / OT</th>
                                <th>Producto</th>
                                <th>Etapa / Máquina</th>
                                <th class="tooltipsC" title="Kg producidos">Kg</th>
                                <th class="tooltipsC" title="Cantidad producida">Cant</th>
                                <th class="tooltipsC" title="Muestras CC registradas">Muestras CC</th>
                                <th class="width120">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-opdetregprod-body">
                        </tbody>
                    </table>
                </div>
                <div id="msg-inicial" class="text-center text-muted" style="padding:30px;">
                    <i class="fa fa-search fa-2x"></i><br>
                    Ingrese los filtros y haga clic en <strong>Consultar</strong> para buscar registros de producción.
                </div>
            </div>
        </div>
    </div>
</div>

@include('generales.buscarclientebd')
@endsection
