<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-9">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Descripción</label>
    <div class="col-lg-9">
        <input type="text" name="desc" id="desc" class="form-control" value="{{old('desc', $data->desc ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="sucursal_id" class="col-lg-3 control-label requerido">Sucursal</label>
    <div class="col-lg-9">
        <select name="sucursal_id" id="sucursal_id" class="form-control select2 sucursal_id" data-live-search='true' required>
            <option value=''>Seleccione...</option>
            @foreach($sucursales as $sucursal)
                <option
                    value="{{$sucursal->id}}"
                    @if (isset($data->sucursal_id) and ($data->sucursal_id==$sucursal->id))
                        {{'selected'}}
                    @endif
                    >
                    {{$sucursal->nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>