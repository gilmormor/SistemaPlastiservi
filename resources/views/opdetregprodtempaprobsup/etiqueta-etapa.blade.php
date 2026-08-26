<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta Etapa — OP {{ $op->id }}</title>
    <style>
        /* Medida fisica de la etiqueta termica: 10.4 cm x 5.08 cm */
        @page {
            size: 10.4cm 5.08cm;
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            background: #fff;
            /* Fuentes con trazo grueso/uniforme — mejor render en impresoras termicas
               que Arial regular (que sale borroso). Se usa sans-serif bold por defecto. */
            font-family: "Arial Black", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-weight: bold;
            -webkit-font-smoothing: none;
            -moz-osx-font-smoothing: grayscale;
            color: #000;
        }

        .etiqueta {
            width: 10.4cm;
            height: 5.08cm;
            padding: 2mm 3mm;
            margin: 0 auto;
            page-break-inside: avoid;
            page-break-after: always;
            overflow: hidden;
        }
        .etiqueta-titulo {
            text-align: center;
            font-size: 8pt;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 1px;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.1;
        }
        .etiqueta-proxima {
            text-align: center;
            font-size: 9pt;
            font-weight: bold;
            background: #000;
            color: #fff;
            padding: 1px 3px;
            margin-bottom: 2px;
            line-height: 1.1;
        }
        .etiqueta-body {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        .etiqueta-datos {
            display: table-cell;
            vertical-align: top;
            font-size: 7.5pt;
            font-weight: bold;
            padding-right: 2mm;
        }
        .etiqueta-datos table { width: 100%; border-collapse: collapse; }
        .etiqueta-datos td {
            padding: 0 2px;
            vertical-align: top;
            font-weight: bold;
            line-height: 1.15;
        }
        .etiqueta-datos td:first-child { white-space: nowrap; width: 1%; padding-right: 3px; }
        .etiqueta-qr {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            width: 22mm;
        }
        .etiqueta-qr small {
            display: block;
            font-size: 6pt;
            font-weight: bold;
            margin-top: 1px;
        }

        .btn-imprimir {
            display: block;
            width: 200px;
            margin: 10px auto 5px;
            padding: 8px 16px;
            background: #5cb85c;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
        }
        @media print {
            .btn-imprimir { display: none !important; }
            body { margin: 0; }
            .etiqueta { margin: 0; border: none; }
        }
    </style>
</head>
<body>

<button class="btn-imprimir" onclick="window.print()">Imprimir etiqueta</button>

<div class="etiqueta">
    <br>
    @if($produccion->es_muestra)
    <div style="background:#e65100;color:#fff;text-align:center;font-size:9pt;font-weight:bold;padding:2px 4px;margin-bottom:2px;letter-spacing:0.5px;">
        🔬MUESTRA CC — NO ES PRODUCCIÓN
    </div>
    @endif
    <div class="etiqueta-titulo">
        Etapa: {{ $produccion->opdet->areaproduccionsucetapaprod->etapaprod->nombre ?? '—' }}
    </div>
    <div class="etiqueta-proxima">
        Proxima: {{ $proximaEtapaNombre }}
    </div>
    <div class="etiqueta-body">
        <div class="etiqueta-datos">
            <table>
                <tr>
                    <td>Producto:</td>
                    <td>id {{ $producto_id }} - {{ $productoNombre }}</td>
                </tr>
                @if($clienteNombre)
                <tr>
                    <td>Cliente:</td>
                    <td>{{ $clienteNombre }}</td>
                </tr>
                @endif
                {{-- Aptitud para contacto con alimentos (pedido de Control de Calidad).
                     El color distingue de un vistazo apto / no apto / sin definir. --}}
                <tr>
                    <td colspan="2" style="padding-top:2px;">
                        <strong style="font-size:7pt; {{ $aptoAlimento === 'APTO PARA CONTACTO CON ALIMENTOS' ? 'color:#1b6e3f;' : ($aptoAlimento === 'NO APTO PARA CONTACTO CON ALIMENTOS' ? 'color:#9c2b21;' : 'color:#777;') }}">
                            {{ $aptoAlimento }}
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td>Cant / Kg:</td>
                    <td>{{ number_format($produccion->cantprod, 0, ',', '.') }} {{ $produccion->unidadmedidasal->nombre ?? 'UM' }} /
                        {{ number_format($produccion->kgprod, 2, ',', '.') }} kg</td>
                </tr>
                <tr>
                    <td>Fecha:</td>
                    <td>{{ $produccion->aprobfechahora ? \Carbon\Carbon::parse($produccion->aprobfechahora)->format('d/m/Y H:i') : '—' }}</td>
                </tr>
                <tr>
                    <td>OP/OT/Det:</td>
                    <td>OP-{{ $op->id }} / OT-{{ $ot->id }} / {{ $otdet->id }}</td>
                </tr>
                <tr>
                    <td>OpDet:</td>
                    <td>{{ $opdet->id }}</td>
                </tr>
                <tr>
                    <td>Operario:</td>
                    <td>{{ $operarioNombre }}</td>
                </tr>
                <tr>
                    <td>Maquina:</td>
                    <td>
                        @if($maquina)
                            Id:{{ $maquina->id }} Nom:{{ $maquina->nombre }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        <div class="etiqueta-qr">
            <div id="qrcode"></div>
            <small>Lote: {{ $produccion->id }}</small>
            @if($empresaNombre)
                <small style="font-weight:normal;">{{ $empresaNombre }}</small>
            @endif
        </div>
    </div>
</div>

<!-- QR Code library (CDN) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById("qrcode"), {
        text: "{{ $produccion->id }}",
        width: 70,
        height: 70,
        correctLevel: QRCode.CorrectLevel.M
    });
</script>
</body>
</html>
