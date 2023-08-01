@extends("theme.$theme.layout")
@section('titulo')
Pesaje
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/reportpesaje/index.js")}}" type="text/javascript"></script>
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
    <div class="box box-primary box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Pesaje</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-xs-12 col-md-9 col-sm-12">
                    <div class="col-xs-12 col-md-12 col-sm-12">
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Inicial">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="fecha">Fecha Ini:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" bsDaterangepicker class="form-control datepicker" name="fechad" id="fechad" value="{{old('fechad', $tablashtml['fechaServ']['fecha1erDiaMes'] ?? '')}}" placeholder="DD/MM/AAAA" required readonly>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Final">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="dep_fecha">Fecha Fin:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <input type="text" class="form-control datepicker" name="fechah" id="fechah" value="{{old('fechah', $tablashtml['fechaServ']['fechaAct'] ?? '')}}" placeholder="DD/MM/AAAA" required readonly="">
                                </div>
                            </div>
                        </div>    
                        <div class="col-xs-12 col-md-12 col-sm-12">
                            <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Sucursal">
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
                                        <option value="x">Seleccione...</option>
                                        @foreach($tablashtml['sucursales'] as $sucursal)
                                            <option
                                                value="{{$sucursal->id}}"
                                                @if ( $sucursal->id == $sucursal_id )
                                                    {{'selected'}}
                                                @endif
                                            >
                                                {{$sucursal->nombre}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Grupo">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="categoriaprodgrupo_id" >Grupo</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <select name="categoriaprodgrupo_id" id="categoriaprodgrupo_id" class="selectpicker form-control" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($tablashtml['categoriaprodgrupos'] as $categoriaprodgrupo)
                                            <option
                                                value="{{$categoriaprodgrupo->id}}"
                                            >{{$categoriaprodgrupo->nombre}}</option>
                                        @endforeach
                                    </select>
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
                                            <button class="btn btn-default" type="button" id="btnbuscarproducto" name="btnbuscarproducto">Buscar</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Agrupar">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label for="agrurep_id" >Agrupar</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <select name="agrurep_id" id="agrurep_id" class="selectpicker form-control" required>
                                        <option value="1" selected>Sin Agrupar</option>
                                        <option value="2">Por Producto</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-xs-12 col-md-3 col-sm-12">
                    <div class="col-xs-12 col-md-12 col-sm-12 text-center">
                        <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success tooltipsC" title="Consultar">Consultar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" id="reporte1" name="reporte1">
    <!-- Custom Tabs -->
    <div class="nav-tabs-custom" id="tabs">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab_1" data-toggle="tab"  id="tab1" name="tab1">Producto</a></li>
            <li><a href="#tab_2" data-toggle="tab" id="tab2" name="tab2">Fecha</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
                <div class="col-lg-12">
                    <div class="box box-danger">
                        <div class="box-header with-border">
                            <h3 class="box-title">Producto</h3>
                            <div class="box-tools pull-right">
                                <a id="btnpdf" name="btnpdf" class="btn btn-block btn-success btn-sm">
                                    <i class="fa fa-fw fa-file-pdf-o"></i>PDF
                                </a>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="tabla-data-reporte-pesaje-sin-agrupar" data-page-length="25">
                                    <thead>
                                        <tr>
                                            <th class="width70 tooltipsC" title="Codigo Producto" style='text-align:center'>Cod</th>
                                            <th>Producto</th>
                                            <th class="width20 tooltipsC" title="Peso Norma" style="text-align:right;">PN</th>
                                            <th class="width50 tooltipsC" title="Linea">Linea</th>
                                            <th class="width50 tooltipsC" title="Turno">Turno</th>
                                            <th class="width70 tooltipsC" title="Carro">Carro</th>
                                            <th class="width30 tooltipsC" title="Peso Carro">Tara</th>
                                            <th class="width60" style="text-align:right;">Cant</th>
                                            <th class="width70 tooltipsC" title="Peso Balanza" style="text-align:right;">PesoBal</th>
                                            <th class="width20 tooltipsC" title="Peso promedio Unitario Balanza" style="text-align:right;">PUB</th>
                                            <th class="width70 tooltipsC" title="Peso Total Norma" style="text-align:right;">Nominal</th>
                                            <th class="width70 tooltipsC" title="Peso Total Producto en Balanza" style="text-align:right;">PesoTubo</th>
                                            <th class="width70 tooltipsC" title="Diferencia Kg" style="text-align:right;">DifKg</th>
                                            <th class="width70 tooltipsC" title="Var %" style="text-align:right;">Var%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                        </tr>
                                        <tr>
                                            <th colspan='6' style='text-align:right' id='Total' name='Total'>Total</th>
                                            <th id='subtotalTara' name='subtotalTara' style='text-align:right'>0</th>
                                            <th></th>
                                            <th id='subtotalPesoBal' name='subtotalPesoBal' style='text-align:right'>0</th>
                                            <th></th>
                                            <th id='subtotalPesoTotalNorma' name='subtotalPesoTotalNorma' style='text-align:right'>0</th>
                                            <th id='subtotalPesoTotalProdBal' name='subtotalPesoTotalProdBal' style='text-align:right'>0</th>
                                            <th id='subtotalDifKg' name='subtotalDifKg' style='text-align:right'>0</th>
                                            <th id='subtotalVar' name='subtotalVar' style='text-align:right'>0</th>                    
                                        </tr>
                                        <tr>
                                            <th colspan='6' style='text-align:right' id='TotalPeriodo' name='TotalPeriodo'>Total periodo:</th>
                                            <th id='TotalTara' name='TotalTara' style='text-align:right'>0</th>
                                            <th></th>
                                            <th id='TotalPesoBal' name='TotalPesoBal' style='text-align:right'>0</th>
                                            <th></th>
                                            <th id='TotalPesoTotalNorma' name='TotalPesoTotalNorma' style='text-align:right'>0</th>
                                            <th id='TotalPesoTotalProdBal' name='TotalPesoTotalProdBal' style='text-align:right'>0</th>
                                            <th id='TotalDifKg' name='TotalDifKg' style='text-align:right'>0</th>
                                            <th id='TotalVar' name='TotalVar' style='text-align:right'>0</th>                    
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tab_2">
                <div class="col-lg-12">
                    <div class="box box-danger">
                        <div class="box-header with-border">
                            <h3 class="box-title">Fecha</h3>
                            <div class="box-tools pull-right">
                                <a id="btnpdfFec" name="btnpdfFec" class="btn btn-block btn-success btn-sm">
                                    <i class="fa fa-fw fa-file-pdf-o"></i>PDF
                                </a>
                            </div>
                        </div>
                        <div class="box-body">

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="tabla-data-reporte-pesaje-por-fecha" data-page-length="25">
                                    <thead>
                                        <tr>
                                            <th class="width70 tooltipsC" title="Fecha" style='text-align:center'>Fecha</th>
                                            <th class="width70 tooltipsC" title="Peso Total Norma" style="text-align:right;">Nominal</th>
                                            <th class="width70 tooltipsC" title="Peso Total Producto en Balanza" style="text-align:right;">PesoTubo</th>
                                            <th class="width70 tooltipsC" title="Diferencia Kg" style="text-align:right;">DifKg</th>
                                            <th class="width70 tooltipsC" title="Var %" style="text-align:right;">Var%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                        </tr>
                                        <tr>
                                            <th style='text-align:right' id='Total' name='Total'>Total</th>
                                            <th id='totalFecnominal' name='totalFecnominal' style='text-align:right'>0</th>
                                            <th id='totalFecpesotubo' name='totalFecpesotubo' style='text-align:right'>0</th>
                                            <th id='totalFecDifKg' name='totalFecDifKg' style='text-align:right'>0</th>
                                            <th id='totalFecVar' name='totalFecVar' style='text-align:right'>0</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('generales.modalpdf')
@include('generales.verpdf')
@include('generales.buscarproductobd')
@endsection