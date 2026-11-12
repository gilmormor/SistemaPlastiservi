@extends("theme.$theme.layout")
@section('titulo')
    Registro Produccion
@endsection

@section("scripts")
    <script>
        // Datos de la etapa para conversión kg ↔ cant y validaciones JS
        // Unidades de ESTE producto en esta etapa (acuerdotecnicoapsucetapaprod)
        const ETAPA_UM_SALIDA_ID       = {{ $tablas['um']['sal_id'] ?? 'null' }};
        const ETAPA_UM_SALIDA_NOMBRE   = "{{ $tablas['um']['sal_nombre'] ?? '' }}";
        const ETAPA_UM_ENTRADA_NOMBRE  = "{{ $tablas['um']['ent_nombre'] ?? '' }}";
        const ETAPA_REQUIERE_KG        = {{ (int)($opdet->areaproduccionsucetapaprod->requiere_kg ?? 1) }};
        const ETAPA_REQUIERE_CC        = {{ (int)($opdet->areaproduccionsucetapaprod->requiere_cc ?? 0) }};
        const ETAPA_USA_MATPRIMA       = {{ (int)($opdet->areaproduccionsucetapaprod->usa_matprima ?? 0) }};
        // peso_unitario (kg por unidad) del producto. 0 = no hay conversión automática.
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
                <h3 class="box-title">Crear Registro Produccion - {{$opdet->areaproduccionsucetapaprod->etapaprod->nombre}}</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('opdetregprodtemp_listaropdet')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('guardar_opdetregprodtemp')}}" id="form-general" class="form-horizontal" method="POST" autocomplete="off">
                @csrf
                <div class="box-body">
                    @include('opdetregprodtemp.form')
                </div>
                <div class="box-footer text-center">
                    @include('includes.boton-form-crear')
                </div>
            </form>
        </div>
    </div>
</div> 
@include('generales.modalpdf')
{{-- Modal de etiqueta de lote: lo abre el numero de lote del panel de origen --}}
@include('generales.modaletiquetaetapa')
@endsection