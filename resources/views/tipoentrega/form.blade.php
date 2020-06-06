<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-8">
    <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="abrev" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Abreviatura Nombre"  maxlength="5">Abreviatura</label>
    <div class="col-lg-8">
    <input type="text" name="abrev" id="abrev" class="form-control" value="{{old('abrev', $data->abrev ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="icono" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Icono"  maxlength="5">Icono</label>
    <div class="col-lg-8">
    <input type="text" name="icono" id="abrev" class="form-control" value="{{old('icono', $data->icono ?? '')}}" required/>
    </div>
</div>