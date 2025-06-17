<input type="hidden" name="aux_fechaphp" id="aux_fechaphp" value="{{old('aux_fechaphp', $fecha ?? '')}}">
<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<input type="hidden" name="cliente_id" id="cliente_id" value="{{old('cliente_id', $data->cliente_id ?? '')}}">
<input type='hidden' id="aux_obs" name="aux_obs" value="{{old('aux_obs', $data->obs ?? '')}}">
<input type="hidden" name="aux_iva" id="aux_iva" value="{{old('aux_iva', $tablas['empresa']->iva ?? '')}}">
<input type="hidden" name="dtefoliocontrol_id" id="dtefoliocontrol_id" value="1">
<input type="hidden" name="foliocontrol_id" id="foliocontrol_id" value="1">
<input type="hidden" name="updatednum_at" id="updatednum_at" value="{{old('updatednum_at', isset($data) ? strtotime($data->updated_at) : "")}}">

@php
    $aux_oc_file = $data->otoc->oc_file ?? '';
@endphp

<input type="hidden" name="imagen" id="imagen" value="{{old('imagen', $aux_oc_file ?? '')}}">
<input type="hidden" name="tipoprod" id="tipoprod" value="10">
<div class="form-group col-xs-4 col-sm-4" style="display:none;">
    <label for="oc_fileaux" class="control-label requerido" data-toggle='tooltip' title="Adjuntar Orden de compra">Adjuntar Orden de compra</label>
    <input type="hidden" name="oc_fileaux" id="oc_fileaux" value="" class="form-control" style="text-align:right;">
</div>
<div style="display: none">
    <select name="auxunidadmedida_id" id="unidadmedida_id" class="form-control select2">
        <option value=""></option>
        @foreach($tablas['unidadmedidas'] as $id => $descripcion)
            <option value="{{$descripcion->id}}">{{$descripcion->nombre}}</option>
        @endforeach
    </select>
</div>
<?php 
    $aux_rut = "";
    if(isset($data)){
        $aux_rut = number_format( substr ( $data->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->cliente->rut, strlen($data->cliente->rut) -1 , 1 );
    }
    $aux_labelRequerido = "";
    $aux_inputRequerido = "";
    $enableCamposCot = ""; //Este campo lo cambio a disbles si llegara a necesitar desactivar los campos marcados con esta variable
?>

<div class="row">
    <div class="col-xs-12 col-sm-9">
        <div class="row">
            <div class="form-group col-xs-12 col-sm-3">
                <label for="rut" class="control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
                @if (isset($data))
                    <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $data->cliente->rut ?? '')}}" readonly disabled/>
                @else
                    <div class="input-group">
                        <input type="text" name="rut" id="rut" class="form-control" value="" onkeyup="llevarMayus(this);" title="F2 Buscar" placeholder="F2 Buscar" maxlength="12" required/>
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button" id="btnbuscarcliente" name="btnbuscarcliente" data-toggle='tooltip' title="Buscar">Buscar</button>
                        </span>
                    </div>
                    
                @endif
            </div>
            <div class="form-group col-xs-12 col-sm-5">
                <label for="razonsocial" class="control-label requerido" data-toggle='tooltip' title="Razón Social">Razón Social</label>
                <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', $data->cliente->razonsocial ?? '')}}" readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-4">
                <label for="direccion" class="control-label">Dirección Princ</label>
                <input type="text" name="direccion" id="direccion" class="form-control" value="{{old('direccion', $data->cliente->direccion ?? '')}}" required placeholder="Dirección principal" readonly/>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-2">
                <label for="comuna_nombre" class="control-label requerido">Comuna</label>
                <input type="text" name="comuna_nombre" id="comuna_nombre" class="form-control" value="{{old('comuna_nombre', $data->cliente->comuna->nombre ?? '')}}" required readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="telefono" class="control-label requerido">Telefono</label>
                <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', $data->cliente->telefono ?? '')}}" required readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-5">
                <label for="email" class="control-label requerido">Email</label>
                <input type="text" name="email" id="email" class="form-control" value="{{old('email', $data->cliente->email ?? '')}}" required readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-3">
                <label for="sucursal_id" class="control-label requerido">Sucursal</label>
                <select name="sucursal_id" id="sucursal_id" class="form-control select2 sucursal_id" data-live-search='true' required>
                    <option value=''>Seleccione...</option>
                    @if (isset($data))
                        @foreach($tablas['sucursales'] as $sucursal)
                            <option
                                value="{{$sucursal->id}}"
                                @if (isset($data->sucursal_id) and ($data->sucursal_id==$sucursal->id))
                                    {{'selected'}}
                                @endif
                            >{{$sucursal->nombre}}</option>
                        @endforeach
                    @endif
                </select>
            </div>

        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-4">
                <label for="obs" class="control-label">Observaciones</label>
                <textarea class="form-control" name="obs" id="obs" value="{{old('obs', $data->obs ?? '')}}" placeholder="Observación" maxlength="90"></textarea>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="fechaestdesp" class="control-label requerido" data-toggle='tooltip' title="Fecha estimada de Despacho">Fec Est Despacho</label>
                <input type="text" name="fechaestdesp" id="fechaestdesp" class="form-control pull-right" value="{{old('fechaestdesp', $data->fechaestdesp ?? '')}}" required readonly/>
            </div>

        </div>    
    </div>
    <div class="col-xs-12 col-sm-3">
        <div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
            <div class="box-header with-border">
                <div class="box-body">
                    <div class="row">
                        <div id="group_oc_id" class="form-group col-xs-12 col-sm-12">
                            <label id="lboc_id" name="lboc_id" for="oc_id" class="control-label {{$aux_labelRequerido}}">Nro OrdenCompra</label>
                            <div class="input-group">
                                <input type="text" name="oc_id" id="oc_id" class="form-control" value="{{old('oc_id', $data->otoc->oc_id ?? '')}}" placeholder="Nro Orden de Compra" maxlength="18" {{$enableCamposCot}} {{$aux_inputRequerido}}/>
                            </div>
                        </div>
                        <div id="group_oc_file" class="form-group col-xs-12 col-sm-12">
                            <label id="lboc_oc_file" name="lboc_oc_file" for="oc_file" class="control-label">Adjuntar OC</label>
                            <div class="input-group">
                                <input type="file" name="oc_file" id="oc_file" class="form-control" data-initial-preview='{{isset($data->otoc->oc_id) ? Storage::url("imagenes/oc/$aux_oc_file") : ""}}' accept="*"/>
                            </div>
                            <span id="oc_file-error" style="color:#dd4b39;display: none;">Este campo es obligatorio.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group col-xs-4 col-sm-4" style="display:none;">
    <label name="lblitemcompletos" id="lblitemcompletos" for="itemcompletos" class="control-label requerido" data-toggle='tooltip' title="Complete valores item">Complete valores item 1</label>
    <input type="hidden" name="itemcompletos" id="itemcompletos" value="" class="form-control" style="text-align:right;" readonly required>
</div>
<div class="form-group col-xs-4 col-sm-4" style="display:none;">
    <label for="total" class="control-label requerido" data-toggle='tooltip' title="Total Documento">Total Documento</label>
    <input type="hidden" name="total" id="total" value="{{old('total', $data->mnttotal ?? '')}}" class="form-control" style="text-align:right;" readonly>
</div>
<div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
    <div class="box-header with-border">
        <h3 class="box-title">Detalle</h3>
        <div class="box-tools pull-right">
            <a onclick="agregarFila()" id="additem" name="additem" class="btn btn-block btn-success btn-sm">
                <i class="fa fa-fw fa-plus-circle"></i>Agregar item
            </a>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="tabla-data" style="font-size:14px">
                    <thead>
                        <tr>
                            <th style="text-align:center;" class="width30">item</th>
                            <th class="width30 tooltipsC" title="Código Producto">CodProd</th>
                            <th class="width100" style="text-align:right;" title="Cantidad">Cant</th>
                            <th class="width100 tooltipsC" title="Unidad de Medida">UniMed</th>
                            <th title="Nombre producto">Nombre</th>
                            <th title="Observacion">Observacion</th>
                            <th class="width100" style="text-align:right;">Kg</th>
                            <th style="display:none;">Desc</th>
                            <th style="display:none;">DescPorc</th>
                            <th style="display:none;">DescVal</th>
                            <th class="width100 tooltipsC" title="Precio Unitario" style="text-align:right;display:none;">PUnit</th>
                            <th style="display:none;">V Kilo</th>
                            <th style="display:none;">Precio X Kilo</th>
                            <th style="display:none;">Precio X Kilo Real</th>
                            <th class="width150;" style="text-align:right;display:none;">Sub Total</th>
                            <th style="display:none;">Sub Total</th>
                            <th class="width30" >Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $aux_totalcant = 0;
                            $aux_totalkg = 0;
                            $aux_fila = 0;
                        @endphp
                        @if (isset($data))
                            @foreach ($data->otdets as $otdet)
                                @php
                                    //dd($otdet->producto->acuerdotecnico);
                                    $atributoProd = $otdet->producto->atributosProducto($otdet->producto_id);
                                    $aux_producto_nombre = $atributoProd["nombre"];
                                    $aux_totalcant += $otdet->cant;
                                    $aux_totalkg += $otdet->kg;
                                    $aux_fila++;
                                    $aux_at_peso = 1;
                                    if(isset($otdet->producto->acuerdotecnico)){
                                        $aux_at_peso = $otdet->producto->acuerdotecnico->at_peso;
                                        //dd($aux_at_peso);
                                    }else{
                                        if($otdet->producto->peso > 0){
                                            $aux_at_peso = $otdet->producto->peso;
                                        }
                                    }
                                @endphp
                                <tr name="fila{{$aux_fila}}" id="fila{{$aux_fila}}" class="proditems" item="{{$aux_fila}}">
                                    <td id="nroitem{{$aux_fila}}" name="nroitem{{$aux_fila}}" class="nroitem" style="text-align:center;vertical-align:middle;">
                                        {{$aux_fila}}
                                    </td>
                                    <td style="text-align:center" name="producto_idTD{{$aux_fila}}" id="producto_idTD{{$aux_fila}}" >
                                        <input type="text" name="vlrcodigo[]" id="vlrcodigo{{$aux_fila}}" onblur="onBlurProducto_id(this)" class="form-control numerico itemrequerido calcularkgprod inikgitem" value="{{$otdet->producto_id}}" maxlength="4" onkeyup="buscarProdKeyUp(this,event)" style="text-align:right" item="{{$aux_fila}}" title="Código Producto (Presione F2 para buscar Producto)" producto_id="{{$otdet->producto_id}}" at_peso="{{$aux_at_peso}}"/>
                                        <input type="text" name="producto_id[]" id="producto_id{{$aux_fila}}" class="form-control numerico" value="{{$otdet->producto_id}}" maxlength="4" onkeyup="buscarProdKeyUp(this,event)" style="text-align:right;display:none;"/>
                                        <input type="text" name="nrolindet[]" id="nrolindet{{$aux_fila}}" class="form-control" value="{{$aux_fila}}" style="display:none;"/>
                                        <input type="text" name="otdet_id[]" id="otdet_id{{$aux_fila}}" class="form-control" value="{{$otdet->id}}" style="display:none;"/>
                                    </td>
                                    <td name="cantTD{{$aux_fila}}" id="cantTD{{$aux_fila}}" style="text-align:right" class="subtotalcant" valor="{{$otdet->cant}}">
                                        <input type="text" name="cant[]" id="cant{{$aux_fila}}" class="form-control" value="{{$otdet->cant}}" style="display:none;" valor="{{$otdet->cant}}" fila="{{$aux_fila}}"/>
                                        <input type="text" name="qtyitem[]" id="qtyitem{{$aux_fila}}" class="form-control numerico calsubtotalitem itemrequerido calcularkgprod" value="{{number_format($otdet->cant, 2, ',', '.')}}" valor="{{$otdet->cant}}" valorini="" item="{{$aux_fila}}"style="text-align:right" title="Cantidad producto" maxlength="10"/>
                                    </td>
                                    <td name="unidadmedida_nombre{{$aux_fila}}" id="unidadmedida_nombre{{$aux_fila}}" valor="">
                                        <input type="text" name="unidadmedidainp_id[]" id="unidadmedidainp_id{{$aux_fila}}" value="{{$otdet->unidadmedida_id}}" style="display:none;"/>
                                        <input type="text" name="unidadmedida_id[]" id="unidadmedida_id{{$aux_fila}}" class="form-control" value="{{$otdet->unidadmedida_id}}" style="display:none;"/>
                                        <select id="unmditem{{$aux_fila}}" name="unmditem[]" class="form-control itemrequerido" title="Unidad Medida" readonly disabled>
                                            @foreach($tablas["unidadmedidas"] as $unidadmedida)
                                                <option
                                                    value="{{$unidadmedida->id}}"
                                                    @if ($unidadmedida->id==$otdet->unidadmedida_id)
                                                        {{'selected'}}
                                                    @endif
                                                >
                                                {{$unidadmedida->nombre}}
                                            @endforeach
                                        </select>
                                    </td>
                                    <td name="nombreProdTDDir{{$aux_fila}}" id="nombreProdTDDir{{$aux_fila}}" valor="">
                                        <input type="text" name="nmbitem[]" id="nmbitem{{$aux_fila}}" class="form-control itemrequerido" value="{{$aux_producto_nombre}}" title="{{$aux_producto_nombre}}" readonly/>
                                        <input type="text" name="dscitem[]" id="dscitem{{$aux_fila}}" class="form-control" value="{{$aux_producto_nombre}}" style="display:none;"/>
                                    </td>
                                    <td>
                                        <input type="text" name="otdet_obs[]" id="otdet_obs{{$aux_fila}}" class="form-control" value="{{$otdet->obs}}" title="{{$otdet->obs}}" maxlength="100"/>
                                    </td>
                                    <td name="kgTD{{$aux_fila}}" id="kgTD{{$aux_fila}}" style="text-align:right;" class="" valor="{{$otdet->kg}}">
                                        <input type="text" name="totalkilos[]" id="totalkilos{{$aux_fila}}" class="form-control" value="{{$otdet->kg}}" style="display:none;" valor="{{$otdet->kg}}" fila="{{$aux_fila}}"/>
                                        <input type="text" name="itemkg[]" id="itemkg{{$aux_fila}}" class="form-control numerico itemrequerido subtotalkg calsubtotalitem kgitemkeyup" value="{{number_format($otdet->kg, 2, ',', '.')}}" valor="{{$otdet->kg}}" item="{{$aux_fila}}" style="text-align:right" maxlength="15"/>
                                    </td>
                                    <td name="descuentoTD{{$aux_fila}}" id="descuentoTD{{$aux_fila}}" style="text-align:right;display:none;">
                                        0%
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="descuento[]" id="descuento{{$aux_fila}}" class="form-control" value="0" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        <input type="text" name="descuentoval[]" id="descuentoval{{$aux_fila}}" class="form-control" value="0" style="display:none;"/>
                                    </td>
                                    <td name="preciounitTD{{$aux_fila}}" id="preciounitTD{{$aux_fila}}" style="text-align:right;display:none;">
                                        <input type="text" name="prcitem[]" id="prcitem{{$aux_fila}}" class="form-control numerico calsubtotalitem itemrequerido1" value="" valor="" valorini="" item="{{$aux_fila}}" style="text-align:right" title="Precio Unitario"/>
                                    </td>
                                    <td style="display:none;" name="precioxkiloTD{{$aux_fila}}" id="precioxkiloTD{{$aux_fila}}" style="text-align:right">
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        <input type="text" name="precioxkilo[]" id="precioxkilo{{$aux_fila}}" class="form-control" value="0" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        <input type="text" name="precioxkiloreal[]" id="precioxkiloreal{{$aux_fila}}" class="form-control" value="0" style="display:none;"/>
                                    </td>
                                    <td name="subtotalFactDet{{$aux_fila}}" id="subtotalFactDet{{$aux_fila}}" class="subtotalFactDet" style="text-align:right;display:none;">
                                        <input type="text" name="montoitem[]" id="montoitem{{$aux_fila}}" class="form-control numerico calpreciounit" value="0" valor="" valorini="" item="{{$aux_fila}}" style="text-align:right" readonly/>
                                    </td>
                                    <td name="subtotalSFTD{{$aux_fila}}" id="subtotalSFTD{{$aux_fila}}" class="subtotal" style="text-align:right;display:none;">
                                        0
                                    </td>
                                    <td style="vertical-align:middle;"> 
                                        <a onclick="delitem({{$aux_fila}})" class="btn-accion-tabla tooltipsC" title="Eliminar" id="delitem{{$aux_fila}}" name="delitem{{$aux_fila}}">
                                            <i class="fa fa-fw fa-trash text-danger"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                        @endif
                    </tbody>
                    @php
                        $aux_display = "display:none;";
                    @endphp
                    @if ($aux_totalcant > 0)
                        @php
                            $aux_display = "";    
                        @endphp
                    @endif
                    <tfoot style="{{$aux_display}}" id="foottotal" name="foottotal">
                        <div id="foot">
                            <tr id="trneto" name="trneto">
                                <th colspan="2" style="text-align:right">
                                    <b>Totales:</b>
                                </th>
                                <th id="Tcant" name="Tcant" style="text-align:right">
                                    {{number_format($aux_totalcant, 2, ',', '.')}}
                                </th>
                                <th colspan="3" style="text-align:right;"><b>Total Kg</b></th>
                                <th id="totalkg" name="totalkg" style="text-align:right;" valor="{{$aux_totalkg}}">{{number_format($aux_totalkg, 2, ',', '.')}}</th>
                                <th colspan="5" style="text-align:right;display:none;"><b>Neto</b></th>
                                <th id="tdneto" name="tdneto" style="text-align:right;display:none;">0</th>
                            </tr>
                            <tr id="triva" name="triva">
                                <th colspan="8" style="text-align:right;display:none;"><b>IVA {{$tablas['empresa']->iva}}%</b></th>
                                <th id="tdiva" name="tdiva" style="text-align:right;display:none;">0</th>
                            </tr>
                            <tr id="trtotal" name="trtotal">
                                <th colspan="8" style="text-align:right;display:none;"><b>Total</b></th>
                                <th id="tdtotal" name="tdtotal" style="text-align:right;display:none;">0</th>
                            </tr>
                        </div>
                    </tfoot>

                </table>
            </div>
        </div>
    </div>
</div>
<input type="text" name="ids" id="ids" value="{{$aux_fila}}" style="display: none">


@include('generales.modalpdf')
@include('generales.buscarclientebd')
@include('generales.buscarproductobd')