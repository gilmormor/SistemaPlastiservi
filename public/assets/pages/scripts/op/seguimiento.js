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

            // Col OP — enlace al reporte PDF + badge de prioridad
            var prioColor = data.prioridad == 1 ? '#e74c3c' : (data.prioridad == 2 ? '#f39c12' : '#95a5a6');
            $('td', row).eq(1).html(
                '<a class="btn-accion-tabla btn-sm tooltipsC" ' +
                    'onclick=\'genpdf(' + data.op_id + ',"","ver-pdf-op","/op/exportPdf/' + data.op_id + '")\' ' +
                    'style="padding-left:0; font-size:13px; font-weight:bold; color:#1a3a5c; cursor:pointer;" ' +
                    'data-original-title="Ver Orden de Producción">' +
                    data.op_id +
                '</a>' +
                (data.prioridad ? ' <span style="display:inline-block; width:7px; height:7px; border-radius:50%; background:' + prioColor + '; margin-left:3px;" title="Prioridad ' + data.prioridad + '"></span>' : '')
            );

            // Col Fecha
            $('td', row).eq(2).html(
                '<span style="font-size:11px; color:#5a6a7e;">' + fechaHoraStr(data.op_fecha).split(' ')[0] + '</span>'
            );

            // Col OT — enlace al reporte PDF de la OT
            $('td', row).eq(3).html(
                data.ot_id
                    ? '<a class="btn-accion-tabla btn-sm tooltipsC" ' +
                          'onclick=\'genpdf(' + data.ot_id + ',"","ver-pdf-ot","/ot/exportPdf/' + data.ot_id + '")\' ' +
                          'style="padding-left:0; font-size:12px; color:#5a6a7e; cursor:pointer;" ' +
                          'data-original-title="Orden de Trabajo">' +
                          data.ot_id +
                      '</a>'
                    : '<span style="color:#5a6a7e; font-size:12px;">—</span>'
            );

            // Col NV — enlace al reporte PDF de la NV (si existe)
            $('td', row).eq(4).html(
                data.notaventa_id
                    ? '<a class="btn-accion-tabla btn-sm tooltipsC" ' +
                          'onclick=\'genpdfNV(' + data.notaventa_id + ',1)\' ' +
                          'style="padding-left:0; font-size:12px; color:#5a6a7e; cursor:pointer;" ' +
                          'data-original-title="Nota de Venta">' +
                          data.notaventa_id +
                      '</a>'
                    : '<span style="color:#5a6a7e; font-size:12px;">—</span>'
            );

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

    // .off() primero para evitar handlers duplicados si construirTabla() se llama más de una vez
    $('#tabla-seguimiento').off('click', 'td.dt-control').on('click', 'td.dt-control', function () {
        var tr  = $(this).closest('tr')[0];
        var row = tabla.row(tr);

        if (row.child.isShown()) {
            // Cerrar
            row.child.hide();
            $(row.node()).find('td.dt-control').html(
                '<i class="fa fa-plus-circle" style="color:#3498db; font-size:14px; cursor:pointer;" title="Ver etapas"></i>'
            );
        } else {
            // Acordeón: cerrar los demás
            $('#tabla-seguimiento tbody td.dt-control').each(function () {
                var otherRow = tabla.row($(this).closest('tr')[0]);
                if (otherRow.child && otherRow.child.isShown()) {
                    otherRow.child.hide();
                    $(otherRow.node()).find('td.dt-control').html(
                        '<i class="fa fa-plus-circle" style="color:#3498db; font-size:14px; cursor:pointer;" title="Ver etapas"></i>'
                    );
                }
            });
            // Mostrar child; cambiar ícono con setTimeout(0) para que se ejecute DESPUÉS
            // de cualquier redraw sincrónico que DataTables dispare con .show()
            row.child(loadingHtml()).show();
            var rowNode = row.node();
            setTimeout(function () {
                $(rowNode).find('td.dt-control').html(
                    '<i class="fa fa-minus-circle" style="color:#e67e22; font-size:14px; cursor:pointer;" title="Ocultar etapas"></i>'
                );
            }, 0);
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
                        '<span>' +
                            '<span class="seg-chip green"><i class="fa fa-check-circle"></i> Aprobado</span>' +
                            (r.invmov_id
                                ? ' <a class="btn-accion-tabla btn-sm tooltipsC" ' +
                                      'onclick=\'genpdfINVMOV(' + r.invmov_id + ',1)\' ' +
                                      'style="padding-left:2px; font-size:11px; color:#8e44ad; cursor:pointer;" ' +
                                      'data-original-title="Movimiento de Inv.">' +
                                      '<i class="fa fa-archive"></i> ' + r.invmov_id +
                                  '</a>'
                                : '') +
                        '</span>' +
                        '<span></span>' +
                        '</li>' +
                        // Fila de trazabilidad despacho (solo última etapa, justo debajo del registro)
                        renderTrazDespacho(r);
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

// ── Trazabilidad despacho (última etapa) ───────────────────────────────────────
// Recibe un registro aprobado y devuelve HTML con chips de la cadena de despacho.
// Solo se muestra cuando r.invmov_id existe (última etapa generó movimiento de inventario).
function renderTrazDespacho(r) {
    if (!r.invmov_id) return '';

    var chips = '';

    if (!r.sol_ids) {
        chips += '<span class="seg-chip gray" style="font-size:10px;">' +
                 '<i class="fa fa-truck"></i> Sin solicitud de despacho</span>';
    } else {
        // Solicitudes de despacho
        r.sol_ids.split(',').forEach(function (id) {
            chips += '<a class="seg-chip orange" style="font-size:10px; cursor:pointer;" ' +
                     'onclick="genpdfSD(' + id + ',1)" ' +
                     'title="Solicitud de Despacho #' + id + '">' +
                     '<i class="fa fa-file-text-o"></i> Sol #' + id + '</a> ';
        });

        // Órdenes de despacho
        if (r.ord_ids) {
            r.ord_ids.split(',').forEach(function (id) {
                chips += '<a class="seg-chip blue" style="font-size:10px; cursor:pointer;" ' +
                         'onclick="genpdfOD(' + id + ',1)" ' +
                         'title="Orden de Despacho #' + id + '">' +
                         '<i class="fa fa-truck"></i> Ord #' + id + '</a> ';
            });
        }

        // Guías de despacho (foliocontrol_id=2)
        if (r.guia_data) {
            r.guia_data.split(',').forEach(function (pair) {
                if (!pair) return;
                var p       = pair.split('|');
                var dteId   = p[0];
                var nro     = p[1] ? String(parseInt(p[1])).padStart(8, '0') : '';
                var label   = p[1] ? 'N°' + parseInt(p[1]) : '#' + dteId;
                var onclick = nro ? 'genpdfGD("' + nro + '","")' : '';
                chips += '<' + (onclick ? 'a onclick="' + onclick + '" style="cursor:pointer;"' : 'span') +
                         ' class="seg-chip green" style="font-size:10px;" title="Guía de Despacho ' + label + '">' +
                         '<i class="fa fa-file-pdf-o"></i> Guía ' + label +
                         '</' + (onclick ? 'a' : 'span') + '> ';
            });
        }

        // Facturas (foliocontrol_id=1)
        if (r.fac_data) {
            r.fac_data.split(',').forEach(function (pair) {
                if (!pair) return;
                var p     = pair.split('|');
                var label = p[1] ? 'N°' + parseInt(p[1]) : '#' + p[0];
                chips += '<span class="seg-chip" ' +
                         'style="background:#8e44ad; color:#fff; font-size:10px;" ' +
                         'title="Factura ' + label + '">' +
                         '<i class="fa fa-file-pdf-o"></i> Fac ' + label + '</span> ';
            });
        }
    }

    return '<li style="grid-column:1 / -1; background:#f0f7ff; border-top:1px dashed #d0e4f7; padding:4px 8px;">' +
           '<span style="font-size:10px; color:#5a6a7e; margin-right:6px;">' +
           '<i class="fa fa-exchange"></i> Despacho:</span>' + chips + '</li>';
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
