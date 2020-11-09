@extends("theme.$theme.layout")
@section('titulo')
    Orden de Despacho
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
    <script src="{{asset("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/despachoord/crear.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Editar Orden Despacho Nro.: {{$data->id}}</h3>
                <a class='btn-accion-tabla btn-sm' onclick='genpdfNV({{$data->notaventa_id}},1)' title='Ver Nota venta' data-toggle='tooltip'>
                    Nota Venta: {{$data->notaventa_id}} <i class='fa fa-fw fa-file-pdf-o'></i>
                </a>
                <a class='btn-accion-tabla btn-sm' onclick='verpdf2("{{$data->notaventa->oc_file}}",2)' title='Ver Nota venta' data-toggle='tooltip'>
                    Orden Compra: {{$data->notaventa->oc_id}} <i class='fa fa-fw fa-file-pdf-o'></i>
                </a>
                <div class="box-tools pull-right">
                    <a href="{{route('despachoord')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('actualizar_despachoord', ['id' => $data->id])}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off"  enctype="multipart/form-data">
                @csrf @method("put")
                <div class="box-body">
                    @include('despachoord.formedit')
                </div>
                <!-- /.box-body -->
                <div class="box-footer text-center">
                    <!--@if (($data->notaventa->vendedor_id == $vendedor_id) or ($data->usuario_id == auth()->id())) --><!-- Solo deja modificar si el el mismo vendedor o si fue el usuario que creo el registro -->
                        @include('includes.boton-form-editar')
                    <!--@endif-->
                </div>
                <!-- /.box-footer -->
            </form>
        </div>
    </div>
</div>
@endsection