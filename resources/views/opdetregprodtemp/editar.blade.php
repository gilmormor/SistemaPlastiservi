@extends("theme.$theme.layout")
@section('titulo')
    Registro Produccion
@endsection

@section('scripts')
    <script>
        // Mismas constantes que en crear: la pantalla de editar no las tenia y
        // el JS caia a los valores por defecto.
        const ETAPA_UM_SALIDA_ID       = {{ $tablas['um']['sal_id'] ?? 'null' }};
        const ETAPA_UM_SALIDA_NOMBRE   = "{{ $tablas['um']['sal_nombre'] ?? '' }}";
        const ETAPA_UM_ENTRADA_NOMBRE  = "{{ $tablas['um']['ent_nombre'] ?? '' }}";
        const PRODUCTO_PESO_UNITARIO   = {{ $opdet->op->otdet->producto->peso_unitario ?? 0 }};
    </script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/opdetregprodtemp/crear.js")}}" type="text/javascript"></script>
    <script>
        const LOTEORIGEN_URL_BASE = "{{ url('opdetregprodtemp/lotesorigen') }}";
    </script>
    <script src="{{autoVer("assets/pages/scripts/opdetregprodtemp/loteorigen.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Editar Registro Produccion</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('opdetregprodtemp')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('actualizar_opdetregprodtemp', ['id' => $data->id])}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off">
                @csrf @method("put")
                <div class="box-body">
                    @include('opdetregprodtemp.form')
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
{{-- Modal de etiqueta de lote: lo abre el numero de lote del panel de origen.
     modalpdf aporta el hidden #venmodant que usa la funcion compartida. --}}
@include('generales.modalpdf')
@include('generales.modaletiquetaetapa')
@endsection