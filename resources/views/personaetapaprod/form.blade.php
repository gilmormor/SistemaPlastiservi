<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label">Nombre</label>
    <div class="col-lg-8">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre . " " . $data->apellido?? '')}}" placeholder="Nombre" disabled/>
    </div>
</div>
<div class="form-group">
    <label for="usuario_nombre" class="col-lg-3 control-label">Usuario</label>
    <div class="col-lg-8">
        <input type="text" name="usuario_nombre" id="usuario_nombre" class="form-control" value="{{old('usuario_nombre', $data->usuario->usuario ?? '')}}" placeholder="Usuario" disabled/>
    </div>
</div>
<div class="form-group">
    <label for="persona_id" class="col-lg-3 control-label requerido">Area que pertenece</label>
    <div class="col-lg-8">
        <select name="persona_id[]" id="persona_id" class="form-control select2" multiple required disabled>
            @foreach($jefaturasucursalareas as $jefaturasucursalarea)
                <option
                    value="{{$jefaturasucursalarea->id}}"
                    {{is_array(old('persona_id')) ? (in_array($jefaturasucursalarea->id, old('persona_id')) ? 'selected' : '') : (isset($data) ? ($data->jefaturasucursalareas->firstWhere('id', $jefaturasucursalarea->id) ? 'selected' : '') : '')}}
                    >
                    {{$jefaturasucursalarea->sucursal_area->sucursal->nombre}} - {{$jefaturasucursalarea->sucursal_area->area->nombre}} - {{$jefaturasucursalarea->jefatura->nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="areaproduccionsucetapaprod_id" class="col-lg-3 control-label requerido">Etapa Produccion</label>
    <div class="col-lg-8">
        {{-- <select name='areaproduccionsucetapaprod_id[]' id='areaproduccionsucetapaprod_id' class='selectpicker form-control areaproduccionsucetapaprod_id'  data-live-search='true' multiple data-actions-box='true'> --}}
        <select name="areaproduccionsucetapaprod_id[]" id="areaproduccionsucetapaprod_id" class="form-control select2" multiple required>
            @foreach($etapaProdSucs as $etapaProdSuc)
                <option
                    value="{{$etapaProdSuc->areaproduccionsucetapaprod_id}}"
                    {{is_array(old('areaproduccionsucetapaprod_id')) ? (in_array($etapaProdSuc->areaproduccionsucetapaprod_id, old('areaproduccionsucetapaprod_id')) ? 'selected' : '') : (isset($data) ? ($data->etapaprods->firstWhere('id', $etapaProdSuc->areaproduccionsucetapaprod_id) ? 'selected' : '') : '')}}
                    >{{$etapaProdSuc->sucursal_nombre}} - {{$etapaProdSuc->areaproduccion_nombre}} - {{$etapaProdSuc->etapaprod_nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>