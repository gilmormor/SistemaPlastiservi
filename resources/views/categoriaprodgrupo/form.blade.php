<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-9">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Descripción</label>
    <div class="col-lg-9">
        <input type="text" name="desc" id="desc" class="form-control" value="{{old('desc', $data->desc ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="comisionventas" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Comisión por ventas"  maxlength="5">% Comision por ventas</label>
    <div class="col-lg-8">
    <input type="text" name="comisionventas" id="comisionventas" class="form-control numerico" value="{{old('comisionventas', $data->comisionventas ?? '')}}" required/>
    </div>
</div>