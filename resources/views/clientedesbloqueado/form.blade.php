<input type="hidden" name="cliente_id" id="cliente_id" value="{{old('cliente_id', $data->cliente_id ?? '')}}">
@if($aux_sta == 0)
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
@endif
@if ($aux_sta == 1)
    <div class="form-group">
        <label for="notaventa_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Nota Venta ID">Nota Venta ID</label>
        <div class="col-lg-2">
            @if (!isset($data))
                <input type="text" name="notaventa_id" id="notaventa_id" class="form-control" value="{{old('notaventa_id', $data->notaventa_id ?? '')}}" maxlength="12" required/>
            @else
                <input type="text" name="notaventa_idenabled" id="notaventa_idenabled" class="form-control" value="{{old('notaventa_idenabled', $data->notaventa_id ?? '')}}" maxlength="12" required  disabled readonly/>
            @endif
        </div>
    </div>
    <div class="form-group">
        <label for="rut" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
        <div class="col-lg-2">
            <input type="text" name="rut" id="rut" class="form-control inputrut" value="{{old('rut', $data->cliente->rut ?? '')}}" onkeyup="llevarMayus(this);" maxlength="12" required disabled readonly/>
        </div>
    </div>    
@endif
@if ($aux_sta == 2)
    <div class="form-group">
        <label for="cotizacion_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Cotizacion ID">Cotizacion ID</label>
        <div class="col-lg-2">
            @if (!isset($data))
                <input type="text" name="cotizacion_id" id="cotizacion_id" class="form-control" value="{{old('cotizacion_id', $data->cotizacion_id ?? '')}}" maxlength="12" required/>
            @else
                <input type="text" name="cotizacion_idenabled" id="cotizacion_idenabled" class="form-control" value="{{old('cotizacion_idenabled', $data->cotizacion_id ?? '')}}" maxlength="12" required  disabled readonly/>
            @endif
        </div>
    </div>
    <div class="form-group">
        <label for="rut" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="RUT">RUT</label>
        <div class="col-lg-2">
            <input type="text" name="rut" id="rut" class="form-control inputrut" value="{{old('rut', $data->cliente->rut ?? '')}}" onkeyup="llevarMayus(this);" maxlength="12" required disabled readonly/>
        </div>
    </div>    
@endif

<div class="form-group">
    <label for="razonsocial" class="col-lg-3 control-label requerido">Razon Social</label>
    <div class="col-lg-8">
        <input type="text" name="razonsocial" id="razonsocial" class="form-control" value="{{old('razonsocial', $data->cliente->razonsocial ?? '')}}" disabled readonly/>
    </div>
</div>

<div class="form-group">
    <label for="modulo_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Modulos">Modulos</label>
    <div class="col-lg-8">
        <div>
            <select name="modulo_id[]" id="modulo_id" class='selectpicker form-control modulo_id' data-live-search='true' multiple data-actions-box='true' required>
                @foreach($modulos as $modulo)
                    <option
                    value="{{$modulo->id}}"
                    {{is_array(old('modulo_id')) ? (in_array($modulo->id, old('modulo_id')) ? 'selected' : '') : (isset($data) ? ($data->modulos->firstWhere('id', $modulo->id) ? 'selected' : '') : '')}}
                    >{{$modulo->nombre}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="obs" class="col-lg-3 control-label requerido">Observación</label>
    <div class="col-lg-8">
        <textarea name="obs" id="obs" class="form-control" value="{{old('obs', $data->obs ?? '')}}" required>{{old('obs', $data->obs ?? '')}}</textarea>
    </div>
</div>
@include('generales.buscarcliente')