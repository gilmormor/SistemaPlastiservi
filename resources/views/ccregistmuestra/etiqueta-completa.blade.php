<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta Muestra CC #{{ $muestra->id }}</title>
    <style>
        /* Papel normal — tamaño A5 o carta (se ajusta solo) */
        @page { margin: 10mm 12mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 9pt; background: #fff; color: #000; }

        .etiq-box {
            border: 2px solid #000;
            border-radius: 3px;
            padding: 8px 10px;
            max-width: 14cm;
            margin: 0 auto;
        }
        .etiq-header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 6px;
        }
        .etiq-header h1 {
            font-size: 10pt;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .etiq-numero {
            font-size: 14pt;
            font-weight: bold;
            background: #1a5fb4;
            color: #fff;
            display: inline-block;
            padding: 2px 12px;
            border-radius: 3px;
            margin-top: 3px;
            letter-spacing: 1px;
        }
        /* Banda resultado */
        .etiq-result {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            padding: 4px;
            margin: 6px 0;
            border-radius: 3px;
            color: #fff;
        }
        .result-1 { background: #00a65a; }
        .result-2 { background: #f39c12; }
        .result-3 { background: #dd4b39; }
        .result-5 { background: #7f8c8d; }

        .etiq-datos-row {
            display: flex;
            gap: 8px;
            margin-bottom: 6px;
        }
        .etiq-datos {
            flex: 1;
            font-size: 8.5pt;
        }
        .etiq-datos table { width: 100%; border-collapse: collapse; }
        .etiq-datos td { padding: 2px 3px; vertical-align: top; }
        .etiq-datos td:first-child { font-weight: bold; white-space: nowrap; width: 38%; color: #333; }

        .etiq-qr-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 70px;
        }
        .etiq-qr-col small { font-size: 7pt; text-align: center; margin-top: 2px; }

        /* Tabla de parámetros */
        .etiq-params { margin-top: 6px; }
        .etiq-params h4 {
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #999;
            padding-bottom: 2px;
            margin-bottom: 4px;
            color: #333;
        }
        .etiq-params table { width: 100%; border-collapse: collapse; font-size: 8pt; }
        .etiq-params thead th {
            background: #333;
            color: #fff;
            padding: 2px 4px;
            text-align: center;
            font-size: 7.5pt;
        }
        .etiq-params tbody td {
            border: 1px solid #ccc;
            padding: 2px 4px;
            text-align: center;
        }
        .etiq-params tbody td:first-child { text-align: left; }
        .res-ok   { color: #007a3d; font-weight: bold; }
        .res-obs  { color: #b35900; font-weight: bold; }
        .res-bad  { color: #c00; font-weight: bold; }

        /* Motivo (status=5) */
        .etiq-motivo {
            background: #f5f5f5;
            border: 1px solid #ccc;
            border-radius: 3px;
            padding: 5px 8px;
            font-size: 8.5pt;
            margin-top: 6px;
        }

        /* Observación */
        .etiq-obs {
            background: #e8f4fd;
            border: 1px solid #b8daff;
            border-radius: 3px;
            padding: 5px 8px;
            font-size: 8.5pt;
            margin-top: 5px;
        }

        .etiq-footer {
            border-top: 1px solid #ccc;
            margin-top: 6px;
            padding-top: 3px;
            font-size: 7pt;
            color: #666;
            text-align: right;
        }

        .btn-imprimir {
            display: block;
            width: 200px;
            margin: 15px auto 8px;
            padding: 8px 16px;
            background: #1a5fb4;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
        }
        @media print {
            .btn-imprimir { display: none !important; }
            body { margin: 0; }
        }
    </style>
</head>
<body>

<button class="btn-imprimir" onclick="window.print()">Imprimir etiqueta</button>

@php
    $resultClass = [1=>'result-1', 2=>'result-2', 3=>'result-3', 5=>'result-5'];
    $resultText  = [1=>'APROBADO', 2=>'APROBADO CON OBSERVACIONES', 3=>'RECHAZADO', 5=>'SIN PARÁMETROS'];
    $rc = $resultClass[$muestra->status] ?? 'result-5';
    $rt = $resultText[$muestra->status]  ?? '—';
    $tipoMap = ['number' => 'Numérico', 'text' => 'Texto', 'boolean' => 'Cumple/No Cumple'];
    $resLabel = [1 => ['OK','res-ok'], 2 => ['Obs','res-obs'], 3 => ['Fuera rango','res-bad']];
@endphp

<div class="etiq-box">
    <div class="etiq-header">
        <h1>Muestra Control de Calidad</h1>
        <div class="etiq-numero">MUESTRA #{{ str_pad($muestra->id, 8, '0', STR_PAD_LEFT) }}</div>
    </div>

    <div class="etiq-result {{ $rc }}">{{ $rt }}</div>

    <div class="etiq-datos-row">
        <div class="etiq-datos">
            <table>
                <tr>
                    <td>Producto:</td>
                    <td>{{ $productoNombre }}</td>
                </tr>
                <tr>
                    <td>Etapa:</td>
                    <td>{{ $etapaNombre }}</td>
                </tr>
                <tr>
                    <td>Lote prod.:</td>
                    <td>#{{ $muestra->opdetregprod_id }}</td>
                </tr>
                <tr>
                    <td>Fecha muestra:</td>
                    <td>{{ \Carbon\Carbon::parse($muestra->fechahora)->format('d/m/Y H:i:s') }}</td>
                </tr>
                <tr>
                    <td>Registrado por:</td>
                    <td>{{ $usuarioCC }}</td>
                </tr>
                <tr>
                    <td>OP / OT:</td>
                    <td>OP-{{ $op_id }} / OT-{{ $ot_id }}</td>
                </tr>
                <tr>
                    <td>Kg producidos:</td>
                    <td>{{ number_format($kgprod, 2, ',', '.') }} kg
                        @if($cantprod) / {{ number_format($cantprod, 2, ',', '.') }} {{ $unidadmedida }} @endif
                    </td>
                </tr>
            </table>
        </div>
        <div class="etiq-qr-col">
            <div id="qrcode"></div>
            <small>CC #{{ $muestra->id }}</small>
        </div>
    </div>

    @if($muestra->status == 5)
        {{-- Sin parámetros: solo muestra el motivo --}}
        <div class="etiq-motivo">
            <strong>Motivo:</strong> {{ $muestra->observacion ?: '—' }}
        </div>
    @else
        {{-- Tabla de parámetros medidos --}}
        @if($muestra->dets->isNotEmpty())
        <div class="etiq-params">
            <h4>Parámetros medidos</h4>
            <table>
                <thead>
                    <tr>
                        <th style="text-align:left;width:35%">Parámetro</th>
                        <th style="width:12%">Tipo</th>
                        <th style="width:12%">Mínimo</th>
                        <th style="width:12%">Máximo</th>
                        <th style="width:14%">Medido</th>
                        <th style="width:15%">Resultado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($muestra->dets as $det)
                    @php
                        $cap = $det->ccparamApsucetapaprod;
                        $cp  = $cap ? $cap->ccparam : null;
                        $rl  = $resLabel[$det->resultado] ?? ['—', ''];
                    @endphp
                    <tr>
                        <td>{{ $cp ? $cp->etiqueta : '—' }}
                            @if($cp && $cp->unidad)<br><span style="font-size:7pt;color:#555;">({{ $cp->unidad }})</span>@endif
                        </td>
                        <td>{{ $cp ? ($tipoMap[$cp->tipo] ?? $cp->tipo) : '—' }}</td>
                        <td>{{ $det->rango_min !== null || $det->rango_max !== null ? ($det->rango_min !== null ? rtrim(rtrim(number_format($det->rango_min,4,',','.'),'0'),',') : '—') : ($cap && $cap->valor_min !== null ? $cap->valor_min : '—') }}</td>
                        <td>{{ $det->rango_min !== null || $det->rango_max !== null ? ($det->rango_max !== null ? rtrim(rtrim(number_format($det->rango_max,4,',','.'),'0'),',') : '—') : ($cap && $cap->valor_max !== null ? $cap->valor_max : '—') }}</td>
                        <td><strong>{{ $cp && $cp->tipo === 'boolean' ? ($det->valor === '1' || $det->valor === 1 ? 'Cumple' : ($det->valor === '0' || $det->valor === 0 ? 'No Cumple' : $det->valor)) : $det->valor }}</strong></td>
                        <td class="{{ $rl[1] }}">{{ $rl[0] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($muestra->observacion)
        <div class="etiq-obs">
            <strong>Observación:</strong> {{ $muestra->observacion }}
        </div>
        @endif
    @endif

    <div class="etiq-footer">Plastiservi &mdash; Sistema ERP</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById("qrcode"), {
        text: "{{ url('ccregistmuestra/' . $muestra->id . '/ver') }}",
        width: 65,
        height: 65,
        correctLevel: QRCode.CorrectLevel.M
    });
</script>
</body>
</html>
