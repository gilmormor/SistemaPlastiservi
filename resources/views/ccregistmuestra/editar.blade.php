@extends("theme.$theme.layout")
@section('titulo')
CC — Editar Muestra #{{ $muestra->id }}
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/ccregistmuestra/editar.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')

        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-pencil"></i> Editar Muestra CC #{{ $muestra->id }}
                </h3>
                <div class="box-tools pull-right">
                    <a href="{{ route('ccregistmuestra') }}" class="btn btn-info btn-sm">
                        <i class="fa fa-reply"></i> Volver
                    </a>
                </div>
            </div>
            <div class="box-body">
                {{-- Datos contextuales del registro de producción --}}
                @php $first = $muestra->dets->first(); $reg = $first ? $first->ccparamApsucetapaprod : null; @endphp
                <div class="row" style="background:#f9f9f9; padding:10px; border-radius:4px; margin-bottom:15px;">
                    <div class="col-xs-2"><strong>Muestra:</strong><br>#{{ $muestra->id }}</div>
                    <div class="col-xs-2"><strong>Reg. Prod.:</strong><br>#{{ $muestra->opdetregprod_id }}</div>
                    <div class="col-xs-3"><strong>Registrado por:</strong><br>{{ $muestra->usuario->nombre ?? '—' }}</div>
                    <div class="col-xs-2"><strong>Fecha:</strong><br>{{ $muestra->fechahora }}</div>
                    <div class="col-xs-3">
                        <strong>Status actual:</strong><br>
                        @php $sl = [1=>['Aprobado','success'],2=>['Aprobado c/obs','warning'],3=>['Rechazado','danger']][$muestra->status] ?? ['—','default']; @endphp
                        <span class="label label-{{ $sl[1] }}" style="font-size:13px;">{{ $sl[0] }}</span>
                    </div>
                </div>

                <form action="{{ route('actualizar_ccregistmuestra', ['id' => $muestra->id]) }}"
                      id="form-editar-muestra" class="form-horizontal" method="POST" autocomplete="off">
                    @csrf
                    @method('PUT')
                    {{-- Timestamp numérico para detectar modificación concurrente --}}
                    <input type="hidden" name="updated_at" value="{{ $muestra->updated_at->timestamp }}">

                    {{-- Parámetros medidos --}}
                    <h4 style="color:#f39c12; margin-bottom:10px;">
                        <i class="fa fa-check-circle-o"></i> Valores medidos
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
                                @foreach($muestra->dets as $det)
                                @php
                                    $cap = $det->ccparamApsucetapaprod;
                                    $cp  = $cap ? $cap->ccparam : null;
                                    $tipoLabel = ['number'=>'Numérico','text'=>'Texto','boolean'=>'Cumple/No Cumple'][$cp->tipo ?? ''] ?? '—';
                                    // Si la muestra se valido contra el acuerdo tecnico, manda el
                                    // rango congelado en el detalle, no el rango fijo de la etapa.
                                    $desdeAt = $det->rango_min !== null || $det->rango_max !== null;
                                    $rgMin   = $desdeAt ? $det->rango_min : ($cap ? $cap->valor_min : null);
                                    $rgMax   = $desdeAt ? $det->rango_max : ($cap ? $cap->valor_max : null);
                                    // Mismos decimales que en la toma de muestra: los configurados
                                    // en el parámetro pueden redondear el rango y volverlo ilegible.
                                    $dec     = \App\Models\CcTolerancia::decimalesNecesarios(
                                                   [$rgMin, $rgMax, $det->valor_objetivo],
                                                   ($cp && $cp->decimales) ? $cp->decimales : 2
                                               );
                                @endphp
                                <tr id="fila-det-{{ $det->id }}">
                                    <td>
                                        <strong>{{ $cp->etiqueta ?? '—' }}</strong>
                                        @if($cp && $cp->unidad)
                                            <small class="text-muted">({{ $cp->unidad }})</small>
                                        @endif
                                        <br><small class="text-muted">{{ $cp->nombre ?? '' }}</small>
                                    </td>
                                    <td class="text-center">{{ $tipoLabel }}</td>
                                    <td class="text-center">
                                        @if($rgMin !== null)
                                            <span class="text-info">{{ number_format($rgMin, $dec, ',', '.') }}</span>
                                            @if($desdeAt && $det->valor_objetivo !== null)
                                                <br><small class="text-muted" title="Valor del acuerdo técnico">obj. {{ number_format($det->valor_objetivo, $dec, ',', '.') }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($rgMax !== null)
                                            <span class="text-info">{{ number_format($rgMax, $dec, ',', '.') }}</span>
                                            @if($desdeAt && $det->tolerancia !== null)
                                                <br><small class="text-muted" title="Tolerancia aplicada">±{{ rtrim(rtrim(number_format($det->tolerancia, 4, ',', '.'), '0'), ',') }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($cp && $cp->tipo === 'boolean')
                                            <select name="valor_param[{{ $det->id }}]"
                                                class="form-control cc-param-input"
                                                data-det-id="{{ $det->id }}"
                                                data-tipo="{{ $cp->tipo }}">
                                                <option value="1" {{ $det->valor == '1' ? 'selected' : '' }}>Cumple</option>
                                                <option value="0" {{ $det->valor == '0' ? 'selected' : '' }}>No Cumple</option>
                                            </select>
                                        @else
                                            <input type="{{ ($cp && $cp->tipo === 'number') ? 'number' : 'text' }}"
                                                name="valor_param[{{ $det->id }}]"
                                                class="form-control cc-param-input"
                                                data-det-id="{{ $det->id }}"
                                                data-tipo="{{ $cp->tipo ?? 'text' }}"
                                                value="{{ old('valor_param.' . $det->id, $det->valor) }}"
                                                @if($cp && $cp->tipo === 'number')
                                                    step="{{ ($cp->decimales ?? 0) > 0 ? '0.' . str_repeat('0', ($cp->decimales - 1)) . '1' : '1' }}"
                                                @endif
                                                placeholder="{{ ($cp && $cp->unidad) ? 'Ej: 100 ' . $cp->unidad : 'Ingrese valor' }}"
                                                {{ ($cap && $cap->requerido) ? 'required' : '' }}>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span id="resultado-det-{{ $det->id }}" class="label label-default" style="font-size:12px; padding:5px 8px;">—</span>
                                    </td>
                                    <td class="text-center">
                                        @if($cap && $cap->requerido)
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

                    {{-- Semáforo resumen --}}
                    <div class="row" style="margin-top:10px; margin-bottom:15px;">
                        <div class="col-xs-12 col-sm-4">
                            <strong>Status general:</strong>
                            <span id="status-resumen" class="label label-default" style="font-size:14px; margin-left:8px; padding:5px 12px;">
                                Sin evaluar
                            </span>
                        </div>
                    </div>

                    {{-- Observación --}}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Observación:</label>
                        <div class="col-sm-10">
                            <textarea name="observacion" class="form-control" rows="2" maxlength="500"
                                placeholder="Observaciones opcionales...">{{ old('observacion', $muestra->observacion) }}</textarea>
                        </div>
                    </div>

                    <div class="box-footer text-center" style="margin-top:15px;">
                        <a href="{{ route('ccregistmuestra') }}" class="btn btn-default">
                            <i class="fa fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning" style="margin-left:10px;">
                            <i class="fa fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- JSON para el JS de recálculo en tiempo real (clave = det.id) --}}
<script>
window._ccParams = {!! $ccParamsJson !!};
</script>
@endsection
