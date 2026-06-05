/**
 * seguimiento.js — Pantalla de seguimiento de Órdenes de Producción
 * Ruta: /op/seguimiento
 */

var tabla;

$(document).ready(function () {
    // Inicializar datepickers
    $('.datepicker').datepicker({
        language: 'es',
        autoclose: true,
        clearBtn: true,
        todayHighlight: true
    }).datepicker('setDate', new Date());   // hoy por defecto

    $('.numerico-entero').numeric({ decimal: false, negative: false });

    // Construir tabla vacía al cargar
    construirTabla('');
});

$('#btnconsultar').click(function () {
    var params = datosFiltros();
    if ($.fn.DataTable.isDataTable('#tabla-seguimiento')) {
        $('#tabla-seguimiento').DataTable().destroy();
        $('#tabla-seguimiento').empty();
    }
    construirTabla('/op/seguimientopage' + params);
});

function datosFiltros() {
    return '?fechad='      + encodeURIComponent($('#fechad').val()) +
           '&fechah='      + encodeURIComponent($('#fechah').val()) +
           '&op_id='       + ($('#op_id').val() || '') +
           '&ot_id='       + ($('#ot_id').val() || '') +
           '&nv_id='       + ($('#nv_id').val() || '') +
           '&producto_id=' + ($('#producto_id').val() || '');
}

function construirTabla(url) {
    // Encabezado
    var thead = '<thead><tr>' +
        '<th></th>' +
        '<th title="OP">OP</th>' +
        '<th title="Fecha">Fecha</th>' +
        '<th title="OT">OT</th>' +
        '<th title="NV">NV</th>' +
        '<th title="Producto">Producto</th>' +
        '<th title="Cliente">Cliente</th>' +
        '<th title="Kg programados" style="text-align:right;">Kg Prog</th>' +
        '<th title="Progreso etapas" style="text-align:center;">Etapas</th>' +
        '<th title="Estado pendientes" style="text-align:center;">Pendientes</th>' +
        '</tr></thead><tfoot></tfoot>';
    $('#tabla-seguimiento').html(thead);

    tabla = $('#tabla-seguimiento').DataTable({
        paging      : true,
        scrollX     : true,
        lengthChange: true,
        searching   : true,
        ordering    : true,
        info        : true,
        autoWidth   : false,
        processing  : true,
        serverSide  : url !== '',
        ajax        : url || null,
        order       : [[1, 'desc']],
        language    : { url: 'https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json' },
        columns: [
            {   // child row toggle
                className    : 'dt-control',
                orderable    : false,
                data         : null,
                defaultContent: '<i class="fa fa-plus-circle text-aqua" title="Ver etapas"></i>'
            },
            { data: 'op_id',           width: '50px' },
            { data: 'op_fecha',        width: '90px' },
            { data: 'ot_id',           width: '50px' },
            { data: 'notaventa_id',    width: '60px' },
            { data: 'producto_nombre', width: '180px' },
            { data: 'razonsocial',     width: '160px' },
            { data: 'op_kgprod',       width: '80px' },
            { data: 'num_etapas',      width: '80px', orderable: false },
            { data: 'pend_no_enviados',width: '100px', orderable: false }
        ],
        createdRow: function (row, data) {
            $(row).attr('id', 'fila-op-' + data.op_id);

            // Col OP: enlace al listaropdet de esa OP
            $('td', row).eq(1).html(
                '<strong><a href="/opdetregprodtemp/listaropdet" style="color:#3c8dbc;" title="OP #' + data.op_id + '">' +
                data.op_id + '</a></strong>'
            );

            // Col fecha
            var f = new Date(data.op_fecha);
            $('td', row).eq(2).html(fechaddmmaaaa(f));

            // Col Kg programados
            $('td', row).eq(7).css('text-align', 'right').html(MASKLA(data.op_kgprod, 2));

            // Col Etapas (badge progreso)
            // etapas_completadas = saldokg<=0 con producción aprobada
            // etapas_parciales   = saldokg>0 con algo de producción aprobada
            var numEtapas   = parseInt(data.num_etapas)        || 0;
            var completadas = parseInt(data.etapas_completadas) || 0;
            var parciales   = parseInt(data.etapas_parciales)   || 0;
            var pct = numEtapas > 0
                ? Math.round((completadas / numEtapas) * 10000) / 100  // 2 decimales
                : 0;
            var color = completadas === numEtapas && numEtapas > 0
                ? '#00a65a'                          // todo completo
                : (completadas > 0 || parciales > 0 ? '#f39c12' : '#aaa'); // parcial o sin iniciar
            var etiquetaEtapas = completadas + '/' + numEtapas;
            if (parciales > 0) {
                etiquetaEtapas += ' <small style="color:#f39c12;">+' + parciales + ' parc.</small>';
            }
            $('td', row).eq(8).css('text-align', 'center').html(
                '<span style="color:' + color + '; font-weight:bold;">' + etiquetaEtapas + '</span>' +
                ' <small>(' + pct.toFixed(2).replace('.', ',') + '%)</small>'
            );

            // Col Pendientes
            var badges = '';
            if (data.pend_no_enviados > 0)
                badges += '<span style="color:#f39c12;" title="No enviados a aprobación"><i class="fa fa-circle"></i> ' + data.pend_no_enviados + '</span> ';
            if (data.pend_esperando_sup > 0)
                badges += '<span style="color:#3c8dbc;" title="Esperando supervisor"><i class="fa fa-circle"></i> ' + data.pend_esperando_sup + '</span> ';
            if (data.pend_rechazados > 0)
                badges += '<span style="color:#dd4b39;" title="Rechazados"><i class="fa fa-circle"></i> ' + data.pend_rechazados + '</span>';
            if (!badges) badges = '<span style="color:#aaa;">—</span>';
            $('td', row).eq(9).css('text-align', 'center').html(badges);
        }
    });

    // Click en la celda dt-control → child row con detalle de etapas
    $('#tabla-seguimiento').on('click', 'td.dt-control', function (e) {
        var tr  = e.target.closest('tr');
        var row = tabla.row(tr);
        if (row.child.isShown()) {
            row.child.hide();
            $(this).html('<i class="fa fa-plus-circle text-aqua" title="Ver etapas"></i>');
        } else {
            $(this).html('<i class="fa fa-minus-circle text-yellow" title="Ocultar etapas"></i>');
            row.child('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Cargando etapas...</div>').show();
            cargarEtapasDetalle(row.data().op_id, row);
        }
    });
}

/**
 * Carga el detalle de etapas de una OP y lo muestra en el child row.
 */
function cargarEtapasDetalle(op_id, row) {
    $.ajax({
        url : '/op/' + op_id + '/etapas-detalle',
        type: 'GET',
        success: function (etapas) {
            row.child(renderEtapas(op_id, etapas)).show();
        },
        error: function () {
            row.child('<p class="text-danger">Error al cargar las etapas.</p>').show();
        }
    });
}

/**
 * Formatea una fecha+hora desde string "YYYY-MM-DD HH:MM:SS" a "DD/MM/YYYY HH:MM".
 * Evita problemas de zona horaria que tiene new Date() con strings sin 'Z'.
 */
function fechaHoraStr(val) {
    if (!val) return '—';
    var s = val.toString().replace('T', ' ');
    var partes = s.split(' ');
    if (partes.length < 2) return s;
    var fecha = partes[0].split('-');
    var hora  = partes[1].substring(0, 5); // HH:MM
    if (fecha.length === 3) {
        return fecha[2] + '/' + fecha[1] + '/' + fecha[0] + ' ' + hora;
    }
    return s;
}

/**
 * Etiqueta de estado para aprobstatus de opdetregprodtemp.
 */
function labelEstadoTemp(s) {
    s = parseInt(s);
    if (s === 1)  return '<span style="color:#3c8dbc;"><i class="fa fa-clock-o"></i> Esperando sup.</span>';
    if (s === 3)  return '<span style="color:#dd4b39;"><i class="fa fa-times-circle"></i> Rechazado</span>';
    return '<span style="color:#f39c12;"><i class="fa fa-pencil"></i> Sin enviar</span>';
}

/**
 * Genera el HTML del detalle de etapas para el child row.
 * Incluye opdet_id, resumen por etapa y sub-tabla de registros individuales.
 */
function renderEtapas(op_id, etapas) {
    if (!etapas || etapas.length === 0) {
        return '<p class="text-muted" style="padding:10px;">Sin etapas registradas para esta OP.</p>';
    }

    var html = '<div style="margin-left:30px; margin-bottom:10px;">';

    etapas.forEach(function (e) {
        // ── Determinar color y estado general de la etapa ──────────────
        var estado = '', colorHeader = '#f4f4f4';
        var kgAprobado = parseFloat(e.kgprod_aprobado) || 0;
        var opKgprod   = parseFloat(e.op_kgprod)       || 0;
        // Completa: opdet.kgprod acumulado alcanzó el total de kg de la OP
        var esCompleta = opKgprod > 0 && kgAprobado >= opKgprod;
        var esParcial  = kgAprobado > 0 && !esCompleta;

        if (esCompleta) {
            estado = '<span style="color:#00a65a;"><i class="fa fa-check-circle"></i> Completa</span>';
            colorHeader = '#eaffea';
        } else if (parseInt(e.temp_esperando_sup) > 0) {
            estado = '<span style="color:#3c8dbc;"><i class="fa fa-clock-o"></i> Esperando aprobación</span>';
            colorHeader = '#eef4ff';
        } else if (parseInt(e.temp_no_enviados) > 0) {
            estado = '<span style="color:#f39c12;"><i class="fa fa-pencil"></i> En proceso</span>';
            colorHeader = '#fff8ee';
        } else if (parseInt(e.temp_rechazados) > 0) {
            estado = '<span style="color:#dd4b39;"><i class="fa fa-times-circle"></i> Rechazado</span>';
            colorHeader = '#fff0f0';
        } else if (esParcial) {
            estado = '<span style="color:#f39c12;"><i class="fa fa-spinner"></i> Parcial</span>';
            colorHeader = '#fffbee';
        } else {
            estado = '<span style="color:#aaa;"><i class="fa fa-circle-o"></i> Sin iniciar</span>';
        }

        // ── Encabezado de la etapa ──────────────────────────────────────
        html += '<table class="table table-condensed table-bordered" style="font-size:12px; width:95%; margin-bottom:4px;">' +
            '<thead><tr style="background:' + colorHeader + ';">' +
            '<th style="width:60px;">OpDet</th>' +
            '<th style="width:40px;">Ord</th>' +
            '<th>Etapa</th>' +
            '<th>Máquina</th>' +
            '<th style="text-align:right; width:80px;">Kg Recib.</th>' +
            '<th style="text-align:right; width:80px;">Kg Aprobado</th>' +
            '<th style="text-align:right; width:80px;">Saldo Kg</th>' +
            '<th style="text-align:center; width:180px;">Estado</th>' +
            '</tr></thead><tbody>' +
            '<tr style="background:' + colorHeader + ';">' +
            '<td style="font-weight:bold;">#' + e.opdet_id + '</td>' +
            '<td style="text-align:center;">' + e.etapa_orden + '</td>' +
            '<td><strong>' + e.etapa_nombre + '</strong></td>' +
            '<td>' + e.maquina_nombre + '</td>' +
            '<td style="text-align:right;">' + MASKLA(e.opdet_kgrec, 2) + '</td>' +
            '<td style="text-align:right; color:#00a65a;">' + MASKLA(e.kgprod_aprobado, 2) + '</td>' +
            '<td style="text-align:right; color:#f39c12;">' + MASKLA(e.opdet_saldokg, 2) + '</td>' +
            '<td style="text-align:center;">' + estado + '</td>' +
            '</tr></tbody></table>';

        // ── Sub-tabla de registros individuales ────────────────────────
        var tieneRegistros = (e.registros_temp && e.registros_temp.length > 0) ||
                             (e.registros_aprobados && e.registros_aprobados.length > 0);

        if (tieneRegistros) {
            html += '<table class="table table-condensed" style="font-size:11px; width:95%; margin-left:20px; margin-bottom:12px;">' +
                '<thead><tr style="background:#e8e8e8;">' +
                '<th>ID</th><th>Operario</th><th>Usuario</th>' +
                '<th style="text-align:right;">Kg Prod</th>' +
                '<th style="text-align:right;">Kg Scrap</th>' +
                '<th style="text-align:right;">Cant Prod</th>' +
                '<th>Fecha</th><th style="text-align:center;">Estado</th>' +
                '<th>Obs rechazo</th>' +
                '</tr></thead><tbody>';

            // Registros pendientes (opdetregprodtemp con aprobstatus 0/1/3)
            if (e.registros_temp && e.registros_temp.length > 0) {
                e.registros_temp.forEach(function (r) {
                    html += '<tr>' +
                        '<td><small class="text-muted">tmp</small> ' + r.id + '</td>' +
                        '<td>' + r.operario_nombre + '</td>' +
                        '<td>' + r.usuario_nombre  + '</td>' +
                        '<td style="text-align:right;">' + MASKLA(r.kgprod, 2)  + '</td>' +
                        '<td style="text-align:right;">' + MASKLA(r.kgscrap, 2) + '</td>' +
                        '<td style="text-align:right;">' + MASKLA(r.cantprod, 0) + '</td>' +
                        '<td style="white-space:nowrap;">' + fechaHoraStr(r.created_at) + '</td>' +
                        '<td style="text-align:center;">' + labelEstadoTemp(r.aprobstatus) + '</td>' +
                        '<td style="font-size:10px; color:#dd4b39;">' + (r.aprobobs || '') + '</td>' +
                        '</tr>';
                });
            }

            // Registros aprobados (opdetregprod)
            if (e.registros_aprobados && e.registros_aprobados.length > 0) {
                e.registros_aprobados.forEach(function (r) {
                    // El id es clickable → abre modal con etiqueta de etapa
                    var idCell = '<a href="javascript:void(0)" ' +
                                 'onclick="verEtiquetaEtapa(' + r.id + ')" ' +
                                 'title="Clic para ver/imprimir etiqueta" ' +
                                 'style="font-weight:bold; color:#3c8dbc;">' +
                                 '<i class="fa fa-tag"></i> apr ' + r.id + '</a>';
                    html += '<tr style="background:#f0fff0;">' +
                        '<td>' + idCell + '</td>' +
                        '<td>' + r.operario_nombre + '</td>' +
                        '<td>' + r.usuario_nombre  + '</td>' +
                        '<td style="text-align:right; color:#00a65a;">' + MASKLA(r.kgprod, 2)  + '</td>' +
                        '<td style="text-align:right;">' + MASKLA(r.kgscrap, 2) + '</td>' +
                        '<td style="text-align:right;">' + MASKLA(r.cantprod, 0) + '</td>' +
                        '<td style="white-space:nowrap;">' + fechaHoraStr(r.created_at) + '</td>' +
                        '<td style="text-align:center;"><span style="color:#00a65a;"><i class="fa fa-check-circle"></i> Aprobado</span></td>' +
                        '<td></td>' +
                        '</tr>';
                });
            }

            html += '</tbody></table>';
        } else {
            html += '<p style="margin-left:20px; font-size:11px; color:#aaa; margin-bottom:12px;">Sin registros de producción aún.</p>';
        }
    });

    html += '</div>';
    return html;
}

/**
 * Abre el modal con la etiqueta de etapa para el opdetregprod indicado.
 * Carga la vista existente (/opdetregprodtempaprobsup/etiqueta-etapa/{id}) en el iframe.
 */
function verEtiquetaEtapa(opdetregprod_id) {
    $('#modalEtiquetaId').text('#' + opdetregprod_id);
    $('#ifrEtiquetaEtapa').attr('src', '/opdetregprodtempaprobsup/etiqueta-etapa/' + opdetregprod_id);
    $('#modalEtiquetaEtapa').modal('show');
}

/**
 * Imprime solo el contenido del iframe (la etiqueta), sin el modal ni la página principal.
 * La vista etiqueta-etapa ya tiene @media print que oculta su botón interno.
 */
function imprimirEtiquetaEtapa() {
    var iframe = document.getElementById('ifrEtiquetaEtapa');
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    }
}

// Limpiar el iframe al cerrar el modal para evitar que siga cargando en background
$(document).on('hidden.bs.modal', '#modalEtiquetaEtapa', function () {
    $('#ifrEtiquetaEtapa').attr('src', 'about:blank');
});
