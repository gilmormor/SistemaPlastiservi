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
    <label for="sucursal_id" class="col-lg-3 control-label requerido">Sucursal</label>
    <div class="col-lg-4">
        <select name="sucursal_id" id="sucursal_id" class="form-control select2" required>
            <option value="">Seleccione...</option>
            @foreach($tablas['sucursales'] as $sucursal)
                <option
                    value="{{$sucursal->id}}"
                    @if (isset($data) and $sucursal->id == $data->sucursal_id)
                        selected                       
                    @endif
                    >{{$sucursal->nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="maquinagrupo_id" class="col-lg-3 control-label requerido">Maquina Grupo</label>
    <div class="col-lg-4">
        <select name="maquinagrupo_id" id="maquinagrupo_id" class="form-control select2" required>
            <option value="">Seleccione...</option>
            @foreach($tablas['maquinagrupo'] as $maquinagrupo)
                <option
                    value="{{$maquinagrupo->id}}"
                    @if (isset($data) and $maquinagrupo->id == $data->maquinagrupo_id)
                        selected                       
                    @endif
                    >{{$maquinagrupo->nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>
