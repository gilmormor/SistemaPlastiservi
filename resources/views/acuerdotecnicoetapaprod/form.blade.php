<input type="hidden" name="usuario_id" id="usuario_id" value="{{old('usuario_id', auth()->id() ?? '')}}">
<div class="form-group">
    <label for="id" class="col-lg-3 control-label" title="Codigo Acuerdo Tecnico">Cod AT</label>
    <div class="col-lg-4">
        <input type="text" name="id" id="id" class="form-control" value="{{old('id', $data->id ?? '')}}" required disabled/>
    </div>
</div>
<div class="form-group">
    <label for="producto_id" class="col-lg-3 control-label" title="Codigo producto">Cod Producto</label>
    <div class="col-lg-4">
        <input type="text" name="producto_id" id="producto_id" class="form-control" value="{{old('producto_id', $data->producto_id ?? '')}}" required disabled/>
    </div>
</div>
<div class="form-group">
    <label for="nombre_producto" class="col-lg-3 control-label" title="Descripcion producto">Desc Producto</label>
    <div class="col-lg-4">
        <input type="text" name="nombre_producto" id="nombre_producto" class="form-control" value="{{old('nombre_producto', $data->nombre_producto ?? '')}}" required disabled/>
    </div>
</div>
{{-- Etapas del producto con la unidad en que sale de cada una.

     Antes era un select multiple que solo elegia las etapas: la unidad de salida
     quedaba sin definir y la produccion caia a la unidad de la etapa, que no
     siempre es la del producto (de Sellado uno puede salir en metros y otro en
     rollos, aunque los dos entren en kilos). Es la misma tabla que el modal de
     otitemprogramacion, para que las dos vias pidan lo mismo. --}}
<div class="form-group">
    <label class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Etapas Produccion">Etapas Produccion</label>
    <div class="col-lg-9">
        @if(count($tablas['etapaprods']) == 0)
            <p class="text-warning">No hay etapas de producción configuradas para este producto.</p>
        @else
        <p class="text-muted" style="margin-bottom:6px;">
            Marque las etapas por las que pasa el producto e indique en qué unidad sale de cada una.
        </p>
        <table class="table table-condensed table-hover" id="tabla-etapas-at">
            <thead>
                <tr>
                    <th style="width:40px;"></th>
                    <th>Etapa</th>
                    <th>Sucursal</th>
                    <th style="width:180px;">Unidad de salida <span class="text-danger">*</span></th>
                    <th style="width:200px;">Contar también en
                        <i class="fa fa-question-circle text-muted"
                           title="Solo cuando la etapa entrega en kilos: anota cuántas unidades hay en esos kilos. Déjelo vacío si no hace falta."></i>
                    </th>
                </tr>
            </thead>
            <tbody>
            @foreach($tablas['etapaprods'] as $aux_etapa)
                @php
                    $aux_id = $aux_etapa->areaproduccionsucetapaprod_id;
                    /* Tras un error de validacion se respeta lo que el usuario habia
                       marcado (old); si no, lo que esta guardado. */
                    $aux_old_etapas = old('apsucetapaprod_id');
                    $aux_marcada = is_array($aux_old_etapas)
                        ? in_array($aux_id, $aux_old_etapas)
                        : in_array($aux_id, $tablas['seleccionadas']);
                    /* Si la etapa ya tiene unidad configurada se respeta; si no, se
                       sugiere la de la etapa para no obligar a elegir desde cero. */
                    $aux_old_sal = old('unidadmedidasal_id');
                    $aux_sal = (is_array($aux_old_sal) and isset($aux_old_sal[$aux_id]))
                        ? $aux_old_sal[$aux_id]
                        : ($aux_etapa->unidadmedidasal_id ?: $aux_etapa->unidad_etapa_id);
                    /* La unidad en que contar no se sugiere: o esta configurada o no se pide. */
                    $aux_old_cant = old('unidadmedidacant_id');
                    $aux_cant = (is_array($aux_old_cant) and isset($aux_old_cant[$aux_id]))
                        ? $aux_old_cant[$aux_id]
                        : $aux_etapa->unidadmedidacant_id;
                @endphp
                <tr>
                    <td>
                        <input type="checkbox" class="chkEtapaAt" name="apsucetapaprod_id[]"
                               value="{{$aux_id}}" {{$aux_marcada ? 'checked' : ''}}>
                    </td>
                    <td>{{$aux_etapa->etapaprod_nombre}}</td>
                    <td>{{$aux_etapa->sucursal_nombre}}</td>
                    <td>
                        <select name="unidadmedidasal_id[{{$aux_id}}]" class="form-control input-sm selUnidadSalAt" data-etapa="{{$aux_id}}">
                            <option value="">Seleccione...</option>
                            @foreach($tablas['unidades'] as $aux_um)
                                <option value="{{$aux_um->id}}" {{(string)$aux_um->id === (string)$aux_sal ? 'selected' : ''}}>{{$aux_um->nombre}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="unidadmedidacant_id[{{$aux_id}}]" class="form-control input-sm selUnidadCantAt" data-etapa="{{$aux_id}}">
                            <option value="">— No se pide —</option>
                            @foreach($tablas['unidades'] as $aux_um)
                                <option value="{{$aux_um->id}}" {{(string)$aux_um->id === (string)$aux_cant ? 'selected' : ''}}>{{$aux_um->nombre}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
{{-- Id de Kgs: el JS lo necesita para habilitar o no "Contar tambien en",
     sin tenerlo escrito a mano. --}}
<script>
    const AT_UNIDAD_KG_ID = {{ $tablas['unidad_kg_id'] ?? 'null' }};
</script>