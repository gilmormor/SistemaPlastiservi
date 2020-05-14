<div class="col-xs-12 col-sm-6">
    <div class="form-group">
        <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
        <div class="col-lg-8">
            <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
        </div>
    </div>
    <div class="form-group">
        <label for="usuario" class="col-lg-3 control-label requerido">Usuario</label>
        <div class="col-lg-8">
            <input type="text" name="usuario" id="usuario" class="form-control" value="{{old('usuario', $data->usuario ?? '')}}" required/>
        </div>
    </div>
    <div class="form-group">
        <label for="email" class="col-lg-3 control-label requerido">E-Mail</label>
        <div class="col-lg-8">
            <input type="email" name="email" id="email" class="form-control" value="{{old('email', $data->email ?? '')}}" required/>
        </div>
    </div>
    <div class="form-group">
        <label for="password" class="col-lg-3 control-label {{!isset($data) ? 'requerido' : ''}}">Contraseña</label>
        <div class="col-lg-8">
            <input type="password" name="password" id="password" class="form-control" value="" {{!isset($data) ? 'required' : ''}} minlength="5"/>
        </div>
    </div>
    <div class="form-group">
        <label for="re_password" class="col-lg-3 control-label {{!isset($data) ? 'requerido' : ''}}">Repita Contraseña</label>
        <div class="col-lg-8">
            <input type="password" name="re_password" id="re_password" class="form-control" value="" {{!isset($data) ? 'required' : ''}} minlength="5"/>
        </div>
    </div>
    <div class="form-group">
        <label for="rol_id" class="col-lg-3 control-label requerido">Rol</label>
        <div class="col-lg-8">
            <select name="rol_id[]" id="rol_id" class="form-control select2" multiple required>
                @foreach($rols as $id => $nombre)
                    <option
                        value="{{$id}}"
                        {{is_array(old('rol_id')) ? (in_array($id, old('rol_id')) ? 'selected' : '') : (isset($data) ? ($data->roles->firstWhere('id', $id) ? 'selected' : '') : '')}}
                        >
                        {{$nombre}}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="sucursal_id" class="col-lg-3 control-label requerido">Sucursal</label>
        <div class="col-lg-8">
            <select name="sucursal_id[]" id="sucursal_id" class="form-control select2" multiple required>
                @foreach($sucursales as $id => $nombre)
                    <option
                        value="{{$id}}"
                        {{is_array(old('sucursal_id')) ? (in_array($id, old('sucursal_id')) ? 'selected' : '') : (isset($data) ? ($data->sucursales->firstWhere('id', $id) ? 'selected' : '') : '')}}
                        >
                        {{$nombre}}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="col-xs-12 col-sm-6">
    <div class="form-group">
        <label for="foto" class="col-lg-3 control-label">Foto</label>
        <div class="col-lg-9">
            <!--<input type="file" name="foto_up" id="foto" data-initial-preview="{{isset($data->imagen) ? Storage::url("imagenes/usuario/$data->imagen") : "http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=Foto+Usuario"}}" accept="image/*"/>-->
            <input type="file" name="foto_up" id="foto" data-initial-preview="{{isset($data->foto) ? "/storage/imagenes/usuario/$data->foto" : "http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=Foto+Usuario"}}" accept="image/*"/>
        </div>
    </div>
</div>
