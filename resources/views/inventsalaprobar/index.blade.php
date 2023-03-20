@extends("theme.$theme.layout")
@section('titulo')
Entradas y Salidas Inventario
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/inventsalaprobar/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Entradas y Salidas Inventario</h3>
            </div>
            <div class="box-body">
                @csrf @method("delete")
                <div class="table-responsive">
                    <table class="table display AllDataTables table-hover table-condensed " id="tabla-data-inventsal">
                        <thead>
                            <tr>
                                <th class="width30">ID</th>
                                <th class="width30">Fecha</th>
                                <th title='Descripción'>Descripción</th>
                                <th class="width150">PDF</th>
                                <th class="width150">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="todo-list1">
                        </tbody>
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