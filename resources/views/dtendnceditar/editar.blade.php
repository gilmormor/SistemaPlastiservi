@extends("theme.$theme.layout")
@section('titulo')
    Editar Nota Credito/Debito
@endsection

@section('scripts')
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/dtendnceditar/crear.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Editar Credito/Debito:
                    <a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="" onclick="genpdfFACDin('{{$data->id}}',0)" data-original-title="Ver PDF">
                        {{$data->nrodocto}}
                        {{-- <a onclick="volverSubirDteSisCob({{$data->id}})" class="btn-accion-tabla btn-sm tooltipsC" title="Subir DTE a Sistema Cobranza" data-toggle="tooltip">
                            <span class="fa fa-upload text-yellow"></span>
                        </a> --}}
                    </a>
                </h3>
                <div class="box-tools pull-right">
                    <a href="{{route('dtendnceditar')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('actualizar_dtendnceditar', ['id' => $data->id])}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off">
                @csrf @method("put")
                <div class="box-body">
                    @include('dtendnceditar.form')
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