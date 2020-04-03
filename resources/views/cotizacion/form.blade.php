<input type="hidden" name="aux_sta" id="aux_sta" value="{{$aux_sta}}">
<input type="hidden" name="aux_fechaphp" id="aux_fechaphp" value="{{old('aux_fechaphp', $fecha ?? '')}}">
<input type="hidden" name="aux_iva" id="aux_iva" value="{{$empresa->iva}}">
<input type="hidden" name="direccioncot" id="direccioncot" value="{{old('direccioncot', $data->direccioncot ?? '')}}">
<input type="hidden" name="cliente_id" id="cliente_id" value="{{old('cliente_id', $data->cliente_id ?? '')}}">
<input type="hidden" name="contacto" id="contacto" value="{{old('contacto', $data->contacto ?? '')}}">
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

<?php
    $disabledReadOnly = "";
    //Si la pantalla es de aprobacion de Cotizacion desactiva todos input
    //$aux_statusPant=='0', Pantalla normal CRUD de Cotizacion
    //$aux_statusPant=='1', Aprobar o rechazar cotización. Y colocar una observacion
    if(session('aux_aprocot')=='1'){
        $disabledReadOnly = ' disabled ';
    }
?>

<div class="container">
    <div class="row">
        <div class="form-group col-xs-12 col-sm-2">
            <!--
            <label for="rut" class="control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
            <div class="input-group">
            <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $clienteselec[0]->rut ?? '')}}" title="F2 Buscar" placeholder="F2 Buscar" required {{$disabledReadOnly}}/>
                <span class="input-group-btn">
                    @if (session('aux_aprocot')=='0')
                        <a id="btnbuscarcliente" name="btnbuscarcliente" href="#" class="btn btn-flat" data-toggle='tooltip' title="Buscar">
                            <i class="fa fa-search"></i>
                        </a>
                    @endif
                </span>
            </div>
            -->
            <label for="rut" class="control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
            <div class="input-group">
                <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $clienteselec[0]->rut ?? '')}}" title="F2 Buscar" placeholder="F2 Buscar" onkeyup="llevarMayus(this);" maxlength="12" required {{$disabledReadOnly}}/>
                <span class="input-group-btn">
                    @if (session('aux_aprocot')=='0')
                        <button class="btn btn-default" type="button" id="btnbuscarcliente" name="btnbuscarcliente" data-toggle='tooltip' title="Buscar">Buscar</button>
                    @endif
                </span>
            </div>

        </div>
        <div class="form-group col-xs-12 col-sm-3">
            <label for="razonsocial" class="control-label requerido" data-toggle='tooltip' title="Razón Social">Razón Social</label>
            <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', $clienteselec[0]->razonsocial ?? '')}}" readonly/>
        </div>

        <div class="form-group col-xs-12 col-sm-4">
            <label for="direccion" class="control-label">Dirección Princ</label>
            <input type="text" name="direccion" id="direccion" class="form-control" value="{{old('direccion', $clienteselec[0]->direccion ?? '')}}" required placeholder="Dirección principal" readonly/>
        </div>
        <div class="form-group col-xs-12 col-sm-2">
            <label for="telefono" class="control-label requerido">Telefono</label>
            <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', $data->telefono ?? '')}}" required readonly/>
        </div>

        <div class="form-group col-xs-12 col-sm-1">
            <label for="fechahora" class="control-label">Fecha</label>
            <input type="text" name="fechahora" id="fechahora" class="form-control" value="{{old('fechahora', $fecha ?? '')}}" style="padding-left: 2px;padding-right: 2px;" required readonly/>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-xs-12 col-sm-2">
            <label for="email" class="control-label requerido">Email</label>
            <input type="text" name="email" id="email" class="form-control" value="{{old('email', $data->email ?? '')}}" required readonly/>
        </div>
        <div class="form-group col-xs-12 col-sm-2">
            <label for="comuna_idD" class="control-label requerido">Comuna</label>
            <select name="comuna_idD" id="comuna_idD" class="selectpicker form-control comuna_idD" data-live-search='true' required readonly disabled>
                <option value="">Seleccione...</option>
                @foreach($comunas as $comuna)
                    <option
                        value="{{$comuna->id}}"
                        @if ($aux_sta==2 and $comuna->id==$data->comuna_id)
                            {{'selected'}}
                        @endif
                        >
                        {{$comuna->nombre}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-xs-12 col-sm-2">
            <label for="vendedor_idD" class="control-label requerido">Vendedor</label>
            <select name="vendedor_idD" id="vendedor_idD" class="selectpicker form-control vendedor_idD" required readonly disabled>
                <option value="">Seleccione...</option>
                @foreach($vendedores1 as $vendedor)
                    <option
                        value="{{$vendedor->id}}"
                        @if (($aux_sta==1) and ($vendedor_id==$vendedor->id))
                            {{'selected'}}
                        @endif
                        @if (($aux_sta==2) and ($data->vendedor_id==$vendedor->id))
                            {{'selected'}}
                        @endif
                        >
                        {{$vendedor->nombre}} {{$vendedor->apellido}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-xs-12 col-sm-2">
            <label for="plazopago_idD" class="control-label requerido">Plazo</label>
            <select name="plazopago_idD" id="plazopago_idD" class="form-control selectpicker plazopago_idD" required readonly disabled>
                <option value=''>Seleccione...</option>
                @foreach($plazopagos as $plazopago)
                    <option
                        value="{{$plazopago->id}}"
                        @if (($aux_sta==2) and ($data->plazopago_id==$plazopago->id))
                            {{'selected'}}
                        @endif
                        >
                        {{$plazopago->descripcion}}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-2">
            <label for="formapago_idD" class="control-label requerido">Forma de Pago</label>
            <select name="formapago_idD" id="formapago_idD" class="form-control selectpicker formapago_idD" required readonly disabled>
                <option value=''>Seleccione...</option>
                @foreach($formapagos as $formapago)
                    <option
                        value="{{$formapago->id}}"
                        @if (($aux_sta==2) and ($data->formapago_id==$formapago->id))
                            {{'selected'}}
                        @endif
                        >
                        {{$formapago->descripcion}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-xs-12 col-sm-2">
            <label for="giro_idD" class="control-label requerido">Giro</label>
            <select name="giro_idD" id="giro_idD" class="form-control selectpicker giro_idD" required readonly disabled>
                <option value=''>Seleccione...</option>
                @foreach($giros as $giro)
                    <option
                        value="{{$giro->id}}"
                        @if (($aux_sta==2) and ($data->giro_id==$giro->id))
                            {{'selected'}}
                        @endif
                        >
                        {{$giro->nombre}}
                    </option>
                @endforeach
            </select>
        </div>
       
    </div>

    <div class="row">
        <div class="form-group col-xs-12 col-sm-3">
            <label id="lblclientedirec_id" name="lblclientedirec_id" for="clientedirec_id" class="control-label">Dirección adicional</label>
            <select name="clientedirec_id" id="clientedirec_id" class="form-control select2 clientedirec_id" data-live-search='true' {{$disabledReadOnly}}>
                <option value="">Seleccione...</option>
                @if ($aux_sta==2)
                    @foreach($clientedirecs as $clientedirec)
                        <option
                        value="{{$clientedirec->id}}"
                        provincia_id="{{$clientedirec->provincia_id}}" 
                        region_id="{{$clientedirec->region_id}}" 
                        comuna_id="{{$clientedirec->comuna_id}}"
                        formapago_id="{{$clientedirec->formapago_id}}"
                        plazopago_id="{{$clientedirec->plazopago_id}}"

                        @if ($data->clientedirec_id==$clientedirec->id))
                            {{'selected'}}
                        @endif
                        >
                        {{$clientedirec->direcciondetalle}}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="form-group col-xs-12 col-sm-3">
            <label for="lugarentrega" class="control-label requerido">Lugar de Entrega</label>
            <input type="text" name="lugarentrega" id="lugarentrega" class="form-control" value="{{old('lugarentrega', $data->lugarentrega ?? '')}}" required placeholder="Lugar de Entrega" {{$disabledReadOnly}}/>
        </div>
        <div class="form-group col-xs-12 col-sm-1">
            <label for="plazoentrega" class="control-label requerido">Plazo Ent.</label>
            <input type="text" name="plazoentrega" id="plazoentrega" class="form-control pull-right datepicker"  value="{{old('plazoentrega', $data->plazoentrega ?? '')}}" readonly required {{$disabledReadOnly}}>
        </div>
        <div class="form-group col-xs-12 col-sm-2">
            <label for="tipoentrega_id" class="control-label requerido">Tipo Entrega</label>
            <select name="tipoentrega_id" id="tipoentrega_id" class="form-control select2 tipoentrega_id" required {{$disabledReadOnly}}>
                <option value=''>Seleccione...</option>
                @foreach($tipoentregas as $tipoentrega)
                    <option
                        value="{{$tipoentrega->id}}"
                        @if (($aux_sta==2) and ($data->tipoentrega_id==$tipoentrega->id))
                            {{'selected'}}
                        @endif
                        >
                        {{$tipoentrega->nombre}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-xs-12 col-sm-3">
            <label for="observacion" class="control-label">Observaciones</label>
            <input type="text" name="observacion" id="observacion" class="form-control" value="{{old('observacion', $data->observacion ?? '')}}" placeholder="Observaciones" {{$disabledReadOnly}}/>
        </div>
    </div>
    <div class="row">
    </div>
</div>
<div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
        <div class="box-header with-border">
            <h3 class="box-title">Detalle</h3>
            @if (session('aux_aprocot')=='0') <!--Estatus en 0 si puede incluir -->
                <div class="box-tools pull-right">
                    <a id="botonNewProd" name="botonNewProd" href="#" class="btn btn-block btn-success btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i> Nuevo Producto
                    </a>
                </div>                    
            @endif
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="tabla-data" style="font-size:14px">
                        <thead>
                            <tr>
                                <th class="width30">ID</th>
                                <th style="display:none;">cotizacionDetalle_ID</th>
                                <th style="display:none;">Codigo Producto</th>
                                <th>Cód Int</th>
                                <th style="display:none;">CódInterno</th>
                                <th>Cant</th>
                                <th style="display:none;">Cant</th>
                                <th>Nombre</th>
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
                                <th>P Neto Unit</th>
                                <th style="display:none;">Precio Neto Unit</th>
                                <th>V Kilo</th>
                                <th style="display:none;">Precio X Kilo</th>
                                <th style="display:none;">Precio X Kilo Real</th>
                                <th>Total Kilos</th>
                                <th style="display:none;">Total Kilos</th>
                                <th>Sub Total</th>
                                <th style="display:none;">Sub Total Neto</th>
                                <th style="display:none;">Sub Total Neto Sin Formato</th>
                                <th class="width70"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($aux_sta==2)
                                <?php $aux_nfila = 0; $i = 0;?>
                                @foreach($cotizacionDetalles as $CotizacionDetalle)
                                    <?php $aux_nfila++; ?>
                                    <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}">
                                        <td name="cotdet_idTD{{$aux_nfila}}" id="cotdet_idTD{{$aux_nfila}}">
                                            {{$CotizacionDetalle->id}}
                                        </td>
                                        <td style="display:none;">
                                            <input type="text" name="cotdet_id[]" id="cotdet_id{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->id}}" style="display:none;"/>
                                        </td>
                                        <td name="producto_idTD{{$aux_nfila}}" id="producto_idTD{{$aux_nfila}}" style="display:none;">
                                            <input type="text" name="producto_id[]" id="producto_id{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->producto_id}}" style="display:none;"/>
                                        </td>
                                        <td name="codintprodTD{{$aux_nfila}}" id="codintprodTD{{$aux_nfila}}">
                                            {{$CotizacionDetalle->producto->codintprod}}
                                        </td>
                                        <td style="display:none;">
                                            <input type="text" name="codintprod[]" id="codintprod{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->producto->codintprod}}" style="display:none;"/>
                                        </td>
                                        <td name="cantTD{{$aux_nfila}}" id="cantTD{{$aux_nfila}}" style="text-align:right">
                                            {{$CotizacionDetalle->cant}}
                                        </td>
                                        <td style="text-align:right;display:none;">
                                            <input type="text" name="cant[]" id="cant{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->cant}}" style="display:none;"/>
                                        </td>
                                        <td name="nombreProdTD{{$aux_nfila}}" id="nombreProdTD{{$aux_nfila}}">
                                            {{$CotizacionDetalle->producto->nombre}}
                                        </td>
                                        <td style="display:none;">
                                            <input type="text" name="unidadmedida_id[]" id="unidadmedida_id{{$aux_nfila}}" class="form-control" value="4" style="display:none;"/>
                                        </td>
                                        <td name="cla_nombreTD{{$aux_nfila}}" id="cla_nombreTD{{$aux_nfila}}">
                                            {{$CotizacionDetalle->producto->claseprod->cla_nombre}}
                                        </td>
                                        <td name="diamextmmTD{{$aux_nfila}}" id="diamextmmTD{{$aux_nfila}}" style="text-align:right">
                                            @if ($CotizacionDetalle->producto->categoriaprod->unidadmedida_id==3)
                                                {{$CotizacionDetalle->producto->diamextpg}}
                                            @else
                                                {{$CotizacionDetalle->producto->diamextmm}}mm
                                            @endif
                                        </td>
                                        <td style="display:none;">
                                            <input type="text" name="diamextmm[]" id="diamextmm{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->producto->diamextmm}}" style="display:none;"/>
                                        </td>
                                        <td name="espesorTD{{$aux_nfila}}" id="espesorTD{{$aux_nfila}}" style="text-align:right">
                                            {{$CotizacionDetalle->producto->espesor}}
                                        </td>
                                        <td style="text-align:right;display:none;"> 
                                            <input type="text" name="espesor[]" id="espesor{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->producto->espesor}}" style="display:none;"/>
                                        </td>
                                        <td name="longTD{{$aux_nfila}}" id="longTD{{$aux_nfila}}" style="text-align:right">
                                            {{$CotizacionDetalle->producto->long}}
                                        </td>
                                        <td style="text-align:right;display:none;"> 
                                            <input type="text" name="long[]" id="long{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->producto->long}}" style="display:none;"/>
                                        </td>
                                        <td name="pesoTD{{$aux_nfila}}" id="pesoTD{{$aux_nfila}}" style="text-align:right;">
                                            {{$CotizacionDetalle->producto->peso}}
                                        </td>
                                        <td style="text-align:right;display:none;"> 
                                            <input type="text" name="peso[]" id="peso{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->producto->peso}}" style="display:none;"/>
                                        </td>
                                        <td name="tipounionTD{{$aux_nfila}}" id="tipounionTD{{$aux_nfila}}"> 
                                            {{$CotizacionDetalle->producto->tipounion}}
                                        </td>
                                        <td style="text-align:right;display:none;"> 
                                            <input type="text" name="tipounion[]" id="tipounion{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->producto->tipounion}}" style="display:none;"/>
                                        </td>
                                        <td name="descuentoTD{{$aux_nfila}}" id="descuentoTD{{$aux_nfila}}" style="text-align:right">
                                            <?php $aux_descPorc = $CotizacionDetalle->descuento * 100; ?>
                                            {{$aux_descPorc}}%
                                        </td>
                                        <td style="text-align:right;display:none;"> 
                                            <input type="text" name="descuento[]" id="descuento{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->descuento}}" style="display:none;"/>
                                        </td>
                                        <td style="text-align:right;display:none;">
                                            <?php $aux_descVal = 1 - $CotizacionDetalle->descuento; ?>
                                            <input type="text" name="descuentoval[]" id="descuentoval{{$aux_nfila}}" class="form-control" value="{{$aux_descVal}}" style="display:none;"/>
                                        </td>
                                        <td name="preciounitTD{{$aux_nfila}}" id="preciounitTD{{$aux_nfila}}" style="text-align:right"> 
                                            {{number_format($CotizacionDetalle->preciounit, 2, '.', ',')}}
                                        </td>
                                        <td style="text-align:right;display:none;"> 
                                            <input type="text" name="preciounit[]" id="preciounit{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->preciounit}}" style="display:none;"/>
                                        </td>
                                        <td name="precioxkiloTD{{$aux_nfila}}" id="precioxkiloTD{{$aux_nfila}}" style="text-align:right"> 
                                            {{number_format($CotizacionDetalle->precioxkilo, 2, '.', ',')}}
                                        </td>
                                        <td style="text-align:right;display:none;"> 
                                            <input type="text" name="precioxkilo[]" id="precioxkilo{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->precioxkilo}}" style="display:none;"/>
                                        </td>
                                        <td style="text-align:right;display:none;"> 
                                            <input type="text" name="precioxkiloreal[]" id="precioxkiloreal{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->precioxkiloreal}}" style="display:none;"/>
                                        </td>
                                        <td name="totalkilosTD{{$aux_nfila}}" id="totalkilosTD{{$aux_nfila}}" style="text-align:right">
                                            {{number_format($CotizacionDetalle->totalkilos, 2, '.', ',')}}
                                        </td>
                                        <td style="text-align:right;display:none;"> 
                                            <input type="text" name="totalkilos[]" id="totalkilos{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->totalkilos}}" style="display:none;"/>
                                        </td>
                                        <td name="subtotalCFTD{{$aux_nfila}}" id="subtotalCFTD{{$aux_nfila}}" class="subtotalCF" style="text-align:right"> 
                                            {{number_format($CotizacionDetalle->subtotal, 2, '.', ',')}}
                                        </td>
                                        <td class="subtotalCF" style="text-align:right;display:none;"> 
                                            <input type="text" name="subtotal[]" id="subtotal{{$aux_nfila}}" class="form-control" value="{{$CotizacionDetalle->subtotal}}" style="display:none;"/>
                                        </td>
                                        <td name="subtotalSFTD{{$aux_nfila}}" id="subtotalSFTD{{$aux_nfila}}" class="subtotal" style="text-align:right;display:none;">
                                            {{$CotizacionDetalle->subtotal}}
                                        </td>
                                        <td>
                                            @if(session('aux_aprocot')=='0')
                                                <a href="#" class="btn-accion-tabla tooltipsC" title="Editar este registro" onclick="editarRegistro({{$aux_nfila}})">
                                                <i class="fa fa-fw fa-pencil"></i>
                                                </a>
                                                <a href="#" class="btn-accion-tabla eliminar tooltipsC" title="Eliminar este registro" onclick="eliminarRegistro({{$aux_nfila}})">
                                                <i class="fa fa-fw fa-trash text-danger"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                    <?php $i++;?>
                                @endforeach
                                <tr id="trneto" name="trneto">
                                    <td colspan="14" style="text-align:right"><b>Neto</b></td>
                                    <td id="tdneto" name="tdneto" style="text-align:right">0.00</td>
                                </tr>
                                <tr id="triva" name="triva">
                                    <td colspan="14" style="text-align:right"><b>IVA {{$empresa->iva}}%</b></td>
                                    <td id="tdiva" name="tdiva" style="text-align:right">0.00</td>
                                </tr>
                                <tr id="trtotal" name="trtotal">
                                    <td colspan="14" style="text-align:right"><b>Total</b></td>
                                    <td id="tdtotal" name="tdtotal" style="text-align:right">0.00</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                </div>

            </div>
        </div>
    </div>

    <!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
    
        <!-- Modal content-->
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h3 class="modal-title">Producto</h3>
        </div>
        <div class="modal-body">
            <input type="hidden" name="aux_numfila" id="aux_numfila" value="0">
            <input type="hidden" name="precioxkilorealM" id="precioxkilorealM" value="0">
            <div class="row">
                <div class="col-xs-12 col-sm-3" classorig="col-xs-12 col-sm-3">
                    <label for="producto_idM" class="control-label" title="F2 Buscar">Producto</label>
                    <div class="input-group">
                        <input type="text" name="producto_idM" id="producto_idM" class="form-control" tipoval="numericootro" value="{{old('producto_idM', $clienteselec[0]->producto_idM ?? '')}}" placeholder="F2 Buscar"/>
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button" id="btnbuscarproducto" name="btnbuscarproducto" data-toggle='tooltip' title="Buscar">Buscar</button>
                        </span>
                    </div>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <label for="codintprodM" class="control-label" data-toggle='tooltip'>Cod Inerno</label>
                    <input type="text" name="codintprodM" id="codintprodM" class="form-control" value="{{old('codintprodM', $data->codintprodM ?? '')}}" placeholder="Cod Interno Prducto" disabled/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-7">
                        <label for="nombreprodM" class="control-label" data-toggle='tooltip'>Nombre Prod</label>
                        <input type="text" name="nombreprodM" id="nombreprodM" class="form-control" value="{{old('nombreprodM', $data->nombreprodM ?? '')}}" placeholder="Nombre Producto" disabled/>
                        <span class="help-block"></span>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-2" classorig="col-xs-12 col-sm-2">
                    <label for="cantM" class="control-label" data-toggle='tooltip'>Cant</label>
                    <input type="text" name="cantM" id="cantM" class="form-control" value="{{old('cantM', $data->cantM ?? '')}}" tipoval="numerico" placeholder="Cant"/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2" classorig="col-xs-12 col-sm-2">
                    <label for="descuentoM" class="control-label" data-toggle='tooltip'>Descuento</label>
                    <select name="descuentoM" id="descuentoM" class="selectpicker form-control descuentoM" tipoval="combobox" data-live-search='true'>
                        <option porc="0" value=1>0%</option>
                        <option porc="0.05" value=0.95>5%</option>
                        <option porc="0.10" value=0.90>10%</option>
                        <option porc="0.15" value=0.85>15%</option>
                        <option porc="0.20" value=0.80>20%</option>
                        <option porc="0.25" value=0.75>25%</option>
                        <option porc="0.30" value=0.70>30%</option>
                        <option porc="0.35" value=0.65>35%</option>
                        <option porc="0.40" value=0.60>40%</option>
                        <option porc="0.45" value=0.55>45%</option>
                        <option porc="0.50" value=0.50>50%</option>

                        <option porc="0.55" value=0.45>55%</option>
                        <option porc="0.60" value=0.40>60%</option>
                        <option porc="0.65" value=0.35>65%</option>
                        <option porc="0.70" value=0.30>70%</option>
                        <option porc="0.75" value=0.25>75%</option>
                        <option porc="0.80" value=0.20>80%</option>
                        <option porc="0.85" value=0.15>85%</option>
                        <option porc="0.90" value=0.10>90%</option>
                        <option porc="0.95" value=0.05>95%</option>
                        <option porc="0.100" value=0.0>100%</option>

                    </select>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2" classorig="col-xs-12 col-sm-2">
                    <label for="precioM" class="control-label" data-toggle='tooltip'>Precio Kg</label>
                    <input type="text" name="precioM" id="precioM" class="form-control" value="{{old('precioM', $data->precio ?? '')}}" valor="0.00" placeholder="Precio Kg"/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <label for="precionetoM" class="control-label" data-toggle='tooltip'>PrecioUnit</label>
                    <input type="text" name="precionetoM" style="text-align:right" id="precionetoM" class="form-control" value="{{old('precionetoM', $data->precioneto ?? '')}}" placeholder="PrecioUnit" valor="0.00" readonly Disabled/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <label for="totalkilosM" class="control-label" data-toggle='tooltip'>Total Kg</label>
                    <input type="text" name="totalkilosM" style="text-align:right" id="totalkilosM" class="form-control" value="{{old('totalkilosM', $data->totalkilosM ?? '')}}" valor="0.00" placeholder="Total Kilos" readonly Disabled/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <label for="subtotalM" class="control-label" data-toggle='tooltip'>SubTotal</label>
                    <input type="text" name="subtotalM" style="text-align:right" id="subtotalM" class="form-control" value="{{old('subtotalM', $data->subtotalM ?? '')}}" valor="0.00" placeholder="SubTotal" readonly Disabled/>
                    <span class="help-block"></span>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-2">
                    <label for="cla_nombreM" class="control-label" data-toggle='tooltip'>Clase</label>
                    <input type="text" name="cla_nombreM" id="cla_nombreM" class="form-control" value="{{old('cla_nombreM', $data->cla_nombreM ?? '')}}" placeholder="Clase" readonly Disabled/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <label for="tipounionM" class="control-label" data-toggle='tooltip'>TUnión</label>
                    <input type="text" name="tipounionM" id="tipounionM" class="form-control" value="{{old('tipounionM', $data->tipounionM ?? '')}}" placeholder="TUnión" readonly Disabled/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <label for="diamextmmM" class="control-label" data-toggle='tooltip'>Diametro</label>
                    <input type="text" name="diamextmmM" id="diamextmmM" class="form-control" value="{{old('diamextmmM', $data->diamextmmM ?? '')}}" placeholder="Diametro" readonly Disabled/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <label for="espesorM" class="control-label" data-toggle='tooltip'>Espesor</label>
                    <input type="text" name="espesorM" id="espesorM" class="form-control" value="{{old('espesorM', $data->espesorM ?? '')}}" placeholder="Espesor" readonly Disabled/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <label for="longM" class="control-label" data-toggle='tooltip'>Largo</label>
                    <input type="text" name="longM" id="longM" class="form-control" value="{{old('longM', $data->longM ?? '')}}" placeholder="Largo" readonly Disabled/>
                    <span class="help-block"></span>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <label for="pesoM" class="control-label" data-toggle='tooltip'>Peso</label>
                    <input type="text" name="pesoM" id="pesoM" class="form-control" value="{{old('pesoM', $data->pesoM ?? '')}}" placeholder="Peso" readonly Disabled/>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary" id="btnGuardarM" name="btnGuardarM" title="Guardar">Guardar</button>
        </div>
        </div>
        
    </div>
</div>

<div class="modal fade" id="myModalBusqueda" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h3 class="modal-title">Buscar Cliente</h3>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover tablas" id="tabla-data-clientes">
                    <thead>
                        <tr>
                            <th class="width70">ID</th>
                            <th>RUT</th>
                            <th>Razón Social</th>
                            <th>Dirección</th>
                            <th>Telefono</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $aux_nfila = 0; $i = 0;?>
                        @foreach($clientes as $cliente)
                            <?php $aux_nfila++;?>
                            <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}">
                                <td>
                                    {{$cliente->id}}
                                </td>
                                <td>
                                    {{$cliente->rut}}
                                </td>
                                <td>
                                    <a href="#" class="copiar_id" onclick="copiar_rut({{$cliente->id}},{{$cliente->rut}})"> {{$cliente->razonsocial}} </a>
                                </td>
                                <td>
                                    {{$cliente->direccion}}
                                </td>
                                <td>
                                    {{$cliente->telefono}}
                                </td>
                            </tr>
                            <?php $i++;?>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
        </div>
        
    </div>
</div>


<div class="modal fade" id="myModalBuscarProd" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Productos</h3>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aux_numfila" id="aux_numfila" value="0">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover tablas" id="tabla-data-productos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Clase</th>
                                <th>Codigo</th>
                                <th>Diametro</th>
                                <th>Esp</th>
                                <th>Long</th>
                                <th>Peso</th>
                                <th>TipU</th>
                                <th>PrecN</th>
                                <th>Prec</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $aux_nfila = 0; $i = 0;?>
                            @foreach($productos as $producto)
                                <?php $aux_nfila++; ?>
                                <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}">
                                    <td name="producto_idBtd{{$aux_nfila}}" id="producto_idBtd{{$aux_nfila}}">
                                        {{$producto->id}}
                                    </td>
                                    <td name="productonombreBtd{{$aux_nfila}}" id="productonombreBtd{{$aux_nfila}}">
                                        <a href="#" class="copiar_id" onclick="copiar_codprod({{$producto->id}},'{{$producto->codintprod}}')"> {{$producto->nombre}} </a>
                                    </td>
                                    <td name="productocla_nombreBtd{{$aux_nfila}}" id="productocla_nombreBtd{{$aux_nfila}}">
                                        {{$producto->cla_nombre}}
                                    </td>
                                    <td name="productocodintprodBtd{{$aux_nfila}}" id="productocodintprodBtd{{$aux_nfila}}">
                                        {{$producto->codintprod}}
                                    </td>
                                    <td name="productodiamextmmBtd{{$aux_nfila}}" id="productodiamextmmBtd{{$aux_nfila}}">
                                        {{$producto->diamextmm}}mm - {{$producto->diamextpg}}
                                    </td>
                                    <td name="productoespesorBtd{{$aux_nfila}}" id="productoespesorBtd{{$aux_nfila}}">
                                        {{$producto->espesor}}
                                    </td>
                                    <td name="productolongBtd{{$aux_nfila}}" id="productolongBtd{{$aux_nfila}}">
                                        {{$producto->long}}
                                    </td>
                                    <td name="productopesoBtd{{$aux_nfila}}" id="productopesoBtd{{$aux_nfila}}">
                                        {{$producto->peso}}
                                    </td>
                                    <td name="productotipounionBtd{{$aux_nfila}}" id="productotipounionBtd{{$aux_nfila}}">
                                        {{$producto->tipounion}}
                                    </td>
                                    <td name="productoprecionetoBtd{{$aux_nfila}}" id="productoprecionetoBtd{{$aux_nfila}}">
                                        {{$producto->precioneto}}
                                    </td>
                                    <td name="productoprecioBtd{{$aux_nfila}}" id="productoprecioBtd{{$aux_nfila}}">
                                        {{$producto->precio}}
                                    </td>
                                </tr>
                                <?php $i++;?>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
        
    </div>
</div>

@if (session('aux_aprocot')=='1')
    <div class="modal fade" id="myModalaprobcot" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" id="mdialTamanio">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">Aprobar Cotización</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="id" class="control-label">ID</label>
                            <input type="text" name="id" id="id" class="form-control" value="{{old('id', $data->id ?? '')}}" required placeholder="Cod Usuario" {{$disabledReadOnly}}/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="aprobusu_id" class="control-label">Cod Usu</label>
                            <input type="text" name="aprobusu_id" id="aprobusu_id" class="form-control" value="{{old('aprobusu_id', auth()->id() ?? '')}}" required placeholder="Cod Usuario" {{$disabledReadOnly}}/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-8">
                            <label for="aprobusu_nom" class="control-label">Nombre Usuario</label>
                            <input type="text" name="aprobusu_nom" id="aprobusu_nom" class="form-control" value="{{old('aprobusu_nom', session()->get('nombre_usuario') ?? '')}}" required placeholder="Nombre Usuario" {{$disabledReadOnly}}/>
                        </div>
                    </div>
                        <!--
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="aprobfechahora" class="control-label requerido">Fecha aprobación</label>
                            <input type="text" name="aprobfechahora" id="aprobfechahora" class="form-control" value="{{old('aprobfechahora', $data->aprobfechahora ?? '')}}" required placeholder="Fecha Aprobación" {{$disabledReadOnly}}/>
                        </div>-->
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-12" classorig="form-group col-xs-12 col-sm-12">
                            <label for="aprobobs" class="control-label">Observación</label>
                            <textarea name="aprobobs" id="aprobobs" class="form-control requeridos" tipoval="texto" value="{{old('aprobobs', $data->aprobobs ?? '')}}" placeholder="Observación"></textarea>
                            <span class="help-block"></span>
                            <!--<input type="textarea" name="aprobobs" id="aprobobs" class="form-control" value="{{old('aprobobs', $data->aprobobs ?? '')}}" required placeholder="Observación"/>-->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="btnaprobarM" name="btnaprobarM" class="btn btn-primary">Aprobar</button>
                    <button type="button" id="btnrechazarM" name="btnrechazarM" class="btn btn-danger">Rechazar</button>
                </div>
            </div>
            
        </div>
    </div>
@endif

    <!-- Modal -->
    <div class="modal fade" id="myModalClienteTemp" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
        
            <!-- Modal content-->
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Cliente Temporal</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="razonsocialCTM" class="control-label" data-toggle='tooltip'>Razón Social</label>
                        <input type="text" name="razonsocialCTM" id="razonsocialCTM" class="form-control requeridos" tipoval="texto" value="{{old('razonsocialCTM')}}" placeholder="Razón Social"/>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="direccionCTM" class="control-label" data-toggle='tooltip' title="direccion">Dirección</label>
                        <input type="text" name="direccionCTM" id="direccionCTM" class="form-control requeridos" tipoval="texto"  maxlength="200" value="{{old('direccionCTM')}}" placeholder="Dirección"/>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="telefonoCTM" class="control-label" data-toggle='tooltip' title="Teléfono">Teléfono</label>
                        <input type="text" name="telefonoCTM" id="telefonoCTM" class="form-control requeridos" tipoval="numerico" maxlength="50" value="{{old('telefonoCTM')}}" placeholder="Teléfono"/>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-3" classorig="col-xs-12 col-sm-3">
                        <label for="emailCTM" class="control-label" data-toggle='tooltip' title="email">Email</label>
                        <input type="text" name="emailCTM" id="emailCTM" class="form-control requeridos" tipoval="email" maxlength="50" value="{{old('emailCTM')}}" placeholder="Email"/>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-3" classorig="col-xs-12 col-sm-3">
                        <label for="giro_idCTM" class="control-label" data-toggle='tooltip' title="Giro">Giro</label>
                        <select name="giro_idCTM" id="giro_idCTM" class="selectpicker form-control requeridos" tipoval="texto" value="{{old('giro_idCTM')}}">
                            <option value="">Seleccione...</option>
                            @foreach($giros as $giro)
                                <option
                                    value="{{$giro->id}}"
                                    >
                                    {{$giro->nombre}}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-3" classorig="col-xs-12 col-sm-3">
                        <label for="formapago_idCTM" class="control-label">Forma de Pago</label>
                        <select name="formapago_idCTM" id="formapago_idCTM" class="selectpicker form-control requeridos" tipoval="texto" data-live-search='true' title='Seleccione...' value="{{old('formapago_idCTM')}}">
                            <option value="">Seleccione...</option>
                            @foreach($formapagos as $formapago)
                                <option
                                    value="{{$formapago->id}}"
                                    >
                                    {{$formapago->descripcion}}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-3" classorig="col-xs-12 col-sm-3">
                        <label for="plazopago_idCTM" class="control-label">Plazo de Pago</label>
                        <select name="plazopago_idCTM" id="plazopago_idCTM" class="selectpicker form-control requeridos" tipoval="texto" data-live-search='true' title='Seleccione...' value="{{old('plazopago_idCTM')}}">
                            <option value="">Seleccione...</option>
                            @foreach($plazopagos as $plazopago)
                                <option
                                    value="{{$plazopago->id}}"
                                    >
                                    {{$plazopago->descripcion}}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="comunap_idCTM" class="control-label">Comuna</label>
                        <select name="comunap_idCTM" id="comunap_idCTM" class="selectpicker form-control requeridos" tipoval="texto" data-live-search='true' title='Seleccione...' value="{{old('comunap_idCTM')}}">
                            <option value="">Seleccione...</option>
                            @foreach($comunas as $comuna)
                                <option
                                    value="{{$comuna->id}}"
                                    region_id="{{$comuna->provincia->region_id}}"
                                    provincia_id="{{$comuna->provincia_id}}"
                                    >
                                    {{$comuna->nombre}}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="provinciap_idCTM" class="control-label">Provincia</label>
                        <select name="provinciap_idCTM" id="provinciap_idCTM" class="selectpicker form-control provinciap_id" tipoval="texto" title='Seleccione...' disabled readonly value="{{old('provinciap_idCTM')}}">
                            @foreach($provincias as $provincia)
                                <option
                                    value="{{$provincia->id}}"
                                    >
                                    {{$provincia->nombre}}
                                </option>
                            @endforeach  
                        </select>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="regionp_idCTM" class="control-label">Región</label>
                        <select name="regionp_idCTM" id="regionp_idCTM" class="selectpicker form-control regionp_id" tipoval="texto" title='Seleccione...' disabled readonly value="{{old('regionp_idCTM')}}">
                            @foreach($regiones as $region)
                                <option
                                    value="{{$region->id}}"
                                    >
                                    {{$region->nombre}}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="contactonombreCTM" class="control-label" data-toggle='tooltip' title="Nombre Contacto">Nombre Contacto</label>
                        <input type="text" name="contactonombreCTM" id="contactonombreCTM" class="form-control requeridos" tipoval="texto" placeholder="Nombre Contacto" value="{{old('contactonombreCTM')}}"/>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="contactoemailCTM" class="control-label" data-toggle='tooltip' title="Email Contacto">Email Contacto</label>
                        <input type="text" name="contactoemailCTM" id="contactoemailCTM" class="form-control requeridos" tipoval="email" placeholder="Email Contacto" value="{{old('contactoemailCTM')}}"/>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="contactotelefCTM" class="control-label" data-toggle='tooltip' title="Teléfono Contacto">Teléfono Contacto</label>
                        <input type="text" name="contactotelefCTM" id="contactotelefCTM" class="form-control requeridos" tipoval="numerico" placeholder="Teléfono Contacto" value="{{old('contactotelefCTM')}}"/>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="finanzascontactoCTM" class="control-label" data-toggle='tooltip' title="Contacto Finanzas">Contacto Finanzas</label>
                        <input type="text" name="finanzascontactoCTM" id="finanzascontactoCTM" class="form-control requeridos" tipoval="texto" placeholder="Contacto Finanzas" value="{{old('finanzascontactoCTM')}}"/>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="finanzanemailCTM" class="control-label" data-toggle='tooltip' title="Email Finanzas">Email Finanzas</label>
                        <input type="text" name="finanzanemailCTM" id="finanzanemailCTM" class="form-control requeridos" tipoval="email" placeholder="Email Finanzas" value="{{old('finanzanemailCTM')}}"/>
                        <span class="help-block"></span>
                    </div>
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="finanzastelefonoCTM" class="control-label" data-toggle='tooltip' title="Teléfono Finanzas">Teléfono Finanzas</label>
                        <input type="text" name="finanzastelefonoCTM" id="finanzastelefonoCTM" class="form-control requeridos" tipoval="numerico" placeholder="Teléfono Finanzas" value="{{old('finanzastelefonoCTM')}}"/>
                        <span class="help-block"></span>
                    </div>
                </div>                
                <div class="row">
                    <div class="col-xs-12 col-sm-4" classorig="col-xs-12 col-sm-4">
                        <label for="sucursal_idCTM" class="control-label">Sucursal</label>
                        <select name="sucursal_idCTM" id="sucursal_idCTM" class="selectpicker form-control requeridos" tipoval="texto" title='Seleccione...'>
                            @foreach($sucursales as $sucursal)
                                <option
                                    value="{{$sucursal->id}}"
                                    >
                                    {{$sucursal->nombre}}
                                </option>
                            @endforeach
                        </select>
                        <span class="help-block"></span>
                    </div>

                    <div class="col-xs-12 col-sm-8" classorig="col-xs-12 col-sm-8">
                        <label for="observacionesCTM" class="control-label" data-toggle='tooltip' title="Observaciones">Observación</label>
                        <textarea class="form-control requeridos" name="observacionesCTM" id="observacionesCTM" placeholder="Observación" value="{{old('contactotelefCTM')}}"></textarea>
                        <span class="help-block"></span>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarCTM" name="btnGuardarCTM" title="Guardar">Guardar</button>
            </div>
            </div>
            
        </div>
    </div>