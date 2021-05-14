@extends("theme.$theme.layout")
@section('titulo')
Crear No Conformidad
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/noconformidad/crear.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Crear No Conformidad</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('noconformidad')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('guardar_noconformidad')}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off">
                @csrf
                <div class="box-body">
                    @include('noconformidad.form')
                </div>
                <div class="box-footer text-center">
                    @include('includes.boton-form-crear')
                </div>
            </form>
        </div>
    </div>
</div> 
@endsection