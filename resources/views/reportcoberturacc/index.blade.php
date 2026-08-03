@extends("theme.$theme.layout")
@section('titulo')
Reporte Cobertura CC
@endsection
@section("scripts")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/reportcoberturacc/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-shield"></i> Reporte de Cobertura CC
                </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    {{-- Filtros --}}
                    <div class="col-xs-12 col-md-9 col-sm-12">

                        {{-- Fila 1: Fechas aprobación --}}
                        <div class="col-xs-12 col-md-12">
                            <div class="col-xs-12 col-md-4">
                                <label>Fecha aprobación desde:</label>
                                <input type="text" id="fecha_desde" class="form-control date-picker" placeholder="dd/mm/aaaa" readonly>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <label>Fecha aprobación hasta:</label>
                                <input type="text" id="fecha_hasta" class="form-control date-picker" placeholder="dd/mm/aaaa" readonly>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <label>Cobertura:</label>
                                <select id="cobertura" class="selectpicker form-control">
                                    <option value="">Todos los lotes</option>
                                    <option value="0">Solo sin cobertura</option>
                                    <option value="1">Solo con cobertura</option>
                                </select>
                            </div>
                        </div>

                        {{-- Fila 2: Etapa + Operario + Máquina --}}
                        <div class="col-xs-12 col-md-12" style="margin-top:8px;">
                            <div class="col-xs-12 col-md-4">
                                <label>Etapa producción:</label>
                                <select id="etapaprod_id" class="selectpicker form-control" data-live-search="true">
                                    <option value="">Todas</option>
                                    @foreach($etapaprods as $ep)
                                        <option value="{{$ep->id}}">{{$ep->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <label>Operario:</label>
                                <select id="operario_id" class="selectpicker form-control" data-live-search="true">
                                    <option value="">Todos</option>
                                    @foreach($operarios as $op)
                                        <option value="{{$op->id}}">{{$op->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <label>Máquina:</label>
                                <select id="maquina_id" class="selectpicker form-control" data-live-search="true">
                                    <option value="">Todas</option>
                                    @foreach($maquinas as $mq)
                                        <option value="{{$mq->id}}">{{$mq->nombre}}</option>
                                    @endforeach
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

                {{-- Tarjetas de totales --}}
                <div class="row" id="div-totales" style="margin-top:16px; display:none;">
                    <div class="col-xs-12">
                        <div class="col-xs-6 col-md-3 text-center">
                            <div style="padding:10px; background:#f9f9f9; border-radius:4px; border-left:4px solid #aaa;">
                                <strong>Total lotes</strong><br>
                                <span id="tot-total" style="font-size:24px; font-weight:bold;">—</span>
                            </div>
                        </div>
                        <div class="col-xs-6 col-md-3 text-center">
                            <div style="padding:10px; background:#f9f9f9; border-radius:4px; border-left:4px solid #00a65a;">
                                <strong style="color:#00a65a;">Con cobertura</strong><br>
                                <span id="tot-con" style="font-size:24px; font-weight:bold; color:#00a65a;">—</span>
                            </div>
                        </div>
                        <div class="col-xs-6 col-md-3 text-center">
                            <div style="padding:10px; background:#f9f9f9; border-radius:4px; border-left:4px solid #dd4b39;">
                                <strong style="color:#dd4b39;">Sin cobertura</strong><br>
                                <span id="tot-sin" style="font-size:24px; font-weight:bold; color:#dd4b39;">—</span>
                            </div>
                        </div>
                        <div class="col-xs-6 col-md-3 text-center">
                            <div style="padding:10px; background:#f9f9f9; border-radius:4px; border-left:4px solid #3c8dbc;">
                                <strong style="color:#3c8dbc;">% Cobertura</strong><br>
                                <span id="tot-pct" style="font-size:24px; font-weight:bold; color:#3c8dbc;">—</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla resultado --}}
            <div class="table-responsive" style="margin-top:10px;">
                <table class="table table-striped table-bordered table-condensed table-hover" id="tabla-cobertura" data-page-length="25">
                    <thead>
                        <tr>
                            <th class="width70">Lote</th>
                            <th>Fecha aprobación</th>
                            <th>OP/OT</th>
                            <th>Producto</th>
                            <th>Etapa</th>
                            <th class="text-right">Kg</th>
                            <th>Operario</th>
                            <th>Máquina</th>
                            <th class="text-center">N° Muestras</th>
                            <th class="text-center">Cobertura</th>
                        </tr>
                    </thead>
                    <tfoot><tr></tr></tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@include('generales.modalpdf')

{{-- Modal etiqueta de etapa (Reg. Prod.) — usado desde el enlace "Ver etiqueta — Lote #N" --}}
@include('generales.modaletiquetaetapa')

@endsection
