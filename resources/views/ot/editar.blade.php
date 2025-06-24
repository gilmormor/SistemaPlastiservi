@extends("theme.$theme.layout")
@section('titulo')
    Orden de Trabajo
@endsection

@section("styles")
    <link rel="stylesheet" href="{{asset("assets/js/bootstrap-fileinput/css/fileinput.min.css")}}">
@endsection

@section("scriptsPlugins")
    <script src="{{asset("assets/js/bootstrap-fileinput/js/fileinput.min.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/js/bootstrap-fileinput/js/locales/es.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/js/bootstrap-fileinput/themes/fas/theme.min.js")}}" type="text/javascript"></script>
@endsection

@section('scripts')
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/ot/crear.js")}}" type="text/javascript"></script>
    {{-- <script src="{{autoVer("assets/pages/scripts/cliente/buscar.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/producto/buscar.js")}}" type="text/javascript"></script> --}}
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Editar OT Nro.: {{$data->id}}</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('ot')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('actualizar_ot', ['id' => $data->id])}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off"  enctype="multipart/form-data">
                @csrf @method("put")
                <div class="box-body">
                    @include('ot.formnew')
                </div>
                <!-- /.box-body -->
                <div class="box-footer text-center">
                        @include('includes.boton-form-editar')
                </div>
                <!-- /.box-footer -->
            </form>
        </div>
    </div>
</div>
@include('generales.editarcamponum')
@endsection