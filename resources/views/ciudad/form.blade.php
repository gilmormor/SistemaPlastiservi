<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-4">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" maxlength="20" required/>
    </div>
</div>