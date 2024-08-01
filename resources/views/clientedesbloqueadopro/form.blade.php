<input type="hidden" name="cliente_id" id="cliente_id" value="{{old('cliente_id', $data->cliente_id ?? '')}}">
<div class="form-group">
    <label for="rut" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
    @if (!isset($data))
        <div class="col-lg-2">
            <input type="text" name="rut" id="rut" class="form-control inputrut" value="{{old('rut', $data->cliente->rut ?? '')}}" onkeyup="llevarMayus(this);" maxlength="12" required/>
        </div>
        <div class="col-lg-1">
            <button class="btn btn-default" type="button" id="btnbuscarcliente" name="btnbuscarcliente" data-toggle='tooltip' title="Buscar">Buscar</button>
        </div>
    @else
        <div class="col-lg-3">
            <input type="text" name="rutenabled" id="rutenabled" class="form-control inputrut" value="{{old('rutenabled', $data->cliente->rut ?? '')}}" maxlength="12" required disabled readonly/>
        </div>
    @endif
</div>

<div class="form-group">
    <label for="razonsocial" class="col-lg-3 control-label requerido">Razon Social</label>
    <div class="col-lg-8">
        <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', $data->cliente->razonsocial ?? '')}}" disabled readonly/>
    </div>
</div>
<div class="form-group">
    <label for="obs" class="col-lg-3 control-label requerido">Observación</label>
    <div class="col-lg-8">
        <textarea name="obs" id="obs" class="form-control" value="{{old('obs', $data->obs ?? '')}}" required>{{old('obs', $data->obs ?? '')}}</textarea>
    </div>
</div>
@include('generales.buscarclientebd')