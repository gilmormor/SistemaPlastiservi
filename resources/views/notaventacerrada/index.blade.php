@extends("theme.$theme.layout")
@section('titulo')
Nota de Venta
@endsection

@section("scripts")
    <script type="text/javascript" src="{{asset("assets/js/jquery-barcode.js")}}"></script>
    <script src="{{asset("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{asset("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Nota de Venta</h3>
                @if ($aux_statusPant == '0')
                    <div class="box-tools pull-right">
                        <!--<a href="{{route('crear_notaventa')}}" class="btn btn-block btn-success btn-sm" id="btnnuevaNV">-->
                        <a href="#" class="btn btn-block btn-success btn-sm" id="btnnuevaNV">
                            <i class="fa fa-fw fa-plus-circle"></i> Crear Nota Venta
                        </a>
                    </div>                        
                @endif
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="tabla-data">
                        <thead>
                            <tr>
                                <th class="width30">ID</th>
                                <th class="width30">Nro Cot</th>
                                <th class="width30">Fecha</th>
                                <th>Cliente</th>
                                <th class='tooltipsC' title='Orden de Compra'>OC</th>
                                <th class="width30"><label for="" title='PDF' data-toggle='tooltip'>PDF</label></th>
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
                                <td >{{$data->cotizacion_id}}</td>
                                <td class="width200">{{date('d-m-Y', strtotime($data->fechahora))}} {{date("h:i:s A", strtotime($data->fechahora))}}</td>
                                <td >{{$data->razonsocial}}</td>
                                <td ><a href="#" onclick='verpdf2("{{$data->oc_file}}",2)'>{{$data->oc_id}}</a></td>
                                <td>
                                    <!--<a href="{{route('exportPdf_notaventa', ['id' => $data->id,'stareport' => '1'])}}" class="btn-accion-tabla tooltipsC" title="Nota de Venta" target="_blank">-->
                                    <a class='btn-accion-tabla btn-sm' onclick='genpdfNV({{$data->id}},{{"1"}})' title='Nota de venta' data-toggle='tooltip'>
                                        <i class="fa fa-fw fa-file-pdf-o"></i>                                    
                                    </a>
                                    <!--<a href="{{route('exportPdf_notaventa', ['id' => $data->id,'stareport' => '2'])}}" class="btn-accion-tabla tooltipsC" title="Precio x Kg" target="_blank">-->
                                    <a class='btn-accion-tabla btn-sm' onclick='genpdfNV({{$data->id}},{{"2"}})' title='Precio x Kg' data-toggle='tooltip'>
                                        <i class="fa fa-fw fa-file-pdf-o"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> 
    <div class="modal fade" id="myModalnumcot" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" id="mdialTamanio">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Número de Cotización</h3>
                </div>
                <div class="modal-body">
                     <div class="row">
                        <div class="form-group col-xs-12 col-sm-4" classorig="form-group col-xs-12 col-sm-4">
                            <label for="cotizacion_idM" class="control-label">Nro. Cotización</label>
                            <div class="input-group">
                                @csrf @method("put")
                                <input type="text" name="cotizacion_idM" id="cotizacion_idM" tipoval='numerico' class="form-control requeridos" required placeholder="Num Cotización"/>
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" id="btnbuscarcotizacion" name="btnbuscarcotizacion">Buscar</button>
                                    <!--
                                    <a id="btnbuscarcotizacion" name="btnbuscarcotizacion" href="#" class="btn btn-flat" data-toggle='tooltip' title="Buscar">
                                        <i class="fa fa-search"></i>
                                    </a>-->
                                    
                                </span>
                            </div>
                            <span class="help-block"></span>
                        </div>
                        <div class="form-group col-xs-12 col-sm-7">
                            <label for="razonsocialM" class="control-label">Razon Social</label>
                            <input type="text" name="razonsocialM" id="razonsocialM" class="form-control" required placeholder="Razon Social" readonly disabled/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="btnaceptar" name="btnaceptar" class="btn btn-primary">Aceptar</button>
                </div>
            </div>
            
        </div>
    </div>
    <div class="modal fade" id="myModalBusquedaCot" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
        
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Cotizaciones</h3>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="aux_numfila" id="aux_numfila" value="0">
                    <table class="table table-striped table-bordered table-hover tablas" id="tabla-data-productos">
                        <thead>
                            <tr>
                                <th class="width30">ID</th>
                                <th>Razon Social</th>
                                <th class="width70">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $aux_nfila = 0; $i = 0;?>
                            @foreach($cotizaciones as $cotizacion)
                                <?php $aux_nfila++; ?>
                                <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}">
                                    <td name="cotizacion_idBtd{{$aux_nfila}}" id="cotizacion_idBtd{{$aux_nfila}}">
                                        <a href="#" class="copiar_id" onclick="copiar_numcot({{$cotizacion->id}})"> {{$cotizacion->id}} </a>
                                    </td>
                                    <td name="razonzocialBtd{{$aux_nfila}}" id="razonzocialBtd{{$aux_nfila}}">
                                        <a href="#" class="copiar_id" onclick="copiar_numcot({{$cotizacion->id}})"> {{$cotizacion->razonsocial}} </a>
                                    </td>
                                    <td name="totalBtd{{$aux_nfila}}" id="totalBtd{{$aux_nfila}}" style="text-align:right">
                                        {{number_format($cotizacion->total, 2, '.', ',')}}
                                    </td>
                                </tr>
                                <?php $i++;?>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
            
        </div>
    </div>
</div>

@include('generales.modalpdf')
@endsection