<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-4">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" maxlength="50" required/>
    </div>
</div>
<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Descripcion</label>
    <div class="col-lg-4">
        <input type="text" name="desc" id="desc" class="form-control" value="{{old('desc', $data->desc ?? '')}}" maxlength="100" required/>
    </div>
</div>
<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Costo Unit</label>
    <div class="col-lg-4">
        <input type="text" name="costounitario" id="costounitario" class="form-control numerico" value="{{old('costounitario', $data->costounitario ?? '')}}" maxlength="10" required/>
    </div>
</div>
<div class="form-group">
    <label for="tipocosto_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Tipo de Costo">Tipo de Costo</label>
    <div class="col-lg-4">
        <select name="tipocosto_id" id="tipocosto_id" class="form-control select2 tipocosto_id" required>
            <option value="">Seleccione...</option>
            @foreach($tipocostos as $id => $nombre)
                <option
                    value="{{$id}}"
                    @if ( isset($data) and ($data->tipocosto_id==$id))
                        {{'selected'}}
                    @endif
                    >{{$nombre}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label for="unidadmedida_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Unidad de Medida">Unidad de Medida</label>
    <div class="col-lg-4">
        <select name="unidadmedida_id" id="unidadmedida_id" class="form-control select2 unidadmedida_id" required>
            <option value="">Seleccione...</option>
            @foreach($unidadmedidas as $id => $descripcion)
                <option
                    value="{{$id}}"
                    @if ( isset($data) and ($data->unidadmedida_id==$id))
                        {{'selected'}}
                    @endif
                    >{{$descripcion}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label for="activo" class="col-lg-3 control-label requerido" title="Activo o Inactivo.">Activo</label>
    <div class="col-lg-4">
        <select name="activo" id="activo" class="form-control select2 activo" required>
            <option value="">Seleccione...</option>
            <option value="1"
                @if (isset($data) and ($data->activo=="1"))
                    {{'selected'}}
                @endif
            >Activo</option>
            <option value="0"
                @if (isset($data) and ($data->activo=="0"))
                    {{'selected'}}
                @endif    
            >Inactivo</option>
        </select>
    </div>
</div>