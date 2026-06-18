@extends("theme.$theme.layout")
@section('titulo')
    Parámetros CC
@endsection

@section('scripts')
    <script src="{{autoVer("assets/pages/scripts/ccparam/crear.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Editar Parámetro CC</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('ccparam')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('actualizar_ccparam', ['id' => $data->id])}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off">
                @csrf @method('put')
                <div class="box-body">
                    @include('ccparam.form')
                </div>
                <div class="box-footer text-center">
                    @include('includes.boton-form-editar')
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
