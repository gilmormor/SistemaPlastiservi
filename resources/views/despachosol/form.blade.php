<?php
    use Illuminate\Http\Request;
    use App\Models\InvBodegaProducto;
?>
<input type="hidden" name="updated_at" id="updated_at" value="{{old('updated_at', $data->updated_at ?? '')}}">
<input type="hidden" name="notaventa_id" id="notaventa_id" value="{{$data->id}}">
<input type="hidden" name="aux_sta" id="aux_sta" value="{{$aux_sta}}">
<input type="hidden" name="aux_fechaphp" id="aux_fechaphp" value="{{old('aux_fechaphp', $fecha ?? '')}}">
<input type="hidden" name="aux_iva" id="aux_iva" value="{{$empresa->iva}}">
<input type="hidden" name="direccioncot" id="direccioncot" value="{{old('direccioncot', $data->direccioncot ?? '')}}">
<input type="hidden" name="cliente_id" id="cliente_id" value="{{old('cliente_id', $data->cliente_id ?? '')}}">
<input type="hidden" name="comuna_id" id="comuna_id" value="{{old('comuna_id', $data->comuna_id ?? '')}}">
<input type="hidden" name="formapago_id" id="formapago_id" value="{{old('formapago_id', $data->formapago_id ?? '')}}">
<input type="hidden" name="plazopago_id" id="plazopago_id" value="{{old('plazopago_id', $data->plazopago_id ?? '')}}">
<input type="hidden" name="giro_id" id="giro_id" value="{{old('giro_id', $data->giro_id ?? '')}}">
<input type="hidden" name="despsolsucursal_id" id="despsolsucursal_id" value="{{old('despsolsucursal_id', $data->sucursal_id ? $data->sucursal_id : $data->notaventa->sucursal_id)}}">


<input type="hidden" name="vendedor_id" id="vendedor_id" value="{{old('vendedor_id', $data->vendedor_id ? $data->vendedor_id : $data->notaventa->vendedor_id)}}">
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
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-1">
                            <label for="notaventa_id" class="control-label requerido" data-toggle='tooltip' title="Id Nota Venta">NotVenta</label>
                            <input type="text" name="notaventa_id" id="notaventa_id" class="form-control" value="{{$data->id}}" required disabled/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="rut" class="control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
                            <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $clienteselec[0]->rut ?? '')}}" maxlength="12" required {{$disabledReadOnly}} {{$disabledcliente}}/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6">
                            <label for="razonsocial" class="control-label requerido" data-toggle='tooltip' title="Razón Social">Razón Social</label>
                            <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', $clienteselec[0]->razonsocial ?? '')}}" readonly/>
                        </div>
            
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="fechahora" class="control-label">Fecha</label>
                            <input type="text" name="fechahora" id="fechahora" class="form-control" value="{{old('fechahora', $fecha ?? '')}}" style="padding-left: 0px;padding-right: 0px;" required readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-5">
                            <label for="direccion" class="control-label">Dirección Princ</label>
                            <input type="text" name="direccion" id="direccion" class="form-control" value="{{old('direccion', $clienteselec[0]->direccion ?? '')}}" required placeholder="Dirección principal" readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="telefono" class="control-label requerido">Telefono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', $data->telefono ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3">
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
                                        @if (($aux_sta==2 or $aux_sta==3) and $comuna->id==$data->comuna_id)
                                            {{'selected'}}
                                        @endif
                                        >
                                        {{$comuna->nombre}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
            
                    </div>
            
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="vendedor_idD" class="control-label requerido">Vendedor</label>
                            <select name="vendedor_idD" id="vendedor_idD" class="form-control select2 vendedor_idD" required readonly disabled>
                                <option value="">Seleccione...</option>
                                @foreach($vendedores1 as $vendedor)
                                    <option
                                        value="{{$vendedor->id}}"
                                        @if (($aux_sta==1) and ($vendedor_id==$vendedor->id))
                                            {{'selected'}}
                                        @endif
                                        @if (($aux_sta==2 or $aux_sta==3) and ($data->vendedor_id==$vendedor->id))
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
                                        @if (($aux_sta==2 or $aux_sta==3) and ($data->plazopago_id==$plazopago->id))
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
                                        @if (($aux_sta==2 or $aux_sta==3) and ($data->formapago_id==$formapago->id))
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
                                        @if (($aux_sta==2 or $aux_sta==3) and ($data->giro_id==$giro->id))
                                            {{'selected'}}
                                        @endif
                                        >
                                        {{$giro->nombre}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
            
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="tipoentrega_id" class="control-label requerido">Tipo Entrega</label>
                            <select name="tipoentrega_id" id="tipoentrega_id" class="form-control select2 tipoentrega_id" required {{$enableCamposCot}}>
                                <option value=''>Seleccione...</option>
                                @foreach($tipoentregas as $tipoentrega)
                                    <option
                                        value="{{$tipoentrega->id}}"
                                        @if (($aux_sta==2 or $aux_sta==3) and ($data->tipoentrega_id==$tipoentrega->id))
                                            {{'selected'}}
                                        @endif
                                        >
                                        {{$tipoentrega->nombre}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="plazoentrega" class="control-label requerido">Plazo Ent.</label>
                            <input type="text" name="plazoentrega" id="plazoentrega" class="form-control pull-right"  value="{{old('plazoentrega', $data->plazoentrega ?? '')}}" readonly required {{$enableCamposCot}}>
                        </div>
                        
            
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="lugarentrega" class="control-label requerido">Lugar de Entrega</label>
                            <input type="text" name="lugarentrega" id="lugarentrega" class="form-control" value="{{old('lugarentrega', $data->lugarentrega ?? '')}}" required placeholder="Lugar de Entrega" {{$enableCamposCot}}/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="comunaentrega_id" class="control-label requerido">Comuna Entrega</label>
                            <select name="comunaentrega_id" id="comunaentrega_id" class="form-control select2  comunaentrega_id" data-live-search='true' value="{{old('comunaentrega_id', $data->comunaentrega_id ?? '')}}" required {{$enableCamposCot}}>
                                <option value="">Seleccione...</option>
                                @foreach($comunas as $comuna)
                                    <option
                                        value="{{$comuna->id}}"
                                        @if (($aux_sta==2 or $aux_sta==3) and $comuna->id==$data->comunaentrega_id)
                                            {{'selected'}}
                                        @endif
                                        >
                                        {{$comuna->nombre}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!--
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="btnfotooc" class="control-label">Cargar OrdenCompra</label>
                            <button type="button" class="form-control btn btn-primary" id="btnfotooc" name="btnfotooc" title="Guardar">Cargar OrdenCompra</button>
                        </div>-->
                        
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="contacto" class="control-label requerido">Contacto</label>
                            <input type="text" name="contacto" id="contacto" class="form-control" value="{{old('contacto', $data->contacto ?? '')}}" required placeholder="Contacto Entrega" {{$enableCamposCot}}/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="contactotelf" class="control-label requerido">Teléfono</label>
                            <input type="text" name="contactotelf" id="contactotelf" class="form-control" value="{{old('contactotelf', $data->contactotelf ?? '')}}" required placeholder="Teléfono Contacto Entrega" {{$enableCamposCot}}/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-4">
                            <label for="contactoemail" class="control-label requerido">Email</label>
                            <input type="email" name="contactoemail" id="contactoemail" class="form-control" value="{{old('contactoemail', $data->contactoemail ?? '')}}" required placeholder="Email Contacto Entrega" {{$enableCamposCot}}/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-4">
                            <label for="observacion" class="control-label">Observaciones</label>
                            <input type="text" name="observacion" id="observacion" class="form-control" value="{{old('observacion', $data->observacion ?? '')}}" placeholder="Observaciones" {{$enableCamposCot}}/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="fechaestdesp" class="control-label requerido" data-toggle='tooltip' title="Fecha estimada de Despacho">Fec Est Despacho</label>
                            <input type="text" name="fechaestdesp" id="fechaestdesp" class="form-control pull-right" value="{{old('fechaestdesp', $data->fechaestdesp ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label id="lblsucursal_id" name="lblsucursal_id" for="sucursal_id" class="control-label requerido" data-toggle='tooltip' title="Sucursal de despacho">Sucursal Despacho</label>
                            <select name="sucursal_id" id="sucursal_id" class="form-control select2 sucursal_id" data-live-search='true' required>
                                <option value=''>Seleccione...</option>
                                    @foreach($tablas['sucursales'] as $sucursal)
                                        <option
                                            value="{{$sucursal->id}}"
                                            >{{$sucursal->nombre}}</option>
                                    @endforeach                    
                            </select>
                        </div>
                        @if (count($data->dteguiadespnvs) > 0)
                            <div class="form-group col-xs-12 col-sm-4">
                                <label for="dte_id" class="control-label requerido" data-toggle='tooltip' title="Origen Solicitud Desp">Origen Solicitud Desp</label>
                                <?php 
                                    $j = 0;
                                ?>
                                @foreach($data->dteguiadespnvs as $dteguiadespnv)
                                    <?php 
                                        $j++;
                                    ?>
                                    <a class="btn-accion-tabla btn-sm tooltipsC" title="Ver Guia despacho: {{$dteguiadespnv->dte->nrodocto}}" onclick="genpdfFACDin('{{$dteguiadespnv->dte->id}}',0)">
                                        {{$dteguiadespnv->dte->nrodocto}}
                                        @if ($j < count($data->dteguiadespnvs))
                                            ,
                                        @endif
                                    </a>
                                @endforeach
                                <select name="dte_id" id="dte_id" class="form-control select2  dte_id" required>
                                    <option value="">Seleccione...</option>
                                    <option value="1">Nota de Venta</option>
                                    @foreach($data->dteguiadespnvs as $dteguiadespnv)
                                        <option 
                                            value="{{$dteguiadespnv->dte_id}}" 
                                            >Guia Despacho: {{$dteguiadespnv->dte->nrodocto}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="tipoguiadesp" class="control-label requerido" data-toggle='tooltip' title="Tipo Guia Despacho">Tipo Guia Despacho</label>
                            <select name="tipoguiadesp" id="tipoguiadesp" class="form-control select2  tipoguiadesp" data-live-search='true' value="{{old('tipoguiadesp', isset($data) ? $data->tipoguiadesp : '')}}" required>
                                <option value="">Seleccione...</option>
                                @if (count($data->dteguiadespnvs) == 0)
                                    <!--<option EN COMENTARIO DESDE EL 30/04/2024, PARA EVITAR ERRORES, LAS GUIAS ORIGINADAS DESDE SOLDESP DEBEN SER 
                                        value="30"
                                        @if(isset($data) and $data->tipoguiadesp =="30")
                                            {{'selected'}}
                                        @endif
                                        >Traslado</option>-->
                                    <option
                                        value="1" 
                                        @if(isset($data) and $data->tipoguiadesp =="1")
                                            {{'selected'}}
                                        @endif
                                        >Precio</option>
                                    @if (false)
                                        <option {{count($data->dteguiadespnvs) == 0 ? "disabled='disabled'" : ""}}
                                            value="6"
                                            @if(isset($data) and $data->tipoguiadesp =="6")
                                                {{'selected'}}
                                            @endif
                                        >Traslado</option>
                                    @endif
                                    <option
                                        value="20"
                                        @if(isset($data) and $data->tipoguiadesp =="20")
                                            {{'selected'}}
                                        @endif
                                        >Traslado + Precio</option>
                                @endif
                            </select>
                        </div>
                    </div>
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
                                <input type="text" name="oc_id" id="oc_id" class="form-control validar-texto-xml" value="{{old('oc_id', $data->oc_id ?? '')}}" placeholder="Nro Orden de Compra" {{$enableCamposCot}}/>
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
                            <th style="display:none;" class="width30">ID</th>
                            <th style="display:none;">NotaVentaDetalle_ID</th>
                            <th style="display:none;">cotizacion_ID</th>
                            <th class="tooltipsC" title="Código Producto">Cod</th>
                            <th style="display:none;">CódInterno</th>
                            <th style="display:none;">Cant</th>
                            <th>Cant</th>
                            <th>Desp</th>
                            <th>Solid</th>
                            <th>Saldo</th>
                            <th class='tooltipsC' title='Marcar todo' style="text-align:center;display:none;">
                                <div class='checkbox'>
                                    <label style='font-size: 1.2em'>
                                        <input type='checkbox' id='marcarTodo' name='marcarTodo' onclick='visto($data->id,$i)'>
                                        <span class='cr'><i class='cr-icon fa fa-check'></i></span>
                                    </label>
                                </div>
                            </th>
                            <th class="width90">SolicDesp</th>
                            <th class="width200">Bodegas/Stock</th>
                            <th style="display:none;">UnidadMedida</th>
                            <th>Nombre</th>
                            <th style="display:none;">Clase<br>Sello</th>
                            <th style="display:none;">Diam<br>Ancho</th>
                            <th style="display:none;">Diametro</th>
                            <th style="display:none;">Largo</th>
                            <th style="display:none;">Largo</th>
                            <th style="display:none;">Esp</th>
                            <th style="display:none;">Espesor</th>
                            <th>Peso</th>
                            <th style="display:none;">Peso</th>
                            <th>Kilos</th>
                            <th style="display:none;">Total Kilos</th>
                            <th style="text-align:center;display:none;">TU</th>
                            <th style="text-align:right" class='tooltipsC' title='Precio por Kilo'>PxK</th>
                            <th style="display:none;">TUnion</th>
                            <th style="display:none;">Desc</th>
                            <th style="display:none;">DescPorc</th>
                            <th style="display:none;">DescVal</th>
                            <th style="text-align:right">PUnit</th>
                            <th style="display:none;">Precio Neto Unit</th>
                            <th style="display:none;">V Kilo</th>
                            <th style="display:none;">Precio X Kilo</th>
                            <th style="display:none;">Precio X Kilo Real</th>
                            <th style="text-align:right">Sub Total</th>
                            <th style="display:none;">Sub Total Neto</th>
                            <th style="display:none;">Sub Total Neto Sin Formato</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($aux_sta==2 or $aux_sta==3)
                            <?php $aux_nfila = 0; $i = 0;?>
                            @foreach($detalles as $detalle)
                                <?php
                                    $aux_cant = $detalle->cant;
                                    $notaventadetalleext = null;
                                    if($detalle->notaventadetalleext){
                                        $notaventadetalleext = $detalle->notaventadetalleext;
                                        $aux_cant = $detalle->cant + $notaventadetalleext->cantext;
                                    }

                                    /*************************/
                                    //SUMA TOTAL SOLICITADO
                                    /*************************/
                                    $sql = "SELECT cantsoldesp
                                            FROM vista_sumsoldespdet
                                            WHERE notaventadetalle_id=$detalle->id";
                                    $datasuma = DB::select($sql);
                                    if(empty($datasuma)){
                                        $sumacantsoldesp= 0;
                                    }else{
                                        $sumacantsoldesp= $datasuma[0]->cantsoldesp;
                                    }
                                    /*************************/
                                    //SUMA TOTAL DESPACHADO
                                    /*************************/
                                    $sql = "SELECT cantdesp
                                        FROM vista_sumorddespxnvdetid
                                        WHERE notaventadetalle_id=$detalle->id";
                                    $datasumadesp = DB::select($sql);
                                    if(empty($datasumadesp)){
                                        $sumacantdesp= 0;
                                    }else{
                                        $sumacantdesp= $datasumadesp[0]->cantdesp;
                                    }
                                    /*************************/
                                    $peso = round($detalle->totalkilos/$aux_cant,3);

                                    $aux_ancho = $detalle->producto->diametro;
                                    $aux_espesor = $detalle->espesor;
                                    $aux_largo = $detalle->producto->long . "Mts";
                                    $aux_cla_sello_nombre = isset($detalle->producto->claseprod->cla_nombre) ? $detalle->producto->claseprod->cla_nombre : "";
                                    $aux_categoria_nombre = $detalle->producto->categoriaprod->nombre;
                                    $aux_atribAcuTec = "";
                                    $aux_staAT = false;
                                    if ($detalle->producto->acuerdotecnico != null){
                                        $AcuTec = $detalle->producto->acuerdotecnico;
                                        $aux_ancho = $AcuTec->at_ancho . " " . ($AcuTec->at_ancho ? $AcuTec->anchounidadmedida->nombre : "");
                                        $aux_largo = $AcuTec->at_largo . " " . ($AcuTec->at_largo ? $AcuTec->largounidadmedida->nombre : "");
                                        $aux_espesor = number_format($AcuTec->at_espesor, 3, ',', '.');
                                        $aux_cla_sello_nombre = $AcuTec->claseprod->cla_nombre;
                                        $aux_atribAcuTec = $AcuTec->color->nombre . " " . $AcuTec->materiaprima->nombre . " " . $AcuTec->at_impresoobs;
                                        $aux_staAT = true;
                                    }
                                    $aux_producto_nombre = $detalle->producto->glosa;
                                    if($aux_cant > $sumacantsoldesp){
                                        $aux_nfila++;
                                        $aux_saldo = $aux_cant - $sumacantsoldesp;
                                        foreach ($detalle->producto->categoriaprod->invbodegas as $invbodega) {
                                            InvBodegaProducto::firstOrCreate(
                                                ['producto_id' => $detalle->producto_id, 'invbodega_id' => $invbodega->id],
                                                [   'producto_id' => $detalle->producto_id, 
                                                    'invbodega_id' => $invbodega->id
                                                ]
                                            );
                                        }
                                        $invbodegaproductos = $detalle->producto->invbodegaproductos;
                                        //dd($aux_saldo);
                                        //Este If cierra abajo

                                        $atributoProd = $detalle->producto->atributosProducto($detalle->producto_id,$detalle->cotizaciondetalle_id);
                                        $aux_producto_nombre = $atributoProd["nombre"];

                                ?>
                                <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}">
                                    <td style="display:none;" name="NVdet_idTD{{$aux_nfila}}" id="NVdet_idTD{{$aux_nfila}}">
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
                                            <input type="text" name="cotizaciondetalle_id[]" id="cotizaciondetalle_id{{$aux_nfila}}" class="form-control" value="{{$detalle->cotizaciondetalle_id}}" style="display:none;"/>
                                        @else
                                            <input type="text" name="cotizaciondetalle_id[]" id="cotizaciondetalle_id{{$aux_nfila}}" class="form-control" value="{{$detalle->id}}" style="display:none;"/>
                                        @endif
                                    </td>
                                    <td style="text-align:center" name="producto_idTD{{$aux_nfila}}" id="producto_idTD{{$aux_nfila}}">
                                        @if ($detalle->producto->acuerdotecnico)
                                            <a class="btn-accion-tabla btn-sm tooltipsC" title="" onclick="genpdfAcuTec({{$detalle->producto->acuerdotecnico->id}},{{$data->cliente_id}},1)" data-original-title="Acuerdo Técnico PDF">
                                                {{$detalle->producto_id}}
                                            </a>
                                        @else
                                            {{$detalle->producto_id}}
                                        @endif
                                        <input type="text" name="producto_id[]" id="producto_id{{$aux_nfila}}" class="form-control" value="{{$detalle->producto_id}}" style="display:none;"/>
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="codintprod[]" id="codintprod{{$aux_nfila}}" class="form-control" value="{{$detalle->producto->codintprod}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        @if ($aux_sta==2)
                                            <input type="text" name="cant[]" id="cant{{$aux_nfila}}" class="form-control" value="{{$detalle->cant}}" style="display:none;"/>
                                        @else 
                                            <input type="text" name="cant[]" id="cant{{$aux_nfila}}" class="form-control" value="{{$detalle->cant - $detalle->cantusada}}" style="display:none;"/>
                                        @endif
                                    </td>
                                    <td style="text-align: center; white-space: nowrap;">
                                        <?php
                                            if($aux_sta==2){
                                                //$aux_cant = $detalle->cant;
                                            }else{
                                                $aux_cant -= $detalle->cantusada;
                                                $detalle->cant -= $detalle->cantusada;
                                            }
                                        ?>
                                        <input type="text" name="cantext[]" id="cantext{{$aux_nfila}}" class="form-control" value="{{$notaventadetalleext ? $notaventadetalleext->cantext : 0}}" style="display:none;"/>
                                        @if ($detalle->producto->acuerdotecnico)
                                            <a id="canttitle{{$aux_nfila}}" name="canttitle{{$aux_nfila}}" class="btn-accion-tabla btn-sm" title="Valor:{{$detalle->cant}} Ext:{{$notaventadetalleext ? $notaventadetalleext->cantext : 0}}" data-toggle="tooltip" style="padding-left: 0px; display: inline;">
                                                <div name="cantTD{{$aux_nfila}}" id="cantTD{{$aux_nfila}}" cantorig="{{$detalle->cant}}" style="display: inline;">
                                                    {{$aux_cant}}
                                                </div>
                                            </a>
                                            <a id="cantextA{{$aux_nfila}}" name="cantextA{{$aux_nfila}}" class="btn-accion-tabla btn-sm editarcampoNum" title="Editar valor sobre despacho" data-toggle="tooltip" valor="{{$notaventadetalleext ? $notaventadetalleext->cantext : 0}}" fila="{{$aux_nfila}}" nomcampo="cantext" style="padding-left: 0px; display: inline;">
                                                <i class="fa fa-fw fa-pencil-square-o"></i>
                                            </a>
                                        @else
                                            <div name="cantTD{{$aux_nfila}}" id="cantTD{{$aux_nfila}}" cantorig="{{$detalle->cant}}">
                                                {{$aux_cant}}
                                            </div>
                                        @endif
                                    </td>
                                    <td name="cantdespF{{$aux_nfila}}" id="cantdespF{{$aux_nfila}}" style="text-align:center">
                                        {{$sumacantdesp}}
                                    </td>
                                    <td name="cantsoldespF{{$aux_nfila}}" id="cantsoldespF{{$aux_nfila}}" style="text-align:center">
                                        {{$sumacantsoldesp}}
                                    </td>
                                    <td name="saldocantOrigF{{$aux_nfila}}" id="saldocantOrigF{{$aux_nfila}}" style="text-align:right;display:none;">
                                        {{$aux_saldo}}
                                    </td>
                                    <td name="saldocantF{{$aux_nfila}}" id="saldocantF{{$aux_nfila}}" style="text-align:center">
                                        @if($detalle->producto->tipoprod == 0)
                                            {{$aux_saldo}}
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td class='tooltipsC' style='text-align:center;display:none;' class='tooltipsC' title='Marcar'>
                                        <div class='checkbox'>
                                            <label style='font-size: 1.2em'>
                                                <input type="checkbox" class="checkllenarCantSol" id="llenarCantSol{{$aux_nfila}}" name="llenarCantSol{{$aux_nfila}}" onclick="llenarCantSol({{$aux_nfila}})">
                                                <span class='cr'><i class='cr-icon fa fa-check'></i></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td name="cantsolF{{$aux_nfila}}" id="cantsolF{{$aux_nfila}}" style="text-align:right">
                                        @if($detalle->producto->tipoprod == 0)
                                            <input type="text" name="cantsol[]" id="cantsol{{$aux_nfila}}" class="form-control numerico cantsolsum" onkeyup="actSaldo({{$detalle->cant - $sumacantsoldesp}},{{$aux_nfila}})" style="text-align:right;" readonly/>
                                        @else
                                            <input type="text" name="cantsol[]" id="cantsol{{$aux_nfila}}" class="form-control numerico cantsolsum" value="{{$aux_cant}}" style="text-align:right;" readonly/>
                                        @endif
                                        
                                    </td>
                                    <td name="cantsoldespinputF{{$aux_nfila}}" id="cantsoldespinputF{{$aux_nfila}}" style="text-align:right;display:none;">
                                        @if($detalle->producto->tipoprod == 0)
                                            <input type="text" name="cantsoldesp[]" id="cantsoldesp{{$aux_nfila}}" class="form-control" style="text-align:right;"/>
                                        @else
                                            <input type="text" name="cantsoldesp[]" id="cantsoldesp{{$aux_nfila}}" class="form-control" style="text-align:right;" value="{{$aux_cant}}"/>
                                        @endif
                                    </td>
                                    <td name="bodegasTB{{$aux_nfila}}" id="bodegasTB{{$aux_nfila}}" style="text-align:center;width: 18% !important">
                                        <?php
                                        // Verificar si hay stock en bodega de producción para este ítem NV.
                                        // Si requiere_fabricacion=1 pero ya se fabricó, se muestra la bodega
                                        // de producción en lugar de "En proceso de fabricacion".
                                        // Cadena: invmovdet_opdetregprod.notaventadetalle_id → invmovdet.cant (SUM)
                                        $stockProdRows = collect();
                                        if ($detalle->requiere_fabricacion == 1) {
                                            /* $stockProdRows = DB::table('invmovdet_opdetregprod as iodr')
                                                ->join('invmovdet as imd',   'imd.id',  '=', 'iodr.invmovdet_id')
                                                ->join('invbodega as ib',    'ib.id',   '=', 'imd.invbodega_id')
                                                ->join('opdetregprod as odrp','odrp.id','=', 'iodr.opdetregprod_id')
                                                ->join('opdet as od',        'od.id',   '=', 'odrp.opdet_id')
                                                ->join('op',                 'op.id',   '=', 'od.op_id')
                                                ->join('otdet as otd',       'otd.id',  '=', 'op.otdet_id')
                                                ->where('iodr.notaventadetalle_id', $detalle->id)
                                                ->whereNull('imd.deleted_at')
                                                ->whereNull('odrp.deleted_at')
                                                ->whereNull('od.deleted_at')
                                                ->whereNull('op.deleted_at')
                                                ->select(
                                                    'imd.invbodega_id',
                                                    'ib.nombre as bodega_nombre',
                                                    'ib.nomabre as bodega_nomabre',
                                                    'ib.sucursal_id as bodega_sucursal_id',
                                                    DB::raw('SUM(imd.cant) as cant_prod'),
                                                    DB::raw('GROUP_CONCAT(DISTINCT otd.ot_id ORDER BY otd.ot_id SEPARATOR \', \') as ot_ids')
                                                )
                                                ->groupBy('imd.invbodega_id', 'ib.nombre', 'ib.nomabre', 'ib.sucursal_id')
                                                ->having('cant_prod', '>', 0)
                                                ->get(); */

                                            $stockProdRows = DB::table('invmovdetnvdet as imdnv')
                                                ->join('invmovdet as imd',   'imd.id',  '=', 'imdnv.invmovdet_id')
                                                ->join('invbodega as ib',    'ib.id',   '=', 'imd.invbodega_id')
                                                ->where('imdnv.notaventadetalle_id', $detalle->id)
                                                ->whereNull('imd.deleted_at')
                                                ->select(
                                                    'imd.invbodega_id',
                                                    'imd.invbodegaproducto_id',
                                                    'ib.nombre as bodega_nombre',
                                                    'ib.nomabre as bodega_nomabre',
                                                    'ib.sucursal_id as bodega_sucursal_id',
                                                    DB::raw('SUM(imd.cant) as cant_prod')
                                                )
                                                ->groupBy('imd.invbodegaproducto_id', 'ib.sucursal_id')
                                                ->having('cant_prod', '>', 0)
                                                ->get();
                                        }
                                        ?>
                                        @if ($detalle->requiere_fabricacion == 1 && $stockProdRows->isEmpty())
                                            En proceso de fabricacion
                                        @elseif ($detalle->requiere_fabricacion == 1 && $stockProdRows->isNotEmpty())
                                            <?php 
                                                $stockProdRowsOT = DB::table('invmovdet_opdetregprod as iodr')
                                                    ->join('invmovdet as imd',   'imd.id',  '=', 'iodr.invmovdet_id')
                                                    ->join('invbodega as ib',    'ib.id',   '=', 'imd.invbodega_id')
                                                    ->join('opdetregprod as odrp','odrp.id','=', 'iodr.opdetregprod_id')
                                                    ->join('opdet as od',        'od.id',   '=', 'odrp.opdet_id')
                                                    ->join('op',                 'op.id',   '=', 'od.op_id')
                                                    ->join('otdet as otd',       'otd.id',  '=', 'op.otdet_id')
                                                    ->where('iodr.notaventadetalle_id', $detalle->id)
                                                    ->whereNull('imd.deleted_at')
                                                    ->whereNull('odrp.deleted_at')
                                                    ->whereNull('od.deleted_at')
                                                    ->whereNull('op.deleted_at')
                                                    ->select(
                                                        'imd.invbodega_id',
                                                        'imd.invbodegaproducto_id',
                                                        DB::raw('SUM(imd.cant) as cant_prod'),
                                                        DB::raw('GROUP_CONCAT(DISTINCT otd.ot_id ORDER BY otd.ot_id SEPARATOR \', \') as ot_ids')
                                                    )
                                                    ->groupBy('imd.invbodegaproducto_id', 'ib.sucursal_id')
                                                    ->having('cant_prod', '>', 0)
                                                    ->get();
                                                //dd($stockProdRowsOT);
                                            ?>
                                            {{-- Hay producción en bodega: mostrar igual que bodegas PT --}}
                                            {{-- <table class="table" id="tabla-bod-prod{{$aux_nfila}}" style="font-size:14px;table-layout:fixed;width:200px;"> --}}
                                            <table class="table" id="tabla-bod" style="font-size:14px;table-layout: fixed;width: 200px;">
                                                <tbody>
                                                @foreach($stockProdRows as $spRow)
                                                    <?php
                                                        //dd($stockProdRows);
                                                        // Obtener o crear el registro invbodegaproducto para esta bodega producción
                                                        $invbodegaproducto = InvBodegaProducto::firstOrCreate(
                                                            ['producto_id'  => $detalle->producto_id,
                                                             'invbodega_id' => $spRow->invbodega_id],
                                                            ['producto_id'  => $detalle->producto_id,
                                                             'invbodega_id' => $spRow->invbodega_id]
                                                        );
                                                        // Buscar en $stockProdRowsOT el registro que coincide con este invbodegaproducto.
                                                        // $stockProdRowsOT ya agrupa por invbodegaproducto_id y tiene ot_ids.
                                                        $aux_ot_ids = "";
                                                        $matchOT = $stockProdRowsOT->firstWhere('invbodegaproducto_id', $invbodegaproducto->id);
                                                        if ($matchOT) {
                                                            $aux_ot_ids = $matchOT->ot_ids ?? "";
                                                        }
                                                        //dd($invbodegaproducto);
                                                        // Color por sucursal (mismo criterio que bodegas PT)
                                                        switch ((int)$spRow->bodega_sucursal_id) {
                                                            case 1:  $colorSucProd = '#26ff00'; break;
                                                            case 2:  $colorSucProd = '#1500ff'; break;
                                                            case 3:  $colorSucProd = '#00c3ff'; break;
                                                            default: $colorSucProd = '#ff8c00'; break;
                                                        }

                                                        // Consultar lotes individuales de producción para esta bodega (FIFO por opdetregprod_id).
                                                        // cant_despachada = total ya asignado en solicitudes de despacho anteriores.
                                                        // cant_disponible  = cant_lote - cant_despachada (lo que realmente queda por despachar).
                                                        // Solo se muestran lotes con cant_disponible > 0.
                                                        $lotesRow = collect(DB::select("
                                                            SELECT
                                                                imop.opdetregprod_id,
                                                                SUM(imd.cant)   AS cant_lote,
                                                                SUM(imd.cantkg) AS cantkg_lote,
                                                                IFNULL(
                                                                    (SELECT SUM(dsop.cant)
                                                                     FROM   despachosoldet_opdetregprod dsop
                                                                     JOIN   despachosoldet dsd ON dsd.id = dsop.despachosoldet_id
                                                                                               AND dsd.deleted_at IS NULL
                                                                     JOIN   despachosol ds    ON ds.id  = dsd.despachosol_id
                                                                                               AND ds.deleted_at IS NULL
                                                                     WHERE  dsop.opdetregprod_id = imop.opdetregprod_id
                                                                       AND  ds.id NOT IN (
                                                                                SELECT despachosolanul.despachosol_id
                                                                                FROM   despachosolanul
                                                                                WHERE  despachosolanul.deleted_at IS NULL
                                                                           )),
                                                                    0
                                                                ) AS cant_despachada
                                                            FROM  invmovdetnvdet imdnv
                                                            JOIN  invmovdet imd               ON imd.id  = imdnv.invmovdet_id
                                                            JOIN  invmovdet_opdetregprod imop  ON imop.invmovdet_id = imd.id
                                                            WHERE imdnv.notaventadetalle_id = ?
                                                              AND imd.invbodegaproducto_id   = ?
                                                              AND imd.deleted_at IS NULL
                                                            GROUP BY imop.opdetregprod_id
                                                            ORDER BY imop.opdetregprod_id ASC
                                                        ", [$detalle->id, $invbodegaproducto->id]))
                                                        ->map(function($lr) {
                                                            // Calcular disponible y kg proporcional
                                                            $lr->cant_disponible   = max(0, $lr->cant_lote - $lr->cant_despachada);
                                                            $lr->cantkg_disponible = $lr->cant_lote > 0
                                                                ? ($lr->cant_disponible / $lr->cant_lote) * $lr->cantkg_lote
                                                                : 0;
                                                            return $lr;
                                                        })
                                                        ->filter(function($lr) {
                                                            return $lr->cant_disponible > 0; // ocultar lotes agotados
                                                        });

                                                        // CC: validación por cadena de producción completa (BFS bidireccional)
                                                        // La lógica centralizada está en CcValidacion::verificarCadenaDespacho()
                                                    ?>
                                                    {{-- <tr name="fila{{$invbodegaproducto->id}}" id="fila{{$invbodegaproducto->id}}" sucursal_id="{{$spRow->bodega_sucursal_id}}"> --}}
                                                    <tr name="fila{{$invbodegaproducto->id}}" id="fila{{$invbodegaproducto->id}}" sucursal_id="{{$invbodegaproducto->invbodega->sucursal_id}}">
                                                        <td name="invbodegaproducto_idTD{{$invbodegaproducto->id}}" id="invbodegaproducto_idTD{{$invbodegaproducto->id}}" style="text-align:left;display:none;">
                                                            <input type="text" name="invbodegaproducto_producto_id[]" id="invbodegaproducto_producto_id{{$invbodegaproducto->id}}" class="form-control" value="{{$detalle->producto_id}}" style="display:none;"/>
                                                            <input type="text" name="invbodegaproducto_id[]" id="invbodegaproducto_id{{$invbodegaproducto->id}}" class="form-control" value="{{$invbodegaproducto->id}}" style="display:none;"/>
                                                            <input type="text" name="invbodegaproductoNVdet_id[]" id="invbodegaproductoNVdet_id{{$aux_nfila}}" class="form-control" value="{{$detalle->id}}" style="display:none;"/>
                                                            {{$invbodegaproducto->id}}
                                                        </td>
                                                        {{-- <td style="text-align:left;width:15% !important;padding-right:0px;padding-left:2px;" class="tooltipsC" title="Bodega Producción: {{$spRow->bodega_nombre}} | OT: {{$spRow->ot_ids}}">
                                                            <div class="centrarhorizontal">
                                                                <p style="color:{{$colorSucProd}};font-size:11px;margin-bottom:0px;">
                                                                    {{$spRow->bodega_nomabre ?? $spRow->bodega_nombre}}
                                                                </p>
                                                            </div>
                                                        </td> --}}
                                                        {{-- <td style="text-align:left;width: 15% !important;padding-right: 0px;padding-left: 2px;" class='tooltipsC' title='Bodega: {{$invbodegaproducto->invbodega->nombre}} {{$invbodegaproducto->invbodega->sucursal->nombre}} | OT: {{$spRow->ot_ids}}'> --}}
                                                        {{-- El @if estaba fuera del title (el ' cerraba antes). Se usa ternario PHP para concatenar dentro del atributo. --}}
                                                        <td style="text-align:left;width: 15% !important;padding-right: 0px;padding-left: 2px;" class='tooltipsC'
                                                            title='Bodega: {{$invbodegaproducto->invbodega->nombre}} {{$invbodegaproducto->invbodega->sucursal->nombre}}{{$aux_ot_ids !== "" ? " | OT: " . $aux_ot_ids : ""}}'>
                                                            <div class="centrarhorizontal">
                                                                <p name="nomabreTD{{$invbodegaproducto->id}}" id="nomabreTD{{$invbodegaproducto->id}}" style="color:{{$colorSucProd}};font-size: 11px;margin-bottom: 0px;">
                                                                    {{$invbodegaproducto->invbodega->nomabre}} {{$invbodegaproducto->invbodega->sucursal->abrev}}
                                                                </p>
                                                            </div>
                                                        </td>

                                                        {{-- <td style="text-align:right;width: 20% !important;padding-left: 0px;padding-right: 0px;" class="tooltipsC" title="Stock en bodega producción">
                                                            <div class="centrarhorizontal">
                                                                <p style="font-size:11px;margin-bottom: 0px;">{{number_format($spRow->cant_prod, 0, ',', '.')}}</p>
                                                            </div>
                                                        </td> --}}
                                                        <td style="text-align:right;width: 20% !important;padding-left: 0px;padding-right: 0px;" class='tooltipsC' title='Stock disponible'>
                                                            <div class="centrarhorizontal">
                                                                <p name="stockcantTD{{$aux_nfila}}-{{$invbodegaproducto->id}}" id="stockcantTD{{$aux_nfila}}-{{$invbodegaproducto->id}}" style="font-size: 11px;margin-bottom: 0px;">{{$spRow->cant_prod}}</p>
                                                            </div>
                                                        </td>

                                                        {{-- <td  class="width90 tooltipsC" style="text-align:right;width:40% !important" title="Cant a despachar">
                                                            <input type="text" name="invcant[]" id="invcant{{$aux_nfila}}-{{$invbodegaproducto->id}}" class="form-control numerico bod{{$aux_nfila}} dismpadding invcant" onkeyup="sumbod({{$aux_nfila}},'{{$aux_nfila}}-{{$invbodegaproducto->id}}','SD')" style="text-align:right;" sucursal_id="{{$spRow->bodega_sucursal_id}}"/>
                                                        </td> --}}
                                                        <td  class="width90 tooltipsC" name="cantorddespF{{$invbodegaproducto->id}}" id="cantorddespF{{$invbodegaproducto->id}}" style="text-align:right;width: 40% !important" title='Total lotes (calculado automáticamente)'>
                                                            {{-- Readonly: su valor es la suma de los lotes de producción ingresados abajo --}}
                                                            <input type="text" name="invcant[]" id="invcant{{$aux_nfila}}-{{$invbodegaproducto->id}}" class="form-control numerico bod{{$aux_nfila}} dismpadding invcant" style="text-align:right;background:#f0f0f0;" sucursal_id="{{$invbodegaproducto->invbodega->sucursal_id}}" readonly tabindex="-1"/>
                                                        </td>

                                                        {{-- <td class="tooltipsC" style="text-align:center;padding-left:0px;padding-right:0px;width:10% !important;" title="Marcar para no usar Stock">
                                                            <div class="checkbox">
                                                                <label style="font-size:1.2em;padding-left:0px;">
                                                                    <input type="hidden" id="staex{{$invbodegaproducto->id}}" name="staex[]" value="0">
                                                                    <input type="checkbox" class="checkstaex" id="aux_staex{{$invbodegaproducto->id}}" name="aux_staex[]" onchange="clickstaex({{$invbodegaproducto->id}})">
                                                                    <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                                                                </label>
                                                            </div>
                                                        </td> --}}

                                                        <?php
                                                            $aux_staexchecked = "";
                                                            $staex = 0;
                                                            if($spRow->cant_prod <=0){
                                                                $aux_staexchecked = "checked";
                                                                $staex = 1;
                                                            }
                                                        ?>
                                                        <td class='tooltipsC' style='text-align:center;padding-left: 0px;padding-right: 0px;width: 10% !important;' class='tooltipsC' title='Marcar para no usar Stock'>
                                                            <div class='checkbox' style='display:none;'>
                                                                <label style='font-size: 1.2em;padding-left: 0px;'>
                                                                    <input type="hidden" id="staex{{$invbodegaproducto->id}}" name="staex[]" value="{{old('staex', $staex ?? '0')}}">
                                                                    <input type="checkbox" class="checkstaex" id="aux_staex{{$invbodegaproducto->id}}" name="aux_staex[]" {{$aux_staexchecked}} onchange="clickstaex({{$invbodegaproducto->id}})">
                                                                    <span class='cr'><i class='cr-icon fa fa-check'></i></span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    {{-- Sub-filas de lotes de producción para trazabilidad granular --}}
                                                    @foreach($lotesRow as $loteRow)
                                                    <?php
                                                        // Validación CC: recorre toda la cadena de producción del lote
                                                        $ccVal            = \App\Models\CcValidacion::verificarCadenaDespacho($loteRow->opdetregprod_id);
                                                        $loteBlocked      = $ccVal['bloqueado'];
                                                        $sinMuestraCC     = $ccVal['sin_muestra'];
                                                        $loteDesbloqueado = $ccVal['desbloqueado'];
                                                        $loteConObs       = $ccVal['con_obs'];
                                                        $rowBg = $loteBlocked ? '#fff0f0' : ($loteDesbloqueado ? '#f0f8ff' : ($loteConObs ? '#fffbe6' : '#f5f5f5'));
                                                    ?>
                                                    <tr class="lote-prod-row" data-ibp="{{$invbodegaproducto->id}}" style="background:{{$rowBg}};">
                                                        <td style="display:none;">
                                                            {{-- Inputs ocultos: identifican el lote, la bodega y el NVdet al que pertenece --}}
                                                            <input type="hidden" name="opdetregprod_id[]"      value="{{$loteRow->opdetregprod_id}}"/>
                                                            <input type="hidden" name="opdetregprod_invbp[]"   value="{{$invbodegaproducto->id}}"/>
                                                            <input type="hidden" name="opdetregprod_nvdet_id[]" value="{{$detalle->id}}"/>
                                                        </td>
                                                        <td colspan="2" style="padding:1px 6px;font-size:10px;text-align:left;vertical-align:middle;">
                                                            <span style="color:#888;">↳ Lote #{{$loteRow->opdetregprod_id}}</span>
                                                            <span style="color:#bbb;" title="Total producido: {{$loteRow->cant_lote}} | Ya despachado: {{$loteRow->cant_despachada}}">(disp. {{$loteRow->cant_disponible}} un)</span>
                                                            @if($loteBlocked)
                                                                @if($sinMuestraCC && !$ccVal['rechazado'])
                                                                    {{-- Falta muestra CC en alguna etapa de la cadena --}}
                                                                    <br><span class="label label-danger tooltipsC"
                                                                        title="Alguna etapa de la cadena de producción requiere muestra CC y no la tiene. Etapa(s): {{implode(', ', $ccVal['etapas_problema'])}}">
                                                                        <i class="fa fa-ban"></i> Sin muestra CC requerida
                                                                        <a href="javascript:void(0);" onclick="verEtiquetaEtapaConPermiso({{$loteRow->opdetregprod_id}})"
                                                                           style="color:#fff;font-weight:600;cursor:pointer;" title="Ver etiqueta — Lote de producción #{{$loteRow->opdetregprod_id}}"><i class="fa fa-tag"></i> Lote-{{$loteRow->opdetregprod_id}}</a>
                                                                        &nbsp;<a href="javascript:void(0);" onclick="verDetalleCCLote({{$loteRow->opdetregprod_id}})"
                                                                           style="color:#fff;text-decoration:underline;cursor:pointer;" title="Ver todas las etapas y su estado CC para este lote"><i class="fa fa-search"></i> Ver etapas CC</a>
                                                                    </span>
                                                                @else
                                                                    {{-- Rechazado por CC en alguna etapa de la cadena --}}
                                                                    @php $idsBlq = $ccVal['ids_bloqueados'] ? explode(', ', $ccVal['ids_bloqueados']) : []; @endphp
                                                                    <br><span class="label label-danger tooltipsC"
                                                                        title="Vaya a CC → Muestras para desbloquear. Etapa(s): {{implode(', ', $ccVal['etapas_problema'])}}">
                                                                        <i class="fa fa-ban"></i> Rechazado CC — Muestra(s):
                                                                        @foreach($idsBlq as $mId)
                                                                            <a href="javascript:void(0);" onclick="genpdfCC({{trim($mId)}})"
                                                                               style="color:#fff;text-decoration:underline;cursor:pointer;" title="Ver PDF muestra CC #{{trim($mId)}}">#{{trim($mId)}}</a>{{ !$loop->last ? ',' : '' }}
                                                                        @endforeach
                                                                        <a href="javascript:void(0);" onclick="verEtiquetaEtapaConPermiso({{$loteRow->opdetregprod_id}})"
                                                                           style="color:#fff;font-weight:600;cursor:pointer;" title="Ver etiqueta — Lote de producción #{{$loteRow->opdetregprod_id}}"><i class="fa fa-tag"></i> Lote-{{$loteRow->opdetregprod_id}}</a>
                                                                    </span>
                                                                @endif
                                                            @elseif($loteDesbloqueado)
                                                                @php $idsDesb = $ccVal['ids_desbloqueados'] ? explode(', ', $ccVal['ids_desbloqueados']) : []; @endphp
                                                                <br><span class="label tooltipsC"
                                                                    style="background:#00c0ef;"
                                                                    title="Rechazado CC y desbloqueado para despacho. {{$ccVal['desbloqueo_info'] ?? ''}}">
                                                                    <i class="fa fa-unlock"></i> Desbloqueado CC — Muestra(s):
                                                                    @foreach($idsDesb as $mId)
                                                                        <a href="javascript:void(0);" onclick="genpdfCC({{trim($mId)}})"
                                                                           style="color:#fff;text-decoration:underline;cursor:pointer;" title="Ver PDF muestra CC #{{trim($mId)}}">#{{trim($mId)}}</a>{{ !$loop->last ? ',' : '' }}
                                                                    @endforeach
                                                                    <a href="javascript:void(0);" onclick="verEtiquetaEtapaConPermiso({{$loteRow->opdetregprod_id}})"
                                                                       style="color:#fff;font-weight:600;cursor:pointer;" title="Ver etiqueta — Lote de producción #{{$loteRow->opdetregprod_id}}"><i class="fa fa-tag"></i> Lote-{{$loteRow->opdetregprod_id}}</a>
                                                                </span>
                                                            @elseif($loteConObs)
                                                                @php $idsObs = $ccVal['ids_con_obs'] ? explode(', ', $ccVal['ids_con_obs']) : []; @endphp
                                                                <br><span class="label label-warning tooltipsC"
                                                                    title="Aprobado con observaciones — puede despachar.">
                                                                    <i class="fa fa-exclamation-triangle"></i> CC con observaciones — Muestra(s):
                                                                    @foreach($idsObs as $mId)
                                                                        <a href="javascript:void(0);" onclick="genpdfCC({{trim($mId)}})"
                                                                           style="color:#fff;text-decoration:underline;cursor:pointer;" title="Ver PDF muestra CC #{{trim($mId)}}">#{{trim($mId)}}</a>{{ !$loop->last ? ',' : '' }}
                                                                    @endforeach
                                                                    <a href="javascript:void(0);" onclick="verEtiquetaEtapaConPermiso({{$loteRow->opdetregprod_id}})"
                                                                       style="color:#fff;font-weight:600;cursor:pointer;" title="Ver etiqueta — Lote de producción #{{$loteRow->opdetregprod_id}}"><i class="fa fa-tag"></i> Lote-{{$loteRow->opdetregprod_id}}</a>
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td style="padding:1px 2px;vertical-align:middle;">
                                                            {{-- El usuario ingresa cuánto de este lote incluye en la solicitud.
                                                                 JS valida que no exceda cant_disponible y suma al input de bodega (readonly).
                                                                 Si el lote tiene rechazo CC sin desbloquear, el input queda deshabilitado. --}}
                                                            <input type="text"
                                                                   name="opdetregprod_cant[]"
                                                                   id="opdetregprod_cant_{{$invbodegaproducto->id}}_{{$loteRow->opdetregprod_id}}"
                                                                   class="form-control dismpadding lote-cant-input"
                                                                   data-ibp="{{$invbodegaproducto->id}}"
                                                                   data-nfila="{{$aux_nfila}}"
                                                                   data-max="{{$loteRow->cant_disponible}}"
                                                                   data-cantkg-total="{{$loteRow->cantkg_disponible}}"
                                                                   value="0"
                                                                   style="text-align:right;font-size:11px;{{$loteBlocked ? 'background:#ffcccc;cursor:not-allowed;' : ''}}"
                                                                   @if($loteBlocked) disabled title="{{$sinMuestraCC && !$ccVal['rechazado'] ? 'Bloqueado — alguna etapa de la cadena requiere muestra CC: ' . implode(', ', $ccVal['etapas_problema']) : 'Bloqueado por rechazo CC — Muestra(s) #' . $ccVal['ids_bloqueados']}}" @endif />
                                                            {{-- Kg proporcionales calculados por JS --}}
                                                            <input type="hidden"
                                                                   name="opdetregprod_cantkg[]"
                                                                   id="opdetregprod_cantkg_{{$invbodegaproducto->id}}_{{$loteRow->opdetregprod_id}}"
                                                                   value="0"/>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                    @endforeach
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <table class="table" id="tabla-bod" style="font-size:14px;table-layout: fixed;width: 200px;">
                                                <tbody>
                                                    <?php $i=0 ?>
                                                    @foreach($invbodegaproductos as $invbodegaproducto)
                                                        @if (true or $invbodegaproducto->invbodega->sucursal_id == $data->sucursal_id)
                                                            <?php
                                                                $request = new Request();
                                                                $request["producto_id"] = $invbodegaproducto->producto_id;
                                                                $request["invbodega_id"] = $invbodegaproducto->invbodega_id;
                                                                $request["tipo"] = 2;
                                                                $existencia = $invbodegaproducto::existencia($request);
                                                                //dd($existencia);
                                                                //$existencia = $invbodegaproductoobj->consexistencia($request);
                                                                if ($invbodegaproducto->invbodega->sucursal_id == 1) {
                                                                    $colorSuc = "#26ff00";
                                                                }
                                                                if ($invbodegaproducto->invbodega->sucursal_id == 2) {
                                                                    $colorSuc = "#1500ff";
                                                                }
                                                                if ($invbodegaproducto->invbodega->sucursal_id == 3) {
                                                                    $colorSuc = "#00c3ff";
                                                                }
                                                            ?>
                                                            @if (in_array($invbodegaproducto->invbodega_id,$array_bodegasmodulo) AND ($invbodegaproducto->invbodega->activo == 1)) <!--SOLO MUESTRA LAS BODEGAS TIPO 1, LAS TIPO 2 NO LAS MUESTRA YA QUE ES BODEGA DE DESPACHO -->
                                                                <?php $i++; ?>
                                                                <tr name="fila{{$invbodegaproducto->id}}" id="fila{{$invbodegaproducto->id}}" sucursal_id="{{$invbodegaproducto->invbodega->sucursal_id}}">
                                                                    <td name="invbodegaproducto_idTD{{$invbodegaproducto->id}}" id="invbodegaproducto_idTD{{$invbodegaproducto->id}}" style="text-align:left;display:none;">
                                                                        <input type="text" name="invbodegaproducto_producto_id[]" id="invbodegaproducto_producto_id{{$invbodegaproducto->id}}" class="form-control" value="{{$detalle->producto_id}}" style="display:none;"/>
                                                                        <input type="text" name="invbodegaproducto_id[]" id="invbodegaproducto_id{{$invbodegaproducto->id}}" class="form-control" value="{{$invbodegaproducto->id}}" style="display:none;"/>
                                                                        <input type="text" name="invbodegaproductoNVdet_id[]" id="invbodegaproductoNVdet_id{{$aux_nfila}}" class="form-control" value="{{$detalle->id}}" style="display:none;"/>
                                                                        {{$invbodegaproducto->id}}
                                                                    </td>
                                                                    <td style="text-align:left;width: 15% !important;padding-right: 0px;padding-left: 2px;" class='tooltipsC' title='Bodega: {{$invbodegaproducto->invbodega->nombre}} {{$invbodegaproducto->invbodega->sucursal->nombre}}'>
                                                                        <div class="centrarhorizontal">
                                                                            <p name="nomabreTD{{$invbodegaproducto->id}}" id="nomabreTD{{$invbodegaproducto->id}}" style="color:{{$colorSuc}};font-size: 11px;margin-bottom: 0px;">{{$invbodegaproducto->invbodega->nomabre}} {{$invbodegaproducto->invbodega->sucursal->abrev}}</p>
                                                                        </div>
                                                                    </td>
                                                                    <td style="text-align:right;width: 20% !important;padding-left: 0px;padding-right: 0px;"  class='tooltipsC' title='Stock disponible'>
                                                                        <div class="centrarhorizontal">
                                                                            <p name="stockcantTD{{$aux_nfila}}-{{$invbodegaproducto->id}}" id="stockcantTD{{$aux_nfila}}-{{$invbodegaproducto->id}}" style="font-size: 11px;margin-bottom: 0px;">{{$existencia["stock"]["cant"]}}</p>
                                                                        </div>
                                                                    </td>
                                                                    <td  class="width90 tooltipsC" name="cantorddespF{{$invbodegaproducto->id}}" id="cantorddespF{{$invbodegaproducto->id}}" style="text-align:right;width: 40% !important" title='Cant a despachar'>
                                                                        <input type="text" name="invcant[]" id="invcant{{$aux_nfila}}-{{$invbodegaproducto->id}}" class="form-control numerico bod{{$aux_nfila}} dismpadding invcant" onkeyup="sumbod({{$aux_nfila}},'{{$aux_nfila}}-{{$invbodegaproducto->id}}','SD')" style="text-align:right;" sucursal_id="{{$invbodegaproducto->invbodega->sucursal_id}}"/>
                                                                    </td>
                                                                    <?php
                                                                        $aux_staexchecked = "";
                                                                        $staex = 0;
                                                                        if($existencia["stock"]["cant"] <=0){
                                                                            $aux_staexchecked = "checked";
                                                                            $staex = 1;
                                                                        }
                                                                    ?>
                                                                    <td class='tooltipsC' style='text-align:center;padding-left: 0px;padding-right: 0px;width: 10% !important;' class='tooltipsC' title='Marcar para no usar Stock'>
                                                                        <div class='checkbox'>
                                                                            <label style='font-size: 1.2em;padding-left: 0px;'>
                                                                                <input type="hidden" id="staex{{$invbodegaproducto->id}}" name="staex[]" value="{{old('staex', $staex ?? '0')}}">
                                                                                <input type="checkbox" class="checkstaex" id="aux_staex{{$invbodegaproducto->id}}" name="aux_staex[]" {{$aux_staexchecked}} onchange="clickstaex({{$invbodegaproducto->id}})">
                                                                                <span class='cr'><i class='cr-icon fa fa-check'></i></span>
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                    @if ($i == 0)
                                                        <a style="text-align:center" class='btn-sm tooltipsC' title='Producto sin Bodega Asignada'>
                                                            <i class='fa fa-fw fa-question-circle text-aqua'></i>
                                                        </a>
                                                    @endif
                                            </tbody>
                                        </table>
                                        @endif

                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="unidadmedida_id[]" id="unidadmedida_id{{$aux_nfila}}" class="form-control" value="4" style="display:none;"/>
                                    </td>
                                    <td name="nombreProdTD{{$aux_nfila}}" id="nombreProdTD{{$aux_nfila}}">
                                        {{$aux_producto_nombre}}
                                        {{-- @if ($aux_staAT)
                                            <br><span class='small-text'>{{$aux_atribAcuTec}}</span>
                                        @endif --}}
                                    </td>
                                    <td name="cla_nombreTD{{$aux_nfila}}" id="cla_nombreTD{{$aux_nfila}}" style="display:none;">
                                        {{$aux_cla_sello_nombre}}
                                    </td>
                                    <td name="diamextmmTD{{$aux_nfila}}" id="diamextmmTD{{$aux_nfila}}" style="text-align:right;display:none;">
                                        {{$aux_ancho}}
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="diamextmm[]" id="diamextmm{{$aux_nfila}}" class="form-control" value="{{$aux_ancho}}" style="display:none;"/>
                                    </td>
                                    <td name="longTD{{$aux_nfila}}" id="longTD{{$aux_nfila}}" style="text-align:center;display:none;">
                                        {{$aux_largo}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="long[]" id="long{{$aux_nfila}}" class="form-control" value="{{$aux_largo}}" style="display:none;"/>
                                    </td>
                                    <td name="espesorTD{{$aux_nfila}}" id="espesorTD{{$aux_nfila}}" style="text-align:center;display:none;">
                                        {{$aux_espesor}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="espesor[]" id="espesor{{$aux_nfila}}" class="form-control" value="{{$aux_espesor}}" style="display:none;"/>
                                    </td>
                                    <td name="pesoTD{{$aux_nfila}}" id="pesoTD{{$aux_nfila}}" style="text-align:right;">
                                        {{-- {{$peso}} --}}
                                        {{number_format($detalle->peso, (fmod($detalle->peso, 1) == 0.0 ? 2 : 6), ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="peso[]" id="peso{{$aux_nfila}}" class="form-control" value="{{$peso}}" style="display:none;"/>
                                    </td>
                                    <td name="totalkilosTD{{$aux_nfila}}" id="totalkilosTD{{$aux_nfila}}" style="text-align:right" class="subtotalkg" valor="{{$detalle->totalkilos}}">
                                        {{number_format($detalle->totalkilos, 2, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="totalkilos[]" id="totalkilos{{$aux_nfila}}" class="form-control" value="{{$detalle->totalkilos}}" style="display:none;"/>
                                    </td>
                                    <td name="tipounionTD{{$aux_nfila}}" id="tipounionTD{{$aux_nfila}}" style="text-align:center;display:none;"> 
                                        {{$detalle->producto->tipounion}}
                                    </td>
                                    <td name="precioxkiloTD{{$aux_nfila}}" id="precioxkiloTD{{$aux_nfila}}" style="text-align:right">
                                        {{number_format($detalle->precioxkilo, 2, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="tipounion[]" id="tipounion{{$aux_nfila}}" class="form-control" value="{{$detalle->producto->tipounion}}" style="display:none;"/>
                                    </td>
                                    <td name="descuentoTD{{$aux_nfila}}" id="descuentoTD{{$aux_nfila}}" style="text-align:right;display:none;">
                                        <?php $aux_descPorc = $detalle->descuento * 100; ?>
                                        {{$aux_descPorc}}%
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="descuento[]" id="descuento{{$aux_nfila}}" class="form-control" value="{{$detalle->descuento}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        <?php $aux_descVal = 1 - $detalle->descuento; ?>
                                        <input type="text" name="descuentoval[]" id="descuentoval{{$aux_nfila}}" class="form-control" value="{{$aux_descVal}}" style="display:none;"/>
                                    </td>
                                    <td name="preciounitTD{{$aux_nfila}}" id="preciounitTD{{$aux_nfila}}" style="text-align:right"> 
                                        {{number_format($detalle->preciounit, 3, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="preciounit[]" id="preciounit{{$aux_nfila}}" class="form-control" value="{{$detalle->preciounit}}" style="display:none;"/>
                                    </td>
                                    <td style="display:none;" name="precioxkiloTD{{$aux_nfila}}" id="precioxkiloTD{{$aux_nfila}}" style="text-align:right"> 
                                        {{number_format($detalle->precioxkilo, 3, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="precioxkilo[]" id="precioxkilo{{$aux_nfila}}" class="form-control" value="{{$detalle->precioxkilo}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="precioxkiloreal[]" id="precioxkiloreal{{$aux_nfila}}" class="form-control" value="{{$detalle->precioxkiloreal}}" style="display:none;"/>
                                    </td>
                                    <td name="subtotalCFTD{{$aux_nfila}}" id="subtotalCFTD{{$aux_nfila}}" class="subtotalCF" style="text-align:right"> 
                                        @if($detalle->producto->tipoprod == 3)
                                            {{number_format($detalle->subtotal, 0, ',', '.')}}
                                        @else
                                            0 <!--{{number_format($detalle->subtotal, 2, '.', ',')}}-->
                                        @endif                                        
                                    </td>
                                    <td class="subtotalCF" style="text-align:right;display:none;"> 
                                        <input type="text" name="subtotal[]" id="subtotal{{$aux_nfila}}" class="form-control" value="{{$detalle->subtotal}}" style="display:none;"/>
                                    </td>
                                    <td name="subtotalSFTD{{$aux_nfila}}" id="subtotalSFTD{{$aux_nfila}}" class="subtotal" style="text-align:right;display:none;">
                                        @if($detalle->producto->tipoprod == 3)
                                            {{$detalle->subtotal}}
                                        @else
                                            0 <!--{{$detalle->subtotal}}-->
                                        @endif
                                    </td>
                                </tr>
                                <?php 
                                    $i++;
                                    }
                                ?>
                            @endforeach
                            <tr id="trneto" name="trneto">
                                <td colspan="5" style="text-align:right;padding-bottom: 0px;padding-top: 0px;">
                                    <b>Total Unidades:</b>
                                </td>
                                <td style="text-align:right;padding-bottom: 0px;padding-top: 0px;padding-left: 2px;padding-right: 2px;">
                                    <div class="form-group col-xs-12 col-sm-12" style="margin-bottom: 0px;width: 100px !important">
                                        <input type="text" name="cantsolTotal" id="cantsolTotal" class="form-control" style="text-align:right;" readonly required/>
                                    </div>
                                </td>
                                <td colspan="3" style="text-align:right"><b>Total Kg</b></td>
                                <td id="totalkg" name="totalkg" style="text-align:right">0,00</td>
                                <td colspan="2" style="text-align:right"><b>Neto</b></td>
                                <td id="tdneto" name="tdneto" style="text-align:right">0,00</td>
                            </tr>
                            <tr id="triva" name="triva">
                                <td colspan="12" style="text-align:right"><b>IVA {{$empresa->iva}}%</b></td>
                                <td id="tdiva" name="tdiva" style="text-align:right">0,00</td>
                            </tr>
                            <tr id="trtotal" name="trtotal">
                                <td colspan="12" style="text-align:right"><b>Total</b></td>
                                <td id="tdtotal" name="tdtotal" style="text-align:right">0,00</td>
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

@include('generales.modalpdf')

{{-- Modal CC por Lote — muestra cobertura de etapas para un lote específico --}}
<div class="modal fade" id="modalCcLote" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width:90%;max-width:900px;">
        <div class="modal-content" style="border-radius:6px;">
            <div class="modal-header" style="background:#d9534f;color:#fff;border-radius:6px 6px 0 0;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;"><span>&times;</span></button>
                <h4 class="modal-title" style="font-size:14px;">
                    <i class="fa fa-search"></i>&nbsp; Cobertura CC — Lote <span id="modalCcLoteId"></span>
                </h4>
            </div>
            <div class="modal-body" id="modalCcLoteBody" style="padding:12px;max-height:70vh;overflow-y:auto;">
                <div class="text-center" style="padding:30px;">
                    <i class="fa fa-spinner fa-spin fa-2x"></i><br>
                    <span style="color:#888;">Cargando...</span>
                </div>
            </div>
            <div class="modal-footer" style="padding:8px 16px;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
<script>
function verDetalleCCLote(loteId) {
    $('#modalCcLoteId').text('#' + loteId);
    $('#modalCcLoteBody').html('<div class="text-center" style="padding:30px;"><i class="fa fa-spinner fa-spin fa-2x"></i><br><span style="color:#888;">Cargando...</span></div>');
    $('#modalCcLote').modal('show');
    $.ajax({
        url  : '/reportcclote/consultar',
        data : { opdetregprod_id: loteId },
        type : 'GET',
        success: function (html) {
            $('#modalCcLoteBody').html(html);
            $('[data-toggle="tooltip"], .tooltipsC').tooltip();
        },
        error: function (xhr) {
            $('#modalCcLoteBody').html('<div class="alert alert-danger">' + (xhr.responseText || 'Error al cargar datos.') + '</div>');
        }
    });
}

// Muestra el PDF de reportcclote en el modal global myModalpdf, ocultando el
// modal de detalle CC (modalCcLote) y restaurándolo al cerrar el PDF (mismo
// patrón que genpdfOD/genpdfSD en general.js).
function verPdfCcLote(url, aux_venmodant = "modalCcLote") {
    $("#venmodant").val("");
    if (aux_venmodant != "") {
        $("#" + aux_venmodant).modal('hide');
        $("#venmodant").val(aux_venmodant);
    }
    $('#contpdf').attr('src', url);
    $("#myModalpdf").modal('show');
}
</script>

{{-- Modal etiqueta de etapa (Reg. Prod.) — usado desde el label de rechazo CC --}}
<div class="modal fade modal-etiqueta" id="modalEtiquetaEtapa" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width:440px;">
        <div class="modal-content" style="border-radius:6px; overflow:hidden;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title" style="font-size:14px;">
                    <i class="fa fa-tag"></i>&nbsp; Etiqueta de etapa &mdash; Reg. <span id="modalEtiquetaId"></span>
                </h4>
            </div>
            <div class="modal-body" style="padding:0; height:340px;">
                <iframe id="ifrEtiquetaEtapa" name="ifrEtiquetaEtapa" src="about:blank"
                        style="width:100%; height:340px; border:none;"
                        sandbox="allow-scripts allow-same-origin allow-popups allow-modals">
                </iframe>
            </div>
            <div class="modal-footer" style="border-top:1px solid #e8edf3; padding:10px 16px;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cerrar
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="imprimirEtiquetaEtapa()">
                    <i class="fa fa-print"></i> Imprimir etiqueta
                </button>
            </div>
        </div>
    </div>
</div>
