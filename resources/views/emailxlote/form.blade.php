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
    <label for="persona_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Personas">Personas</label>
    <div class="col-lg-4">
        <select name="persona_id[]" id="persona_id" class="form-control select2" multiple required>
            @foreach($personas as $persona)
                <option
                    value="{{$persona->id}}"
                    {{is_array(old('persona_id')) ? (in_array($persona->id, old('persona_id')) ? 'selected' : '') : (isset($data) ? ($data->personas->firstWhere('id', $persona->id) ? 'selected' : '') : '')}}
                    >{{$persona->nombre . " " . $persona->apellido}}</option>
            @endforeach
        </select>
    </div>
</div>
