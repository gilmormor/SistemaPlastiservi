@extends("theme.$theme.layout")
@section('titulo')
    Rechazo Orden de Despacho
@endsection

@section("styles")
    <link rel="stylesheet" href="{{asset("assets/js/bootstrap-fileinput/css/fileinput.min.css")}}">
@endsection

@section("scriptsPlugins")
    <script src="{{asset("assets/js/bootstrap-fileinput/js/fileinput.min.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/js/bootstrap-fileinput/js/locales/es.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/js/bootstrap-fileinput/themes/fas/theme.min.js")}}" type="text/javascript"></script>
@endsection

@section('scripts')
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/despachoordrec/crear.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Editar Rechazo Orden Despacho Nro.: {{$despachoordrec->id}}</h3>
                <a class='btn-accion-tabla btn-sm' onclick='genpdfNV({{$data->notaventa_id}},1)' title='Ver Nota venta' data-toggle='tooltip'>
                    Nota Venta: {{$data->notaventa_id}} <i class='fa fa-fw fa-file-pdf-o'></i>
                </a>
                <a class='btn-accion-tabla btn-sm' onclick='verpdf2("{{$data->notaventa->oc_file}}",2)' title='Orden de Compra' data-toggle='tooltip'>
                    Orden Compra: {{$data->notaventa->oc_id}} <i class='fa fa-fw fa-file-pdf-o'></i>
                </a>
                <div class="box-tools pull-right">
                    <a href="{{route('despachoordrec')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('actualizar_despachoordrec', ['id' => $data->id])}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off" enctype="multipart/form-data">
                @csrf @method("put")
                <div class="box-body">
                    <input type="hidden" name="despachoordrec_id" id="despachoordrec_id" value="{{$despachoordrec->id}}">
                    <input type="hidden" name="recupdated_at" id="recupdated_at" value="{{old('recupdated_at', $despachoordrec->updated_at ?? '')}}">
                    @include('despachoordrec.form')
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