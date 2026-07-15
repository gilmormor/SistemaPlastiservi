/**
 * Reporte Cobertura CC — lotes aprobados con/sin muestra CC registrada.
 */
$(document).ready(function () {

    // Inicializar date-pickers
    $('.date-picker').datepicker({
        language  : 'es',
        format    : 'dd/mm/yyyy',
        autoclose : true,
        todayHighlight: true
    });

    var tabla = null;

    function configurarTabla(url) {
        if ($.fn.dataTable.isDataTable('#tabla-cobertura')) {
            $('#tabla-cobertura').dataTable().fnDestroy();
        }

        tabla = $('#tabla-cobertura').DataTable({
            processing : true,
            serverSide : true,
            ajax       : url,
            order      : [[0, 'desc']],
            columns: [
                { data: 'id' },              // 0 Lote
                { defaultContent: '' },      // 1 Fecha aprobación
                { defaultContent: '' },      // 2 OP/OT
                { data: 'producto_nombre' }, // 3
                { data: 'etapaprod_nombre' },// 4
                { defaultContent: '' },      // 5 Kg
                { defaultContent: '' },      // 6 Operario
                { defaultContent: '' },      // 7 Máquina
                { defaultContent: '' },      // 8 N° Muestras
                { defaultContent: '' }       // 9 Cobertura
            ],
            createdRow: function (row, data) {
                $(row).attr('id', 'lote' + data.id);

                // Fila roja si sin cobertura
                if (!data.con_muestra) {
                    $(row).css('background-color', '#fff3f3');
                }

                // Lote — link a etiqueta de etapa
                $('td', row).eq(0).html(
                    '<a href="javascript:void(0)" onclick="verEtiquetaEtapaConPermiso(' + data.id + ',\'ver-etiqueta-regprod\')"'
                    + ' data-lote="' + data.id + '"'
                    + ' title="Ver etiqueta — Lote #' + data.id + '"'
                    + ' style="color:#2980b9;font-weight:600;">'
                    + '<i class="fa fa-tag"></i> Lote-' + data.id + '</a>'
                );

                // Fecha aprobación
                $('td', row).eq(1).html(data.aprobfechahora ? data.aprobfechahora.substring(0, 16) : '—');

                // OP / OT
                $('td', row).eq(2).html('OP ' + data.op_id + ' / OT ' + data.ot_id);

                // Kg
                $('td', row).eq(5).attr('style', 'text-align:right');
                $('td', row).eq(5).html(
                    data.kgprod
                        ? parseFloat(data.kgprod).toLocaleString('es-CL', { minimumFractionDigits: 2 })
                        : '—'
                );

                // Operario
                $('td', row).eq(6).html(data.operario_nombre || '<span class="text-muted">—</span>');

                // Máquina
                $('td', row).eq(7).html(data.maquina_nombre || '<span class="text-muted">—</span>');

                // N° Muestras
                $('td', row).eq(8).attr('style', 'text-align:center');
                $('td', row).eq(8).html(
                    data.total_muestras > 0
                        ? '<strong>' + data.total_muestras + '</strong>'
                        : '<span class="text-muted">0</span>'
                );

                // Cobertura
                $('td', row).eq(9).attr('style', 'text-align:center');
                if (data.con_muestra) {
                    $('td', row).eq(9).html(
                        '<span class="label" style="background:#00a65a;font-size:12px;">'
                        + '<i class="fa fa-check"></i> Con muestra</span>'
                    );
                } else {
                    $('td', row).eq(9).html(
                        '<span class="label label-danger" style="font-size:12px;">'
                        + '<i class="fa fa-exclamation-triangle"></i> Sin muestra</span>'
                    );
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
        configurarTabla('/reportcoberturacc/page' + qs);
        totalizarindex(qs);
        $('#div-totales').show();
    });

    function datosFiltros() {
        return '?fecha_desde='  + encodeURIComponent($('#fecha_desde').val())
             + '&fecha_hasta='  + encodeURIComponent($('#fecha_hasta').val())
             + '&etapaprod_id=' + $('#etapaprod_id').val()
             + '&operario_id='  + $('#operario_id').val()
             + '&maquina_id='   + $('#maquina_id').val()
             + '&cobertura='    + $('#cobertura').val();
    }

    function totalizarindex(qs) {
        $.ajax({
            url: '/reportcoberturacc/totalizarindex' + qs,
            type: 'GET',
            success: function (d) {
                $('#tot-total').text(d.total);
                $('#tot-con').text(d.conCobertura);
                $('#tot-sin').text(d.sinCobertura);
                $('#tot-pct').text(d.pct + '%');
            }
        });
    }

    // PDF en modal (mismo estándar que reportccmuestra)
    $('#btnpdf').on('click', function () {
        var qs = datosFiltros();
        $('#contpdf').attr('src', '/reportcoberturacc/exportPdf' + qs);
        $('#myModalpdf').modal('show');
    });

    // Excel (client-side)
    window.exportarExcel = function () {
        if (!tabla) {
            Biblioteca.notificaciones('Primero consulte los datos.', 'Cobertura CC', 'error');
            return;
        }

        var qs = datosFiltros();
        $.ajax({
            url : '/reportcoberturacc/page' + qs,
            type: 'GET',
            dataType: 'json',
            success: function (resp) {
                var rows = resp.data || [];
                if (rows.length === 0) {
                    Biblioteca.notificaciones('No hay datos para exportar.', 'Cobertura CC', 'error');
                    return;
                }

                var datos = [];
                datos.push(['Reporte Cobertura CC', '', '', '', '', '', '', '', '', fechaactual()]);
                datos.push([]);
                datos.push(['Lote', 'Fecha aprobación', 'OP', 'OT', 'Producto', 'Etapa', 'Kg', 'Operario', 'Máquina', 'N° Muestras', 'Cobertura']);

                rows.forEach(function (d) {
                    datos.push([
                        d.id,
                        d.aprobfechahora ? d.aprobfechahora.substring(0, 16) : '',
                        d.op_id,
                        d.ot_id,
                        d.producto_nombre,
                        d.etapaprod_nombre,
                        parseFloat(d.kgprod || 0),
                        d.operario_nombre || '',
                        d.maquina_nombre || '',
                        parseInt(d.total_muestras || 0),
                        d.con_muestra ? 'Con muestra' : 'Sin muestra'
                    ]);
                });

                var wb = new ExcelJS.Workbook();
                var ws = wb.addWorksheet('Cobertura CC');
                ws.addRows(datos);

                ws.getRow(1).getCell(1).font = { bold: true, size: 14 };
                var rowH = ws.getRow(3);
                for (var i = 1; i <= 11; i++) {
                    rowH.getCell(i).font = { bold: true };
                }
                ws.getColumn(7).eachCell({ includeEmpty: true }, function (cell) {
                    if (typeof cell.value === 'number') cell.numFmt = '#,##0.00';
                });

                ajustarcolumnaexcel(ws, 'E');
                ajustarcolumnaexcel(ws, 'F');
                ajustarcolumnaexcel(ws, 'H');

                wb.xlsx.writeBuffer().then(function (buffer) {
                    var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    var url  = window.URL.createObjectURL(blob);
                    var a    = document.createElement('a');
                    a.href     = url;
                    a.download = 'ReporteCC_Cobertura.xlsx';
                    a.click();
                    window.URL.revokeObjectURL(url);
                });
            },
            error: function () {
                Biblioteca.notificaciones('Error al obtener datos.', 'Cobertura CC', 'error');
            }
        });
    };
});
