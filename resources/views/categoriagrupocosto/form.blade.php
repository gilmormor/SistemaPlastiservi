<?php 
    use App\Models\CategoriaGrupoCosto;
    if(isset($data)){
        $mesanno = CategoriaGrupoCosto::mesanno($data->annomes);
    }
?>
<input type="hidden" name="categoriaprod_idH" id="categoriaprod_idH" value="{{old('categoriaprod_idH', $data->grupoprod->categoriaprod_id ?? '')}}"/>
<input type="hidden" name="idH" id="idH" value="{{old('idH', $data->id ?? '')}}"/>
<div class="form-group">
    <label for="annomes" class="col-lg-3 control-label requerido">Fecha</label>
    <div class="col-lg-8">
        <input type="text" name="annomes" id="annomes" class="form-control date-picker" value="{{old('annomes', $mesanno ?? '')}}" readonly required>
    </div>
</div>

<div class="form-group">
    <label for="categoriaprod_id" class="col-lg-3 control-label requerido">Categoria</label>
    <div class="col-xs-12 col-md-8 col-sm-8">
        <select name="categoriaprod_id" id="categoriaprod_id" class="form-control select2 categoriaprod_id" required>
            <option value="">Seleccione...</option>
            @if (isset($categoriaprods))
                @foreach($categoriaprods as $categoriaprod)
                    <option
                        value="{{$categoriaprod->id}}"
                        @if ( isset($data) and ($data->grupoprod->categoriaprod_id==$categoriaprod->id))
                            {{'selected'}}
                        @endif
                    >
                    {{$categoriaprod->nombre}}
                    </option>
                @endforeach
            @endif
        </select>
    </div>
</div>
<div class="form-group">
    <label for="grupoprod_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Campo de agrupación">Grupo</label>
    <div class="col-xs-12 col-md-8 col-sm-8">
        <select name="grupoprod_id" id="grupoprod_id" class="form-control select2 grupoprod_id" required>
            <option value="">Seleccione...</option>
            @if (isset($grupoprods))
                @foreach($grupoprods as $grupoprod)
                    <option value="{{$grupoprod->id}}"
                        @if (isset($data) and ($data->grupoprod_id==$grupoprod->id))
                            {{'selected'}}
                        @endif
                        >
                        {{$grupoprod->gru_nombre}}
                    </option>
                @endforeach                
            @endif
        </select>
    </div>
</div>

<div class="form-group">
    <label for="unidadmedida_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Unidad Medida de Factura">Unidad Medida</label>
    <div class="col-lg-8">
        <select name="unidadmedida_id" id="unidadmedida_id" class="form-control select2 unidadmedida_id" required>
            <option value="">Seleccione...</option>
            @foreach($unidadmedidas as $id => $descripcion)
                <option
                    value="{{$id}}"
                    @if (isset($data) and ($data->unidadmedida_id==$id))
                        {{'selected'}}
                    @endif
                    >
                    {{$descripcion}}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="costo" class="col-lg-3 control-label requerido">Costo</label>
    <div class="col-lg-8">
        <input type="text" name="costoV" id="costoV" class="form-control numerico" valor="{{old('costo', $data->costo ?? '')}}" value="{{old('costo', $data->costo ?? '')}}" required/>
        <input type="hidden" name="costo" id="costo" value="{{old('costo', $data->costo ?? '')}}" required/>
    </div>
</div>