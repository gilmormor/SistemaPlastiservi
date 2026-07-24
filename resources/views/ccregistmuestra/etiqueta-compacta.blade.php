<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta Muestra CC #{{ $muestra->id }}</title>
    <style>
        /* Medida física: 10.4 cm x 5.08 cm — igual a etiqueta de etapa */
        @page {
            size: 10.4cm 5.08cm;
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            background: #fff;
            font-family: "Arial Black", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-weight: bold;
            -webkit-font-smoothing: none;
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
            font-size: 7pt;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 1px;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.1;
        }
        /* Número de muestra prominente */
        .etiqueta-numero {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            background: #1a5fb4;
            color: #fff;
            padding: 1px 3px;
            margin-bottom: 2px;
            line-height: 1.2;
            letter-spacing: 0.5px;
        }
        /* Banda de resultado (semáforo) */
        .etiqueta-result {
            text-align: center;
            font-size: 9pt;
            font-weight: bold;
            padding: 1px 3px;
            margin-bottom: 3px;
            line-height: 1.2;
            letter-spacing: 0.3px;
        }
        .result-1  { background: #00a65a; color: #fff; }
        .result-2  { background: #f39c12; color: #fff; }
        .result-3  { background: #dd4b39; color: #fff; }
        .result-5  { background: #7f8c8d; color: #fff; }

        .etiqueta-body {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        .etiqueta-datos {
            display: table-cell;
            vertical-align: top;
            font-size: 7pt;
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
        .etiqueta-datos td:first-child { white-space: nowrap; width: 36%; }
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
            background: #1a5fb4;
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

@php
    $resultClass = [1=>'result-1', 2=>'result-2', 3=>'result-3', 5=>'result-5'];
    $resultText  = [1=>'APROBADO', 2=>'APROBADO C/OBS', 3=>'RECHAZADO', 5=>'SIN PARÁMETROS'];
    $rc = $resultClass[$muestra->status] ?? 'result-5';
    $rt = $resultText[$muestra->status]  ?? '—';
@endphp

<div class="etiqueta">
    <div class="etiqueta-titulo">Muestra CC — Control de Calidad</div>
    <div class="etiqueta-numero">MUESTRA #{{ str_pad($muestra->id, 8, '0', STR_PAD_LEFT) }}</div>
    <div class="etiqueta-result {{ $rc }}">{{ $rt }}</div>
    <div class="etiqueta-body">
        <div class="etiqueta-datos">
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
                    <td>Fecha:</td>
                    <td>{{ \Carbon\Carbon::parse($muestra->fechahora)->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td>CC:</td>
                    <td>{{ $usuarioCC }}</td>
                </tr>
                <tr>
                    <td>OP/OT:</td>
                    <td>{{ $op_id }} / {{ $ot_id }}</td>
                </tr>
            </table>
        </div>
        <div class="etiqueta-qr">
            <div id="qrcode"></div>
            <small>CC #{{ $muestra->id }}</small>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById("qrcode"), {
        text: "{{ url('ccregistmuestra/' . $muestra->id . '/ver') }}",
        width: 68,
        height: 68,
        correctLevel: QRCode.CorrectLevel.M
    });
</script>
</body>
</html>
