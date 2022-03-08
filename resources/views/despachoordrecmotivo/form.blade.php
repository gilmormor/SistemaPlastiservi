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
    <label for="tipomovinv" class="col-lg-3 control-label requerido">Tipo Mov Inv</label>
    <div class="col-lg-8">
        <select name="tipomovinv" id="tipomovinv" class="form-control select2" required>
            <option value="">Seleccione...</option>
            <option
                value="0"
                @if (isset($data->tipomovinv) and ($data->tipomovinv==0))
                    {{'selected'}}
                @endif
            >
                No entra al inventario
            </option>
            <option
                value="1"
                @if (isset($data->tipomovinv) and ($data->tipomovinv==1))
                    {{'selected'}}
                @endif
            >
                Entra al Inventario
            </option>
            <option
                value="2"
                @if (isset($data->tipomovinv) and ($data->tipomovinv==2))
                    {{'selected'}}
                @endif
            >
                Entra y sale al Inventario
            </option>
        </select>
    </div>
</div>