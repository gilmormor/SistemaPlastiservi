@extends("theme.$theme.layout")
@section('titulo')
Solicitud Despacho
@endsection

@section("scripts")
    <script src="{{asset("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/despachoord/listardespachosol.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary collapsed-box">
            <div class="box-header with-border">
                <h3 class="box-title">Pendientes Solicitud Despacho</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                </div>
                <div class="box-tools pull-right">
                    <a href="{{route('despachoord')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
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
