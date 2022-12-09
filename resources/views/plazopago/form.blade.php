<div class="form-group">
    <label for="descripcion" class="col-lg-3 control-label requerido">Descripción</label>
    <div class="col-lg-8">
        <input type="text" name="descripcion" id="descripcion" class="form-control" value="{{old('descripcion', $data->descripcion ?? '')}}" required/>
    </div>
    <label for="dias" class="col-lg-3 control-label requerido">Dias</label>
    <div class="col-lg-8">
        <input type="text" name="dias" id="dias" class="form-control" value="{{old('dias', $data->dias ?? '')}}" required/>
    </div>
</div>