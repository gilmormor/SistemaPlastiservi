@extends("theme.$theme.layout")
@section('titulo')
Asignar Guia Despacho
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/despachoordguia/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Asignar Guia Despacho</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id='tabla-data-despachoordguia' name='tabla-data-despachoordguia' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
                    <!--<table class="table table-striped table-bordered table-hover" id="tabla-data">-->
                        <thead>
                            <tr>
                                <th class='tooltipsC' title='Orden despacho'>OD</th>
                                <th class='tooltipsC' title='Fecha Orden despacho'>Fecha OD</th>
                                <th class='tooltipsC' title='Fecha estimada de Despacho'>Fecha ED</th>
                                <th>Razón Social</th>
                                <th class='tooltipsC' title='Orden despacho'>OD</th>
                                <th class='tooltipsC' title='Solicitud Despacho'>SD</th>
                                <th class='tooltipsC' title='Orden de Compra'>OC</th>
                                <th class='tooltipsC' title='Nota de Venta'>NV</th>
                                <th class='tooltipsC' title='Comuna'>Comuna</th>
                                <th  style='text-align:right' class='tooltipsC' title='Total Kg'>Total Kg</th>
                                <th  style='text-align:right' class='tooltipsC' title='Total $'>Total $</th>
                                <th class='tooltipsC' title='Tipo Entrega'>TE</th>
                                <th class="ocultar">Icono</th>
                                <th class="ocultar">clientebloqueado_descripcion</th>
                                <th class="ocultar">oc_file</th>
                                <th class="ocultar">updated_at</th>
                                <th class="width100">Accion</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                            </tr>
                            <tr>
                                <th colspan='9' style='text-align:right'>Total página</th>
                                <th id='subtotalkg' name='subtotalkg' style='text-align:right'>0,00</th>
                                <th id='subtotal' name='subtotal' style='text-align:right'>0</th>
                                <th colspan='2' style='text-align:right'></th>
                            </tr>
                            <tr>
                                <th colspan='9' style='text-align:right'>TOTAL GENERAL</th>
                                <th id='totalkg' name='totalkg' style='text-align:right'>0,00</th>
                                <th id='total' name='total' style='text-align:right'>0</th>
                                <th colspan='2' style='text-align:right'></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('generales.modalpdf')
@include('generales.despachoguia')
@include('generales.despachofactura')
@include('generales.despachoanularguiafact')
@endsection