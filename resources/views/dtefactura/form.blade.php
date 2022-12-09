<input type="hidden" name="dte_id" id="dte_id" value="{{old('dte_id', $data->id ?? '')}}">
<input type="hidden" name="dte_id" id="aux_fechaphp" value="{{old('aux_fechaphp', $fecha ?? '')}}">
<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<input type="hidden" name="cliente_id" id="cliente_id" value="{{old('cliente_id', $data->cliente_id ?? '')}}">
<input type='hidden' id="aux_obs" name="aux_obs" value="{{old('aux_obs', $data->obs ?? '')}}">
<input type="hidden" name="aux_iva" id="aux_iva" value="{{old('aux_iva', $tablas['empresa']->iva ?? '')}}">
<input type="hidden" name="updated_at" id="updated_at" value="{{old('updated_at', $data->updated_at ?? '')}}">




<?php 
    $aux_rut = "";
    if(isset($data)){
        $aux_rut = number_format( substr ( $data->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->cliente->rut, strlen($data->cliente->rut) -1 , 1 );
    }
?>


<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
            <div class="box-header with-border">
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="rut" class="control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
                            @if (isset($data))
                                <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $aux_rut ?? '')}}" readonly disabled/>
                            @else
                                <div class="input-group">
                                    <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $clienteselec[0]->rut ?? '')}}" onkeyup="llevarMayus(this);" title="F2 Buscar" placeholder="F2 Buscar" maxlength="12" required/>
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" id="btnbuscarcliente" name="btnbuscarcliente" data-toggle='tooltip' title="Buscar">Buscar</button>
                                    </span>
                                </div>
                                
                            @endif
                        </div>
                        <div class="form-group col-xs-12 col-sm-4">
                            <label for="razonsocial" class="control-label requerido" data-toggle='tooltip' title="Razón Social">Razón Social</label>
                            <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', $data->cliente->razonsocial ?? '')}}" readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-4">
                            <label for="direccion" class="control-label">Dirección Princ</label>
                            <input type="text" name="direccion" id="direccion" class="form-control" value="{{old('direccion', $data->cliente->direccion ?? '')}}" required placeholder="Dirección principal" readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="telefono" class="control-label requerido">Telefono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', $data->cliente->telefono ?? '')}}" required readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="email" class="control-label requerido">Email</label>
                            <input type="text" name="email" id="email" class="form-control" value="{{old('email', $data->cliente->email ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-1">
                            <label for="comuna_nombre" class="control-label requerido">Comuna</label>
                            <input type="text" name="comuna_nombre" id="comuna_nombre" class="form-control" value="{{old('comuna_nombre', $data->cliente->comuna->nombre ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="provincia_nombre" class="control-label requerido">Provincia</label>
                            <input type="text" name="provincia_nombre" id="provincia_nombre" class="form-control" value="{{old('provincia_nombre', $data->cliente->comuna->provincia->nombre ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="formapago_desc" class="control-label requerido">Forma de Pago</label>
                            <input type="text" name="formapago_desc" id="formapago_desc" class="form-control" value="{{old('formapago_desc', $data->cliente->formapago->descripcion ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-1">
                            <label for="plazopago" class="control-label requerido">Plazo Pago</label>
                            <input type="text" name="plazopago" id="plazopago" class="form-control" value="{{old('plazopago', $data->cliente->plazopago->descripcion ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="fchemis" class="control-label requerido">Fecha Emision</label>
                            <input type="text" name="fchemis" id="fchemis" class="form-control pull-right datepicker"  value="{{old('fchemis', date("d/m/Y") )}}" readonly required>
                        </div>

                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="fchvenc" class="control-label requerido">F. Venc</label>
                            <input type="text" name="fchvenc" id="fchvenc" class="form-control" value="{{old('fchvenc', isset($data) ? date('d/m/Y', strtotime(date('Y-m-d') ."+ " . $data->cliente->plazopago->dias . " days"))  : "")}}" required readonly/>
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="vendedor_id" class="control-label requerido">Vendedor</label>
                            <select name="vendedor_id" id="vendedor_id" class="form-control select2 vendedor_id" data-live-search='true' required>
                                <option value="">Seleccione...</option>
                                @foreach($tablas['vendedores'] as $vendedor)
                                    <option
                                        value="{{$vendedor->id}}"
                                        @if (isset($data) and ($data->vendedor_id == $vendedor->id))
                                            {{'selected'}}
                                        @endif
                                        >{{$vendedor->nombre}} {{$vendedor->apellido}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="centroeconomico_id" class="control-label requerido">Centro Economico</label>
                            <select name="centroeconomico_id" id="centroeconomico_id" class="form-control select2 centroeconomico_id" data-live-search='true' required>
                                <option value="">Seleccione...</option>
                                @foreach($centroeconomicos as $centroeconomico)
                                    <option
                                        value="{{$centroeconomico->id}}"
                                        @if (isset($data) and $data->centroeconomico_id==$centroeconomico->id) 
                                            {{'selected'}}
                                        @endif
                                        >{{$centroeconomico->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="hep" class="control-label">Num Atencion o Hep</label>
                            <input type="text" name="hep" id="hep" class="form-control" value="{{old('hep', $data->dtefac->hep ?? '')}}" maxlength="12"/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6">
                            <label for="obs" class="control-label">Observaciones</label>
                            <textarea class="form-control" name="obs" id="obs" value="{{old('obs', $data->obs ?? '')}}" placeholder="Observación" maxlength="90"></textarea>
                        </div>
                    </div>    
                </div>
            </div>
        </div>
    </div>    
</div>
<div class="form-group col-xs-4 col-sm-4" style="display:none;">
    <label for="total" class="control-label requerido" data-toggle='tooltip' title="Total Documento">Total Documento</label>
    <input type="hidden" name="total" id="total" value="{{old('total', $data->mnttotal ?? '')}}"class="form-control" style="text-align:right;" readonly required>
</div>
<div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
    <div class="box-header with-border">
        <h3 class="box-title">Detalle</h3>
        <div class="box-tools pull-right">
            <a id="botonNewGuia" name="botonNewGuia" href="#" class="btn btn-block btn-success btn-sm" style="{{isset($data) ? "" : "display:none;" }}">
                <i class="fa fa-fw fa-plus-circle"></i> Seccionar Guia
            </a>
        </div>                    
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="tabla-data" style="font-size:14px">
                    <thead>
                        <tr>
                            <th style="text-align:center;" class="width30">item</th>
                            <th class="width30 tooltipsC" title="Código Producto">CodProd</th>
                            <th class="width30 tooltipsC" title="Guia Despacho">GD</th>
                            <th class="width30" style="text-align:right;">Cant</th>
                            <th class="tooltipsC" title="Unidad de Medida">UniMed</th>
                            <th>Nombre</th>
                            <th style="text-align:right;">Kilos</th>
                            <th style="display:none;">Desc</th>
                            <th style="display:none;">DescPorc</th>
                            <th style="display:none;">DescVal</th>
                            <th style="text-align:right;">PUnit</th>
                            <th style="display:none;">V Kilo</th>
                            <th style="display:none;">Precio X Kilo</th>
                            <th style="display:none;">Precio X Kilo Real</th>
                            <th style="text-align:right;">Sub Total</th>
                            <th style="display:none;">Sub Total</th>
                            <th class="width30" >Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $aux_nfila = 0; $i = 0;
                            $aux_Tsubtotal = 0;
                            $aux_Tcant = 0;
                            $aux_Tkilos = 0;
                            $aux_Tiva = 0;
                        ?>
                        @if (isset($data))
                            @foreach($data->dtedets as $dtedet)
                                <tr name="fila{{$data->id}}" id="fila{{$data->id}}" class="proditems {{$dtedet->dtedet->dte->nrodocto}}">
                                    <td style="text-align:center">
                                        {{$dtedet->nrolindet}}
                                        <input type="text" name="det_id[]" id="det_id{{$dtedet->id}}" class="form-control" value="{{$dtedet->id}}" style="display:none;"/>
                                        <input type="text" name="nrolindet[]" id="nrolindet{{$dtedet->id}}" class="form-control" value="{{$dtedet->nrolindet}}" style="display:none;"/>
                                        <input type="text" name="despachoorddet_id[]" id="despachoorddet_id{{$dtedet->id}}" class="form-control" value="{{$dtedet->id}}" style="display:none;"/>
                                        <input type="text" name="notaventadetalle_id[]" id="notaventadetalle_id{{$dtedet->id}}" class="form-control" value="{{$dtedet->notaventadetalle_id}}" style=display:none;"/>
                                        <input type="text" name="dte_id[]" id="dte_id{{$dtedet->id}}" class="form-control" value="{{$dtedet->dte_id}}" style="display:none;"/>
                                        <input type="text" name="dtedet_id[]" id="dtedet_id{{$dtedet->id}}" class="form-control" value="{{$dtedet->dtedet_id}}" style="display:none;"/>
                                        <input type="text" name="dteorigen_id[]" id="dteorigen_id{{$dtedet->id}}" class="form-control" value="{{$dtedet->dtedet->dte_id}}" style="display:none;"/>
                                        <input type="text" name="obsdet[]" id="obsdet{{$dtedet->id}}" class="form-control" value="{{$dtedet->obsdet}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:center" name="producto_idTD{{$dtedet->id}}" id="producto_idTD{{$dtedet->id}}" >
                                        {{$dtedet->producto_id}}
                                        <input type="text" name="producto_id[]" id="producto_id{{$dtedet->id}}" class="form-control" value="{{$dtedet->producto_id}}'" style="display:none;"/>
                                    </td>
                                    <td name="nrodoctoTD{{$dtedet->id}}" id="nrodoctoTD{{$dtedet->id}}" style="text-align:right">
                                        <a id="nrodocto{{$dtedet->id}}" name="nrodocto{{$dtedet->id}}" class="btn-accion-tabla btn-sm verguiasii" title="Editar valor" data-toggle="tooltip" nomcampo="nrodocto" valor="{{$dtedet->dtedet->dte->nrodocto}}" title="Guia Despacho: {{$dtedet->dtedet->dte->nrodocto}} '" onclick="verGD({{$dtedet->dtedet->dte->nrodocto}})">
                                            {{$dtedet->dtedet->dte->nrodocto}} 
                                        </a>
                                    </td>
                                    <td name="cantTD{{$dtedet->id}}" id="cantTD{{$dtedet->id}}" style="text-align:right" class="subtotalcant" valor="{{$dtedet->qtyitem}}">
                                        {{$dtedet->qtyitem}}
                                        <input type="text" name="cant[]" id="cant{{$dtedet->id}}" class="form-control" value="{{$dtedet->qtyitem}}" style="display:none;"/>
                                        <input type="text" name="qtyitem[]" id="qtyitem{{$dtedet->id}}" class="form-control" value="{{$dtedet->qtyitem}}" style="display:none;"/>
                                    </td>
                                    <td name="unidadmedida_nombre{{$dtedet->id}}" id="unidadmedida_nombre{{$dtedet->id}}" valor="{{$dtedet->unmditem}}">
                                        {{$dtedet->unmditem}}
                                        <input type="text" name="unidadmedida_id[]" id="unidadmedida_id{{$dtedet->id}}" class="form-control" value="{{$dtedet->unidadmedida_id}}" style="display:none;"/>
                                        <input type="text" name="unmditem[]" id="unmditem{{$dtedet->id}}" class="form-control" value="{{$dtedet->unmditem}}" style="display:none;"/>
                                    </td>
                                    <td name="nombreProdTD{{$dtedet->id}}" id="nombreProdTD{{$dtedet->id}}" valor="{{$dtedet->nmbitem}}">
                                        {{$dtedet->nmbitem}}
                                        <input type="text" name="nmbitem[]" id="nmbitem{{$dtedet->id}}" class="form-control" value="{{$dtedet->nmbitem}}" style="display:none;"/>
                                        <input type="text" name="dscitem[]" id="dscitem{{$dtedet->id}}" class="form-control" value="{{$dtedet->dscitem}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;" class="subtotalkg" valor="{{$dtedet->itemkg}}">
                                        {{number_format($dtedet->itemkg, 2, ',', '.')}}
                                        <input type="text" name="totalkilos[]" id="totalkilos{{$dtedet->id}}" class="form-control" value="{{$dtedet->itemkg}}" style="display:none;" valor="{{$dtedet->itemkg}}" fila="{{$dtedet->id}}"/>
                                        <input type="text" name="itemkg[]" id="itemkg{{$dtedet->id}}" class="form-control" value="{{$dtedet->itemkg}}" style="display:none;"/>
                                    </td>
                                    <td name="descuentoTD{{$dtedet->id}}" id="descuentoTD{{$dtedet->id}}" style="text-align:right;display:none;">
                                        '0%'
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="descuento[]" id="descuento{{$dtedet->id}}" class="form-control" value="0" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        <input type="text" name="descuentoval[]" id="descuentoval{{$dtedet->id}}" class="form-control" value="0" style="display:none;"/>
                                    </td>
                                    <td name="preciounitTD{{$dtedet->id}}" id="preciounitTD{{$dtedet->id}}" style="text-align:right;">
                                        {{number_format($dtedet->prcitem, 0, ',', '.')}}
                                        <input type="text" name="preciounit[]" id="preciounit{{$dtedet->id}}" class="form-control" value="{{$dtedet->prcitem}}'" style="display:none;"/>
                                        <input type="text" name="prcitem[]" id="prcitem{{$dtedet->id}}" class="form-control" value="{{$dtedet->prcitem}}" style="display:none;"/>
                                    </td>
                                    <td style="display:none;" name="precioxkiloTD{{$dtedet->id}}" id="precioxkiloTD{{$dtedet->id}}" style="text-align:right">
                                        {{$dtedet->precioxkilo}}
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        <input type="text" name="precioxkilo[]" id="precioxkilo{{$dtedet->id}}" class="form-control" value="{{$dtedet->precioxkilo}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        <input type="text" name="precioxkiloreal[]" id="precioxkiloreal{{$dtedet->id}}" class="form-control" value="{{$dtedet->precioxkiloreal}}'" style="display:none;"/>
                                    </td>
                                    <td name="subtotalCFTD{{$dtedet->id}}" id="subtotalCFTD{{$dtedet->id}}" class="subtotalCFTD" style="text-align:right">
                                        {{number_format($dtedet->montoitem, 0, ',', '.')}}
                                        <input type="text" name="subtotal[]" id="subtotal{{$dtedet->id}}" class="form-control" value="{{$dtedet->montoitem}}" style="display:none;"/>
                                        <input type="text" name="montoitem[]" id="montoitem{{$dtedet->id}}" class="form-control" value="{{$dtedet->montoitem}}" style="display:none;"/>
                                    </td>
                                    <td name="subtotalFactDet{{$aux_nfila}}" id="subtotalFactDet{{$aux_nfila}}" class="subtotal" style="text-align:right;display:none;">
                                        {{$dtedet->montoitem}}
                                    </td>
                                    <td name="accion{{$dtedet->id}}" id="accion{{$dtedet->id}}" style="text-align:center">
                                        <a class="btn-accion-tabla btn-sm tooltipsC" onclick="delguiadespfactdet({{$dtedet->dtedet->dte->nrodocto}},{{$dtedet->id}},{{$dtedet->dtedet->dte->id}})" title="Eliminar Guia {{$dtedet->dtedet->dte->nrodocto}}">
                                            <span class="glyphicon glyphicon-erase" style="bottom: 0px;top: 2px;"></span>
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                                    $aux_Tcant += $dtedet->qtyitem;
                                    $aux_Tkilos += $dtedet->itemkg;
                                    $aux_Tsubtotal += $dtedet->montoitem;
                                ?>                        
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot style="display:none;">
                        <div id="foot">
                            <tr id="trneto" name="trneto">
                                <th colspan="3" style="text-align:right">
                                    <b>Totales:</b>
                                </th>
                                <th id="Tcant" name="Tcant" style="text-align:right">
                                    {{$aux_Tcant}}
                                </th>
                                <th colspan="2" style="text-align:right"><b>Total Kg</b></th>
                                <th id="totalkg" name="totalkg" style="text-align:right" valor="{{$aux_Tkilos}}">{{number_format($aux_Tkilos, 2, ',', '.')}}</th>
                                <th style="text-align:right"><b>Neto</b></th>
                                <th id="tdneto" name="tdneto" style="text-align:right">{{number_format($aux_Tsubtotal, 0, ',', '.')}}</th>
                            </tr>
                            <?php 
                                $aux_Tiva = round(($tablas['empresa']->iva * $aux_Tsubtotal/100));
                                $aux_total = round($aux_Tsubtotal + $aux_Tiva);
                            ?>
                            <tr id="triva" name="triva">
                                <th colspan="8" style="text-align:right"><b>IVA {{$tablas['empresa']->iva}}%</b></th>
                                <th id="tdiva" name="tdiva" style="text-align:right">{{number_format($aux_Tiva, 0, ',', '.')}}</th>
                            </tr>
                            <tr id="trtotal" name="trtotal">
                                <th colspan="8" style="text-align:right"><b>Total</b></th>
                                <th id="tdtotal" name="tdtotal" style="text-align:right">{{number_format($aux_total, 0, ',', '.')}}</th>
                            </tr>
                        </div>
                    </tfoot>

                </table>
            </div>
        </div>
    </div>
</div>

@include('generales.modalpdf')
@include('generales.buscarclientebd')
@include('generales.buscardteguiadesp')

