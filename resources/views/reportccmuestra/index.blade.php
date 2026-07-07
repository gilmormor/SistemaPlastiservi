@extends("theme.$theme.layout")
@section('titulo')
Reporte Muestras CC
@endsection
@section("scripts")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    <script src="{{autoVer("assets/pages/scripts/cliente/buscar.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/producto/buscar.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/reportccmuestra/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-tasks"></i> Reporte de Muestras CC
                </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    {{-- Filtros --}}
                    <div class="col-xs-12 col-md-9 col-sm-12">

                        {{-- Fila 1: Fechas + Sucursal --}}
                        <div class="col-xs-12 col-md-12">
                            <div class="col-xs-12 col-md-4">
                                <label>Fecha desde:</label>
                                <input type="text" id="fecha_desde" name="fecha_desde" class="form-control date-picker" placeholder="dd/mm/aaaa" readonly>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <label>Fecha hasta:</label>
                                <input type="text" id="fecha_hasta" name="fecha_hasta" class="form-control date-picker" placeholder="dd/mm/aaaa" readonly>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <label>Sucursal:</label>
                                <select id="sucursal_id" name="sucursal_id" class="selectpicker form-control" data-live-search="true">
                                    <option value="">Todas</option>
                                    @foreach($sucursales as $s)
                                        <option value="{{$s->id}}">{{$s->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Fila 2: Etapa + Operario + Máquina --}}
                        <div class="col-xs-12 col-md-12" style="margin-top:8px;">
                            <div class="col-xs-12 col-md-4">
                                <label>Etapa producción:</label>
                                <select id="etapaprod_id" name="etapaprod_id" class="selectpicker form-control" data-live-search="true">
                                    <option value="">Todas</option>
                                    @foreach($etapaprods as $ep)
                                        <option value="{{$ep->id}}">{{$ep->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <label>Operario:</label>
                                <select id="operario_id" name="operario_id" class="selectpicker form-control" data-live-search="true">
                                    <option value="">Todos</option>
                                    @foreach($operarios as $op)
                                        <option value="{{$op->id}}">{{$op->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <label>Máquina:</label>
                                <select id="maquina_id" name="maquina_id" class="selectpicker form-control" data-live-search="true">
                                    <option value="">Todas</option>
                                    @foreach($maquinas as $mq)
                                        <option value="{{$mq->id}}">{{$mq->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Fila 3: RUT cliente + Producto --}}
                        <div class="col-xs-12 col-md-12" style="margin-top:8px;">
                            <div class="col-xs-12 col-md-4">
                                <label>RUT cliente:</label>
                                <div class="input-group">
                                    <input type="text" name="rut1" id="rut1" class="form-control" value="" placeholder="F2 Buscar" onkeyup="llevarMayus(this);" maxlength="12">
                                    <p id="error-message" style="color:red; display:none;">RUT inválido</p>
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" id="btnbuscarcliente" name="btnbuscarcliente">Buscar</button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <label>Producto (código):</label>
                                <div class="input-group">
                                    <input type="text" name="producto_idPxP" id="producto_idPxP" class="form-control" tipoval="numericootro" placeholder="ID(s) separados por coma">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" id="btnbuscarproducto" name="btnbuscarproducto">Buscar</button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Fila 4: Status CC + sta_env + Anulado + Desbloqueado --}}
                        <div class="col-xs-12 col-md-12" style="margin-top:8px;">
                            <div class="col-xs-12 col-md-3">
                                <label>Status CC:</label>
                                <select id="status" name="status" class="selectpicker form-control">
                                    <option value="">Todos</option>
                                    <option value="1">Aprobado</option>
                                    <option value="2">Aprobado c/obs</option>
                                    <option value="3">Rechazado</option>
                                </select>
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <label>Estado envío:</label>
                                <select id="sta_env" name="sta_env" class="selectpicker form-control">
                                    <option value="">Todos</option>
                                    <option value="0">Sin enviar</option>
                                    <option value="1">Enviado al supervisor</option>
                                    <option value="2">Aprobado supervisor</option>
                                    <option value="3">Rechazado supervisor</option>
                                </select>
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <label>Anulado:</label>
                                <select id="anulado" name="anulado" class="selectpicker form-control">
                                    <option value="">Todos</option>
                                    <option value="0">No anulados</option>
                                    <option value="1">Solo anulados</option>
                                </select>
                            </div>
                            <div class="col-xs-12 col-md-3">
                                <label>Desbloqueado:</label>
                                <select id="desbloqueado" name="desbloqueado" class="selectpicker form-control">
                                    <option value="">Todos</option>
                                    <option value="0">Sin desbloqueo</option>
                                    <option value="1">Desbloqueados</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Botones acción --}}
                    <div class="col-xs-12 col-md-3 col-sm-12 text-center" style="padding-top:20px;">
                        <button type="button" id="btnconsultar" class="btn btn-success btn-block tooltipsC" title="Consultar">
                            <i class="fa fa-search"></i> Consultar
                        </button>
                        <button type="button" id="btnpdf" class="btn btn-danger btn-block tooltipsC" style="margin-top:6px;" title="Exportar PDF">
                            <i class="fa fa-file-pdf-o"></i> PDF
                        </button>
                        <button type="button" id="btnexportarExcel" class="btn btn-success btn-block tooltipsC" style="margin-top:6px;" title="Exportar Excel" onclick="exportarExcel()">
                            <i class="fa fa-file-excel-o"></i> Excel
                        </button>
                    </div>
                </div>

                {{-- Totalizadores --}}
                <div class="row" style="margin-top:12px;" id="div-totales" style="display:none;">
                    <div class="col-xs-12">
                        <div class="col-xs-6 col-md-2 text-center">
                            <strong>Total</strong><br>
                            <span id="tot-total" style="font-size:18px; font-weight:bold;">—</span>
                        </div>
                        <div class="col-xs-6 col-md-2 text-center">
                            <strong style="color:#00a65a;">Aprobados</strong><br>
                            <span id="tot-aprobados" style="font-size:18px; font-weight:bold; color:#00a65a;">—</span>
                        </div>
                        <div class="col-xs-6 col-md-2 text-center">
                            <strong style="color:#f39c12;">Con obs</strong><br>
                            <span id="tot-conobs" style="font-size:18px; font-weight:bold; color:#f39c12;">—</span>
                        </div>
                        <div class="col-xs-6 col-md-2 text-center">
                            <strong style="color:#dd4b39;">Rechazados</strong><br>
                            <span id="tot-rechazados" style="font-size:18px; font-weight:bold; color:#dd4b39;">—</span>
                        </div>
                        <div class="col-xs-6 col-md-2 text-center">
                            <strong style="color:#777;">Anulados</strong><br>
                            <span id="tot-anulados" style="font-size:18px; font-weight:bold; color:#777;">—</span>
                        </div>
                        <div class="col-xs-6 col-md-2 text-center">
                            <strong style="color:#00c0ef;">Desbloqueados</strong><br>
                            <span id="tot-desbloqueados" style="font-size:18px; font-weight:bold; color:#00c0ef;">—</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla resultado --}}
            <div class="table-responsive" style="margin-top:10px;">
                <table class="table table-striped table-bordered table-condensed table-hover" id="tabla-data-ccmuestra" data-page-length="25">
                    <thead>
                        <tr>
                            <th class="width70">ID</th>
                            <th>Fecha</th>
                            <th>OP/OT</th>
                            <th>NV</th>
                            <th>Cliente</th>
                            <th class="tooltipsC" title="Código Producto">Cod</th>
                            <th>Producto</th>
                            <th>Etapa</th>
                            <th>Kg</th>
                            <th>Operario</th>
                            <th>Máquina</th>
                            <th>Status CC</th>
                            <th>Estado envío</th>
                            <th>Anulado</th>
                            <th>Desbloqueado</th>
                        </tr>
                    </thead>
                    <tfoot><tr></tr></tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@include('generales.buscarclientebd')
@include('generales.buscarproductobd')
@include('generales.modalpdf')
@include('generales.verpdf')
@endsection
