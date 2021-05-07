<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-8">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="descripcion" class="col-lg-3 control-label requerido">Descripción</label>
    <div class="col-lg-8">
        <input type="text" name="descripcion" id="descripcion" class="form-control" value="{{old('descripcion', $data->descripcion ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <div class="checkbox">
        <label class="col-sm-offset-3" style="font-size: 1.2em;display:flex;align-items: center;">
            <input type="checkbox" id="marcarTodo" name="marcarTodo">
            <span class="cr"><i class="cr-icon fa fa-check"></i></span>
            Mostrar en Factura
        </label>
    </div>
</div>