<link rel="stylesheet" href="{{asset("assets/css/factura.css")}}">

<br><br>
<div id="page_pdf">
    <table id="factura_head">
        <tr>
            <td class="logo_factura">
                <div>
                    <img src="{{asset("assets/$theme/dist/img/LOGO-PLASTISERVI.png")}}" style="max-width:1400%;width:auto;height:auto;">
                    <p>{{$empresa[0]['nombre']}}</p>
                    <p>RUT: {{$empresa[0]['rut']}}</p>
                </div>
            </td>
            <td class="info_empresa">
            </td>
            <td class="info_factura">
                <div class="round" style="padding-bottom:3px;">
                    <span class="h3">Muestra Control de Calidad</span>
                    <p>Muestra Nro: <strong>#{{ str_pad($muestra->id, 8, "0", STR_PAD_LEFT) }}</strong>
                        @if($muestra->anulacion)
                            <small style="background:#dd4b39;color:#fff;padding:2px 5px;border-radius:3px;">ANULADA</small>
                        @endif
                    </p>
                    <p>Fecha: {{ date('d-m-Y', strtotime($muestra->fechahora)) }}</p>
                    <p>Hora: {{ date('H:i:s', strtotime($muestra->fechahora)) }}</p>
                    @if($reg)
                    <p>Lote: #{{ $reg->id }}</p>
                    <p>OP {{ $reg->op_id }} / OT {{ $reg->ot_id }}</p>
                    @endif
                    <p>Registrado por: {{ $muestra->usuario->nombre ?? '—' }}</p>
                </div>
            </td>
        </tr>
    </table>

    {{-- Datos del cliente y lote de producción --}}
    @if($reg)
    <table id="factura_cliente">
        <tr>
            <td class="info_cliente">
                <div class="round">
                    <span class="h3">Cliente</span>
                    <table class="datos_cliente">
                        <tr class="headt">
                            <td style="width:10%"><label>Rut:</label></td>
                            <td style="width:40%"><p>{{ $reg->cliente_rut ? number_format(substr($reg->cliente_rut, 0, -1), 0, '', '.') . '-' . substr($reg->cliente_rut, -1) : '—' }}</p></td>
                            <td style="width:15%"><label>Razón Social:</label></td>
                            <td style="width:35%"><p>{{ $reg->cliente_razonsocial ?? '—' }}</p></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
    <table id="factura_cliente">
        <tr>
            <td class="info_cliente">
                <div class="round">
                    <span class="h3">Lote #{{ $reg->id }}</span>
                    <table class="datos_cliente">
                        <tr class="headt">
                            <td style="width:15%"><label>Producto:</label></td>
                            <td style="width:35%"><p>{{ $reg->producto_nombre }}</p></td>
                            <td style="width:15%"><label>Etapa:</label></td>
                            <td style="width:35%"><p>{{ $reg->etapaprod_nombre }}</p></td>
                        </tr>
                        <tr class="headt">
                            <td><label>Cant. Prod.:</label></td>
                            <td><p>{{ number_format($reg->cantprod, 2, ',', '.') }} {{ $reg->unidadmedidasal_nombre ?? '' }}</p></td>
                            <td><label>Kg Producidos:</label></td>
                            <td><p>{{ number_format($reg->kgprod, 2, ',', '.') }} Kg</p></td>
                        </tr>
                        <tr class="headt">
                            <td><label>Fecha Prod.:</label></td>
                            <td colspan="3"><p>{{ $reg->created_at ? date('d-m-Y H:i', strtotime($reg->created_at)) : '—' }}</p></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
    @endif

    {{-- Status y resultado general --}}
    @php
        $statusLabel = [1 => 'APROBADO', 2 => 'APROBADO CON OBSERVACIONES', 3 => 'RECHAZADO', 5 => 'No se pudo tomar la muestra'];
        $statusColor = [1 => '#00a65a', 2 => '#f39c12', 3 => '#dd4b39', 5 => '#999'];
        $statusText  = $statusLabel[$muestra->status] ?? '—';
        $statusBg    = $statusColor[$muestra->status]  ?? '#999';
    @endphp
    <table style="width:100%; margin-bottom:10px;">
        <tr>
            <td style="text-align:center; padding:8px; background:{{ $statusBg }}; color:#fff; font-size:13pt; font-weight:bold; border-radius:4px;">
                {{ $statusText }}
                @if($muestra->anulacion)
                    — ANULADA
                @endif
            </td>
        </tr>
    </table>

    @if($muestra->observacion)
    <table style="width:100%; margin-bottom:10px;">
        <tr>
            <td style="padding:6px 10px; border:1px solid #b8daff; background:#cce5ff; border-radius:4px;">
                <strong>Observación:</strong> {{ $muestra->observacion }}
            </td>
        </tr>
    </table>
    @endif

    @if($muestra->anulacion)
    <table style="width:100%; margin-bottom:10px;">
        <tr>
            <td style="padding:6px 10px; border:1px solid #f5c6cb; background:#f8d7da; border-radius:4px;">
                <strong>Motivo anulación:</strong> {{ $muestra->anulacion->motivo }}
                — por {{ $muestra->anulacion->usuario->nombre ?? '—' }}
            </td>
        </tr>
    </table>
    @endif

    @if($muestra->desbloqueo)
    <table style="width:100%; margin-bottom:10px;">
        <tr>
            <td style="padding:6px 10px; border:1px solid #ffeeba; background:#fff3cd; border-radius:4px;">
                <strong>Desbloqueado por:</strong> {{ $muestra->desbloqueo->usuario->nombre ?? '—' }}
                | <strong>Fecha:</strong> {{ $muestra->desbloqueo->created_at }}
                @if($muestra->desbloqueo->observacion)
                    | <strong>Obs:</strong> {{ $muestra->desbloqueo->observacion }}
                @endif
            </td>
        </tr>
    </table>
    @endif

    {{-- Parámetros evaluados (no aplica para status=5) --}}
    @if($muestra->status != 5)
    <div>
        <table id="factura_detalle">
            <thead>
                <tr>
                    <th class="textleft"  style="width:30%">Parámetro</th>
                    <th class="textcenter" style="width:12%">Tipo</th>
                    <th class="textcenter" style="width:12%">Mínimo</th>
                    <th class="textcenter" style="width:12%">Máximo</th>
                    <th class="textcenter" style="width:14%">Valor Medido</th>
                    <th class="textcenter" style="width:20%">Resultado</th>
                </tr>
            </thead>
            <tbody id="detalle_productos">
                @foreach($muestra->dets as $det)
                @php
                    $cap = $det->ccparamApsucetapaprod;
                    $cp  = $cap ? $cap->ccparam : null;
                    $resLabel = [1 => 'OK', 2 => 'Obs', 3 => 'Fuera de rango'];
                    $resBg    = [1 => '#00a65a', 2 => '#f39c12', 3 => '#dd4b39'];
                    $rl  = $resLabel[$det->resultado] ?? '—';
                    $rb  = $resBg[$det->resultado]   ?? '#999';
                    $tipoMap = ['number' => 'Numérico', 'text' => 'Texto', 'boolean' => 'Cumple/No Cumple'];
                @endphp
                <tr>
                    <td>{{ $cp ? $cp->etiqueta : '—' }}
                        @if($cp && $cp->unidad)<small> ({{ $cp->unidad }})</small>@endif
                    </td>
                    <td class="textcenter">{{ $cp ? ($tipoMap[$cp->tipo] ?? $cp->tipo) : '—' }}</td>
                    <td class="textcenter">{{ $det->rango_min !== null || $det->rango_max !== null ? ($det->rango_min !== null ? rtrim(rtrim(number_format($det->rango_min,4,',','.'),'0'),',') : '—') : ($cap && $cap->valor_min !== null ? $cap->valor_min : '—') }}</td>
                    <td class="textcenter">{{ $det->rango_min !== null || $det->rango_max !== null ? ($det->rango_max !== null ? rtrim(rtrim(number_format($det->rango_max,4,',','.'),'0'),',') : '—') : ($cap && $cap->valor_max !== null ? $cap->valor_max : '—') }}</td>
                    <td class="textcenter"><strong>{{ $cp && $cp->tipo === 'boolean' ? ($det->valor === '1' || $det->valor === 1 ? 'Cumple' : ($det->valor === '0' || $det->valor === 0 ? 'No Cumple' : $det->valor)) : $det->valor }}</strong></td>
                    <td class="textcenter">
                        <span style="background:{{ $rb }};color:#fff;padding:2px 8px;border-radius:3px;font-size:8pt;">
                            {{ $rl }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Pie: liberación al módulo de despacho --}}
    <table style="width:100%; margin-top:15px;">
        <tr>
            <td style="width:50%; padding-right:10px;">
                <table style="width:100%; border:1px solid #ccc; border-radius:4px; padding:8px;">
                    <tr>
                        <td style="font-size:9pt;"><strong>Liberado al despacho:</strong></td>
                        <td style="font-size:9pt; text-align:right;">
                            @if($muestra->sta_env)
                                Sí — {{ $muestra->fechahora_env }}<br>
                                <small>por {{ $muestra->usuarioStaenv->nombre ?? '—' }}</small>
                            @else
                                Pendiente
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:50%; padding-left:10px;">
                <table style="width:100%; border:1px solid #ccc; border-radius:4px; padding:8px;">
                    <tr>
                        <td style="font-size:9pt; text-align:center;">
                            <br><br>
                            ___________________________<br>
                            Responsable Control de Calidad
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</div>
