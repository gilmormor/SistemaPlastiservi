@extends("theme.$theme.layout")
@section('titulo')
Orden Despacho
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/reportorddesp/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/producto/buscarprod.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/cliente/buscarcli.js")}}" type="text/javascript"></script>
@endsection

<?php 
    $selecmultprod = true;
?>
@section('contenido')
<input type="hidden" name="selecmultprod" id="selecmultprod" value="{{$selecmultprod}}">
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Consultar Orden Despacho</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <form action="{{route('exportPdf_notaventaconsulta')}}" class="d-inline form-eliminar" method="get" target="_blank">
                        @csrf
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
                                        <input type="text" class="form-control datepicker" name="fechah" id="fechah" value="{{old('fechah', $fechaServ['fechaAct'] ?? '')}}" placeholder="DD/MM/AAAA" required readonly="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Estimada de Despacho">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="fecha">Fecha ED:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" bsDaterangepicker class="form-control datepicker" name="fechaestdesp" id="fechaestdesp" placeholder="DD/MM/AAAA" required readonly="">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Estatus Nota de Venta">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label>Estatus:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="aprobstatus" id="aprobstatus" class="selectpicker form-control aprobstatus">
                                            <option value="0">Todos</option>
                                            <option value="1">Emitidas sin aprobar</option>
                                            <option value="2">Por debajo precio en tabla</option>
                                            <option value="3" selected>Aprobadas</option>
                                            <option value="4">Rechazadas</option>
                                        </select>
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
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Orden de Compra">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="oc_id">OC:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" name="oc_id" id="oc_id" class="form-control" value="{{old('oc_id')}}" maxlength="12"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Nro. Solicitud Despacho">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="despachosol_id">SolDespacho:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" name="despachosol_id" id="despachosol_id" class="form-control" value="{{old('despachosol_id')}}" maxlength="12"/>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Nro Orden Despacho">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="id">OrdDespacho:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" name="id" id="id" class="form-control" maxlength="10"/>
                                    </div>
                                </div>

                            </div>


                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Area de Producción">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label >Area Prod:</label>
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
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Tipo de Entrega">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label >T Entrega:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="tipoentrega_id" id="tipoentrega_id" class="selectpicker form-control tipoentrega_id">
                                            <option value="">Todos</option>
                                            @foreach($tipoentregas as $tipoentrega)
                                                <option
                                                    value="{{$tipoentrega->id}}"
                                                    >
                                                    {{$tipoentrega->nombre}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Giro">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label>Giro:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="giro_id" id="giro_id" class="selectpicker form-control giro_id">
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
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Comuna">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label>Comuna:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <?php
                                            echo $tablashtml['comunas'];
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Código Producto">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="producto_idPxP" class="control-label">Producto:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <div class="input-group">
                                            <input type="text" name="producto_idPxP" id="producto_idPxP" class="form-control" tipoval="numericootro"/>
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" id="btnbuscarproducto" name="btnbuscarproducto">Buscar</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Sucursal">
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
    
                        </div>
                        <div class="col-xs-12 col-md-3 col-sm-12 text-center">
                                <button type="button" id="btnconsultarpage" name="btnconsultarpage" class="btn btn-success tooltipsC" title="Consultar">Consultar</button>
                                <button type='button' id='btnpdf' name='btnpdf' class='btn btn-success tooltipsC' title="Reporte PDF">
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
                <table class="table display AllDataTables table-condensed table-hover" id="tabla-data-consulta" style="display:none;">
                    <thead>
                        <tr>
                            <th>OD</th>
                            <th>Fecha</th>
                            <th class='tooltipsC' title='Fecha Estimada de Despacho'>Fecha ED</th>
                            <th>Razón Social</th>
                            <th class='tooltipsC' title='Orden de Despacho'>OD</th>
                            <th class='tooltipsC' title='Solicitud de Despacho'>SD</th>
                            <th class='tooltipsC' title='Orden de Compra'>OC</th>
                            <th class='tooltipsC' title='Nota de Venta'>NV</th>
                            <th>Comuna</th>
                            <th class='tooltipsC' title='Total Kg'>Total Kg</th>
                            <th class='tooltipsC' title='Tipo de Entrega'>TE</th>
                            <th class='tooltipsC' title='Orden Despacho'>Despacho</th>
                            <th class='ocultar'>oc_file</th>
                            <th class='ocultar'>icono</th>
                            <th class='ocultar'>aprguiadespfh</th>
                            <th class='ocultar'>despachoordrec_id</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan='9' style='text-align:right'>Total página</th>
                            <th id='subtotalkg' name='subtotalkg' style='text-align:right'>0,00</th>
                            <th colspan='2' style='text-align:right'></th>
                        </tr>
                        <tr>
                            <th colspan='9' style='text-align:right'>TOTAL GENERAL</th>
                            <th id='totalkg' name='totalkg' style='text-align:right'>0,00</th>
                            <th colspan='2' style='text-align:right'></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="table-responsive1" id="print1">
                <table class="table display AllDataTables table-condensed table-hover" id="tabla-data-consulta1" style="display:none;">
                    <thead>
                        <tr>
                            <th>OD</th>
                            <th>Fecha</th>
                            <th class='tooltipsC' title='Fecha Estimada de Despacho'>Fecha ED</th>
                            <th>Razón Social</th>
                            <th class='tooltipsC' title='Orden de Despacho'>OD</th>
                            <th class='tooltipsC' title='Solicitud de Despacho'>SD</th>
                            <th class='tooltipsC' title='Orden de Compra'>OC</th>
                            <th class='tooltipsC' title='Nota de Venta'>NV</th>
                            <th>Comuna</th>
                            <th class='tooltipsC' title='Total Kg'>Total Kg</th>
                            <th class='tooltipsC' title='Tipo de Entrega'>TE</th>
                            <th class='tooltipsC' title='Orden Despacho'>Despacho</th>

                            <th class='tooltipsC' title='Fecha Guia'>F Guia</th>
                            <th class='tooltipsC' title='Num Factura'>NumFact</th>
                            <th class='tooltipsC' title='Fecha Factura'>F Fact</th>
                            <th class='tooltipsC' title='Motivo Rechazo'>Motivo</th>
                            <th class='ocultar'>oc_file</th>
                            <th class='ocultar'>anulada</th>
                            <th class='ocultar'>A</th>
                            <th class='ocultar'>documento_file</th>
                            <th class="ocultar">aprobstatus</th>
                            <th class="ocultar">aprobobs</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan='9' style='text-align:right'>Total página</th>
                            <th id='subtotalkg' name='subtotalkg' style='text-align:right'>0,00</th>
                            <th id='subtotaldinero' name='subtotaldinero' style='text-align:right'>0,00</th>
                            <th colspan='4' style='text-align:right'></th>
                        </tr>
                        <tr>
                            <th colspan='9' style='text-align:right'>TOTAL GENERAL</th>
                            <th id='totalkg' name='totalkg' style='text-align:right'>0,00</th>
                            <th id='totaldinero' name='totaldinero' style='text-align:right'>0,00</th>
                            <th colspan='4' style='text-align:right'></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>
@include('generales.buscarclientebdtemp')
@include('generales.buscarproductobdtemp')
@include('generales.modalpdf')
@include('generales.verpdf')
@endsection
