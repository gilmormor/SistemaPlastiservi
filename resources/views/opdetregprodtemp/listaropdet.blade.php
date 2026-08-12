@extends("theme.$theme.layout")
@section('titulo')
OP Det
@endsection

<?php
    $selecmultprod = true;
?>

@section("scripts")
    <script>
        const ETAPAPROD_ID = "{{ session('etapaprod_id') }}";
    </script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/opdetregprodtemp/listaropdet.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/producto/buscar.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/cliente/buscar.js")}}" type="text/javascript"></script> 
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary collapsed-box">
            <div class="box-header with-border">
                <h3 class="box-title">Pendientes OP Det. Etapa Produccion: {{$tablashtml["etapaprod"]->nombre}}</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                </div>
                <div class="box-tools pull-right">
                    <a href="{{route('opdetregprodtemp')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <form action="{{route('exportPdf_notaventaconsulta')}}" class="d-inline form-eliminar" method="get" target="_blank">
                        @csrf
                        <input type="hidden" name="selecmultprod" id="selecmultprod" value="{{old('selecmultprod', $selecmultprod ?? '')}}">
                        <input type="hidden" name="aux_ruta_creardespsol" id="aux_ruta_creardespsol" value="{{route('crearsol_despachosol', ['id' => "0"])}}">
                        <div class="col-xs-12 col-md-9 col-sm-12">
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Inicial">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="fecha">Fecha Ini:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" bsDaterangepicker class="form-control datepicker" name="fechad" id="fechad" placeholder="DD/MM/AAAA" required readonly>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Final">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="dep_fecha">Fecha Fin:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" class="form-control datepicker" name="fechah" id="fechah" value="{{old('fechah', $fechaAct ?? '')}}" placeholder="DD/MM/AAAA" required readonly="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
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
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Número Nota de Venta">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="notaventa_id">NotaVenta:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" name="notaventa_id" id="notaventa_id" class="form-control" value="{{old('notaventa_id')}}" maxlength="12"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Número OT">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="ot_id">OT:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" name="ot_id" id="ot_id" class="form-control" value="{{old('ot_id')}}" maxlength="8"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Orden Produccion">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="op_id">OP:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" name="op_id" id="op_id" class="form-control" value="{{old('op_id')}}" maxlength="6"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Código Producto">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="producto_idPxP" class="control-label">Producto</label>
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
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Código Producto">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="sucursal_id" class="control-label">Sucursal:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="sucursal_id[]" id="sucursal_id" multiple class='selectpicker form-control' data-live-search='true' multiple data-actions-box='true'>
                                            @foreach($tablashtml['sucursales'] as $sucursal)
                                                <option
                                                    value="{{$sucursal->id}}"
                                                    {{is_array(old('sucursal_id')) ? (in_array($sucursal->id, old('sucursal_id')) ? 'selected' : '') : (isset($data) ? ($data->sucursales->firstWhere('id', $sucursal->id) ? 'selected' : '') : '')}}
                                                    >
                                                    {{$sucursal->nombre}}
                                                </option>
                                            @endforeach
                                        </select>            
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Estatus Bloqueo">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label>Estatus Bloqueo:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="statusBloqueo" id="statusBloqueo" class="selectpicker form-control statusBloqueo">
                                            <option value="">Seleccione...</option>
                                            <option value="1">Clientes Bloqueados</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-3 col-sm-12 text-center">
                            <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success tooltipsC" title="Consultar">Consultar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
				<div>
					<legend></legend>
				</div>
			</div>
            <div class="table-responsive">
                <table class="table display AllDataTables table-hover table-condensed" id="tabla-data-consulta" data-page-length="25">
                    <thead>
                        <tr>
                            {{-- <th></th> --}}
                            <th title='Orden de Produccion Detalle ID'>OpDet ID</th>
                            <th title='Fecha OpDet'>FechaOp</th>
                            <th title='Trazabilidad'>Traz</th>
                            <th>Razón Social</th>
                            <th title="Maquina">Maq</th>
                            <th title='Cod Producto'>CodProd</th>
                            <th title="Producto Descripcion">Producto</th>
                            <th title='Cantidad Recibidos'>CantRec</th>
                            <th title='Kg Recibidos'>KgRec</th>
                            <th title='Cantidad Producidos'>CantProd</th>
                            <th title='Kg Producidos'>KgProd</th>
                            <th title='Kg Producidos'>KgScrap</th>
                            <th title='Kg Producidos'>KgSaldo</th>
                            <th title='Produccion'>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    {{-- <tfoot>
                        <tr>
                            <th colspan='8' style='text-align:right'>Total página</th>
                            <th id='totalkg' name='totalkg' style='text-align:right'>0,00</th>
                            <th id='totaldinero' name='totaldinero' style='text-align:right'>0,00</th>
                            <th style='text-align:right'></th>
                        </tr>
            
                        <tr>
                            <th colspan='8' style='text-align:right'>TOTAL GENERAL</th>
                            <th style='text-align:right' id='totalgenkg' name='totalgenkg'>0,00</th>
                            <th style='text-align:right' id='totalgendin' name='totalgendin'>0,00</th>
                            <th style='text-align:right'></th>
                        </tr>
                    </tfoot>--}}
                </table>
            </div>
            <div class="col-lg-12 text-center">
                <button type='button' id='btnpdf' name='btnpdf' class='btn btn-success tooltipsC' title="Reporte PDF" onclick='btnpdf(1)'><i class='glyphicon glyphicon-print'></i> Reporte</button>
            </div>

            <!--
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                        <div class="table-responsive" id="tablaconsulta">
                        </div>			
                    </div>
                </div>
            </div>
            -->
        </div>
    </div>
</div>

@include('generales.buscarclientebd')
@include('generales.modalpdf')
@include('generales.verpdf')
@include('generales.buscarproductobd')
@endsection
