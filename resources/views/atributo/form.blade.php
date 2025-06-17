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
        <input type="text" name="desc" id="desc" class="form-control" value="{{old('desc', $data->desc ?? '')}}" maxlength="200" required/>
    </div>
</div>
<div class="form-group">
    <label for="tipodato" class="col-lg-3 control-label requerido">Tipo Dato</label>
    <div class="col-lg-4">
        <select name="tipodato" id="tipodato" class="form-control select2" required>
            <option value="">Seleccione...</option>
            @foreach($tablas['tipodatos'] as $tipodato)
                <option
                    value="{{$tipodato->id}}"
                    @if (isset($data) and $tipodato->id == $data->tipodato)
                        selected                       
                    @endif
                    >{{$tipodato->nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="longitud" class="col-lg-3 control-label requerido">Longitud</label>
    <div class="col-lg-4">
        <input type="text" name="longitud" id="longitud" class="form-control" value="{{old('longitud', $data->longitud ?? '')}}" maxlength="150" required/>
    </div>
</div>
