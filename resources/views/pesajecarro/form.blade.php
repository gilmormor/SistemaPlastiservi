<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-9">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="obs" class="col-lg-3 control-label requerido">Observacion</label>
    <div class="col-lg-9">
        <input type="text" name="obs" id="obs" class="form-control" value="{{old('obs', $data->obs ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="tara" class="col-lg-3 control-label requerido">Tara</label>
    <div class="col-lg-9">
        <input type="text" name="tara" id="tara" class="form-control numerico" value="{{old('tara', $data->tara ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label id="lblsucursal_id" name="lblsucursal_id" for="sucursal_id" class="col-lg-3 control-label requerido">Sucursal</label>
    <div class="col-lg-9">
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
</div>

<div class="form-group">
    <label for="activo" class="col-lg-3 control-label requerido">Activo</label>
    <div class="col-lg-9">
        <select name="activo" id="activo" class="form-control select2" required>
            <option value="">Seleccione...</option>
            <option
                value="1"
                @if (isset($data->activo) and ($data->activo==1))
                    {{'selected'}}
                @endif
            >Activo</option>
            <option
                value="0"
                @if (isset($data->activo) and ($data->activo==0))
                    {{'selected'}}
                @endif
            >Inactivo</option>
        </select>
    </div>
</div>