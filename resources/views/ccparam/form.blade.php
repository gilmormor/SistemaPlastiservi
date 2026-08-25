<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre interno</label>
    <div class="col-lg-4">
        <input type="text" name="nombre" id="nombre" class="form-control"
               value="{{old('nombre', $data->nombre ?? '')}}" maxlength="50" required
               placeholder="ej: espesor, resistencia_sello"/>
        <span class="help-block">Identificador sin espacios, usado internamente.</span>
    </div>
</div>
<div class="form-group">
    <label for="etiqueta" class="col-lg-3 control-label requerido">Etiqueta visible</label>
    <div class="col-lg-4">
        <input type="text" name="etiqueta" id="etiqueta" class="form-control"
               value="{{old('etiqueta', $data->etiqueta ?? '')}}" maxlength="100" required
               placeholder="ej: Espesor (µm)"/>
    </div>
</div>
<div class="form-group">
    <label for="tipo" class="col-lg-3 control-label requerido">Tipo de dato</label>
    <div class="col-lg-3">
        <select name="tipo" id="tipo" class="form-control" required>
            <option value="">-- Seleccione --</option>
            <option value="number"  {{old('tipo', $data->tipo ?? '') == 'number'  ? 'selected' : ''}}>Numérico</option>
            <option value="text"    {{old('tipo', $data->tipo ?? '') == 'text'    ? 'selected' : ''}}>Texto</option>
            <option value="boolean" {{old('tipo', $data->tipo ?? '') == 'boolean' ? 'selected' : ''}}>Cumple/No Cumple</option>
        </select>
    </div>
</div>
<div class="form-group" id="row-unidad">
    <label for="unidad" class="col-lg-3 control-label">Unidad de medida</label>
    <div class="col-lg-2">
        <input type="text" name="unidad" id="unidad" class="form-control"
               value="{{old('unidad', $data->unidad ?? '')}}" maxlength="20"
               placeholder="ej: µm, mm, kg/cm²"/>
    </div>
</div>
<div class="form-group" id="row-decimales">
    <label for="decimales" class="col-lg-3 control-label requerido">Decimales</label>
    <div class="col-lg-1">
        <input type="number" name="decimales" id="decimales" class="form-control"
               value="{{old('decimales', $data->decimales ?? 2)}}" min="0" max="6" required/>
    </div>
</div>
<div class="form-group">
    <label for="orden" class="col-lg-3 control-label requerido">Orden</label>
    <div class="col-lg-1">
        <input type="number" name="orden" id="orden" class="form-control"
               value="{{old('orden', $data->orden ?? 0)}}" min="0" required/>
    </div>
</div>
