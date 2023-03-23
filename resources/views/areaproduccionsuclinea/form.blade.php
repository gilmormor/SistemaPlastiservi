<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-9">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Descripcion</label>
    <div class="col-lg-9">
        <input type="text" name="desc" id="desc" class="form-control numerico" value="{{old('desc', $data->desc ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="obs" class="col-lg-3 control-label">Observacion</label>
    <div class="col-lg-9">
        <input type="text" name="obs" id="obs" class="form-control" value="{{old('obs', $data->obs ?? '')}}"/>
    </div>
</div>
<div class="form-group">
    <label id="lblareaproduccionsuc_id" name="lblareaproduccionsuc_id" for="areaproduccionsuc_id" class="col-lg-3 control-label requerido">Area Produccion / Sucursal</label>
    <div class="col-lg-9">
        <select name="areaproduccionsuc_id" id="areaproduccionsuc_id" class="form-control select2 areaproduccionsuc_id" data-live-search='true' required>
            <option value=''>Seleccione...</option>
                @foreach($tablas['areaproduccionsucs'] as $areaproduccionsuc)
                    <option
                        value="{{$areaproduccionsuc->id}}"
                        @if (isset($data) and ($data->areaproduccionsuc_id==$areaproduccionsuc->id))
                            {{'selected'}}
                        @endif
                        >{{$areaproduccionsuc->areaproduccion->nombre}} - {{$areaproduccionsuc->sucursal->nombre}}</option>
                @endforeach                    
        </select>
    </div>
</div>

<div class="form-group">
    <label for="activo" class="col-lg-3 control-label requerido">Activo</label>
    <div class="col-lg-9">
        <select name="activo" id="activo" class="form-control select2" required>
            <option value="">Seleccione...</option>
            <option
                value="1"
                @if (isset($data->activo) and ($data->activo==1))
                    {{'selected'}}
                @endif
            >Activo</option>
            <option
                value="0"
                @if (isset($data->activo) and ($data->activo==0))
                    {{'selected'}}
                @endif
            >Inactivo</option>
        </select>
    </div>
</div>