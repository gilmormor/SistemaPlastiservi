@extends("theme.$theme.layout")
@section('titulo')
Devolución Orden de Despacho
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/despachoorddev/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Devolución Orden de Despacho</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('consultadespordfact_despachoorddev')}}" class="btn btn-block btn-success btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i> Crear Devolución
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <!--<table class="table table-striped table-bordered table-hover" id="tabla-data-cotizacion">-->
                    <table class="table display AllDataTables table-condensed table-hover" id="tabla-data">
                        <thead>
                            <tr>
                                <th class="width70">ID</th>
                                <th class="width100">Fecha</th>
                                <th class="width70">Nota Venta</th>
                                <th class="width70">SolDesp</th>
                                <th class="width70">OrdDesp</th>
                                <th>Razon Social</th>
                                <th class="ocultar">fechaaaaammdd</th>
                                <th class="width70">Acción</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('generales.modalpdf')
@endsection