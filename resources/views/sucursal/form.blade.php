<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-8">
    <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="abrev" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Abreviatura Nombre"  maxlength="5">Abreviatura</label>
    <div class="col-lg-8">
    <input type="text" name="abrev" id="abrev" class="form-control" value="{{old('abrev', $data->abrev ?? '')}}" required/>
    </div>
</div>
@if ($aux_sta==1)
    <div class="form-group">
        <label for="region_id" class="col-lg-3 control-label requerido">Región</label>
        <div class="col-lg-8">
            <select name="region_id" id="region_id" class="form-control select2 region_id" required>
                <option value="">Seleccione...</option>
                @foreach($regiones as $region)
                    <option value="{{$region->id}}">{{trim($region->nombre)}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="provincia_id" class="col-lg-3 control-label requerido">Provincia</label>
        <div class="col-lg-8">
            <select name="provincia_id" id="provincia_id" class="form-control select2 provincia_id" required>
                <option value="">Seleccione...</option>
            </select>
        </div>
    </div>
    
    <div class="form-group">
        <label for="comuna_id" class="col-lg-3 control-label requerido">Comuna</label>
        <div class="col-lg-8">
            <select name="comuna_id" id="comuna_id" class="form-control select2 comuna_id" required>
                <option value="">Seleccione...</option>
            </select>
        </div>
    </div>
                
@else
    <div class="form-group">
        <label for="region_id" class="col-lg-3 control-label requerido">Región</label>
        <div class="col-lg-8">
            <select name="region_id" id="region_id" class="form-control select2 region_id" required>
                <option value="">Seleccione...</option>
                @foreach($regiones as $region)
                    <option
                        value="{{$region->id}}"
                        @if ($data->region_id==$region->id)
                            {{'selected'}}
                        @endif
                        >{{$region->nombre}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="provincia_id" class="col-lg-3 control-label requerido">Provincia</label>
        <div class="col-lg-8">
            <select name="provincia_id" id="provincia_id" class="form-control select2 provincia_id" required>
                <option value="">Seleccione...</option>
                @foreach($provincias as $provincia)
                    <option value="{{$provincia->id}}"
                        @if ($data->provincia_id==$provincia->id)
                            {{'selected'}}
                        @endif
                        >{{$provincia->nombre}}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="form-group">
        <label for="comuna_id" class="col-lg-3 control-label requerido">Comuna</label>
        <div class="col-lg-8">
            <select name="comuna_id" id="comuna_id" class="form-control select2 comuna_id" required>
                <option value="">Seleccione...</option>
                @foreach($comunas as $comuna)
                    <option
                        value="{{$comuna->id}}"
                        @if ($data->comuna_id==$comuna->id)
                            {{'selected'}}
                        @endif
                        >{{$comuna->nombre}}</option>
                @endforeach>
            </select>
        </div>
    </div>
@endif

<div class="form-group">
    <label for="direccion" class="col-lg-3 control-label requerido">Dirección</label>
    <div class="col-lg-8">
    <input type="text" name="direccion" id="direccion" class="form-control" value="{{old('direccion', $data->direccion ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="telefono1" class="col-lg-3 control-label requerido">Teléfono 1</label>
    <div class="col-lg-8">
    <input type="text" name="telefono1" id="telefono1" class="form-control" value="{{old('telefono1', $data->telefono1 ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="telefono2" class="col-lg-3 control-label">Teléfono 2</label>
    <div class="col-lg-8">
    <input type="text" name="telefono2" id="telefono1" class="form-control" value="{{old('telefono2', $data->telefono2 ?? '')}}"/>
    </div>
</div>
<div class="form-group">
    <label for="telefono3" class="col-lg-3 control-label">Teléfono 3</label>
    <div class="col-lg-8">
    <input type="text" name="telefono3" id="telefono1" class="form-control" value="{{old('telefono3', $data->telefono3 ?? '')}}"/>
    </div>
</div>
<div class="form-group">
    <label for="email" class="col-lg-3 control-label requerido">email</label>
    <div class="col-lg-8">
    <input type="email" name="email" id="email" class="form-control" value="{{old('email', $data->email ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="area_id" class="col-lg-3 control-label requerido">Area</label>
    <div class="col-lg-8">
        <select name="area_id[]" id="area_id" class="form-control select2" multiple required>
            @foreach($areas as $id => $nombre)
                <option
                    value="{{$id}}"
                    {{is_array(old('area_id')) ? (in_array($id, old('area_id')) ? 'selected' : '') : (isset($data) ? ($data->areas->firstWhere('id', $id) ? 'selected' : '') : '')}}
                    >
                    {{$nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="staaprobnv" class="col-lg-3 control-label requerido" title="Estatus Aprovar Nota Venta" data-toggle='tooltip'>Status Aprob Nota Venta</label>
    <div class="col-lg-8">
        <select name="staaprobnv" id="staaprobnv" class="form-control select2 staaprobnv" required>
            <option value="">Seleccione...</option>
            <option value="0"
                @if ($data->staaprobnv == 0)
                    {{'selected'}}
                @endif>No es necesario Validar</option>
            <option value="1"
            @if ($data->staaprobnv == 1)
                {{'selected'}}
            @endif>Todas deben ser Validadas</option>
            <option value="2"
            @if ($data->staaprobnv == 2)
                {{'selected'}}
            @endif>Solo validar las que esten por debajo de precio en tabla</option>
        </select>
    </div>
</div>