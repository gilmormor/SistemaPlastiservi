@extends("theme.$theme.layout")
@section('titulo')
Movimientos de Inventario
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/invmov/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Movimientos de Inventario</h3>
            </div>
            <div class="box-body">
                @csrf @method("delete")
                <div class="table-responsive">
                    <table class="table display AllDataTables table-hover table-condensed " id="tabla-data-inventsal">
                        <thead>
                            <tr>
                                <th class="width30">ID</th>
                                <th class="width30">Fecha Creado</th>
                                <th class="width30">Fecha Inv</th>
                                <th title='Descripción'>Descripción</th>
                                <th title='Módulo'>Módulo Origen</th>
                                <th title='Id Documento Origen'>Id Documento</th>
                                <th class="ocultar">Id Modulo</th>
                                <th class="width150">PDF</th>
                            </tr>
                        </thead>
                        <tbody class="todo-list1">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> 
    @include('generales.modalpdf')
    @include('generales.verpdf')
@endsection