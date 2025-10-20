<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-4">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" maxlength="50" required/>
    </div>
</div>
<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Descripción</label>
    <div class="col-lg-4">
        <input type="text" name="desc" id="desc" class="form-control" value="{{old('desc', $data->desc ?? '')}}" maxlength="150" required/>
    </div>
</div>

<div class="form-group">
    <label for="sucursal_id" class="col-lg-3 control-label requerido">Area de Produccion</label>
    <div class="col-lg-4">
        <select name="areaproduccionsucep_id[]" id="areaproduccionsucep_id" class="form-control select2" multiple required>
            @foreach($tablas['areaproduccionsucep'] as $areaproduccionsucep)
                <option
                    value="{{$areaproduccionsucep->id}}"
                    {{is_array(old('areaproduccionsucep_id')) ? (in_array($areaproduccionsucep->id, old('areaproduccionsucep_id')) ? 'selected' : '') : (isset($data) ? ($data->areaproduccionsuceps->firstWhere('id', $areaproduccionsucep->id) ? 'selected' : '') : '')}}
                    >{{$areaproduccionsucep->sucursal_nombre}}/{{$areaproduccionsucep->areaproduccion_nombre}}/{{$areaproduccionsucep->etapaprod_nombre}}</option>
            @endforeach
        </select>
    </div>
</div>