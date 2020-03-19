<div class="form-group">
    <label for="persona_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Lamina">Persona</label>
    <div class="col-lg-8">
        <select name="persona_id" id="persona_id" class="select2 form-control persona_id" data-live-search='true' required>
            <option value="">Seleccione...</option>
            @foreach($personas as $persona)
                <option
                    value="{{$persona->id}}"
                    @if (($aux_sta==2) and ($data->persona_id==$persona->id))
                        {{'selected'}}
                    @endif
                    >
                    {{$persona->nombre}} {{$persona->apellido}}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="sta_activo" class="col-lg-3 control-label requerido">Activo</label>
    <div class="col-lg-8">
        <select name="sta_activo" id="sta_activo" class="select2 form-control sta_activo"  data-live-search='true' required>
            <option value="">Seleccione...</option>
            <option value="1"
                @if (($aux_sta==2) and ($data->sta_activo=="1"))
                    {{'selected'}}
                @endif
            >Si</option>
            <option value="0"
                @if (($aux_sta==2) and ($data->sta_activo=="0"))
                    {{'selected'}}
                @endif            
            >No</option>
        </select>
    </div>
</div>
