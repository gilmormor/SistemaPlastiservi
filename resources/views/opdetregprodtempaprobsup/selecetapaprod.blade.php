@extends("theme.$theme.layout")
@section('titulo')
Etapa Produccion
@endsection

@section("scripts")
    <script>
        const RUTA_AJAX = "{{ url('opdetregprodtempaprobsup/selecetapaprodpage') }}";
    </script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/opdetregprodtempaprobsup/selecetapaprod.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Seleccionar Etapa Produccion</h3>
            </div>
            <div class="box-body">
                <table class="table table-striped table-bordered table-hover" id="tabla-data">
                    <thead>
                        <tr>
                            <th class="width70" title="Id Etata Produccion">ID</th>
                            <th class="width70">Etapa Produccion</th>
                            <th class="width70">Sucursal</th>
                            <th class="width70">Accion</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection