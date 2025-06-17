@extends("theme.$theme.layout")
@section('titulo')
Orden Trabajo
@endsection

<?php
    $selecmultprod = true;
?>

@section("scripts")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/reportot/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/producto/buscar.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/cliente/buscar.js")}}" type="text/javascript"></script> 
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Reporte Orden Trabajo</h3>
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
                        <input type="hidden" name="selecmultprod" id="selecmultprod" value="{{old('selecmultprod', $selecmultprod ?? '')}}">
                        <div class="col-xs-12 col-md-9 col-sm-12">
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-md-6 col-sm-6" style="display: flex; align-items: center;">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="fecha" data-toggle='tooltip' title="Fecha Inicial">Fecha Ini:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" bsDaterangepicker class="form-control datepicker" name="fechad" id="fechad" value="{{old('fechad', date("01/m/Y") ?? '')}}" placeholder="DD/MM/AAAA" required readonly="">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6 col-sm-6" style="display: flex; align-items: center;">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="dep_fecha" data-toggle='tooltip' title="Fecha Final">Fecha Fin:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" class="form-control datepicker" name="fechah" id="fechah" value="{{old('fechah', date("d/m/Y") ?? '')}}" placeholder="DD/MM/AAAA" required readonly="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6" style="display: flex; align-items: center;">
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
                                <div class="col-xs-12 col-sm-6" style="display: flex; align-items: center;">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label data-toggle='tooltip' title="Vendedor">Vendedor:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <?php
                                            echo $tablashtml['vendedores'];
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6" style="display: flex; align-items: center;">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="ot_id" data-toggle='tooltip' title="Número Orden Trabajo">Nro Ot:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" name="ot_id" id="ot_id" class="form-control numerico" value="{{old('ot_id')}}" maxlength="12"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6" style="display: flex; align-items: center;">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="notaventa_id" data-toggle='tooltip' title="Número Nota de Venta">NotaVenta:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" name="notaventa_id" id="notaventa_id" class="form-control numerico" value="{{old('notaventa_id')}}" maxlength="12"/>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6" style="display: flex; align-items: center;">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="oc_id" data-toggle='tooltip' title="Orden de Compra">OC:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" name="oc_id" id="oc_id" class="form-control" value="{{old('oc_id')}}" maxlength="18"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6" style="display: flex; align-items: center;">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="producto_idPxP" class="control-label" data-toggle='tooltip' title="Código Producto">Producto</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <div class="input-group">
                                            <input type="text" name="producto_idPxP" id="producto_idPxP" class="form-control" tipoval="numericootro"/>
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" id="btnbuscarproductogen" name="btnbuscarproductogen">Buscar</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6" style="display: flex; align-items: center;">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label data-toggle='tooltip' title="Area de Producción">Area Prod:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="areaproduccion_id" id="areaproduccion_id" class="selectpicker form-control areaproduccion_id">
                                            <option value="">Todos</option>
                                            @foreach($areaproduccions as $areaproduccion)
                                                <option
                                                    value="{{$areaproduccion->id}}"
                                                    >
                                                    {{$areaproduccion->nombre}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-md-6 col-sm-6" style="display: flex; align-items: center;">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="sucursal_id" data-toggle='tooltip' title="Sucursal">Sucursal</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="sucursal_id" id="sucursal_id" class="selectpicker form-control" required>
                                            <option value="">Todas</option>
                                            @foreach($tablashtml['sucursales'] as $sucursal)
                                                <option
                                                    value="{{$sucursal->id}}"
                                                >{{$sucursal->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6" style="display: flex; align-items: center;">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label data-toggle='tooltip' title="Comuna">Comuna:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <?php
                                            echo $tablashtml['comunas'];
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-md-6 col-sm-6" style="display: flex; align-items: center;">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label data-toggle='tooltip' title="Estado OT">Estado:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="aux_estado" id="aux_estado" class="selectpicker form-control aux_estado">
                                            <option value="">Seleccione...</option>
                                            <option value="1">Esperando a ser enviadas Aprob</option>
                                            <option value="2">Enviadas Aprobacion</option>
                                            <option value="3">Aprobadas</option>
                                            <option value="4">Anuladas</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-3 col-sm-12 text-center">
                            <div class="col-xs-12 col-md-4 col-sm-4">
                                <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success tooltipsC" title="Consultar">Consultar</button>
                            </div>
                            <div class="col-xs-12 col-md-4 col-sm-4">
                                <button type='button' id='btnpdf3' name='btnpdf3' class='btn btn-success tooltipsC' title="Reporte PDF" onclick="ejecutarConsulta(2)">
                                    <i class='glyphicon glyphicon-print'></i> Reporte
                                </button>
                            </div>
                            <div class="col-xs-12 col-md-4 col-sm-4">
                                <button type="button" id="btnexportarExcel" name="btnexportarExcel" class="btn btn-success tooltipsC" title="Exportar Excel" onclick="exportarExcel()">
                                    <i class='fa fa-fw fa-file-excel-o'> </i> Excel
                                </button>
                            </div>
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
                            <th class="width70" title='OT'>OT ID</th>
                            <th title='Fecha'>Fecha</th>
                            <th>Rut</th>
                            <th>Razón Social</th>
                            <th title='Cotizacion'>Cot</th>
                            <th title='Orden de Compra'>OC</th>
                            <th title='Nota de Venta'>NV</th>
                            <th title='Comuna'>Comuna</th>
                            <th title='Total Kg' style="text-align:right">Kg</th>                        
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <!--
                    <tfoot>
                        <tr>
                        </tr>
                        <tr>
                            <th colspan='11' style='text-align:right'>Total página</th>
                            <th id='subtotalkg' name='subtotalkg' style='text-align:right'>0,00</th>
                        </tr>
                        <tr>
                            <th colspan='11' style='text-align:right'>TOTAL GENERAL</th>
                            <th id='totalkg' name='totalkg' style='text-align:right'>0,00</th>
                        </tr>
                    </tfoot>
                    -->
                </table>
            </div>

        </div>
    </div>
</div>

@include('generales.buscarclientebd')
@include('generales.modalpdf')
@include('generales.verpdf')
@include('generales.buscarproductobd')
@endsection
