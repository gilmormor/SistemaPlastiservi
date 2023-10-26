@extends("theme.$theme.layout")
@section('titulo')
DTE Libro Ventas
@endsection

<?php
    $selecmultprod = true;
?>

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/reportdtelibroventasdte/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/cliente/buscar.js")}}" type="text/javascript"></script> 
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">DTE Libro Ventas</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            @csrf
            <div class="box-body">
                <div class="row">
                    <form action="{{route('exportPdf_notaventaconsulta')}}" class="d-inline form-eliminar" method="get" target="_blank">
                        @csrf
                        @csrf @method("put")
                        <input type="hidden" name="selecmultprod" id="selecmultprod" value="{{old('selecmultprod', $selecmultprod ?? '')}}">
                        <div class="col-xs-12 col-md-9 col-sm-12">
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Inicial">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="fecha">Fecha Ini:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" bsDaterangepicker class="form-control datepicker" name="fechad" id="fechad"  value="{{old('fechad', $fechaServ['fecha1erDiaMes'] ?? '')}}" placeholder="DD/MM/AAAA" required readonly="">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Final">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="dep_fecha">Fecha Fin:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" class="form-control datepicker" name="fechah" id="fechah" value="{{old('fechah', date("d/m/Y") ?? '')}}" placeholder="DD/MM/AAAA" required readonly="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Sucursal">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="sucursal_id" >Sucursal</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="sucursal_id" id="sucursal_id" class="selectpicker form-control" required>
                                            <option value="">Seleccione...</option>
                                            @foreach($tablas['sucursales'] as $sucursal)
                                                <option
                                                    value="{{$sucursal->id}}"
                                                >
                                                    {{$sucursal->nombre}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="RUT">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="rut">RUT:</label>
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
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-3 col-sm-12 text-center">
                                <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success tooltipsC" title="Consultar">Consultar</button>
                                <button type='button' id='btnpdf2' name='btnpdf2' class='btn btn-success tooltipsC' title="Reporte PDF">
                                    <i class='glyphicon glyphicon-print'></i> Reporte
                                </button>
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
                            <th class="width20 tooltipsC" title='Factura'>ID</th>
                            <th class='tooltipsC' title='Fecha'>Fecha</th>
                            <th>RUT</th>
                            <th>Razón Social</th>
                            <th class='tooltipsC' title='Cotizacion'>Cot</th>
                            <th class='tooltipsC' title='Orden de Compra'>OC</th>
                            <th class='tooltipsC' title='Nota de Venta'>NV</th>
                            <th class='tooltipsC' title='Solicitud de Despacho'>SD</th>
                            <th class='tooltipsC' title='Orden de Despacho'>OD</th>
                            <th class='tooltipsC' title='DTE Origen'>DTEOrigen</th>
                            <th class='tooltipsC' title='Factura'>DTE Doc</th>
                            <th class='tooltipsC' title='Tipo DTE'>Tipo</th>
                            <th class='tooltipsC' title='Monto'>Monto</th>
                            <th class="ocultar">dteanul_obs</th>
                            <th class="ocultar">dteanulcreated_at</th>
                            <th class="ocultar">Obs Bloqueo</th>
                            <th class="ocultar">oc_file</th>
                            <th class="ocultar">foliocontrol_id</th>
                            <th class="ocultar">staverfacdesp</th>
                            <th class="ocultar">updated_at</th>
                            <th class="ocultar">dtefac_updated_at</th>
                            <th class="ocultar">foliocontrol_desc</th>
                            <th class="ocultar">pdftipodte_origen</th>
                            <th class="ocultar">foliocontroldesc_origen</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                        </tr>
                        <tr>
                            <th colspan='12' style='text-align:right'>Total página</th>
                            <th id='subtotalmonto' name='subtotalmonto' style='text-align:right'>0</th>
                        </tr>
                        <tr>
                            <th colspan='12' style='text-align:right'>TOTAL GENERAL</th>
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
