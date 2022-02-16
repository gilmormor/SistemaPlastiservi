<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-8">
    <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Descripción</label>
    <div class="col-lg-8">
    <input type="text" name="desc" id="desc" class="form-control" value="{{old('desc', $data->desc ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="tipomov" class="col-lg-3 control-label requerido">Tipo</label>
    <div class="col-lg-8">
        <select name="tipomov" id="tipomov" class="form-control select2" required>
            <option value="">Seleccione...</option>
            <option
                value="1"
                @if (isset($data->tipomov) and ($data->tipomov==1))
                    {{'selected'}}
                @endif
            >
                Entrada
            </option>
            <option
                value="-1"
                @if (isset($data->tipomov) and ($data->tipomov==-1))
                    {{'selected'}}
                @endif
            >
                Salida
            </option>
        </select>
    </div>
</div>