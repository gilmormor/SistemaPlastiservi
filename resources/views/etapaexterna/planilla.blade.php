<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planilla {{ $datos['etapaprod_nombre'] ?? 'Area' }} — Lote {{ $datos['lote']->id }}</title>
    <style>
        /* Carta horizontal: el formato en papel que hoy se llena a mano.
           Los margenes y los alto de fila de abajo estan ajustados para que
           TODO entre en UNA hoja: con mas espacio, el pie se desbordaba a una
           segunda pagina en blanco. Al tocar cualquiera de esos valores hay
           que volver a comprobar que siga cabiendo en una sola. */
        /* margin 0 a proposito: con margen de pagina, el navegador imprime su
           propio encabezado (titulo y URL) y su pie (numero de pagina y fecha)
           dentro de ese margen. Sin margen no tiene donde dibujarlos y la hoja
           sale limpia. El margen real lo pone el padding de .hoja. */
        @page { size: letter landscape; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            background: #fff;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
        }
        /* El margen de impresion va aqui, no en @page (ver la nota de arriba) */
        .hoja { width: 100%; padding: 8mm; }

        /* ---------------------------------------------------- encabezado */
        .cab { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .cab td { border: 1px solid #000; padding: 3px 6px; vertical-align: middle; }
        .cab .empresa { width: 18%; text-align: center; font-weight: bold; font-size: 11pt; }
        .cab .titulo  { text-align: center; font-weight: bold; font-size: 13pt; letter-spacing: .5px; }
        .cab .codigo  { width: 24%; padding: 0; }
        .cab .codigo table { width: 100%; border-collapse: collapse; font-size: 7.5pt; }
        .cab .codigo td { border: 1px solid #000; padding: 1px 4px; }

        /* Franja de maquina y responsable. La maquina es la ASIGNADA para
           procesar (no la de origen del lote: esa va en la etiqueta de abajo). */
        .franja { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .franja td { padding: 4px 6px; font-size: 10pt; }
        .franja .et { font-weight: normal; }
        .franja .val {
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding: 0 14px 1px 14px;
        }

        /* ---------------------------------------------- tabla de programacion */
        .prog { width: 100%; border-collapse: collapse; }
        .prog th, .prog td { border: 1px solid #000; padding: 4px 5px; font-size: 9pt; }
        .prog th { text-align: center; font-weight: bold; font-size: 8.5pt; line-height: 1.15; }
        .prog td { height: 21px; }   /* alto de fila ajustado para caber en 1 hoja */
        .prog .c { text-align: center; }
        .prog .r { text-align: right; }

        /* ------------------------------------------------- etiqueta del lote */
        /* Misma informacion que la etiqueta que hoy se imprime suelta y se pega
           a mano en esta hoja. Va impresa aqui para que el operario reciba un
           solo papel y no pueda equivocarse de rollo. */
        .zona-etiqueta { margin-top: 5px; }   /* ajustado para caber en 1 hoja */
        .etiqueta {
            width: 10.4cm;
            border: 2px solid #000;
            padding: 3mm;
            font-family: "Arial Black", Arial, sans-serif;
            font-weight: bold;
            font-size: 7.5pt;
        }
        .etiqueta-titulo {
            text-align: center;
            font-size: 8pt;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        .etiqueta-body { display: table; width: 100%; table-layout: fixed; }
        .etiqueta-datos { display: table-cell; vertical-align: top; }
        .etiqueta-datos table { width: 100%; border-collapse: collapse; }
        .etiqueta-datos td { padding: 0 2px 1px 0; font-size: 7pt; line-height: 1.2; }
        .etiqueta-datos td:first-child { width: 32%; }
        .etiqueta-qr {
            display: table-cell;
            width: 2.6cm;
            text-align: center;
            vertical-align: top;
        }
        .etiqueta-qr small { display: block; font-size: 6.5pt; }

        /* ------------------------------------------------------------ firmas */
        .firmas { width: 100%; margin-top: 8px; border-collapse: collapse; }
        .firmas td {
            width: 50%;
            text-align: center;
            font-size: 8.5pt;
            padding-top: 15px;
        }
        .firmas .linea { border-top: 1px solid #000; padding-top: 3px; margin: 0 18%; }

        /* Pie sin margen superior: era lo que empujaba una segunda hoja. */
        .pie { margin-top: 2px; font-size: 7pt; color: #555; text-align: right; }

        @media print { .noprint { display: none; } }
        .noprint { text-align: center; margin: 10px 0; }
        .noprint button { padding: 6px 18px; font-size: 11pt; cursor: pointer; }
    </style>
</head>
<body>

{{-- Dentro del modal de la pantalla el boton Imprimir esta en el pie del modal,
     asi que aqui no se dibuja. Abierta sola (reimpresion por URL) si aparece. --}}
@if(!request('modal'))
<div class="noprint">
    <button onclick="window.print();">Imprimir</button>
</div>
@endif

<div class="hoja">

    {{-- ====================================================== ENCABEZADO --}}
    <table class="cab">
        <tr>
            <td class="empresa">{{ $empresaNombre ?: 'PLASTISERVI' }}</td>
            <td class="titulo">
                REGISTRO PROGRAMACION AREA {{ strtoupper($datos['etapaprod_nombre'] ?? '') }}
            </td>
            <td class="codigo">
                <table>
                    <tr><td>Codigo</td><td>DP REG.06</td></tr>
                    <tr><td>Fecha Creacion</td><td>Enero, 2018</td></tr>
                    <tr><td>Version</td><td>2</td></tr>
                    <tr><td>Pagina</td><td>1</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Maquina asignada y responsable. Ambos los definio el supervisor al
         generar la planilla; quedan guardados en etapaextplanilla. --}}
    <table class="franja">
        <tr>
            <td style="width:50%;">
                <span class="et">N° Equipo / Máquina:</span>
                <span class="val">{{ $maquinaNombre }}</span>
            </td>
            <td style="width:50%;">
                <span class="et">Nombre Responsable Equipo:</span>
                <span class="val">{{ $operarioNombre }}</span>
            </td>
        </tr>
    </table>

    {{-- ============================================ TABLA DE PROGRAMACION --}}
    <table class="prog">
        <thead>
            <tr>
                <th style="width:8%;">Fecha</th>
                <th style="width:8%;">Orden de<br>Trabajo</th>
                <th style="width:8%;">Orden de<br>producción</th>
                <th style="width:19%;">Identificación Cliente</th>
                <th style="width:19%;">Medida y Espesor</th>
                <th style="width:10%;">Material</th>
                <th style="width:8%;">N° Bolsas</th>
                <th style="width:9%;">Producción<br>Kg.</th>
                <th style="width:11%;">Detalle de Embalaje</th>
            </tr>
        </thead>
        <tbody>
            {{-- Fila 1: los datos del lote que se manda a procesar. --}}
            <tr>
                <td class="c">{{ $planilla->created_at->format('d/m/y') }}</td>
                <td class="c">{{ $datos['ot']->id }}</td>
                <td class="c">{{ $datos['op']->id }}</td>
                <td>{{ $datos['cliente_nombre'] ?: '—' }}</td>
                <td>{{ $datos['producto_glosa'] }}</td>
                <td>{{ $datos['materiaprima_nombre'] }}</td>
                <td class="r">
                    {{-- N Bolsas lo escribe el supervisor; si no lo puso, la celda
                         queda en blanco para anotarlo a mano. --}}
                    {{ $planilla->nrobolsas !== null ? number_format($planilla->nrobolsas, 0, ',', '.') : '' }}
                </td>
                <td class="r">{{ number_format($datos['lote']->kgprod, 2, ',', '.') }}</td>
                <td>{{ $formaembalar ?: '' }}</td>
            </tr>

            {{-- Filas en blanco: el operario anota a mano el avance del turno,
                 igual que en el formato de papel que usan hoy.

                 La cantidad la ajusta el script del final segun el espacio que
                 sobre, porque el alto de la fila de datos es variable (el
                 nombre del producto puede ocupar una o cuatro lineas) y un
                 numero fijo desborda a una segunda hoja. Estas quedan como
                 valor por defecto si el script no llegara a ejecutarse. --}}
            @for($i = 0; $i < 11; $i++)
            <tr class="relleno">
                <td></td><td></td><td></td><td></td><td></td>
                <td></td><td></td><td></td><td></td>
            </tr>
            @endfor
        </tbody>
    </table>

    @if($planilla->obs)
    <p style="margin-top:6px; font-size:9pt;"><strong>Observación:</strong> {{ $planilla->obs }}</p>
    @endif

    {{-- ================================================ ETIQUETA DEL LOTE --}}
    <div class="zona-etiqueta">
        <div style="font-size:8pt; margin-bottom:3px;">
            <strong>Lote de origen que debe procesar:</strong>
        </div>
        <div class="etiqueta">
            <div class="etiqueta-titulo">
                {{ $datos['etapaorigen_nombre'] }} &rarr; {{ $datos['etapaprod_nombre'] ?? 'SIGUIENTE ETAPA' }}
            </div>
            <div class="etiqueta-body">
                <div class="etiqueta-datos">
                    <table>
                        <tr>
                            <td>Producto:</td>
                            <td>id {{ $datos['otdet']->producto_id }} - {{ $datos['producto_glosa'] }}</td>
                        </tr>
                        @if($datos['cliente_nombre'])
                        <tr><td>Cliente:</td><td>{{ $datos['cliente_nombre'] }}</td></tr>
                        @endif
                        <tr>
                            <td>Cant / Kg:</td>
                            <td>{{ number_format($datos['lote']->cantprod, 0, ',', '.') }} /
                                {{ number_format($datos['lote']->kgprod, 2, ',', '.') }} kg</td>
                        </tr>
                        <tr>
                            <td>Fecha:</td>
                            <td>{{ $datos['lote']->aprobfechahora ? \Carbon\Carbon::parse($datos['lote']->aprobfechahora)->format('d/m/Y H:i') : '—' }}</td>
                        </tr>
                        <tr>
                            <td>OP/OT/Det:</td>
                            <td>OP-{{ $datos['op']->id }} / OT-{{ $datos['ot']->id }} / {{ $datos['otdet']->id }}</td>
                        </tr>
                        <tr><td>OpDet:</td><td>{{ $datos['opdet']->id }}</td></tr>
                        <tr><td>Operario:</td><td>{{ $datos['operarioorigen_nombre'] }}</td></tr>
                        <tr><td>Maquina:</td><td>{{ $datos['maquinaorigen_nombre'] }}</td></tr>
                    </table>
                </div>
                <div class="etiqueta-qr">
                    <div id="qrcode"></div>
                    <small>Lote: {{ $datos['lote']->id }}</small>
                    @if($empresaNombre)
                        <small style="font-weight:normal;">{{ $empresaNombre }}</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================== FIRMAS --}}
    <table class="firmas">
        <tr>
            <td><div class="linea">Nombre y Firma Supervisor</div></td>
            <td><div class="linea">Nombre y Firma Jefe {{ $datos['etapaprod_nombre'] ?? '' }}</div></td>
        </tr>
    </table>

    <div class="pie">
        Planilla N° {{ $planilla->id }} — generada el
        {{ $planilla->created_at->format('d/m/Y H:i') }}
    </div>

</div>

<!-- QR Code library (CDN) — mismo uso que las etiquetas del modulo -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    /* QR del LOTE DE ORIGEN, igual que la etiqueta de etapa que hoy se pega a
       mano: lo que el operario escanea aqui es el rollo que debe tomar. */
    new QRCode(document.getElementById("qrcode"), {
        text: "{{ $datos['lote']->id }}",
        width: 70,
        height: 70,
        correctLevel: QRCode.CorrectLevel.M
    });

    /* ----------------------------------------------------------------
       La planilla debe salir SIEMPRE en una sola hoja.

       El alto del contenido no es fijo: la celda del producto puede ocupar
       una linea o cuatro segun el largo del nombre, asi que un numero fijo de
       filas en blanco a veces empujaba el pie a una segunda hoja vacia.

       Aqui se ajusta la cantidad de filas en blanco al espacio que realmente
       queda: se quitan todas y se van agregando mientras el pie siga dentro
       de la hoja. Se corre despues de dibujar el QR, que ocupa altura.
       ---------------------------------------------------------------- */
    (function ajustarFilasEnBlanco() {
        var MM          = 96 / 25.4;            // px por milimetro a 96 dpi
        /* Carta horizontal: 215.9mm de alto. Como @page va sin margen (para que
           el navegador no imprima su encabezado), el documento arranca en el
           borde del papel y el margen lo pone el padding de .hoja: aqui solo hay
           que descontar el padding de ABAJO, porque el de arriba ya esta
           incluido en la posicion medida.
           El colchon de 10px (~2,6mm) cubre que el calculo se hace en pantalla
           y el render de impresion puede variar en decimas: sin el, la ultima
           fila podia empujar una segunda hoja. */
        var disponible  = (215.9 - 8) * MM - 10;
        var tbody       = document.querySelector('.prog tbody');
        var pie         = document.querySelector('.pie');
        if (!tbody || !pie) { return; }

        var relleno = tbody.querySelectorAll('tr.relleno');
        for (var i = 0; i < relleno.length; i++) {
            relleno[i].parentNode.removeChild(relleno[i]);
        }

        /* Se agregan de a una. El tope de 40 es solo una guarda para que un
           error de medicion no deje el bucle girando. */
        var guarda = 0;
        while (pie.getBoundingClientRect().bottom < disponible && guarda < 40) {
            var tr = document.createElement('tr');
            tr.className = 'relleno';
            tr.innerHTML = '<td></td><td></td><td></td><td></td><td></td>' +
                           '<td></td><td></td><td></td><td></td>';
            tbody.appendChild(tr);
            guarda++;
        }

        // La ultima que se agrego es la que se paso: se quita.
        if (pie.getBoundingClientRect().bottom > disponible) {
            var ultima = tbody.querySelector('tr.relleno:last-child');
            if (ultima) { ultima.parentNode.removeChild(ultima); }
        }
    })();
</script>
</body>
</html>
