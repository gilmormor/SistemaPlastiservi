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
                            <input type="text" name="comunanombre" id="comunanombre" class="form-control" value="{{old('comunanombre', $data->notaventa->comuna->nombre ?? '')}}" required readonly/>
                            <input type="hidden" name="comuna_idD" id="comuna_idD" class="form-control" value="{{old('comuna_idD', $data->notaventa->comuna_id ?? '')}}"/>
                            {{-- <select name="comuna_idD" id="comuna_idD" class="selectpicker form-control comuna_idD" data-live-search='true' required readonly disabled>
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
                            </select> --}}
                        </div>
            
                    </div>
            
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="vendedor_idD" class="control-label requerido">Vendedor</label>
                            <input type="text" name="vendedornombre" id="vendedornombre" class="form-control" value="{{old('vendedornombre', $data->notaventa->vendedor->persona->nombre . ' ' . $data->notaventa->vendedor->persona->apellido ?? '')}}" required readonly/>
                            <input type="hidden" name="vendedor_idD" id="vendedor_idD" class="form-control" value="{{old('vendedor_idD', $data->notaventa->vendedor_id ?? '')}}"/>
                            {{-- <select name="vendedor_idD" id="vendedor_idD" class="form-control select2 vendedor_idD" required readonly disabled>
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
                            </select> --}}
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="plazopago_idD" class="control-label requerido">Plazo</label>
                            <input type="text" name="plazodesc" id="plazodesc" class="form-control" value="{{old('plazodesc', $data->notaventa->plazopago->descripcion ?? '')}}" required readonly/>
                            <input type="hidden" name="plazopago_idD" id="plazopago_idD" class="form-control" value="{{old('plazopago_idD', $data->notaventa->plazopago_id ?? '')}}"/>
                            {{-- <select name="plazopago_idD" id="plazopago_idD" class="form-control selectpicker plazopago_idD" required readonly disabled>
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
                            </select> --}}
                        </div>
            
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="formapago_idD" class="control-label requerido">Forma de Pago</label>
                            <input type="text" name="formapagodesc" id="formapagodesc" class="form-control" value="{{old('formapagodesc', $data->notaventa->formapago->descripcion ?? '')}}" required readonly/>
                            <input type="hidden" name="formapago_idD" id="formapago_idD" class="form-control" value="{{old('formapago_idD', $data->notaventa->formapago_id ?? '')}}"/>                            
                            {{-- <select name="formapago_idD" id="formapago_idD" class="form-control selectpicker formapago_idD" required readonly disabled>
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
                            </select> --}}
                        </div>
            
            
            
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="giro_idD" class="control-label requerido">Giro</label>
                            <input type="text" name="girodes" id="girodes" class="form-control" value="{{old('girodes', $data->notaventa->giro->descripcion ?? '')}}" required readonly/>
                            <input type="hidden" name="giro_idD" id="giro_idD" class="form-control" value="{{old('giro_idD', $data->notaventa->giro_id ?? '')}}"/>
                            {{-- <select name="giro_idD" id="giro_idD" class="form-control selectpicker giro_idD" required readonly disabled>
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
                            </select> --}}
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
                                    <a class="btn-accion-tabla btn-sm tooltipsC" title="Ver Guia despacho: {{$dteguiadespnv->dte->nrodocto}}" onclick="genpdfFACDin('{{$dteguiadespnv->dte->id}}',0)">
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
                                    {{-- <option
                                        value="30"
                                        @if(isset($data) and $data->tipoguiadesp =="30")
                                            {{'selected'}}
                                        @endif
                                        >Traslado</option> --}}

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
                                <input type="text" name="oc_id" id="oc_id" class="form-control validar-texto-xml" value="{{old('oc_id', $data->oc_id ?? '')}}" placeholder="Nro Orden de Compra" {{$enableCamposCot}}/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-12">
                            <label id="lboc_id" name="lboc_id" for="oc_file" class="control-label">Adjuntar OC</label>
                            <div class="input-group">
                                <input type="file" name="oc_file" id="oc_file" class="form-control" data-initial-preview='{{isset($data->oc_file) ? Storage::url("imagenes/notaventa/$data->oc_file") : ""}}'  accept=".jpg,.jpeg,.png,.pdf, application/pdf"/>
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
                            <th style="display:none;">TU</th>
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
                                /* Las cantidades solicitada y despachada de todos los items se
                                   piden de una vez. Antes se consultaba vista_sumsoldespdet y
                                   vista_sumorddespxnvdetid dentro del bucle, una vez por item, y
                                   como MySQL 5.7 materializa completa una vista con GROUP BY al
                                   filtrarla, cada item costaba unos 6,4 s: esta pantalla tardaba
                                   casi 4 minutos con una solicitud de 30 items.
                                   Aqui $detalles son despachosoldet, asi que el id del item de la
                                   nota de venta es notaventadetalle_id. */
                                $aux_sumas = \App\Models\NotaVentaDetalle::sumasDespachoPorItem(
                                    collect($detalles)->pluck('notaventadetalle_id')->all()
                                );

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
                                    // viene de la precarga de arriba; sin movimientos, 0, como antes
                                    $sumacantsoldesp = isset($aux_sumas['cantsoldesp'][$detalle->notaventadetalle_id])
                                                        ? $aux_sumas['cantsoldesp'][$detalle->notaventadetalle_id]
                                                        : 0;
                                    //SUMA TOTAL DESPACHADO
                                    /*************************/
                                    // viene de la precarga de arriba; sin movimientos, 0, como antes
                                    $sumacantdesp = isset($aux_sumas['cantdesp'][$detalle->notaventadetalle_id])
                                                     ? $aux_sumas['cantdesp'][$detalle->notaventadetalle_id]
                                                     : 0;
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
                                    $aux_cla_sello_nombre = isset($detalle->notaventadetalle->producto->claseprod) ? $detalle->notaventadetalle->producto->claseprod->cla_nombre : "";
                                    $aux_categoria_nombre = $detalle->notaventadetalle->producto->categoriaprod->nombre;
                                    $aux_atribAcuTec = "";
                                    $aux_staAT = false;
                                    if ($detalle->notaventadetalle->producto->acuerdotecnico != null){
                                        $AcuTec = $detalle->notaventadetalle->producto->acuerdotecnico;
                                        $aux_ancho = $AcuTec->at_ancho . " " . ($AcuTec->at_ancho ? $AcuTec->anchounidadmedida->nombre : "");
                                        $aux_largo = $AcuTec->at_largo . " " . ($AcuTec->at_largo ? $AcuTec->largounidadmedida->nombre : "");
                                        $aux_espesor = number_format($AcuTec->at_espesor, 3, ',', '.');
                                        $aux_cla_sello_nombre = $AcuTec->claseprod->cla_nombre;
                                        $aux_atribAcuTec = $AcuTec->color->nombre . " " . $AcuTec->materiaprima->nombre . " " . $AcuTec->at_impresoobs;
                                        $aux_staAT = true;
                                    }
                                    $aux_producto_nombre = $detalle->notaventadetalle->producto->glosa;
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
                                        <?php
                                        // Verificar si hay stock en bodega de producción para este ítem NV.
                                        // Si requiere_fabricacion=1 pero ya se fabricó, se muestra la bodega
                                        // de producción en lugar de "En proceso de fabricacion".
                                        // Cadena: invmovdet_opdetregprod.notaventadetalle_id → invmovdet.cant (SUM)
                                        $stockProdRows = collect();
                                        if ($detalle->notaventadetalle->requiere_fabricacion == 1) {
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

                                            /* Stock por bodega del item. Mismo criterio que despachosol/form.blade.php.

                                               invmovdetnvdet la escribe solo DespachoSolController: tiene la entrada
                                               de produccion y el traspaso de la solicitud, pero no la salida que hace
                                               la orden de despacho, asi que Picking quedaba mostrando stock ya
                                               despachado. Esa salida si esta en invmovdet_bodsoldesp, via
                                               despachosoldet_invbodegaproducto: se suman las DOS vias con UNION para
                                               no contar dos veces el traspaso, que aparece en ambas. */
                                            $aux_nvdet_id = intval(optional($detalle->notaventadetalle)->id);
                                            $stockProdRows = DB::table(DB::raw('('
                                                    . ' SELECT imd2.id FROM invmovdetnvdet imdnv'
                                                    . '   JOIN invmovdet imd2 ON imd2.id = imdnv.invmovdet_id'
                                                    . '  WHERE imdnv.notaventadetalle_id = ' . $aux_nvdet_id
                                                    . '    AND imd2.deleted_at IS NULL'
                                                    . ' UNION'
                                                    . ' SELECT imd3.id FROM invmovdet_bodsoldesp b'
                                                    . '   JOIN despachosoldet_invbodegaproducto z ON z.id = b.despachosoldet_invbodegaproducto_id AND z.deleted_at IS NULL'
                                                    . '   JOIN despachosoldet dsd ON dsd.id = z.despachosoldet_id AND dsd.deleted_at IS NULL'
                                                    . '   JOIN despachosol ds ON ds.id = dsd.despachosol_id AND ds.deleted_at IS NULL'
                                                    . '   JOIN invmovdet imd3 ON imd3.id = b.invmovdet_id'
                                                    . '  WHERE dsd.notaventadetalle_id = ' . $aux_nvdet_id
                                                    . '    AND imd3.deleted_at IS NULL'
                                                    /* Tercera via: lotes que se le ENTREGARON a este item desde otra
                                                       nota de venta o desde una OT libre (modulo Reasignar
                                                       Produccion). Sin esto la pantalla decia "En proceso de
                                                       fabricacion" aunque el material ya estuviera en bodega y
                                                       asignado a este cliente: las otras dos vias preguntan por
                                                       notaventadetalle_id y un lote reasignado no cuelga de el.
                                                       Es ADITIVA: sin reasignaciones devuelve vacio y la pantalla se
                                                       comporta exactamente como antes. */
                                                    . ' UNION'
                                                    . ' SELECT imd4.id FROM prodreasignacion r'
                                                    . '   JOIN invmovdet_opdetregprod l ON l.opdetregprod_id = r.opdetregprod_id'
                                                    . '   JOIN invmovdet imd4 ON imd4.id = l.invmovdet_id'
                                                    /* Solo bodegas de almacenamiento (tipo 2). De picking no se
                                                       despacha: ahi va el material ya apartado para una solicitud,
                                                       y ofrecerlo como origen invita a tomarlo dos veces. */
                                                    . '   JOIN invbodega ib4 ON ib4.id = imd4.invbodega_id AND ib4.tipo = 2'
                                                    . '  WHERE r.nvdet_destino_id = ' . $aux_nvdet_id
                                                    . '    AND r.deleted_at IS NULL AND r.anulada_at IS NULL'
                                                    . '    AND r.consumida_at IS NULL'
                                                    . '    AND imd4.deleted_at IS NULL'
                                                    . ') as u'))
                                                ->join('invmovdet as imd',   'imd.id',  '=', 'u.id')
                                                ->join('invbodega as ib',    'ib.id',   '=', 'imd.invbodega_id')
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

                                            /* Cuarta via: stock RESERVADO a nombre de este cliente o de
                                               esta nota de venta, mas las bodegas que ESTA solicitud ya
                                               esta usando.

                                               Lo primero es lo mismo que hace la pantalla de crear: un
                                               producto que se fabrica a medida pero cuyo stock entro por
                                               inventario no tiene lotes, y sin lote no hay nada que
                                               ofrecer aunque haya material apartado a su nombre.

                                               Lo segundo solo hace falta aqui: si la reserva se anula o
                                               vence despues de crear la solicitud, la bodega desapareceria
                                               de la pantalla y al guardar se perderia lo ya pedido. Su
                                               tope es lo que la solicitud ya tenia -no deja subir, pero si
                                               ver y corregir-.

                                               Solo entra cuando las otras tres vias no encontraron nada.
                                               Sin reservas y sin nada guardado devuelve vacio y la
                                               pantalla se comporta como antes. */
                                            if ($stockProdRows->isEmpty()) {
                                                $aux_bodHabilita = [];
                                                foreach (\App\Models\StockReserva::bodegasReservadasParaNvdet($detalle->notaventadetalle_id) as $aux_br) {
                                                    $aux_bodHabilita[$aux_br->invbodegaproducto_id] = $aux_br;
                                                }

                                                foreach ($detalle->despachosoldet_invbodegaproductos as $aux_dsdb) {
                                                    $aux_usado = round((($aux_dsdb->cant + $aux_dsdb->cantex) * -1), 2);
                                                    if ($aux_usado <= 0) { continue; }

                                                    $aux_ibpUsa = \App\Models\InvBodegaProducto::find($aux_dsdb->invbodegaproducto_id);
                                                    if (!$aux_ibpUsa or !$aux_ibpUsa->invbodega) { continue; }

                                                    if (isset($aux_bodHabilita[$aux_dsdb->invbodegaproducto_id])) {
                                                        /* Ya viene por la reserva: el tope es el mayor de los dos,
                                                           porque lo guardado no se puede dejar por debajo. */
                                                        $aux_bodHabilita[$aux_dsdb->invbodegaproducto_id]->cant_prod =
                                                            max((float) $aux_bodHabilita[$aux_dsdb->invbodegaproducto_id]->cant_prod, $aux_usado);
                                                        continue;
                                                    }

                                                    $aux_fil = new \stdClass();
                                                    $aux_fil->invbodega_id         = $aux_ibpUsa->invbodega_id;
                                                    $aux_fil->invbodegaproducto_id = $aux_ibpUsa->id;
                                                    $aux_fil->bodega_nombre        = $aux_ibpUsa->invbodega->nombre;
                                                    $aux_fil->bodega_nomabre       = $aux_ibpUsa->invbodega->nomabre;
                                                    $aux_fil->bodega_sucursal_id   = $aux_ibpUsa->invbodega->sucursal_id;
                                                    $aux_fil->cant_prod            = $aux_usado;
                                                    $aux_fil->es_reserva           = true;   // mismo tope acotado
                                                    $aux_bodHabilita[$aux_ibpUsa->id] = $aux_fil;
                                                }

                                                if (count($aux_bodHabilita)) {
                                                    $stockProdRows = collect(array_values($aux_bodHabilita));
                                                }
                                            }
                                        }
                                        ?>
                                        <?php
                                        /* REASIGNACION DE PRODUCCION: lotes que se fabricaron para
                                           ESTE item y se entregaron a otro cliente u otra nota de
                                           venta. Hay que decirlo aqui: sin este aviso la pantalla
                                           mostraria "En proceso de fabricacion", que seria falso
                                           -ya se fabrico- y la ejecutiva esperaria un material que
                                           no va a llegar. */
                                        $aux_reasigQuitadas = \App\Models\ProdReasignacion::quitadasANvdet($detalle->notaventadetalle_id);
                                        ?>
                                        @if (count($aux_reasigQuitadas) > 0)
                                            <div class="alert alert-warning" style="text-align:left;padding:6px 8px;margin-bottom:6px;font-size:11px;">
                                                <i class="fa fa-exclamation-triangle"></i>
                                                <strong>Producción entregada a otro cliente</strong>
                                                @foreach($aux_reasigQuitadas as $aux_re)
                                                    <div style="margin-top:3px;">
                                                        Lote <strong>#{{$aux_re->opdetregprod_id}}</strong>
                                                        @if($aux_re->cant) ({{number_format($aux_re->cant, 2, ',', '.')}}) @endif
                                                        &rarr;
                                                        @if($aux_re->destino_nv) <strong>NV {{$aux_re->destino_nv}}</strong> @endif
                                                        {{$aux_re->destino_cliente}}
                                                        <br>
                                                        <span style="color:#777;">
                                                            {{date('d/m/Y', strtotime($aux_re->created_at))}}
                                                            @if($aux_re->usuario_nombre) · {{$aux_re->usuario_nombre}} @endif
                                                            @if($aux_re->motivo) · {{$aux_re->motivo}} @endif
                                                        </span>
                                                    </div>
                                                @endforeach
                                                <div style="margin-top:4px;">
                                                    Esta nota de venta requiere una nueva fabricación o que se le asigne stock.
                                                </div>
                                            </div>
                                        @endif
                                        @if ($detalle->notaventadetalle->requiere_fabricacion == 1 && $stockProdRows->isEmpty())
                                            En proceso de fabricacion
                                        @elseif ($detalle->notaventadetalle->requiere_fabricacion == 1 && $stockProdRows->isNotEmpty())
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
                                                            ['producto_id'  => $detalle->notaventadetalle->producto_id,
                                                             'invbodega_id' => $spRow->invbodega_id],
                                                            ['producto_id'  => $detalle->notaventadetalle->producto_id,
                                                             'invbodega_id' => $spRow->invbodega_id]
                                                        );

                                                        /* Cuanto tiene asignado ESTA solicitud en esta bodega. Se calcula
                                                           aqui arriba -el mismo calculo de $aux_invcantBod de mas abajo-
                                                           solo para decidir si la bodega se muestra. */
                                                        $aux_asignadoAqui = 0;
                                                        foreach ($detalle->despachosoldet_invbodegaproductos as $aux_dsdBP) {
                                                            if ($aux_dsdBP->invbodegaproducto_id == $invbodegaproducto->id) {
                                                                $aux_asignadoAqui = (($aux_dsdBP->cant + $aux_dsdBP->cantex) * -1);
                                                            }
                                                        }
                                                        $aux_bodTipoEd = \App\Models\InvBodega::where('id', $spRow->invbodega_id)->value('tipo');
                                                    ?>
                                                    {{-- Las bodegas de picking (tipo 1) no se ofrecen para pedir: ese
                                                         material ya esta comprometido por la solicitud que lo mando a
                                                         picking, no esta libre. Pero si ESTA solicitud tiene algo
                                                         asignado ahi, la fila se muestra igual: esconderla le quitaria
                                                         al usuario lo que ya habia pedido. --}}
                                                    @continue($aux_bodTipoEd == 1 and $aux_asignadoAqui <= 0)
                                                    <?php
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

                                                        // Consultar lotes con cant_disponible = cant_lote - ya_despachado_en_OTRAS_solicitudes.
                                                        // Se excluye el despachosoldet actual (id=$detalle->id) para no restar lo que ya asignó este formulario.
                                                        $currentDsdId = $detalle->id;
                                                        /* Cuanto de cada lote de este item se entrego a otro cliente o a
                                                           otra nota de venta. Se DESCUENTA, no se esconde el lote: una
                                                           reasignacion puede ser parcial y de 600 unidades pueden
                                                           haberse entregado 400, con lo que a esta NV todavia le
                                                           quedan 200 suyas que si puede despachar. */
                                                        $aux_loteQuitadoCant = \App\Models\ProdReasignacion::cantQuitadaPorLote($detalle->notaventadetalle_id);
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
                                                                       AND  dsop.despachosoldet_id != ?
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
                                                        ", [$currentDsdId, $detalle->notaventadetalle_id, $invbodegaproducto->id]))
                                                        ->map(function($lr) use ($aux_loteQuitadoCant) {
                                                            /* Lo entregado a otro sale del disponible igual que lo ya
                                                               despachado: para esta nota de venta ese material no
                                                               esta. Si se entrego el lote completo queda en 0 y la
                                                               fila desaparece sola por el filtro de mas abajo. */
                                                            $lr->cant_reasignada = $aux_loteQuitadoCant[$lr->opdetregprod_id] ?? 0;
                                                            $lr->cant_disponible   = max(0, $lr->cant_lote - $lr->cant_despachada - $lr->cant_reasignada);
                                                            $lr->cantkg_disponible = $lr->cant_lote > 0
                                                                ? ($lr->cant_disponible / $lr->cant_lote) * $lr->cantkg_lote
                                                                : 0;
                                                            return $lr;
                                                        })
                                                        ->filter(function($lr) {
                                                            return $lr->cant_disponible > 0;
                                                        });

                                                        /* Lotes ENTREGADOS a este item desde otra nota de venta o desde
                                                           una OT libre. No cuelgan de invmovdetnvdet, asi que la
                                                           consulta de arriba no los ve y hay que traerlos aparte.

                                                           La cantidad disponible es la REASIGNADA, no la del lote: si el
                                                           lote tiene 600 en bodega pero a esta NV se le entregaron 400,
                                                           se pueden despachar 400. Lo demas sigue siendo de otro.

                                                           Se fusiona en PHP en vez de meterlo en la consulta de arriba
                                                           para no tocarla: sin reasignaciones esta lista viene vacia y
                                                           todo queda igual que antes. */
                                                        /* Se excluye el item que se esta editando: lo que esta propia
                                                           solicitud tiene tomado no es material ajeno, lo puede soltar o
                                                           volver a pedir. Sin esto el lote se ve agotado por su propia
                                                           asignacion y desaparece de la pantalla. */
                                                        $aux_lotesRecibidos = \App\Models\ProdReasignacion::lotesRecibidosEnBodega(
                                                            $aux_nvdet_id, $invbodegaproducto->id, $detalle->id
                                                        );
                                                        foreach ($aux_lotesRecibidos as $aux_lr) {
                                                            /* Si por lo que sea el lote ya aparecia por la via normal, se
                                                               respeta esa fila y no se agrega otra: dos filas del mismo
                                                               lote dejarian despachar el doble. */
                                                            if ($lotesRow->firstWhere('opdetregprod_id', $aux_lr->opdetregprod_id)) {
                                                                continue;
                                                            }
                                                            if ($aux_lr->cant_disponible > 0) {
                                                                $lotesRow->push($aux_lr);
                                                            }
                                                        }

                                                        /* Tope GLOBAL por lote. El mismo lote puede aparecer en varias
                                                           bodegas y cada fila traia el disponible completo: entre dos
                                                           filas se podia cargar 400 de un lote con 300. El JS usa este
                                                           valor para que la suma de las filas del mismo lote no lo
                                                           pase. Si el lote no vino por reasignacion queda null y manda
                                                           su propio cant_disponible, como siempre. */
                                                        foreach ($lotesRow as $aux_lrow) {
                                                            $aux_lrow->cant_disponible_lote =
                                                                \App\Models\ProdReasignacion::dispGlobalDeLotePorNvdet(
                                                                    $aux_lrow->opdetregprod_id, $aux_nvdet_id, $detalle->id
                                                                ) ?? $aux_lrow->cant_disponible;
                                                        }

                                                        /* Lo ya guardado para este item. Desde que la tabla guarda la
                                                           bodega, cada campo de la pantalla tiene su propia fila y se
                                                           recupera tal cual se escribio; las filas anteriores a eso no
                                                           traen bodega y se siguen repartiendo en orden. */
                                                        $aux_lotesGuardados = DB::table('despachosoldet_opdetregprod')
                                                            ->where('despachosoldet_id', $detalle->id)
                                                            ->get();
                                                        // Indice por lote, para el reparto de las filas sin bodega
                                                        $existingLoteAsig = $aux_lotesGuardados
                                                            ->filter(function ($f) { return !is_null($f->opdetregprod_id); })
                                                            ->keyBy('opdetregprod_id');

                                                        // CC: el estado de calidad por lote ya no se calcula aquí con una consulta
                                                        // propia — se resuelve por lote más abajo con
                                                        // CcValidacion::verificarCadenaDespacho(), igual que en crearsol, para que
                                                        // también detecte lotes SIN muestra CC cuando la etapa la requiere.
                                                    ?>
                                                    {{-- <tr name="fila{{$invbodegaproducto->id}}" id="fila{{$invbodegaproducto->id}}" sucursal_id="{{$spRow->bodega_sucursal_id}}"> --}}
                                                    <tr name="fila{{$invbodegaproducto->id}}" id="fila{{$invbodegaproducto->id}}" sucursal_id="{{$invbodegaproducto->invbodega->sucursal_id}}">
                                                        <td name="invbodegaproducto_idTD{{$invbodegaproducto->id}}" id="invbodegaproducto_idTD{{$invbodegaproducto->id}}" style="text-align:left;display:none;">
                                                            <input type="text" name="invbodegaproducto_producto_id[]" id="invbodegaproducto_producto_id{{$invbodegaproducto->id}}" class="form-control" value="{{$detalle->notaventadetalle->producto_id}}" style="display:none;"/>
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
                                                                {{-- El stock de la bodega NO es lo que esta nota de venta puede
                                                                     despachar: del lote pueden quedar 600 en bodega y haberle
                                                                     tocado solo 400, con las otras 200 reservadas o entregadas
                                                                     a otro cliente. Se muestra la suma de los lotes que se
                                                                     ofrecen abajo, que es lo que de verdad puede tomar; si no,
                                                                     el encabezado diria 600 y las filas sumarian 400. --}}
                                                                <?php
                                                                    /* Lo que este item puede tomar de la bodega: descuenta lo
                                                                       reservado o ya entregado a otros clientes. */
                                                                    $aux_dispBodega = \App\Models\StockReserva::disponibleParaNvdet(
                                                                        $invbodegaproducto->id, $detalle->notaventadetalle_id
                                                                    );
                                                                    /* Lo que queda despues de los lotes que se ofrecen abajo es
                                                                       material SIN lote: entro por carga manual de inventario y
                                                                       no por produccion. Tambien se puede pedir, asi que suma al
                                                                       disponible y se ofrece como una fila mas. */
                                                                    $aux_libreSinLote = round($aux_dispBodega - $lotesRow->sum('cant_disponible'), 2);
                                                                    if ($aux_libreSinLote < 0) { $aux_libreSinLote = 0; }
                                                                    /* Si la bodega se ofrece por una reserva a su nombre -o por
                                                                       ser una que esta solicitud ya usa-, el tope es eso y no
                                                                       todo lo que el item podria tomar de la bodega: el stock
                                                                       libre no se le ofrece. Mas abajo se respeta igual la
                                                                       regla de no bajar de lo ya guardado. */
                                                                    if (!empty($spRow->es_reserva)) {
                                                                        $aux_libreSinLote = min($aux_libreSinLote, (float) $spRow->cant_prod);
                                                                    }
                                                                    /* Cuanto de lo guardado se precarga en lotes DE ESTA bodega:
                                                                       lo usa la fila "sin lote" de mas abajo para saber cuanto
                                                                       de esta bodega se guardo sin lote. */
                                                                    $aux_preFillBodega = 0;
                                                                    $aux_stockDisp = count($lotesRow)
                                                                        ? $lotesRow->sum('cant_disponible') + $aux_libreSinLote
                                                                        : $spRow->cant_prod;
                                                                ?>
                                                                <p name="stockcantTD{{$aux_nfila}}-{{$invbodegaproducto->id}}" id="stockcantTD{{$aux_nfila}}-{{$invbodegaproducto->id}}" style="font-size: 11px;margin-bottom: 0px;">{{$aux_stockDisp}}</p>
                                                            </div>
                                                        </td>

                                                        {{-- <td  class="width90 tooltipsC" style="text-align:right;width:40% !important" title="Cant a despachar">
                                                            <input type="text" name="invcant[]" id="invcant{{$aux_nfila}}-{{$invbodegaproducto->id}}" class="form-control numerico bod{{$aux_nfila}} dismpadding invcant" onkeyup="sumbod({{$aux_nfila}},'{{$aux_nfila}}-{{$invbodegaproducto->id}}','SD')" style="text-align:right;" sucursal_id="{{$spRow->bodega_sucursal_id}}"/>
                                                        </td> --}}
                                                        <?php
                                                            // Cantidad realmente asignada A ESTA bodega (no el total del item).
                                                            // Mismo calculo que ya usa el bloque de productos sin fabricacion mas
                                                            // abajo: se busca la fila de despachosoldet_invbodegaproducto que
                                                            // corresponde a la bodega que se esta pintando. Antes se usaba
                                                            // $detalle->cantsoldesp (total del item), lo que repetia ese total en
                                                            // TODAS las bodegas al abrir el editar.
                                                            $aux_invcantBod = 0;
                                                            foreach($detalle->despachosoldet_invbodegaproductos as $dsdBodProd){
                                                                if ($dsdBodProd->invbodegaproducto_id == $invbodegaproducto->id){
                                                                    $aux_invcantBod = (($dsdBodProd->cant + $dsdBodProd->cantex) * -1);
                                                                }
                                                            }
                                                        ?>
                                                        <td  class="width90 tooltipsC" name="cantorddespF{{$invbodegaproducto->id}}" id="cantorddespF{{$invbodegaproducto->id}}" style="text-align:right;width: 40% !important;vertical-align:middle;" title='Cant a despachar'>
                                                            <div style='display:none;'>
                                                                {{-- Input oculto; su valor es actualizado por el handler de lote-cant-input en general.js --}}
                                                                <input type="text" name="invcant[]" id="invcant{{$aux_nfila}}-{{$invbodegaproducto->id}}" class="form-control numerico bod{{$aux_nfila}} dismpadding invcant" onkeyup="sumbod({{$aux_nfila}},'{{$aux_nfila}}-{{$invbodegaproducto->id}}','SD',{{$detalle->notaventadetalle->requiere_fabricacion}})" style="text-align:right;" sucursal_id="{{$invbodegaproducto->invbodega->sucursal_id}}" value="{{$aux_invcantBod}}"/>
                                                            </div>
                                                            <label style="margin:0;">{{$aux_invcantBod}}</label>
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
                                                        // Pre-cargar cantidad ya asignada a este lote (si existe en despachosoldet_opdetregprod)
                                                        $loteAsigExist = $existingLoteAsig->get($loteRow->opdetregprod_id);

                                                        /* Camino exacto: la fila guardada dice de que bodega salio, asi
                                                           que su cantidad es la de ESTE campo y se devuelve tal cual. */
                                                        $aux_filaBod = $aux_lotesGuardados->first(function ($f) use ($loteRow, $invbodegaproducto) {
                                                            return $f->opdetregprod_id == $loteRow->opdetregprod_id
                                                                and $f->invbodegaproducto_id == $invbodegaproducto->id;
                                                        });

                                                        if ($aux_filaBod) {
                                                            $cantPreFill   = (float) $aux_filaBod->cant;
                                                            $cantKgPreFill = (float) $aux_filaBod->cantkg;
                                                            $aux_preFillBodega += $cantPreFill;
                                                        } else {
                                                            /* Filas anteriores a que la tabla guardara la bodega: se
                                                               reparte lo guardado entre las bodegas donde el lote
                                                               aparece, sin repetirlo. Si se precargara el total en cada
                                                               una, un lote visible en dos bodegas se precargaria dos
                                                               veces y al guardar quedaria el doble. El reparto se apoya
                                                               en $aux_invcantBod, que si es por bodega. */
                                                            $aux_clvPre = $detalle->id . '-' . $loteRow->opdetregprod_id;
                                                            if (!isset($aux_prefillUsado[$aux_clvPre])) {
                                                                $aux_prefillUsado[$aux_clvPre] = 0;
                                                            }
                                                            $aux_pendPre = $loteAsigExist
                                                                ? max(0, (float) $loteAsigExist->cant - $aux_prefillUsado[$aux_clvPre])
                                                                : 0;
                                                            $cantPreFill = min(
                                                                $aux_pendPre,
                                                                (float) $loteRow->cant_disponible,
                                                                max(0, (float) $aux_invcantBod - $aux_preFillBodega)
                                                            );
                                                            $aux_prefillUsado[$aux_clvPre] += $cantPreFill;
                                                            $aux_preFillBodega             += $cantPreFill;
                                                            /* Los kilos, en la misma proporcion que las unidades precargadas */
                                                            $cantKgPreFill = ($loteAsigExist and (float) $loteAsigExist->cant > 0)
                                                                ? round((float) $loteAsigExist->cantkg * $cantPreFill / (float) $loteAsigExist->cant, 2)
                                                                : 0;
                                                        }
                                                        // Validación CC: recorre toda la cadena de producción del lote.
                                                        // Se usa el mismo CcValidacion::verificarCadenaDespacho() que en crearsol
                                                        // (form.blade.php) — la consulta SQL previa solo detectaba muestras CC
                                                        // rechazadas ya existentes, pero NO el caso de un lote cuya etapa REQUIERE
                                                        // muestra CC y no tiene ninguna registrada (ahí no había fila en
                                                        // ccregistmuestra, quedaba $ccInfoLote=null y el lote NO se bloqueaba).
                                                        $ccVal            = \App\Models\CcValidacion::verificarCadenaDespacho($loteRow->opdetregprod_id);
                                                        $loteBlocked      = $ccVal['bloqueado'];
                                                        $sinMuestraCC     = $ccVal['sin_muestra'];
                                                        $loteDesbloqueado = $ccVal['desbloqueado'];
                                                        $loteConObs       = $ccVal['con_obs'];
                                                        $rowBg            = $loteBlocked ? '#fff0f0' : ($loteDesbloqueado ? '#f0f8ff' : ($loteConObs ? '#fffbe6' : '#f5f5f5'));
                                                    ?>
                                                    <tr class="lote-prod-row" data-ibp="{{$invbodegaproducto->id}}" style="background:{{$rowBg}};">
                                                        <td style="display:none;">
                                                            {{-- Inputs ocultos: identifican el lote, la bodega y el NVdet al que pertenece --}}
                                                            <input type="hidden" name="opdetregprod_id[]"       value="{{$loteRow->opdetregprod_id}}"/>
                                                            <input type="hidden" name="opdetregprod_invbp[]"    value="{{$invbodegaproducto->id}}"/>
                                                            {{-- En edición, NVdet_id[] = despachosoldet_id, por eso usamos $detalle->id aquí --}}
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
                                                            {{-- Cantidad asignada a este lote; pre-cargada si ya fue guardada antes.
                                                                 readonly (no "disabled") cuando está bloqueado por CC: un input
                                                                 disabled no se envía en el submit, lo que desalinea por posición
                                                                 los arrays opdetregprod_id[]/opdetregprod_cant[] y hace que el valor
                                                                 tipeado para OTRO lote quede mal asignado a este lote bloqueado. --}}
                                                            <input type="text"
                                                                   name="opdetregprod_cant[]"
                                                                   id="opdetregprod_cant_{{$invbodegaproducto->id}}_{{$loteRow->opdetregprod_id}}"
                                                                   class="form-control dismpadding lote-cant-input"
                                                                   data-ibp="{{$invbodegaproducto->id}}"
                                                                   data-nfila="{{$aux_nfila}}"
                                                                   data-max="{{$loteRow->cant_disponible}}"
                                                                   {{-- El lote y su disponible GLOBAL: el mismo lote puede estar
                                                                        en varias bodegas y el tope es del lote, no de la fila.
                                                                        Sin esto se podia cargar 400 de un lote con 300. --}}
                                                                   data-lote="{{$loteRow->opdetregprod_id}}"
                                                                   data-max-lote="{{$loteRow->cant_disponible_lote ?? $loteRow->cant_disponible}}"
                                                                   data-cantkg-total="{{$loteRow->cantkg_disponible}}"
                                                                   data-stock-bodega="{{$spRow->cant_prod ?? 0}}"
                                                                   {{-- Lo que el item tiene pendiente MAS lo que esta solicitud ya
                                                                        tenia: al editar puede soltarlo y volver a repartirlo. Es el
                                                                        techo de todas las filas juntas. --}}
                                                                   data-max-item="{{$aux_saldo + $detalle->cantsoldesp}}"
                                                                   value="{{$loteBlocked ? '0' : $cantPreFill}}"
                                                                   style="text-align:right;font-size:11px;{{$loteBlocked ? 'background:#ffcccc;cursor:not-allowed;' : ''}}"
                                                                   @if($loteBlocked) readonly title="{{$sinMuestraCC && !$ccVal['rechazado'] ? 'Bloqueado — alguna etapa de la cadena requiere muestra CC: ' . implode(', ', $ccVal['etapas_problema']) : 'Bloqueado por rechazo CC — Muestra(s) #' . $ccVal['ids_bloqueados']}}" @endif />
                                                            {{-- Kg proporcionales calculados por JS --}}
                                                            <input type="hidden"
                                                                   name="opdetregprod_cantkg[]"
                                                                   id="opdetregprod_cantkg_{{$invbodegaproducto->id}}_{{$loteRow->opdetregprod_id}}"
                                                                   value="{{$cantKgPreFill}}"/>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                    @endforeach

                                                    {{-- Stock libre SIN lote de esta bodega -------------------------
                                                         Espejo de la fila que ofrece crearsol. Aqui cumple ademas algo
                                                         imprescindible: trae precargado lo que la solicitud ya guardo
                                                         sin lote. El campo de la bodega es la suma de las filas de
                                                         abajo, asi que sin esta fila bastaba tocar un lote para que el
                                                         campo se recalculara solo con los lotes y esa cantidad se
                                                         perdiera en silencio. Si no hay nada sin lote -ni guardado ni
                                                         libre- la fila no aparece y la pantalla queda igual que antes. --}}
                                                    <?php
                                                        /* Lo guardado sin lote: lo que la bodega lleva menos lo que esta
                                                           declarado en lotes. OJO: despachosoldet_opdetregprod no guarda
                                                           la bodega, asi que con el item repartido en VARIAS bodegas esta
                                                           resta no se puede atribuir por bodega y queda en 0; con una
                                                           sola bodega -el caso normal- es exacto. */
                                                        /* La cantidad sin lote de ESTA bodega, tal como se guardo. */
                                                        $aux_filaSinLote = $aux_lotesGuardados->first(function ($f) use ($invbodegaproducto) {
                                                            return is_null($f->opdetregprod_id)
                                                                and $f->invbodegaproducto_id == $invbodegaproducto->id;
                                                        });
                                                        if ($aux_filaSinLote) {
                                                            $aux_sinLoteGuardado = (float) $aux_filaSinLote->cant;
                                                        } else {
                                                            /* Solicitudes guardadas antes de que esto se registrara: se
                                                               deduce restando lo que esta GUARDADO en la trazabilidad -no
                                                               lo que se alcanzo a precargar en pantalla-, porque un lote
                                                               guardado que la pantalla ya no ofrece precarga 0 y su
                                                               cantidad se habria presentado como "sin lote" teniendo lote.
                                                               Con el item en varias bodegas la resta no se puede atribuir
                                                               por bodega y queda en 0, que es el lado seguro. */
                                                            $aux_sinLoteGuardado = round($aux_invcantBod - $existingLoteAsig->sum('cant'), 2);
                                                        }
                                                        if ($aux_sinLoteGuardado < 0) { $aux_sinLoteGuardado = 0; }
                                                        /* El tope nunca puede quedar por debajo de lo ya guardado: el JS
                                                           recorta al tope y, si fuera menor, al tocar cualquier lote se
                                                           bajaria sola la cantidad que la solicitud ya tenia. */
                                                        $aux_topeSinLote = max($aux_libreSinLote, $aux_sinLoteGuardado);
                                                    ?>
                                                    {{-- O cuando la bodega entro por una reserva a su nombre o por estar
                                                         ya usada por esta solicitud: ahi no hay lotes que acompanar y esta
                                                         es la unica fila. --}}
                                                    @if($aux_topeSinLote > 0 and (count($lotesRow) or !empty($spRow->es_reserva)))
                                                    <tr class="lote-prod-row" data-ibp="{{$invbodegaproducto->id}}" style="background:#eef7ee;">
                                                        <td style="display:none;">
                                                            {{-- opdetregprod_id en 0: el guardado la reconoce como "sin lote" y
                                                                 no la inserta en la trazabilidad. Los tres campos se emiten
                                                                 igual que en las filas de lote: los arrays se alinean por
                                                                 posicion y si faltara uno, el valor tipeado aqui se le
                                                                 asignaria a otro lote. --}}
                                                            <input type="hidden" name="opdetregprod_id[]"       value="0"/>
                                                            <input type="hidden" name="opdetregprod_invbp[]"    value="{{$invbodegaproducto->id}}"/>
                                                            <input type="hidden" name="opdetregprod_nvdet_id[]" value="{{$detalle->id}}"/>
                                                        </td>
                                                        <td colspan="2" style="padding:1px 6px;font-size:10px;text-align:left;vertical-align:middle;">
                                                            <span style="color:#888;">&#8627; Sin lote</span>
                                                            <span style="color:#bbb;" title="Material de esta bodega que no proviene de un lote de produccion (carga manual de inventario). Se despacha sin trazabilidad de lote.">(disp. {{$aux_topeSinLote}} un)</span>
                                                        </td>
                                                        <td style="padding:1px 2px;vertical-align:middle;">
                                                            <input type="text"
                                                                   name="opdetregprod_cant[]"
                                                                   id="opdetregprod_cant_{{$invbodegaproducto->id}}_0"
                                                                   class="form-control dismpadding lote-cant-input"
                                                                   data-ibp="{{$invbodegaproducto->id}}"
                                                                   data-nfila="{{$aux_nfila}}"
                                                                   data-max="{{$aux_topeSinLote}}"
                                                                   {{-- Sin data-lote ni data-max-lote: no hay lote al que ponerle
                                                                        tope global. Y el tope de bodega es lo que el item puede
                                                                        tomar en total, no solo el stock de produccion: esto
                                                                        justamente no viene de produccion. --}}
                                                                   data-cantkg-total="0"
                                                                   {{-- Si la bodega viene de una reserva a su nombre, el techo es
                                                                        lo reservado; nunca por debajo de lo ya guardado. --}}
                                                                   data-stock-bodega="{{!empty($spRow->es_reserva) ? max(min($aux_dispBodega, (float) $spRow->cant_prod), $aux_invcantBod) : max($aux_dispBodega, $aux_invcantBod)}}"
                                                                   {{-- Mismo techo que las filas de lote --}}
                                                                   data-max-item="{{$aux_saldo + $detalle->cantsoldesp}}"
                                                                   value="{{$aux_sinLoteGuardado}}"
                                                                   style="text-align:right;font-size:11px;" />
                                                            {{-- Se emite para no desalinear los arrays; sin lote no hay kg de lote --}}
                                                            <input type="hidden" name="opdetregprod_cantkg[]" value="0"/>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                    @endif
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            {{-- No hay producción en bodega: mostrar mensaje dentro del mismo formato de tabla --}}
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
                                                                        {{-- Lo disponible, no el stock bruto: si hay 600 en bodega pero
                                                                             200 estan reservadas para otro cliente, esta nota de venta
                                                                             solo puede tomar 400. Mismo criterio que el reporte de
                                                                             stock, para que los dos digan lo mismo. --}}
                                                                        <?php
                                                                            $aux_stockDisp = \App\Models\StockReserva::disponibleParaNvdet($invbodegaproducto->id, $aux_nvdet_id);
                                                                            /* Nunca mas de lo que hay fisicamente en esta bodega. */
                                                                            $aux_stockDisp = min($aux_stockDisp, $existencia["stock"]["cant"]);
                                                                        ?>
                                                                        <div name="stockcantTD{{$aux_nfila}}-{{$invbodegaproducto->id}}" id="stockcantTD{{$aux_nfila}}-{{$invbodegaproducto->id}}" class="centrarhorizontal">
                                                                            {{$aux_stockDisp}}
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
                                        @endif
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="unidadmedida_id[]" id="unidadmedida_id{{$aux_nfila}}" class="form-control" value="4" style="display:none;"/>
                                    </td>
                                    <td name="nombreProdTD{{$aux_nfila}}" id="nombreProdTD{{$aux_nfila}}">
                                        {{$aux_producto_nombre}}
                                        {{-- @if ($aux_staAT)
                                            <br><span class="small-text">{{$aux_atribAcuTec}}</span>
                                        @endif --}}
                                    </td>
                                    <td name="cla_nombreTD{{$aux_nfila}}" id="cla_nombreTD{{$aux_nfila}}" style="display:none;">
                                        {{$aux_cla_sello_nombre}}
                                    </td>
                                    <td name="diamextmmTD{{$aux_nfila}}" id="diamextmmTD{{$aux_nfila}}" style="text-align:right;display:none;">
                                        {{$aux_ancho}}
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="diamextmm[]" id="diamextmm{{$aux_nfila}}" class="form-control" value="{{$detalle->notaventadetalle->producto->diametro}}" style="display:none;"/>
                                    </td>
                                    <td name="longTD{{$aux_nfila}}" id="longTD{{$aux_nfila}}" style="text-align:right;display:none;">
                                        {{$aux_largo}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="long[]" id="long{{$aux_nfila}}" class="form-control" value="{{$aux_largo}}" style="display:none;"/>
                                    </td>
                                    <td name="espesorTD{{$aux_nfila}}" id="espesorTD{{$aux_nfila}}" style="text-align:right;display:none;">
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
                                    <td name="tipounionTD{{$aux_nfila}}" id="tipounionTD{{$aux_nfila}}" style="display:none;"> 
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
                                <td colspan="3" style="text-align:right"><b>Total Kg</b></td>
                                <td id="totalkg" name="totalkg" style="text-align:right">0.00</td>
                                <td colspan="1" style="text-align:right"><b>Neto</b></td>
                                <td id="tdneto" name="tdneto" style="text-align:right">0.00</td>
                            </tr>
                            <tr id="triva" name="triva">
                                <td colspan="11" style="text-align:right"><b>IVA {{$empresa->iva}}%</b></td>
                                <td id="tdiva" name="tdiva" style="text-align:right">0.00</td>
                            </tr>
                            <tr id="trtotal" name="trtotal">
                                <td colspan="11" style="text-align:right"><b>Total</b></td>
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