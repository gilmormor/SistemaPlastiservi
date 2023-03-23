<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-9">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Descripción</label>
    <div class="col-lg-9">
        <input type="text" name="desc" id="desc" class="form-control" value="{{old('desc', $data->desc ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="turno" class="col-lg-3 control-label requerido">Turno</label>
    <div class="col-lg-9">
        <select name="turno" id="turno" class="form-control select2" required>
            <option value="">Seleccione...</option>
            <option
                value="D"
                @if (isset($data->turno) and ($data->turno=="D"))
                    {{'selected'}}
                @endif
            >Dia</option>
            <option
                value="N"
                @if (isset($data->turno) and ($data->turno=="N"))
                    {{'selected'}}
                @endif
            >Noche</option>
        </select>
    </div>
</div>
<div class="form-group">
    <label for="ini" class="col-lg-3 control-label requerido">Inicio Turno</label>
    <div class="col-lg-9">
        <input type="text" name="ini" id="ini" class="form-control numerico" value="{{old('ini', $data->ini ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="fin" class="col-lg-3 control-label requerido">Fin Turno</label>
    <div class="col-lg-9">
        <input type="text" name="fin" id="fin" class="form-control numerico" value="{{old('fin', $data->fin ?? '')}}" required/>
    </div>
</div>