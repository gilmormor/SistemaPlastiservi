@extends("theme.$theme.layout")
@section('titulo')
Stock Inventario
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/reportinvstockbppendxprod/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/producto/buscarprod.js")}}" type="text/javascript"></script>
@endsection
<?php
    use App\Models\CategoriaGrupoValMes;
    $aux_mesanno = CategoriaGrupoValMes::mesanno(date("Y") . date("m"));
    $selecmultprod = true;
?>
@section('contenido')
<input type="hidden" name="selecmultprod" id="selecmultprod" value="{{$selecmultprod}}">
<input type="hidden" name="tipobodega" id="tipobodega" value="1,2">
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Stock Inventario</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 col-md-9 col-sm-12">
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-md-6 col-sm-6">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="annomes" data-toggle='tooltip' title="Mes">Fecha:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" name="annomes" id="annomes" class="form-control date-picker" value="{{old('annomes', $aux_mesanno ?? '')}}" readonly required>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6 col-sm-6">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="sucursal_id" data-toggle='tooltip' title="Sucursal">Sucursal</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <?php
                                            $sucursal_id = 0;
                                            if(count($tablashtml['sucursales']) == 1){
                                                $sucursal_id = $tablashtml['sucursales'][0]->id;
                                            }
                                        ?>
                                        <select name="sucursal_id" id="sucursal_id" class="selectpicker form-control" required>
                                            <option value="x">Seleccione...</option>
                                            @foreach($tablashtml['sucursales'] as $sucursal)
                                                <option
                                                    value="{{$sucursal->id}}"
                                                    @if ( $sucursal->id == $sucursal_id ))
                                                        {{'selected'}}
                                                    @endif
                                                >
                                                    {{$sucursal->nombre}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="categoriaprod_id" class="control-label" data-toggle='tooltip' title="Categoria">Categoria:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name='categoriaprod_id' id='categoriaprod_id' class='selectpicker form-control categoriaprod_id' data-live-search='true' multiple data-actions-box='true'>
                                            @foreach($tablashtml['categoriaprod'] as $categoriaprod)
                                                <option value="{{$categoriaprod->id}}">
                                                    {{$categoriaprod->nombre}}
                                                </option>";
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label data-toggle='tooltip' title="Area de Producción">Area Prod:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="areaproduccion_id" id="areaproduccion_id" class="selectpicker form-control areaproduccion_id" data-live-search='true' multiple data-actions-box='true'>
                                            @foreach($tablashtml['areaproduccions'] as $areaproduccion)
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
                                        <label for="producto_idPxP" class="control-label" data-toggle='tooltip' title="Código Producto">Producto</label>
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
                                <div class="col-xs-12 col-sm-6">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label data-toggle='tooltip' title="Estatus Nota de Venta">Estatus NV:</label>
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
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3 col-sm-12">
                        <div class="col-xs-12 col-md-12 col-sm-12 text-center">
                            <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success tooltipsC" title="Consultar">Consultar</button>
                            <button type='button' id='btnpdf' name='btnpdf' class='btn btn-success tooltipsC' title="Reporte PDF">
                                <i class='glyphicon glyphicon-print'></i> Reporte
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div>
                    <legend></legend>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="tabla-data-reporte-stockpicking" data-page-length="25">
                    <thead>
                        <tr>
                            <th class="width70 tooltipsC" title="Codigo Producto" style='text-align:center'>Cod</th>
                            <th>Producto</th>
                            <th>Categoria</th>
                            <th>Diametro</th>
                            <th>Clase</th>
                            <th>Largo</th>
                            <th>Peso</th>
                            <th class="tooltipsC" title="Tipo de Union">TU</th>
                            <th style='text-align:center'>Bodega</th>
                            <th style='text-align:center'>Picking</th>
                            <th style='text-align:center'>Stock</th>
                            <th style='text-align:center'>Pend</th>
                            <th style='text-align:center'>Dif</th>
                            <!--<th style='text-align:right'>Kg</th>-->
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <!--
                    <tfoot>
                        <tr>
                        </tr>
                        <tr>
                            <th colspan='13' style='text-align:right'>Total página</th>
                            <th id='subtotalkg' name='subtotalkg' style='text-align:right'>0,00</th>
                        </tr>
                        <tr>
                            <th colspan='13' style='text-align:right'>TOTAL GENERAL</th>
                            <th id='totalkg' name='totalkg' style='text-align:right'>0,00</th>
                        </tr>
                    </tfoot>
                    -->
                </table>
            </div>
        </div>
    </div>
</div>
@include('generales.modalpdf')
@include('generales.verpdf')
@include('generales.buscarproductobdtemp')
@endsection