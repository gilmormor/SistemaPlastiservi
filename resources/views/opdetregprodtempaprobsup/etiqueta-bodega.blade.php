<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta Bodega — OP {{ $op->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #fff; }

        .etiqueta {
            width: 10cm;
            min-height: 7cm;
            border: 2px solid #000;
            padding: 8px 10px;
            margin: 10px auto;
            page-break-inside: avoid;
        }
        .etiqueta-titulo {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .etiqueta-body {
            display: flex;
            gap: 8px;
        }
        .etiqueta-datos {
            flex: 1;
            font-size: 8pt;
        }
        .etiqueta-datos table { width: 100%; border-collapse: collapse; }
        .etiqueta-datos td { padding: 2px 3px; vertical-align: top; }
        .etiqueta-datos td:first-child { font-weight: bold; white-space: nowrap; width: 42%; }
        .etiqueta-datos .val-grande {
            font-size: 10pt;
            font-weight: bold;
        }
        .etiqueta-qr {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 80px;
        }
        .etiqueta-qr small { font-size: 6pt; text-align: center; margin-top: 3px; }
        .etiqueta-footer {
            border-top: 1px solid #ccc;
            margin-top: 6px;
            padding-top: 3px;
            font-size: 7pt;
            color: #555;
            text-align: right;
        }

        /* Botón de impresión (se oculta al imprimir) */
        .btn-imprimir {
            display: block;
            width: 200px;
            margin: 20px auto 5px;
            padding: 8px 16px;
            background: #337ab7;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
        }
        @media print {
            .btn-imprimir { display: none !important; }
            body { margin: 0; }
            .etiqueta { margin: 0; border: 2px solid #000; }
        }
    </style>
</head>
<body>

<button class="btn-imprimir" onclick="window.print()">Imprimir etiqueta</button>

<div class="etiqueta">
    <div class="etiqueta-titulo">Ingreso Bodega Producción</div>
    <div class="etiqueta-body">
        <div class="etiqueta-datos">
            <table>
                <tr>
                    <td>Producto:</td>
                    <td class="val-grande">{{ $productoNombre }}</td>
                </tr>
                <tr>
                    <td>Cód. Producto:</td>
                    <td>{{ $productoCodigo }}</td>
                </tr>
                <tr>
                    <td>Cant / Kg:</td>
                    <td><strong>{{ number_format($produccion->cant, 0, ',', '.') }}</strong>
                        uds / <strong>{{ number_format($produccion->kg, 2, ',', '.') }}</strong> kg</td>
                </tr>
                <tr>
                    <td>Fecha prod.:</td>
                    <td>{{ $produccion->aprobfechahora ? \Carbon\Carbon::parse($produccion->aprobfechahora)->format('d/m/Y H:i') : '—' }}</td>
                </tr>
                <tr>
                    <td>OP / OT:</td>
                    <td>OP-{{ $op->id }} / OT-{{ $ot->id }}</td>
                </tr>
                <tr>
                    <td>Det. OT:</td>
                    <td>{{ $otdet->id }}</td>
                </tr>
                @if($notaventa_id)
                <tr>
                    <td>N° Venta:</td>
                    <td><strong>{{ $notaventa_id }}</strong></td>
                </tr>
                @endif
                <tr>
                    <td>ID Prod.:</td>
                    <td style="font-size:7pt; color:#555;">{{ $produccion->id }}</td>
                </tr>
            </table>
        </div>
        <div class="etiqueta-qr">
            <div id="qrcode"></div>
            <small>ID: {{ $produccion->id }}</small>
        </div>
    </div>
    <div class="etiqueta-footer">
        {{-- Plastiservi &mdash; Sistema ERP --}}
        Plastiservi
    </div>
</div>

<!-- QR Code library (CDN) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById("qrcode"), {
        text: "{{ $produccion->id }}",
        width: 75,
        height: 75,
        correctLevel: QRCode.CorrectLevel.M
    });
</script>
</body>
</html>
