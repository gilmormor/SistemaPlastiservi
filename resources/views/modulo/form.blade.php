<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-4">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" maxlength="50" required/>
    </div>
</div>
<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Descripcion</label>
    <div class="col-lg-4">
        <input type="text" name="desc" id="desc" class="form-control" value="{{old('desc', $data->desc ?? '')}}" maxlength="100" required/>
    </div>
</div>
<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Orden</label>
    <div class="col-lg-4">
        <input type="text" name="orden" id="orden" class="form-control" value="{{old('orden', $data->orden ?? '')}}" maxlength="4" required/>
    </div>
</div>
<div class="form-group">
    <label for="stamodapl" class="col-lg-3 control-label requerido">Aplica a:</label>
    <div class="col-lg-4">
        <select name="stamodapl" id="stamodapl" class="form-control select2  stamodapl" data-live-search='true' value="{{old('stamodapl', $data->stamodapl ?? '')}}" required>
            <option 
                value=""
                @if (!isset($data))
                    {{'selected'}}
                @endif
                >Seleccione...</option>
            <option 
              value="2"
                @if (isset($data) and ($data->stamodapl=="2"))
                    {{'selected'}}
                @endif
                >Cotización</option>
            <option 
                value="0"
                @if (isset($data) and ($data->stamodapl=="0"))
                    {{'selected'}}
                @endif
                >Cliente</option>
            <option 
                value="1"
                @if (isset($data) and ($data->stamodapl=="1"))
                    {{'selected'}}
                @endif
                >Nota Venta</option>
        </select>
    </div>
</div>