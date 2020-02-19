<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>

    <div class="col-lg-8">
    <input type="text"  id="nombre" name="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required>
    </div>
</div>
<div class="form-group">
    <label for="url" class="col-lg-3 control-label requerido">URL</label>

    <div class="col-lg-8">
      <input type="text"  id="url" name="url" class="form-control" value="{{old('url', $data->url ?? '')}}" required>
    </div>
</div>
<div class="form-group">
    <label for="icono" class="col-lg-3 control-label">Icono</label>

    <div class="col-lg-8">
      <input type="text"  id="icono" name="icono" class="form-control" value="{{old('icono', $data->icono ?? '')}}">
    </div>
    <div class="col-lg-1">
      <span id="mostrar-icono" name="mostrar-icono" class="fa fa-fw {{old("icono")}}"></span>
    </div>
</div>

    