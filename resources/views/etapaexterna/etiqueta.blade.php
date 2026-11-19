<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Etiquetas paquete — Tirada {{ $etiqueta->id }} / Lote {{ $datos['lote']->id }}</title>
    <style>
        /* Misma medida fisica que las demas etiquetas del modulo: 10.4 x 5.08 cm
           (impresora termica). */
        @page { size: 10.4cm 5.08cm; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            background: #fff;
            color: #000;
            /* Trazo grueso: Arial regular sale borroso en impresora termica. */
            font-family: "Arial Black", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-weight: bold;
            -webkit-font-smoothing: none;
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
        .etiqueta:last-child { page-break-after: auto; }

        .etiqueta-titulo {
            text-align: center;
            font-size: 8pt;
            border-bottom: 1px solid #000;
            padding-bottom: 1px;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: .3px;
            line-height: 1.1;
        }
        .etiqueta-body { display: table; width: 100%; table-layout: fixed; }
        .etiqueta-datos { display: table-cell; vertical-align: top; }
        .etiqueta-datos table { width: 100%; border-collapse: collapse; }
        .etiqueta-datos td { padding: 0 2px 1px 0; font-size: 7pt; line-height: 1.18; }
        .etiqueta-datos td:first-child { width: 34%; }

        /* Kilos y unidades por paquete: es lo que el operario verifica de un
           vistazo al pegar la etiqueta, va destacado. */
        .destacado td { font-size: 8.5pt; padding-top: 1px; }

        .etiqueta-qr {
            display: table-cell;
            width: 2.4cm;
            text-align: center;
            vertical-align: top;
        }
        .etiqueta-qr small { display: block; font-size: 6.5pt; }

        @media print { .noprint { display: none; } }
        .noprint { text-align: center; padding: 8px; font-family: Arial, sans-serif; }
        .noprint button { padding: 6px 18px; font-size: 11pt; cursor: pointer; }
    </style>
</head>
<body>

{{-- Ver la nota equivalente en planilla.blade.php --}}
@if(!request('modal'))
<div class="noprint">
    <button onclick="window.print();">Imprimir {{ $etiqueta->cantidad }} etiqueta(s)</button>
</div>
@endif

{{-- La tirada se imprime como N etiquetas identicas: una por paquete que el
     operario va a armar. El registro en etapaextetiqueta es uno solo. --}}
@for($i = 1; $i <= $etiqueta->cantidad; $i++)
<div class="etiqueta">
    <div class="etiqueta-titulo">
        {{ $datos['etapaprod_nombre'] ?? 'PROCESO' }} — {{ $i }}/{{ $etiqueta->cantidad }}
    </div>
    <div class="etiqueta-body">
        <div class="etiqueta-datos">
            <table>
                @if($datos['cliente_nombre'])
                <tr><td>Cliente:</td><td>{{ $datos['cliente_nombre'] }}</td></tr>
                @endif
                <tr><td>Producto:</td><td>{{ $datos['producto_glosa'] }}</td></tr>
                <tr><td>OP / OT:</td><td>{{ $datos['op']->id }} / {{ $datos['ot']->id }}</td></tr>
                <tr><td>Fecha Prod.:</td>
                    <td>{{ \Carbon\Carbon::parse($etiqueta->fecha)->format('d/m/Y') }}
                        @if($etiqueta->turno) — Turno {{ $etiqueta->turno }} @endif
                    </td></tr>
                <tr><td>Maquina:</td><td>{{ $maquinaNombre }}</td></tr>
                <tr><td>Operario:</td><td>{{ $operarioNombre }}</td></tr>
                <tr><td>Materia Prima:</td><td>{{ $datos['materiaprima_nombre'] }}</td></tr>
                {{-- Kilos y unidades son POR PAQUETE, no el total del lote. --}}
                <tr class="destacado">
                    <td>Kilos / Unid.:</td>
                    <td>{{ $etiqueta->kilos !== null ? number_format($etiqueta->kilos, 2, ',', '.') : '—' }} kg /
                        {{ $etiqueta->unidades !== null ? number_format($etiqueta->unidades, 0, ',', '.') : '—' }}</td>
                </tr>
                @if($etiqueta->obs)
                <tr><td>Obs.:</td><td style="font-size:6.5pt;">{{ $etiqueta->obs }}</td></tr>
                @endif
            </table>
        </div>
        <div class="etiqueta-qr">
            {{-- Un div por etiqueta: qrcodejs necesita un contenedor distinto
                 para cada codigo que dibuja. --}}
            <div class="qrcode" data-texto="{{ $etiqueta->id }}"></div>
            <small>Tirada: {{ $etiqueta->id }}</small>
            <small>Lote: {{ $datos['lote']->id }}</small>
            @if($empresaNombre)
                <small style="font-weight:normal;">{{ $empresaNombre }}</small>
            @endif
        </div>
    </div>
</div>
@endfor

<!-- QR Code library (CDN) — mismo uso que las demas etiquetas del modulo -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    /* El QR lleva el id de la TIRADA (etapaextetiqueta), que es el registro que
       guarda maquina, operario, turno, kilos y unidades de estos paquetes. El
       lote de origen va impreso debajo en texto.

       Se dibuja UNA sola vez y se clona al resto: todas las etiquetas de la
       tirada llevan el mismo id, asi que el codigo es identico. Generarlos uno
       por uno tardaba mas de 3 segundos en una tirada de 300.
       OJO: si algun dia el QR pasara a ser distinto por etiqueta (por ejemplo
       con el numero de paquete), hay que volver a generarlos de a uno. */
    var aux_qrs = document.querySelectorAll('.qrcode');
    if (aux_qrs.length > 0) {
        new QRCode(aux_qrs[0], {
            text: aux_qrs[0].getAttribute('data-texto'),
            width: 62,
            height: 62,
            correctLevel: QRCode.CorrectLevel.M
        });
        /* qrcodejs dibuja el codigo en un <canvas>, y lo pintado en un canvas
           NO viaja al copiar el HTML: clonando el innerHTML la primera etiqueta
           salia bien y todas las demas con el recuadro vacio. Se convierte el
           canvas a una imagen, que si se copia. Se aplica tambien a la primera
           para que las etiquetas sean identicas entre si. */
        var aux_canvas = aux_qrs[0].querySelector('canvas');
        var aux_html;
        if (aux_canvas) {
            aux_html = '<img src="' + aux_canvas.toDataURL('image/png') + '" ' +
                       'width="62" height="62" alt="QR">';
        } else {
            /* Sin canvas, qrcodejs genera una tabla o un <img>, que si se
               clonan correctamente. */
            aux_html = aux_qrs[0].innerHTML;
        }
        for (var i = 0; i < aux_qrs.length; i++) {
            aux_qrs[i].innerHTML = aux_html;
        }
    }

    /* ----------------------------------------------------------------
       Que el contenido no se salga de la etiqueta.

       La etiqueta mide 5,08 cm de alto fijo y recorta lo que sobre
       (overflow:hidden), asi que un cliente y un producto largos a la vez
       hacian desaparecer en silencio la observacion o la maquina: el operario
       recibia el paquete con una etiqueta incompleta sin que nadie lo notara.

       Aqui se achica la letra lo justo para que entre todo. Como todas las
       etiquetas de la tirada son identicas, se mide la primera y el tamaño
       elegido se aplica a todas con una sola regla de estilo.
       ---------------------------------------------------------------- */
    (function ajustarTextoEtiqueta() {
        var et = document.querySelector('.etiqueta');
        if (!et) { return; }
        var cuerpo = et.querySelector('.etiqueta-body');
        if (!cuerpo) { return; }

        var estilo = document.createElement('style');
        document.head.appendChild(estilo);

        // Tamaños normales (los del CSS de arriba) y las reducciones a probar
        var aux_base     = 7;     // pt de .etiqueta-datos td
        var aux_destac   = 8.5;   // pt de .destacado td
        var aux_escalas  = [1, 0.93, 0.86, 0.80, 0.74, 0.68];

        function cabe() {
            var pad = parseFloat(window.getComputedStyle(et).paddingBottom) || 0;
            var limite = et.getBoundingClientRect().top + et.clientHeight - pad;
            return cuerpo.getBoundingClientRect().bottom <= limite;
        }

        for (var i = 0; i < aux_escalas.length; i++) {
            var e = aux_escalas[i];
            estilo.innerHTML =
                '.etiqueta-datos td { font-size: ' + (aux_base * e).toFixed(2) + 'pt; }' +
                '.destacado td { font-size: ' + (aux_destac * e).toFixed(2) + 'pt; }';
            if (cabe()) { break; }
        }
    })();
</script>
</body>
</html>
