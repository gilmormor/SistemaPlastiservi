@extends("theme.$theme.layout")
@section('titulo')
CC — Ver Muestra #{{ $muestra->id }}
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-flask"></i> Muestra CC #{{ $muestra->id }}
                </h3>
                <div class="box-tools pull-right">
                    <a href="{{route('ccregistmuestra')}}" class="btn btn-info btn-sm">
                        <i class="fa fa-reply-all"></i> Listado
                    </a>
                </div>
            </div>
            <div class="box-body">
                @if($reg)
                <div class="row" style="background:#f9f9f9; padding:10px; border-radius:4px; margin-bottom:15px;">
                    <div class="col-xs-3"><strong>Reg. Prod.:</strong> #{{ $reg->id }}</div>
                    <div class="col-xs-3"><strong>OP/OT:</strong> OP {{ $reg->op_id }} / OT {{ $reg->ot_id }}</div>
                    <div class="col-xs-3"><strong>Producto:</strong> {{ $reg->producto_nombre }}</div>
                    <div class="col-xs-3"><strong>Etapa:</strong> {{ $reg->etapaprod_nombre }}</div>
                </div>
                @endif

                @php
                    $statusLabel = [
                        1 => ['Aprobado','success'],
                        2 => ['Aprobado c/obs','warning'],
                        3 => ['Rechazado','danger'],
                        5 => ['Sin parámetros','default'],
                    ];
                    $sl = $statusLabel[$muestra->status] ?? ['—','default'];
                    $anulada = $muestra->anulacion ? true : false;
                @endphp

                {{-- Alerta si está anulada --}}
                @if($anulada)
                <div class="alert alert-danger">
                    <strong><i class="fa fa-ban"></i> Muestra anulada.</strong>
                    Motivo: {{ $muestra->anulacion->motivo }}
                    — por {{ $muestra->anulacion->usuario->nombre ?? '—' }}
                </div>
                @endif

                <div class="row" style="margin-bottom:15px;">
                    <div class="col-xs-3">
                        <strong>Fecha:</strong><br>{{ $muestra->fechahora }}
                    </div>
                    <div class="col-xs-3">
                        <strong>Registrado por:</strong><br>{{ $muestra->usuario->nombre ?? '—' }}
                    </div>
                    <div class="col-xs-3">
                        <strong>Status CC:</strong><br>
                        <span class="label label-{{ $sl[1] }}" style="font-size:14px; padding:5px 10px;">
                            {{ $sl[0] }}
                        </span>
                    </div>
                    <div class="col-xs-3">
                        <strong>Liberado:</strong><br>
                        @if($muestra->sta_env)
                            <span class="label label-success" style="font-size:13px; padding:4px 8px;">
                                <i class="fa fa-check"></i> Sí
                            </span>
                            <br><small class="text-muted">{{ $muestra->fechahora_env }}<br>por {{ $muestra->usuarioStaenv->nombre ?? '—' }}</small>
                        @else
                            <span class="label label-default">Pendiente</span>
                        @endif
                    </div>
                </div>

                @if($muestra->observacion)
                <div class="alert alert-info">
                    <strong>Observación:</strong> {{ $muestra->observacion }}
                </div>
                @endif

                {{-- Detalles de parámetros --}}
                <table class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th>Parámetro</th>
                            <th>Tipo</th>
                            <th>Mínimo</th>
                            <th>Máximo</th>
                            <th>Valor medido</th>
                            <th>Resultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($muestra->dets as $det)
                        @php
                            $cap = $det->ccparamApsucetapaprod;
                            $cp  = $cap ? $cap->ccparam : null;
                            $resLabel = [1 => ['OK','success'], 2 => ['Obs','warning'], 3 => ['Fuera rango','danger']];
                            $rl = $resLabel[$det->resultado] ?? ['—','default'];
                        @endphp
                        <tr>
                            <td>{{ $cp ? $cp->etiqueta : '—' }} @if($cp && $cp->unidad)<small class="text-muted">({{ $cp->unidad }})</small>@endif</td>
                            <td>{{ $cp ? (['number'=>'Numérico','text'=>'Texto','boolean'=>'Cumple/No Cumple'][$cp->tipo] ?? $cp->tipo) : '—' }}</td>
                            <td class="text-center">{{ $det->rango_min !== null || $det->rango_max !== null ? ($det->rango_min !== null ? rtrim(rtrim(number_format($det->rango_min,4,',','.'),'0'),',') : '—') : ($cap && $cap->valor_min !== null ? $cap->valor_min : '—') }}</td>
                            <td class="text-center">{{ $det->rango_min !== null || $det->rango_max !== null ? ($det->rango_max !== null ? rtrim(rtrim(number_format($det->rango_max,4,',','.'),'0'),',') : '—') : ($cap && $cap->valor_max !== null ? $cap->valor_max : '—') }}</td>
                            <td><strong>{{ $cp && $cp->tipo === 'boolean' ? ($det->valor === '1' || $det->valor === 1 ? 'Cumple' : ($det->valor === '0' || $det->valor === 0 ? 'No Cumple' : $det->valor)) : $det->valor }}</strong></td>
                            <td><span class="label label-{{ $rl[1] }}">{{ $rl[0] }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Botones de etiqueta --}}
                <div class="text-center" style="margin-top:16px; margin-bottom:4px;">
                    <div class="btn-group">
                        <a href="{{ route('etiqueta_compacta_ccregistmuestra', $muestra->id) }}"
                           target="_blank"
                           class="btn btn-default btn-sm tooltipsC"
                           title="Etiqueta térmica compacta (10.4 x 5.08 cm) — para impresora Zebra">
                            <i class="fa fa-tag"></i> Etiqueta compacta
                        </a>
                        <a href="{{ route('etiqueta_completa_ccregistmuestra', $muestra->id) }}"
                           target="_blank"
                           class="btn btn-default btn-sm tooltipsC"
                           title="Etiqueta con tabla de parámetros medidos — para imprimir en papel">
                            <i class="fa fa-file-text-o"></i> Etiqueta completa
                        </a>
                    </div>
                </div>

                {{-- Acciones --}}
                @if(!$anulada)
                <div class="text-center" style="margin-top:10px;">
                    {{-- Nueva muestra del mismo registro --}}
                    <a href="{{ route('crear_ccregistmuestra', ['opdetregprod_id' => $muestra->opdetregprod_id]) }}"
                        class="btn btn-primary" style="margin:4px;">
                        <i class="fa fa-plus"></i> Nueva muestra del mismo registro
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
