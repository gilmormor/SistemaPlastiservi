<div class="form-group">
    <label for="rut" class="col-lg-3 control-label requerido">RUT</label>
    <div class="col-lg-8">
    <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $data->rut ?? '')}}" placeholder="RUT: 12345678-9" required/>
    </div>
</div>
<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-8">
    <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" placeholder="Nombre" required/>
    </div>
</div>
<div class="form-group">
    <label for="apellido" class="col-lg-3 control-label requerido">Apellido</label>
    <div class="col-lg-8">
    <input type="text" name="apellido" id="apellido" class="form-control" value="{{old('apellido', $data->apellido ?? '')}}" placeholder="Apellido" required/>
    </div>
</div>
<div class="form-group">
    <label for="direccion" class="col-lg-3 control-label requerido">Dirección</label>
    <div class="col-lg-8">
    <input type="text" name="direccion" id="direccion" class="form-control" value="{{old('direccion', $data->direccion ?? '')}}" placeholder="Dirección" required/>
    </div>
</div>
<div class="form-group">
    <label for="telefono" class="col-lg-3 control-label requerido">Telefono</label>
    <div class="col-lg-8">
    <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', $data->telefono ?? '')}}" placeholder="Teléfono" required/>
    </div>
</div>
<div class="form-group">
    <label for="ext" class="col-lg-3 control-label requerido">Ext.</label>
    <div class="col-lg-8">
    <input type="text" name="ext" id="ext" class="form-control" value="{{old('ext', $data->ext ?? '')}}" required placeholder="Extención"/>
    </div>
</div>
<div class="form-group">
    <label for="email" class="col-lg-3 control-label requerido">Email</label>
    <div class="col-lg-8">
    <input type="email" name="email" id="email" class="form-control" value="{{old('email', $data->email ?? '')}}" required placeholder="Email"/>
    </div>
</div>
<div class="form-group">
    <label for="cargo_id" class="col-lg-3 control-label requerido">Cargo</label>
    <div class="col-lg-8">
        <select name="cargo_id" id="cargo_id" class="selectpicker form-control cargo_id" data-live-search='true' title='Seleccione...'  required>
            @foreach($cargos as $cargo)
                <option
                    value="{{$cargo->id}}"
                    @if (($aux_sta==2) and ($data->cargo_id==$cargo->id))
                        {{'selected'}}
                    @endif
                    >
                    {{$cargo->nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="usuario_id" class="col-lg-3 control-label">Usuario</label>
    <div class="col-lg-8">
        <select name="usuario_id" id="usuario_id" class="selectpicker form-control usuario_id" data-live-search='true' title='Seleccione...'>
            <option value="">Seleccione...</option>
            @foreach($users as $user)
                <option
                    value="{{$user->id}}"
                    @if (($aux_sta==2) and ($data->usuario_id==$user->id))
                        {{'selected'}}
                    @endif
                    >
                    {{$user->nombre}} - {{$user->usuario}}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="persona_id" class="col-lg-3 control-label requerido">Area que pertenece</label>
    <div class="col-lg-8">
        <select name="persona_id[]" id="persona_id" class="form-control select2" multiple required>
            @foreach($jefaturasucursalareas as $jefaturasucursalarea)
                <option
                    value="{{$jefaturasucursalarea->id}}"
                    {{is_array(old('persona_id')) ? (in_array($jefaturasucursalarea->id, old('persona_id')) ? 'selected' : '') : (isset($data) ? ($data->jefaturasucursalareas->firstWhere('id', $jefaturasucursalarea->id) ? 'selected' : '') : '')}}
                    >
                    {{$jefaturasucursalarea->sucursal_area->sucursal->nombre}} - {{$jefaturasucursalarea->sucursal_area->area->nombre}} - {{$jefaturasucursalarea->jefatura->nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>
