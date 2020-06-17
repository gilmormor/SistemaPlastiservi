<div class="form-group">
    <label for="motivonc_id" class="col-lg-2 control-label requerido" data-toggle='tooltip' title="Motivo de la no conformidad">Motivo</label>
    <div class="col-lg-10">
        <select name="motivonc_id" id="motivonc_id" class="form-control select2 motivonc_id" required>
            <option value="">Seleccione...</option>
            @foreach($motivoncs as $id => $descripcion)
                <option
                    value="{{$id}}"
                    {{(isset($data->motivonc_id) ? ($data->motivonc_id==$id ? 'selected' : '') : '')}}
                    >
                    {{$descripcion}}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="puntonormativo" class="col-lg-2 control-label" data-toggle='tooltip' title="Descripción punto normativo">Punto normativo</label>
    <div class="col-lg-10">
        <textarea name="puntonormativo" id="puntonormativo" class="form-control" value="{{old('puntonormativo', $data->puntonormativo ?? '')}}">{{old('puntonormativo', $data->puntonormativo ?? '')}}</textarea>
    </div>
</div>
<div class="form-group">
    <label for="hallazgo" class="col-lg-2 control-label requerido" data-toggle='tooltip' title="Descripción de la observación o hallazgo">Hallazgo</label>
    <div class="col-lg-10">
        <textarea name="hallazgo" id="hallazgo" class="form-control" value="{{old('hallazgo', $data->hallazgo ?? '')}}" required>{{old('hallazgo', $data->hallazgo ?? '')}}</textarea>
    </div>
</div>
<div class="form-group">
    <label for="formadeteccionnc_id" class="col-lg-2 control-label requerido" data-toggle='tooltip' title="Forma detección No Conformidad">Forma detección</label>
    <div class="col-lg-10">
        <select name="formadeteccionnc_id" id="formadeteccionnc_id" class="form-control select2 formadeteccionnc_id" required>
            <option value="">Seleccione...</option>
            @foreach($formadeteccionncs as $id => $descripcion)
                <option
                    value="{{$id}}"
                    {{(isset($data->formadeteccionnc_id) ? ($data->formadeteccionnc_id==$id ? 'selected' : '') : '')}}
                    >
                    {{$descripcion}}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="jefatura_sucursal_area_id" class="col-lg-2 control-label requerido" data-toggle='tooltip' title="Area responsable">Area responsable</label>
    <div class="col-lg-10">
        <select name="jefatura_sucursal_area_id[]" id="jefatura_sucursal_area_id" class="form-control select2" multiple required>
            @foreach($jefaturasucursalareas as $jefaturasucursalarea)
                <option
                    value="{{$jefaturasucursalarea->id}}"
                    {{is_array(old('jefatura_sucursal_area_id')) ? (in_array($jefaturasucursalarea->id, old('jefatura_sucursal_area_id')) ? 'selected' : '') : (isset($data) ? ($data->jefaturasucursalareas->firstWhere('id', $jefaturasucursalarea->id) ? 'selected' : '') : '')}}
                    >
                    <!--{{$jefaturasucursalarea->sucursal_area->sucursal->abrev}}/{{$jefaturasucursalarea->sucursal_area->area->abrev}}/{{$jefaturasucursalarea->jefatura->nombre}}-->
                    {{$jefaturasucursalarea->jefatura->nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="certificado_id" class="col-lg-2 control-label requerido" data-toggle='tooltip' title="Norma">Norma</label>
    <div class="col-lg-10">
        <select name="certificado_id[]" id="certificado_id" class="form-control select2" multiple required>
            @foreach($certificados as $certificado)
                <option
                    value="{{$certificado->id}}"
                    {{is_array(old('certificado_id')) ? (in_array($certificado->id, old('certificado_id')) ? 'selected' : '') : (isset($data) ? ($data->certificados->firstWhere('id', $certificado->id) ? 'selected' : '') : '')}}
                    >
                    {{$certificado->descripcion}}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="puntonorma" class="col-lg-2 control-label requerido" data-toggle='tooltip' title="Punto de la norma">Punto de la norma</label>
    <div class="col-lg-10">
        <textarea name="puntonorma" id="puntonorma" class="form-control" value="{{old('puntonorma', $data->puntonorma ?? '')}}" required>{{old('puntonorma', $data->puntonorma ?? '')}}</textarea>
    </div>
</div>
<div class="form-group">
    <label for="jefatura_sucursal_areaR_id" class="col-lg-2 control-label requerido" data-toggle='tooltip' title="Responsable">Responsable</label>
    <div class="col-lg-10">
        <select name="jefatura_sucursal_areaR_id[]" id="jefatura_sucursal_areaR_id" class="form-control select2" multiple required>
            @foreach($jefaturasucursalareasR as $jefaturasucursalarea)
                <option
                    value="{{$jefaturasucursalarea->id}}"
                    {{is_array(old('jefatura_sucursal_areaR_id')) ? (in_array($jefaturasucursalarea->id, old('jefatura_sucursal_areaR_id')) ? 'selected' : '') : (isset($data) ? ($data->jefaturasucursalarearesponsables->firstWhere('id', $jefaturasucursalarea->id) ? 'selected' : '') : '')}}
                    >
                    <!--{{$jefaturasucursalarea->sucursal_area->sucursal->abrev}}/{{$jefaturasucursalarea->jefatura->abrev}}/{{$jefaturasucursalarea->persona->nombre}} {{$jefaturasucursalarea->persona->apellido}}-->
                    {{$jefaturasucursalarea->jefatura->nombre}}/{{$jefaturasucursalarea->persona->nombre}} {{$jefaturasucursalarea->persona->apellido}}
                </option>
            @endforeach
        </select>
    </div>
</div>