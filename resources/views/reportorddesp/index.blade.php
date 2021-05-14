@extends("theme.$theme.layout")
@section('titulo')
Orden Despacho
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/reportorddesp/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
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
            @include('generales.filtrosconsultasoldespacho')  <!--Filtros consulta solicitid de despacho-->
        </div>
    </div>
</div>
@include('generales.buscarcliente')
@include('generales.modalpdf')
@include('generales.verpdf')
@endsection
