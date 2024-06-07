<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-4">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" maxlength="50" required/>
    </div>
</div>
<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Descripcion</label>
    <div class="col-lg-4">
        <input type="text" name="desc" id="desc" class="form-control" value="{{old('desc', $data->desc ?? '')}}" maxlength="100" required/>
    </div>
</div>
<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Orden</label>
    <div class="col-lg-4">
        <input type="text" name="orden" id="orden" class="form-control" value="{{old('orden', $data->orden ?? '')}}" maxlength="4" required/>
    </div>
</div>
<div class="form-group">
    <div class="checkbox">
        <label class="col-sm-offset-3" style="font-size: 1.2em;display:flex;align-items: center;">
            <input type="checkbox" id="aux_stanvdc" name="aux_stanvdc">
            <span class="cr"><i class="cr-icon fa fa-check"></i></span>
            Status Asociado al ID Nota de Venta
        </label>
    </div>
</div>
<input type="hidden" name="stanvdc" id="stanvdc" value="{{old('stanvdc', $data->stanvdc ?? '0')}}">
