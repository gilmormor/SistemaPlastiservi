@extends("theme.$theme.layout")
@section('titulo')
Orden de despacho
@endsection

@section("scripts")
    <script src="{{asset("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/despachoord/indexguiafact.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Orden de Despacho</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id='tabla-data' name='tabla-data' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
                    <!--<table class="table table-striped table-bordered table-hover" id="tabla-data">-->
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th class='tooltipsC' title='Fecha Orden despacho'>Fecha OD</th>
                                <th class='tooltipsC' title='Fecha estimada de Despacho'>Fecha ED</th>
                                <th>Descripción</th>
                                <th class='tooltipsC' title='Orden Despacho'>OD</th>
                                <th class='tooltipsC' title='Solicitud Despacho'>SD</th>
                                <th class='tooltipsC' title='Orden de Compra'>OC</th>
                                <th class='tooltipsC' title='Nota de Venta'>NV</th>
                                <th class='tooltipsC' title='Total Kg'>Total Kg</th>
                                @if ($aux_vista != "C")
                                    <th class="width100"></th>
                                @else
                                    <th class='tooltipsC' title='Num Guia'>NumGuia</th>
                                    <th class='tooltipsC' title='Fecha Guia'>F Guia</th>
                                    <th class='tooltipsC' title='Num Factura'>NumFact</th>
                                    <th class='tooltipsC' title='Fecha Factura'>F Fact</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            <?php $aux_nfila = 0; ?>
                            @foreach ($datas as $data)
                            <?php
                                $aux_nfila++;
                                $aux_totalkg = 0;
                                foreach($data->despachoorddets as $despachoorddet){
                                    $aux_totalkg += $despachoorddet->cantdesp * ($despachoorddet->notaventadetalle->totalkilos / $despachoorddet->notaventadetalle->cant);
                                }
                            ?>
                            <tr id="fila{{$aux_nfila}}" name="fila{{$aux_nfila}}">
                                <td>{{$data->id}}</td>
                                <td>{{date("d/m/Y", strtotime($data->fechahora))}}</td>
                                <td>{{date("d/m/Y", strtotime($data->fechaestdesp))}}</td>
                                <td>{{$data->notaventa->cliente->razonsocial}}</td>
                                <td>
                                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Despacho' onclick='genpdfOD({{$data->id}},1)'>
                                        <i class='fa fa-fw fa-file-pdf-o'></i>
                                    </a>
                                </td>
                                <td>
                                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Solicitud de Despacho' onclick='genpdfSD({{$data->despachosol_id}},1)'>
                                        <i class='fa fa-fw fa-file-pdf-o'></i> {{$data->despachosol_id}}
                                    </a>
                                </td>
                                <td>
                                    <a class='btn-accion-tabla btn-sm' onclick='verpdf2("{{$data->notaventa->oc_file}}",2)'>
                                        {{$data->notaventa->oc_id}}
                                    </a>
                                </td>
                                <td>
                                    <a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV({{$data->notaventa_id}},1)'>
                                        <i class='fa fa-fw fa-file-pdf-o'></i> {{$data->notaventa_id}}
                                    </a>
                                </td>
                                <td style='text-align:right'>
                                    {{number_format($aux_totalkg, 2, ",", ".")}}
                                </td>
                                @if ($aux_vista != "C")
                                    <td id="accion{{$aux_nfila}}">
                                        @if ($data->despachoordanul)
                                            <small class="label pull-left bg-red">Anulado</small>
                                        @else
                                            @if ($aux_vista == "G")
                                                <a onclick='guiadesp({{$aux_nfila}},{{$data->id}})' class="btn btn-danger btn-xs tooltipsC" title="Guia de despacho">
                                                    Guia
                                                </a>
                                            @endif
                                            @if ($aux_vista == "F")
                                                <a onclick='numfactura({{$aux_nfila}},{{$data->id}})' class='btn btn-warning btn-xs' title='Factura' data-toggle='tooltip'>
                                                    Fact
                                                </a>
                                                |
                                                <a onclick='anularguiafact({{$aux_nfila}},{{$data->id}})' class='btn btn-danger btn-xs' title='Anular Guia y Factura' data-toggle='tooltip'>
                                                    Anular
                                                </a>
                                            @endif
                                        @endif
                                    </td>
                                @endif
                                @if ($aux_vista == "C")
                                    <td>{{$data->guiadespacho}}</td>
                                    <td>{{date("d/m/Y", strtotime($data->fechahora))}}</td>
                                    <td>{{$data->numfactura}}</td>
                                    <td>{{date("d/m/Y", strtotime($data->fechaestdesp))}}</td>        
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
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