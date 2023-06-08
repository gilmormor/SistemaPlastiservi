@extends("theme.$theme.layout")
@section('titulo')
    Cotización
@endsection

@section("styles")
    <link rel="stylesheet" href="{{autoVer("assets/js/bootstrap-fileinput/css/fileinput.min.css")}}">
@endsection

@section("scriptsPlugins")
    <script src="{{autoVer("assets/js/bootstrap-fileinput/js/fileinput.min.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/js/bootstrap-fileinput/js/locales/es.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/js/bootstrap-fileinput/themes/fas/theme.min.js")}}" type="text/javascript"></script>
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/cotizacion/crear.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/producto/buscar.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/cliente/buscar.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Crear Cotización</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('cotizacion')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('guardar_cotizacion')}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="box-body">
                    @include('cotizacion.form')
                </div>
                <div class="box-footer text-center">
                    @include('includes.boton-form-crear')
                </div>
            </form>
        </div>
    </div>
</div> 
@endsection