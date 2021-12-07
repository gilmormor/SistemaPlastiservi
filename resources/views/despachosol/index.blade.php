@extends("theme.$theme.layout")
@section('titulo')
Solicitud de despacho
@endsection
@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/despachosol/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Solicitud de Despacho por aprobar</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('listarnv_despachosol')}}" class="btn btn-block btn-success btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i> Nueva Solicitud Despacho
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <!--<table id='tabla-data-cotizacion' name='tabla-data-cotizacion' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>-->
                    <table class="table display AllDataTables table-condensed table-hover" id="tabla-data-cotizacion">

                    <!--<table class="table table-striped table-bordered table-hover" id="tabla-data">-->
                        <thead>
                            <tr>
                                <th class="width70 tooltipsC" title='Solicitud de Despacho'>SD</th>
                                <th>Razón Social</th>
                                <th class='tooltipsC' title='Orden de Compra'>OC</th>
                                <th class='tooltipsC' title='Nota de Venta'>NV</th>
                                <th class='tooltipsC' title='Precio x Kg'>$ x Kg</th>
                                <th class='tooltipsC' title='Comuna'>Comuna</th>
                                <th class='tooltipsC' title='Total Kg' style="text-align:right">Total Kg</th>
                                <th class='tooltipsC' title='Tipo Entrega'>TE</th>
                                <th class="ocultar">Icono</th>
                                <th class="ocultar">Obs Bloqueo</th>
                                <th class="width70"></th>
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