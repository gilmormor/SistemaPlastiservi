<div class="form-group">
    <label for="bod_desc" class="col-lg-3 control-label requerido">Descripción</label>
    <div class="col-lg-8">
    <input type="text" name="bod_desc" id="bod_desc" class="form-control" value="{{old('bod_desc', $data->bod_desc ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="sucursal_id" class="col-lg-3 control-label requerido">Sucursal</label>
    <div class="col-lg-8">
        <select name="sucursal_id" id="sucursal_id" class="form-control selectpicker" data-live-search='true' title='Seleccione...' required>
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
