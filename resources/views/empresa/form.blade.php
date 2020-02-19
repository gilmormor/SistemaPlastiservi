<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-8">
    <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="rut" class="col-lg-3 control-label requerido">RUT</label>
    <div class="col-lg-8">
    <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $data->rut ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="iva" class="col-lg-3 control-label requerido">Iva</label>
    <div class="col-lg-8">
    <input type="text" name="iva" id="iva" class="form-control" value="{{old('iva', $data->iva ?? '')}}" required/>
    </div>
</div>

<div class="form-group">
    <label for="sucursal_id" class="col-lg-3 control-label requerido">Sucursal Principal</label>
    <div class="col-lg-8">
        <select name="sucursal_id" id="sucursal_id" class="form-control select2 sucursal_id" required>
            <option value="">Seleccione...</option>
            @foreach($sucursales as $sucursal)
                <option
                    value="{{$sucursal->id}}"
                    @if (($aux_sta==2) and ($data->sucursal_id==$sucursal->id))
                        {{'selected'}}
                    @endif
                    >
                    {{$sucursal->nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>
