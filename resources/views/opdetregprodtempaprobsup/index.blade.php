@extends("theme.$theme.layout")
@section('titulo')
Aprobar Registro de Produccion
@endsection

@section("scripts")
    <script>
        const RUTA_AJAX = "{{ url('opdetregprodtempaprobsuppage') }}";
    </script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/opdetregprodtempaprobsup/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
    <div class="row">
        <div class="col-lg-12">
            @include('includes.mensaje')
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Aprobar Registro de Produccion: {{$tablas["etapaprod"]->nombre}}</h3>
                </div>
                <div class="box-body">
                    <table class="table table-striped table-bordered table-hover" id="tabla-data">
                        <thead>
                            <tr>
                                <th class="width70">ID</th>
                                <th class="width70">OT-OP-OPDet</th>
                                <th class="width70">CodProd</th>
                                <th class="width70">Producto</th>
                                <th class="width70">Razon Social</th>
                                <th class="width70">Total kg</th>
                                <th class="width70">Kg Recibidos</th>
                                <th class="width70">Kg Procesados</th>
                                <th class="width70">Kg Scrap</th>
                                <th class="width70">Kg</th>
                                <th class="width70">Kg Scrap</th>
                                <th class="width70">Kg Saldo</th>
                                <th class="width70"></th>
                                <th class="width70">Accion</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php 
        $disabledReadOnly = "disabled";
    ?>
    @include('generales.modalpdf')
    @include('generales.verpdf')
    @include('generales.aprobarcotnv')
@endsection