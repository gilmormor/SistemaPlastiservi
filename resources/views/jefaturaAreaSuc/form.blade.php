<div class="form-group">
    <label for="suc_nombre" class="col-lg-3 control-label requerido">Sucursal</label>
    <div class="col-lg-8">
    <input type="text" name="suc_nombre" id="suc_nombre" class="form-control" value="{{old('suc_nombre', $sucursales[0]->suc_nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="are_nombre" class="col-lg-3 control-label requerido">Nombre Area</label>
    <div class="col-lg-8">
    <input type="text" name="are_nombre" id="are_nombre" class="form-control" value="{{old('are_nombre', $sucursales[0]->are_nombre ?? '')}}" required/>
    </div>
</div>

<div class="form-group">
    <label for="jefatura_id" class="col-lg-3 control-label requerido">Area</label>
    <div class="col-lg-8">
        <select name="jefatura_id[]" id="jefatura_id" class="form-control select2" multiple required>
            @foreach($jefaturas as $id => $nombre)
                <option
                    value="{{$id}}"
                    {{is_array(old('jefatura_id')) ? (in_array($id, old('jefatura_id')) ? 'selected' : '') : (isset($sucursales[0]) ? ($sucursales[0]->jefaturas->firstWhere('id', $id) ? 'selected' : '') : '')}}
                    >
                    {{$nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>