<?php 
    use App\Models\Producto;
?>
<input type="hidden" name="updated_at" id="updated_at" value="{{old('updated_at', isset($dteguiadesp) ? $dteguiadesp->updated_at : $data->updated_at ?? '')}}">
<input type="hidden" name="notaventa_id" id="notaventa_id" value="{{$data->notaventa_id}}">
<!--<input type="hidden" name="aux_sta" id="aux_sta" value="$aux_sta">-->
<input type="hidden" name="aux_fechaphp" id="aux_fechaphp" value="{{old('aux_fechaphp', $fecha ?? '')}}">
<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<input type='hidden' id="aux_obs" name="aux_obs" value="{{old('aux_obs', isset($dteguiadesp) ? $dteguiadesp->obs : ($data->observacion ?? '') ?? '')}}">
<input type="hidden" id="tipoguiadesp" name="tipoguiadesp" value="{{old('tipoguiadesp', $data->despachosol->tipoguiadesp ?? '')}}"/>


<div class="row">
    <div class="col-xs-12 col-sm-12">
        <div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
            <div class="box-header with-border">
                <div class="box-body">
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-1">
                            <label for="despachoord_id" class="control-label requerido" data-toggle='tooltip' title="Id Orden Despacho">OD</label>
                            <input type="text" name="despachoord_id" id="despachoord_id" class="form-control" value="{{$data->id}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="rut_cliente" class="control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
                            <?php 
                                $aux_rut = $data->notaventa->cliente->rut;
                                $aux_rut = number_format( substr ( $aux_rut, 0 , -1 ) , 0, "", "") . '-' . substr ( $aux_rut, strlen($aux_rut) -1 , 1 )
                            ?>
                            <input type="text" name="rut_cliente" id="rut_cliente" class="form-control" value="{{old('rut_cliente', $aux_rut ?? '')}}" maxlength="12" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-5">
                            <label for="razonsocial" class="control-label requerido" data-toggle='tooltip' title="Razón Social">Razón Social</label>
                            <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', isset($dteguiadesp) ? $dteguiadesp->cliente->razonsocial : $data->notaventa->cliente->razonsocial ?? '')}}" readonly required/>
                            <input type="text" name="rznsocrecep" id="rznsocrecep" class="form-control" value="{{old('rznsocrecep', isset($dteguiadesp) ? $dteguiadesp->cliente->razonsocial : $data->notaventa->cliente->razonsocial ?? '')}}" style="display:none;" readonly/>
                            <input type="text" name="girorecep" id="girorecep" class="form-control" value="{{old('girorecep', isset($dteguiadesp) ? $dteguiadesp->cliente->giro : $data->notaventa->cliente->giro ?? '')}}" style="display:none;" readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="giro" class="control-label requerido" data-toggle='tooltip' title="Giro">Giro</label>
                            <input type="text" name="giro" id="giro" class="form-control" value="{{old('giro', isset($dteguiadesp) ? $dteguiadesp->cliente->giro : $data->notaventa->cliente->giro ?? '')}}" readonly required/>
                        </div>
                    
                        <div class="form-group col-xs-12 col-sm-1">
                            <label for="fechahora" class="control-label">Fecha</label>
                            <input type="text" name="fechahora" id="fechahora" class="form-control" value="{{old('fechahora', isset($dteguiadesp) ? date("d/m/Y", strtotime($dteguiadesp->fechahora)) : date("d/m/Y", strtotime($data->fechahora)) ?? '')}}" style="padding-left: 0px;padding-right: 0px;" required readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-4">
                            <label for="direccion" class="control-label">Dirección Princ</label>
                            <input type="text" name="direccion" id="direccion" class="form-control" value="{{old('direccion', isset($dteguiadesp) ? $dteguiadesp->cliente->direccion : $data->notaventa->cliente->direccion ?? '')}}" required placeholder="Dirección principal" readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="telefono" class="control-label requerido">Telefono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', isset($dteguiadesp) ? $dteguiadesp->dteguiadesp->notaventa->telefono : $data->notaventa->telefono ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="email" class="control-label requerido">Email</label>
                            <input type="text" name="email" id="email" class="form-control" value="{{old('email', isset($dteguiadesp) ? $dteguiadesp->dteguiadesp->notaventa->email : $data->notaventa->email ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="comuna_nombre" class="control-label requerido">Comuna</label>
                            <input type="text" name="comuna_nombre" id="comuna_nombre" class="form-control" value="{{old('comuna_nombre', isset($dteguiadesp) ? $dteguiadesp->dteguiadesp->notaventa->comuna->nombre : $data->notaventa->comuna->nombre ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="provincia_nombre" class="control-label requerido">Provincia</label>
                            <input type="text" name="provincia_nombre" id="provincia_nombre" class="form-control" value="{{old('provincia_nombre', isset($dteguiadesp) ? $dteguiadesp->dteguiadesp->notaventa->comuna->provincia->nombre : $data->notaventa->comuna->provincia->nombre ?? '')}}" required readonly/>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="vendedor_nombre" class="control-label requerido">Vendedor</label>
                            <input type="text" name="vendedor_nombre" id="vendedor_nombre" class="form-control" value="{{old('vendedor_nombre', isset($dteguiadesp) ? $dteguiadesp->dteguiadesp->notaventa->vendedor->persona->nombre . " " . $dteguiadesp->dteguiadesp->notaventa->vendedor->persona->apellido : $data->notaventa->vendedor->persona->nombre . " " . $data->notaventa->vendedor->persona->apellido ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="plazopago_desc" class="control-label requerido">Plazo</label>
                            <input type="text" name="plazopago_desc" id="plazopago_desc" class="form-control" value="{{old('plazopago_desc', isset($dteguiadesp) ? $dteguiadesp->dteguiadesp->notaventa->plazopago->descripcion : $data->notaventa->plazopago->descripcion ?? '')}}" required readonly/>
                        </div>
                    
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="formapago_desc" class="control-label requerido">Forma de Pago</label>
                            <input type="text" name="formapago_desc" id="formapago_desc" class="form-control" value="{{old('formapago_desc', isset($dteguiadesp) ? $dteguiadesp->dteguiadesp->notaventa->formapago->descripcion : $data->notaventa->formapago->descripcion ?? '')}}" required readonly/>
                        </div>
                    
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="giro_nombre" class="control-label requerido">Giro</label>
                            <input type="text" name="giro_nombre" id="giro_nombre" class="form-control" value="{{old('giro_nombre', isset($dteguiadesp) ? $dteguiadesp->dteguiadesp->notaventa->giro->nombre : $data->notaventa->giro->nombre ?? '')}}" required readonly/>
                        </div>
                    
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="tipoentrega_id" class="control-label requerido">Tipo Entrega</label>
                            <input type="text" name="tipoentrega_nombre" id="tipoentrega_nombre" class="form-control" value="{{old('tipoentrega_nombre', isset($dteguiadesp) ? $dteguiadesp->dteguiadesp->tipoentrega->nombre : $data->tipoentrega->nombre ?? '')}}" required readonly/>
                            <input type="text" name="tipoentrega_id" id="tipoentrega_id" class="form-control" value="{{old('tipoentrega_id', isset($dteguiadesp) ? $dteguiadesp->dteguiadesp->tipoentrega_id : ($data->tipoentrega_id ?? ''))}}" required style="display:none;" readonly/>
<!--
                            <select name="tipoentrega_id" id="tipoentrega_id" class="form-control select2 tipoentrega_id" required  disabled>
                                <option value=''>Seleccione...</option>
                                @foreach($tipoentregas as $tipoentrega)
                                    <option
                                        value="{{$tipoentrega->id}}"
                                        @if (($data->tipoentrega_id==$tipoentrega->id))
                                            {{'selected'}}
                                        @endif
                                        >{{$tipoentrega->nombre}}</option>
                                @endforeach
                            </select>
-->
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="plazoentrega" class="control-label requerido">Plazo Ent.</label>
                            <input type="text" name="plazoentrega" id="plazoentrega" class="form-control pull-right"  value="{{old('plazoentrega', $data->plazoentrega ?? '')}}" readonly required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="contacto" class="control-label requerido">Contacto</label>
                            <input type="text" name="contacto" id="contacto" class="form-control" value="{{old('contacto', $data->contacto ?? '')}}" required placeholder="Contacto Entrega" readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="contactotelf" class="control-label requerido">Teléfono</label>
                            <input type="text" name="contactotelf" id="contactotelf" class="form-control" value="{{old('contactotelf', $data->contactotelf ?? '')}}" required placeholder="Teléfono Contacto Entrega" readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-5">
                            <label for="contactoemail" class="control-label requerido">Email</label>
                            <input type="email" name="contactoemail" id="contactoemail" class="form-control" value="{{old('contactoemail', $data->contactoemail ?? '')}}" required placeholder="Email Contacto Entrega" readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="fechaestdesp" class="control-label requerido" data-toggle='tooltip' title="Fecha estimada de Despacho">Fec Est Despacho</label>
                            <input type="text" name="fechaestdesp" id="fechaestdesp" class="form-control pull-right" value="{{old('fechaestdesp', $data->fechaestdesp ?? '')}}" required readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-1">
                            <label for="ot" class="control-label" data-toggle='tooltip' title="Orden de trabajo">OT</label>
                            <input type="text" name="ot" id="ot" class="form-control pull-right numerico" value="{{old('ot', isset($dteguiadesp) ? $dteguiadesp->dteguiadesp->ot : '')}}" maxlength="5"/>
                        </div>

                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="fchemis" class="control-label requerido">Fecha Emision</label>
                            <input type="text" name="fchemis" id="fchemis" class="form-control pull-right datepicker"  value="{{old('fchemis', isset($dteguiadesp) ? date("d/m/Y", strtotime($dteguiadesp->fchemis)) : date("d/m/Y"))}}" readonly required>
                        </div>


                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="centroeconomico_id" class="control-label requerido">Centro Economico</label>
                            <select name="centroeconomico_id" id="centroeconomico_id" class="form-control select2  centroeconomico_id" data-live-search='true' value="{{old('centroeconomico_id', isset($dteguiadesp) ? $dteguiadesp->centroeconomico_id : $data->centroeconomico_id ?? '')}}" required>
                                @foreach($centroeconomicos as $centroeconomico)
                                    <option
                                        value="{{$centroeconomico->id}}"
                                        @if (isset($dteguiadesp)) 
                                            @if ($dteguiadesp->centroeconomico_id==$centroeconomico->id)
                                                {{'selected'}}
                                            @endif
                                        @else
                                            @if ($data->notaventa->sucursal_id==$centroeconomico->sucursal_id)
                                                {{'selected'}}
                                            @endif
                                        @endif
                                        >{{$centroeconomico->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-5">
                            <label for="tipodespacho" class="control-label requerido" data-toggle='tooltip' title="Tipo Despacho">Tipo Desp</label>
                            <select name="tipodespacho" id="tipodespacho" class="form-control select2  tipodespacho" data-live-search='true' value="{{old('tipodespacho', isset($dteguiadesp) ? $dteguiadesp->tipodespacho : ($data->tipodespacho ?? ''))}}" required>
                                <option 
                                    value="1"
                                    @if (isset($dteguiadesp) and ($dteguiadesp->tipodespacho=="1"))
                                        {{'selected'}}
                                    @endif
                                    >Despacho por cuenta del receptor del documento (cliente o vendedor en caso de Facturas de compra.)</option>
                                <option 
                                    value="2"
                                    @if (isset($dteguiadesp))
                                        @if (($dteguiadesp->tipodespacho=="2"))
                                            {{'selected'}}
                                        @endif
                                    @else 
                                        {{'selected'}}
                                    @endif
                                    >Despacho por cuenta del emisor a instalaciones del cliente</option>
                                <option 
                                    value="3"
                                    @if (isset($dteguiadesp) and ($dteguiadesp->tipodespacho=="3"))
                                        {{'selected'}}
                                    @endif
                                    >Despacho por cuenta del emisor a otras instalaciones (Ejemplo: entrega en Obra)</option>
                            </select>
                        </div>
                        <!--
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="indtraslado" class="control-label requerido">Tipo Traslado</label>
                            <select name="indtraslado" id="indtraslado" class="form-control select2  indtraslado" data-live-search='true' value="{{old('indtraslado', isset($dteguiadesp) ? $dteguiadesp->indtraslado : ($data->indtraslado ?? ''))}}" required>
                                <option value="">Seleccione...</option>
                                <option 
                                    value="1" 
                                    @if(isset($dteguiadesp)) 
                                        @if($dteguiadesp->indtraslado =="1")
                                            {{'selected'}}
                                        @endif
                                    @endif
                                    >Operación constituye venta</option>
                                <option 
                                    value="6"
                                    @if(isset($dteguiadesp) and $dteguiadesp->indtraslado =="6")
                                        {{'selected'}}
                                    @endif
                                    >Otros traslados no venta</option>
                            </select>
                        </div>
                        -->
                        <?php 
                            $despachosol = $data->despachosol;
                        ?>
                        @if (count($despachosol->notaventa->dteguiadespnvs) > 0 and isset($despachosol->despachosoldte->dte->nrodocto))
                            <div class="form-group col-xs-12 col-sm-4">
                                <label for="dte_id" class="control-label requerido" data-toggle='tooltip' title="Origen">Origen</label>
                                <a class="btn-accion-tabla btn-sm tooltipsC" title="Ver Guia despacho: {{$despachosol->despachosoldte->dte->nrodocto}}" onclick="genpdfGD('{{$despachosol->despachosoldte->dte->nrodocto}}','')">
                                    {{$despachosol->despachosoldte->dte->nrodocto}}
                                </a>
                                <input type="text" name="dte_id" id="dte_id" class="form-control" value="{{old('dte_id', "Guia Despacho: " . $despachosol->despachosoldte->dte->nrodocto ?? '')}}" required readonly/>    
                            </div>
                        @endif
                        <div class="form-group col-xs-12 col-sm-2">
                            <?php
                                $tipoguiadesp = $data->despachosol->tipoguiadesp;
                                if($tipoguiadesp == "1"){
                                    $aux_tipoguiadesp = "Precio";
                                }
                                if($tipoguiadesp == "6"){
                                    $aux_tipoguiadesp = "Traslado";
                                }
                                if($tipoguiadesp == "20"){
                                    $aux_tipoguiadesp = "Traslado + Precio";
                                }
                                if($tipoguiadesp == "30"){
                                    $aux_tipoguiadesp = "Traslado";
                                }
                            ?>
                            <label for="txttipoguiadesp" class="control-label requerido">Tipo Guia</label>
                            <input type="text" name="txttipoguiadesp" id="txttipoguiadesp" class="form-control" value="{{old('txttipoguiadesp', $aux_tipoguiadesp ?? '')}}" required readonly/>
                        </div>
                        <!--
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="btnfotooc" class="control-label">Cargar OrdenCompra</label>
                            <button type="button" class="form-control btn btn-primary" id="btnfotooc" name="btnfotooc" title="Guardar">Cargar OrdenCompra</button>
                        </div>-->
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="lugarentrega" class="control-label requerido">Lugar de Entrega</label>
                            <input type="text" name="lugarentrega" id="lugarentrega" class="form-control" value="{{old('lugarentrega', isset($dteguiadesp) ? $dteguiadesp->dteguiadesp->lugarentrega : ($data->lugarentrega ?? ''))}}" required placeholder="Lugar de Entrega"/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="comunaentrega_id" class="control-label requerido">Comuna Entrega</label>
                            <select name="comunaentrega_id" id="comunaentrega_id" class="form-control select2  comunaentrega_id" data-live-search='true' value="{{old('comunaentrega_id', $data->comunaentrega_id ?? '')}}" required>
                                <option value="">Seleccione...</option>
                                @foreach($comunas as $comuna)
                                    <option
                                        value="{{$comuna->id}}"
                                        @if (!isset($dteguiadesp) and $comuna->id==$data->comunaentrega_id)
                                            {{'selected'}}
                                        @else 
                                            @if(isset($dteguiadesp) and $comuna->id==$dteguiadesp->dteguiadesp->comunaentrega_id)
                                                {{'selected'}}
                                            @endif
                                        @endif
                                        >{{$comuna->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-4">
                            <label for="obsdespord" class="control-label">Observación Orden Despacho</label>
                            <textarea class="form-control" name="obsdespord" id="obsdespord" value="{{old('obsdespord', isset($dteguiadesp) ? $dteguiadesp->obs : ($data->observacion ?? ''))}}" placeholder="Observación" readonly disabled></textarea>
                        </div>
                        <div class="form-group col-xs-12 col-sm-4">
                            <label for="obs" class="control-label">Observación Guia</label>
                            <textarea class="form-control" name="obs" id="obs" maxlength="90"></textarea>
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
                            <th style="text-align:center;" class="width30">item</th>
                            <th class="width30 tooltipsC" title="Código Producto">CodProd</th>
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
                            <th style="display:none;">Sub Total Neto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $aux_nfila = 0; $i = 0;
                            $aux_Tsubtotal = 0;
                            $aux_Tcantdesp = 0;
                            $aux_Tkilos = 0;
                        ?>
                        <!--DEBO REVISAR SI LA GUIA YA FUE PROCESADA ANTERIORMENTE O SI ESTA ANULADA-->
                        @foreach($detalles as $detalle)
                            <?php 
                                $aux_nfila++;
                                if(isset($dteguiadesp)){

                                }else{

                                }
                                //dd($detalle->dteguiadespdet);
                                if(isset($dteguiadesp)){
                                    $aux_subtotal = $detalle->montoitem;
                                    $aux_Tsubtotal += $aux_subtotal;
                                    $aux_Tcantdesp += $detalle->qtyitem;
                                    
                                    $aux_kilos = $detalle->itemkg;
                                }else{
                                    $NVDet = $detalle->notaventadetalle;
                                    $aux_subtotal = ($NVDet->subtotal/$NVDet->cant) * $detalle->cantdesp;
                                    $aux_Tsubtotal += $aux_subtotal;
                                    $aux_Tcantdesp += $detalle->cantdesp;

                                    $aux_kilos = ($NVDet->totalkilos/$NVDet->cant) * $detalle->cantdesp;
                                }
                                $aux_Tkilos += $aux_kilos;
                                $producto_id = isset($dteguiadesp) ? $detalle->producto_id : $NVDet->producto_id;
                                $producto = Producto::findOrFail($producto_id);
                                if(isset($dteguiadesp)){
                                    //esto es para reemplazar el caracter comilla doble " de la cadena, para evitar que me trunque los valores en javascript al asignar a attr val 
                                    $detalle->nmbitem = str_replace('"',"'",$detalle->nmbitem) ;
                                    $aux_nombreprod = $detalle->nmbitem;
                                }else{
                                    $aux_nombreprod = $NVDet->producto->nombre;
                                    if(isset($producto->acuerdotecnico)){
                                        $at_ancho = $producto->acuerdotecnico->at_ancho;
                                        $at_largo = $producto->acuerdotecnico->at_largo;
                                        $at_espesor = $producto->acuerdotecnico->at_espesor;
                                        $at_ancho = empty($at_ancho) ? "0.00" : $at_ancho;
                                        $at_largo = empty($at_largo) ? "0.00" : $at_largo;
                                        $at_espesor = empty($at_espesor) ? "0.00" : $at_espesor;
                                        //$aux_nombreprod = $aux_nombreprod . " " . $at_ancho . "x" . $at_largo . "x" . $at_espesor;

                                        $AcuTec = $producto->acuerdotecnico;
                                        $aux_formatofilm = $AcuTec->at_formatofilm > 0 ? " " . number_format($AcuTec->at_formatofilm, 2, ',', '.') . "Kg." : "";
                                        $aux_color =  empty($AcuTec->color->descripcion) ? "" : " " . $AcuTec->color->descripcion . " ";
                                        $aux_at_complementonomprod = empty($AcuTec->at_complementonomprod) ? "" : $AcuTec->at_complementonomprod . " ";
                                        $aux_atribAcuTec = $AcuTec->materiaprima->descfact . $aux_color . $aux_at_complementonomprod . $aux_formatofilm;
                                        //CONCATENAR TODO LOS CAMPOS NECESARIOS PARA QUE SE FORME EL NOMBRE DEL RODUCTO EN LA GUIA
                                        $aux_nombreprod = nl2br($producto->categoriaprod->nombre . " " . $aux_atribAcuTec . " " . $at_ancho . "x" . $at_largo . "x" . number_format($AcuTec->at_espesor, 3, ',', '.'));
                                    }else{
                                        //CUANDO LA CLASE TRAE N/A=NO APLICA CAMBIO ESTO POR EMPTY ""
                                        $aux_cla_nombre =str_replace("N/A","",$producto->claseprod->cla_descripcion);
                                        $aux_diametro = $producto->diametro > 0 ? " D:" . $producto->diametro : "";
                                        $aux_long = $producto->long ? " L:" . $producto->long : "";
                                        $aux_tipounion = "";
                                        if(!($producto->tipounion === "S/C" or $producto->tipounion === "S/U")){
                                            $aux_tipounion = $producto->tipounion;
                                        }                                        
                                        $aux_nombreprod = $aux_nombreprod . $aux_diametro . $aux_long . " " . $aux_cla_nombre. " " . $aux_tipounion;
                                    }
                                }
                                //esto es para reemplazar el caracter comilla doble " de la cadena, para evitar que me trunque los valores en javascript al asignar a attr val 
                                $aux_nombreprod = str_replace('"',"'",$aux_nombreprod);
                                //dd($detalle->dteguiadespdet);
                            ?>
                            <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}" class="proditems">
                                <td style='text-align:center'>
                                    {{isset($dteguiadesp) ? $detalle->nrolindet : ($i + 1)}}
                                    <input type="text" name="nrolindet[]" id="nrolindet{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? $detalle->nrolindet : ($i + 1)}}" style="display:none;"/>
                                    <input type="text" name="despachoorddet_id[]" id="despachoorddet_id{{$aux_nfila}}" class="form-control" value="{{$detalle->id}}" style="display:none;"/>
                                    <input type="text" name="notaventadetalle_id[]" id="notaventadetalle_id{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? $detalle->notaventadetalle_id : $NVDet->id}}" style="display:none;"/>
                                    <input type="text" name="iddet[]" id="iddet{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? $detalle->id : ''}}" style="display:none;"/>
                                    <input type="text" name="obsdet[]" id="obsdet{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? $detalle->obs : ''}}" style="display:none;"/>
                                    
                                </td>
                                <td style='text-align:center' name="producto_idTD{{$aux_nfila}}" id="producto_idTD{{$aux_nfila}}">
                                    {{isset($dteguiadesp) ? $detalle->producto_id : $NVDet->producto_id}}
                                    <input type="text" name="producto_id[]" id="producto_id{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? $detalle->producto_id : $NVDet->producto_id}}" style="display:none;"/>
                                </td>
                                <td name="cantTD{{$aux_nfila}}" id="cantTD{{$aux_nfila}}" style="text-align:right">
                                    <a id="qtyitemlbl{{$aux_nfila}}" name="qtyitemlbl{{$aux_nfila}}" class="btn-accion-tabla btn-sm editarcampoNum" title="Editar valor" data-toggle="tooltip" valor="{{isset($dteguiadesp) ? $detalle->qtyitem : $detalle->cantdesp}}"  fila="{{$aux_nfila}}" nomcampo="qtyitemlbl">
                                        {{isset($dteguiadesp) ? $detalle->qtyitem : $detalle->cantdesp}}
                                    </a>
                                    <input type="text" name="cant[]" id="cant{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? $detalle->qtyitem : $detalle->cantdesp}}" style="display:none;"/>
                                    <input type="text" name="qtyitem[]" id="qtyitem{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? $detalle->qtyitem : $detalle->cantdesp}}" style="display:none;"/>
                                </td>
                                <td name="unidadmedida_nombre{{$aux_nfila}}" id="unidadmedida_nombre{{$aux_nfila}}">
                                    <a id="unmditemlbl{{$aux_nfila}}" name="unmditemlbl{{$aux_nfila}}" class="btn-accion-tabla btn-sm editarcampoTex" title="Editar Unidad Medida" data-toggle="tooltip" valor="{{isset($dteguiadesp) ? $detalle->unmditem : $NVDet->unidadmedida->nombre}}"  fila="{{$aux_nfila}}" tipocampo="texto" nomcampo="unmditemlbl">
                                        {{isset($dteguiadesp) ? $detalle->unmditem : $NVDet->unidadmedida->nombre}}
                                    </a>
                                    <input type="text" name="unidadmedida_id[]" id="unidadmedida_id{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? $detalle->unidadmedida_id : $NVDet->unidadmedida_id}}" style="display:none;"/>
                                    <input type="text" name="unmditem[]" id="unmditem{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? $detalle->unmditem : $NVDet->unidadmedida->nombre}}" style="display:none;"/>
                                </td>
                                <td name="nombreProdTD{{$aux_nfila}}" id="nombreProdTD{{$aux_nfila}}">
                                    <a id="producto_nombre{{$aux_nfila}}" name="producto_nombre{{$aux_nfila}}" class="btn-accion-tabla btn-sm editarcampoTex" title="Editar Nombre Producto" data-toggle="tooltip" valor="{{isset($dteguiadesp) ? $detalle->nmbitem : $aux_nombreprod}}"  fila="{{$aux_nfila}}" tipocampo="texto" nomcampo="producto_nombre">
                                        {{isset($dteguiadesp) ? $detalle->nmbitem : $aux_nombreprod}}
                                    </a>
                                    <input type="text" name="nmbitem[]" id="nmbitem{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? $detalle->nmbitem : $aux_nombreprod}}" style="display:none;"/>
                                    <input type="text" name="dscitem[]" id="dscitem{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? $detalle->dscitem : ''}}" style="display:none;"/>
                                </td>
                                <td style="text-align:right;"> 
                                    <a id="aux_kilos{{$aux_nfila}}" name="aux_kilos{{$aux_nfila}}" class="btn-accion-tabla btn-sm editarcampoNum" title="Editar Kilos" data-toggle="tooltip" valor={{isset($dteguiadesp) ? $detalle->itemkg : $aux_kilos}} fila="{{$aux_nfila}}" tipocampo="numerico" nomcampo="aux_kilos">
                                        {{number_format(isset($dteguiadesp) ? $detalle->itemkg : $aux_kilos, 2, ',', '.')}}
                                    </a>
                                    <input type="text" name="totalkilos[]" id="totalkilos{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? $detalle->itemkg : $aux_kilos}}" style="display:none;" valor={{isset($dteguiadesp) ? $detalle->itemkg : $aux_kilos}} fila="{{$aux_nfila}}"/>
                                    <input type="text" name="itemkg[]" id="itemkg{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? $detalle->itemkg : $aux_kilos}}" style="display:none;"/>
                                </td>
                                <td name="descuentoTD{{$aux_nfila}}" id="descuentoTD{{$aux_nfila}}" style="text-align:right;display:none;">
                                    <?php $aux_descPorc = isset($dteguiadesp) ? 0 : $NVDet->descuento * 100; ?>
                                    {{$aux_descPorc}}%
                                </td>
                                <td style="text-align:right;display:none;"> 
                                    <input type="text" name="descuento[]" id="descuento{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? 0 : $NVDet->descuento}}" style="display:none;"/>
                                </td>
                                <td style="text-align:right;display:none;">
                                    <?php $aux_descVal = 1 - (isset($dteguiadesp) ? 0 : $NVDet->descuento); ?>
                                    <input type="text" name="descuentoval[]" id="descuentoval{{$aux_nfila}}" class="form-control" value="{{$aux_descVal}}" style="display:none;"/>
                                </td>
                                <td name="preciounitTD{{$aux_nfila}}" id="preciounitTD{{$aux_nfila}}" style="text-align:right;"> 
                                    {{number_format(isset($dteguiadesp) ? $detalle->prcitem : $NVDet->preciounit, 0, ',', '.')}}
                                    <input type="text" name="preciounit[]" id="preciounit{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? $detalle->prcitem : $NVDet->preciounit}}" style="display:none;"/>
                                    <input type="text" name="prcitem[]" id="prcitem{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? $detalle->prcitem : $NVDet->preciounit}}" style="display:none;"/>
                                </td>
                                <td style="display:none;" name="precioxkiloTD{{$aux_nfila}}" id="precioxkiloTD{{$aux_nfila}}" style="text-align:right"> 
                                    {{number_format(isset($dteguiadesp) ? 0 : $NVDet->precioxkilo, 0, ',', '.')}}                                    
                                </td>
                                <td style="text-align:right;display:none;"> 
                                    <input type="text" name="precioxkilo[]" id="precioxkilo{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? 0 : $NVDet->precioxkilo}}" style="display:none;"/>
                                </td>
                                <td style="text-align:right;display:none;"> 
                                    <input type="text" name="precioxkiloreal[]" id="precioxkiloreal{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? 0 : $NVDet->precioxkiloreal}}" style="display:none;"/>
                                </td>
                                <td name="subtotalCFTD{{$aux_nfila}}" id="subtotalCFTD{{$aux_nfila}}" class="subtotalCFTD" style="text-align:right"> 
                                    {{number_format(isset($dteguiadesp) ? $detalle->montoitem : $aux_subtotal, 0, ',', '.')}}
                                    <input type="text" name="subtotal[]" id="subtotal{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? $detalle->montoitem : $aux_subtotal}}" style="display:none;"/>
                                    <input type="text" name="montoitem[]" id="montoitem{{$aux_nfila}}" class="form-control" value="{{isset($dteguiadesp) ? $detalle->montoitem : $aux_subtotal}}" style="display:none;"/>
                                </td>
                            </tr>
                            <?php $i++;
                            ?>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr id="trneto" name="trneto">
                            <th colspan="2" style="text-align:right">
                                <b>Totales:</b>
                            </th>
                            <th style="text-align:right">
                                {{$aux_Tcantdesp}}
                            </th>
                            <th colspan="2" style="text-align:right"><b>Total Kg</b></th>
                            <th id="totalkg" name="totalkg" style="text-align:right" valor="{{$aux_Tkilos}}">{{number_format($aux_Tkilos, 2, ',', '.')}}</th>
                            <th style="text-align:right"><b>Neto</b></th>
                            <th id="tdneto" name="tdneto" style="text-align:right">{{number_format($aux_Tsubtotal, 0, ',', '.')}}</th>
                        </tr>
                        <?php 
                            $aux_Tiva = round(($empresa->iva * $aux_Tsubtotal/100));
                            $aux_total = round($aux_Tsubtotal + $aux_Tiva);
                        ?>
                        <tr id="triva" name="triva">
                            <th colspan="7" style="text-align:right"><b>IVA {{$empresa->iva}}%</b></th>
                            <th id="tdiva" name="tdiva" style="text-align:right">{{number_format($aux_Tiva, 0, ',', '.')}}</th>
                        </tr>
                        <tr id="trtotal" name="trtotal">
                            <th colspan="7" style="text-align:right"><b>Total</b></th>
                            <th id="tdtotal" name="tdtotal" style="text-align:right">{{number_format($aux_total, 0, ',', '.')}}</th>
                        </tr>

                        
                    </tfoot>

                </table>
            </div>
        </div>
    </div>
</div>
@include('generales.modalpdf')
@include('generales.editarcamponum')
@include('generales.editarcampotex')