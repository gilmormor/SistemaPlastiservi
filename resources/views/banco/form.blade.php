<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Descripción</label>
    <div class="col-lg-8">
    <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Descripción</label>
    <div class="col-lg-8">
    <input type="text" name="desc" id="desc" class="form-control" value="{{old('desc', $data->desc ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="numcta" class="col-lg-3 control-label requerido">Número Cta</label>
    <div class="col-lg-8">
    <input type="text" name="numcta" id="numcta" class="form-control" value="{{old('numcta', $data->numcta ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="bancotipocta_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Tipo Cuenta">Tipo Cuenta</label>
    <div class="col-lg-8">
        <select name="bancotipocta_id" id="bancotipocta_id" class="form-control select2 bancotipocta_id" required>
            <option value="">Seleccione...</option>
            @foreach($bancotipoctas as $bancotipocta)
                <option
                    value="{{$bancotipocta->id}}"
                    @if (isset($data) and $data->bancotipocta_id==$bancotipocta->id)
                        {{'selected'}}
                    @endif
                    >
                    {{$bancotipocta->desc}}
                </option>
            @endforeach
        </select>
    </div>
</div>
