<div class="form-group">
    <label for="persona_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Lamina">Persona</label>
    <div class="col-lg-8">
        <select name="persona_id" id="persona_id" class="selectpicker form-control persona_id" data-live-search='true' title='Seleccione...'  required>
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