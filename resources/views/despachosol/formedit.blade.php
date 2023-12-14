<?php
    use Illuminate\Http\Request;
    use App\Models\InvBodegaProducto;
?>
<input type="hidden" name="updated_at" id="updated_at" value="{{$data->updated_at}}">
<input type="hidden" name="id" id="id" value="{{$data->id}}">
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
                            <label for="id" class="control-label requerido" data-toggle='tooltip' title="Id Solicitud Despacho">Id</label>
                            <input type="text" name="id" id="id" class="form-control" value="{{$data->id}}" required disabled/>
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
                            <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', $data->notaventa->telefono ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="email" class="control-label requerido">Email</label>
                            <input type="text" name="email" id="email" class="form-control" value="{{old('email', $data->notaventa->email ?? '')}}" required readonly/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="comuna_idD" class="control-label requerido">Comuna</label>
                            <select name="comuna_idD" id="comuna_idD" class="selectpicker form-control comuna_idD" data-live-search='true' required readonly disabled>
                                <option value="">Seleccione...</option>
                                @foreach($comunas as $comuna)
                                    <option
                                        value="{{$comuna->id}}"
                                        @if (($aux_sta==2 or $aux_sta==3) and $comuna->id==$data->notaventa->comuna_id)
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
                                        @if (($aux_sta==2 or $aux_sta==3) and ($data->notaventa->vendedor_id==$vendedor->id))
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
                                        @if (($aux_sta==2 or $aux_sta==3) and ($data->notaventa->plazopago_id==$plazopago->id))
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
                                        @if (($aux_sta==2 or $aux_sta==3) and ($data->notaventa->formapago_id==$formapago->id))
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
                                        @if (($aux_sta==2 or $aux_sta==3) and ($data->notaventa->giro_id==$giro->id))
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
                            <input type="text" name="plazoentrega" id="plazoentrega" class="form-control pull-right datepicker"  value="{{old('plazoentrega', $data->plazoentrega ?? '')}}" readonly required {{$enableCamposCot}}>
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
                                            @if (isset($data) and ($data->sucursal_id==$sucursal->id))
                                                {{'selected'}}
                                            @endif
                                            >{{$sucursal->nombre}}</option>
                                    @endforeach                    
                            </select>
                        </div>
                        @if (count($data->notaventa->dteguiadespnvs) > 0)
                            <div class="form-group col-xs-12 col-sm-4">
                                <label for="dte_id" class="control-label requerido" data-toggle='tooltip' title="Origen Solicitud Desp">Origen Solicitud Desp</label>
                                <?php 
                                    $j = 0;
                                ?>
                                @foreach($data->notaventa->dteguiadespnvs as $dteguiadespnv)
                                    <?php 
                                        $j++;
                                    ?>
                                    <a class="btn-accion-tabla btn-sm tooltipsC" title="Ver Guia despacho: {{$dteguiadespnv->dte->nrodocto}}" onclick="genpdfGD('{{$dteguiadespnv->dte->nrodocto}}','')">
                                        {{$dteguiadespnv->dte->nrodocto}}
                                        @if ($j < count($data->notaventa->dteguiadespnvs))
                                            ,
                                        @endif
                                    </a>
                                @endforeach
                                <select name="dte_id" id="dte_id" class="form-control select2  dte_id" required>
                                    <option value="">Seleccione...</option>
                                    <option value="1"
                                        @if (($data->tipoguiadesp == 1 or $data->tipoguiadesp == 20))
                                            {{'selected'}}
                                        @endif
                                        >Nota de Venta</option>
                                    @foreach($data->notaventa->dteguiadespnvs as $dteguiadespnv)
                                        <option 
                                            value="{{$dteguiadespnv->dte_id}}"
                                            @if (isset($data->despachosoldte) and $dteguiadespnv->dte_id == $data->despachosoldte->dte_id)
                                                {{'selected'}}
                                            @endif
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
                                @if ($data->tipoguiadesp == 6)
                                    <option 
                                        value="6"
                                        @if(isset($data) and $data->tipoguiadesp =="6")
                                            {{'selected'}}
                                        @endif
                                        >Traslado</option>
                                    
                                @else
                                    <option
                                        value="30"
                                        @if(isset($data) and $data->tipoguiadesp =="30")
                                            {{'selected'}}
                                        @endif
                                        >Traslado</option>

                                    <option 
                                        value="1" 
                                        @if(isset($data) and $data->tipoguiadesp =="1")
                                            {{'selected'}}
                                        @endif
                                        >Precio</option>
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
                            <th style="display:none;" class="width30">ID</th>
                            <th style="display:none;">NotaVentaDetalle_ID</th>
                            <th style="display:none;">cotizacion_ID</th>
                            <th class="tooltipsC" title="Código Producto">Cod</th>
                            <th style="display:none;">CódInterno</th>
                            <th style="display:none;">Cant</th>
                            <th>Cant</th>
                            <th>Desp</th>
                            <th>Solid</th>
                            <!--<th>SolDesp</th>-->
                            <th>Saldo</th>
                            <th class='tooltipsC' title='Marcar todo' style="text-align:center;display:none">
                                <div class='checkbox'>
                                    <label style='font-size: 1.2em'>
                                        <input type='checkbox' id='marcarTodo' name='marcarTodo' checked>
                                        <span class='cr'><i class='cr-icon fa fa-check'></i></span>
                                    </label>
                                </div>
                            </th>
                            <th class="width90">SolDesp</th>
                            <th class="width200">Bodegas/Stock</th>
                            <th style="display:none;">UnidadMedida</th>
                            <th>Nombre</th>
                            <th>Clase<br>Sello</th>
                            <th>Diam<br>Ancho</th>
                            <th style="display:none;">Diametro</th>
                            <th>Largo</th>
                            <th style="display:none;">Largo</th>
                            <th>Esp</th>
                            <th style="display:none;">Espesor</th>
                            <th>Peso</th>
                            <th style="display:none;">Peso</th>
                            <th>Kilos</th>
                            <th style="display:none;">Total Kilos</th>
                            <th>TU</th>
                            <th style="display:none;">TUnion</th>
                            <th style="display:none;">Desc</th>
                            <th style="display:none;">DescPorc</th>
                            <th style="display:none;">DescVal</th>
                            <th>PUnit</th>
                            <th style="display:none;">Precio Neto Unit</th>
                            <th style="display:none;">V Kilo</th>
                            <th style="display:none;">Precio X Kilo</th>
                            <th style="display:none;">Precio X Kilo Real</th>
                            <th>Sub Total</th>
                            <th style="display:none;">Sub Total Neto</th>
                            <th style="display:none;">Sub Total Neto Sin Formato</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($aux_sta==2 or $aux_sta==3)
                            <?php 
                                $aux_nfila = 0; $i = 0;
                                $cantsolTotal = 0;
                            ?>
                            @foreach($detalles as $detalle)
                                <?php 
                                    $aux_nfila++;
                                    $cantsolTotal = $cantsolTotal + $detalle->cantsoldesp;
                                    $aux_cant = $detalle->notaventadetalle->cant;
                                    $notaventadetalleext = null;
                                    if($detalle->notaventadetalle->notaventadetalleext){
                                        $notaventadetalleext = $detalle->notaventadetalle->notaventadetalleext;
                                        $aux_cant = $detalle->notaventadetalle->cant + $notaventadetalleext->cantext;
                                    }
                                    $sql = "SELECT cantsoldesp
                                            FROM vista_sumsoldespdet
                                            WHERE notaventadetalle_id=$detalle->notaventadetalle_id";
                                    $datasuma = DB::select($sql);
                                    if(empty($datasuma)){
                                        $sumacantsoldesp= 0;
                                    }else{
                                        $sumacantsoldesp= $datasuma[0]->cantsoldesp;
                                    }
                                    //SUMA TOTAL DESPACHADO
                                    /*************************/
                                    $sql = "SELECT cantdesp
                                        FROM vista_sumorddespxnvdetid
                                        WHERE notaventadetalle_id=$detalle->notaventadetalle_id";
                                    $datasumadesp = DB::select($sql);
                                    if(empty($datasumadesp)){
                                        $sumacantdesp= 0;
                                    }else{
                                        $sumacantdesp= $datasumadesp[0]->cantdesp;
                                    }
                                    /*************************/

                                    $aux_saldo = $aux_cant - $sumacantsoldesp;
                                    $sumacantsoldesp = $sumacantsoldesp - $detalle->cantsoldesp;
                                    $subtotalItem = $detalle->cantsoldesp * $detalle->notaventadetalle->preciounit;
                                    $peso = $detalle->notaventadetalle->totalkilos/$aux_cant;
                                    $totalkilos = $peso * $detalle->cantsoldesp;
                                    $invbodegaproductos = $detalle->notaventadetalle->producto->invbodegaproductos;
                                    foreach ($detalle->notaventadetalle->producto->categoriaprod->invbodegas as $invbodega) {
                                        InvBodegaProducto::firstOrCreate(
                                            ['producto_id' => $detalle->notaventadetalle->producto_id, 'invbodega_id' => $invbodega->id],
                                            [   'producto_id' => $detalle->producto_id, 
                                                'invbodega_id' => $invbodega->id
                                            ]
                                        );
                                    }
                                    $aux_ancho = $detalle->notaventadetalle->producto->diametro;
                                    $aux_espesor = $detalle->notaventadetalle->producto->espesor;
                                    $aux_largo = $detalle->notaventadetalle->producto->long . "Mts";
                                    $aux_cla_sello_nombre = $detalle->notaventadetalle->producto->claseprod->cla_nombre;
                                    $aux_producto_nombre = $detalle->notaventadetalle->producto->nombre;
                                    $aux_categoria_nombre = $detalle->notaventadetalle->producto->categoriaprod->nombre;
                                    $aux_atribAcuTec = "";
                                    $aux_staAT = false;
                                    if ($detalle->notaventadetalle->producto->acuerdotecnico != null){
                                        $AcuTec = $detalle->notaventadetalle->producto->acuerdotecnico;
                                        $aux_producto_nombre = nl2br($AcuTec->producto->categoriaprod->nombre . ", " . $detalle->notaventadetalle->unidadmedida->nombre . ", " . $AcuTec->at_desc);
                                        $aux_ancho = $AcuTec->at_ancho . " " . ($AcuTec->at_ancho ? $AcuTec->anchounidadmedida->nombre : "");
                                        $aux_largo = $AcuTec->at_largo . " " . ($AcuTec->at_largo ? $AcuTec->largounidadmedida->nombre : "");
                                        $aux_espesor = number_format($AcuTec->at_espesor, 3, ',', '.');
                                        $aux_cla_sello_nombre = $AcuTec->claseprod->cla_nombre;
                                        $aux_atribAcuTec = $AcuTec->color->nombre . " " . $AcuTec->materiaprima->nombre . " " . $AcuTec->at_impresoobs;
                                        $aux_staAT = true;
                                    }
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
                                            <input type="text" name="cotizaciondetalle_id[]" id="cotizaciondetalle_id{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->cotizaciondetalle_id}}" style="display:none;"/>
                                        @else
                                            <input type="text" name="cotizaciondetalle_id[]" id="cotizaciondetalle_id{{$aux_nfila}}" class="form-control" value="{{$detalle->id}}" style="display:none;"/>
                                        @endif
                                    </td>
                                    <td style="text-align:center" name="producto_idTD{{$aux_nfila}}" id="producto_idTD{{$aux_nfila}}">
                                        @if ($detalle->notaventadetalle->producto->acuerdotecnico)
                                            <a class="btn-accion-tabla btn-sm tooltipsC" title="" onclick="genpdfAcuTec({{$detalle->notaventadetalle->producto->acuerdotecnico->id}},{{$data->notaventa->cliente_id}},1)" data-original-title="Acuerdo Técnico PDF">
                                                {{$detalle->notaventadetalle->producto_id}}
                                            </a>
                                        @else
                                            {{$detalle->notaventadetalle->producto_id}}
                                        @endif
                                        <input type="text" name="producto_id[]" id="producto_id{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto_id}}" style="display:none;"/>
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="codintprod[]" id="codintprod{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->codintprod}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        @if ($aux_sta==2)
                                            <input type="text" name="cant[]" id="cant{{$aux_nfila}}" class="form-control" value="{{$aux_cant}}" style="display:none;"/>
                                        @else 
                                            <input type="text" name="cant[]" id="cant{{$aux_nfila}}" class="form-control" value="{{$aux_cant - $detalle->notaventadetalle->cantusada}}" style="display:none;"/>
                                        @endif
                                    </td>
                                    <td style="text-align: center; white-space: nowrap;">
                                        <?php
                                            if($aux_sta==2){
                                                //$aux_cant = $detalle->notaventadetalle->cant;
                                            }else{
                                                $aux_cant -= $detalle->notaventadetalle->cantusada;
                                                $detalle->notaventadetalle->cant -= $detalle->notaventadetalle->cantusada;
                                            }
                                        ?>
                                        <input type="text" name="cantext[]" id="cantext{{$aux_nfila}}" class="form-control" value="{{$notaventadetalleext ? $notaventadetalleext->cantext : 0}}" style="display:none;"/>
                                        @if ($detalle->notaventadetalle->producto->acuerdotecnico)
                                            <a id="canttitle{{$aux_nfila}}" name="canttitle{{$aux_nfila}}" class="btn-accion-tabla btn-sm" title="Valor:{{$detalle->notaventadetalle->cant}} Ext:{{$notaventadetalleext ? $notaventadetalleext->cantext : 0}}" data-toggle="tooltip" style="padding-left: 0px; display: inline;">
                                                <div name="cantTD{{$aux_nfila}}" id="cantTD{{$aux_nfila}}" cantorig="{{$detalle->notaventadetalle->cant}}" style="display: inline;">
                                                    {{$aux_cant}}
                                                </div>
                                            </a>
                                            <a id="cantextA{{$aux_nfila}}" name="cantextA{{$aux_nfila}}" class="btn-accion-tabla btn-sm editarcampoNum" title="Editar valor sobre despacho" data-toggle="tooltip" valor="{{$notaventadetalleext ? $notaventadetalleext->cantext : 0}}" fila="{{$aux_nfila}}" nomcampo="cantext" style="padding-left: 0px;display: inline;">
                                                <i class="fa fa-fw fa-pencil-square-o"></i>
                                            </a>
                                        @else
                                            <div name="cantTD{{$aux_nfila}}" id="cantTD{{$aux_nfila}}" cantorig="{{$detalle->notaventadetalle->cant}}">
                                                {{$aux_cant}}
                                            </div>
                                        @endif
                                    </td>
                                    <td name="cantorddespF{{$aux_nfila}}" id="cantorddespF{{$aux_nfila}}" style="text-align:right">
                                        {{$sumacantdesp}}
                                    </td>
                                    <td name="cantsoldespF{{$aux_nfila}}" id="cantsoldespF{{$aux_nfila}}" style="text-align:right">
                                        {{$sumacantsoldesp}}
                                    </td>
                                    <td name="saldocantOrigF{{$aux_nfila}}" id="saldocantOrigF{{$aux_nfila}}" style="text-align:right;display:none;">
                                        {{$aux_saldo + $detalle->cantsoldesp}}
                                    </td>
                                    <td name="saldocantF{{$aux_nfila}}" id="saldocantF{{$aux_nfila}}" style="text-align:right">
                                        {{$aux_saldo}}
                                    </td>
                                    <td class='tooltipsC' style='text-align:center;display:none' class='tooltipsC' title='Marcar'>
                                        <div class='checkbox'>
                                            <label style='font-size: 1.2em'>
                                                <input type="checkbox" class="checkllenarCantSol" id="llenarCantSol{{$aux_nfila}}" name="llenarCantSol{{$aux_nfila}}" onclick="llenarCantSol({{$aux_nfila}})" checked>
                                                <span class='cr'><i class='cr-icon fa fa-check'></i></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td name="cantsolF{{$aux_nfila}}" id="cantsolF{{$aux_nfila}}" style="text-align:right">
                                        <input type="text" name="cantsol[]" id="cantsol{{$aux_nfila}}" class="form-control numerico cantsolsum" onkeyup="actSaldo({{$aux_cant - $detalle->cantsoldesp}},{{$aux_nfila}})" value="{{$detalle->cantsoldesp}}" style="text-align:right;" readonly/>
                                    </td>
                                    <td name="cantsoldespinputF{{$aux_nfila}}" id="cantsoldespinputF{{$aux_nfila}}" style="text-align:right;display:none;">
                                        <input type="text" name="cantsoldesp[]" id="cantsoldesp{{$aux_nfila}}" class="form-control" value="{{$detalle->cantsoldesp}}" style="text-align:right;"/>
                                    </td>
                                    <td name="bodegasTB{{$aux_nfila}}" id="bodegasTB{{$aux_nfila}}" style="text-align:center;">
                                        <table class="table" id="tabla-bod" style="font-size:14px;table-layout: fixed;width: 200px;">
                                            <tbody>
                                                <?php $i=0 ?>
                                                @foreach($invbodegaproductos as $invbodegaproducto)
                                                    @if (true or $invbodegaproducto->invbodega->sucursal_id == $data->notaventa->sucursal_id)
                                                        <?php
                                                            $i++;
                                                            $request = new Request();
                                                            $request["producto_id"] = $invbodegaproducto->producto_id;
                                                            $request["invbodega_id"] = $invbodegaproducto->invbodega_id;
                                                            $request["tipo"] = 2;
                                                            $existencia = $invbodegaproducto::existencia($request);
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
                                                            <tr name="fila{{$invbodegaproducto->id}}" id="fila{{$invbodegaproducto->id}}">
                                                                <td name="invbodegaproducto_idTD{{$invbodegaproducto->id}}" id="invbodegaproducto_idTD{{$invbodegaproducto->id}}" style="text-align:left;display:none;">
                                                                    <input type="text" name="invbodegaproducto_producto_id[]" id="invbodegaproducto_producto_id{{$invbodegaproducto->id}}" class="form-control" value="{{$detalle->notaventadetalle->producto_id}}" style="display:none;"/>
                                                                    <input type="text" name="invbodegaproducto_id[]" id="invbodegaproducto_id{{$invbodegaproducto->id}}" class="form-control" value="{{$invbodegaproducto->id}}" style="display:none;"/>
                                                                    <input type="text" name="invbodegaproductoNVdet_id[]" id="invbodegaproductoNVdet_id{{$aux_nfila}}" class="form-control" value="{{$detalle->id}}" style="display:none;"/>

                                                                    {{$invbodegaproducto->id}}
                                                                </td>
                                                                <td style="text-align:left;width: 10% !importan;padding-right: 0px;padding-left: 2px;" class='tooltipsC' title='Bodega: {{$invbodegaproducto->invbodega->nombre}} {{$invbodegaproducto->invbodega->sucursal->nombre}}'>
                                                                    <div class="centrarhorizontal">
                                                                        <p name="nomabreTD{{$invbodegaproducto->id}}" id="nomabreTD{{$invbodegaproducto->id}}" style="color:{{$colorSuc}};font-size: 11px;margin-bottom: 0px;">{{$invbodegaproducto->invbodega->nomabre}} {{$invbodegaproducto->invbodega->sucursal->abrev}}</p>
                                                                    </div>
                                                                </td>
                                                                <td style="text-align:right;width: 20% !importan;padding-left: 0px;padding-right: 0px;"  class='tooltipsC' title='Stock disponible'>
                                                                    <div name="stockcantTD{{$aux_nfila}}-{{$invbodegaproducto->id}}" id="stockcantTD{{$aux_nfila}}-{{$invbodegaproducto->id}}" class="centrarhorizontal">
                                                                        {{$existencia["stock"]["cant"]}}
                                                                    </div>
                                                                </td>
                                                                <?php 
                                                                    $aux_invcant = 0;
                                                                    $aux_staexchecked = "";
                                                                    $aux_staex = "";
                                                                    foreach($detalle->despachosoldet_invbodegaproductos as $despachosoldet_invbodegaproducto){
                                                                        if ($despachosoldet_invbodegaproducto->invbodegaproducto_id == $invbodegaproducto->id){
                                                                            $aux_invcant = (($despachosoldet_invbodegaproducto->cant + $despachosoldet_invbodegaproducto->cantex) * -1);
                                                                            if($despachosoldet_invbodegaproducto->staex == 1){
                                                                                $aux_staexchecked = "checked";
                                                                                $aux_staex = "1";
                                                                            }
                                                                        }
                                                                    }
                                                                ?>
                                                                <td class="width90 tooltipsC" name="cantorddespFInput{{$invbodegaproducto->id}}" id="cantorddespFInput{{$invbodegaproducto->id}}" style="text-align:right;width: 40% !importan"  title="Cant a despachar">
                                                                    <input type="text" name="invcant[]" id="invcant{{$aux_nfila}}-{{$invbodegaproducto->id}}" class="form-control numerico bod{{$aux_nfila}} dismpadding invcant" onkeyup="sumbod({{$aux_nfila}},'{{$aux_nfila}}-{{$invbodegaproducto->id}}','SD')" style="text-align:right;" value="{{$aux_invcant}}" sucursal_id="{{$invbodegaproducto->invbodega->sucursal_id}}"/>
                                                                </td>
                                                                <td class='tooltipsC' style='text-align:center;padding-left: 0px;padding-right: 0px;' class='tooltipsC' title='Marcar para no usar Stock'>
                                                                    <div class='checkbox'>
                                                                        <label style='font-size: 1.2em;padding-left: 0px;'>
                                                                            <input type="hidden" id="staex{{$invbodegaproducto->id}}" name="staex[]" value="{{$aux_staex}}">
                                                                            <input type="checkbox" class="checkstaex" id="aux_staex{{$invbodegaproducto->id}}" name="aux_staex[]" {{$aux_staexchecked}} onchange="clickstaex({{$invbodegaproducto->id}})">
                                                                            <span class='cr'><i class='cr-icon fa fa-check'></i></span>
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="unidadmedida_id[]" id="unidadmedida_id{{$aux_nfila}}" class="form-control" value="4" style="display:none;"/>
                                    </td>
                                    <td name="nombreProdTD{{$aux_nfila}}" id="nombreProdTD{{$aux_nfila}}">
                                        {{$aux_producto_nombre}}
                                        @if ($aux_staAT)
                                            <br><span class="small-text">{{$aux_atribAcuTec}}</span>
                                        @endif
                                    </td>
                                    <td name="cla_nombreTD{{$aux_nfila}}" id="cla_nombreTD{{$aux_nfila}}">
                                        {{$aux_cla_sello_nombre}}
                                    </td>
                                    <td name="diamextmmTD{{$aux_nfila}}" id="diamextmmTD{{$aux_nfila}}" style="text-align:right">
                                        {{$aux_ancho}}
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="diamextmm[]" id="diamextmm{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->diametro}}" style="display:none;"/>
                                    </td>
                                    <td name="longTD{{$aux_nfila}}" id="longTD{{$aux_nfila}}" style="text-align:right">
                                        {{$aux_largo}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="long[]" id="long{{$aux_nfila}}" class="form-control" value="{{$aux_largo}}" style="display:none;"/>
                                    </td>
                                    <td name="espesorTD{{$aux_nfila}}" id="espesorTD{{$aux_nfila}}" style="text-align:right">
                                        {{$aux_espesor}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="espesor[]" id="espesor{{$aux_nfila}}" class="form-control" value="{{$aux_espesor}}" style="display:none;"/>
                                    </td>
                                    <td name="pesoTD{{$aux_nfila}}" id="pesoTD{{$aux_nfila}}" style="text-align:right;">
                                        {{$peso}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="peso[]" id="peso{{$aux_nfila}}" class="form-control" value="{{$peso}}" style="display:none;"/>
                                    </td>
                                    <td name="totalkilosTD{{$aux_nfila}}" id="totalkilosTD{{$aux_nfila}}" style="text-align:right" class="subtotalkg" valor="{{$totalkilos}}">
                                        {{number_format($totalkilos, 2, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="totalkilos[]" id="totalkilos{{$aux_nfila}}" class="form-control" value="{{$totalkilos}}" style="display:none;"/>
                                    </td>
                                    <td name="tipounionTD{{$aux_nfila}}" id="tipounionTD{{$aux_nfila}}"> 
                                        {{$detalle->notaventadetalle->producto->tipounion}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="tipounion[]" id="tipounion{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->tipounion}}" style="display:none;"/>
                                    </td>
                                    <td name="descuentoTD{{$aux_nfila}}" id="descuentoTD{{$aux_nfila}}" style="text-align:right;display:none;">
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
                                        {{number_format($detalle->notaventadetalle->preciounit, 3, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="preciounit[]" id="preciounit{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->preciounit}}" style="display:none;"/>
                                    </td>
                                    <td style="display:none;" name="precioxkiloTD{{$aux_nfila}}" id="precioxkiloTD{{$aux_nfila}}" style="text-align:right"> 
                                        {{number_format($detalle->notaventadetalle->precioxkilo, 3, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="precioxkilo[]" id="precioxkilo{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->precioxkilo}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="precioxkiloreal[]" id="precioxkiloreal{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->precioxkiloreal}}" style="display:none;"/>
                                    </td>
                                    <td name="subtotalCFTD{{$aux_nfila}}" id="subtotalCFTD{{$aux_nfila}}" class="subtotalCF" style="text-align:right"> 
                                        {{number_format($subtotalItem, 0, ',', '.')}}
                                    </td>
                                    <td class="subtotalCF" style="text-align:right;display:none;"> 
                                        <input type="text" name="subtotal[]" id="subtotal{{$aux_nfila}}" class="form-control" value="{{$subtotalItem}}" style="display:none;"/>
                                    </td>
                                    <td name="subtotalSFTD{{$aux_nfila}}" id="subtotalSFTD{{$aux_nfila}}" class="subtotal" style="text-align:right;display:none;">
                                        {{$subtotalItem}}
                                    </td>
                                </tr>
                                <?php
                                    $i++;
                                ?>
                            @endforeach
                            <tr id="trneto" name="trneto">
                                <td colspan="5" style="text-align:right">
                                    <b>Total Unidades:</b>
                                </td>
                                <td style="text-align:right;padding-bottom: 0px;padding-top: 0px;padding-left: 2px;padding-right: 2px;">
                                    <div class="form-group col-xs-12 col-sm-12" style="margin-bottom: 0px;width: 100px !important">
                                        <input type="text" name="cantsolTotal" id="cantsolTotal" value={{$cantsolTotal}} class="form-control" style="text-align:right;" readonly required/>
                                    </div>
                                </td>
                                <td colspan="7" style="text-align:right"><b>Total Kg</b></td>
                                <td id="totalkg" name="totalkg" style="text-align:right">0.00</td>
                                <td colspan="2" style="text-align:right"><b>Neto</b></td>
                                <td id="tdneto" name="tdneto" style="text-align:right">0.00</td>
                            </tr>
                            <tr id="triva" name="triva">
                                <td colspan="16" style="text-align:right"><b>IVA {{$empresa->iva}}%</b></td>
                                <td id="tdiva" name="tdiva" style="text-align:right">0.00</td>
                            </tr>
                            <tr id="trtotal" name="trtotal">
                                <td colspan="16" style="text-align:right"><b>Total</b></td>
                                <td id="tdtotal" name="tdtotal" style="text-align:right">0.00</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('generales.modalpdf')