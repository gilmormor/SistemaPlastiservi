<input type="hidden" name="id" id="id" value="{{$data->id}}">
<input type="hidden" name="despachosol_id" id="despachosol_id" value="{{$data->despachosol_id}}">
<input type="hidden" name="notaventa_id" id="notaventa_id" value="{{$data->notaventa_id}}">
<input type="hidden" name="aux_sta" id="aux_sta" value="{{$aux_sta}}">
<input type="hidden" name="aux_fechaphp" id="aux_fechaphp" value="{{old('aux_fechaphp', $fecha ?? '')}}">
<input type="hidden" name="aux_iva" id="aux_iva" value="{{$empresa->iva}}">
<input type="hidden" name="direccioncot" id="direccioncot" value="{{old('direccioncot', $data->direccioncot ?? '')}}">
<input type="hidden" name="cliente_id" id="cliente_id" value="{{old('cliente_id', $data->cliente_id ?? '')}}">
<input type="hidden" name="comuna_id" id="comuna_id" value="{{old('comuna_id', $data->comuna_id ?? '')}}">
<input type="hidden" name="formapago_id" id="formapago_id" value="{{old('formapago_id', $data->formapago_id ?? '')}}">
<input type="hidden" name="plazopago_id" id="plazopago_id" value="{{old('plazopago_id', $data->plazopago_id ?? '')}}">
<input type="hidden" name="giro_id" id="giro_id" value="{{old('giro_id', $data->giro_id ?? '')}}">
<input type="hidden" name="sucursal_id" id="sucursal_id" value="{{old('sucursal_id', $sucurArray[0] ?? '')}}">


@if($aux_sta==1)
    <input type="hidden" name="vendedor_id" id="vendedor_id" value="{{old('vendedor_id', $vendedor_id ?? '')}}">
@else
    <input type="hidden" name="vendedor_id" id="vendedor_id" value="{{old('vendedor_id', $data->vendedor_id ?? '')}}">
@endif
<input type="hidden" name="region_id" id="region_id" value="{{old('region_id', $data->region_id ?? '')}}">
<input type="hidden" name="provincia_id" id="provincia_id" value="{{old('provincia_id', $data->provincia_id ?? '')}}">
<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">

<input type="hidden" name="neto" id="neto" value="{{old('neto', $data->neto ?? '')}}">
<input type="hidden" name="iva" id="iva" value="{{old('iva', $data->iva ?? '')}}">
<input type="hidden" name="total" id="total" value="{{old('total', $data->total ?? '')}}">
<input type="hidden" name="imagen" id="imagen" value="{{old('imagen', $data->oc_file ?? '')}}">

<?php
    $disabledReadOnly = "";
    $disabledcliente = "";
    $enableCamposCot = ""; //Este campo lo cambio a disbles si llegara a necesitar desactivar los campos marcados con esta variable
    //Si la pantalla es de aprobacion de Cotizacion desactiva todos input
    //$aux_statusPant=='0', Pantalla normal CRUD de Cotizacion
    //$aux_statusPant=='1', Aprobar o rechazar cotización. Y colocar una observacion
    if($aux_sta==3){
        $disabledReadOnly = ' disabled ';
    }
    $disabledReadOnly = " readonly";
    $aux_concot = false;
    if ($aux_sta==2 and $data->cotizacion_id and $data->id){
        $disabledcliente = ' disabled ';
        $aux_concot = true;
    }
    $disabledcliente = " disabled";

?>
<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
            <div class="box-header with-border">
                <div class="box-body">
                    @include('despachoord.datosform')
                </div>
            </div>
        </div>
    </div>    
    <div style="display:none;" class="col-xs-12 col-sm-3">
        <div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
            <div class="box-header with-border">
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-12">
                            <label id="lboc_id" name="lboc_id" for="oc_id" class="control-label">Nro OrdenCompra</label>
                            <div class="input-group">
                                <input type="text" name="oc_id" id="oc_id" class="form-control" value="{{old('oc_id', $data->oc_id ?? '')}}" placeholder="Nro Orden de Compra" {{$enableCamposCot}}/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-12">
                            <label id="lboc_id" name="lboc_id" for="oc_file" class="control-label">Adjuntar OC</label>
                            <div class="input-group">
                                <input type="file" name="oc_file" id="oc_file" class="form-control" data-initial-preview='{{isset($data->oc_file) ? Storage::url("imagenes/notaventa/$data->oc_file") : ""}}' accept="image/*"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
    <div class="box-header with-border">
        <h3 class="box-title">Detalle</h3>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="tabla-data" style="font-size:14px">
                    <thead>
                        <tr>
                            <th class="width30">ID</th>
                            <th style="display:none;">NotaVentaDetalle_ID</th>
                            <th style="display:none;">cotizacion_ID</th>
                            <th style="display:none;">Codigo Producto</th>
                            <th style="display:none;">CódInterno</th>
                            <th style="display:none;">Cant</th>
                            <th>Nombre</th>
                            <th>Cant</th>
                            <th>Desp</th>
                            <!--<th>SolDesp</th>-->
                            <th>Saldo</th>
                            <th class='tooltipsC' title='Marcar todo'>
                                <div class='checkbox'>
                                    <label style='font-size: 1.2em'>
                                        <input type='checkbox' id='marcarTodo' name='marcarTodo' checked readonly disabled>
                                        <span class='cr'><i class='cr-icon fa fa-check'></i></span>
                                    </label>
                                </div>
                            </th>
                            <th class="width70">SolicitudDesp</th>
                            <th style="display:none;">UnidadMedida</th>
                            <th>Clase</th>
                            <th>Diam</th>
                            <th style="display:none;">Diametro</th>
                            <th>Esp</th>
                            <th style="display:none;">Espesor</th>
                            <th>Largo</th>
                            <th style="display:none;">Largo</th>
                            <th>Peso</th>
                            <th style="display:none;">Peso</th>
                            <th>TU</th>
                            <th style="display:none;">TUnion</th>
                            <th>Desc</th>
                            <th style="display:none;">DescPorc</th>
                            <th style="display:none;">DescVal</th>
                            <th>PUnit</th>
                            <th style="display:none;">Precio Neto Unit</th>
                            <th>V Kilo</th>
                            <th style="display:none;">Precio X Kilo</th>
                            <th style="display:none;">Precio X Kilo Real</th>
                            <th>Kilos</th>
                            <th style="display:none;">Total Kilos</th>
                            <th>Sub Total</th>
                            <th style="display:none;">Sub Total Neto</th>
                            <th style="display:none;">Sub Total Neto Sin Formato</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($aux_sta==2 or $aux_sta==3)
                            <?php 
                                $aux_nfila = 0; $i = 0;
                                $cantordTotal = 0;
                            ?>
                            @foreach($detalles as $detalle)
                                <?php 
                                    $aux_nfila++;
                                    $cantordTotal = $cantordTotal + $detalle->cantdesp;
                                ?>
                                <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}">
                                    <td name="NVdet_idTD{{$aux_nfila}}" id="NVdet_idTD{{$aux_nfila}}">
                                        @if ($aux_sta==2)
                                            {{$detalle->id}}
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td style="display:none;">
                                        @if ($aux_sta==2)
                                            <input type="text" name="NVdet_id[]" id="NVdet_id{{$aux_nfila}}" class="form-control" value="{{$detalle->id}}" style="display:none;"/>
                                        @else
                                            <input type="text" name="NVdet_id[]" id="NVdet_id{{$aux_nfila}}" class="form-control" value="0" style="display:none;"/>
                                        @endif
                                    </td>
                                    <td style="display:none;">
                                        @if ($aux_sta==2)
                                            <input type="text" name="cotizaciondetalle_id[]" id="cotizaciondetalle_id{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->cotizaciondetalle_id}}" style="display:none;"/>
                                        @else
                                            <input type="text" name="cotizaciondetalle_id[]" id="cotizaciondetalle_id{{$aux_nfila}}" class="form-control" value="{{$detalle->id}}" style="display:none;"/>
                                        @endif
                                    </td>
                                    <td name="producto_idTD{{$aux_nfila}}" id="producto_idTD{{$aux_nfila}}" style="display:none;">
                                        <input type="text" name="producto_id[]" id="producto_id{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto_id}}" style="display:none;"/>
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="codintprod[]" id="codintprod{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->codintprod}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        @if ($aux_sta==2)
                                            <input type="text" name="cant[]" id="cant{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->cant}}" style="display:none;"/>
                                        @else 
                                            <input type="text" name="cant[]" id="cant{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->cant - $detalle->notaventadetalle->cantusada}}" style="display:none;"/>
                                        @endif
                                    </td>
                                    <td name="nombreProdTD{{$aux_nfila}}" id="nombreProdTD{{$aux_nfila}}">
                                        {{$detalle->notaventadetalle->producto->nombre}}
                                    </td>
                                    <td name="cantTD{{$aux_nfila}}" id="cantTD{{$aux_nfila}}" style="text-align:right">
                                        @if ($aux_sta==2)
                                            {{$detalle->notaventadetalle->cant}}
                                        @else 
                                            {{$detalle->notaventadetalle->cant - $detalle->notaventadetalle->cantusada}}
                                        @endif
                                    </td>
                                    <td name="cantdespF{{$aux_nfila}}" id="cantdespF{{$aux_nfila}}" style="text-align:right">
                                        {{$detalle->cantdesp}}
                                    </td>
                                    <!--
                                    <td name="cantsoldespF{{$aux_nfila}}" id="cantsoldespF{{$aux_nfila}}" style="text-align:right">
                                        {{$detalle->cantsoldesp}}
                                    </td>
                                    -->
                                    <td name="saldocantOrigF{{$aux_nfila}}" id="saldocantOrigF{{$aux_nfila}}" style="text-align:right;display:none;">
                                        {{$detalle->notaventadetalle->cant - $detalle->cantdesp}}
                                    </td>
                                    <td name="saldocantF{{$aux_nfila}}" id="saldocantF{{$aux_nfila}}" style="text-align:right">
                                        {{$detalle->notaventadetalle->cant - $detalle->cantdesp}}
                                    </td>
                                    <td class='tooltipsC' style='text-align:center' class='tooltipsC' title='Marcar'>
                                        <div class='checkbox'>
                                            <label style='font-size: 1.2em'>
                                                <input type="checkbox" class="checkllenarCantSol" id="llenarCantSol{{$aux_nfila}}" name="llenarCantSol{{$aux_nfila}}" onclick="llenarCantSol({{$aux_nfila}})" readonly disabled checked>
                                                <span class='cr'><i class='cr-icon fa fa-check'></i></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td name="cantordF{{$aux_nfila}}" id="cantordF{{$aux_nfila}}" style="text-align:right">
                                        <input type="text" name="cantord[]" id="cantord{{$aux_nfila}}" class="form-control" onkeyup="actSaldo({{$detalle->notaventadetalle->cant - $detalle->cantdesp}},{{$aux_nfila}})" value="{{$detalle->cantdesp}}" style="text-align:right;" readonly disabled/>
                                    </td>
                                    <td name="cantdespF{{$aux_nfila}}" id="cantdespF{{$aux_nfila}}" style="text-align:right;display:none;">
                                        <input type="text" name="cantdesp[]" id="cantdesp{{$aux_nfila}}" class="form-control" value="{{$detalle->cantdesp}}" style="text-align:right;"/>
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="unidadmedida_id[]" id="unidadmedida_id{{$aux_nfila}}" class="form-control" value="4" style="display:none;"/>
                                    </td>
                                    <td name="cla_nombreTD{{$aux_nfila}}" id="cla_nombreTD{{$aux_nfila}}">
                                        {{$detalle->notaventadetalle->producto->claseprod->cla_nombre}}
                                    </td>
                                    <td name="diamextmmTD{{$aux_nfila}}" id="diamextmmTD{{$aux_nfila}}" style="text-align:right">
                                        @if ($detalle->notaventadetalle->producto->categoriaprod->unidadmedida_id==3)
                                            {{$detalle->notaventadetalle->producto->diamextpg}}
                                        @else
                                            {{$detalle->notaventadetalle->producto->diamextmm}}
                                        @endif

                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="diamextmm[]" id="diamextmm{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->diamextmm}}" style="display:none;"/>
                                    </td>
                                    <td name="espesorTD{{$aux_nfila}}" id="espesorTD{{$aux_nfila}}" style="text-align:right">
                                        {{$detalle->notaventadetalle->producto->espesor}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="espesor[]" id="espesor{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->espesor}}" style="display:none;"/>
                                    </td>
                                    <td name="longTD{{$aux_nfila}}" id="longTD{{$aux_nfila}}" style="text-align:right">
                                        {{$detalle->notaventadetalle->producto->long}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="long[]" id="long{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->long}}" style="display:none;"/>
                                    </td>
                                    <td name="pesoTD{{$aux_nfila}}" id="pesoTD{{$aux_nfila}}" style="text-align:right;">
                                        {{$detalle->notaventadetalle->producto->peso}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="peso[]" id="peso{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->peso}}" style="display:none;"/>
                                    </td>
                                    <td name="tipounionTD{{$aux_nfila}}" id="tipounionTD{{$aux_nfila}}"> 
                                        {{$detalle->notaventadetalle->producto->tipounion}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="tipounion[]" id="tipounion{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->tipounion}}" style="display:none;"/>
                                    </td>
                                    <td name="descuentoTD{{$aux_nfila}}" id="descuentoTD{{$aux_nfila}}" style="text-align:right">
                                        <?php $aux_descPorc = $detalle->notaventadetalle->descuento * 100; ?>
                                        {{$aux_descPorc}}%
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="descuento[]" id="descuento{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->descuento}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        <?php $aux_descVal = 1 - $detalle->notaventadetalle->descuento; ?>
                                        <input type="text" name="descuentoval[]" id="descuentoval{{$aux_nfila}}" class="form-control" value="{{$aux_descVal}}" style="display:none;"/>
                                    </td>
                                    <td name="preciounitTD{{$aux_nfila}}" id="preciounitTD{{$aux_nfila}}" style="text-align:right"> 
                                        {{number_format($detalle->notaventadetalle->preciounit, 2, '.', ',')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="preciounit[]" id="preciounit{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->preciounit}}" style="display:none;"/>
                                    </td>
                                    <td name="precioxkiloTD{{$aux_nfila}}" id="precioxkiloTD{{$aux_nfila}}" style="text-align:right"> 
                                        {{number_format($detalle->notaventadetalle->precioxkilo, 2, '.', ',')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="precioxkilo[]" id="precioxkilo{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->precioxkilo}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="precioxkiloreal[]" id="precioxkiloreal{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->precioxkiloreal}}" style="display:none;"/>
                                    </td>
                                    <td name="totalkilosTD{{$aux_nfila}}" id="totalkilosTD{{$aux_nfila}}" style="text-align:right">
                                        {{number_format($detalle->notaventadetalle->totalkilos, 2, '.', ',')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="totalkilos[]" id="totalkilos{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->totalkilos}}" style="display:none;"/>
                                    </td>
                                    <td name="subtotalCFTD{{$aux_nfila}}" id="subtotalCFTD{{$aux_nfila}}" class="subtotalCF" style="text-align:right"> 
                                        {{number_format($detalle->notaventadetalle->subtotal, 2, '.', ',')}}
                                    </td>
                                    <td class="subtotalCF" style="text-align:right;display:none;"> 
                                        <input type="text" name="subtotal[]" id="subtotal{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->subtotal}}" style="display:none;"/>
                                    </td>
                                    <td name="subtotalSFTD{{$aux_nfila}}" id="subtotalSFTD{{$aux_nfila}}" class="subtotal" style="text-align:right;display:none;">
                                        {{$detalle->notaventadetalle->subtotal}}
                                    </td>
                                </tr>
                                <?php $i++;?>
                            @endforeach
                            <tr id="trneto" name="trneto">
                                <td colspan="6" style="text-align:right">
                                    <b>Total:</b>
                                </td>
                                <td style="text-align:right">
                                    <div class="form-group col-xs-12 col-sm-12">
                                        <input type="text" name="cantordTotal" id="cantordTotal" value={{$cantordTotal}} class="form-control" style="text-align:right;" readonly required/>
                                    </div>
                                </td>
                                <td colspan="10" style="text-align:right"><b>Neto</b></td>
                                <td id="tdneto" name="tdneto" style="text-align:right">0.00</td>
                            </tr>
                            <tr id="triva" name="triva">
                                <td colspan="17" style="text-align:right"><b>IVA {{$empresa->iva}}%</b></td>
                                <td id="tdiva" name="tdiva" style="text-align:right">0.00</td>
                            </tr>
                            <tr id="trtotal" name="trtotal">
                                <td colspan="17" style="text-align:right"><b>Total</b></td>
                                <td id="tdtotal" name="tdtotal" style="text-align:right">0.00</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!--
<div class="file-loading">
    <input id="oc_file" name="oc_file" type="file" multiple>
</div>
-->

@include('generales.calcprecioprodsn')
@include('generales.buscarcliente')
@include('generales.buscarproducto')
@if (session('aux_aproNV')=='1')
    @include('generales.aprobarcotnv')
@endif


<div class="modal fade" id="myModalFotoOC" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Producto</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-xs-12 col-sm-12">
                        <div class="form-group">
                            <label for="foto" class="control-label">Orden de Compra</label>
                            <!--<input type="file" name="oc_file" id="oc_file" class="form-control" data-initial-preview="{{isset($data->oc_file) ? Storage::url("/storage/imagenes/notaventa/$data->oc_file") : "http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=Foto+Certificado"}}" accept="image/*"/>-->
                            <!--<input type="file" name="oc_file" id="oc_file" class="form-control" data-initial-preview="{{isset($data->oc_file) ? "/storage/imagenes/notaventa/$data->oc_file" : "http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=Foto+Certificado"}}" accept="image/*"/>-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnOrdenCompra" name="btnOrdenCompra" title="Guardar" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
        
    </div>
</div>

@if ($aux_sta==2 or $aux_sta==3)
    @if ($data->oc_file)
        <?php 
            $ruta = "imagenes/notaventa/";
            $nameFile = $data->oc_file
        ?>
        @include('generales.verfoto')
    @endif
@endif

@include('generales.modalpdf')