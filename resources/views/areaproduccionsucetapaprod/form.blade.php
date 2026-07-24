<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label">Nombre</label>
    <div class="col-lg-9">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->areaproduccion->nombre ?? '')}}" readonly/>
    </div>
</div>
<div class="form-group">
    <label for="descripcion" class="col-lg-3 control-label">Descripción</label>
    <div class="col-lg-9">
        <input type="text" name="descripcion" id="descripcion" class="form-control" value="{{old('descripcion', $data->areaproduccion->descripcion ?? '')}}" readonly/>
    </div>
</div>
<div class="form-group">
    <label for="sucursal_nombre" class="col-lg-3 control-label">Sucursal</label>
    <div class="col-lg-9">
        <input type="text" name="sucursal_nombre" id="sucursal_nombre" class="form-control" value="{{old('sucursal_nombre', $data->sucursal->nombre ?? '')}}" readonly/>
    </div>
</div>
<div class="form-group">
    <label for="etapaprod_id" class="col-lg-3 control-label requerido">Etapa Producción</label>
    <div class="col-lg-9">
        <select name="etapaprod_id[]" id="etapaprod_id" class="form-control select2" multiple required>
            @foreach($tablas['etapaprods'] as $etapaprod)
                <option
                    value="{{$etapaprod->id}}"
                    {{is_array(old('etapaprod_id')) ? (in_array($etapaprod->id, old('etapaprod_id')) ? 'selected' : '') : (isset($data) ? ($data->etapaprods->firstWhere('id', $etapaprod->id) ? 'selected' : '') : '')}}
                    >{{$etapaprod->nombre}}</option>
            @endforeach
        </select>
    </div>
</div>