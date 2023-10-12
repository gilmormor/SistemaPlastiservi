<input type="hidden" name="aux_fechaphp" id="aux_fechaphp" value="{{old('aux_fechaphp', $fecha ?? '')}}">
<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<input type="hidden" name="cliente_id" id="cliente_id" value="{{old('cliente_id', $data->cliente_id ?? '')}}">
<input type='hidden' id="aux_obs" name="aux_obs" value="{{old('aux_obs', $data->obs ?? '')}}">
<input type="hidden" name="aux_iva" id="aux_iva" value="{{old('aux_iva', $tablas['empresa']->iva ?? '')}}">
<input type="hidden" name="dtefoliocontrol_id" id="dtefoliocontrol_id" value="1">
<input type="hidden" name="foliocontrol_id" id="foliocontrol_id" value="1">
<input type="text" name="ids" id="ids" value="0" style="display: none">
<input type="hidden" name="imagen" id="imagen" value="{{old('imagen', $data->oc_file ?? '')}}">
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
                    <input type="text" name="rut" id="rut" class="form-control" value="" readonly disabled/>
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
                <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="" readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-4">
                <label for="giro" class="control-label requerido">Giro</label>
                <input type="text" name="giro" id="giro" class="form-control" value="" required readonly/>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6">
                <label for="direccion" class="control-label">Dirección Princ</label>
                <input type="text" name="direccion" id="direccion" class="form-control" value="" required placeholder="Dirección principal" readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="comuna_nombre" class="control-label requerido">Comuna</label>
                <input type="text" name="comuna_nombre" id="comuna_nombre" class="form-control" value="" required readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="provincia_nombre" class="control-label requerido">Provincia</label>
                <input type="text" name="provincia_nombre" id="provincia_nombre" class="form-control" value="" required readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="telefono" class="control-label requerido">Telefono</label>
                <input type="text" name="telefono" id="telefono" class="form-control" value="" required readonly/>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-4">
                <label for="email" class="control-label requerido">Email</label>
                <input type="text" name="email" id="email" class="form-control" value="" required readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="formapago_desc" class="control-label requerido">Forma de Pago</label>
                <input type="text" name="formapago_desc" id="formapago_desc" class="form-control" value="" required readonly/>
            </div>
            <div class="form-group col-xs-12 col-sm-2">
                <label for="plazopago" class="control-label requerido tooltipsC" title="Plazo Pago">Plazo pago</label>
                <input type="text" name="plazopago" id="plazopago" class="form-control" value="" required readonly/>
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
            <div class="form-group col-xs-12 col-sm-3">
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
    
            <div class="form-group col-xs-12 col-sm-3">
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
                <input type="text" name="hep" id="hep" class="form-control" value="{{old('hep', $data->dtefac->hep ?? '')}}" maxlength="12"/>
            </div>
            <div class="form-group col-xs-12 col-sm-3">
                <label for="notped" class="control-label" data-toggle='tooltip' title="Nota de Pedido CodRef:802">Nota Pedido</label>
                <input type="text" name="notped" id="notped" class="form-control" value="{{old('notped', $data->dtefac->notped ?? '')}}" maxlength="12"/>
            </div>

            <div class="form-group col-xs-12 col-sm-4" style="display:none;">
                <label for="tipodespacho" class="control-label requerido" data-toggle='tooltip' title="Tipo Despacho">Tipo Desp</label>
                <select name="tipodespacho" id="tipodespacho" class="form-control select2  tipodespacho" data-live-search='true' value="{{old('tipodespacho', isset($data) ? $data->tipodespacho : ($data->tipodespacho ?? ''))}}" required>
                    <option 
                        value="1"
                        @if (isset($data) and ($data->tipodespacho=="1"))
                            {{'selected'}}
                        @endif
                        >Despacho por cuenta del receptor del documento (cliente o vendedor en caso de Facturas de compra.)</option>
                    <option 
                        value="2"
                        @if (isset($data))
                            @if (($data->tipodespacho=="2"))
                                {{'selected'}}
                            @endif
                        @else 
                            {{'selected'}}
                        @endif
                        >Despacho por cuenta del emisor a instalaciones del cliente</option>
                    <option 
                        value="3"
                        @if (isset($data) and ($data->tipodespacho=="3"))
                            {{'selected'}}
                        @endif
                        >Despacho por cuenta del emisor a otras instalaciones (Ejemplo: entrega en Obra)</option>
                </select>
            </div>
            <div class="form-group col-xs-12 col-sm-3" style="display:none;">
                <label for="indtraslado" class="control-label requerido">Tipo Traslado</label>
                <select name="indtraslado" id="indtraslado" class="form-control select2  indtraslado" data-live-search='true' value="{{old('indtraslado', isset($data) ? $data->indtraslado : ($data->indtraslado ?? ''))}}" required>
                    <option 
                        value="1" 
                        @if(isset($data)) 
                            @if($data->indtraslado =="1")
                                {{'selected'}}
                            @endif
                        @else
                            {{'selected'}}
                        @endif
                        >Operación constituye venta</option>
                    <option 
                        value="6"
                        @if(isset($data) and $data->indtraslado =="6")
                            {{'selected'}}
                        @endif
                        >Otros traslados no venta</option>
                </select>
            </div>
            <div class="form-group col-xs-12 col-sm-5">
                <label for="obs" class="control-label">Observaciones</label>
                <textarea class="form-control" name="obs" id="obs" value="{{old('obs', $data->obs ?? '')}}" placeholder="Observación" maxlength="90"></textarea>
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
                                <input type="text" name="oc_id" id="oc_id" class="form-control" value="{{old('oc_id', $data->oc_id ?? '')}}" placeholder="Nro Orden de Compra" maxlength="15" {{$enableCamposCot}} {{$aux_inputRequerido}}/>
                            </div>
                        </div>
                        <div id="group_oc_file" class="form-group col-xs-12 col-sm-12">
                            <label id="lboc_oc_file" name="lboc_oc_file" for="oc_file" class="control-label">Adjuntar OC</label>
                            <div class="input-group">
                                <input type="file" name="oc_file" id="oc_file" class="form-control" data-initial-preview='{{isset($data->oc_file) ? Storage::url("imagenes/notaventa/$data->oc_file") : ""}}' accept="*"/>
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
    <input type="hidden" name="total" id="total" value="{{old('total', $data->mnttotal ?? '')}}" class="form-control" style="text-align:right;" readonly required>
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
                            <th class="width70" style="text-align:right;">Cant</th>
                            <th class="width100 tooltipsC" title="Unidad de Medida">UniMed</th>
                            <th>Nombre</th>
                            <th style="text-align:right;"></th>
                            <th style="display:none;">Desc</th>
                            <th style="display:none;">DescPorc</th>
                            <th style="display:none;">DescVal</th>
                            <th class="width100 tooltipsC" title="Precio Unitario" style="text-align:right;">PUnit</th>
                            <th style="display:none;">V Kilo</th>
                            <th style="display:none;">Precio X Kilo</th>
                            <th style="display:none;">Precio X Kilo Real</th>
                            <th class="width150" style="text-align:right;">Sub Total</th>
                            <th style="display:none;">Sub Total</th>
                            <th class="width30" >Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot style="display:none;" id="foottotal" name="foottotal">
                        <div id="foot">
                            <tr id="trneto" name="trneto">
                                <th colspan="2" style="text-align:right">
                                    <b>Totales:</b>
                                </th>
                                <th id="Tcant" name="Tcant" style="text-align:right">
                                    0
                                </th>
                                <th colspan="2" style="text-align:right;display:none;"><b>Total Kg</b></th>
                                <th id="totalkg" name="totalkg" style="text-align:right;display:none;" valor="0">0</th>
                                <th colspan="4" style="text-align:right"><b>Neto</b></th>
                                <th id="tdneto" name="tdneto" style="text-align:right">0</th>
                            </tr>
                            <tr id="triva" name="triva">
                                <th colspan="7" style="text-align:right"><b>IVA {{$tablas['empresa']->iva}}%</b></th>
                                <th id="tdiva" name="tdiva" style="text-align:right">0</th>
                            </tr>
                            <tr id="trtotal" name="trtotal">
                                <th colspan="7" style="text-align:right"><b>Total</b></th>
                                <th id="tdtotal" name="tdtotal" style="text-align:right">0</th>
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
@include('generales.buscarproductobd')
