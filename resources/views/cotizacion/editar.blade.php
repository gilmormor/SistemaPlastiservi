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

@section('scripts')
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/cotizacion/crear.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/producto/buscar.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/cliente/buscar.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/producto/buscarselectmult.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Editar Cotización Nro.: {{$data->id}}</h3>
                @if (session('aux_aprocot')=='0')
                    <div class="box-tools pull-right">
                        <a href="{{route('cotizacion')}}" class="btn btn-block btn-info btn-sm">
                            <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                        </a>
                    </div>
                @endif
            </div>
            <form action="{{route('actualizar_cotizacion', ['id' => $data->id])}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off" enctype="multipart/form-data">
                @csrf @method("put")
                <div class="box-body">
                    @include('cotizacion.form')
                </div>
                <!-- /.box-body -->
                <div class="box-footer text-center">
                    @if (session('aux_aprocot')=='0')
                        @if (($data->vendedor_id == $tablas['vendedor_id']) or ($data->usuario_id == auth()->id()) or (auth()->id() == 1)) <!-- Solo deja modificar si es el mismo vendedor o si fue el usuario que creo el registro or si es el usuario administrador-->
                            @include('includes.boton-form-editar')
                        @endif
                    @else
                        <button type="reset" class="btn btn-default">Cancel</button>
                        <button type="button" id="btnguardaraprob" name="btnguardaraprob" class="btn btn-success">Aprobar/Rechazar</button>
                    @endif
                    <!--
                    <a href="{{route('exportPdf_cotizacion', ['id' => $data->id])}}" class="btn-accion-tabla tooltipsC" title="PDF" target="_blank">
                        <i class="fa fa-fw fa-file-pdf-o"></i>                                    
                    </a>
                    -->
                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Cotizacion: {{$data->id}}' onclick='genpdfCOT({{$data->id}},1)'>
                        <i class='fa fa-fw fa-file-pdf-o'></i>
                    </a>
                </div>
                <!-- /.box-footer -->
            </form>
        </div>
    </div>
</div>
@include('generales.modalpdf')
@endsection