@extends("theme.$theme.layout")
@section('titulo')
Pendiente x Producto
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/reportpendxprod/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/producto/buscar.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/cliente/buscar.js")}}" type="text/javascript"></script> 
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
                <h3 class="box-title">Pendientes por Producto</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
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
                    <div class="col-xs-12 col-md-3 col-sm-12">
                        <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success tooltipsC" title="Consultar">Consultar</button>
                        <button type="button" id="btnpdf" name="btnpdf" class="btn btn-success tooltipsC" title="Reporte PDF"><i class='glyphicon glyphicon-print'></i> Reporte</button>
                    </div>
                </div>
            </div>
            <div class="row">
				<div>
					<legend></legend>
				</div>
			</div>
            <div class="table-responsive">
                <table id='tabla-data-consulta' name='tabla-data-consulta' class='table display AllDataTables table-hover table-condensed tablascons2' data-page-length='25'>
                    <thead>
                        <tr>
                            <th>NV</th>
                            <th>OC</th>
                            <th>Fecha</th>
                            <th>Plazo<br>Entrega</th>
                            <th>Razón Social</th>
                            <th>Comuna</th>
                            <th class='tooltipsC' title='Código Producto'>Cod</th>
                            <th>Producto</th>
                            <th>Clase<br>Sello</th>
                            <th>Diametro<br>Ancho</th>
                            <th>Largo</th>
                            <th>Peso<br>Espesor</th>
                            <th>TU</th>
                            <th style='text-align:right'>Stock</th>
                            <th style='text-align:right'>Cant</th>
                            <th style='text-align:right' class='tooltipsC' title='Cantidad Despachada'>Cant<br>Desp</th>
                            <th style='text-align:right' class='tooltipsC' title='Cantidad Pendiente'>Cant<br>Pend</th>
                            <th style='text-align:right' class='tooltipsC' title='Kilos Pendiente'>Kilos<br>Pend</th>
                            <th style='text-align:right' class='tooltipsC' title='Precio por Kilo'>Precio<br>Kilo</th>
                            <th style='text-align:right' class='tooltipsC' title='Dinero'>$</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <?php 
                        $aux_totalcant = 0;
                        $aux_totalcantdesp = 0;
                        $aux_totalcantpend = 0;
                        $aux_totalkilospend = 0;
                        $aux_totalplata = 0;
                        $aux_promprecioxkilo = 0;
                        $aux_totalkilospend = 0;
                        $aux_promprecioxkilo = 0;
                    ?>
                    <tfoot>
                        <tr>
                            <th colspan="14" style="text-align:right">TOTALES</th>
                            <th style="text-align:right" class="tooltipsC" title="Cantidad">{{number_format($aux_totalcant, 0, ",", ".")}}</th>
                            <th style="text-align:right" class="tooltipsC" title="Cantidad Despachada">{{number_format($aux_totalcantdesp, 0, ",", ".")}}</th>
                            <th style="text-align:right" class="tooltipsC" title="Cantidad Pendiente">{{number_format($aux_totalcantpend, 0, ",", ".")}}</th>
                            <th style="text-align:right" class="tooltipsC" title="Kg Pendientes">{{number_format($aux_totalkilospend, 2, ",", ".")}}</th>
                            <th style="text-align:right"></th>
                            <th style="text-align:right" class="tooltipsC" title="Total $">{{number_format($aux_totalplata, 2, ",", ".")}}</th>
                        </tr>
                        <tr>
                            <th colspan="14" style="text-align:right">PROMEDIO</th>
                            <th colspan="4" style="text-align:right"></th>
                            <th style="text-align:right" class="tooltipsC" title="Precio Kg Promedio">{{number_format($aux_promprecioxkilo, 2, ",", ".")}}</th>
                            <th style="text-align:right" class="tooltipsC" title="Total $ (Precio promedio)">{{number_format($aux_totalkilospend * $aux_promprecioxkilo, 2, ",", ".")}}</th>
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