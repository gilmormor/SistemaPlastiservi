@extends("theme.$theme.layout")
@section('titulo')
Estadistica Ventas
@endsection

<?php
    $selecmultprod = true;
?>

@section("scripts")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/reportdteestadisticaventa/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/cliente/buscar.js")}}" type="text/javascript"></script> 
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Estadistica Ventas</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            @csrf
            <div class="box-body">
                <div class="row">
                    <form class="d-inline form-eliminar" method="get" target="_blank">
                        @csrf
                        @csrf @method("put")
                        <div class="col-xs-12 col-md-9 col-sm-12">
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-md-6 col-sm-6">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="fecha" data-toggle='tooltip' title="Fecha Inicial">Fecha Ini:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" bsDaterangepicker class="form-control datepicker" name="fechad" id="fechad" value="{{old('fechad', date("01/m/Y") ?? '')}}" placeholder="DD/MM/AAAA" required readonly="">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6 col-sm-6">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="dep_fecha" data-toggle='tooltip' title="Fecha Final">Fecha Fin:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" class="form-control datepicker" name="fechah" id="fechah" value="{{old('fechah', date("d/m/Y") ?? '')}}" placeholder="DD/MM/AAAA" required readonly="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-md-6 col-sm-6">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="sucursal_id" data-toggle='tooltip' title="Sucursal">Sucursal</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="sucursal_id" id="sucursal_id" class="selectpicker form-control" required>
                                            <option value="">Todos...</option>
                                            @foreach($tablas['sucursales'] as $sucursal)
                                                <option
                                                    value="{{$sucursal->id}}"
                                                >{{$sucursal->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6 col-sm-6">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="centroeconomico_id" data-toggle='tooltip' title="Centro Económico">Centro Económico</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="centroeconomico_id" id="centroeconomico_id" class="selectpicker form-control" required>
                                            <option value="">Seleccione...</option>
                                            @foreach($tablas['centroeconomicos'] as $centroeconomico)
                                                <option
                                                    value="{{$centroeconomico->id}}"
                                                >{{$centroeconomico->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="rut" data-toggle='tooltip' title="RUT">RUT:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <div class="input-group">
                                            <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut')}}" placeholder="F2 Buscar" onkeyup="llevarMayus(this);" maxlength="12" data-toggle='tooltip'/>
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" id="btnbuscarcliente" name="btnbuscarcliente" data-toggle='tooltip' title="Buscar">Buscar</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label data-toggle='tooltip' title="Area de Producción">Area Prod:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="areaproduccion_id" id="areaproduccion_id" class="selectpicker form-control areaproduccion_id" multiple>
                                            @foreach($tablas['areaproduccions'] as $areaproduccion)
                                                <option
                                                    value="{{$areaproduccion->id}}"
                                                    >
                                                    {{$areaproduccion->nombre}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
    
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label data-toggle='tooltip' title="Vendedor">Vendedor:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <?php
                                            echo $tablas['vendedores'];
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-3 col-sm-12 text-center">
                            <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success tooltipsC" title="Consultar">Consultar</button>
                            @if (false)
                                <button type='button' id='btnpdf2' name='btnpdf2' class='btn btn-success tooltipsC' title="Reporte PDF">
                                    <i class='glyphicon glyphicon-print'></i> Reporte
                                </button>                                
                            @endif
                            @if (in_array(1, $tablas['sucFisXUsu']))
                                <button type="button" id="btnexportarExcel" name="btnexportarExcel" class="btn btn-success tooltipsC" title="Exportar Excel" onclick="exportarExcelSantaEster()">Excel SE</button>                            
                            @endif
                            @if (in_array(2, $tablas['sucFisXUsu']))
                                <button type="button" id="btnexportarExcel" name="btnexportarExcel" class="btn btn-success tooltipsC" title="Exportar Excel" onclick="exportarExcelLosPinos()">Excel LP</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div>
                    <legend></legend>
                </div>
            </div>
            
            <div class="table-responsive" id="tablaconsulta">
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="tabla-data-consulta" data-page-length="25">
                    <thead>
                        <tr>
                            <th class="width20 tooltipsC" title='Id'>ID</th>
                            <th class='width60 tooltipsC' title='Factura'>DTE</th>
                            <th class='width20 tooltipsC' title='Documento'>Doc</th>
                            <th class='width30 tooltipsC' title='Fecha'>Fecha</th>
                            <th class='width40'>RUT</th>
                            <th>Razón Social</th>
                            <th class='width20 tooltipsC'  style='text-align:center' title='Cod Producto'>CodP</th>
                            <th class='tooltipsC' title='Producto'>Producto</th>
                            <th class='width40 tooltipsC' title='Monto'>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                        </tr>
                        <tr>
                            <th colspan="8" style='text-align:right'>Total página</th>
                            <th id='subtotalmonto' name='subtotalmonto' style='text-align:right'>0</th>
                        </tr>
                        <tr>
                            <th colspan="8" style='text-align:right'>TOTAL GENERAL</th>
                            <th id='totalmonto' name='totalmonto' style='text-align:right'>0</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>

@include('generales.buscarclientebd')
@include('generales.modalpdf')
@include('generales.verpdf')
@endsection
