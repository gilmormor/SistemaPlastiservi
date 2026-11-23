<input type="hidden" name="etapaprod_id" id="etapaprod_id" value="{{old('etapaprod_id', $opdet->areaproduccionsucetapaprod->etapaprod_id ?? '')}}">
<input type="hidden" name="sucursal_id" id="sucursal_id" value="{{old('sucursal_id', $opdet->op->otdet->ot->sucursal_id ?? '')}}">
<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<input type="hidden" name="guardar_updatednum_at" id="guardar_updatednum_at" value="{{old('guardar_updatednum_at', session('opdet_updatednum_at') ?? '')}}">
<input type="hidden" name="editar_updatednum_at" id="editar_updatednum_at" value="{{old('editar_updatednum_at', isset($data->updated_at) ? (strtotime($data->updated_at) ?? '') : '')}}">
<input type="hidden" name="cantent" id="cantent" value="{{old('cantent', isset($data) ? ($data->cantent ?? '0') : ($opdet->cant ?? 0))}}"/>
<input type="hidden" name="cantprod" id="cantprod" value="{{old('cantprod', $data->cantprod ?? '0')}}"/>
{{-- Unidades que representan los kilos; viaja aparte de cantprod --}}
<input type="hidden" name="cantunid" id="cantunid" value="{{old('cantunid', $data->cantunid ?? '')}}"/>
{{-- UM de entrada: la de SALIDA de la etapa anterior de este producto --}}
<input type="hidden" name="unidadmedidaent_id" id="unidadmedidaent_id" value="{{old('unidadmedidaent_id', $tablas['um']['ent_id'] ?? $opdet->areaproduccionsucetapaprod->unidadmedida_id ?? '')}}"/>
{{-- UM de salida: la configurada para este producto en acuerdotecnicoapsucetapaprod --}}
<input type="hidden" name="unidadmedidasal_id" id="unidadmedidasal_id" value="{{old('unidadmedidasal_id', $tablas['um']['sal_id'] ?? $opdet->areaproduccionsucetapaprod->unidadmedida_id ?? '')}}"/>
{{-- Unidad en que se cuenta ademas de la cantidad; vacia = la etapa no lo pide --}}
<input type="hidden" name="cantunid_unidadmedida_id" id="cantunid_unidadmedida_id" value="{{old('cantunid_unidadmedida_id', $tablas['um']['cant_id'] ?? '')}}"/>
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
    <div class="form-group col-xs-12 col-sm-1">
        <label for="kg" class="control-label" title="Total Kg OT">Total Kg OT</label>
        <input type="text" name="kg" id="kg" class="form-control" value="{{old('kg', number_format($opdet->op->otdet->kg, 2, ',', '.') ?? '')}}" valor="{{$opdet->op->otdet->kg}}" style="text-align:right;" readonly disabled/>
    </div>
    <div class="form-group col-xs-12 col-sm-1">
        <label for="kgprog" class="control-label" title="Kilos Programados">Kg Prog</label>
        <input type="text" name="kgprog" id="kgprog" class="form-control" value="{{old('kgprog', number_format($opdet->op->otdet->kgprog, 2, ',', '.') ?? '')}}" valor="{{$opdet->op->otdet->kgprog}}" style="text-align:right;" readonly disabled/>
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
            <input type="text" name="aux_kgprod_opdet" id="aux_kgprod_opdet" class="form-control" value="{{old('aux_kgprod_opdet', number_format($opdet->kgprod + $opdet->totales_pendientes->total_kgprod - ($data->kgprod ?? 0), 2, ',', '.') ?? '')}}" valor={{$opdet->kgprod + $opdet->totales_pendientes->total_kgprod  - ($data->kgprod ?? 0)}} style="text-align:right;" readonly disabled/>
        </div>
        <div class="form-group col-xs-12 col-sm-12">
            <label for="saldokg" class="control-label" title="Kg Faltantes">Kg Faltantes</label>
            {{-- saldokg dinamico: saldo kg del opdet + (kgent del registro editado) - (pendientes_kgprod + pendientes_kgscrap) --}}
            <input type="text" name="saldokg" id="saldokg" class="form-control" valor="{{$opdet->saldokg  ? ($opdet->saldokg + ($data->kgent ?? 0) - ($opdet->totales_pendientes->total_kgprod + $opdet->totales_pendientes->total_kgscrap)) : '0'}}" value="{{old('saldokg', number_format($opdet->saldokg + ($data->kgent ?? 0) - ($opdet->totales_pendientes->total_kgprod + $opdet->totales_pendientes->total_kgscrap), 2, ',', '.') ?? '')}}" style="text-align:right;" readonly disabled/>
        </div>
    </div>
</div>
<div class="form-group col-xs-12 col-sm-2">
    <div class="row">
        <div class="form-group col-xs-12 col-sm-12">
            <label for="aux_kgprod" class="control-label" title="Kg producidos (buenos). kgent = kgprod + kgscrap se calcula automático">Kg Producción</label>
            <input type="text" name="aux_kgprod" id="aux_kgprod" nomcamp="kgprod" class="form-control numerico requerido validarsaldokg sumarkg" valor="{{$data->kgprod ?? ''}}" valorOriginal="{{$data->kgprod ?? '0'}}" value="{{old('kgprod', $data->kgprod ?? '')}}" style="text-align:right;" maxlength="10"/>
            <input type="hidden" name="kgprod" id="kgprod" value="{{old('kgprod', $data->kgprod ?? '')}}" class="form-control requerido"/>
            <input type="hidden" name="kgent" id="kgent" value="{{old('kgent', $data->kgent ?? '')}}"/>
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
{{-- Maquina del registro de produccion.

     Se muestra la maquina en la que se programo el item (informativa, bloqueada) y
     un select para que el operario indique en cual produjo realmente: la programada
     puede estar ocupada y terminar produciendo en otra. Queda en blanco a proposito,
     para que el operario la elija, y es obligatoria.

     Solo aparece en etapas que tienen maquinas asignadas (maquinaetapaprod). En
     Mezclado, Distribucion y las demas sin maquinas no hay nada que elegir y el
     bloque no se muestra, igual que antes de este cambio. --}}
@if($tablas['usamaquina'] ?? false)
<div class="form-group col-xs-12 col-sm-2">
    <div class="row">
        <div class="form-group col-xs-12 col-sm-12">
            <label for="maquina_prog_nombre" class="control-label" title="Maquina asignada en la programacion de la OP">Máquina Programada</label>
            <input type="text" id="maquina_prog_nombre" class="form-control" value="{{$tablas['maquina_prog']->nombre ?? 'Sin asignar'}}" readonly disabled/>
        </div>
        <div class="form-group col-xs-12 col-sm-12">
            @if(count($tablas['maquinas']) > 0)
                <label for="maquina_id" class="control-label requerido" data-toggle='tooltip' title="Maquina en la que se produjo realmente">Máquina</label>
                <select name="maquina_id" id="maquina_id" class="form-control select2" required>
                    <option value="">Seleccione...</option>
                    @foreach($tablas['maquinas'] as $aux_maquina)
                        <option
                            value="{{$aux_maquina->id}}"
                            {{-- Kg que esta maquina puede procesar de mas (extrusoras sin
                                 cuenta metros). 0 en las demas: ahi nada cambia. --}}
                            data-exceso="{{$aux_maquina->kgexceso_permitido ?? 0}}"
                            @if(old('maquina_id', $tablas['maquina_sel'] ?? '') == $aux_maquina->id)
                                {{'selected'}}
                            @endif
                            >{{$aux_maquina->nombre}}</option>
                    @endforeach
                </select>
            @else
                {{-- La etapa usa maquinas pero ninguna esta en una sucursal del usuario:
                     se avisa el motivo en vez de dejar un select vacio con required,
                     que daria el mensaje genérico "Falta: Valor" sin explicar nada. --}}
                <label class="control-label" title="Maquina en la que se produjo realmente">Máquina</label>
                <p class="text-danger" style="font-size:11px;margin:0;">
                    No hay máquinas de esta etapa en su sucursal. Consulte con el administrador.
                </p>
            @endif
        </div>
    </div>
</div>
@endif
<div class="clearfix"></div>

{{-- R1: Checkbox muestra física. Solo aplica si la etapa requiere control de calidad
     (areaproduccionsucetapaprod.requiere_cc). Si la etapa no requiere CC, no tiene
     sentido registrar una muestra física de control de calidad.
     El hidden value="0" va ANTES del checkbox para que cuando esté desmarcado
     llegue 0, y cuando esté marcado el checkbox override con 1 (PHP toma el último). --}}
<input type="hidden" name="es_muestra" value="0">
@if($opdet->areaproduccionsucetapaprod->requiere_cc ?? 0)
<div class="form-group col-xs-12" id="div-es-muestra" style="margin-bottom:4px;">
    <div style="background:#fff8e1;border:1px solid #ffe082;border-radius:4px;padding:8px 14px;display:inline-block;">
        <label style="margin:0;font-weight:normal;cursor:pointer;">
            <input type="checkbox" name="es_muestra" id="es_muestra" value="1"
                {{ old('es_muestra', $data->es_muestra ?? 0) ? 'checked' : '' }}
                style="margin-right:6px;">
            <strong>Este registro es <span style="color:#e65100;">MUESTRA FÍSICA</span></strong>
            — no suma a producción, no genera entrada a bodega
        </label>
    </div>
</div>
@endif

{{-- Trazabilidad: selección de lote de origen (etapa anterior).
     Se muestra siempre que la etapa tenga una etapa anterior (así sea un solo lote).
     Si es la primera etapa (sin etapa anterior — el origen vendrá del futuro módulo
     de Materia Prima), esta sección no aparece y Kg Producción/Scrap se ingresan
     directo como siempre. --}}
<input type="hidden" id="excluir_temp_id" value="{{$data->id ?? ''}}">
<script>
    // Selección de lotes ya guardada (modo editar): kg = producción (kg total - scrap).
    var LOTEORIGEN_EXISTENTE = {!! json_encode(isset($data) ? $data->origenes->map(function($o){
        return ['id' => $o->opdetregprod_id, 'kg' => $o->kg - $o->scrap, 'scrap' => $o->scrap];
    })->values() : []) !!};
</script>
<div class="form-group col-xs-12" id="div-lote-origen" style="display:none;">
    <div class="box box-primary" style="margin-bottom:10px;">
        <div class="box-header with-border" style="display:flex;align-items:center;gap:8px;">
            <h3 class="box-title" style="margin:0;">Seleccione de qué lote viene esta producción</h3>
            <span class="label label-default" id="lote-origen-badge" style="margin-left:auto;"></span>
        </div>
        <div class="box-body">
            <p style="color:#777;font-size:12px;">
                Indique cuántos kg de producción y cuántos de scrap salieron de cada lote.
                Lo que no asigne queda disponible en el lote para un próximo registro (no se asume como scrap).
            </p>
            <div class="table-responsive">
                <table class="table table-condensed table-bordered" id="tabla-lote-origen">
                    <thead>
                        <tr>
                            <th style="width:60px;">Lote</th>
                            <th>Origen</th>
                            <th class="text-right" style="width:110px;">Pendientes</th>
                            <th class="text-right" style="width:140px;">Kg Producción</th>
                            <th class="text-right" style="width:140px;">Kg Scrap</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-lote-origen-body"></tbody>
                </table>
            </div>
            {{-- Con UN solo lote de origen el control de reparto no dice nada: el
                 campo Kg Produccion se llena desde la propia fila, asi que los dos
                 numeros son siempre identicos. En ese caso se muestra lo que si
                 importa, que es cuanto material le queda al lote despues de este
                 registro. Con varios lotes vuelve el control del reparto, que ahi
                 si hace falta para no pasarse ni quedarse corto. --}}
            <div id="lote-origen-pie-saldo" style="display:none;text-align:right;padding:6px 4px;background:#f4f4f4;border-radius:3px;">
                <span style="margin-right:20px;">Saldo del lote <strong id="lote-origen-saldo-nro"></strong>:
                    <strong id="lote-origen-saldo-disp">0,00</strong> kg</span>
                <span style="margin-right:20px;">Este registro: <strong id="lote-origen-saldo-usa">0,00</strong> kg</span>
                <span>Quedarían: <strong id="lote-origen-saldo-queda">0,00</strong> kg</span>
                <span id="lote-origen-saldo-exceso" style="display:none;color:#b7600a;margin-left:14px;"></span>
            </div>
            <div id="lote-origen-pie-reparto" style="text-align:right;padding:6px 4px;background:#f4f4f4;border-radius:3px;">
                <span style="margin-right:20px;">Kg Producción: <strong id="lote-origen-kg-registro">0,00</strong></span>
                <span style="margin-right:20px;">Kg asignados: <strong id="lote-origen-kg-asignado">0,00</strong></span>
                <span style="margin-right:20px;">Kg Scrap: <strong id="lote-origen-scrap-registro">0,00</strong></span>
                <span>Scrap asignado: <strong id="lote-origen-scrap-asignado">0,00</strong></span>
            </div>
            <div id="lote-origen-alerta" class="alert" style="margin-top:8px;margin-bottom:0;padding:8px 12px;font-size:12px;"></div>
        </div>
    </div>
</div>

{{-- Campos adicionales configurados para esta etapa (si los hay) --}}
@include('opdetregprodtemp._campos_adicionales')

<div class="form-group col-xs-12 col-sm-2">
    <div class="row">
        <div class="form-group col-xs-12 col-sm-12">
            <label for="aux_cantprod" class="control-label requerido" title="Cantidad producida en UM de salida de la etapa">Cant. Producida ({{$tablas['um']['sal_nombre'] ?? $opdet->areaproduccionsucetapaprod->unidadmedida->nombre ?? 'sin configurar'}})</label>
            <input type="text" name="aux_cantprod" id="aux_cantprod" class="form-control numerico requerido" valor="{{$data->cantprod  ?? ''}}" valorOriginal="{{$data->cantprod  ?? '0'}}" value="{{old('cantprod', number_format($data->cantprod ?? '0', 2, ',', '.'))}}" style="text-align:right;" maxlength="10"/>
        </div>
        {{-- Unidades que representan los kilos producidos. Aparece solo si la
             etapa lo pide para este producto (acuerdotecnicoapsucetapaprod.
             unidadmedidacant_id): cuando la etapa entrega en kilos, cant y
             cantkg quedan iguales y las unidades no tienen donde ir. --}}
        @if(!empty($tablas['um']['cant_id']))
        <div class="form-group col-xs-12 col-sm-12">
            <label for="aux_cantunid" class="control-label" title="Unidades que representan los kilos producidos">Cant. Unidades ({{$tablas['um']['cant_nombre']}})</label>
            <input type="text" name="aux_cantunid" id="aux_cantunid" class="form-control numerico" valor="{{$data->cantunid ?? ''}}" valorOriginal="{{$data->cantunid ?? '0'}}" value="{{old('cantunid', number_format($data->cantunid ?? '0', 2, ',', '.'))}}" style="text-align:right;" maxlength="10"/>
        </div>
        @endif
        <div class="form-group col-xs-12 col-sm-12">
            <label class="control-label" data-toggle='tooltip' title="Unidades de medida configuradas para la etapa">UM Etapa</label>
            <div style="font-size:11px;color:#555;">
                {{-- Unidades del producto en esta etapa, no las de la etapa --}}
                <strong>Entrada:</strong> {{ $tablas['um']['ent_nombre'] ?? '—' }}<br>
                <strong>Salida:</strong> {{ $tablas['um']['sal_nombre'] ?? 'sin configurar' }}
                @if(!empty($tablas['um']['cant_id']))
                <br><strong>Se cuenta en:</strong> {{ $tablas['um']['cant_nombre'] }}
                @endif
            </div>
        </div>
    </div>
</div>