@extends("theme.$theme.layout")
@section('titulo')
    Entrada, Salida Inventario
@endsection


@section('scripts')
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/inventsal/crear.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/producto/buscarprod.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Editar Entrada, Salida Inventario Nro.: {{$data->id}}</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('inventsal')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('actualizar_inventsal', ['id' => $data->id])}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off"  enctype="multipart/form-data">
                @csrf @method("put")
                <div class="box-body">
                    @include('inventsal.form')
                </div>
                <!-- /.box-body -->
                <div class="box-footer text-center">
                    @if ($data->usuario_id == auth()->id()) <!-- Solo deja modificar si el el mismo vendedor o si fue el usuario que creo el registro -->
                        @include('includes.boton-form-editar')
                    @endif
                </div>
                <!-- /.box-footer -->
            </form>
        </div>
    </div>
</div>
@include('generales.modalpdf')
@endsection