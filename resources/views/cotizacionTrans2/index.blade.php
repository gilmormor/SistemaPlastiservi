@extends("theme.$theme.layout")
@section('titulo')
Cotización
@endsection

@section("scripts")
    <script type="text/javascript" src="{{asset("assets/js/jquery-barcode.js")}}"></script>
    <script src="{{asset("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/cotizacion/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Cotización</h3>
                <!--@if (session('aux_aprocot') == '0')-->
                    <div class="box-tools pull-right">
                        <a href="{{route('crear_cotizacion')}}" class="btn btn-block btn-success btn-sm">
                            <i class="fa fa-fw fa-plus-circle"></i> Nuevo registro
                        </a>
                    </div>                        
                <!--@endif-->
            </div>
            <div class="box-body">
                <table class="table table-striped table-bordered table-hover" id="tabla-data">
                    <thead>
                        <tr>
                            <th class="width70">ID</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            @if (session('aux_aprocot')=='0')
                                <th class="width30"><label for="" title='Enviar a Nota de venta' data-toggle='tooltip'> NV</label></th>
                            @endif
                            <th class="width70"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $aux_nfila = 0; ?>
                        @foreach ($datas as $data)
                        <?php 
                            $aux_nfila++; 
                            $colorFila = "";
                            $aprobstatus = 1;
                            $aux_mensaje = "";
                            $aux_data_toggle = "";
                            $aux_title = "";
                            if($data->contador>0){
                                $colorFila = 'background-color: #87CEEB;';
                                $aprobstatus = 2;
                                $aux_data_toggle = "tooltip";
                                $aux_title = "Precio menor al valor en tabla";
                            }
                            if($data->aprobstatus==4){
                                $colorFila = 'background-color: #FFC6C6;';  //" style=background-color: #FFC6C6;  title=Rechazo por: $data->aprobobs data-toggle=tooltip"; //'background-color: #FFC6C6;'; 
                                $aux_data_toggle = "tooltip";
                                $aux_title = "Rechazado por: " . $data->aprobobs;
                            }
                        ?>
                        <tr id="fila{{$aux_nfila}}" name="fila{{$aux_nfila}}" style="{{$colorFila}}" title="{{$aux_title}}" data-toggle="{{$aux_data_toggle}}">
                            <td>{{$data->id}}</td>
                            <!--<td class="width200">{{$data->fechahora}}</td>-->
                            <td class="width200">{{date('d-m-Y', strtotime($data->fechahora))}} {{date("h:i:s A", strtotime($data->fechahora))}}</td>
                            <td >{{$data->razonsocial}}</td>
                            @if (session('aux_aprocot')=='0')
                                <td>
                                    @csrf @method("put")
                                    <a id='btnOrdenTrabajo$i' name='btnOrdenTrabajo$i' class='btn-accion-tabla btn-sm' onclick='aprobarcotvend({{$aux_nfila}},{{$data->id}},{{$aprobstatus}})' title='Enviar a Nota de venta' data-toggle='tooltip'>
                                        <span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span></a>
                                </td>
                            @endif
                            <td>
                                <a href="{{route('exportPdf_cotizacion', ['id' => $data->id])}}" class="btn-accion-tabla tooltipsC" title="PDF" target="_blank">
                                    <i class="fa fa-fw fa-file-pdf-o"></i>                                    
                                </a>
                                <a href="{{route('editar_cotizacion', ['id' => $data->id])}}" class="btn-accion-tabla tooltipsC" title="Editar este registro">
                                    <i class="fa fa-fw fa-pencil"></i>                                    
                                </a>
                                @if (session('aux_aprocot')=='0')
                                    <form action="{{route('eliminar_cotizacion', ['id' => $data->id])}}" class="d-inline form-eliminar" method="POST">
                                        @csrf @method("delete")
                                        <button type="submit" class="btn-accion-tabla eliminar tooltipsC" title="Eliminar este registro">
                                            <i class="fa fa-fw fa-trash text-danger"></i>
                                        </button>
                                    </form>
                                @endif    
                                    
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection