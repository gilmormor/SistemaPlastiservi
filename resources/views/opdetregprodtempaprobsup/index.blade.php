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
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="tabla-data">
                            <thead>
                                <tr>
                                    <th class="width70" title="Id Registro produccion temporal">ID</th>
                                    <th class="width70" title="Trazabilidad">Trazabilidad</th>
                                    <th class="width70" title="Codigo de Producto">CodProd</th>
                                    <th class="width70" title="Producto">Producto</th>
                                    <th class="width70" title="Razon Social">Razon Social</th>
                                    <th class="width70" title="Kilos recibidos">KgRec</th>
                                    <th class="width70" title="Kilos procesados">KgProc</th>
                                    <th class="width70" title="Kilos de desperdicio">Kg Scrap</th>
                                    <th class="width70" title="Kilos">Kg</th>
                                    <th class="width70" title="Kilos de desperdicio">Kg Scrap</th>
                                    <th class="width70" title="Kilos de saldo">Kg Saldo</th>
                                    <th class="width70" title="Acciones"></th>
                                    <th class="width70" title="Maquina">Maquina</th>
                                    <th class="width70" title="Operario">Operario</th>
                                    <th class="width70" title="Estado del cierre de la unidad de medida de salida de la etapa (rollo, bolsa, pieza, etc.)">Cierre UM Sal.</th>
                                    <th class="width70">Accion</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
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
    @include('generales.modaletiquetaetapa')
@endsection