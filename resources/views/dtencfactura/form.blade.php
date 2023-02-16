<input type="hidden" name="dte_id" id="dte_id" value="{{old('dte_id', $data->dte_id ?? '')}}">
<input type="hidden" name="aux_fechaphp" id="aux_fechaphp">
<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<input type="hidden" name="cliente_id" id="cliente_id" value="{{old('cliente_id', $data->cliente_id ?? '')}}">
<input type='hidden' id="aux_obs" name="aux_obs" value="{{old('aux_obs', $data->obs ?? '')}}">
<input type="hidden" name="aux_iva" id="aux_iva" value="{{old('aux_iva', $tablas['empresa']->iva ?? '')}}">
<input type="hidden" name="updated_at" id="updated_at" value="{{old('updated_at', $data->updated_at ?? '')}}">
<input type="hidden" name="indtraslado" id="indtraslado" value="{{old('indtraslado', $data->indtraslado ?? '')}}">
<input type="hidden" name="dtefoliocontrol_id" id="dtefoliocontrol_id" value="5">
<input type="hidden" name="foliocontrol_id" id="foliocontrol_id" value="{{old('foliocontrol_id', $data->foliocontrol_id ?? '')}}">
<input type="hidden" name="tablaoriginal" id="tablaoriginal" value="">

<?php 
    $aux_rut = "";
    if(isset($data)){
        $aux_rut = number_format( substr ( $data->cliente->rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $data->cliente->rut, strlen($data->cliente->rut) -1 , 1 );
    }
?>

<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="row">
            <div class="form-group col-xs-12 col-sm-1">
                <label for="tdfoliocontrol_id" class="control-label requerido" data-toggle='tooltip' title="Tipo documento">TD</label>
                <select name="tdfoliocontrol_id" id="tdfoliocontrol_id" class="form-control select2" data-live-search='true' required>
                    <option value="" {{'selected'}}>Seleccione...</option>
                    <option value="1">Factura</option>
                    <option value="6">Nota Débito</option>
                </select>
            </div>

            <div class="form-group col-xs-12 col-sm-1">
                <label for="nrodoctoF" class="control-label requerido" data-toggle='tooltip' title="Número Documento (Factura o Nota Crédito">DTE</label>
                <input type="text" name="nrodoctoF" id="nrodoctoF" class="form-control" style="padding-left: 4px;padding-right: 4px;" value="{{old('nrodoctoF', $data->nrodocto ?? '')}}" required readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="rut" class="control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
                <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $data->rut ?? '')}}" readonly disabled/>
                <!--
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
                -->
            </div>
            <div class="form-group col-xs-12 col-sm-3">
                <label for="razonsocial" class="control-label requerido" data-toggle='tooltip' title="Razón Social">Razón Social</label>
                <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', $data->razonsocial ?? '')}}" readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-3">
                <label for="direccion" class="control-label">Dirección Princ</label>
                <input type="text" name="direccion" id="direccion" class="form-control" value="{{old('direccion', $data->direccion ?? '')}}" placeholder="Dirección principal" readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-1">
                <label for="telefono" class="control-label">Telefono</label>
                <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', $data->telefono ?? '')}}" style="padding-left: 4px;padding-right: 4px;" readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-1">
                <label for="comuna_nombre" class="control-label">Comuna</label>
                <input type="text" name="comuna_nombre" id="comuna_nombre" value="{{old('comuna_nombre', $data->cliente->comuna->nombre ?? '')}}" class="form-control" readonly/>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-2">
                <label for="email" class="control-label">Email</label>
                <input type="text" name="email" id="email" class="form-control" value="{{old('email', $data->email ?? '')}}" readonly/>
            </div>
            <!--
            <div class="form-group col-xs-12 col-sm-2">
                <label for="provincia_nombre" class="control-label requerido">Provincia</label>
                <input type="text" name="provincia_nombre" id="provincia_nombre" class="form-control" required readonly/>
            </div>
            -->
            <div class="form-group col-xs-12 col-sm-2">
                <label for="formapago_desc" class="control-label"data-toggle='tooltip' title="Forma de pago Factura">Forma Pago</label>
                <input type="text" name="formapago_desc" id="formapago_desc" class="form-control" value="{{old('formapago_desc', $data->cliente->formapago->descripcion ?? '')}}" readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-1">
                <label for="plazopago" class="control-label" data-toggle='tooltip' title="Plazo pago Factura">Plazo P</label>
                <input type="text" name="plazopago" id="plazopago" class="form-control" value="{{old('plazopago', $data->cliente->plazopago->descripcion ?? '')}}" readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-1">
                <label for="fchemis" class="control-label" data-toggle='tooltip' title="Fecha emisión Factura">F Emision</label>
                <input type="text" name="fchemis" id="fchemis" class="form-control pull-right" value="{{old('fchemis', date("d/m/Y") )}}" style="padding-left: 2px;padding-right: 2px;" readonly>
            </div>

            <div class="form-group col-xs-12 col-sm-1">
                <label for="fchvenc" class="control-label" data-toggle='tooltip' title="Fecha vencimiento Factura">F. Venc</label>
                <input type="text" name="fchvenc" id="fchvenc" class="form-control"value="{{old('fchvenc', isset($data) ? date('d/m/Y', strtotime(date('Y-m-d') ."+ " . $data->cliente->plazopago->dias . " days"))  : "")}}" style="padding-left: 2px;padding-right: 2px;" readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="vendedor_id" class="control-label requerido">Vendedor</label>
                <select name="vendedor_id" id="vendedor_id" class="form-control select2 vendedor_id" data-live-search='true' required disabled>
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
                <select name="centroeconomico_id" id="centroeconomico_id" class="form-control select2 centroeconomico_id" data-live-search='true' required disabled>
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
            <div class="form-group col-xs-12 col-sm-1">
                <label for="hep" class="control-label" data-toggle='tooltip' title="Número de atención o HEP">Hep</label>
                <input type="text" name="hep" id="hep" class="form-control" value="{{old('hep', $data->dtefac->hep ?? '')}}" maxlength="12" readonly/>
            </div>    
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-3">
                <label for="obsfac" class="control-label" data-toggle='tooltip' title="Observación Factura">Observación Fact</label>
                <textarea class="form-control" name="obsfac" id="obsfac" value="{{old('obs', $data->obs ?? '')}}" placeholder="Observación" maxlength="90" readonly></textarea>
            </div>
            <div class="form-group col-xs-12 col-sm-4">
                <label for="codref" class="control-label requerido" data-toggle='tooltip' title="Código de Referencia">Cod Ref</label>
                <select name="codref" id="codref" class="form-control select2 codref" data-live-search='true' required disabled>
                    <option value="" {{'selected'}}>Seleccione...</option>
                    <option value="1">Anula Documento de Referencia</option>
                    <option value="2">Corrige Texto Documento Referencia</option>
                    <option value="3">Corrige montos</option>
                </select>
            </div>
            <div class="form-group col-xs-12 col-sm-5">
                <label for="obs" class="control-label" data-toggle='tooltip' title="Observación Nota de Crédito">Observación NC</label>
                <textarea class="form-control" name="obs" id="obs" value="{{old('obs', $data->obs ?? '')}}" placeholder="Observación" maxlength="90" readonly></textarea>
            </div>
        </div>    
    </div>    
</div>
<div class="form-group col-xs-4 col-sm-4" style="display:none;">
    <label for="total" class="control-label requerido" data-toggle='tooltip' title="Total Documento">Total Documento</label>
    <input type="hidden" name="total" id="total" class="form-control" style="text-align:right;" readonly required>
    <input type="hidden" name="totalini" id="totalini" value="0" valor="0" style="display:none;">
</div>
<div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
    <div class="box-header with-border">
        <h3 class="box-title">Detalle</h3>
        <div class="box-tools pull-right" id="dtencdet" name="dtencdet">
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="tabla-data" style="font-size:14px">
                    <thead>
                        <tr>
                            <th style="text-align:center;" class="width30">item</th>
                            <th class="width30 tooltipsC" title="Código Producto">CodProd</th>
                            <th class="width80" style="text-align:right;">Cant</th>
                            <th class="tooltipsC width100" title="Unidad de Medida">UniMed</th>
                            <th>Nombre</th>
                            <th class="width100" style="text-align:right;">Kilos</th>
                            <th style="display:none;">Desc</th>
                            <th style="display:none;">DescPorc</th>
                            <th style="display:none;">DescVal</th>
                            <th class="width100" style="text-align:right;">PUnit</th>
                            <th style="display:none;">V Kilo</th>
                            <th class="width100" style="text-align:right;">Sub Total</th>
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
                    </tbody>
                    <tfoot style="display:none;" id="foottotal" name="foottotal">
                        <div id="foot">
                            <tr id="trneto" name="trneto">
                                <th colspan="2" style="text-align:right">
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
                                <th colspan="7" style="text-align:right"><b>IVA {{$tablas['empresa']->iva}}%</b></th>
                                <th id="tdiva" name="tdiva" style="text-align:right">{{number_format($aux_Tiva, 0, ',', '.')}}</th>
                            </tr>
                            <tr id="trtotal" name="trtotal">
                                <th colspan="7" style="text-align:right"><b>Total</b></th>
                                <th id="tdtotal" name="tdtotal" style="text-align:right">{{number_format($aux_total, 0, ',', '.')}}</th>
                            </tr>
                            <tr id="trtotalrestante" name="trtotalrestante" style="display:none;">
                                <th colspan="7" style="text-align:right" class="tooltipsC" title="Monto maximo permitido para hacer Nota Credito a Factura"><b>Total Restante Fact</b></th>
                                <th id="tdtotalrestante" name="tdtotalrestante" class="tooltipsC" title="Monto maximo permitido para hacer Nota Credito a Factura" style="text-align:right">{{number_format($aux_total, 0, ',', '.')}}</th>
                            </tr>
                            <tr id="trtotaloriginal" name="trtotaloriginal">
                                <th colspan="7" style="text-align:right" class="tooltipsC" title="Monto neto Inicial"><b>Monto neto Inicial</b></th>
                                <th id="tdtotaloriginal" name="tdtotaloriginal" class="tooltipsC" title="Monto neto Inicial" style="text-align:right"></th>
                            </tr>
                            <tr id="trtotalmodificado" name="trtotalmodificado">
                                <th colspan="7" style="text-align:right" class="tooltipsC" title="Monto Saldo neto"><b>Monto Saldo neto</b></th>
                                <th id="tdtotalmodificado" name="tdtotalmodificado" class="tooltipsC" title="Monto Saldo neto" style="text-align:right"></th>
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