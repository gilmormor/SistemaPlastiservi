@extends("theme.$theme.layout")
@section('titulo')
Area Produccion por Sucursal - Asignar Etapas de produccion por Area Produccion y Sucursal
@endsection

<?php 
    $aux_vista = 'F';
?>

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    {{-- <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script> --}}
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/areaproduccionsucetapaprod/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/areaproduccionsucetapaprod/buscar.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Fases por Area Produccion</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            @csrf
            <div class="box-body">
                <div class="row">
                    <input type="hidden" name="selecmultprod" id="selecmultprod" value="{{old('selecmultprod', $selecmultprod ?? '')}}">
                    <input type="hidden" name="maquinas" id="maquinas" value="{{old('maquinas', $tablas['maquinas'] ?? '')}}">
                    <div class="col-xs-12 col-md-9 col-sm-12">
                        <div class="col-xs-12 col-md-12 col-sm-12">
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
                    </div>
                    <div class="col-xs-12 col-md-3 col-sm-12 text-center">
                        <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success tooltipsC" title="Consultar">Consultar</button>
                        {{-- <button type='button' id='btnpdf2' name='btnpdf2' class='btn btn-success tooltipsC' title="Reporte PDF">
                            <i class='glyphicon glyphicon-print'></i> Reporte
                        </button> --}}
                    </div>

                </div>        
            </div>
            <div class="row">
                <div>
                    <legend></legend>
                </div>
            </div>

            <div class="table-responsive">
                <table id='tabla-data-areaproduccionsucetapaprod' name='tabla-data-areaproduccionsucetapaprod' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
                <!--<table class="table table-striped table-bordered table-hover" id="tabla-data">-->
                    <tfoot>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@include('areaproduccionsucetapaprod.editarordenetapaprod')
@endsection