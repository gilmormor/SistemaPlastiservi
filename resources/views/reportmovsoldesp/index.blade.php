@extends("theme.$theme.layout")
@section('titulo')
Movimiento Solicitud Despacho
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/reportmovsoldesp/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Movimiento Solicitud Despacho</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <form class="d-inline" method="get" target="_blank">
                        @csrf
                        <div class="col-xs-12 col-md-9 col-sm-12">
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Nro. Solicitud Despacho">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="despachosol_id" class="requerido">SolDespacho:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8" classorig="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" name="despachosol_id" id="despachosol_id" class="form-control numerico" value="{{old('despachosol_id')}}" maxlength="12" required/>
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
                    </form>
                </div>
            </div>
            <div class="row">
                <div>
                    <legend></legend>
                </div>
            </div>
            
            <div class="table-responsive" id="tablaconsulta">
            </div>
        </div>
    </div>
</div>
@include('generales.buscarcliente')
@include('generales.modalpdf')
@include('generales.verpdf')
@endsection
