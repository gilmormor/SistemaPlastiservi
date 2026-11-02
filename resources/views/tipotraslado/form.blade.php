<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">

<div class="form-group">
    <label for="indtraslado" class="col-lg-3 control-label requerido">Código SII</label>
    <div class="col-lg-2">
        <input type="text" name="indtraslado" id="indtraslado" class="form-control numerico"
               value="{{old('indtraslado', $data->indtraslado ?? '')}}" maxlength="3" required/>
    </div>
</div>

<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Descripción</label>
    <div class="col-lg-6">
        <input type="text" name="desc" id="desc" class="form-control"
               value="{{old('desc', $data->desc ?? '')}}" maxlength="100" required/>
    </div>
</div>

<div class="form-group">
    <label for="activo" class="col-lg-3 control-label requerido">Activo</label>
    <div class="col-lg-2">
        <select name="activo" id="activo" class="form-control select2 activo" required>
            <option value="">Seleccione...</option>
            <option value="1" @if(isset($data) && $data->activo == 1) selected @endif>Activo</option>
            <option value="0" @if(isset($data) && $data->activo == 0) selected @endif>Inactivo</option>
        </select>
    </div>
</div>
