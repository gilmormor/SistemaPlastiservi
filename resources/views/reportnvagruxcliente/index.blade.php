@extends("theme.$theme.layout")
@section('titulo')
Total Notas de Venta Agrupada x Cliente
@endsection

<?php
    $selecmultprod = true;
?>

@section("scripts")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/reportnvagruxcliente/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/producto/buscar.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/cliente/buscar.js")}}" type="text/javascript"></script> 
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Total Notas de Venta Agrupada x Cliente</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <form action="{{route('exportPdf_notaventaconsulta')}}" class="d-inline form-eliminar" method="get" target="_blank">
                        @csrf
                        @csrf @method("put")

                        <input type="hidden" name="selecmultprod" id="selecmultprod" value="{{$selecmultprod}}">
                        <div class="col-xs-12 col-md-9 col-sm-12">
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Inicial">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="fecha">Fecha Ini:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" bsDaterangepicker class="form-control datepicker" name="fechad" id="fechad" value="{{old('fechad', date("01/m/Y") ?? '')}}" placeholder="DD/MM/AAAA" required readonly>
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
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Vendedor">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label>Vendedor:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name='vendedor_id' id='vendedor_id' class='selectpicker form-control vendedor_id' data-live-search='true' data-actions-box='true' multiple {{(count($tablashtml['vendedores']) == 1) ? "disabled" : ""}}>"
                                            <option value="">Todos</option>
                                            @foreach($tablashtml['vendedores'] as $vendedor)
                                                <option value="{{$vendedor->id}}"
                                                    @if (count($tablashtml['vendedores']) == 1)
                                                        {{'selected'}}
                                                    @endif
                                                >{{$vendedor->nombre}} {{$vendedor->apellido}}</option>";
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
                                        <input type="text" name="oc_id" id="oc_id" class="form-control" value="{{old('oc_id')}}" maxlength="18"/>
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
                                        <?php
                                            echo $tablashtml['comunas'];
                                        ?>
                                    </div>
                                </div>
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
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Categoria">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="categoriaprod_id" class="control-label">Categoria:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name='categoriaprod_id' id='categoriaprod_id' class='selectpicker form-control categoriaprod_id'  data-live-search='true' multiple data-actions-box='true'>"
                                            @foreach($tablashtml['categoriaprod'] as $categoriaprod)
                                                <option value="{{$categoriaprod->id}}">
                                                    {{$categoriaprod->nombre}}
                                                </option>";
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Categoria">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="sucursal_id" class="control-label">Sucursal:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="sucursal_id" id="sucursal_id" class='selectpicker form-control' data-live-search='true' data-actions-box='true' {{(count($tablashtml['sucursales']) == 1) ? "disabled" : ""}}>
                                            <option value="">Seleccione...</option>
                                            @foreach($tablashtml['sucursales'] as $sucursal)
                                                <option
                                                    value="{{$sucursal->id}}"
                                                    @if (count($tablashtml['sucursales']) == 1)
                                                        {{'selected'}}
                                                    @else
                                                        {{is_array(old('sucursal_id')) ? (in_array($sucursal->id, old('sucursal_id')) ? 'selected' : '') : (isset($data) ? ($data->sucursales->firstWhere('id', $sucursal->id) ? 'selected' : '') : '')}}
                                                    @endif
                                                    >{{$sucursal->nombre}}</option>
                                            @endforeach
                                        </select>            
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-3 col-sm-12">
                            <div class="col-xs-12 col-md-4 col-sm-4">
                                <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success tooltipsC" title="Consultar" onclick="ejecutarConsulta(1)">Consultar</button>
                            </div>
                            <!--<button type="button" id="btnpdf" name="btnpdf" class="btn btn-success tooltipsC" title="Reporte PDF"><i class='glyphicon glyphicon-print'></i> Reporte</button>-->
                            <div class="col-xs-12 col-md-4 col-sm-4">
                                <button type="button" id="btnpdfJS" name="btnpdfJS" class="btn btn-success tooltipsC" title="Reporte PDF" onclick="ejecutarConsulta(2)">
                                    <i class='glyphicon glyphicon-print'></i> Reporte
                                </button>
                            </div>
                            <div class="col-xs-12 col-md-4 col-sm-4">
                                <button type="button" id="btnexportarExcel" name="btnexportarExcel" class="btn btn-success tooltipsC" title="Exportar Excel" onclick="ejecutarConsulta(3)">
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
            <div class="table-responsive">
                <table id='tabla-data-consulta' name='tabla-data-consulta' class='table display AllDataTables table-hover table-condensed tablascons2' data-page-length='10'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>RUT</th>
                            <th>Razón Social</th>
                            <th>Comuna</th>
                            <th style='text-align:right' class='tooltipsC' title='KG PVC'>Kg PVC</th>
                            <th style='text-align:right' class='tooltipsC' title='KG '>Kg </th>
                            <th style='text-align:right' class='tooltipsC' title='Dinero'>$</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" style="text-align:right">Sub Total</th>
                            <th id="subkgpvc" name="subkgpvc" style="text-align:right" class="tooltipsC" title="Total KG PVC Pagina">0</th>
                            <th id="subkg" name="subkg" style="text-align:right" class="tooltipsC" title="Total KG Pagina">0</th>
                            <th id="subdinero" name="subdinero" style="text-align:right" class="tooltipsC" title="Total Dinero Pagina">0</th>
                        </tr>
                        <tr>
                            <th colspan="4" style="text-align:right">Total</th>
                            <th id="totalkgpvc" name="totalkgpvc" style="text-align:right" class="tooltipsC" title="Total KG PVC">0</th>
                            <th id="totalkg" name="totalkg" style="text-align:right" class="tooltipsC" title="Total KG">0</th>
                            <th id="totaldinero" name="totaldinero" style="text-align:right" class="tooltipsC" title="Total Dinero">0</th>
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
@include('generales.buscarproductobd')
@include('generales.listarorddesp')
@endsection
