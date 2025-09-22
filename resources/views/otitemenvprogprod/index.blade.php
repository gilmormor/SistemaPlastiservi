@extends("theme.$theme.layout")
@section('titulo')
Enviar item OT a Programación de Producción
@endsection

<?php 
    $aux_vista = 'F';
?>

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    {{-- <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script> --}}
    <script src="{{autoVer("assets/pages/scripts/otitemenvprogprod/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/producto/buscar.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/cliente/buscar.js")}}" type="text/javascript"></script> 

@endsection
<style>
    table td {
        vertical-align: middle !important;
    }
</style>

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Enviar item OT a Programación de Producción</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            @csrf
            <div class="box-body">
                <div class="row">
                    <input type="hidden" name="selecmultprod" id="selecmultprod" value="{{old('selecmultprod', $selecmultprod ?? '')}}">
                    <div class="col-xs-12 col-md-9 col-sm-12">
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-md-6 col-sm-6" title="Fecha Inicial">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="fecha">Fecha Ini:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" bsDaterangepicker class="form-control datepicker" name="fechad" id="fechad"  value="{{old('fechad', $fechaServ['fecha1erDiaMes'] ?? '')}}" placeholder="DD/MM/AAAA" required readonly="">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6 col-sm-6" title="Fecha Final">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="dep_fecha">Fecha Fin:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" class="form-control datepicker" name="fechah" id="fechah" value="{{old('fechah', $fechaServ['fechaAct'] ?? '')}}" placeholder="DD/MM/AAAA" required readonly="">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-sm-6" title="RUT">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="rut">RUT:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <div class="input-group">
                                        <input type="text" name="rut1" id="rut1" class="form-control" value="{{old('rut')}}" placeholder="F2 Buscar" onkeyup="llevarMayus(this);" maxlength="12"/>
                                        <p id="error-message" style="color: red; display: none;">RUT inválido</p>
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button" id="btnbuscarcliente" name="btnbuscarcliente">Buscar</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6" title="Número OT">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Num OT:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" name="ot_id" id="ot_id" class="form-control numerico" value="{{old('ot_id')}}" maxlength="8"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-sm-6" title="Número Nota de Venta">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Nota Venta:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" name="notaventa_id" id="notaventa_id" class="form-control numerico" value="{{old('notaventa_id')}}" maxlength="10"/>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6" title="Orden de Compra">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Orden Compra:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" name="oc_id" id="oc_id" class="form-control" value="{{old('oc_id')}}" maxlength="14"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-sm-6" title="Ancho Inicial">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Ancho Ini:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" name="at_AnchoIni" id="at_AnchoIni" class="form-control numerico" value="{{old('at_AnchoIni')}}" maxlength="3"/>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6" title="Ancho Final">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Ancho Fin:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" name="at_AnchoFin" id="at_AnchoFin" class="form-control numerico" value="{{old('at_AnchoFin')}}" maxlength="3"/>
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
                            <div class="col-xs-12 col-sm-6" title="Sucursal">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Sucursal:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <select name="sucursal_id" id="sucursal_id" class="selectpicker form-control" data-live-search='true' multiple data-actions-box='true'>
                                        @foreach($tablas["sucursales"] as $sucursal)
                                            <option
                                                value="{{$sucursal->id}}"
                                            >{{$sucursal->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-sm-6" title="Area de Producción">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Materia prima:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <select name="materiaprima_id" id="materiaprima_id" class="selectpicker form-control" data-live-search='true' multiple data-actions-box='true'>
                                        @foreach($tablas["materiaprimas"] as $materiaprima)
                                            <option
                                                value="{{$materiaprima->id}}"
                                            >{{$materiaprima->nombre}}</option>
                                        @endforeach
                                    </select>
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

                </div>        
            </div>
            <div class="row">
                <div>
                    <legend></legend>
                </div>
            </div>

            <div class="table-responsive">
                <table id='tabla-data-factura' name='tabla-data-factura' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
                <!--<table class="table table-striped table-bordered table-hover" id="tabla-data">-->
                    <tfoot>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@include('generales.buscarclientebd')
@include('generales.buscarproductobd')
@include('generales.modalpdf')
@include('generales.despachoanularguiafact')
@endsection