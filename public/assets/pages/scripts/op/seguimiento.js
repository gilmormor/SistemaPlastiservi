/**
 * seguimiento.js — Seguimiento de Órdenes de Producción
 * Rediseño UX/UI 2026 — interfaz moderna y profesional.
 */

var tabla;

$(document).ready(function () {
    $('.datepicker').datepicker({
        language: 'es', autoclose: true, clearBtn: true, todayHighlight: true
    }).datepicker('setDate', new Date());

    $('.numerico-entero').numeric({ decimal: false, negative: false });

    construirTabla('');
});

// ── Consultar ──────────────────────────────────────────────────────────────────
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
           '&op_id='       + ($('#op_id').val()       || '') +
           '&ot_id='       + ($('#ot_id').val()       || '') +
           '&nv_id='       + ($('#nv_id').val()       || '') +
           '&producto_id=' + ($('#producto_id').val() || '');
}

// ── Construir DataTable ────────────────────────────────────────────────────────
function construirTabla(url) {
    var thead =
        '<thead><tr>' +
        '<th style="width:32px;"></th>' +
        '<th>OP</th>' +
        '<th>Fecha</th>' +
        '<th>OT</th>' +
        '<th>NV</th>' +
        '<th>Producto</th>' +
        '<th>Cliente</th>' +
        '<th>Kg Prog.</th>' +
        '<th>Progreso Etapas</th>' +
        '<th>Pendientes</th>' +
        '</tr></thead><tfoot></tfoot>';
    $('#tabla-seguimiento').html(thead);

    tabla = $('#tabla-seguimiento').DataTable({
        paging: true, scrollX: true, lengthChange: true,
        searching: true, ordering: true, info: true,
        autoWidth: false, processing: true,
        serverSide: url !== '',
        ajax: url || null,
        order: [[1, 'desc']],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json' },
        columns: [
            { className: 'dt-control', orderable: false, data: null,
              defaultContent: '<i class="fa fa-plus-circle" style="color:#3498db; font-size:14px; cursor:pointer;" title="Ver etapas"></i>' },
            { data: 'op_id',            width: '55px'  },
            { data: 'op_fecha',         width: '95px'  },
            { data: 'ot_id',            width: '50px'  },
            { data: 'notaventa_id',     width: '60px'  },
            { data: 'producto_nombre',  width: '180px' },
            { data: 'razonsocial',      width: '170px' },
            { data: 'op_kgprod',        width: '80px'  },
            { data: 'num_etapas',       width: '160px', orderable: false },
            { data: 'pend_no_enviados', width: '120px', orderable: false }
        ],
        drawCallback: function () { actualizarKPIs(); },
        createdRow: function (row, data) {
            $(row).attr('id', 'fila-op-' + data.op_id);

            // Col OP — negrita con badge de prioridad
            var prioColor = data.prioridad == 1 ? '#e74c3c' : (data.prioridad == 2 ? '#f39c12' : '#95a5a6');
            $('td', row).eq(1).html(
                '<strong style="font-size:13px; color:#1a3a5c;">' + data.op_id + '</strong>' +
                (data.prioridad ? ' <span style="display:inline-block; width:7px; height:7px; border-radius:50%; background:' + prioColor + '; margin-left:3px;" title="Prioridad ' + data.prioridad + '"></span>' : '')
            );

            // Col Fecha
            $('td', row).eq(2).html(
                '<span style="font-size:11px; color:#5a6a7e;">' + fechaHoraStr(data.op_fecha).split(' ')[0] + '</span>'
            );

            // Col OT
            $('td', row).eq(3).html('<span style="color:#5a6a7e; font-size:12px;">' + (data.ot_id || '—') + '</span>');

            // Col NV
            $('td', row).eq(4).html('<span style="color:#5a6a7e; font-size:12px;">' + (data.notaventa_id || '—') + '</span>');

            // Col Kg
            $('td', row).eq(7).css('text-align', 'right').html(
                '<strong style="color:#1a3a5c;">' + MASKLA(data.op_kgprod, 2) + '</strong>' +
                '<br><span style="font-size:10px; color:#aaa;">kg</span>'
            );

            // Col Progreso (barra visual)
            var n          = parseInt(data.num_etapas)         || 0;
            var comp       = parseInt(data.etapas_completadas)  || 0;
            var parc       = parseInt(data.etapas_parciales)    || 0;
            var pct        = n > 0 ? Math.round((comp / n) * 10000) / 100 : 0;
            var barColor   = comp === n && n > 0 ? '#27ae60' : (comp > 0 || parc > 0 ? '#f39c12' : '#bdc3c7');
            var labelExtra = parc > 0 ? ' <span style="color:#e67e22;">+' + parc + ' parc.</span>' : '';
            $('td', row).eq(8).html(
                '<div class="seg-progress-wrap">' +
                '<div class="seg-progress-bar-bg">' +
                '<div class="seg-progress-bar-fill" style="width:' + Math.min(pct, 100) + '%; background:' + barColor + ';"></div>' +
                '</div>' +
                '<div class="seg-progress-label">' +
                '<strong style="color:' + barColor + ';">' + comp + '/' + n + '</strong>' + labelExtra +
                ' <span style="color:#aaa;">(' + pct.toFixed(1) + '%)</span>' +
                '</div></div>'
            );

            // Col Pendientes — chips compactos
            var chips = '';
            if (parseInt(data.pend_no_enviados)  > 0) chips += '<span class="seg-chip orange"><i class="fa fa-pencil"></i> ' + data.pend_no_enviados  + ' sin enviar</span> ';
            if (parseInt(data.pend_esperando_sup) > 0) chips += '<span class="seg-chip blue"><i class="fa fa-clock-o"></i> ' + data.pend_esperando_sup + ' esp. sup.</span> ';
            if (parseInt(data.pend_rechazados)    > 0) chips += '<span class="seg-chip red"><i class="fa fa-times-circle"></i> ' + data.pend_rechazados + ' rechaz.</span>';
            if (!chips) chips = '<span class="seg-chip gray"><i class="fa fa-check"></i> Sin pendientes</span>';
            $('td', row).eq(9).html('<div style="display:flex; flex-wrap:wrap; gap:3px;">' + chips + '</div>');
        }
    });

    // Click expand
    $('#tabla-seguimiento').on('click', 'td.dt-control', function (e) {
        var tr  = e.target.closest('tr');
        var row = tabla.row(tr);
        if (row.child.isShown()) {
            row.child.hide();
            $(this).html('<i class="fa fa-plus-circle" style="color:#3498db; font-size:14px; cursor:pointer;"></i>');
        } else {
            $(this).html('<i class="fa fa-minus-circle" style="color:#e67e22; font-size:14px; cursor:pointer;"></i>');
            row.child(loadingHtml()).show();
            cargarEtapasDetalle(row.data().op_id, row);
        }
    });
}

// ── KPIs ───────────────────────────────────────────────────────────────────────
function actualizarKPIs() {
    if (!tabla) return;
    var rows = tabla.rows({ search: 'applied' }).data();
    var total = rows.length, completas = 0, enProceso = 0, conPendientes = 0;
    rows.each(function (d) {
        var n = parseInt(d.num_etapas) || 0, c = parseInt(d.etapas_completadas) || 0;
        if (n > 0 && c === n) completas++;
        else if (c > 0 || parseInt(d.etapas_parciales) > 0) enProceso++;
        if (parseInt(d.pend_no_enviados) > 0 || parseInt(d.pend_esperando_sup) > 0) conPendientes++;
    });
    $('#kpi-total').text(total);
    $('#kpi-completas').text(completas);
    $('#kpi-parciales').text(enProceso);
    $('#kpi-pendientes').text(conPendientes);
}

// ── Loader ─────────────────────────────────────────────────────────────────────
function loadingHtml() {
    return '<div style="padding:16px 24px; color:#8a99aa; font-size:12px;">' +
           '<i class="fa fa-spinner fa-spin"></i>&nbsp; Cargando detalle de etapas...</div>';
}

// ── Detalle de etapas ──────────────────────────────────────────────────────────
function cargarEtapasDetalle(op_id, row) {
    $.ajax({
        url: '/op/' + op_id + '/etapas-detalle', type: 'GET',
        success: function (etapas) { row.child(renderEtapas(op_id, etapas)).show(); },
        error:   function ()       { row.child('<p class="text-danger" style="padding:12px 20px;"><i class="fa fa-exclamation-circle"></i> Error al cargar las etapas.</p>').show(); }
    });
}

// ── Estado helper ──────────────────────────────────────────────────────────────
function estadoConfig(e) {
    var kgEnt     = (parseFloat(e.opdet_kgprod) || 0) + (parseFloat(e.opdet_kgscrap) || 0);
    var opKg      = parseFloat(e.op_kgprod) || 0;
    var completa  = opKg > 0 && kgEnt >= opKg;
    var parcial   = kgEnt > 0 && !completa;
    var espSup    = parseInt(e.temp_esperando_sup) > 0;
    var sinEnviar = parseInt(e.temp_no_enviados)   > 0;
    var rechazado = parseInt(e.temp_rechazados)    > 0;

    if (completa)        return { color:'#27ae60', borde:'#27ae60', chip:'green',  label:'Completa',           icon:'check-circle' };
    if (espSup)          return { color:'#2980b9', borde:'#3498db', chip:'blue',   label:'Esp. aprobación',    icon:'clock-o'      };
    if (sinEnviar)       return { color:'#e67e22', borde:'#f39c12', chip:'orange', label:'En proceso',         icon:'pencil'       };
    if (rechazado)       return { color:'#c0392b', borde:'#e74c3c', chip:'red',    label:'Rechazado',          icon:'times-circle' };
    if (parcial)         return { color:'#e67e22', borde:'#f39c12', chip:'orange', label:'Parcial',            icon:'spinner'      };
    return                      { color:'#bdc3c7', borde:'#d0d9e4', chip:'gray',   label:'Sin iniciar',        icon:'circle-o'     };
}

// ── Render etapas (child row) ──────────────────────────────────────────────────
function renderEtapas(op_id, etapas) {
    if (!etapas || etapas.length === 0) {
        return '<div style="padding:16px 24px; color:#8a99aa; font-size:12px;"><i class="fa fa-inbox"></i> Sin etapas registradas.</div>';
    }

    var wrap = '<div style="padding:10px 20px 14px; background:#f8fafd;">';

    etapas.forEach(function (e) {
        var cfg    = estadoConfig(e);
        var kgEnt  = (parseFloat(e.opdet_kgprod) || 0) + (parseFloat(e.opdet_kgscrap) || 0);
        var opKg   = parseFloat(e.op_kgprod) || 0;
        var pctEt  = opKg > 0 ? Math.min(Math.round(kgEnt / opKg * 100), 100) : 0;

        // Cabecera de la etapa
        wrap +=
            '<div class="seg-etapa-card" style="border-left-color:' + cfg.borde + '; margin-bottom:8px;">' +
            '<div class="seg-etapa-header">' +
            '<span class="seg-etapa-nombre">' +
                '<i class="fa fa-caret-right" style="color:' + cfg.color + '; margin-right:4px;"></i>' +
                e.etapa_nombre +
            '</span>' +
            '<span class="seg-chip ' + cfg.chip + '"><i class="fa fa-' + cfg.icon + '"></i> ' + cfg.label + '</span>' +
            '<span class="seg-etapa-meta">OpDet <strong>#' + e.opdet_id + '</strong></span>' +
            '<span class="seg-etapa-meta">Máq: ' + e.maquina_nombre + '</span>' +
            // Mini barra de progreso de la etapa
            '<div style="flex:1; min-width:100px;">' +
                '<div class="seg-progress-bar-bg" style="height:5px;">' +
                '<div class="seg-progress-bar-fill" style="width:' + pctEt + '%; background:' + cfg.borde + ';"></div>' +
                '</div>' +
                '<div style="font-size:10px; color:#8a99aa; margin-top:2px;">' +
                MASKLA(kgEnt, 2) + ' / ' + MASKLA(opKg, 2) + ' kg (' + pctEt + '%)' +
                '</div>' +
            '</div>' +
            '</div>';

        // Registros individuales
        var tieneRegs = (e.registros_temp && e.registros_temp.length > 0) ||
                        (e.registros_aprobados && e.registros_aprobados.length > 0);

        if (tieneRegs) {
            wrap += '<div class="seg-etapa-body">' +
                '<div class="seg-tl-header">' +
                '<span>ID</span><span>Operario</span><span>Fecha/Hora</span>' +
                '<span style="text-align:right;">Kg Prod</span>' +
                '<span style="text-align:right;">Kg Scrap</span>' +
                '<span style="text-align:right;">Cant</span>' +
                '<span>Estado</span><span>Obs</span>' +
                '</div>' +
                '<ul class="seg-timeline">';

            // Temp (pendientes)
            if (e.registros_temp) {
                e.registros_temp.forEach(function (r) {
                    wrap += '<li>' +
                        '<span style="color:#8a99aa; font-size:10px;"><i class="fa fa-clock-o"></i> tmp-' + r.id + '</span>' +
                        '<span style="font-weight:600;">' + r.operario_nombre + '</span>' +
                        '<span style="color:#8a99aa;">' + fechaHoraStr(r.created_at) + '</span>' +
                        '<span style="text-align:right; font-weight:600;">' + MASKLA(r.kgprod, 2)   + '</span>' +
                        '<span style="text-align:right; color:#e74c3c;">'   + MASKLA(r.kgscrap, 2)  + '</span>' +
                        '<span style="text-align:right;">'                  + MASKLA(r.cantprod, 0)  + '</span>' +
                        '<span>' + labelEstadoTemp(r.aprobstatus) + '</span>' +
                        '<span style="color:#e74c3c; font-size:10px;">' + (r.aprobobs || '') + '</span>' +
                        '</li>';
                });
            }

            // Aprobados
            if (e.registros_aprobados) {
                e.registros_aprobados.forEach(function (r) {
                    var idLink = '<a href="javascript:void(0)" onclick="verEtiquetaEtapa(' + r.id + ')" ' +
                        'title="Ver/imprimir etiqueta" style="color:#2980b9; font-weight:600;">' +
                        '<i class="fa fa-tag"></i> apr-' + r.id + '</a>';
                    wrap += '<li class="aprobado">' +
                        '<span>' + idLink + '</span>' +
                        '<span style="font-weight:600;">' + r.operario_nombre + '</span>' +
                        '<span style="color:#8a99aa;">' + fechaHoraStr(r.created_at) + '</span>' +
                        '<span style="text-align:right; font-weight:600; color:#27ae60;">' + MASKLA(r.kgprod, 2)  + '</span>' +
                        '<span style="text-align:right; color:#e74c3c;">'                 + MASKLA(r.kgscrap, 2) + '</span>' +
                        '<span style="text-align:right;">'                                + MASKLA(r.cantprod, 0) + '</span>' +
                        '<span><span class="seg-chip green"><i class="fa fa-check-circle"></i> Aprobado</span></span>' +
                        '<span></span>' +
                        '</li>';
                });
            }

            wrap += '</ul></div>';
        } else {
            wrap += '<div style="padding:8px 14px; color:#bdc3c7; font-size:11px; font-style:italic;">' +
                    '<i class="fa fa-inbox"></i> Sin registros de producción aún.</div>';
        }

        wrap += '</div>'; // seg-etapa-card
    });

    wrap += '</div>';
    return wrap;
}

// ── Helpers ────────────────────────────────────────────────────────────────────
function labelEstadoTemp(s) {
    s = parseInt(s);
    if (s === 1) return '<span class="seg-chip blue"><i class="fa fa-clock-o"></i> Esp. sup.</span>';
    if (s === 3) return '<span class="seg-chip red"><i class="fa fa-times-circle"></i> Rechazado</span>';
    return '<span class="seg-chip orange"><i class="fa fa-pencil"></i> Sin enviar</span>';
}

function fechaHoraStr(val) {
    if (!val) return '—';
    var s = val.toString().replace('T', ' ');
    var p = s.split(' ');
    if (p.length < 2) return s;
    var f = p[0].split('-');
    var h = p[1].substring(0, 5);
    return f.length === 3 ? f[2] + '/' + f[1] + '/' + f[0] + ' ' + h : s;
}

// ── Modal etiqueta ─────────────────────────────────────────────────────────────
function verEtiquetaEtapa(opdetregprod_id) {
    $('#modalEtiquetaId').text('#' + opdetregprod_id);
    $('#ifrEtiquetaEtapa').attr('src', '/opdetregprodtempaprobsup/etiqueta-etapa/' + opdetregprod_id);
    $('#modalEtiquetaEtapa').modal('show');
}

function imprimirEtiquetaEtapa() {
    var iframe = document.getElementById('ifrEtiquetaEtapa');
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    }
}

$(document).on('hidden.bs.modal', '#modalEtiquetaEtapa', function () {
    $('#ifrEtiquetaEtapa').attr('src', 'about:blank');
});
