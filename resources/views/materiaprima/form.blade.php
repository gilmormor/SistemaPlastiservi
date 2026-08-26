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
    <label for="pe" class="col-lg-3 control-label requerido">Peso Específico</label>
    <div class="col-lg-9">
        <input type="text" name="pe" id="pe" class="form-control numerico" value="{{old('pe', $data->pe ?? '')}}" required/>
    </div>
</div>
{{-- Aptitud para contacto con alimentos: la declara Control de Calidad y se
     imprime en las etiquetas de lote y de muestra. "Sin definir" es un estado
     válido: mientras CC no clasifique el material, la etiqueta no afirma nada. --}}
<div class="form-group">
    <label for="staaptoalimento" class="col-lg-3 control-label">Contacto con alimentos</label>
    <div class="col-lg-9">
        @php
            $aux_apto = old('staaptoalimento', isset($data->staaptoalimento) ? $data->staaptoalimento : '');
        @endphp
        <select name="staaptoalimento" id="staaptoalimento" class="form-control">
            @foreach(\App\Models\MateriaPrima::opcionesAptoAlimento() as $valor => $etiqueta)
                <option value="{{$valor}}" {{ (string)$aux_apto === (string)$valor ? 'selected' : '' }}>{{$etiqueta}}</option>
            @endforeach
        </select>
        <small class="text-muted">Se imprime en la etiqueta de cada lote. Déjelo en
            "Sin definir" mientras Control de Calidad no lo haya determinado.</small>
    </div>
</div>