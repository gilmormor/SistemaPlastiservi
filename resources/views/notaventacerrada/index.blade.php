@extends("theme.$theme.layout")
@section('titulo')
Nota de Venta
@endsection

@section("scripts")
    <script src="{{asset("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/notaventacerrada/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Nota de Venta</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <!--<table class="table table-striped table-bordered table-hover" id="tabla-data">-->
                    <table class="table display AllDataTables table-condensed table-hover" id="tabla-data">
                        <thead>
                            <tr>
                                <th class="width30">ID</th>
                                <th class="width30">Nro Cot</th>
                                <th class="width200">Fecha</th>
                                <th>Cliente</th>
                                <th class="ocultar">contador</th>
                                <th class="ocultar">aprobstatus</th>
                                <th class="ocultar">aprobobs</th>
                                <th class="ocultar">oc_file</th>
                                <th class='tooltipsC width50' title='Orden de Compra'>OC</th>
                                <th class="width50"><label for="" title='PDF' data-toggle='tooltip'>PDF</label></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> 
</div>
@include('generales.modalpdf')
@endsection