<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<input type="hidden" name="updated_at" id="updated_at" value="{{old('updated_at', session('opdet_updatednum_at') ?? '')}}">
<div class="row">
    <div class="form-group col-xs-12 col-sm-1">
        <label for="rut" class="control-label" title="RUT Cliente">RUT</label>
        <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $opdet->op->otdet->ot->cliente->rut ?? '')}}" readonly/>
    </div>
    <div class="form-group col-xs-12 col-sm-8">
        <label for="rut" class="control-label" title="Razon Social">Nombre Cliente</label>
        <input type="text" name="rut" id="rut" class="form-control" value="{{old('rut', $opdet->op->otdet->ot->cliente->razonsocial ?? '')}}" readonly/>
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-12 col-sm-1">
        <label for="ot_id" class="control-label" title="OT ID">OT ID</label>
        <input type="text" name="ot_id" id="ot_id" class="form-control" value="{{old('ot_id', $opdet->op->otdet->ot_id ?? '')}}" readonly/>
    </div>
    <div class="form-group col-xs-12 col-sm-1">
        <label for="op_id" class="control-label" title="OP ID">OP ID</label>
        <input type="text" name="op_id" id="op_id" class="form-control" value="{{old('op_id', $opdet->op_id ?? '')}}" readonly/>
    </div>
    <div class="form-group col-xs-12 col-sm-1">
        <label for="opdet_id" class="control-label" title="OpDet ID">OpDet ID</label>
        <input type="text" name="opdet_id" id="opdet_id" class="form-control" value="{{old('opdet_id', $opdet->id ?? '')}}" readonly/>
    </div>
    <div class="form-group col-xs-12 col-sm-1">
        <label for="kgrec" class="control-label" title="Kilos procesar">Kg Procesar</label>
        <input type="text" name="kgrec" id="kgrec" class="form-control" value="{{old('kgrec', number_format($opdet->kgrec, 2, ',', '.') ?? '')}}" style="text-align:right;" readonly/>
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-12 col-sm-1">
        <label for="producto_id" class="control-label" title="Producto ID">Producto ID</label>
        <input type="text" name="producto_id" id="producto_id" class="form-control" value="{{old('producto_id', $opdet->op->otdet->producto_id ?? '')}}" readonly/>
    </div>
    <div class="form-group col-xs-12 col-sm-1">
        <label for="acuerdotecnico_id" class="control-label" title="Acuerdo Tecnico ID">AT ID</label>
        <a class="btn-accion-tabla btn-sm tooltipsC" title="" onclick="genpdfAcuTec({{$opdet->op->otdet->producto->acuerdotecnico->id}},{{$opdet->op->otdet->ot->cliente_id}},1)" data-original-title="Acuerdo Técnico: {{$opdet->op->otdet->producto->acuerdotecnico->id}}">
            {{$opdet->op->otdet->producto->acuerdotecnico->id}}
        </a>
    </div>
    <div class="form-group col-xs-12 col-sm-4">
        <label for="producto_nombre" class="control-label" title="Descripcion producto">Producto</label>
        <input type="text" name="producto_nombre" id="producto_nombre" class="form-control" value="{{old('producto_nombre', $opdet->op->otdet->producto->acuerdotecnico->nombre_producto ?? '')}}" readonly/>
    </div>


    <div class="form-group col-xs-12 col-sm-1">
        <label for="at_ancho" class="control-label" title="Ancho producto">Ancho</label>
        <input type="text" name="at_ancho" id="at_ancho" class="form-control" value="{{old('at_ancho', $opdet->op->otdet->producto->acuerdotecnico->at_ancho ?? '')}}" readonly/>
    </div>
    <div class="form-group col-xs-12 col-sm-1">
        <label for="at_largo" class="control-label" title="Largo producto">Largo</label>
        <input type="text" name="at_largo" id="at_largo" class="form-control" value="{{old('at_largo', $opdet->op->otdet->producto->acuerdotecnico->at_ancho ?? '')}}" readonly/>
    </div>
    <div class="form-group col-xs-12 col-sm-1">
        <label for="espesorprod" class="control-label" title="Espesor Produccion">Espesor Prod</label>
        <input type="text" name="espesorprod" id="espesorprod" class="form-control" value="{{old('espesorprod', number_format($opdet->op->otdet->espesorprod, 3, ',', '.') ?? '')}}" readonly/>
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-12 col-sm-3">
        <label for="ot_obs" class="control-label" title="OT Observacion">Observacion OT</label>
        <textarea class="form-control" name="ot_obs" id="ot_obs" placeholder="Observación" disabled>{{$opdet->op->otdet->ot->obs}}</textarea>
    </div>
    <div class="form-group col-xs-12 col-sm-3">
        <label for="op_obs" class="control-label">Observacion OP</label>
        <textarea class="form-control" name="op_obs" id="op_obs" title="OP Observacion" placeholder="Observación" disabled>{{$opdet->op->obs}}</textarea>
    </div>
    <div class="form-group col-xs-12 col-sm-3">
        <label for="opdet_obs" class="control-label">Observacion OpDet</label>
        <textarea class="form-control" name="opdet_obs" id="opdet_obs" title="OP Observacion" placeholder="Observación" disabled>{{$opdet->obs}}</textarea>
    </div>
</div>
<div class="form-group col-xs-12 col-sm-2">
    <div class="row">
        <div class="form-group col-xs-12 col-sm-12">
            <label for="kgprod" class="control-label" title="Kg Procesados">Kg Procesados</label>
            <input type="text" name="kgprod" id="kgprod" class="form-control" value="{{old('kgprod', number_format($opdet->kgprod, 2, ',', '.') ?? '')}}" style="text-align:right;" readonly/>
        </div>
        <div class="form-group col-xs-12 col-sm-12">
            <label for="saldokg" class="control-label" title="Kg Faltantes">Kg Faltantes</label>
            <input type="text" name="saldokg" id="saldokg" class="form-control" value="{{old('saldokg', number_format($opdet->saldokg, 2, ',', '.') ?? '')}}" style="text-align:right;" readonly/>
        </div>
    </div>
</div>