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
            var pct = data.num_etapas > 0
                ? Math.round((data.etapas_completadas / data.num_etapas) * 100) : 0;
            var color = pct === 100 ? '#00a65a' : (pct > 0 ? '#f39c12' : '#aaa');
            $('td', row).eq(8).css('text-align', 'center').html(
                '<span style="color:' + color + '; font-weight:bold;">' +
                data.etapas_completadas + '/' + data.num_etapas + '</span>' +
                ' <small>(' + pct + '%)</small>'
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
 * Genera el HTML del detalle de etapas para el child row.
 */
function renderEtapas(op_id, etapas) {
    if (!etapas || etapas.length === 0) {
        return '<p class="text-muted" style="padding:10px;">Sin etapas registradas para esta OP.</p>';
    }

    var html = '<div style="margin-left:30px; margin-bottom:10px;">' +
               '<table class="table table-condensed table-bordered" style="font-size:12px; width:90%;">' +
               '<thead><tr style="background:#f4f4f4;">' +
               '<th>Ord</th><th>Etapa</th><th>Máquina</th>' +
               '<th style="text-align:right;">Kg Total</th>' +
               '<th style="text-align:right;">Kg Aprobado</th>' +
               '<th style="text-align:right;">Kg Pendiente</th>' +
               '<th style="text-align:center;">Estado</th>' +
               '</tr></thead><tbody>';

    etapas.forEach(function (e) {
        var estado = '';
        var colorFila = '';

        if (parseFloat(e.kgprod_aprobado) > 0 && parseFloat(e.opdet_saldokg) <= 0) {
            // Etapa completada
            estado = '<span style="color:#00a65a;"><i class="fa fa-check-circle"></i> Completa</span>';
            colorFila = 'background:#f0fff0;';
        } else if (parseFloat(e.temp_esperando) > 0) {
            estado = '<span style="color:#3c8dbc;"><i class="fa fa-clock-o"></i> Esperando aprobación (' + e.temp_esperando_sup + ')</span>';
            colorFila = 'background:#eef4ff;';
        } else if (parseFloat(e.temp_no_enviados) > 0) {
            estado = '<span style="color:#f39c12;"><i class="fa fa-pencil"></i> En proceso (' + e.temp_no_enviados + ' sin enviar)</span>';
            colorFila = 'background:#fff8ee;';
        } else if (parseFloat(e.temp_rechazados) > 0) {
            estado = '<span style="color:#dd4b39;"><i class="fa fa-times-circle"></i> Rechazado (' + e.temp_rechazados + ')</span>';
            colorFila = 'background:#fff0f0;';
        } else if (parseFloat(e.kgprod_aprobado) > 0) {
            estado = '<span style="color:#f39c12;"><i class="fa fa-spinner"></i> Parcial</span>';
            colorFila = 'background:#fffbee;';
        } else {
            estado = '<span style="color:#aaa;"><i class="fa fa-circle-o"></i> Sin iniciar</span>';
        }

        var kgPend = parseFloat(e.opdet_saldokg) || 0;

        html += '<tr style="' + colorFila + '">' +
            '<td style="text-align:center;">' + e.etapa_orden + '</td>' +
            '<td><strong>' + e.etapa_nombre + '</strong></td>' +
            '<td>' + e.maquina_nombre + '</td>' +
            '<td style="text-align:right;">' + MASKLA(e.opdet_kgrec, 2) + '</td>' +
            '<td style="text-align:right; color:#00a65a;">' + MASKLA(e.kgprod_aprobado, 2) + '</td>' +
            '<td style="text-align:right; color:#f39c12;">' + MASKLA(kgPend, 2) + '</td>' +
            '<td style="text-align:center;">' + estado + '</td>' +
            '</tr>';
    });

    html += '</tbody></table></div>';
    return html;
}
