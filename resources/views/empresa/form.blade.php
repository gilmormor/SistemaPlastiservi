<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-8">
    <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="rut" class="col-lg-3 control-label requerido">RUT</label>
    <div class="col-lg-8">
    <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $data->rut ?? '')}}" required/>
    </div>
</div>
<div class="form-group">
    <label for="iva" class="col-lg-3 control-label requerido">Iva</label>
    <div class="col-lg-8">
    <input type="text" name="iva" id="iva" class="form-control" value="{{old('iva', $data->iva ?? '')}}" required/>
    </div>
</div>

<div class="form-group">
    <label for="sucursal_id" class="col-lg-3 control-label requerido">Sucursal Principal</label>
    <div class="col-lg-8">
        <select name="sucursal_id" id="sucursal_id" class="form-control select2 sucursal_id" required>
            <option value="">Seleccione...</option>
            @foreach($sucursales as $sucursal)
                <option
                    value="{{$sucursal->id}}"
                    @if (($aux_sta==2) and ($data->sucursal_id==$sucursal->id))
                        {{'selected'}}
                    @endif
                    >
                    {{$sucursal->nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label for="diasprorrogacob" class="col-lg-3 control-label requerido">Dias Prorroga Cobranza</label>
    <div class="col-lg-8">
    <input type="text" name="diasprorrogacob" id="diasprorrogacob" class="form-control" value="{{old('diasprorrogacob', $data->diasprorrogacob ?? '')}}"  min="0" max="30" step="1" maxlength="2" required/>
    </div>
</div>

<div class="form-group">
    <div class="checkbox">
        <label class="col-sm-offset-3" style="font-size: 1.2em;display:flex;align-items: center;">
            <input type="checkbox" id="aux_actsiscob" name="aux_actsiscob">
            <span class="cr"><i class="cr-icon fa fa-check"></i></span>
            Actualizar informacion en Sistema externo de Cobranza?
        </label>
    </div>
</div>
<input type="hidden" name="actsiscob" id="actsiscob" value="{{old('actsiscob', $data->actsiscob ?? '0')}}">

<div class="form-group">
    <div class="checkbox">
        <label class="col-sm-offset-3" style="font-size: 1.2em;display:flex;align-items: center;">
            <input type="checkbox" id="aux_stabloxdeusiscob" name="aux_stabloxdeusiscob">
            <span class="cr"><i class="cr-icon fa fa-check"></i></span>
            Activar consulta Deuda y limite de Credito en Sistema Cobranza (Manager)?
        </label>
    </div>
</div>

<input type="hidden" name="stabloxdeusiscob" id="stabloxdeusiscob" value="{{old('stabloxdeusiscob', $data->stabloxdeusiscob ?? '0')}}">
