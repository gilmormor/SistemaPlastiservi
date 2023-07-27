@extends("theme.$theme.layout")
@section('titulo')
    Cliente
@endsection

@section('scripts')
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/clienteproducto/crear.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/producto/buscar.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Editar Cliente</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('clienteproducto')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('actualizar_clienteproducto', ['id' => $data->id])}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off">
                @csrf @method("put")
                <div class="box-body">
                    @include('clienteproducto.form')
                </div>
                @if(can('guardar-cliente-producto',false) == true)
                    <!-- /.box-body -->
                    <div class="box-footer text-center">
                        @include('includes.boton-form-editar')
                    </div>
                    <!-- /.box-footer -->
                @endif
            </form>
        </div>
    </div>
</div>
@include('generales.modalpdf')
@endsection