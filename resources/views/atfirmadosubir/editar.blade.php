@extends("theme.$theme.layout")
@section('titulo')
    Subir Acuerdo Técnico Firmado
@endsection

@section("styles")
    <link rel="stylesheet" href="{{autoVer("assets/js/bootstrap-fileinput/css/fileinput.min.css")}}">
@endsection

@section("scriptsPlugins")
    <script src="{{autoVer("assets/js/bootstrap-fileinput/js/fileinput.min.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/js/bootstrap-fileinput/js/locales/es.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/js/bootstrap-fileinput/themes/fas/theme.min.js")}}" type="text/javascript"></script>
@endsection

@section('scripts')
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/atfirmadosubir/crear.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
    <div class="row">
        <div class="col-lg-12">
            @include('includes.form-error')
            @include('includes.mensaje')
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Editar Acuerdo Técnico Id:</h3>
                    <a class='btn-accion-tabla btn-sm' onclick='genpdfAcuTec({{$data->acuerdotecnico->id}},"")' title='Ver PDF AT' data-toggle='tooltip'>
                        {{$data->acuerdotecnico->id}} <i class='fa fa-fw fa-file-pdf-o'></i>
                    </a>
                    <div class="box-tools pull-right">
                        <a href="{{route('atfirmadosubir')}}" class="btn btn-block btn-info btn-sm">
                            <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                        </a>
                    </div>
                </div>
                <form action="{{route('actualizar_atfirmadosubir', ['id' => $data->id])}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off" enctype="multipart/form-data">
                    @csrf @method("put")
                    <div class="box-body">
                        @include('atfirmadosubir.form')
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
    @include('generales.modalpdf')
@endsection