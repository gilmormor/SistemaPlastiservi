@include('generales.modalpdf')
<input type="hidden" name="updated_at" id="updated_at" value="{{old('updated_at', $data->updated_at ?? '')}}">
<input type="hidden" name="aux_sta" id="aux_sta" value="{{$aux_sta}}">
<input type="hidden" name="aux_fechaphp" id="aux_fechaphp" value="{{old('aux_fechaphp', $fecha ?? '')}}">
<input type="hidden" name="aux_iva" id="aux_iva" value="{{$empresa->iva}}">
<input type="hidden" name="direccioncot" id="direccioncot" value="{{old('direccioncot', $data->direccioncot ?? '')}}">
<input type="hidden" name="cliente_id" id="cliente_id" value="{{old('cliente_id', $data->cliente_id ?? '')}}">
<input type="hidden" name="comuna_id" id="comuna_id" value="{{old('comuna_id', $data->comuna_id ?? '')}}">
<input type="hidden" name="formapago_id" id="formapago_id" value="{{old('formapago_id', $data->formapago_id ?? '')}}">
<input type="hidden" name="plazopago_id" id="plazopago_id" value="{{old('plazopago_id', $data->plazopago_id ?? '')}}">
<input type="hidden" name="giro_id" id="giro_id" value="{{old('giro_id', $data->giro_id ?? '')}}">


<input type="hidden" name="staapronv" id="staapronv" value="{{old('staapronv', session('aux_aproNV') ?? '')}}">
@if($aux_sta==1)
    <input type="hidden" name="vendedor_id" id="vendedor_id" value="{{old('vendedor_id', $vendedor_id ?? '')}}">
@else
    <input type="hidden" name="vendedor_id" id="vendedor_id" value="{{old('vendedor_id', $data->vendedor_id ?? '')}}">
@endif
<input type="hidden" name="region_id" id="region_id" value="{{old('region_id', $data->region_id ?? '')}}">
<input type="hidden" name="provincia_id" id="provincia_id" value="{{old('provincia_id', $data->provincia_id ?? '')}}">
<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">

<input type="hidden" name="neto" id="neto" value="{{old('neto', $data->neto ?? '')}}">
<input type="hidden" name="piva" id="piva" value="{{old('piva', $empresa->iva ?? '')}}">
<input type="hidden" name="iva" id="iva" value="{{old('iva', $data->iva ?? '')}}">
<div class="form-group col-xs-4 col-sm-4" style="display:none;">
    <label for="total" class="control-label requerido" data-toggle='tooltip' title="Total Documento">Total Documento</label>
    <input type="hidden" name="total" id="total" value="{{old('total', $data->total ?? '')}}"class="form-control" style="text-align:right;" readonly required>
</div>
<input type="hidden" name="imagen" id="imagen" value="{{old('imagen', $data->oc_file ?? '')}}">

<?php
    $selecmultprod = false;
?>
<input type="hidden" name="selecmultprod" id="selecmultprod" value="{{$selecmultprod}}">
<div class="form-group col-xs-4 col-sm-4" style="display:none;">
    <label for="oc_fileaux" class="control-label requerido" data-toggle='tooltip' title="Adjuntar Orden de compra">Adjuntar Orden de compra</label>
    <input type="hidden" name="oc_fileaux" id="oc_fileaux" value="" class="form-control" style="text-align:right;">
</div>


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
    $aux_concot = false;
    if ($aux_sta==2 and $data->cotizacion_id and $data->id){
        $disabledcliente = ' disabled ';
        $aux_concot = true;
    }

?>
<div class="row">
    <div class="col-xs-12 col-sm-9">
        <div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
            <div class="box-header with-border">
                <div class="box-body">
                    <div class="row">
                        @if (($aux_sta==2 and $data->cotizacion_id and $data->id) or $aux_sta==3)
                            <div class="form-group col-xs-12 col-sm-1">
                                <label for="cotizacion_id" class="control-label requerido" data-toggle='tooltip' title="Num Cotización">Cot</label>
                                @if($aux_sta==2)
                                    <input type="text" name="cotizacion_id" id="cotizacion_id" class="form-control" value="{{old('cotizacion_id', $data->cotizacion_id ?? '')}}" required readonly/>
                                @else
                                    <input type="text" name="cotizacion_id" id="cotizacion_id" class="form-control" value="{{old('cotizacion_id', $data->id ?? '')}}" required readonly/>
                                @endif
                            </div>            
                        @endif
                        <div class="form-group col-xs-12 col-sm-3">
                <!--
                            <label for="rut" class="control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
                            <div class="input-group">
                            <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $clienteselec[0]->rut ?? '')}}" title="F2 Buscar" placeholder="F2 Buscar" required {{$disabledReadOnly}}/>
                                <span class="input-group-btn">
                                    @if (session('aux_aproNV')=='0')
                                        <a id="btnbuscarcliente" name="btnbuscarcliente" href="#" class="btn btn-flat" data-toggle='tooltip' title="Buscar">
                                            <i class="fa fa-search"></i>
                                        </a>
                                    @endif
                                </span>
                            </div>
                -->
                            <label for="rut" class="control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
                            <div class="input-group">
                                <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $clienteselec[0]->rut ?? '')}}" onkeyup="llevarMayus(this);" title="F2 Buscar" placeholder="F2 Buscar" maxlength="12" required {{$disabledReadOnly}} {{$disabledcliente}}/>
                                <span class="input-group-btn">
                                    @if (session('aux_aproNV')=='0')
                                        <button class="btn btn-default" type="button" id="btnbuscarcliente" name="btnbuscarcliente" data-toggle='tooltip' title="Buscar" {{$disabledcliente}}>Buscar</button>
                                    @endif
                                </span>
                            </div>
            
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
                        <!--
                        <div class="form-group col-xs-12 col-sm-4">
                            <label for="clientedirec_id" class="control-label">Dirección adicional</label>
                            <select name="clientedirec_id" id="clientedirec_id" class="form-control select2 clientedirec_id" data-live-search='true' disabled readonly>
                                <option value="">Seleccione...</option>
                                @if ($aux_sta==2 or $aux_sta==3)
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
                                        {{$clientedirec->direccion}}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        -->
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="comuna_idD" class="control-label requerido">Comuna</label>
                            <input type="text" name="comuna_idDT" id="comuna_idDT" class="form-control"  value="{{old('comuna_idDT', $data->comuna->nombre . " " .$data->vendedor->persona->apellido ?? '')}}" readonly required disabled>
                            <div style="display:none;">
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
                    </div>
            
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="vendedor_idD" class="control-label requerido">Vendedor</label>
                            <input type="text" name="vendedor_idDT" id="vendedor_idDT" class="form-control"  value="{{old('vendedor_idDT', $data->vendedor->persona->nombre . " " .$data->vendedor->persona->apellido ?? '')}}" readonly required disabled>
                            <div style="display:none;">
                                <select name="vendedor_idD" id="vendedor_idD" class="form-control select2 vendedor_idD" required {{(isset($data) ? ($data->vendedor_id == $vendedor_id ? "readonly disabled" : "") : "readonly disabled")  }}  readonly disabled>
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
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="plazopago_idD" class="control-label requerido">Plazo</label>
                            <input type="text" name="plazopago_idDT" id="plazopago_idDT" class="form-control"  value="{{old('plazopago_idDT', $data->plazopago->descripcion ?? '')}}" readonly required disabled>
                            <div style="display:none;">
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
                        </div>
            
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="formapago_idD" class="control-label requerido">Forma de Pago</label>
                            <input type="text" name="formapago_idDT" id="formapago_idDT" class="form-control"  value="{{old('formapago_idDT', $data->formapago->descripcion ?? '')}}" readonly required disabled>
                            <div style="display:none;">
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

                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="giro_idD" class="control-label requerido">Giro</label>
                            <input type="text" name="giroDT" id="giroDT" class="form-control" value="{{old('giroDT', $data->cliente->giro ?? '')}}" readonly required disabled>
                            <div style="display:none;">
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
                        </div>
            
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="tipoentrega_id" class="control-label requerido">Tipo Entrega</label>
                            <input type="text" name="tipoentrega_idT" id="tipoentrega_idT" class="form-control" value="{{old('tipoentrega_idT', $data->tipoentrega->nombre ?? '')}}" readonly required disabled>
                            <div style="display:none;">
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
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label for="plazoentrega" class="control-label requerido">Plazo Ent.</label>
                            <input type="text" name="plazoentrega" id="plazoentrega" class="form-control"  value="{{old('plazoentrega', $data->plazoentrega ?? '')}}" readonly required {{$enableCamposCot}}>
                        </div>
                        
            
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="sucursal_id" class="control-label requerido">Sucursal</label>
                            <input type="text" name="sucursal_idT" id="sucursal_idT" class="form-control" value="{{old('sucursal_idT', $data->sucursal->nombre ?? '')}}" readonly required disabled>
                            <div style="display:none;">
                                <select name="sucursal_id" id="sucursal_id" class="form-control select2 sucursal_id" data-live-search='true' required>
                                    <option value=''>Seleccione...</option>
                                    @if (isset($data))
                                        @foreach($tablas['sucursales'] as $sucursal)
                                            <option
                                                value="{{$sucursal->id}}"
                                                @if (isset($data->sucursal_id) and ($data->sucursal_id==$sucursal->id))
                                                    {{'selected'}}
                                                @endif
                                                >
                                                {{$sucursal->nombre}}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>    
                            </div>
                        </div>
                
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="lugarentrega" class="control-label requerido">Lugar de Entrega</label>
                            <input type="text" name="lugarentrega" id="lugarentrega" class="form-control" value="{{old('lugarentrega', $data->lugarentrega ?? '')}}" required placeholder="Lugar de Entrega" readonly required disabled/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="comunaentrega_id" class="control-label requerido">Comuna Entrega</label>
                            <input type="text" name="comunaentrega_idT" id="comunaentrega_idT" class="form-control" value="{{old('comunaentrega_idT', $data->comunaentrega->nombre ?? '')}}" readonly required disabled>
                            <div style="display:none;">
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
                        </div>
                        <!--
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="btnfotooc" class="control-label">Cargar OrdenCompra</label>
                            <button type="button" class="form-control btn btn-primary" id="btnfotooc" name="btnfotooc" title="Guardar">Cargar OrdenCompra</button>
                        </div>-->
                        
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="contacto" class="control-label requerido">Contacto</label>
                            <input type="text" name="contacto" id="contacto" class="form-control" value="{{old('contacto', $data->contacto ?? '')}}" required placeholder="Contacto Entrega" readonly required disabled/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-3">
                            <label for="contactotelf" class="control-label requerido">Teléfono</label>
                            <input type="text" name="contactotelf" id="contactotelf" class="form-control" value="{{old('contactotelf', $data->contactotelf ?? '')}}" required placeholder="Teléfono Contacto Entrega" readonly required disabled/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-4">
                            <label for="contactoemail" class="control-label requerido">Email</label>
                            <input type="email" name="contactoemail" id="contactoemail" class="form-control" value="{{old('contactoemail', $data->contactoemail ?? '')}}" required placeholder="Email Contacto Entrega" readonly required disabled/>
                        </div>
                        <div class="form-group col-xs-12 col-sm-5">
                            <label for="observacion" class="control-label">Observaciones</label>
                            <input type="text" name="observacion" id="observacion" class="form-control" value="{{old('observacion', $data->observacion ?? '')}}" placeholder="Observaciones" readonly disabled/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
    <div class="col-xs-12 col-sm-3">
        <div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
            <div class="box-header with-border">
                <div class="box-body">
                    <div class="row">
                        <div id="group_oc_id" class="form-group col-xs-12 col-sm-12">
                            <label id="lboc_id" name="lboc_id" for="oc_id" class="control-label">Nro OrdenCompra</label>
                            <div class="input-group">
                                <input type="text" name="oc_id" id="oc_id" class="form-control" value="{{old('oc_id', $data->oc_id ?? '')}}" placeholder="Nro Orden de Compra" maxlength="18" readonly disabled/>
                                <!--<span class="input-group-btn">
                                    <button class="btn btn-default" type="button" id="btnfotooc" name="btnfotooc" data-toggle='tooltip' title="Cargar Imagen OC" {{$enableCamposCot}}>Examinar...</button>
                                </span>-->
                            </div>
                        </div>
                        <div id="group_oc_file" class="form-group col-xs-12 col-sm-12">
                            <label id="lboc_oc_file" name="lboc_oc_file" for="oc_file" class="control-label">Adjuntar OC</label>
                            <div class="input-group">
                                <!--<input type="file" name="oc_file" id="oc_file" class="form-control" data-initial-preview='{{isset($data->oc_file) ? Storage::url("imagenes/notaventa/$data->oc_file") : ""}}' accept="image/*"/>-->
                                <input type="file" name="oc_file" id="oc_file" class="form-control" data-initial-preview='{{isset($data->oc_file) ? Storage::url("imagenes/notaventa/$data->oc_file") : ""}}' accept="*"/>
                            </div>
                            <span id="oc_file-error" style="color:#dd4b39;display: none;">Este campo es obligatorio.</span>
                        </div>
                        <!--
                        @if ($aux_sta==2 or $aux_sta==3)
                            @if ($data->oc_file)
                                <div class="form-group col-xs-12 col-sm-1">
                                    <label for="btnverfoto" class="control-label requerido">Ver</label>
                                    <button class="btn btn-default" type="button" id="btnverfoto" name="btnverfoto" data-toggle='tooltip' title="Ver Archivo">Ver OC</button>
                                </div>                    
                            @endif
                        @endif
                        -->
                    </div>
                    <!--
                            <div class="form-group col-xs-12 col-sm-12">
                                <div class="form-group">
                                    <label for="foto" class="control-label">Orden de Compra</label>
                                    <input type="file" name="oc_file" id="oc_file" class="form-control" data-initial-preview="{{isset($data->imagen) ? Storage::url("imagenes/certificado/$data->imagen") : "http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=Foto+Certificado"}}" accept="image/*"/>
                                </div>
                            </div>
                    -->


                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-danger" style="margin-bottom: 0px;margin-top: 2px;">
    <div class="box-header with-border">
        <h3 class="box-title">Detalle</h3>
        @if(($aux_sta==1 or $aux_sta==2) and $aux_concot == false) <!--Estatus en 0 si puede incluir -->
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
                            <th style="text-align:center;">Cod</th>
                            <th style="display:none;" class="width30">ID</th>
                            <th style="display:none;">NotaVentaDetalle_ID</th>
                            <th style="display:none;">cotizacion_ID</th>
                            <th style="display:none;">Codigo Producto</th>
                            <th style="display:none;">Cód Int</th>
                            <th style="display:none;">CódInterno</th>
                            <th>Cant</th>
                            <th style="display:none;">Cant</th>
                            <th>Nombre</th>
                            <th style="display:none;">UnidadMedida</th>
                            <th>Clase<br>Sello</th>
                            <th>Diam<br>Ancho</th>
                            <th style="display:none;">Diametro</th>
                            <th>Largo</th>
                            <th style="display:none;">Largo</th>
                            <th>Esp</th>
                            <th style="display:none;">Espesor</th>
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
                            @if($aux_concot==false)
                                <th class="width70"></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if ($aux_sta==2 or $aux_sta==3)
                            <?php $aux_nfila = 0; $i = 0;?>
                            @foreach($detalles as $detalle)
                                <?php 
                                $aux_nfila++; 
                                    $acuerdotecnico = null;
                                    if ($detalle->producto->tipoprod == 1){
                                        //SI 
                                        //dd(isset($detalle->acuerdotecnico));
                                        if(isset($detalle->acuerdotecnico) and $detalle->acuerdotecnico){
                                            $acuerdotecnico = $detalle->acuerdotecnico;
                                        }else{
                                            if(isset($detalle->acuerdotecnicotempunoauno) and $detalle->acuerdotecnicotempunoauno){
                                                $acuerdotecnico = $detalle->acuerdotecnicotempunoauno;
                                            }else{
                                                $acuerdotecnico = $detalle->cotizaciondetalle->acuerdotecnicotempunoauno;
                                            }
                                            //dd($acuerdotecnico);
                                        }
                                    }
                                    $aux_ancho = $detalle->producto->diametro;
                                    $aux_espesor = $detalle->espesor;
                                    $aux_largo = $detalle->largo;
                                    $aux_cla_sello_nombre = isset($detalle->producto->claseprod->cla_nombre) ? $detalle->producto->claseprod->cla_nombre : "";
                                    $aux_producto_nombre = $detalle->producto->nombre;
                                    $aux_categoria_nombre = $detalle->producto->categoriaprod->nombre;
                                    $aux_staAT = false;
            						$aux_atribAcuTec = "";
                                    //dd($detalle);
                                    if ($detalle->acuerdotecnicotempunoauno){
                                        $AcuTec = $detalle->acuerdotecnicotempunoauno;
                                        $aux_producto_nombre = nl2br($detalle->producto->categoriaprod->nombre . ", " . $AcuTec->at_desc);
                                        $aux_staAT = true;
                                    }
                                    if ($detalle->producto->acuerdotecnico != null){
                                        $AcuTec = $detalle->producto->acuerdotecnico;
                                        $aux_producto_nombre = nl2br($AcuTec->producto->categoriaprod->nombre . ", " . $AcuTec->at_desc);
                                        $aux_staAT = true;
                                    }
                                    if($aux_staAT){
                                        $aux_atribAcuTec = $AcuTec->color->nombre . " " . $AcuTec->materiaprima->nombre . " " . $AcuTec->at_impresoobs;
                                        $aux_ancho = $AcuTec->at_ancho . " " . ($AcuTec->at_ancho ? $AcuTec->anchounidadmedida->nombre : "");
                                        $aux_largo = $AcuTec->at_largo . " " . ($AcuTec->at_largo ? $AcuTec->largounidadmedida->nombre : "");
                                        $aux_espesor = $AcuTec->at_espesor;
                                        $aux_cla_sello_nombre = $AcuTec->claseprod->cla_nombre;
                                    }
                                ?>
                                <tr name="fila{{$aux_nfila}}" id="fila{{$aux_nfila}}">
                                    <td name="producto_idTDT{{$aux_nfila}}" id="producto_idTDT{{$aux_nfila}}" style="text-align:center;" categoriaprod_id="{{$detalle->producto->categoriaprod_id}}">
                                        @if ($detalle->producto->tipoprod == 1)
                                            <a class="btn-accion-tabla btn-sm tooltipsC" title="" onclick="genpdfAcuTecTemp({{$acuerdotecnico->id}},{{$data->cliente_id}},1)" data-original-title="Acuerdo Técnico PDF">
                                                {{$detalle->producto_id}}
                                            </a>
                                        @else
                                            @if ($detalle->producto->acuerdotecnico)
                                                <a class="btn-accion-tabla btn-sm tooltipsC" title="" onclick="genpdfAcuTec({{$detalle->producto->acuerdotecnico->id}},{{$data->cliente_id}},1)" data-original-title="Acuerdo Técnico PDF">
                                                    {{$detalle->producto_id}}
                                                </a>
                                            @else
                                                {{$detalle->producto_id}}
                                            @endif
    
                                        @endif
    
                                        @if (false)
                                            @if ($detalle->producto->tipoprod == 1)
                                                <a class="btn-accion-tabla tooltipsC" title="Acuerdo tecnico" onclick="crearEditarAcuTec({{$aux_nfila}})">
                                                    @if ($acuerdotecnico == null)
                                                        <i id="icoat{{$aux_nfila}}" class="fa fa-cog text-red girarimagen"></i>
                                                    @else
                                                        <i id="icoat{{$aux_nfila}}" class="fa fa-cog text-aqua girarimagen"></i>
                                                    @endif
                                                </a>
                                            @endif
                                            @if ($detalle->producto->acuerdotecnico)
                                                <a class="btn-accion-tabla tooltipsC" title="Acuerdo tecnico" onclick="genpdfAcuTec({{$detalle->producto_id}})">
                                                    <i id="icoat{{$aux_nfila}}" class="fa fa-cog text-aqua girarimagen"></i>
                                                </a>
                                            @endif
                                            
                                        @endif
                                    </td>
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
                                    <td name="producto_idTD{{$aux_nfila}}" id="producto_idTD{{$aux_nfila}}" style="display:none;">
                                        <input type="text" name="producto_id[]" id="producto_id{{$aux_nfila}}" class="form-control" value="{{$detalle->producto_id}}" style="display:none;"/>
                                    </td>
                                    <td style="display:none;" name="codintprodTD{{$aux_nfila}}" id="codintprodTD{{$aux_nfila}}">
                                        {{$detalle->producto->codintprod}}
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="codintprod[]" id="codintprod{{$aux_nfila}}" class="form-control" value="{{$detalle->producto->codintprod}}" style="display:none;"/>
                                    </td>
                                    <td name="cantTD{{$aux_nfila}}" id="cantTD{{$aux_nfila}}" style="text-align:right">
                                        @if ($aux_sta==2)
                                            {{$detalle->cant}}
                                        @else 
                                            {{$detalle->cant - $detalle->cantusada}}
                                        @endif
                                    </td>
                                    <td style="text-align:right;display:none;">
                                        @if ($aux_sta==2)
                                            <input type="text" name="cant[]" id="cant{{$aux_nfila}}" class="form-control" value="{{$detalle->cant}}" style="display:none;"/>
                                        @else 
                                            <input type="text" name="cant[]" id="cant{{$aux_nfila}}" class="form-control" value="{{$detalle->cant - $detalle->cantusada}}" style="display:none;"/>
                                        @endif
                                    </td>
                                    <td name="nombreProdTD{{$aux_nfila}}" id="nombreProdTD{{$aux_nfila}}">
                                        {{$aux_producto_nombre}}
                                        @if ($aux_staAT)
                                            <br><span class='small-text'>{{$aux_atribAcuTec}}</span>
                                        @endif        
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="unidadmedida_id[]" id="unidadmedida_id{{$aux_nfila}}" class="form-control"  value="{{$detalle->unidadmedida_id}}" style="display:none;"/>
                                    </td>
                                    <td name="cla_nombreTD{{$aux_nfila}}" id="cla_nombreTD{{$aux_nfila}}">
                                        {{$aux_cla_sello_nombre}}
                                    </td>
                                    <td name="diamextmmTD{{$aux_nfila}}" id="diamextmmTD{{$aux_nfila}}" style="text-align:right">
                                        {{$aux_ancho}}
                                    </td>
                                    <td style="display:none;">
                                        <input type="text" name="diamextmm[]" id="diamextmm{{$aux_nfila}}" class="form-control" value="{{$detalle->producto->diametro}}" style="display:none;"/>
                                    </td>
                                    <td name="longTD{{$aux_nfila}}" id="longTD{{$aux_nfila}}" style="text-align:right">
                                        {{$aux_largo}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="long[]" id="long{{$aux_nfila}}" class="form-control" value="{{$detalle->largo}}" style="display:none;"/>
                                    </td>
                                    <td name="espesorTD{{$aux_nfila}}" id="espesorTD{{$aux_nfila}}" style="text-align:right">
                                        {{number_format($aux_espesor, 3, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="espesor[]" id="espesor{{$aux_nfila}}" class="form-control" value="{{$detalle->espesor}}" style="display:none;"/>
                                        <input type="text" name="ancho[]" id="ancho{{$aux_nfila}}" class="form-control" value="{{$detalle->ancho}}" style="display:none;"/>
                                        <input type="text" name="obs[]" id="obs{{$aux_nfila}}" class="form-control" value="{{$detalle->obs}}" style="display:none;"/>
                                    </td>
                                    <td name="pesoTD{{$aux_nfila}}" id="pesoTD{{$aux_nfila}}" style="text-align:right;">
                                        {{$detalle->producto->peso}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="peso[]" id="peso{{$aux_nfila}}" class="form-control" value="{{$detalle->producto->peso}}" style="display:none;"/>
                                    </td>
                                    <td name="tipounionTD{{$aux_nfila}}" id="tipounionTD{{$aux_nfila}}"> 
                                        {{$detalle->producto->tipounion}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="tipounion[]" id="tipounion{{$aux_nfila}}" class="form-control" value="{{$detalle->producto->tipounion}}" style="display:none;"/>
                                    </td>
                                    <td name="descuentoTD{{$aux_nfila}}" id="descuentoTD{{$aux_nfila}}" style="text-align:right">
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
                                    <td name="precioxkiloTD{{$aux_nfila}}" id="precioxkiloTD{{$aux_nfila}}" style="text-align:right"> 
                                        {{number_format($detalle->precioxkilo, 3, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="precioxkilo[]" id="precioxkilo{{$aux_nfila}}" class="form-control" value="{{$detalle->precioxkilo}}" style="display:none;"/>
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="precioxkiloreal[]" id="precioxkiloreal{{$aux_nfila}}" class="form-control" value="{{$detalle->precioxkiloreal}}" style="display:none;"/>
                                    </td>
                                    <td name="totalkilosTD{{$aux_nfila}}" id="totalkilosTD{{$aux_nfila}}" style="text-align:right">
                                        {{number_format($detalle->totalkilos, 2, ',', '.')}}
                                    </td>
                                    <td style="text-align:right;display:none;"> 
                                        <input type="text" name="totalkilos[]" id="totalkilos{{$aux_nfila}}" class="form-control" value="{{$detalle->totalkilos}}" style="display:none;"/>
                                    </td>
                                    <td name="subtotalCFTD{{$aux_nfila}}" id="subtotalCFTD{{$aux_nfila}}" class="subtotalCF" style="text-align:right"> 
                                        {{number_format($detalle->subtotal, 0, ',', '.')}}
                                    </td>
                                    <td class="subtotalCF" style="text-align:right;display:none;"> 
                                        <input type="text" name="subtotal[]" id="subtotal{{$aux_nfila}}" class="form-control" value="{{$detalle->subtotal}}" style="display:none;"/>
                                    </td>
                                    <td name="subtotalSFTD{{$aux_nfila}}" id="subtotalSFTD{{$aux_nfila}}" class="subtotal" style="text-align:right;display:none;">
                                        {{$detalle->subtotal}}
                                    </td>
                                    @if(($aux_sta==1 or $aux_sta==2) and $aux_concot == false)
                                        <td>
                                            <a href="#" class="btn-accion-tabla tooltipsC" title="Editar este registro" onclick="editarRegistro({{$aux_nfila}})">
                                            <i class="fa fa-fw fa-pencil"></i>
                                            </a>
                                            <a href="#" class="btn-accion-tabla eliminar tooltipsC" title="Eliminar este registro" onclick="eliminarRegistro({{$aux_nfila}})">
                                            <i class="fa fa-fw fa-trash text-danger"></i></a>
                                        </td>
                                    @endif
                                </tr>
                                <?php $i++;?>
                            @endforeach
                            <tr id="trneto" name="trneto">
                                <td colspan="13" style="text-align:right"><b>Neto</b></td>
                                <td id="tdneto" name="tdneto" style="text-align:right">0,00</td>
                            </tr>
                            <tr id="triva" name="triva">
                                <td colspan="13" style="text-align:right"><b>IVA {{$empresa->iva}}%</b></td>
                                <td id="tdiva" name="tdiva" style="text-align:right">0,00</td>
                            </tr>
                            <tr id="trtotal" name="trtotal">
                                <td colspan="13" style="text-align:right"><b>Total</b></td>
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

@include('generales.calcprecioprodsn')
@if (session('aux_aproNV')=='1')
    @include('generales.aprobarcotnv')
@endif


<div class="modal fade" id="myModalFotoOC" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Producto</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-xs-12 col-sm-12">
                        <div class="form-group">
                            <label for="foto" class="control-label">Orden de Compra</label>
                            <!--<input type="file" name="oc_file" id="oc_file" class="form-control" data-initial-preview="{{isset($data->oc_file) ? Storage::url("/storage/imagenes/notaventa/$data->oc_file") : "http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=Foto+Certificado"}}" accept="image/*"/>-->
                            <!--<input type="file" name="oc_file" id="oc_file" class="form-control" data-initial-preview="{{isset($data->oc_file) ? "/storage/imagenes/notaventa/$data->oc_file" : "http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=Foto+Certificado"}}" accept="image/*"/>-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnOrdenCompra" name="btnOrdenCompra" title="Guardar" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
        
    </div>
</div>

@if ($aux_sta==2 or $aux_sta==3)
    @if ($data->oc_file)
        <?php 
            $ruta = "imagenes/notaventa/";
            $nameFile = $data->oc_file
        ?>
        @include('generales.verfoto')
    @endif
@endif
