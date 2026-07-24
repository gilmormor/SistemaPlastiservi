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

    // Reconstruir el grafo de trazabilidad entre etapas para el efecto spotlight.
    // El acordeón solo mantiene una OP expandida, así que el grafo global es seguro.
    _trazEtapasGrafoBuild(etapas);

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
                    // data-lote: el enlace del registro también participa del spotlight de cadena
                    var idLink = '<a href="javascript:void(0)" onclick="verEtiquetaEtapa(' + r.id + ')" ' +
                        'data-lote="' + r.id + '" ' +
                        'title="Ver etiqueta — Lote de producción #' + r.id + '" style="color:#2980b9; font-weight:600;">' +
                        '<i class="fa fa-tag"></i> Lote-' + r.id + '</a>';
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
                        // Fila de trazabilidad entre etapas (de qué lote viene / a cuál alimenta)
                        renderTrazEtapas(r) +
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

// ── Trazabilidad entre etapas (opdetregprod_origen) ────────────────────────────
// Muestra para cada lote aprobado: de qué lotes de la etapa anterior vino
// ("Viene de") y qué registros de la etapa siguiente consumieron de él
// ("Alimenta a"). Colapsado por defecto; reutiliza el toggle .btn-traz-toggle.
function renderTrazEtapas(r) {
    var tieneOrig = r.origenes && r.origenes.length > 0;
    var tieneDest = r.destinos && r.destinos.length > 0;
    if (!tieneOrig && !tieneDest) return ''; // sin trazabilidad (histórico o primera/última etapa)

    var inner = '';
    if (tieneOrig) {
        inner += '<div style="margin-bottom:3px;">' +
                 '<span style="font-size:10px; color:#5d6d7e; font-weight:600;">' +
                 '<i class="fa fa-sign-in"></i> Viene de:</span>';
        r.origenes.forEach(function (o) {
            // data-lote habilita el efecto spotlight de cadena (hover)
            inner += ' <span class="seg-chip" data-lote="' + o.lote_id + '" ' +
                     'style="font-size:10px; background:#eaf0f6; color:#34495e; border:1px solid #aab7c4;" ' +
                     'title="Lote de producción #' + o.lote_id + ' — Etapa: ' + (o.etapa_nombre || '—') + '">' +
                     '<i class="fa fa-tag"></i> Lote-' + o.lote_id +
                     ' &nbsp;' + MASKLA(o.kg, 2) + ' kg</span>';
        });
        inner += '</div>';
    }
    if (tieneDest) {
        inner += '<div>' +
                 '<span style="font-size:10px; color:#5d6d7e; font-weight:600;">' +
                 '<i class="fa fa-sign-out"></i> Alimenta a:</span>';
        r.destinos.forEach(function (d) {
            // data-lote habilita el efecto spotlight de cadena (hover)
            inner += ' <span class="seg-chip" data-lote="' + d.hijo_id + '" ' +
                     'style="font-size:10px; background:#eafaf1; color:#1e8449; border:1px solid #82e0aa;" ' +
                     'title="Lote de producción #' + d.hijo_id + ' — Etapa: ' + (d.etapa_nombre || '—') + '">' +
                     '<i class="fa fa-tag"></i> Lote-' + d.hijo_id +
                     ' &nbsp;' + MASKLA(d.kg, 2) + ' kg</span>';
        });
        inner += '</div>';
    }

    // Mismo patrón colapsable que Trazabilidad Despacho (display:block anula el grid del li)
    return '<li style="grid-column:1 / -1; display:block; background:#fbf7f0; border-top:1px dashed #e8d9bd; ' +
           'padding:6px 12px; list-style:none;">' +
           '<div style="font-size:10px; color:#8a6d2f; font-weight:bold; margin-bottom:4px; cursor:pointer;" ' +
                'class="btn-traz-toggle">' +
           '<i class="fa fa-plus-circle" style="color:#3498db; font-size:14px; cursor:pointer;" title="Ver trazabilidad etapas"></i> ' +
           '<i class="fa fa-random"></i> Trazabilidad Etapas</div>' +
           '<div class="traz-content" style="display:none; margin-top:4px;">' + inner + '</div>' +
           '</li>';
}

// ── Trazabilidad despacho (última etapa) ───────────────────────────────────────
// Dibuja el árbol jerárquico: Sol → Ord → Guía → Factura → NC/ND
// Solo se muestra cuando r.invmov_id existe (última etapa).
// ── Helpers trazabilidad despacho ─────────────────────────────────────────────
// Convierte "YYYY-MM-DD HH:MM:SS" → "DD/MM/YYYY HH:MM"
function _trazFecha(fh) {
    if (!fh) return '';
    var s = String(fh).substring(0, 16);
    if (s.length < 10) return s;
    return s.substring(8,10) + '/' + s.substring(5,7) + '/' + s.substring(0,4) +
           (s.length > 10 ? ' ' + s.substring(11,16) : '');
}
// Chip "Creado" — mismo estilo que chip Enviado pero color gris azulado
function _trazInfo(nombre, fechahora) {
    if (!nombre && !fechahora) return '';
    var fh    = _trazFecha(fechahora);
    var title = 'Creado por: ' + (nombre || '—') + (fh ? ' ' + fh : '');
    var texto = (nombre || '—') + (fh ? ' &nbsp;|&nbsp; ' + fh : '');
    return ' <span class="seg-chip" style="font-size:10px; background:#eaf0f6; color:#5d6d7e; border:1px solid #aab7c4;" title="' + title + '">' +
           '<i class="fa fa-user-circle-o"></i> Creado &nbsp;' + texto +
           '</span>';
}
// Chip estado Sol/Ord: aprXXX=0 → En bandeja; =1 → Enviado + fecha
function _trazEstadoSolOrd(aprobado, fechaEnvio) {
    if (parseInt(aprobado) === 1) {
        return ' <span class="seg-chip" style="font-size:10px;background:#eafaf1;color:#27ae60;border:1px solid #82e0aa;">' +
               '<i class="fa fa-check-circle"></i> Enviado ' + _trazFecha(fechaEnvio) + '</span>';
    }
    return ' <span class="seg-chip" style="font-size:10px;background:#fef9e7;color:#d4ac0d;border:1px solid #f9d489;">' +
           '<i class="fa fa-clock-o"></i> En bandeja</span>';
}
// Chip estado DTE: aprobstatus=0 → En módulo; >0 → Enviado por nombre | fecha
function _trazEstadoDte(aprobstatus, aprobador, aprobfechahora) {
    if (parseInt(aprobstatus) > 0) {
        var p = [];
        if (aprobador)      p.push('<i class="fa fa-user-o"></i> ' + aprobador);
        if (aprobfechahora) p.push(_trazFecha(aprobfechahora));
        var det = p.length ? ' &nbsp;' + p.join(' | ') : '';
        return ' <span class="seg-chip" style="font-size:10px;background:#eafaf1;color:#27ae60;border:1px solid #82e0aa;">' +
               '<i class="fa fa-check-circle"></i> Enviado' + det + '</span>';
    }
    return ' <span class="seg-chip" style="font-size:10px;background:#fef9e7;color:#d4ac0d;border:1px solid #f9d489;">' +
           '<i class="fa fa-clock-o"></i> En módulo</span>';
}

function renderTrazDespacho(r) {
    if (!r.invmov_id) return '';

    var traza = r.traza_despacho;
    var inner = '';

    if (!traza || traza.length === 0) {
        inner = '<span class="seg-chip gray" style="font-size:10px;">' +
                '<i class="fa fa-truck"></i> Sin solicitud de despacho</span>';
    } else {
        traza.forEach(function (sol) {
            // ── Sol ──────────────────────────────────────────────────────────
            var cSol = 's' + sol.id; // chain id acumulado
            var solStyle = sol.anulada
                ? 'font-size:12px;cursor:pointer;background:#c0392b;color:#fff;'
                : 'font-size:12px;cursor:pointer;background:#e8920a;color:#fff;';
            var solLabel = '<i class="fa fa-file-text-o"></i> Sol #' + sol.id +
                           (sol.anulada ? ' <em>[Anulada]</em>' : '');
            var solEstado = sol.anulada
                ? '<span class="seg-chip" style="font-size:10px;background:#fadbd8;color:#c0392b;border:1px solid #e74c3c;">' +
                  '<i class="fa fa-ban"></i> Anulada</span>'
                : _trazEstadoSolOrd(sol.aprorddesp, sol.aprorddespfh);
            inner += '<div style="margin-bottom:5px;">' +
                     '<a class="seg-chip" data-chain="' + cSol + '" ' +
                        'style="' + solStyle + '" ' +
                        'onclick="genpdfSD(' + sol.id + ',1)" ' +
                        'title="Solicitud de Despacho #' + sol.id + '">' +
                        solLabel + '</a>' +
                     '<div style="margin-left:12px; margin-top:2px;">' +
                     _trazInfo(sol.usuario_nombre, sol.fechahora) +
                     solEstado +
                     '</div>';

            if (!sol.ords || sol.ords.length === 0) {
                inner += '<span style="font-size:10px; color:#aaa; font-style:italic; margin-left:12px;">Sin órdenes</span>';
            } else {
                sol.ords.forEach(function (ord) {
                    // ── Ord ──────────────────────────────────────────────────
                    var cOrd  = cSol + '-o' + ord.id; // chain acumulado
                    var ordIcon  = ord.anulada ? 'ban' : 'truck';
                    var ordLabel = 'Ord #' + ord.id + (ord.anulada ? ' <em>[Anulada]</em>' : '');
                    var ordStyle = ord.anulada
                        ? 'font-size:12px;cursor:pointer;background:#c0392b;color:#fff;'
                        : 'font-size:12px;cursor:pointer;background:#2471a3;color:#fff;';
                    inner += '<div style="margin-left:14px; margin-top:3px;">' +
                             '<span style="color:#bdc3c7; margin-right:2px;">↳</span>' +
                             '<a class="seg-chip" data-chain="' + cOrd + '" style="' + ordStyle + '" ' +
                                'onclick="genpdfOD(' + ord.id + ',1)" ' +
                                'title="Orden de Despacho #' + ord.id + '">' +
                                '<i class="fa fa-' + ordIcon + '"></i> ' + ordLabel + '</a>' +
                             '<div style="margin-left:12px; margin-top:2px;">' +
                             _trazInfo(ord.usuario_nombre, ord.fechahora) +
                             (!ord.anulada ? _trazEstadoSolOrd(ord.aprguiadesp, ord.aprguiadespfh) : '') +
                             '</div>';

                    if (!ord.guias || ord.guias.length === 0) {
                        inner += '<span style="font-size:10px; color:#aaa; font-style:italic; margin-left:12px;">Sin guía</span>';
                    } else {
                        ord.guias.forEach(function (guia) {
                            // ── Guía ─────────────────────────────────────────
                            var cGuia   = cOrd + '-g' + guia.id; // chain acumulado
                            var guiaIcon    = guia.anulada ? 'ban' : 'file-pdf-o';
                            var guiaNro     = guia.nrodocto ? 'N°' + parseInt(guia.nrodocto) : '#' + guia.id;
                            var guiaOnClick = !guia.anulada ? 'onclick="genpdfFACDin(' + guia.id + ',0)"' : '';
                            var guiaTag     = guiaOnClick ? 'a' : 'span';
                            var guiaStyle   = guia.anulada
                                ? 'font-size:12px;background:#c0392b;color:#fff;'
                                : 'font-size:12px;background:#2e86c1;color:#fff;' + (guiaOnClick ? 'cursor:pointer;' : '');
                            inner += '<div style="margin-left:14px; margin-top:3px;">' +
                                     '<span style="color:#bdc3c7; margin-right:2px;">↳</span>' +
                                     '<' + guiaTag + ' class="seg-chip" data-chain="' + cGuia + '" ' +
                                        'style="' + guiaStyle + '" ' +
                                        guiaOnClick + ' title="Guía ' + guiaNro + '">' +
                                        '<i class="fa fa-' + guiaIcon + '"></i> Guía ' + guiaNro +
                                        (guia.anulada ? ' <em>[Anulada]</em>' : '') +
                                     '</' + guiaTag + '>' +
                                     '<div style="margin-left:12px; margin-top:2px;">' +
                                     _trazInfo(guia.usuario_nombre, guia.fechahora) +
                                     (!guia.anulada ? _trazEstadoDte(guia.aprobstatus, guia.aprobador_nombre, guia.aprobfechahora) : '') +
                                     '</div>';

                            if (!guia.facturas || guia.facturas.length === 0) {
                                inner += '<span style="font-size:10px; color:#aaa; font-style:italic; margin-left:12px;">Sin factura</span>';
                            } else {
                                guia.facturas.forEach(function (fac) {
                                    // ── Factura — genpdfFACDin(dte.id, 0) ────────
                                    var cFac = cGuia + '-f' + fac.id; // chain acumulado
                                    var facNro = fac.nrodocto ? 'N°' + parseInt(fac.nrodocto) : '#' + fac.id;
                                    inner += '<div style="margin-left:14px; margin-top:3px;">' +
                                             '<span style="color:#bdc3c7; margin-right:2px;">↳</span>' +
                                             '<a class="seg-chip" data-chain="' + cFac + '" ' +
                                                'onclick="genpdfFACDin(' + fac.id + ',0)" ' +
                                                'style="font-size:12px;cursor:pointer;background:#7d3c98;color:#fff;" ' +
                                                'title="Factura ' + facNro + '">' +
                                                '<i class="fa fa-file-pdf-o"></i> Fac ' + facNro + '</a>' +
                                             '<div style="margin-left:12px; margin-top:2px;">' +
                                             _trazInfo(fac.usuario_nombre, fac.fechahora) +
                                             _trazEstadoDte(fac.aprobstatus, fac.aprobador_nombre, fac.aprobfechahora) +
                                             '</div>';

                                    // ── NC / ND ───────────────────────────────
                                    if (fac.ncnd && fac.ncnd.length > 0) {
                                        fac.ncnd.forEach(function (n) {
                                            var esNC    = parseInt(n.foliocontrol_id) === 5;
                                            var cN      = cFac + '-n' + n.id; // chain acumulado
                                            var nNro    = n.nrodocto ? 'N°' + parseInt(n.nrodocto) : '#' + n.id;
                                            var nIcon   = esNC ? 'minus-circle' : 'plus-circle';
                                            var nTipo   = esNC ? 'NC' : 'ND';
                                            var nStyle  = esNC
                                                ? 'font-size:12px;background:#922b21;color:#fff;'
                                                : 'font-size:12px;background:#1f618d;color:#fff;';
                                            inner += '<div style="margin-left:14px; margin-top:3px;">' +
                                                     '<span style="color:#bdc3c7; margin-right:2px;">↳</span>' +
                                                     '<span class="seg-chip" data-chain="' + cN + '" style="' + nStyle + '" ' +
                                                           'title="' + nTipo + ' ' + nNro + '">' +
                                                           '<i class="fa fa-' + nIcon + '"></i> ' + nTipo + ' ' + nNro +
                                                     '</span>' +
                                                     '<div style="margin-left:12px; margin-top:2px;">' +
                                                     _trazInfo(n.usuario_nombre, n.fechahora) +
                                                     _trazEstadoDte(n.aprobstatus, n.aprobador_nombre, n.aprobfechahora) +
                                                     '</div>' +
                                                     '</div>'; // nc/nd
                                        });
                                    }
                                    inner += '</div>'; // factura
                                });
                            }
                            inner += '</div>'; // guía
                        });
                    }
                    inner += '</div>'; // ord
                });
            }
            inner += '</div>'; // sol
        });
    }

    // display:block anula el "display:grid" que .seg-timeline li hereda del CSS,
    // evitando que los divs de cada Sol queden en columnas adyacentes en vez de apilarse.
    // El contenido inicia oculto; el icono + / - lo colapsa/expande igual que el DataTable.
    return '<li style="grid-column:1 / -1; display:block; background:#f0f8ff; border-top:1px dashed #c8dff5; ' +
           'padding:6px 12px; list-style:none;">' +
           '<div style="font-size:10px; color:#2c5f8a; font-weight:bold; margin-bottom:4px; cursor:pointer;" ' +
                'class="btn-traz-toggle">' +
           '<i class="fa fa-plus-circle" style="color:#3498db; font-size:14px; cursor:pointer;" title="Ver trazabilidad"></i> ' +
           '<i class="fa fa-exchange"></i> Trazabilidad Despacho</div>' +
           '<div class="traz-content" style="display:none; margin-top:4px;">' + inner + '</div>' +
           '</li>';
}

// ── Efecto cadena: spotlight al hacer hover (activa cadena, atenúa el resto) ───
$(document).on('mouseenter', '.seg-chip[data-chain]', function () {
    var chain = String($(this).data('chain'));
    $('.seg-chip[data-chain]').each(function () {
        var c = String($(this).data('chain'));
        // En la cadena: mismo chip, ancestro (c es prefijo de chain) o descendiente (chain es prefijo de c)
        var enCadena = (c === chain) ||
                       (chain.indexOf(c + '-') === 0) ||
                       (c.indexOf(chain + '-') === 0);
        if (enCadena) {
            $(this).addClass('traz-activo').removeClass('traz-inactivo');
        } else {
            $(this).addClass('traz-inactivo').removeClass('traz-activo');
        }
    });
}).on('mouseleave', '.seg-chip[data-chain]', function () {
    // Modo automático (check desmarcado): limpiar al quitar el mouse.
    // Modo fijado (check marcado): mantener; se limpia con clic en zona vacía.
    if (!$('#chk-fijar-traz').is(':checked')) {
        $('.seg-chip[data-chain]').removeClass('traz-activo traz-inactivo');
    }
});

// ── Efecto cadena en Trazabilidad Etapas (grafo padre/hijo por lote) ───────────
// A diferencia de despacho (árbol con prefijos), las etapas forman un grafo N:M:
// un lote puede venir de varios y alimentar a varios. Se construye el grafo al
// renderizar y al hacer hover se recorre hacia arriba (ancestros) y hacia abajo
// (descendientes) para iluminar la cadena completa, atenuando el resto.
var _trazEtapasPadres = {}; // hijo  → [padres]
var _trazEtapasHijos  = {}; // padre → [hijos]

function _trazEtapasGrafoBuild(etapas) {
    _trazEtapasPadres = {};
    _trazEtapasHijos  = {};
    etapas.forEach(function (e) {
        (e.registros_aprobados || []).forEach(function (r) {
            (r.origenes || []).forEach(function (o) {
                // arista: lote_id (padre, etapa anterior) → r.id (hijo, etapa actual)
                (_trazEtapasPadres[r.id]      = _trazEtapasPadres[r.id]      || []).push(o.lote_id);
                (_trazEtapasHijos[o.lote_id]  = _trazEtapasHijos[o.lote_id]  || []).push(r.id);
            });
            (r.destinos || []).forEach(function (d) {
                // arista: r.id (padre) → hijo_id (hijo, etapa siguiente)
                (_trazEtapasPadres[d.hijo_id] = _trazEtapasPadres[d.hijo_id] || []).push(r.id);
                (_trazEtapasHijos[r.id]       = _trazEtapasHijos[r.id]       || []).push(d.hijo_id);
            });
        });
    });
}

// Selector genérico [data-lote]: aplica a los chips Viene de/Alimenta a Y al
// enlace apr-XX de cada registro aprobado (verEtiquetaEtapa).
$(document).on('mouseenter', '[data-lote]', function () {
    var id = String($(this).data('lote'));
    var cadena = {};
    cadena[id] = true;
    // Recorrido hacia arriba (ancestros) con set de visitados (evita ciclos/duplicados)
    var pila = [id];
    while (pila.length) {
        var n = pila.pop();
        (_trazEtapasPadres[n] || []).forEach(function (p) {
            p = String(p);
            if (!cadena[p]) { cadena[p] = true; pila.push(p); }
        });
    }
    // Recorrido hacia abajo (descendientes)
    pila = [id];
    while (pila.length) {
        var m = pila.pop();
        (_trazEtapasHijos[m] || []).forEach(function (h) {
            h = String(h);
            if (!cadena[h]) { cadena[h] = true; pila.push(h); }
        });
    }
    // Iluminar todos los elementos de la cadena (chips y enlaces apr-XX), atenuar el resto
    $('[data-lote]').each(function () {
        if (cadena[String($(this).data('lote'))]) {
            $(this).addClass('traz-activo').removeClass('traz-inactivo');
        } else {
            $(this).addClass('traz-inactivo').removeClass('traz-activo');
        }
    });
}).on('mouseleave', '[data-lote]', function () {
    // Modo automático (check desmarcado): limpiar al quitar el mouse.
    // Modo fijado (check marcado): mantener; se limpia con clic en zona vacía.
    if (!$('#chk-fijar-traz').is(':checked')) {
        $('[data-lote]').removeClass('traz-activo traz-inactivo');
    }
});

// ── Limpiar spotlight (despacho + etapas) con clic en cualquier zona vacía ─────
// Activo en ambos modos (en automático no estorba; en fijado es la forma de limpiar).
// Si el clic cae sobre un chip/enlace de cadena NO se limpia, para no interferir
// con sus clics existentes (abrir PDF de Sol/Ord/Guía/Fac, ver etiqueta apr-XX).
$(document).on('click', function (e) {
    if ($(e.target).closest('[data-lote], [data-chain]').length) return;
    $('[data-lote], .seg-chip[data-chain]').removeClass('traz-activo traz-inactivo');
});

// ── Preferencia del modo fijado (localStorage, por navegador/usuario) ──────────
$(document).ready(function () {
    // Por defecto desmarcado; si el usuario lo cambió antes, restaurar su preferencia
    $('#chk-fijar-traz').prop('checked', localStorage.getItem('seg_fijar_traz') === '1');
    $('#chk-fijar-traz').on('change', function () {
        localStorage.setItem('seg_fijar_traz', $(this).is(':checked') ? '1' : '0');
        // Al cambiar de modo, limpiar cualquier iluminación pendiente
        $('[data-lote], .seg-chip[data-chain]').removeClass('traz-activo traz-inactivo');
    });
});

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

// ── Toggle colapsar / expandir Trazabilidad Despacho ──────────────────────────
$(document).on('click', '.btn-traz-toggle', function () {
    var $icon    = $(this).find('i.fa-plus-circle, i.fa-minus-circle');
    var $content = $(this).next('.traz-content');
    if ($content.is(':visible')) {
        $content.slideUp(150);
        $icon.removeClass('fa-minus-circle').addClass('fa-plus-circle')
             .css('color', '#3498db').attr('title', 'Ver trazabilidad');
    } else {
        $content.slideDown(150);
        $icon.removeClass('fa-plus-circle').addClass('fa-minus-circle')
             .css('color', '#e67e22').attr('title', 'Ocultar trazabilidad');
    }
});
