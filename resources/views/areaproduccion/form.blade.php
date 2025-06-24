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
<div class="form-group">
    <label for="sucursal_id" class="col-lg-3 control-label requerido">Sucursal</label>
    <div class="col-lg-9">
        <select name="sucursal_id[]" id="sucursal_id" class="form-control select2" multiple required>
            @foreach($tablas['sucursales'] as $sucursal)
                <option
                    value="{{$sucursal->id}}"
                    {{is_array(old('sucursal_id')) ? (in_array($sucursal->id, old('sucursal_id')) ? 'selected' : '') : (isset($data) ? ($data->sucursales->firstWhere('id', $sucursal->id) ? 'selected' : '') : '')}}
                    >{{$sucursal->nombre}}</option>
            @endforeach
        </select>
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