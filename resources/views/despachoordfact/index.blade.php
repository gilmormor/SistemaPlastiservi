@extends("theme.$theme.layout")
@section('titulo')
Asignar Número de Factura
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/despachoordfact/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Asignar Número de Factura</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 col-md-9 col-sm-12">
                        <div class="col-xs-12 col-md-6 col-sm-6">
                            <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                <label for="sucursal_id" data-toggle='tooltip' title="Sucursal">Sucursal</label>
                            </div>
                            <div class="col-xs-12 col-md-8 col-sm-8">
                                <?php
                                    $sucursal_id = 0;
                                    if(count($tablashtml['sucursales']) == 1){
                                        $sucursal_id = $tablashtml['sucursales'][0]->id;
                                    }
                                ?>
                                <select name="sucursal_id" id="sucursal_id" class="selectpicker form-control" required>
                                    <option value="x">Seleccione...</option>
                                    @foreach($tablashtml['sucursales'] as $sucursal)
                                        <option
                                            value="{{$sucursal->id}}"
                                            @if ( $sucursal->id == $sucursal_id )
                                                {{'selected'}}
                                            @endif
                                        >{{$sucursal->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3 col-sm-12">
                        <div class="col-xs-12 col-md-12 col-sm-12 text-center">
                            <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success tooltipsC" title="Consultar">Consultar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div>
                    <legend></legend>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id='tabla-data-despachoordfact' name='tabla-data-despachoordfact' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
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
                                <th class='tooltipsC' title='Guia Despacho'>GuiaDesp</th>
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