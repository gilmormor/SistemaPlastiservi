<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-4">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" maxlength="20" required/>
    </div>
</div>

<div class="form-group">
    <label for="ciudad_id" class="col-lg-3 control-label requerido">Ciudad</label>
    <div class="col-lg-4">
        <select name="ciudad_id" id="ciudad_id" class="select2 form-control ciudad_id" title='Seleccione...' required>
            <option value="">Seleccione...</option>
            @foreach($ciudades as $ciudad)
                <option
                    value="{{$ciudad->id}}"
                    @if (isset($data) and ($data->ciudad_id==$ciudad->id))
                        {{'selected'}}
                    @endif
                    >{{$ciudad->nombre}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group" style="display:none;">
    <label for="provincia_id" class="col-lg-3 control-label requerido">Provincia</label>
    <div class="col-lg-4">
        <select name="provincia_id" id="provincia_id" class="select2 form-control provincia_id" title='Seleccione...' required>
            <option value="">Seleccione...</option>
            @foreach($provincias as $provincia)
                <option
                    value="{{$provincia->id}}"
                    @if (isset($data) and ($data->provincia_id==$provincia->id))
                        {{'selected'}}
                    @endif
                    >{{$provincia->nombre}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label for="provincia_nombre" class="col-lg-3 control-label requerido">Provincia</label>
    <div class="col-lg-4">
        <input type="text" name="provincia_nombre" id="provincia_nombre" class="form-control" value="{{old('provincia_nombre', $data->provincia->nombre ?? '')}}" maxlength="60" required readonly/>
    </div>
</div>
