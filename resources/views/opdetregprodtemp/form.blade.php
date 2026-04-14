<input type="hidden" name="etapaprod_id" id="etapaprod_id" value="{{old('etapaprod_id', $opdet->areaproduccionsucetapaprod->etapaprod_id ?? '')}}">
<input type="hidden" name="sucursal_id" id="sucursal_id" value="{{old('sucursal_id', $opdet->op->otdet->ot->sucursal_id ?? '')}}">
<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<input type="hidden" name="guardar_updatednum_at" id="guardar_updatednum_at" value="{{old('guardar_updatednum_at', session('opdet_updatednum_at') ?? '')}}">
<input type="hidden" name="editar_updatednum_at" id="editar_updatednum_at" value="{{old('editar_updatednum_at', isset($data->updated_at) ? (strtotime($data->updated_at) ?? '') : '')}}">
<input type="hidden" name="cant" id="cant" value="{{old('cant', isset($data) ? ($data->cant ?? '0') : $opdet->cant)}}"/>
<input type="hidden" name="unidadmedida_id" id="unidadmedida_id" value="{{old('cant', isset($opdet) ? $tablas['unidadmedida_id'] : "")}}"/>
<input type="hidden" name="cantrec" id="cantrec" value="{{old('cantrec', $opdet->cantrec ?? 0)}}"/>

<div class="row">
    <div class="form-group col-xs-12 col-sm-1">
        <label for="rut" class="control-label" title="RUT Cliente">RUT</label>
        <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $opdet->op->otdet->ot->cliente->rut ?? '')}}" readonly disabled/>
    </div>
    <div class="form-group col-xs-12 col-sm-5">
        <label for="rut" class="control-label" title="Razon Social">Nombre Cliente</label>
        <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $opdet->op->otdet->ot->cliente->razonsocial ?? '')}}" readonly disabled/>
    </div>
    <div class="form-group col-xs-12 col-sm-1">
        <label for="ot_id" class="control-label" title="OT ID">OT ID</label>
        <input type="text" name="ot_id" id="ot_id" class="form-control" value="{{old('ot_id', $opdet->op->otdet->ot_id ?? '')}}" readonly disabled/>
    </div>
    <div class="form-group col-xs-12 col-sm-1">
        <label for="op_id" class="control-label" title="OP ID">OP ID</label>
        <input type="text" name="op_id" id="op_id" class="form-control" value="{{old('op_id', $opdet->op_id ?? '')}}" readonly disabled/>
    </div>
    <div class="form-group col-xs-12 col-sm-1">
        <label for="opdet_id" class="control-label" title="OpDet ID">OpDet ID</label>
        <input type="text" name="opdet_id" id="opdet_id" class="form-control" value="{{old('opdet_id', $opdet->id ?? '')}}" readonly/>
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-12 col-sm-1">
        <label for="producto_id" class="control-label" title="Producto ID">Producto ID</label>
        <input type="text" name="producto_id" id="producto_id" class="form-control" value="{{old('producto_id', $opdet->op->otdet->producto_id ?? '')}}" readonly/>
    </div>
    <div class="form-group col-xs-12 col-sm-4">
        <label for="producto_nombre" class="control-label" title="Descripcion producto">Producto</label>
        @if (isset($opdet->op->otdet->producto->acuerdotecnico))
            <a class="btn-accion-tabla btn-sm tooltipsC" title="" onclick="genpdfAcuTec({{$opdet->op->otdet->producto->acuerdotecnico->id}},{{$opdet->op->otdet->ot->cliente_id}},1)" data-original-title="Acuerdo Técnico: {{$opdet->op->otdet->producto->acuerdotecnico->id}}">
                AT {{$opdet->op->otdet->producto->acuerdotecnico->id}}
            </a>            
        @endif
        <input type="text" name="producto_nombre" id="producto_nombre" class="form-control" value="{{old('producto_nombre', $opdet->op->otdet->producto->acuerdotecnico->nombre_producto ?? '')}}" readonly disabled/>
    </div>
    <div class="form-group col-xs-12 col-sm-1">
        <label for="at_ancho" class="control-label" title="Ancho producto">Ancho</label>
        <input type="text" name="at_ancho" id="at_ancho" class="form-control" value="{{old('at_ancho', $opdet->op->otdet->producto->acuerdotecnico->at_ancho ?? '')}}" readonly disabled/>
    </div>
    <div class="form-group col-xs-12 col-sm-1">
        <label for="at_largo" class="control-label" title="Largo producto">Largo</label>
        <input type="text" name="at_largo" id="at_largo" class="form-control" value="{{old('at_largo', $opdet->op->otdet->producto->acuerdotecnico->at_ancho ?? '')}}" readonly disabled/>
    </div>
    <div class="form-group col-xs-12 col-sm-1">
        <label for="espesorprod" class="control-label" title="Espesor Produccion">Espesor Prod</label>
        <input type="text" name="espesorprod" id="espesorprod" class="form-control" value="{{old('espesorprod', number_format($opdet->op->otdet->espesorprod, 3, ',', '.') ?? '')}}" readonly disabled/>
    </div>
    <div class="form-group col-xs-12 col-sm-1">
        <label for="kgrec" class="control-label" title="Kilos procesar">Kg Procesar</label>
        <input type="text" name="kgrec" id="kgrec" class="form-control" value="{{old('kgrec', number_format($opdet->kgrec, 2, ',', '.') ?? '')}}" valor="{{$opdet->kgrec}}" style="text-align:right;" readonly disabled/>
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-12 col-sm-3">
        <label for="ot_obs" class="control-label" title="Observacion Orden de Trabajo">Observacion OT</label>
        <textarea class="form-control" name="ot_obs" id="ot_obs" placeholder="Observación" disabled>{{$opdet->op->otdet->ot->obs}}</textarea>
    </div>
    <div class="form-group col-xs-12 col-sm-3">
        <label for="op_obs" class="control-label" title="Observacion Orden de Produccion">Observacion OP</label>
        <textarea class="form-control" name="op_obs" id="op_obs" title="Observacion Orden de Produccion" placeholder="Observación" disabled>{{$opdet->op->obs}}</textarea>
    </div>
    <div class="form-group col-xs-12 col-sm-3">
        <label for="opdet_obs" class="control-label" title="Observacion Detalle Orden de Produccion">Observacion OPDet</label>
        <textarea class="form-control" name="opdet_obs" id="opdet_obs" title="Observacion Detalle Orden de Produccion" placeholder="Observación" disabled>{{$opdet->obs}}</textarea>
    </div>
    <div class="form-group col-xs-12 col-sm-3">
        <label for="obs" class="control-label" title="Observacion Registro de Produccion">Observacion RegProd</label>
        <textarea class="form-control" name="obs" id="obs" title="Observacion Registro de Produccion" placeholder="Observación">{{$data->obs ?? ''}}</textarea>
    </div>
</div>
<div class="form-group col-xs-12 col-sm-2">
    <div class="row">
        <div class="form-group col-xs-12 col-sm-12">
            <label for="kgprod" class="control-label" title="Kg Procesados">Kg Procesados</label>
            <input type="text" name="kgprod" id="kgprod" class="form-control" value="{{old('kgprod', number_format($opdet->kgprod + $opdet->totales_pendientes->total_kg - ($data->kg ?? 0), 2, ',', '.') ?? '')}}" valor={{$opdet->kgprod + $opdet->totales_pendientes->total_kg  - ($data->kg ?? 0)}} style="text-align:right;" readonly disabled/>
        </div>
        <div class="form-group col-xs-12 col-sm-12">
            <label for="saldokg" class="control-label" title="Kg Faltantes">Kg Faltantes</label>
            {{-- <input type="text" name="saldokg" id="saldokg" class="form-control" valor="{{$opdet->saldokg  ? ($opdet->saldokg - ($opdet->totales_pendientes->total_kg + $opdet->totales_pendientes->total_kgscrap + ($data->kg ?? 0))) : '0'}}" value="{{old('saldokg', number_format($opdet->saldokg - ($opdet->totales_pendientes->total_kg + $opdet->totales_pendientes->total_kgscrap + ($data->kg ?? 0)), 2, ',', '.') ?? '')}}" style="text-align:right;" readonly disabled/> --}}
            <input type="text" name="saldokg" id="saldokg" class="form-control" valor="{{$opdet->saldokg  ? ($opdet->saldokg + ($data->kg ?? 0) - ($opdet->totales_pendientes->total_kg + $opdet->totales_pendientes->total_kgscrap)) : '0'}}" value="{{old('saldokg', number_format($opdet->saldokg + ($data->kg ?? 0) - ($opdet->totales_pendientes->total_kg + $opdet->totales_pendientes->total_kgscrap), 2, ',', '.') ?? '')}}" style="text-align:right;" readonly disabled/>
        </div>
    </div>
</div>
<div class="form-group col-xs-12 col-sm-2">
    <div class="row">
        <div class="form-group col-xs-12 col-sm-12">
            <label for="kg" class="control-label" title="Procesar Kg">Kg</label>
            <input type="text" name="aux_kg" id="aux_kg" nomcamp="kg" class="form-control numerico requerido validarsaldokg sumarkg" valor="{{$data->kg  ?? ''}}" valorOriginal="{{$data->kg  ?? '0'}}" value="{{old('kg', $data->kg  ?? '')}}" style="text-align:right;" maxlength="10"/>
            <input type="hidden" name="kg" id="kg" value="{{old('kg', $data->kg ?? '')}}" class="form-control requerido"/>
        </div>
        <div class="form-group col-xs-12 col-sm-12">
            <label for="kgscrap" class="control-label" title="Kg Scrap">Kg Scrap</label>
            <input type="text" name="aux_kgscrap" id="aux_kgscrap" nomcamp="kgscrap" class="form-control numerico requerido validarsaldokg sumarkg" valor="{{$data->kgscrap  ?? ''}}" valorOriginal="{{$data->kgscrap  ?? '0'}}"  value="{{old('kgscrap', $data->kgscrap ?? '')}}" style="text-align:right;" maxlength="10"/>
            <input type="hidden" name="kgscrap" id="kgscrap" value="{{old('kgscrap', $data->kgscrap ?? '')}}" class="form-control requerido"/>
        </div>
    </div>
</div>
<div class="form-group col-xs-12 col-sm-2">
    <div class="row">
        <div class="form-group col-xs-12 col-sm-12">
            <label for="mtslineal" class="control-label" title="Metros lineales">Metro Lineales</label>
            <input type="text" name="mtslineal" id="mtslineal" class="form-control numerico requerido" valor="{{$data->mtslineal  ?? '0'}}" valorOriginal="{{$data->mtslineal  ?? '0'}}" value="{{old('mtslineal', number_format($data->mtslineal ?? '0', 2, ',', '.'))}}" style="text-align:right;" disabled/>
        </div>
        <div class="form-group col-xs-12 col-sm-12">
            <label for="operario_id" class="control-label requerido" data-toggle='tooltip' title="Operario">Operario</label>
            <select name="operario_id" id="operario_id" class="form-control select2" required>
                <option value="">Seleccione...</option>
                @foreach($tablas['operarios'] as $operario)
                    <option
                        value="{{$operario->id}}"
                        @if (isset($data) and ($data->operario_id==$operario->id))
                            {{'selected'}}
                        @endif
                        >{{$operario->nombre}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="form-group col-xs-12 col-sm-2">
    <div class="row">
        <div class="form-group col-xs-12 col-sm-12">
            <label for="aux_cant" class="control-label" title="Cantidad">Cantidad</label>
            <input type="text" name="aux_cant" id="aux_cant" class="form-control numerico requerido" valor="{{$data->cant  ?? '0'}}" valorOriginal="{{$data->cant  ?? '0'}}" value="{{old('cant', number_format($data->cant ?? '0', 2, ',', '.'))}}" style="text-align:right;" readonly/>
        </div>
        <div class="form-group col-xs-12 col-sm-12">
            <label for="unidadmedida_id" class="control-label requerido" data-toggle='tooltip' title="Unidad Medida">Unidad Medida ({{$opdet->areaproduccionsucetapaprod->unidadmedida->nombre}})</label>
            <select name="unidadmedida_id" id="unidadmedida_id" class="form-control select2" required>
                <option value="">Seleccione...</option>
                @foreach($tablas['unidadmedidas'] as $unidadmedida)
                    <option
                        value="{{$unidadmedida->id}}"
                        @if (isset($data) and ($data->unidadmedida_id==$unidadmedida->id))
                            {{'selected'}}
                        @elseif (isset($opdet) and ($opdet->areaproduccionsucetapaprod->unidadmedida_id==$unidadmedida->id))
                            {{'selected'}}
                        @endif
                        >{{$unidadmedida->nombre}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>