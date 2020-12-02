@extends("theme.$theme.layout")
@section('titulo')
Solicitud de despacho
@endsection
<?php
    use App\Models\ClienteBloqueado;
?>
@section("scripts")
    <script src="{{asset("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/despachosol/index.js")}}" type="text/javascript"></script>
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
                    <table id='tabla-data' name='tabla-data' class='table display AllDataTables table-hover table-condensed tablascons' data-page-length='50'>
                    <!--<table class="table table-striped table-bordered table-hover" id="tabla-data">-->
                        <thead>
                            <tr>
                                <th class="width70">ID</th>
                                <th>Razón Social</th>
                                <th class='tooltipsC' title='Solicitud de Despacho'>SD</th>
                                <th class='tooltipsC' title='Orden de Compra'>OC</th>
                                <th class='tooltipsC' title='Nota de Venta'>NV</th>
                                <th class='tooltipsC' title='Precio x Kg'>$ x Kg</th>
                                <th class='tooltipsC' title='Comuna'>Comuna</th>
                                <th class='tooltipsC' title='Total Kg'>Total Kg</th>
                                <th class='tooltipsC' title='Tipo Entrega'>TE</th>
                                <th class="width70"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $aux_nfila = 0; ?>
                            @foreach ($datas as $data)
                                @if (count($data->despachoords) == 0)
                                <?php
                                    
                                    $aux_nfila++; 
                                    $aux_totalkg = 0;
                                    foreach($data->despachosoldets as $despachosoldet){
                                        $aux_totalkg += $despachosoldet->cantsoldesp * ($despachosoldet->notaventadetalle->totalkilos / $despachosoldet->notaventadetalle->cant);
                                    }
                                ?>
                                <tr id="fila{{$aux_nfila}}" name="fila{{$aux_nfila}}">
                                    <td>{{$data->id}}</td>
                                    <td>{{$data->notaventa->cliente->razonsocial}}</td>
                                    <td>
                                        <a class='btn-accion-tabla btn-sm tooltipsC' title='Solicitud de Despacho' onclick='genpdfSD({{$data->id}},1)'>
                                            <i class='fa fa-fw fa-file-pdf-o'></i>
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
                                    <td>
                                        <a class='btn-accion-tabla btn-sm tooltipsC' title='Precio x Kg' onclick='genpdfNV({{$data->notaventa_id}},2)'>
                                            <i class='fa fa-fw fa-file-pdf-o'></i>
                                        </a>
                                    </td>
                                    <td>{{$data->comunaentrega->nombre}}</td>
                                    <td style='text-align:right'>
                                        {{number_format($aux_totalkg, 2, ",", ".")}}
                                    </td>
                                    <td>
                                        <i class='fa fa-fw {{$data->tipoentrega->icono}} tooltipsC' title='{{$data->tipoentrega->nombre}}'></i>
                                    </td>    
                                    <td id="accion{{$aux_nfila}}">
                                        @if ($data->despachosolanul)
                                            <small class="label pull-left bg-red">Anulado</small>
                                        @else
                                            <?php 
                                                $clibloq = ClienteBloqueado::where("cliente_id" , "=" ,$data->notaventa->cliente_id)->get();
                                            ?>
                                            @if(count($clibloq) > 0)
                                                <a class='btn-accion-tabla btn-sm' title='Cliente Bloqueado: {{$clibloq[0]->descripcion}}' data-toggle='tooltip'>
                                                    <span class='glyphicon glyphicon-remove-circle text-danger' style='bottom: 0px;top: 2px;'></span>
                                                </a>
                                            @else
                                                <a class='btn-accion-tabla btn-sm' onclick='aprobarsol({{$aux_nfila}},{{$data->id}})' title='Aprobar Solicitud Despacho' data-toggle='tooltip'>
                                                    <span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>
                                                </a>
                                                <a href="{{route('editar_despachosol', ['id' => $data->id])}}" class="btn-accion-tabla tooltipsC" title="Editar este registro">
                                                    <i class="fa fa-fw fa-pencil"></i>
                                                </a>
                                            @endif
                                            <a id='btnanularnv$i' name='btnanularnv$i' class='btn-accion-tabla btn-sm' onclick='anular({{$aux_nfila}},{{$data->id}})' title='Anular Solicitud Despacho' data-toggle='tooltip'>
                                                <span class='glyphicon glyphicon-remove text-danger'></span>
                                            </a>
                                            <!--
                                            <form action="{{route('eliminar_despachosol', ['id' => $data->id])}}" class="d-inline form-eliminar" method="POST">
                                                @csrf @method("delete")
                                                <button type="submit" class="btn-accion-tabla eliminar tooltipsC" title="Eliminar este registro">
                                                    <i class="fa fa-fw fa-trash text-danger"></i>
                                                </button>
                                            </form>
                                            -->
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('generales.modalpdf')
@endsection