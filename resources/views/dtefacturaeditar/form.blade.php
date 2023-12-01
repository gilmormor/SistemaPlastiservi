<input type="hidden" name="dte_id" id="dte_id" value="{{old('dte_id', $data->id ?? '')}}">
<input type="hidden" name="updatededitar_at" id="updatededitar_at" value="{{old('updatededitar_at', $data->updated_at ?? '')}}">
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
    <div class="col-xs-12 col-sm-12">
        <div class="row">
            <div class="form-group col-xs-12 col-sm-3">
                <label for="rut" class="control-label" data-toggle='tooltip' title="RUT">RUT</label>
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
                <label for="razonsocial" class="control-label" data-toggle='tooltip' title="Razón Social">Razón Social</label>
                <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', $data->cliente->razonsocial ?? '')}}" readonly disabled/>
            </div>
            <div class="form-group col-xs-12 col-sm-3">
                <label for="direccion" class="control-label">Dirección Princ</label>
                <input type="text" name="direccion" id="direccion" class="form-control" value="{{old('direccion', $data->cliente->direccion ?? '')}}" required placeholder="Dirección principal" readonly disabled/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="telefono" class="control-label">Telefono</label>
                <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', $data->cliente->telefono ?? '')}}" required readonly disabled/>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-2">
                <label for="email" class="control-label">Email</label>
                <input type="text" name="email" id="email" class="form-control" value="{{old('email', $data->cliente->email ?? '')}}" required readonly disabled/>
            </div>
            <div class="form-group col-xs-12 col-sm-1">
                <label for="comuna_nombre" class="control-label">Comuna</label>
                <input type="text" name="comuna_nombre" id="comuna_nombre" class="form-control" value="{{old('comuna_nombre', $data->cliente->comuna->nombre ?? '')}}" required readonly disabled/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="provincia_nombre" class="control-label">Provincia</label>
                <input type="text" name="provincia_nombre" id="provincia_nombre" class="form-control" value="{{old('provincia_nombre', $data->cliente->comuna->provincia->nombre ?? '')}}" required readonly disabled/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="formapago_desc" class="control-label">Forma de Pago</label>
                <input type="text" name="formapago_desc" id="formapago_desc" class="form-control" value="{{old('formapago_desc', $data->cliente->formapago->descripcion ?? '')}}" required readonly disabled/>
            </div>
            <div class="form-group col-xs-12 col-sm-1">
                <label for="plazopago" class="control-label" data-toggle='tooltip' title="Plazo de pago">Pl Pago</label>
                <input type="text" name="plazopago" id="plazopago" class="form-control" value="{{old('plazopago', $data->cliente->plazopago->descripcion ?? '')}}" required readonly disabled/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="fchemis" class="control-label">Fecha Emision</label>
                <input type="text" name="fchemis" id="fchemis" class="form-control pull-right datepicker"  value="{{old('fchemis', date("d/m/Y") )}}" readonly required disabled>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="fchvenc" class="control-label">F. Venc</label>
                <input type="text" name="fchvenc" id="fchvenc" class="form-control" value="{{old('fchvenc', isset($data) ? date('d/m/Y', strtotime(date('Y-m-d') ."+ " . $data->cliente->plazopago->dias . " days"))  : "")}}" required readonly disabled/>
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
            <div class="form-group col-xs-12 col-sm-3">
                <label for="hep" class="control-label" data-toggle='tooltip' title="Hoja de Entrada de Servicio CodRef:HES">Hes</label>
                <input type="text" name="hep" id="hep" class="form-control" value="{{old('hep', $data->dtefac->hep ?? '')}}" maxlength="12" readonly disabled/>
            </div>
            <div class="form-group col-xs-12 col-sm-3">
                <label for="notped" class="control-label" data-toggle='tooltip' title="Nota de Pedido CodRef:802">Nota Pedido</label>
                <input type="text" name="notped" id="notped" class="form-control" value="{{old('notped', $data->dtefac->notped ?? '')}}" maxlength="12" readonly disabled/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label id="lblocnv_id" for="ocnv_id" class="control-label" data-toggle='tooltip' title="Orden de compra">OC:
                    @foreach ($tablas['dteoc'] as $dteoc)
                        <a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" onclick="verpdf2('{{$dteoc->oc_folder}}/{{$dteoc->oc_file}}',2)" title="{{$dteoc->oc_id}}">
                            {{$dteoc->oc_id}}
                        </a>
                    @endforeach
                </label>
            </div>
            <div class="form-group col-xs-12 col-sm-5">
                <label for="obs" class="control-label">Observaciones</label>
                <textarea class="form-control" name="obs" id="obs" value="{{old('obs', $data->obs ?? '')}}" placeholder="Observación" maxlength="90" readonly disabled></textarea>
            </div>
        </div>
    </div>
</div>
<div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
    <div class="box-header with-border">
        <h3 class="box-title" id="titulo-detalle">Detalle</h3>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $i = 1;
                        ?>
                        @foreach ($data->dtedets as $dtede)
                            <tr name="fila{{$i}}" id="fila{{$i}}">
                                <td>
                                    {{$i}}
                                </td>
                                <td>
                                    {{$dtede->producto_id}}
                                </td>
                                <td>
                                    {{$dtede->dte->nrodocto}}
                                </td>
                                <td style="text-align:right;">
                                    {{$dtede->qtyitem}}
                                </td>
                                <td>
                                    {{$dtede->unidadmedida->nombre}}
                                </td>
                                <td>
                                    {{$dtede->nmbitem}}
                                </td>
                                <td style="text-align:right;">
                                    {{number_format($dtede->itemkg, 0, ',', '.')}}
                                </td>
                                <td style="display:none;"></td>
                                <td style="display:none;"></td>
                                <td style="display:none;"></td>
                                <td style="text-align:right;">
                                    {{number_format($dtede->prcitem, 0, ',', '.')}}
                                </td>
                                <td style="display:none;"></td>
                                <td style="display:none;"></td>
                                <td style="display:none;"></td>
                                <td style="text-align:right;">
                                    {{number_format($dtede->montoitem, 0, ',', '.')}}
                                </td>
                                <td style="display:none;"></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot id="foottotal" name="foottotal">
                        <div id="foot">
                            <tr id="trneto" name="trneto">
                                <th colspan="3" style="text-align:right">
                                    <b>Totales:</b>
                                </th>
                                <th id="Tcant" name="Tcant" style="text-align:right">
                                    {{number_format($data->kgtotal, 0, ',', '.')}}
                                </th>
                                <th colspan="2" style="text-align:right"><b>Total Kg</b></th>
                                <th id="totalkg" name="totalkg" style="text-align:right" valor="{{$data->kgtotal}}">{{number_format($data->kgtotal, 0, ',', '.')}}</th>
                                <th style="text-align:right"><b>Neto</b></th>
                                <th id="tdneto" name="tdneto" style="text-align:right">{{number_format($data->mntneto, 0, ',', '.')}}</th>
                            </tr>
                            <tr id="triva" name="triva">
                                <th colspan="8" style="text-align:right"><b>IVA {{$tablas['empresa']->iva}}%</b></th>
                                <th id="tdiva" name="tdiva" style="text-align:right">{{number_format($data->iva, 0, ',', '.')}}</th>
                            </tr>
                            <tr id="trtotal" name="trtotal">
                                <th colspan="8" style="text-align:right"><b>Total</b></th>
                                <th id="tdtotal" name="tdtotal" style="text-align:right">{{number_format($data->mnttotal, 0, ',', '.')}}</th>
                            </tr>
                        </div>
                    </tfoot>

                </table>
            </div>
        </div>
    </div>
</div>

@include('generales.modalpdf')
