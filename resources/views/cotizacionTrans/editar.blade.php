@extends("theme.$theme.layout")
@section('titulo')
    Cotización
@endsection

@routes
@section('scripts')
    <script src="{{asset("assets/pages/scripts/cotizacion/crear.js")}}" type="text/javascript"></script>
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
                        <a href="{{route('cotizacion')}}" class="btn btn-block btn-info btn-sm">
                            <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                        </a>
                    </div>
                @endif
            </div>
            <form action="{{route('actualizar_cotizacion', ['id' => $data->id])}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off">
                @csrf @method("put")
                <div class="box-body">
                    @include('cotizacion.form')
                </div>
                <!-- /.box-body -->
                <div class="box-footer text-center">
                    @if (session('aux_aprocot')=='0')
                        @include('includes.boton-form-editar')
                    @else
                        <button type="reset" class="btn btn-default">Cancel</button>
                        <button type="button" id="btnguardaraprob" name="btnguardaraprob" class="btn btn-success">Actualizar</button>
                    @endif
                    <a href="{{route('exportPdf_cotizacion', ['id' => $data->id])}}" class="btn-accion-tabla tooltipsC" title="PDF" target="_blank">
                        <i class="fa fa-fw fa-file-pdf-o"></i>                                    
                    </a>
                </div>
                <!-- /.box-footer -->
            </form>
        </div>
    </div>
</div>
@endsection