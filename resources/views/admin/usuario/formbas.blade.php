<div class="col-xs-12 col-sm-6">
    <div class="form-group">
        <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
        <div class="col-lg-8">
            <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
        </div>
    </div>
    <input type="hidden" name="usuario" id="usuario" class="form-control" value="{{old('usuario', $data->usuario ?? '')}}" required/>
    <div class="form-group">
        <label for="email" class="col-lg-3 control-label requerido">E-Mail</label>
        <div class="col-lg-8">
            <input type="email" name="email" id="email" class="form-control" value="{{old('email', $data->email ?? '')}}" required/>
        </div>
    </div>
</div>
<div class="col-xs-12 col-sm-6">
    <div class="form-group">
        <label for="foto" class="col-lg-3 control-label">Foto</label>
        <div class="col-lg-6">
            <!--<input type="file" name="foto_up" id="foto" data-initial-preview="{{isset($data->imagen) ? Storage::url("imagenes/usuario/$data->imagen") : "http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=Foto+Usuario"}}" accept="image/*"/>-->
            <input type="file" name="foto_up" id="foto" data-initial-preview="{{isset($data->foto) ? "/storage/imagenes/usuario/$data->foto" : "http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=Foto+Usuario"}}" accept="image/*"/>
        </div>
    </div>
</div>