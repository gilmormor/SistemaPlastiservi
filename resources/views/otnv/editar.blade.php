@extends("theme.$theme.layout")
@section('titulo')
    Orden de Trabajo
@endsection

@section('scripts')
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/otnv/crear.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Editar OT Nro.: {{$ot->id}}</h3>
                <a class='btn-accion-tabla btn-sm' onclick='genpdfNV({{$notaventa->id}},1)' title='Ver Nota venta' data-toggle='tooltip'>
                    Nota Venta: {{$ot->otnotaventa->notaventa_id}} <i class='fa fa-fw fa-file-pdf-o'></i>
                </a>
                <a class='btn-accion-tabla btn-sm' onclick='verpdf2("{{$notaventa->oc_file}}",2)' title='Orden de Compra' data-toggle='tooltip'>
                    Orden Compra: {{$notaventa->oc_id}} <i class='fa fa-fw fa-file-pdf-o'></i>
                </a>
                <div class="box-tools pull-right">
                    <a href="{{route('otnv')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('actualizar_otnv', ['id' => $ot->id])}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off"  enctype="multipart/form-data">
                @csrf @method("put")
                <div class="box-body">
                    @include('otnv.form')
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
@include('generales.editarcamponum')
@endsection