@extends("theme.$theme.layout")
@section('titulo')
Acuerdo Tecnico Etapas de Produccion
@endsection

@section("scripts")
    {{-- general.js: trae genpdfAcuTec(), que abre el acuerdo tecnico --}}
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/acuerdotecnicoetapaprod/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Acuerdo Tecnico Etapas de Produccion</h3>
            </div>
            <div class="box-body">
                <table class="table table-striped table-bordered table-hover" id="tabla-data">
                    <thead>
                        <tr>
                            <th class="width70">ID</th>
                            {{-- codigo del producto: al hacer clic abre su acuerdo tecnico --}}
                            <th class="width70">CodProd</th>
                            <th>Nombre</th>
                            <th class="width70">Accion</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- Visor donde se abre el PDF del acuerdo tecnico --}}
@include('generales.modalpdf')
@endsection