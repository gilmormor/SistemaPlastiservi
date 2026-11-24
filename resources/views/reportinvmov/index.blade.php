@extends("theme.$theme.layout")
@section('titulo')
Movimiento de Inventario
@endsection

@section("scripts")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/reportinvmov/index.js")}}" type="text/javascript"></script>
    {{-- <script src="{{autoVer("assets/pages/scripts/producto/buscar.js")}}" type="text/javascript"></script> --}}
    <script src="{{autoVer("assets/pages/scripts/producto/buscarnew.js")}}" type="text/javascript"></script>
@endsection

<?php
    use App\Models\CategoriaGrupoValMes;
    $aux_mesanno = CategoriaGrupoValMes::mesanno(date("Y") . date("m"));
    $selecmultprod = true;
?>
@section('contenido')
<input type="hidden" name="selecmultprod" id="selecmultprod" value="{{$selecmultprod}}">
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Movimiento de Inventario</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    @csrf
                    <div class="col-xs-12 col-md-9 col-sm-12">
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-md-6 col-sm-6" title="Mes">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="annomes">Fecha:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" name="annomes" id="annomes" class="form-control date-pickermes" value="{{old('annomes', $aux_mesanno ?? '')}}" readonly required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6 col-sm-6" title="Sucursal">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="sucursal_id" >Sucursal</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <?php
                                        $sucursal_id = 0;
                                        if(count($tablashtml['sucursales']) == 1){
                                            $sucursal_id = $tablashtml['sucursales'][0]->id;
                                        }
                                    ?>
                                    <select name="sucursal_id" id="sucursal_id" class="selectpicker form-control" required>
                                        <option value="0">Seleccione...</option>
                                        @foreach($tablashtml['sucursales'] as $sucursal)
                                            <option
                                                value="{{$sucursal->id}}"
                                                @if ($sucursal->id == $sucursal_id))
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
                            <div class="col-xs-12 col-md-6 col-sm-6" title="Fecha Inicial Orden Despacho">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="fecha">Fecha Ini:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" bsDaterangepicker class="form-control datepicker" name="fechad" id="fechad" placeholder="DD/MM/AAAA" required readonly="">
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6 col-sm-6" title="Fecha Final Orden Despacho">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="dep_fecha">Fecha Fin:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" class="form-control datepicker" name="fechah" id="fechah"placeholder="DD/MM/AAAA" required readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-sm-6" title="Area de Producción">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label >Area Prod:</label>
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
                            <div class="col-xs-12 col-sm-6" title="Código Producto">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="producto_idPxP" class="control-label">Producto</label>
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
                            <div class="col-xs-12 col-sm-6" title="Bodega">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Bodega:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <select name="invbodega_id" id="invbodega_id" class="selectpicker form-control invbodega_id" data-live-search='true' multiple data-actions-box='true'>
                                        @foreach($tablashtml['invbodegas'] as $invbodega)
                                            <option
                                                value="{{$invbodega->id}}" sucursal_id="{{$invbodega->sucursal_id}}"
                                                >{{$invbodega->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6" title="Buscar por descripcion">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="buscarxdesc" class="control-label">Descripción</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" name="buscarxdesc" id="buscarxdesc" class="form-control" tipoval="texto"/>
                                </div>
                            </div>
                        </div>

                        {{-- Filtros de origen y destino del movimiento.
                             Modulo: de donde viene el movimiento (Produccion, Entrada
                             y Salida Inv, Guia de Despacho...).
                             Destino: si entro para una nota de venta concreta o quedo
                             como stock disponible (OT libre, carga manual). --}}
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-sm-6" title="Modulo que origino el movimiento">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Módulo:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <select name="invmovmodulo_id" id="invmovmodulo_id" class="selectpicker form-control" data-live-search='true' multiple data-actions-box='true' title="Todos">
                                        @foreach($tablashtml['invmovmodulos'] as $invmovmodulo)
                                            <option value="{{$invmovmodulo->id}}">{{$invmovmodulo->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6" title="Para quien es el movimiento">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Destino:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <select name="destino" id="destino" class="selectpicker form-control">
                                        <option value="">Todos</option>
                                        <option value="nv">Solo los de una nota de venta</option>
                                        <option value="stock">Solo los que quedaron para stock</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-xs-12 col-md-3 col-sm-12 text-center" style="padding-left: 0px;padding-right: 0px;">
                        <!--
                        <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success tooltipsC" title="Consultar">Consultar</button>
                        <button type='button' id='btnpdf' name='btnpdf' class='btn btn-success tooltipsC' title="Reporte PDF"><i class='glyphicon glyphicon-print'></i> Reporte</button>
                        -->
                        <button type="button" id="btnconsultarpage" name="btnconsultarpage" class="btn btn-success tooltipsC" title="Consultar">Consultar</button>
                        <button type='button' id='btnpdf' name='btnpdf' class='btn btn-success tooltipsC' title="Reporte PDF">
                            <i class='glyphicon glyphicon-print'></i> Reporte
                        </button>
                        <button type="button" id="btnexportarExcel" name="btnexportarExcel" class="btn btn-success tooltipsC" title="Exportar Excel" onclick="exportarExcel()">Excel</button>

                    </div>
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
                <table class="table display AllDataTables table-condensed table-hover" id="tabla-data-invmov">
                    <thead>
                        <tr>
                            <th class='tooltipsC' title='Id MovInv'>ID</th>
                            <th class='tooltipsC' title='Usuario'>Usuario</th>
                            <th class='tooltipsC' title='Id Detalle'>IDDet</th>
                            <th class='tooltipsC' title='Origen'>Origen</th>
                            <th>Fecha</th>
                            <th title='Descripcion del movimiento'>Descripción</th>
                            <th title='Código Producto'>CodProd</th>
                            <th title='Nombre Producto'>Producto</th>
                            <th class='tooltipsC' title='Modulo de Origen'>Modulo</th>
                            {{-- Para quien: la nota de venta a la que pertenece el
                                 movimiento, o "Stock" si quedo disponible --}}
                            <th class='tooltipsC' title='Nota de venta a la que pertenece, o Stock si quedo disponible'>Destino</th>
                            <th>Bodega</th>
                            <th>Cant</th>
                            {{-- Unidad de medida del detalle del movimiento --}}
                            <th class='tooltipsC' title='Unidad de medida del movimiento'>UM</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                        </tr>
                        <tr>
                            {{-- 11 y no 10: la columna Destino se agrego antes de Cant --}}
                            <th colspan='11' style='text-align:right'>TOTAL</th>
                            <th id='totalcant' name='totalcant' style='text-align:right'>0,00</th>
                            {{-- celda vacia que empareja el pie con la nueva columna UM --}}
                            <th></th>
                        </tr>
                    </tfoot>

        
                </table>
            </div>

        </div>
    </div>
</div>
@include('generales.modalpdf')
@include('generales.verpdf')
@include('generales.buscarproductobd')
{{-- Modal de la etiqueta de lote: lo abre el enlace de la columna Origen cuando
     el movimiento viene del modulo de Produccion (invmovmodulo_id = 9). --}}
@include('generales.modaletiquetaetapa')
@endsection