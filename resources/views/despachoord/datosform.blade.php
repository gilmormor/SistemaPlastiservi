<div class="row">
    <div class="form-group col-xs-12 col-sm-1">
        <label for="notaventa_id" class="control-label requerido" data-toggle='tooltip' title="Id Nota Venta">NotVenta</label>
        <input type="text" name="notaventa_id" id="notaventa_id" class="form-control" value="{{$data->notaventa_id}}" required disabled/>
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
        <label for="contactoemail" class="control-label requerido">Contacto Email</label>
        <input type="email" name="contactoemail" id="contactoemail" class="form-control" value="{{old('contactoemail', $data->contactoemail ?? '')}}" required placeholder="Email Contacto Entrega" {{$enableCamposCot}}/>
    </div>
    <div class="form-group col-xs-12 col-sm-4">
        <label for="observacion" class="control-label">Observaciones</label>
        <input type="text" name="observacion" id="observacion" class="form-control" value="{{old('observacion', $data->observacion ?? '')}}" placeholder="Observaciones" {{$enableCamposCot}}/>
    </div>
    <div class="form-group col-xs-12 col-sm-2">
        <label for="fechaestdesp" class="control-label requerido" data-toggle='tooltip' title="Fecha estimada de Despacho">Fec Est Despacho</label>
        <input type="text" name="fechaestdesp" id="fechaestdesp" class="form-control pull-right datepicker" value="{{old('fechaestdesp', $data->fechaestdesp ?? '')}}" required readonly/>
    </div>
    <div class="form-group col-xs-12 col-sm-2">
        <label for="despachoobs_id" class="control-label">Obs</label>
        <select name="despachoobs_id" id="despachoobs_id" class="form-control select2 despachoobs_id" {{$enableCamposCot}}>
            <option value=''>Seleccione...</option>
            @foreach($despachoobss as $despachoobs)
                <option
                    value="{{$despachoobs->id}}"
                    @if (($aux_sta==2 or $aux_sta==3) and ($data->despachoobs_id==$despachoobs->id))
                        {{'selected'}}
                    @endif
                    >
                    {{$despachoobs->nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>