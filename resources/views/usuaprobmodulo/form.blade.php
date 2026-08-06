<div class="form-group">
    <label for="usuario_id" class="col-lg-3 control-label requerido">Usuario Aprobador</label>
    <div class="col-lg-6">
        <select name="usuario_id" id="usuario_id" class="form-control selectpicker" data-live-search='true' required>
            <option value="">Seleccione...</option>
            @foreach($usuarios as $id => $nombre)
                <option value="{{$id}}" {{old('usuario_id', $data->usuario_id ?? '') == $id ? 'selected' : ''}}>{{$nombre}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="modulo" class="col-lg-3 control-label requerido">Módulo a restringir</label>
    <div class="col-lg-6">
        <select name="modulo" id="modulo" class="form-control selectpicker" data-live-search='true' required>
            <option value="">Seleccione...</option>
            @foreach($modulos as $url => $nombre)
                <option value="{{$url}}" {{old('modulo', $data->modulo ?? '') == $url ? 'selected' : ''}}>{{$nombre}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="usuario_creador_id" class="col-lg-3 control-label requerido">Puede aprobar registros creados por</label>
    <div class="col-lg-6">
        <select name="usuario_creador_id[]" id="usuario_creador_id" class="form-control selectpicker" data-live-search='true' multiple required>
            @foreach($usuarios as $id => $nombre)
                <option value="{{$id}}"
                    {{is_array(old('usuario_creador_id')) ? (in_array($id, old('usuario_creador_id')) ? 'selected' : '') : (isset($data) ? ($data->usuariosPermitidos->firstWhere('id', $id) ? 'selected' : '') : '')}}
                >{{$nombre}}</option>
            @endforeach
        </select>
        <span class="help-block">Puede seleccionar varios, incluyendo al mismo usuario aprobador.</span>
    </div>
</div>
