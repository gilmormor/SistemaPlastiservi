@extends("theme.$theme.layout")
@section('titulo')
CC — Nueva Muestra
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/ccregistmuestra/crear.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')

        {{-- Cabecera informativa del registro de producción --}}
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-flask"></i> Nueva Muestra de Control de Calidad
                </h3>
                <div class="box-tools pull-right">
                    <a href="{{route('listaropdetregprod_ccregistmuestra')}}" class="btn btn-block btn-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver a la búsqueda
                    </a>
                </div>
            </div>
            <div class="box-body">
                {{-- Datos del registro de producción --}}
                <div class="row" style="background:#f9f9f9; padding:10px; border-radius:4px; margin-bottom:10px;">
                    <div class="col-xs-12 col-sm-2">
                        <strong>Reg. Producción:</strong><br>
                        <span class="label label-default" style="font-size:14px;">#{{ $reg->id }}</span>
                    </div>
                    <div class="col-xs-12 col-sm-2">
                        <strong>OP / OT:</strong><br>
                        OP {{ $reg->op_id }} / OT {{ $reg->ot_id }}
                    </div>
                    <div class="col-xs-12 col-sm-3">
                        <strong>Producto:</strong><br>
                        {{ $reg->producto_glosa ?: $reg->producto_nombre }}
                    </div>
                    {{-- Código de producto: abre el PDF del acuerdo técnico del producto,
                         con el cliente de la OT para que el acuerdo salga a su nombre.
                         Si el producto no tiene acuerdo técnico se muestra solo el código. --}}
                    <div class="col-xs-12 col-sm-1">
                        <strong>Código:</strong><br>
                        @if($reg->acuerdotecnico_id)
                            <a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC"
                               title="Acuerdo Técnico"
                               onclick='genpdfAcuTec({{ $reg->acuerdotecnico_id }}, {{ $reg->cliente_id ?: 0 }}, 1, "")'>
                                {{ $reg->producto_id }}
                            </a>
                        @else
                            {{ $reg->producto_id }}
                        @endif
                    </div>
                    {{-- Cliché: imagen del arte impreso, para verificar contra el acuerdo
                         técnico que lo impreso en el material es lo que corresponde.
                         Solo aparece si el producto es impreso y tiene arte cargado. --}}
                    <div class="col-xs-12 col-sm-1">
                        <strong>Cliché:</strong><br>
                        @if($reg->at_impresofoto)
                            <a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC"
                               title="Ver cliché — arte impreso del producto"
                               onclick='verCliche(@json($reg->at_impresofoto))'>
                                <i class="fa fa-picture-o"></i> Ver
                            </a>
                        @elseif($reg->at_impreso)
                            <span class="text-muted" title="El producto es impreso pero no tiene arte cargado en el acuerdo técnico">Sin arte</span>
                        @else
                            <span class="text-muted" title="Producto sin impresión">—</span>
                        @endif
                    </div>
                    <div class="col-xs-12 col-sm-1">
                        <strong>Etapa:</strong><br>
                        {{ $reg->etapaprod_nombre }}
                    </div>
                    <div class="col-xs-12 col-sm-2">
                        <strong>Kg producidos:</strong><br>
                        {{ number_format($reg->kgprod, 2, ',', '.') }} kg
                        @if($reg->cantprod)
                            / {{ number_format($reg->cantprod, 2, ',', '.') }} {{ $reg->unidadmedidasal_nombre }}
                        @endif
                    </div>
                    {{-- Cliente al que se le fabrica. Una OT libre (sin nota de venta)
                         puede no tener cliente asociado. --}}
                    <div class="col-xs-12 col-sm-12" style="margin-top:8px;">
                        <strong>Cliente:</strong>
                        {{ $reg->cliente_nombre ?: 'Sin cliente (OT libre)' }}
                    </div>
                </div>

                <form action="{{route('guardar_ccregistmuestra')}}" id="form-crear-muestra" class="form-horizontal" method="POST" autocomplete="off">
                    @csrf
                    <input type="hidden" name="opdetregprod_id" value="{{ $reg->id }}">
                    <input type="hidden" name="sin_parametros" id="sin_parametros" value="0">

                    {{-- Toggle R2: No se pudo tomar la muestra --}}
                    <div style="margin-bottom:14px;">
                        <a href="javascript:void(0)" id="btn-sin-params"
                           style="color:#e67e22; font-size:13px;">
                            <i class="fa fa-times-circle"></i> No se pudo tomar la muestra
                        </a>
                        <a href="javascript:void(0)" id="btn-con-params"
                           style="color:#27ae60; font-size:13px; display:none;">
                            <i class="fa fa-undo"></i> Sí pude tomar la muestra
                        </a>
                    </div>

                    {{-- Banner visible cuando sin_parametros=1 --}}
                    <div id="div-sin-params-banner" style="display:none; background:#fef9e7; border:1px solid #f39c12; border-radius:4px; padding:10px 14px; margin-bottom:14px; color:#7f6000;">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>Modo: Sin parámetros.</strong>
                        Esta muestra quedará registrada pero <strong>no bloqueará el despacho</strong>.
                        Ingrese el motivo en el campo de observación.
                    </div>

                    @if($params->isEmpty())
                        <div class="alert alert-warning" id="alerta-sin-config">
                            <i class="fa fa-exclamation-triangle"></i>
                            Esta etapa no tiene parámetros CC configurados.
                            Configure los parámetros en
                            <a href="{{route('areaproduccionsucetapaprod')}}">Etapa Prod x Área Produc</a>
                            antes de registrar una muestra con medición.
                        </div>
                    @else
                        {{-- Sección normal: tabla de parámetros y semáforo --}}
                        <div id="div-params-normal">
                            <h4 style="color:#f39c12; margin-bottom:15px;">
                                <i class="fa fa-check-circle-o"></i> Parámetros a medir
                            </h4>

                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed" id="tabla-params-cc">
                                    <thead>
                                        <tr>
                                            <th style="width:30%;">Parámetro</th>
                                            <th style="width:10%; text-align:center;">Tipo</th>
                                            <th style="width:10%; text-align:center;">Mínimo</th>
                                            <th style="width:10%; text-align:center;">Máximo</th>
                                            <th style="width:20%;">Valor medido</th>
                                            <th style="width:10%; text-align:center;">Resultado</th>
                                            <th style="width:10%; text-align:center;">Req.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($params as $p)
                                        @php
                                            $cp = $p->ccparam;
                                            $tipoLabel = ['number' => 'Numérico', 'text' => 'Texto', 'boolean' => 'Cumple/No Cumple'][$cp->tipo] ?? $cp->tipo;
                                            // Rango calculado desde el acuerdo tecnico del producto, si el
                                            // parametro esta configurado asi. Si no, manda el rango fijo.
                                            $rg     = isset($rangos[$p->id]) ? $rangos[$p->id] : null;
                                            $rgMin  = $rg ? $rg['min'] : $p->valor_min;
                                            $rgMax  = $rg ? $rg['max'] : $p->valor_max;
                                            // Los decimales configurados en el parámetro pueden ser menos de los
                                            // que el rango necesita: con 2 decimales, 0,025 y 0,031 se mostraban
                                            // ambos como "0,03". Se usan los que hagan falta, sin bajar de los
                                            // configurados. El step del input acompaña, si no no se podría teclear.
                                            $dec    = \App\Models\CcTolerancia::decimalesNecesarios(
                                                          [$rgMin, $rgMax, $rg ? $rg['objetivo'] : null],
                                                          $cp->decimales ?: 2
                                                      );
                                            $step   = $dec > 0 ? '0.' . str_repeat('0', $dec - 1) . '1' : '1';
                                        @endphp
                                        <tr id="fila-param-{{ $p->id }}">
                                            <td>
                                                <strong>{{ $cp->etiqueta }}</strong>
                                                @if($cp->unidad)
                                                    <small class="text-muted">({{ $cp->unidad }})</small>
                                                @endif
                                                <br><small class="text-muted">{{ $cp->nombre }}</small>
                                                {{-- Espesor: se mide contra el de producción, no contra el
                                                     comprometido con el cliente. Se muestran ambos para que
                                                     quien mide sepa cuál se está usando. --}}
                                                @if($rg && $rg['usa_prod'])
                                                    <br>
                                                    <small class="text-muted" title="Espesor comprometido con el cliente en el acuerdo técnico">
                                                        Esp AT: {{ rtrim(rtrim(number_format($rg['objetivo_at'], 4, ',', '.'), '0'), ',') }}
                                                    </small>
                                                    <small class="text-primary" style="font-weight:600;" title="Espesor de producción: es el que se valida">
                                                        &nbsp;Esp Prod: {{ rtrim(rtrim(number_format($rg['objetivo'], 4, ',', '.'), '0'), ',') }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $tipoLabel }}</td>
                                            <td class="text-center">
                                                @if($rgMin !== null)
                                                    <span class="text-info">{{ number_format($rgMin, $dec, ',', '.') }}</span>
                                                    @if($rg)
                                                        <br><small class="text-muted" title="Valor del acuerdo técnico">obj. {{ number_format($rg['objetivo'], $dec, ',', '.') }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($rgMax !== null)
                                                    <span class="text-info">{{ number_format($rgMax, $dec, ',', '.') }}</span>
                                                    @if($rg)
                                                        <br><small class="text-muted" title="Tolerancia del acuerdo técnico">{{ $rg['texto'] }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($cp->tipo === 'boolean')
                                                    <select
                                                        name="valor_param[{{ $p->id }}]"
                                                        class="form-control cc-param-input"
                                                        data-param-id="{{ $p->id }}"
                                                        data-tipo="{{ $cp->tipo }}"
                                                        data-min="{{ $rgMin }}"
                                                        data-max="{{ $rgMax }}"
                                                        {{ $p->requerido ? 'required' : '' }}>
                                                        <option value="">-- Seleccione --</option>
                                                        <option value="1">Cumple</option>
                                                        <option value="0">No Cumple</option>
                                                    </select>
                                                @else
                                                    <input type="{{ $cp->tipo === 'number' ? 'number' : 'text' }}"
                                                        name="valor_param[{{ $p->id }}]"
                                                        class="form-control cc-param-input"
                                                        data-param-id="{{ $p->id }}"
                                                        data-tipo="{{ $cp->tipo }}"
                                                        data-min="{{ $rgMin ?? '' }}"
                                                        data-max="{{ $rgMax ?? '' }}"
                                                        data-decimales="{{ $cp->decimales }}"
                                                        step="{{ $step }}"
                                                        placeholder="{{ $cp->unidad ? 'Ej: 100 ' . $cp->unidad : 'Ingrese valor' }}"
                                                        {{ $p->requerido ? 'required' : '' }}>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span id="resultado-param-{{ $p->id }}" class="label label-default" style="font-size:12px; padding:5px 8px;">
                                                    —
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($p->requerido)
                                                    <span class="label label-danger">Sí</span>
                                                @else
                                                    <span class="label label-default">No</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Semáforo resumen + check Aprobado c/obs --}}
                            <div class="row" style="margin-top:10px; margin-bottom:15px;">
                                <div class="col-xs-12 col-sm-5">
                                    <strong>Status general:</strong>
                                    <span id="status-resumen" class="label label-default" style="font-size:14px; margin-left:8px; padding:5px 12px;">
                                        Sin evaluar
                                    </span>
                                </div>
                                <div class="col-xs-12 col-sm-7" style="padding-top:4px;">
                                    <label style="font-weight:normal; cursor:pointer;">
                                        <input type="checkbox" id="chk-aprobado-obs" style="margin-right:6px;">
                                        <span id="lbl-aprobado-obs">
                                            Marcar como <strong>Aprobado con observaciones</strong>
                                            <span class="label label-warning" style="margin-left:4px;">Amarillo</span>
                                        </span>
                                    </label>
                                    <input type="hidden" name="forzar_status_2" id="forzar_status_2" value="0">
                                </div>
                            </div>
                        </div>{{-- fin #div-params-normal --}}
                    @endif

                    {{-- Observación (compartida: normal=opcional, sin_params=obligatoria) --}}
                    <div class="form-group" id="grupo-observacion">
                        <label class="col-sm-2 control-label" id="lbl-observacion">Observación:</label>
                        <div class="col-sm-10">
                            <textarea name="observacion" id="campo-observacion" class="form-control" rows="2" maxlength="500"
                                placeholder="Observaciones opcionales sobre la muestra...">{{ old('observacion') }}</textarea>
                            <small id="hint-observacion" class="text-danger" style="display:none;">
                                <i class="fa fa-exclamation-triangle"></i> La observación es obligatoria al aprobar con observaciones.
                            </small>
                        </div>
                    </div>

                    <div class="box-footer text-center" style="margin-top:20px;">
                        <a href="{{route('listaropdetregprod_ccregistmuestra')}}" class="btn btn-default">
                            <i class="fa fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning" style="margin-left:10px;" id="btn-guardar-muestra">
                            <i class="fa fa-save"></i> Guardar Muestra CC
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal contenedor del PDF del acuerdo técnico, que abre genpdfAcuTec().
     Va fuera de los contenedores para que Bootstrap lo posicione sobre la página. --}}
@include('generales.modalpdf')

{{-- JSON de parámetros para el JS de cálculo en tiempo real --}}
<script>
window._ccParams = {!! $ccParamsJson !!};
</script>
@endsection
