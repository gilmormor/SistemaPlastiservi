@extends("theme.$theme.layout")
@section('titulo')
    Nota de Venta
@endsection

@routes
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
    <script src="{{autoVer("assets/pages/scripts/notaventa/crear.js")}}" type="text/javascript"></script>
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
                <h3 class="box-title">Editar Nota de Venta Nro.: {{$data->id}}</h3>
                @if (session('aux_aproNV')=='0')
                <div class="box-tools pull-right">
                    <a href="{{route('notaventa')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
                @endif                    
            </div>
            <form action="{{route('actualizar_notaventa', ['id' => $data->id])}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off"  enctype="multipart/form-data">
                @csrf @method("put")
                <div class="box-body">
                    @include('notaventa.form')
                </div>
                <!-- /.box-body -->
                <div class="box-footer text-center">
                    @if (session('aux_aproNV')=='0')
                        @if (($data->vendedor_id == $vendedor_id) or ($data->usuario_id == auth()->id())) <!-- Solo deja modificar si el el mismo vendedor o si fue el usuario que creo el registro -->
                            @include('includes.boton-form-editar')
                        @endif
                    @else
                            <button type="reset" class="btn btn-default">Cancel</button>
                            <button type="button" id="btnguardaraprob" name="btnguardaraprob" class="btn btn-success">Aprobar/Rechazar</button>
                    @endif
                    <!--
                    <a href="{{route('exportPdf_notaventa', ['id' => $data->id,'stareport' => '1'])}}" class="btn-accion-tabla tooltipsC" title="PDF" target="_blank">
                        <i class="fa fa-fw fa-file-pdf-o"></i>                                    
                    </a>
                    -->
                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de venta: {{$data->id}}' onclick='genpdfNV({{$data->id}},1)'>
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