$(document).ready(function () {
    $("#btnbuscartraz").on("click", buscarTrazabilidad);
});

// ── Modal etiqueta (mismo modal/patrón que op/seguimiento.js) ──────────────────
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

function buscarTrazabilidad() {
    var params = {
        nrofactura:   $("#nrofactura").val(),
        nroguia:      $("#nroguia").val(),
        notaventa_id: $("#notaventa_id").val(),
        lote_id:     $("#lote_id").val(),
        producto_id: $("#producto_id").val(),
        fechad:      $("#fechad").val(),
        fechah:      $("#fechah").val()
    };

    var $cont = $("#trz-resultados");
    $cont.html('<div class="trz-empty"><i class="fa fa-spinner fa-spin"></i> Buscando...</div>');

    $.get("/trazabilidaddocumento/buscar", params)
        .done(function (resp) {
            renderResultados(resp.items || [], resp.nv_info);
        })
        .fail(function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : "Error al buscar la trazabilidad.";
            $cont.html('<div class="trz-empty" style="color:#a93226;"><i class="fa fa-exclamation-triangle"></i> ' + msg + '</div>');
        });
}

// Aviso del estado de la NV: aclara si no existe, si no tiene OT creada, o
// que OT(s) tiene y en que estado. Sin esto, una NV sin OT devolvia "sin
// resultados" sin explicar el motivo.
function renderAvisoNv(nv) {
    if (!nv) return '';

    if (!nv.existe) {
        return '<div class="trz-empty" style="color:#a93226;border-left:3px solid #a93226;padding-left:10px;">' +
               '<i class="fa fa-exclamation-triangle"></i> La Nota de Venta <strong>' + nv.notaventa_id +
               '</strong> no existe.</div>';
    }

    if (!nv.ots.length) {
        return '<div class="trz-empty" style="color:#b7600a;border-left:3px solid #f39c12;padding-left:10px;">' +
               '<i class="fa fa-exclamation-triangle"></i> La Nota de Venta <strong>' + nv.notaventa_id +
               '</strong> existe pero <strong>no tiene ninguna OT creada</strong>, por lo que aun no hay produccion ni lotes que trazar.</div>';
    }

    // La NV tiene OT: no se muestra panel aparte. El estado de cada OT se ve
    // dentro de la cadena de trazabilidad, junto a su propio chip (renderItem).
    // Solo se avisa si ninguna OT llego a generar OP, porque en ese caso la
    // busqueda no devuelve lotes y conviene explicar por que.
    var conOp = nv.ots.filter(function (o) { return o.con_op; }).length;
    if (conOp === 0) {
        var det = nv.ots.map(function (o) {
            if (o.anulada)        return 'OT-' + o.id + ' anulada';
            if (o.rechazada)      return 'OT-' + o.id + ' rechazada';
            if (!o.aprobada)      return 'OT-' + o.id + ' pendiente de aprobar';
            return 'OT-' + o.id + ' aprobada pero sin OP';
        }).join(', ');
        return '<div class="trz-empty" style="color:#b7600a;border-left:3px solid #f39c12;padding-left:10px;">' +
               '<i class="fa fa-exclamation-triangle"></i> La Nota de Venta <strong>' + nv.notaventa_id +
               '</strong> tiene OT pero aun no hay produccion: ' + det + '.</div>';
    }
    return '';
}

function renderResultados(items, nvInfo) {
    var $cont = $("#trz-resultados");
    var aviso = renderAvisoNv(nvInfo);

    if (!items.length) {
        $cont.html(aviso + '<div class="trz-empty"><i class="fa fa-inbox"></i> Sin resultados para los filtros indicados.</div>');
        return;
    }

    var html = aviso;
    items.forEach(function (it) {
        html += renderItem(it);
    });
    $cont.html(html);
}

function fechaCorta(fh) {
    if (!fh) return '';
    var s = String(fh).substring(0, 16);
    if (s.length < 10) return s;
    return s.substring(8,10) + '/' + s.substring(5,7) + '/' + s.substring(0,4) +
           (s.length > 10 ? ' ' + s.substring(11,16) : '');
}

function renderItem(it) {
    var flow = '<div class="trz-flow">';
    if (it.notaventa_id) {
        flow += '<a href="javascript:void(0)" class="trz-chip purple" style="cursor:pointer;" ' +
            'onclick="genpdfNV(' + it.notaventa_id + ',1)" title="Nota de Venta">' +
            '<i class="fa fa-file-text-o"></i> NV-' + it.notaventa_id +
        '</a>';
        flow += '<span class="trz-arrow"><i class="fa fa-long-arrow-right"></i></span>';
    }
    // Estado de la OT junto a su chip: solo se anota cuando NO esta aprobada,
    // para no ensuciar la cadena en el caso normal.
    var otCls = 'blue', otNota = '', otTitle = 'Orden de Trabajo';
    if (it.ot_anulada)        { otCls = 'red';    otNota = ' (anulada)';              otTitle = 'Orden de Trabajo — anulada'; }
    else if (it.ot_rechazada) { otCls = 'red';    otNota = ' (rechazada)';            otTitle = 'Orden de Trabajo — rechazada'; }
    else if (!it.ot_aprobada) { otCls = 'orange'; otNota = ' (pend. aprobación)';     otTitle = 'Orden de Trabajo — pendiente de aprobación'; }

    flow += '<a href="javascript:void(0)" class="trz-chip ' + otCls + '" style="cursor:pointer;" ' +
        'onclick=\'genpdf(' + it.ot_id + ',"","ver-pdf-ot","/ot/exportPdf/' + it.ot_id + '")\' title="' + otTitle + '">' +
        '<i class="fa fa-clipboard"></i> OT-' + it.ot_id + otNota +
    '</a>';
    flow += '<span class="trz-arrow"><i class="fa fa-long-arrow-right"></i></span>';
    flow += '<a href="javascript:void(0)" class="trz-chip blue" style="cursor:pointer;" ' +
        'onclick=\'genpdf(' + it.op_id + ',"","ver-pdf-op","/op/exportPdf/' + it.op_id + '")\' title="Orden de Producción">' +
        '<i class="fa fa-cogs"></i> OP-' + it.op_id +
    '</a>';
    flow += '</div>';

    var html = '<div class="trz-item">' +
        '<div class="trz-item-header">' +
            '<a href="javascript:void(0)" onclick="verEtiquetaEtapa(' + it.lote_id + ')" ' +
                'title="Ver etiqueta — Lote de producción #' + it.lote_id + '" class="lote-tag" style="color:#fff;">' +
                '<i class="fa fa-tag"></i> Lote-' + it.lote_id +
            '</a>' +
            '<span style="font-weight:600;">' + (it.producto_nombre || ('Prod. ' + it.producto_id)) + '</span>' +
            '<span style="color:#8a99aa; font-size:11px;">' + (it.etapa_nombre || '—') + '</span>' +
            '<span style="margin-left:auto; text-align:right; font-size:11px; color:#8a99aa;">' +
                MASKLA(it.kgprod, 2) + ' kg &nbsp;/&nbsp; ' + MASKLA(it.cantprod, 0) + ' u.<br>' +
                fechaCorta(it.aprobfechahora) +
            '</span>' +
        '</div>' +
        '<div class="trz-item-body">' +
            flow +
            '<div class="trz-lotes-title"><i class="fa fa-industry"></i> Cadena de lotes de producción</div>' +
            renderCadenaLotes(it) +
        '</div>' +
    '</div>';
    return html;
}

// Aplana el árbol (puede ramificarse, ej. un lote consumido de 2 lotes previos
// que a su vez vienen de un mismo lote más antiguo) en una lista única de
// lotes, sumando kg si el mismo lote aparece más de una vez en el árbol.
function aplanarArbolLotes(nodos, campoHijos) {
    var porLote = {};
    (function recorrer(lista) {
        (lista || []).forEach(function (n) {
            if (!porLote[n.lote_id]) {
                porLote[n.lote_id] = {
                    lote_id: n.lote_id, kg: 0,
                    etapa_nombre: n.etapa_nombre,
                    despacho: n.despacho || []
                };
            }
            porLote[n.lote_id].kg += parseFloat(n.kg) || 0;
            recorrer(n[campoHijos]);
        });
    })(nodos);
    return Object.keys(porLote).map(function (k) { return porLote[k]; })
        .sort(function (a, b) { return a.lote_id - b.lote_id; });
}

// Cadena completa en una sola lista continua, ascendente por número de lote:
// lotes de etapas anteriores → el lote consultado (destacado, en su posición
// real dentro de la secuencia) → lotes de etapas siguientes. El despacho se
// muestra debajo de cualquier lote de la lista que efectivamente lo tenga
// (normalmente el último — el lote consultado si es final, o uno de los
// siguientes si es intermedio).
function renderCadenaLotes(it) {
    var antes   = aplanarArbolLotes(it.origenes, 'origenes');
    var despues = aplanarArbolLotes(it.destinos, 'destinos');

    var actual = {
        lote_id: it.lote_id,
        kg: it.kgprod,
        etapa_nombre: it.etapa_nombre,
        despacho: despues.length ? [] : (it.despacho || []) // si tiene siguientes, el despacho real está en ellos
    };

    var html = '<div class="trz-tree">';
    antes.forEach(function (o) { html += filaLote(o, false); });
    html += filaLote(actual, true);
    despues.forEach(function (d) { html += filaLote(d, false); });
    html += '</div>';
    return html;
}

function filaLote(o, esActual) {
    var html = '<div class="trz-tree-node' + (esActual ? ' trz-actual' : '') + '">' +
        '<a href="javascript:void(0)" onclick="verEtiquetaEtapa(' + o.lote_id + ')" ' +
            'data-lote="' + o.lote_id + '" title="Ver etiqueta — Lote de producción #' + o.lote_id + '" ' +
            'class="trz-chip ' + (esActual ? 'blue' : 'gray') + '" style="cursor:pointer;">' +
            '<i class="fa fa-tag"></i> Lote-' + o.lote_id + (esActual ? ' (consultado)' : '') +
        '</a>' +
        '<span style="font-size:11px; color:#8a99aa;">' + (o.etapa_nombre || '—') + ' &nbsp;·&nbsp; ' + MASKLA(o.kg, 2) + ' kg</span>' +
    '</div>';
    if (o.despacho && o.despacho.length) {
        html += '<div class="trz-tree">' +
            '<div class="trz-lotes-title" style="margin-top:6px;"><i class="fa fa-truck"></i> Trazabilidad de despacho</div>' +
            renderDespacho(o.despacho) +
        '</div>';
    }
    return html;
}

function renderDespacho(sols) {
    if (!sols || !sols.length) {
        return '<div class="trz-empty">Este lote aún no ha sido despachado.</div>';
    }
    var html = '';
    sols.forEach(function (sol) {
        var cSol = 's' + sol.id;
        html += '<div class="trz-tree-node">' +
            '<a href="javascript:void(0)" class="trz-chip ' + (sol.anulada ? 'red' : 'orange') + '" ' +
                'style="cursor:pointer;" data-chain="' + cSol + '" ' +
                'onclick="genpdfSD(' + sol.id + ',1)" title="Solicitud de Despacho #' + sol.id + '">' +
                '<i class="fa fa-file-text-o"></i> Sol #' + sol.id + (sol.anulada ? ' (Anulada)' : '') +
            '</a> <small style="color:#8a99aa;">' + fechaCorta(sol.fechahora) + '</small>' +
        '</div>';
        if (sol.ords && sol.ords.length) {
            html += '<div class="trz-tree">';
            sol.ords.forEach(function (ord) {
                var cOrd = cSol + '-o' + ord.id;
                html += '<div class="trz-tree-node">' +
                    '<a href="javascript:void(0)" class="trz-chip ' + (ord.anulada ? 'red' : 'blue') + '" ' +
                        'style="cursor:pointer;" data-chain="' + cOrd + '" ' +
                        'onclick="genpdfOD(' + ord.id + ',1)" title="Orden de Despacho #' + ord.id + '">' +
                        '<i class="fa fa-truck"></i> Orden #' + ord.id + (ord.anulada ? ' (Anulada)' : '') +
                    '</a> <small style="color:#8a99aa;">' + fechaCorta(ord.fechahora) + '</small>' +
                '</div>';
                if (ord.guias && ord.guias.length) {
                    html += '<div class="trz-tree">';
                    ord.guias.forEach(function (guia) {
                        var cGuia = cOrd + '-g' + guia.id;
                        html += '<div class="trz-tree-node">' +
                            '<a href="javascript:void(0)" class="trz-chip ' + (guia.anulada ? 'red' : 'green') + '" ' +
                                'style="cursor:pointer;" data-chain="' + cGuia + '" ' +
                                'onclick="genpdfFACDin(' + guia.id + ',0)" title="Guía N°' + (guia.nrodocto || guia.id) + '">' +
                                '<i class="fa fa-file-pdf-o"></i> Guía N°' + (guia.nrodocto || guia.id) + (guia.anulada ? ' (Anulada)' : '') +
                            '</a> <small style="color:#8a99aa;">' + fechaCorta(guia.fechahora) + '</small>' +
                        '</div>';
                        if (guia.facturas && guia.facturas.length) {
                            html += '<div class="trz-tree">';
                            guia.facturas.forEach(function (fac) {
                                var cFac = cGuia + '-f' + fac.id;
                                html += '<div class="trz-tree-node">' +
                                    '<a href="javascript:void(0)" class="trz-chip purple" ' +
                                        'style="cursor:pointer;" data-chain="' + cFac + '" ' +
                                        'onclick="genpdfFACDin(' + fac.id + ',0)" title="Factura N°' + (fac.nrodocto || fac.id) + '">' +
                                        '<i class="fa fa-file-pdf-o"></i> Fac N°' + (fac.nrodocto || fac.id) +
                                    '</a> <small style="color:#8a99aa;">' + fechaCorta(fac.fechahora) + '</small>' +
                                '</div>';
                                if (fac.ncnd && fac.ncnd.length) {
                                    html += '<div class="trz-tree">';
                                    fac.ncnd.forEach(function (n) {
                                        var esNC = n.foliocontrol_id == 5;
                                        html += '<div class="trz-tree-node">' +
                                            '<a href="javascript:void(0)" class="trz-chip gray" ' +
                                                'style="cursor:pointer;" ' +
                                                'onclick="genpdfFACDin(' + n.id + ',0)" title="' + (esNC ? 'NC' : 'ND') + ' N°' + (n.nrodocto || n.id) + '">' +
                                                '<i class="fa fa-file-pdf-o"></i> ' + (esNC ? 'NC' : 'ND') + ' ' + (n.nrodocto || n.id) +
                                            '</a> <small style="color:#8a99aa;">' + fechaCorta(n.fechahora) + '</small>' +
                                        '</div>';
                                    });
                                    html += '</div>';
                                }
                            });
                            html += '</div>';
                        }
                    });
                    html += '</div>';
                }
            });
            html += '</div>';
        }
    });
    return html;
}
