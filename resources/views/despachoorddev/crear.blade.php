@extends("theme.$theme.layout")
@section('titulo')
    Devolución Despacho
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/despachoorddev/crear.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Crear Devolución Despacho</h3>
                <a class='btn-accion-tabla btn-sm' onclick='genpdfNV({{$data->notaventa_id}},1)' title='Ver Nota venta' data-toggle='tooltip'>
                    Nota Venta: {{$data->notaventa_id}} <i class='fa fa-fw fa-file-pdf-o'></i>
                </a>
                <a class='btn-accion-tabla btn-sm' onclick='verpdf2("{{$data->oc_file}}",2)' title='Ver Nota venta' data-toggle='tooltip'>
                    Orden Compra: {{$data->oc_id}} <i class='fa fa-fw fa-file-pdf-o'></i>
                </a>
                <div class="box-tools pull-right">
                    <a href="{{route('consultadespordfact_despachoorddev')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('guardar_despachoorddev')}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off">
                @csrf
                <div class="box-body">
                    @include('despachoorddev.form')
                </div>
                <div class="box-footer text-center">
                    @include('includes.boton-form-crear')
                </div>
            </form>
        </div>
    </div>
</div> 
@endsection