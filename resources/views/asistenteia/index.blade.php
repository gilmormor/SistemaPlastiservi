@extends("theme.$theme.layout")

@section('titulo', 'Asistente IA')

@section('styles')
<style>
    #chat-container {
        height: 520px;
        overflow-y: auto;
        background: #f4f6f9;
        border: 1px solid #d2d6de;
        border-radius: 4px;
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .msg-user {
        align-self: flex-end;
        max-width: 75%;
    }
    .msg-bot {
        align-self: flex-start;
        max-width: 90%;
        width: 100%;
    }
    .msg-user .burbuja {
        background: #3c8dbc;
        color: #fff;
        border-radius: 18px 18px 4px 18px;
        padding: 10px 15px;
        display: inline-block;
        word-break: break-word;
    }
    .msg-bot .burbuja {
        background: #fff;
        border: 1px solid #d2d6de;
        border-radius: 18px 18px 18px 4px;
        padding: 10px 15px;
        display: inline-block;
        word-break: break-word;
    }
    .msg-bot .resultado {
        background: #fff;
        border: 1px solid #d2d6de;
        border-radius: 4px;
        padding: 12px;
        margin-top: 6px;
    }
    .msg-nombre {
        font-size: 11px;
        color: #888;
        margin-bottom: 3px;
    }
    .msg-user .msg-nombre { text-align: right; }
    #input-pregunta {
        resize: none;
        border-radius: 4px;
    }
    .numero-grande {
        font-size: 42px;
        font-weight: 700;
        color: #3c8dbc;
        text-align: center;
        padding: 20px 0;
    }
    .chart-wrapper {
        position: relative;
        height: 280px;
        width: 100%;
    }
    .typing-indicator span {
        display: inline-block;
        width: 8px;
        height: 8px;
        margin: 0 2px;
        background: #aaa;
        border-radius: 50%;
        animation: bounce 1.2s infinite;
    }
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes bounce {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }
    .tabla-resultado { font-size: 12px; }
    .tabla-resultado th { background: #3c8dbc; color: #fff; }
    .error-sql { color: #a94442; font-size: 12px; margin-top: 5px; }
</style>
@endsection

@section('contenido')
<section class="content-header">
    <h1><i class="fa fa-robot"></i> Asistente IA <small>Consulta datos del sistema en lenguaje natural</small></h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-comments"></i> Chat con los datos</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-xs btn-default" id="btn-limpiar" title="Nueva conversación">
                            <i class="fa fa-trash"></i> Nueva conversación
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div id="chat-container"></div>

                    <div class="row" style="margin-top:12px;">
                        <div class="col-xs-10">
                            <textarea id="input-pregunta" class="form-control" rows="2"
                                placeholder="Ej: ¿Cuánto vendimos este mes? ¿Qué vendedor vende más? ¿Cuáles son las NV pendientes de despacho?"></textarea>
                        </div>
                        <div class="col-xs-2">
                            <button id="btn-enviar" class="btn btn-primary btn-block" style="height:58px;">
                                <i class="fa fa-paper-plane"></i><br>Enviar
                            </button>
                        </div>
                    </div>

                    <div style="margin-top:8px;">
                        <small class="text-muted">
                            <i class="fa fa-lightbulb-o"></i> Sugerencias:
                            <a href="#" class="sugerencia">¿Cuánto vendimos este mes?</a> &bull;
                            <a href="#" class="sugerencia">¿Qué vendedor vende más?</a> &bull;
                            <a href="#" class="sugerencia">Ventas por región este año</a> &bull;
                            <a href="#" class="sugerencia">Cotizaciones sin convertir en NV</a> &bull;
                            <a href="#" class="sugerencia">Facturas del mes</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
var historial = [];
var chartInstancia = null;

$(document).ready(function () {

    agregarMensajeBot(
        'Hola, soy el asistente IA de Plastiservi. Puedes preguntarme sobre ventas, cotizaciones, despachos, facturas, clientes, vendedores y más.',
        null, null
    );

    $('#btn-enviar').on('click', enviar);

    $('#input-pregunta').on('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            enviar();
        }
    });

    $(document).on('click', '.sugerencia', function (e) {
        e.preventDefault();
        $('#input-pregunta').val($(this).text());
        enviar();
    });

    $('#btn-limpiar').on('click', function () {
        historial = [];
        $('#chat-container').empty();
        agregarMensajeBot('Conversación reiniciada. ¿En qué puedo ayudarte?', null, null);
    });

});

function enviar() {
    var pregunta = $('#input-pregunta').val().trim();
    if (!pregunta) return;

    agregarMensajeUsuario(pregunta);
    $('#input-pregunta').val('');
    $('#btn-enviar').prop('disabled', true);

    var idTyping = agregarTyping();

    $.ajax({
        url: '{{ route("asistenteia.consultar") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            pregunta: pregunta,
            historial: JSON.stringify(historial)
        },
        success: function (res) {
            $('#' + idTyping).remove();
            $('#btn-enviar').prop('disabled', false);

            // Agregar al historial (máx últimas 6 interacciones)
            historial.push({ role: 'user', content: pregunta });
            historial.push({ role: 'assistant', content: res.assistant_msg || res.respuesta || '' });
            if (historial.length > 12) historial = historial.slice(-12);

            agregarMensajeBot(res.respuesta, res, res.datos);
            scrollChat();
        },
        error: function (xhr) {
            $('#' + idTyping).remove();
            $('#btn-enviar').prop('disabled', false);
            var msg = 'Error al consultar.';
            if (xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
            agregarMensajeBot(msg, null, null);
        }
    });
}

function agregarMensajeUsuario(texto) {
    var html = '<div class="msg-user">' +
        '<div class="msg-nombre">Tú</div>' +
        '<div class="burbuja">' + escHtml(texto) + '</div>' +
        '</div>';
    $('#chat-container').append(html);
    scrollChat();
}

function agregarTyping() {
    var id = 'typing-' + Date.now();
    var html = '<div class="msg-bot" id="' + id + '">' +
        '<div class="msg-nombre">Asistente IA</div>' +
        '<div class="burbuja"><div class="typing-indicator"><span></span><span></span><span></span></div></div>' +
        '</div>';
    $('#chat-container').append(html);
    scrollChat();
    return id;
}

function agregarMensajeBot(texto, res, datos) {
    var uid = 'res-' + Date.now();
    var html = '<div class="msg-bot">' +
        '<div class="msg-nombre">Asistente IA</div>' +
        '<div class="burbuja">' + escHtml(texto) + '</div>';

    if (res && res.error_sql) {
        html += '<div class="resultado"><span class="error-sql"><i class="fa fa-warning"></i> ' + escHtml(res.error_sql) + '</span></div>';
    }

    if (datos && datos.length > 0) {
        var tipo = res ? res.tipo : 'tabla';

        if (tipo === 'numero') {
            var primerVal = Object.values(datos[0])[0];
            html += '<div class="resultado"><div class="numero-grande">' + formatNum(primerVal) + '</div>' +
                '<div class="text-center text-muted" style="font-size:13px;">' + escHtml(res.titulo_grafico || '') + '</div></div>';

        } else if (tipo === 'barra' || tipo === 'linea' || tipo === 'torta') {
            html += '<div class="resultado">' +
                '<div class="text-center" style="margin-bottom:6px;font-weight:600;">' + escHtml(res.titulo_grafico || '') + '</div>' +
                '<div class="chart-wrapper"><canvas id="' + uid + '"></canvas></div>' +
                '</div>';
            // Tabla debajo del gráfico
            html += renderTabla(datos);

        } else {
            // tipo tabla
            if (res && res.titulo_grafico) {
                html += '<div class="resultado"><strong>' + escHtml(res.titulo_grafico) + '</strong>';
            } else {
                html += '<div class="resultado">';
            }
            html += renderTabla(datos) + '</div>';
        }
    } else if (datos && datos.length === 0) {
        html += '<div class="resultado"><em class="text-muted">Sin resultados para esta consulta.</em></div>';
    }

    html += '</div>';
    $('#chat-container').append(html);

    // Renderizar gráfico si aplica
    if (datos && datos.length > 0 && res && (res.tipo === 'barra' || res.tipo === 'linea' || res.tipo === 'torta')) {
        renderGrafico(uid, res, datos);
    }

    scrollChat();
}

function renderTabla(datos) {
    if (!datos || datos.length === 0) return '';
    var cols = Object.keys(datos[0]);
    var html = '<div style="overflow-x:auto;margin-top:8px;">' +
        '<table class="table table-condensed table-bordered tabla-resultado"><thead><tr>';
    cols.forEach(function (c) { html += '<th>' + escHtml(c) + '</th>'; });
    html += '</tr></thead><tbody>';
    datos.forEach(function (row) {
        html += '<tr>';
        cols.forEach(function (c) {
            html += '<td>' + formatCelda(row[c]) + '</td>';
        });
        html += '</tr>';
    });
    html += '</tbody></table></div>';
    return html;
}

function renderGrafico(canvasId, res, datos) {
    if (!datos || datos.length === 0) return;

    var cols     = Object.keys(datos[0]);
    var campoX   = res.eje_x && cols.includes(res.eje_x) ? res.eje_x : cols[0];
    var campoY   = res.eje_y && cols.includes(res.eje_y) ? res.eje_y : cols[1] || cols[0];

    var labels = datos.map(function (r) { return String(r[campoX] || ''); });
    var values = datos.map(function (r) { return parseFloat(r[campoY]) || 0; });

    var colores = generarColores(datos.length);

    var ctx = document.getElementById(canvasId);
    if (!ctx) return;

    var config = {};

    if (res.tipo === 'torta') {
        config = {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{ data: values, backgroundColor: colores }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                legend: { position: 'right' }
            }
        };
    } else if (res.tipo === 'linea') {
        config = {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: campoY,
                    data: values,
                    borderColor: '#3c8dbc',
                    backgroundColor: 'rgba(60,141,188,0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: { yAxes: [{ ticks: { beginAtZero: true } }] }
            }
        };
    } else {
        // barra
        config = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: campoY,
                    data: values,
                    backgroundColor: colores
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: { yAxes: [{ ticks: { beginAtZero: true } }] }
            }
        };
    }

    new Chart(ctx, config);
}

function generarColores(n) {
    var base = ['#3c8dbc','#00a65a','#f39c12','#dd4b39','#605ca8','#00c0ef','#d2d6de','#001f3f','#39cccc','#ff851b'];
    var colores = [];
    for (var i = 0; i < n; i++) {
        colores.push(base[i % base.length]);
    }
    return colores;
}

function scrollChat() {
    var c = document.getElementById('chat-container');
    c.scrollTop = c.scrollHeight;
}

function escHtml(s) {
    if (s == null) return '';
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function formatNum(v) {
    var n = parseFloat(v);
    if (isNaN(n)) return escHtml(v);
    return n.toLocaleString('es-CL');
}

function formatCelda(v) {
    if (v == null) return '<span class="text-muted">-</span>';
    var n = parseFloat(v);
    if (!isNaN(n) && String(v).trim() !== '') {
        return n.toLocaleString('es-CL');
    }
    return escHtml(String(v));
}
</script>
@endsection
