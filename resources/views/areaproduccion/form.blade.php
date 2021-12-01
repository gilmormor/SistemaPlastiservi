<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-9">
    <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="descripcion" class="col-lg-3 control-label requerido">Descripción</label>
    <div class="col-lg-9">
    <input type="text" name="descripcion" id="descripcion" class="form-control" value="{{old('descripcion', $data->descripcion ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="stapromkg" class="col-lg-3 control-label requerido" title="Estatus para mostrar o no las columnas de promedio por kilo en consuta y reportes.">Status Mostar Prom Kilo</label>
    <div class="col-lg-9">
        <select name="stapromkg" id="stapromkg" class="form-control select2 stapromkg" required>
            <option value="">Seleccione...</option>
            <option value="0"
                @if (isset($data) and $data->stapromkg=="0")
                    {{'selected'}}
                @endif
            >No</option>
            <option value="1"
                @if (isset($data) and $data->stapromkg=="1")
                    {{'selected'}}
                @endif    
            >Si</option>
        </select>
    </div>
</div>