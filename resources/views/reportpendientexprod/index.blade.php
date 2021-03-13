@extends("theme.$theme.layout")
@section('titulo')
Pendiente x Producto
@endsection

@section("scripts")
    <script src="{{asset("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/reportpendientexprod/index.js")}}" type="text/javascript"></script>
@endsection


@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Pendientes por Producto</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
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
                            <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Plazo Entrega Inicial">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="fecha">Plazo EntIni:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" bsDaterangepicker class="form-control datepicker" name="plazoentregad" id="plazoentregad" placeholder="DD/MM/AAAA" required readonly>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Plazo Entrega Final">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="dep_fecha">Plazo EntFin:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" class="form-control datepicker" name="plazoentregah" id="plazoentregah" value="{{old('fechah', $fechaAct ?? '')}}" placeholder="DD/MM/AAAA" required readonly="">
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
                                    <select name="vendedor_id" id="vendedor_id" class="selectpicker form-control vendedor_id">
                                        <option value="">Todos</option>
                                        @foreach($vendedores1 as $vendedor)
                                            <option
                                                value="{{$vendedor->id}}"
                                                >
                                                {{$vendedor->nombre}} {{$vendedor->apellido}}
                                            </option>
                                        @endforeach
                                    </select>
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
                            <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Comuna">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Comuna:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <select name="comuna_id" id="comuna_id" class="selectpicker form-control comuna_id" data-live-search="true">
                                        <option value="">Todos</option>
                                        @foreach($comunas as $comuna)
                                            <option
                                                value="{{$comuna->id}}"
                                                >
                                                {{$comuna->nombre}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Producto">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="producto_idM" class="control-label">Producto</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <div class="input-group">
                                        <input type="text" name="producto_idM" id="producto_idM" class="form-control" tipoval="numericootro" value="{{old('producto_idM', $clienteselec[0]->producto_idM ?? '')}}" placeholder="F2 Buscar"/>
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button" id="btnbuscarproducto" name="btnbuscarproducto">Buscar</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3 col-sm-12">
                        <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success tooltipsC" title="Consultar">Consultar</button>
                        <button type="button" id="btnpdf" name="btnpdf" class="btn btn-success tooltipsC" title="Reporte"><i class='glyphicon glyphicon-print'></i> Reporte</button>
                    </div>
                </div>
            </div>
            <div class="row">
				<div>
					<legend></legend>
				</div>
			</div>
<!--
            <div class="table-responsive" id="tablaconsulta">
            </div>-->
            
            <!--
            <div class="row">
                <div class="col-md-12">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_1" data-toggle="tab"  id="tab1" name="tab1">Pendiente x Nota Venta</a></li>
                            <li><a href="#tab_2" data-toggle="tab" id="tab2" name="tab2">Pendiente x Cliente</a></li>
                            <li><a href="#tab_3" data-toggle="tab" id="tab3" name="tab3">Pendiente x producto</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_1">
                                <div class="table-responsive" id="tablaconsulta">
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_2">
                                <div class="table-responsive" id="tablaconsulta2">
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_3">
                                <div class="table-responsive" id="tablaconsulta3">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            -->
            <div class="table-responsive" id="tablaconsulta3">
            </div>
            <!--
            <div class="container">
                <div class="row">
                </div>
            </div>
            -->
        </div>
    </div>
</div>
@include('generales.buscarcliente')
@include('generales.modalpdf')
@include('generales.verpdf')
@include('generales.buscarproducto')
@include('generales.listarorddesp')
@endsection
