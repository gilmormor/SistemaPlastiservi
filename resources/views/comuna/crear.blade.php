@extends("theme.$theme.layout")
@section('titulo')
    Comuna
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/comuna/crear.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Crear Comuna</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('comuna')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('guardar_comuna')}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off">
                @csrf
                <div class="box-body">
                    @include('comuna.form')
                </div>
                <div class="box-footer text-center">
                    @include('includes.boton-form-crear')
                </div>
            </form>
        </div>
    </div>
</div> 
@endsection