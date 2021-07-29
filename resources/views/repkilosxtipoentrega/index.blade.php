@extends("theme.$theme.layout")
@section('titulo')
Productos Notas de Venta
@endsection

@section("scripts")
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/repkilosxtipoentrega/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    @include('includes.mensaje')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Kilos x Tipo Entrega</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <form action="{{route('prodxnotaventa_exportPdf')}}" class="d-inline form-eliminar" method="get" target="_blank">
                    @csrf
                    <div class="col-xs-12 col-md-7 col-sm-7">
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Inicial">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="fecha">Fecha Ini:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" bsDaterangepicker class="form-control datepickerdesde" name="fechad" id="fechad" placeholder="DD/MM/AAAA" value="{{old('fechad', $fechaServ['fecha1erDiaMes'] ?? '')}}" required readonly="">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Fin">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="dep_fecha">Fecha Fin:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" class="form-control datepickerhasta" name="fechah" id="fechah" value="{{old('fechah', $fechaServ['fechaAct'] ?? '')}}" placeholder="DD/MM/AAAA" required readonly="">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Vendedor">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Vendedor:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <?php
                                        echo $tablashtml['vendedores'];
                                    ?>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Giro">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="giro_id">Giro:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <select name="giro_id" id="giro_id" class="form-control selectpicker giro_id">
                                        <option value="">Todos</option>
                                        @foreach($giros as $giro)
                                            <option
                                                value="{{$giro->id}}"
                                                >
                                                {{$giro->nombre}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Categoría">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Categoría:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <select name="categoriaprod_id" id="categoriaprod_id" class="form-control selectpicker categoriaprod_id">
                                        <option value="">Todos</option>
                                        @foreach($categoriaprods as $categoriaprod)
                                            <option
                                                value="{{$categoriaprod->id}}">
                                                {{$categoriaprod->nombre}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Area de Producción">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label >Area Prod:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <select name="areaproduccion_id" id="areaproduccion_id" class="selectpicker form-control areaproduccion_id"   data-live-search='true' multiple data-actions-box='true'>
                                        @foreach($areaproduccions as $areaproduccion)
                                            <option
                                                value="{{$areaproduccion->id}}"
                                                @if ($areaproduccion->id==1)
                                                    {{'selected'}}
                                                @endif
                                                >
                                                {{$areaproduccion->nombre}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-xs-12 col-md-4 col-sm-4">
                        <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                            <label>Consulta:</label>
                        </div>
                        <div class="col-xs-12 col-md-8 col-sm-8">
                            <select name="consulta_id" id="consulta_id" class="selectpicker form-control consulta_id">
                                <option value="2">Facturado (Fecha FC)</option>
                                <option value="3">Facturado (Fecha NV)</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                            <label>Estatus NV:</label>
                        </div>
                        <div class="col-xs-12 col-md-8 col-sm-8">
                            <select name="statusact_id" id="statusact_id" class="selectpicker form-control statusact_id">
                                <option value="1" selected>Activas</option>
                                <option value="2">Cerradas</option>
                                <option value="3">Todas: Activas + cerradas</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-1 col-sm-1 text-center">
                        <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success" data-toggle='tooltip' title="Consultar">Consultar</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div>
                    <legend></legend>
                </div>
            </div>
        </div>
    </div>
    <div class="box box-danger" id="consulta1" name="consulta1" style="display:none;">
        <div class="box-header with-border">
            <h3 class="box-title" id="titulo_consulta1" name="titulo_consulta1"></h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="col-xs-12 col-sm-12">
                <div class="table-responsive" id="tablaconsulta1">
                </div>
            </div>                        
            <div class="col-lg-12 text-center">
                <button type='button' id='btnpdf1' name='btnpdf1' class='btn btn-success tooltipsC' title="Reporte PDF" onclick="btnpdf(1)"><i class='glyphicon glyphicon-print'></i> Reporte</button>
            </div>
        </div>

    </div>
</div>

@include('generales.modalpdf')
@endsection
