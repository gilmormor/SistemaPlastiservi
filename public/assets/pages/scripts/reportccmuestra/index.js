/**
 * Reporte Muestras CC — filtros, DataTable, PDF y Excel.
 * Patrón: reportinvstock adaptado para ccregistmuestra.
 */
$(document).ready(function () {

    // Inicializar date-pickers
    $('.date-picker').datepicker({
        language  : 'es',
        format    : 'dd/mm/yyyy',
        autoclose : true,
        todayHighlight: true
    });

    var statusLabel = {
        1: '<span class="label" style="background:#00a65a;">Aprobado</span>',
        2: '<span class="label label-warning">Aprobado c/obs</span>',
        3: '<span class="label label-danger">Rechazado</span>'
    };

    var staEnvLabel = {
        0: '<span class="label label-default">Sin enviar</span>',
        1: '<span class="label label-info">Enviado</span>',
        2: '<span class="label label-success">Aprobado</span>',
        3: '<span class="label label-danger">Rechazado</span>'
    };

    var tabla = null;

    function configurarTabla(url) {
        if ($.fn.DataTable.isDataTable('#tabla-data-ccmuestra')) {
            // Solo destruir la instancia, no vaciar el DOM para preservar el <thead>
            $('#tabla-data-ccmuestra').DataTable().destroy();
        }

        tabla = $('#tabla-data-ccmuestra').DataTable({
            processing  : true,
            serverSide  : true,
            ajax        : url,
            order       : [[0, 'desc']],
            columns: [
                { data: 'id' },               // 0
                { data: 'fechahora' },         // 1
                { defaultContent: '' },        // 2 OP/OT
                { defaultContent: '' },        // 3 NV
                { defaultContent: '' },        // 4 Cliente
                { defaultContent: '' },        // 5 Cod
                { data: 'producto_nombre' },   // 6
                { data: 'etapaprod_nombre' },  // 7
                { defaultContent: '' },        // 8 Kg
                { defaultContent: '' },        // 9 Operario
                { defaultContent: '' },        // 10 Máquina
                { defaultContent: '' },        // 11 Status CC
                { defaultContent: '' },        // 12 sta_env
                { defaultContent: '' },        // 13 Anulado
                { defaultContent: '' }         // 14 Desbloqueado
            ],
            createdRow: function (row, data) {
                $(row).attr('id', 'fila' + data.id);

                // Fondo si está anulado
                if (data.anulado == 1) $(row).css('opacity', '0.6');

                // ID — link PDF muestra
                $('td', row).eq(0).html(
                    '<a href="javascript:void(0);" onclick="genpdfCC(' + data.id + ')" class="tooltipsC"'
                    + ' title="Ver PDF muestra #' + data.id + '" style="text-decoration:underline;cursor:pointer;">'
                    + data.id + '</a>'
                );

                // Fecha (solo dd/mm/aaaa hh:mm)
                $('td', row).eq(1).html(data.fechahora ? data.fechahora.substring(0, 16) : '—');

                // OP / OT
                $('td', row).eq(2).html('OP ' + data.op_id + ' / OT ' + data.ot_id);

                // NV
                if (data.notaventa_id) {
                    $('td', row).eq(3).html(
                        '<a href="javascript:void(0);" onclick="genpdfNV(' + data.notaventa_id + ',1)" class="tooltipsC"'
                        + ' title="Ver NV #' + data.notaventa_id + '" style="text-decoration:underline;cursor:pointer;">'
                        + data.notaventa_id + '</a>'
                    );
                } else {
                    $('td', row).eq(3).html('<span class="text-muted">—</span>');
                }

                // Cliente
                if (data.cliente_razonsocial) {
                    $('td', row).eq(4).html(
                        '<span class="tooltipsC" title="RUT: ' + (data.cliente_rut || '—') + '">'
                        + data.cliente_razonsocial + '</span>'
                    );
                } else {
                    $('td', row).eq(4).html('<span class="text-muted">—</span>');
                }

                // Cod Producto — con link a Acuerdo Técnico e imagen si corresponde
                $('td', row).eq(5).attr('style', 'text-align:center');
                if (data.acuerdotecnico_id != null) {
                    var codHtml = '<a class="btn-accion-tabla btn-sm tooltipsC" title="Acuerdo Técnico" onclick=\'genpdfAcuTec(' + data.acuerdotecnico_id + ',0,"")\'>'
                                + data.producto_id + '</a>';
                    if (data.at_impresofoto != '' && data.at_impresofoto != null) {
                        codHtml += '<a class="btn-accion-tabla btn-sm tooltipsC" title="Ver Imagen" onclick=\'verpdf2("at/' + data.at_impresofoto + '",2,"","ver-arte-acuerdo-tecnico")\'>'
                                 + '<i class="fa fa-fw fa-photo"></i></a>';
                    }
                    $('td', row).eq(5).html(codHtml);
                } else {
                    $('td', row).eq(5).html('<span class="text-muted">' + data.producto_id + '</span>');
                }

                // Kg
                $('td', row).eq(8).attr('style', 'text-align:right');
                $('td', row).eq(8).html(
                    data.kgprod
                        ? parseFloat(data.kgprod).toLocaleString('es-CL', { minimumFractionDigits: 2 })
                        : '—'
                );

                // Operario
                $('td', row).eq(9).html(data.operario_nombre || '<span class="text-muted">—</span>');

                // Máquina
                $('td', row).eq(10).html(data.maquina_nombre || '<span class="text-muted">—</span>');

                // Status CC
                if (data.anulado == 1) {
                    var titleAnul = 'Anulado por: ' + (data.anulacion_usuario || '—')
                                  + ' | Fecha: ' + (data.anulacion_fecha ? data.anulacion_fecha.substring(0, 16) : '—')
                                  + (data.anulacion_motivo ? ' | ' + data.anulacion_motivo : '');
                    $('td', row).eq(11).html('<span class="label label-default tooltipsC" title="' + titleAnul + '">Anulado</span>');
                } else {
                    $('td', row).eq(11).html(statusLabel[data.status] || '<span class="label label-default">—</span>');
                }

                // sta_env
                var staNombre = staEnvLabel[data.sta_env !== null ? data.sta_env : 0] || staEnvLabel[0];
                if (data.sta_env == 3 && data.sta_env_obs) {
                    staNombre = '<span class="label label-danger tooltipsC" title="' + data.sta_env_obs + '">Rechazado</span>';
                }
                $('td', row).eq(12).html(staNombre);

                // Anulado
                if (data.anulado == 1) {
                    var tAnul = (data.anulacion_usuario || '—') + ' — '
                              + (data.anulacion_fecha ? data.anulacion_fecha.substring(0, 16) : '—')
                              + (data.anulacion_motivo ? ': ' + data.anulacion_motivo : '');
                    $('td', row).eq(13).html(
                        '<span class="label label-default tooltipsC" title="' + tAnul + '">'
                        + '<i class="fa fa-ban"></i> Sí</span>'
                    );
                } else {
                    $('td', row).eq(13).html('<span class="text-muted">—</span>');
                }

                // Desbloqueado
                if (data.desbloqueado == 1) {
                    var tDesb = (data.desbloqueo_usuario || '—') + ' — '
                              + (data.desbloqueo_fecha ? data.desbloqueo_fecha.substring(0, 16) : '—')
                              + (data.desbloqueo_obs ? ': ' + data.desbloqueo_obs : '');
                    $('td', row).eq(14).html(
                        '<span class="label tooltipsC" style="background:#00c0ef;" title="' + tDesb + '">'
                        + '<i class="fa fa-unlock"></i> Sí</span>'
                    );
                } else {
                    $('td', row).eq(14).html('<span class="text-muted">—</span>');
                }
            },
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json'
            }
        });
    }

    // Botón Consultar
    $('#btnconsultar').on('click', function () {
        var qs = datosFiltros();
        configurarTabla('reportccmuestrapage' + qs);
        totalizarindex(qs);
        $('#div-totales').show();
    });

    // PDF
    $('#btnpdf').on('click', function () {
        var qs = datosFiltros();
        $('#contpdf').attr('src', '/reportccmuestra/exportPdf' + qs);
        $('#myModalpdf').modal('show');
    });

    // Botón buscar cliente — abre modal
    $('#btnbuscarcliente').on('click', function () {
        $('#myModalBusqueda').modal('show');
    });

    // Botón buscar producto — igual patrón que reportinvmov: recarga URL y abre modal
    $('#btnbuscarproducto').on('click', function () {
        $(".input-sm").val('');
        var aux_id = $('#producto_idPxP').val();
        // Recarga la tabla con todos los productos (sin filtro de cliente/sucursal)
        $('#tabla-data-productos').DataTable().ajax.url('producto/productobuscarpage/?cliente_id=&sucursal_id=&producto_id=').load();

        if (aux_id == null || aux_id.length === 0 || /^\s+$/.test(aux_id)) {
            $('#divprodselec').hide();
            $('#productos').html('');
        } else {
            var arr = aux_id.split(',');
            $('#productos').html('');
            for (var i = 0; i < arr.length; i++) {
                $('#productos').append("<option value='" + arr[i] + "' selected>" + arr[i] + "</option>");
            }
            $('#divprodselec').show();
        }
        $('#myModalBuscarProd').modal('show');
    });

    // Validación RUT: solo números y K
    $('#rut1').on('input', function () {
        var rut = $(this).val().replace(/[^0-9kK]/g, '');
        var idxK = rut.toLowerCase().indexOf('k');
        if (idxK !== -1 && idxK !== rut.length - 1) rut = rut.replace(/k/gi, '');
        if (rut.toLowerCase().includes('k')) rut = rut.slice(0, rut.toLowerCase().indexOf('k') + 1);
        $(this).val(rut);
    });

    // Al salir del campo: agregar guion y validar
    $('#rut1').on('blur', function () {
        var rut = $(this).val().trim();
        if (rut === '') { $('#error-message').hide(); return; }
        if (!rut.includes('-') && rut.length >= 2) {
            rut = rut.slice(0, -1) + '-' + rut.slice(-1);
            $(this).val(rut);
        }
        if (validarRUT(rut)) { $('#error-message').hide(); }
        else                  { $('#error-message').show(); }
    });

    // Al entrar al campo: quitar guion para editar
    $('#rut1').on('focus', function () {
        var rut = $(this).val().trim();
        if (rut.includes('-')) $(this).val(rut.replace('-', ''));
    });

    function datosFiltros() {
        // Enviar RUT sin guion para que el servidor compare contra ambos formatos
        var rutVal = $('#rut1').val().trim().replace('-', '');
        return '?fecha_desde='   + encodeURIComponent($('#fecha_desde').val())
             + '&fecha_hasta='   + encodeURIComponent($('#fecha_hasta').val())
             + '&sucursal_id='   + $('#sucursal_id').val()
             + '&etapaprod_id='  + $('#etapaprod_id').val()
             + '&operario_id='   + $('#operario_id').val()
             + '&maquina_id='    + $('#maquina_id').val()
             + '&cliente_rut='      + encodeURIComponent(rutVal)
             + '&producto_idPxP='  + encodeURIComponent($('#producto_idPxP').val())
             + '&status='          + $('#status').val()
             + '&sta_env='       + $('#sta_env').val()
             + '&anulado='       + $('#anulado').val()
             + '&desbloqueado='  + $('#desbloqueado').val();
    }

    function totalizarindex(qs) {
        $.ajax({
            url: '/reportccmuestra/totalizarindex' + qs,
            type: 'GET',
            success: function (d) {
                $('#tot-total').text(d.total);
                $('#tot-aprobados').text(d.aprobados);
                $('#tot-conobs').text(d.conObs);
                $('#tot-rechazados').text(d.rechazados);
                $('#tot-anulados').text(d.anulados);
                $('#tot-desbloqueados').text(d.desbloqueados);
            }
        });
    }

    // Excel (client-side con ExcelJS)
    window.exportarExcel = function () {
        var qs = datosFiltros();
        $.ajax({
            url : 'reportccmuestrapage' + qs,
            type: 'GET',
            dataType: 'json',
            success: function (resp) {
                var rows = resp.data || [];
                if (rows.length === 0) {
                    Biblioteca.notificaciones('No hay datos para exportar.', 'Reporte CC', 'error');
                    return;
                }

                var statusTexto = { 1: 'Aprobado', 2: 'Aprobado c/obs', 3: 'Rechazado' };
                var staEnvTexto = { 0: 'Sin enviar', 1: 'Enviado', 2: 'Aprobado sup.', 3: 'Rechazado sup.' };

                var datosExcel = [];
                datosExcel.push(['Reporte Muestras CC', '', '', '', '', '', '', '', '', '', '', '', '', '', fechaactual()]);
                datosExcel.push(['', '', '', '', '', '', '', '', '', '', '', '', '', '', '']);
                datosExcel.push([
                    'ID', 'Fecha', 'OP', 'OT', 'NV', 'RUT Cliente', 'Cliente',
                    'Cod', 'Producto', 'Etapa', 'Kg', 'Operario', 'Máquina',
                    'Status CC', 'Estado envío', 'Anulado', 'Motivo anulación',
                    'Desbloqueado', 'Obs desbloqueo'
                ]);

                rows.forEach(function (d) {
                    datosExcel.push([
                        d.id,
                        d.fechahora ? d.fechahora.substring(0, 16) : '',
                        d.op_id,
                        d.ot_id,
                        d.notaventa_id || '',
                        d.cliente_rut || '',
                        d.cliente_razonsocial || '',
                        d.producto_id,
                        d.producto_nombre,
                        d.etapaprod_nombre,
                        parseFloat(d.kgprod || 0),
                        d.operario_nombre || '',
                        d.maquina_nombre || '',
                        statusTexto[d.status] || '',
                        staEnvTexto[d.sta_env !== null ? d.sta_env : 0] || '',
                        d.anulado == 1 ? 'Sí' : 'No',
                        d.anulacion_motivo || '',
                        d.desbloqueado == 1 ? 'Sí' : 'No',
                        d.desbloqueo_obs || ''
                    ]);
                });

                var wb = new ExcelJS.Workbook();
                var ws = wb.addWorksheet('Muestras CC');
                ws.addRows(datosExcel);

                // Negrita fila 1 y fila 3 (cabeceras)
                ws.getRow(1).getCell(1).font = { bold: true, size: 14 };
                var rowH = ws.getRow(3);
                for (var i = 1; i <= 18; i++) {
                    rowH.getCell(i).font = { bold: true };
                }

                // Formato número columna Kg (col 11)
                ws.getColumn(11).eachCell({ includeEmpty: true }, function (cell) {
                    if (typeof cell.value === 'number') cell.numFmt = '#,##0.00';
                });

                ajustarcolumnaexcel(ws, 'G');
                ajustarcolumnaexcel(ws, 'I');
                ajustarcolumnaexcel(ws, 'J');
                ajustarcolumnaexcel(ws, 'L');

                wb.xlsx.writeBuffer().then(function (buffer) {
                    var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    var url  = window.URL.createObjectURL(blob);
                    var a    = document.createElement('a');
                    a.href     = url;
                    a.download = 'ReporteCC_Muestras.xlsx';
                    a.click();
                    window.URL.revokeObjectURL(url);
                });
            },
            error: function () {
                Biblioteca.notificaciones('Error al obtener datos para Excel.', 'Reporte CC', 'error');
            }
        });
    };
});

// Cierra el modal de cliente y copia el RUT al campo #rut1
function copiar_rut(id, rut) {
    $('#myModalBusqueda').modal('hide');
    $('#rut1').val(rut);
    $('#rut1').trigger('blur');
}

// Cierra el modal de producto y agrega el ID al campo #producto_idPxP (acepta múltiples separados por coma)
function copiar_codprod(id, codintprod) {
    $('#myModalBuscarProd').modal('hide');
    var aux_id = $('#producto_idPxP').val();
    if (aux_id == null || aux_id.length === 0 || /^\s+$/.test(aux_id)) {
        $('#producto_idPxP').val(id);
    } else {
        $('#producto_idPxP').val(aux_id + ',' + id);
    }
    $('#producto_idPxP').focus();
}

function validarRUT(rut) {
    var regex = /^(\d{1,8})-([\dkK])$/;
    if (!regex.test(rut)) return false;
    var partes = rut.split('-');
    return partes[1].toUpperCase() === calcularDigitoVerificador(partes[0]);
}

function calcularDigitoVerificador(rut) {
    var suma = 0, mult = 2;
    for (var i = rut.length - 1; i >= 0; i--) {
        suma += parseInt(rut.charAt(i)) * mult;
        mult = mult === 7 ? 2 : mult + 1;
    }
    var digito = 11 - (suma % 11);
    if (digito === 10) return 'K';
    if (digito === 11) return '0';
    return digito.toString();
}
