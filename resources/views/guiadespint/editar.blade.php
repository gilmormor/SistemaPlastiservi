@extends("theme.$theme.layout")
@section('titulo')
    Cotización
@endsection

@section('scripts')
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/guiadespint/crear.js")}}" type="text/javascript"></script>
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
                        <a href="{{route('guiadespint')}}" class="btn btn-block btn-info btn-sm">
                            <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                        </a>
                    </div>
                @endif
            </div>
            <form action="{{route('actualizar_guiadespint', ['id' => $data->id])}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off">
                @csrf @method("put")
                <div class="box-body">
                    @include('guiadespint.form')
                </div>
                <div class="box-footer text-center">
                    @if (session('aux_aprocot')=='0')
                        @if (($data->vendedor_id == $tablas['vendedor_id']) or ($data->usuario_id == auth()->id())) <!-- Solo deja modificar si el el mismo vendedor o si fue el usuario que creo el registro -->
                            @include('includes.boton-form-editar')
                        @endif
                    @else
                        <button type="reset" class="btn btn-default">Cancel</button>
                        <button type="button" id="btnguardaraprob" name="btnguardaraprob" class="btn btn-success">Actualizar</button>
                    @endif
                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Cotizacion: {{$data->id}}' onclick='genpdfCOT({{$data->id}},1)'>
                        <i class='fa fa-fw fa-file-pdf-o'></i>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@include('generales.modalpdf')
@endsection