<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-4">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" maxlength="50" required/>
    </div>
</div>
<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Descripción</label>
    <div class="col-lg-4">
        <input type="text" name="desc" id="desc" class="form-control" value="{{old('desc', $data->desc ?? '')}}" maxlength="150" required/>
    </div>
</div>

<div class="form-group">
    <label for="sucursal_id" class="col-lg-3 control-label requerido">Area de Produccion</label>
    <div class="col-lg-4">
        <select name="areaproduccionsuc_id[]" id="areaproduccionsuc_id" class="form-control select2" multiple required>
            @foreach($tablas['areaproduccionsuc'] as $areaproduccionsuc)
                <option
                    value="{{$areaproduccionsuc->id}}"
                    {{is_array(old('areaproduccionsuc_id')) ? (in_array($areaproduccionsuc->id, old('areaproduccionsuc_id')) ? 'selected' : '') : (isset($data) ? ($data->areaproduccionsucs->firstWhere('id', $areaproduccionsuc->id) ? 'selected' : '') : '')}}
                    >{{$areaproduccionsuc->sucursal_nombre}}/{{$areaproduccionsuc->areaproduccion_nombre}}</option>
            @endforeach
        </select>
    </div>
</div>