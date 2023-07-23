@extends("theme.$theme.layout")
@section('titulo')
    Guia Despacho
@endsection

@section('scripts')
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/guiadesp/crear.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Editar Guia Despacho # {{$data->guiadesp->id}}</h3>
                <a class='btn-accion-tabla btn-sm' onclick='genpdfNV({{$data->notaventa_id}},1)' title='Ver Nota venta' data-toggle='tooltip'>
                    Nota Venta: {{$data->notaventa_id}} <i class='fa fa-fw fa-file-pdf-o'></i>
                </a>
                <a class='btn-accion-tabla btn-sm' onclick='verpdf2("{{$data->oc_file}}",2)' title='Orden de Compra' data-toggle='tooltip'>
                    Orden Compra: {{$data->oc_id}} <i class='fa fa-fw fa-file-pdf-o'></i>
                </a>
                <div class="box-tools pull-right">
                    <a href="{{route('guiadesp')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('actualizar_guiadesp', ['id' => $data->guiadesp->id])}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off">
                @csrf @method("put")
                <div class="box-body">
                    @include('guiadesp.form')
                </div>
                <!-- /.box-body -->
                <div class="box-footer text-center">
                    @include('includes.boton-form-editar')
                </div>
                <!-- /.box-footer -->
            </form>
        </div>
    </div>
</div>
@endsection