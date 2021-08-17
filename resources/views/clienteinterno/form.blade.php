<input type='hidden' id="aux_rut" name="aux_rut" value="{{old('aux_rut', $data->rut ?? '')}}">
<input type='hidden' id="aux_direccion" name="aux_direccion" value="{{old('direccion', $data->direccion ?? '')}}">
<input type='hidden' id="aux_observaciones" name="aux_observaciones" value="{{old('observaciones', $data->observaciones ?? '')}}">

<div class="form-group">
    <label for="rut" class="col-lg-2 control-label requerido">Rut</label>
    <div class="col-lg-9">
        <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $data->rut ?? '')}}" required placeholder="123456789 RUT sin guiones"/>
    </div>
</div>
<div class="form-group">
    <label for="razonsocial" class="col-lg-2 control-label requerido">Razón Social</label>
    <div class="col-lg-9">
        <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', $data->razonsocial ?? '')}}" required placeholder="Razón Social"/>
    </div>
</div>
<div class="form-group">
    <label for="direccion" class="col-lg-2 control-label requerido">Dirección</label>
    <div class="col-lg-9">
        <textarea class="form-control" name="direccion" id="direccion" value="{{old('direccion', $data->direccion ?? '')}}"></textarea>
    </div>
</div>
<div class="form-group">
    <label for="telefono" class="col-lg-2 control-label requerido">Telefono</label>
    <div class="col-lg-9">
        <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', $data->telefono ?? '')}}" required placeholder="Teléfono"/>
    </div>
</div>
<div class="form-group">
    <label for="email" class="col-lg-2 control-label requerido">Email</label>
    <div class="col-lg-9">
        <input type="text" name="email" id="email" class="form-control" value="{{old('email', $data->email ?? '')}}" required placeholder="Ejemplo: nombre@dominio.com"/>
    </div>
</div>
<div class="form-group">
    <label for="vendedor_id" class="col-lg-2 control-label requerido">Vendedor</label>
    <div class="col-lg-9">
        <select name="vendedor_id[]" id="vendedor_id" class="form-control select2" multiple required>
            @foreach($vendedores as $vendedor)
                <option
                    value="{{$vendedor->id}}"
                    {{is_array(old('vendedor_id')) ? (in_array($vendedor->id, old('vendedor_id')) ? 'selected' : '') : (isset($data) ? ($data->vendedores->firstWhere('id', $vendedor->id) ? 'selected' : '') : '')}}
                    >
                    {{$vendedor->nombre}}
                </option>
            @endforeach
        </select>        
    </div>
</div>

<div class="form-group">
    <label for="sucursalp_id" class="col-lg-2 control-label requerido">Sucursal</label>
    <div class="col-lg-9">
        <select name="sucursalp_id[]" id="sucursalp_id" class="form-control select2" multiple required>
            @foreach($sucursales as $id => $nombre)
                <option
                    value="{{$id}}"
                    {{is_array(old('sucursalp_id')) ? (in_array($id, old('sucursalp_id')) ? 'selected' : '') : (isset($data) ? ($data->sucursales->firstWhere('id', $id) ? 'selected' : '') : '')}}
                    >
                    {{$nombre}}
                </option>
            @endforeach
        </select>        
    </div>
</div>

<div class="form-group">
    <label for="comunap_id" class="col-lg-2 control-label requerido">Comuna</label>
    <div class="col-lg-9">
        <select name="comunap_id" id="comunap_id" class="select2 form-control comunap_id" title='Seleccione...' required>
            <option value="">Seleccione...</option>
            @foreach($comunas as $comuna)
                <option
                    value="{{$comuna->id}}"
                    {{(isset($data) ? (($data->comunap_id==$comuna->id) ? 'selected' : '') : '')}}
                >
                    {{$comuna->nombre}}
                </option>
            @endforeach
        </select>

    </div>
</div>
<div class="form-group">
    <label for="formapago_id" class="col-lg-2 control-label requerido">Forma de pago</label>
    <div class="col-lg-9">
        <select name="formapago_id" id="formapago_id" class="select2 form-control formapago" data-live-search='true' title='Seleccione...' required>
            <option value="">Seleccione...</option>
            @foreach($formapagos as $formapago)
                <option
                    value="{{$formapago->id}}"
                        {{(isset($data) ? (($data->formapago_id==$formapago->id) ? 'selected' : '') : '')}}
                    >
                    {{$formapago->descripcion}}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="plazopago_id" class="col-lg-2 control-label requerido">Plazo pago</label>
    <div class="col-lg-9">
        <select name="plazopago_id" id="plazopago_id" class="select2 form-control plazopago" data-live-search='true' title='Seleccione...' required>
            <option value="">Seleccione...</option>
            @foreach($plazopagos as $plazopago)
                <option
                    value="{{$plazopago->id}}"
                        {{(isset($data) ? (($data->plazopago_id==$plazopago->id) ? 'selected' : '') : '')}}
                    >
                    {{$plazopago->descripcion}}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="observaciones" class="col-lg-2 control-label">Observaciones</label>
    <div class="col-lg-9">
        <textarea class="form-control" name="observaciones" id="observaciones" value="{{old('observaciones', $data->observaciones ?? '')}}" placeholder="Observación"></textarea>
    </div>
</div>
