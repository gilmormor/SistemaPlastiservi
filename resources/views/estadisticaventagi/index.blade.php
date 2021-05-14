@extends("theme.$theme.layout")
@section('titulo')
Guia Interna
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/estadisticaventagi/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Guia Interna</h3>
                @if(can('guardar-guia-interna',false) == true)
                    <div class="box-tools pull-right">
                        <a href="{{route('crear_estadisticaventagi')}}" class="btn btn-block btn-success btn-sm">
                            <i class="fa fa-fw fa-plus-circle"></i> Crear Guia Interna
                        </a>
                    </div>
                @endif
            </div>
            <div class="box-body">
                <table class="table table-striped table-bordered table-hover" id="tabla-data">
                    <thead>
                        <tr>
                            <th class="width30">ID</th>
                            <th>Fecha</th>
                            <th class="width30">Docum</th>
                            <th>Nombre</th>
                            <th>Descripcion</th>
                            <th>Medidas</th>
                            <th>Materia Prima</th>
                            <th>Unidades</th>
                            <th>Kilos</th>
                            <th>Valor<br>Unitario</th>
                            <th>Total</th>
                            <th class="width70">Acción</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection