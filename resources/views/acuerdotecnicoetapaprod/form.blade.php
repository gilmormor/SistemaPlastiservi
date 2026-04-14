<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<div class="form-group">
    <label for="id" class="col-lg-3 control-label" title="Codigo Acuerdo Tecnico">Cod AT</label>
    <div class="col-lg-4">
        <input type="text" name="id" id="id" class="form-control" value="{{old('id', $data->id ?? '')}}" required disabled/>
    </div>
</div>
<div class="form-group">
    <label for="producto_id" class="col-lg-3 control-label" title="Codigo producto">Cod Producto</label>
    <div class="col-lg-4">
        <input type="text" name="producto_id" id="producto_id" class="form-control" value="{{old('producto_id', $data->producto_id ?? '')}}" required disabled/>
    </div>
</div>
<div class="form-group">
    <label for="nombre_producto" class="col-lg-3 control-label" title="Descripcion producto">Desc Producto</label>
    <div class="col-lg-4">
        <input type="text" name="nombre_producto" id="nombre_producto" class="form-control" value="{{old('nombre_producto', $data->nombre_producto ?? '')}}" required disabled/>
    </div>
</div>
<div class="form-group">
    <label for="apsucetapaprod_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Etapas Produccion">Etapas Produccion</label>
    <div class="col-lg-4">
        <select name="apsucetapaprod_id[]" id="apsucetapaprod_id" class="form-control select2" multiple required>
            @foreach($tablas['etapaprods'] as $etapaprod)
                <option
                    value="{{$etapaprod->areaproduccionsucetapaprod_id}}"
                    {{is_array(old('apsucetapaprod_id')) ? (in_array($etapaprod->areaproduccionsucetapaprod_id, old('apsucetapaprod_id')) ? 'selected' : '') : (isset($data) ? ($data->apsucetapaprods->firstWhere('id', $etapaprod->areaproduccionsucetapaprod_id) ? 'selected' : '') : '')}}
                    >{{$etapaprod->etapaprod_nombre}}</option>
            @endforeach
        </select>
    </div>
</div>