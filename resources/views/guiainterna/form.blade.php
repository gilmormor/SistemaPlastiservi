<div class="row">
    <div class="form-group col-xs-12 col-sm-2">
        <label for="fechadocumento" class="control-label requerido">Fecha</label>
        <input type="text" name="fechadocumento" id="fechadocumento" class="form-control pull-right datepicker"  value="{{old('fechadocumento', $data->fechadocumento ?? '')}}" readonly required>
    </div>
    <div class="form-group col-xs-12 col-sm-3">
        <label for="numerodocumento" class="control-label requerido" data-toggle='tooltip' title="Numero de Guia">Nro Guia</label>
        <input type="text" name="numerodocumento" id="numerodocumento" class="form-control" value="{{old('numerodocumento', $data->numerodocumento ?? '')}}" maxlength="70" required/>
    </div>
    <div class="form-group col-xs-12 col-sm-7">
        <label for="razonsocial" class="control-label requerido" data-toggle='tooltip' title="Razón Social">Razón Social</label>
        <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', $data->razonsocial ?? '')}}" maxlength="70" required/>
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-12 col-sm-4">
        <label for="descripcion" class="control-label requerido" data-toggle='tooltip' title="Producto">Producto</label>
        <input type="text" name="descripcion" id="descripcion" class="form-control" value="{{old('descripcion', $data->descripcion ?? '')}}" maxlength="70" required/>
    </div>
    <div class="form-group col-xs-12 col-sm-4">
        <label for="medidas" class="control-label requerido" data-toggle='tooltip' title="Medidas">Medidas</label>
        <input type="text" name="medidas" id="medidas" class="form-control" value="{{old('medidas', $data->medidas ?? '')}}" maxlength="70" required/>
    </div>
    <div class="form-group col-xs-12 col-sm-4">
        <label for="matprimdesc" class="control-label requerido" data-toggle='tooltip' title="Materia Prima">Materia Prima</label>
        <input type="text" name="matprimdesc" id="matprimdesc" class="form-control" value="{{old('matprimdesc', $data->matprimdesc ?? '')}}" maxlength="70" required/>
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-12 col-sm-2">
        <label for="kilos" class="control-label requerido" data-toggle='tooltip' title="Kilos">Kilos</label>
        <input type="text" name="kilos" id="kilos" class="form-control" value="{{old('kilos', $data->kilos ?? '')}}" maxlength="10" required/>
    </div>
    <div class="form-group col-xs-12 col-sm-2">
        <label for="unidades" class="control-label requerido" data-toggle='tooltip' title="Unidades">Unidades</label>
        <input type="text" name="unidades" id="unidades" class="form-control" value="{{old('unidades', $data->unidades ?? '')}}" maxlength="10" required/>
    </div>
    <div class="form-group col-xs-12 col-sm-4">
        <label for="unidadmedida_id" class="control-label requerido" data-toggle='tooltip' title="Unidad Medida">Unidad Medida</label>
        <select name="unidadmedida_id" id="unidadmedida_id" class="form-control select2 unidadmedida_id" required>
            <option value="">Seleccione...</option>
            @foreach($unidadmedidas as $id => $descripcion)
                <option
                    value="{{$id}}"
                    @if (($aux_sta==2) and ($data->unidadmedida_id==$id))
                        {{'selected'}}
                    @endif
                    >
                    {{$descripcion}}
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-xs-12 col-sm-4">
        <label for="subtotal" class="control-label requerido" data-toggle='tooltip' title="Valor Total">Valor Total</label>
        <input type="text" name="subtotal" id="subtotal" class="form-control" value="{{old('subtotal', $data->subtotal ?? '')}}" maxlength="70" required/>
    </div>
</div>
