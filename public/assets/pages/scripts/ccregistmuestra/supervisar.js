/**
 * Supervisión de muestras CC — pantalla exclusiva del supervisor.
 * Solo muestra registros con sta_env=1 (enviados por el operario).
 * Patrón: igual a inventsalaprobar/index.js.
 */
$(document).ready(function () {

    var statusLabel = {
        1: '<span class="label" style="background:#00a65a;">Aprobado</span>',
        2: '<span class="label label-warning">Aprobado c/obs</span>',
        3: '<span class="label label-danger">Rechazado</span>'
    };

    var tabla = $('#tabla-data-ccsupervisar').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/ccmuestrasuperpage',
        order: [[0, 'asc']],
        columns: [
            { data: 'id' },                                  // eq(0)
            { data: 'fechahora' },                           // eq(1)
            { data: 'op_ot' },                               // eq(2)
            { defaultContent: '' },                          // eq(3) NV
            { defaultContent: '' },                          // eq(4) Reg. Prod.
            { defaultContent: '' },                          // eq(5) CodProd
            { data: 'producto_nombre' },                     // eq(6)
            { data: 'etapaprod_nombre' },                    // eq(7)
            { defaultContent: '' },                          // eq(8) Kg
            { defaultContent: '' },                          // eq(9) Status CC
            { data: 'usuario_nombre' },                      // eq(10)
            { data: 'status',     className: 'ocultar' },    // eq(11)
            { data: 'updated_at', className: 'ocultar' },    // eq(12)
            { defaultContent: '' }                           // eq(13) Acción
        ],
        createdRow: function (row, data, index) {
            $(row).attr('id', 'fila' + data.id);

            // ID clickeable — abre PDF de la muestra
            $('td', row).eq(0).html(
                '<a href="javascript:void(0);" onclick="genpdfCC(' + data.id + ')" class="tooltipsC" title="Ver PDF muestra #' + data.id + '" style="text-decoration:underline;cursor:pointer;">'
                + data.id + '</a>'
            );

            // OP / OT
            $('td', row).eq(2).html('OP ' + data.op_id + ' / OT ' + data.ot_id);

            // NV — clickeable si existe, guión si la OT no tiene NV asociada
            if (data.notaventa_id) {
                $('td', row).eq(3).html(
                    '<a href="javascript:void(0);" onclick="genpdfNV(' + data.notaventa_id + ',1)" class="tooltipsC" title="Ver NV #' + data.notaventa_id + '" style="text-decoration:underline;cursor:pointer;">'
                    + data.notaventa_id + '</a>'
                );
            } else {
                $('td', row).eq(3).html('<span class="text-muted">—</span>');
            }

            // Reg. Prod. — clickeable abre etiqueta de registro de producción
            $('td', row).eq(4).html(
                '<a href="javascript:void(0);" onclick="verEtiquetaEtapaConPermiso(' + data.opdetregprod_id + ',\'ver-etiqueta-regprod\')" class="tooltipsC" title="Ver etiqueta Reg. Prod. #' + data.opdetregprod_id + '" style="text-decoration:underline;cursor:pointer;">'
                + data.opdetregprod_id + '</a>'
            );

            // CodProd — con acuerdo técnico si existe, igual que reportproducto
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
            $('td', row).eq(8).html(data.kgprod
                ? parseFloat(data.kgprod).toLocaleString('es-CL', { minimumFractionDigits: 2 })
                : '—');

            // Status CC (resultado de calidad)
            $('td', row).eq(9).html(statusLabel[data.status] || '<span class="label label-default">—</span>');

            // Botón acción supervisor — data-updated_at para optimistic locking
            var updatedTs = data.updated_at
                ? Math.floor(new Date(data.updated_at.replace(' ', 'T')).getTime() / 1000)
                : 0;
            $('td', row).eq(13).html(
                '<button class="btn btn-xs btn-warning btn-cc-supervisar" data-id="' + data.id + '" data-updated_at="' + updatedTs + '" title="Aprobar o Rechazar">'
                + '<i class="fa fa-check-square-o"></i> Revisar</button>'
            );
        },
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
    });

    // Abrir modal de revisión — capturar updated_at para optimistic locking
    $(document).on('click', '.btn-cc-supervisar', function () {
        var id        = $(this).data('id');
        var updatedAt = $(this).data('updated_at');
        $('#cc-sup-id-label').text(id);
        $('#cc-sup-obs').val('');
        $('#btn-cc-sup-aprobar').data('id', id).data('updated_at', updatedAt);
        $('#btn-cc-sup-rechazar').data('id', id).data('updated_at', updatedAt);
        $('#modal-cc-supervisar').modal('show');
    });

    // Aprobar
    $('#btn-cc-sup-aprobar').on('click', function () {
        var id        = $(this).data('id');
        var updatedAt = $(this).data('updated_at');
        swal({
            title: '¿Aprobar muestra CC #' + id + '?',
            text: 'La muestra quedará activa para el módulo de despacho.',
            icon: 'warning',
            buttons: { cancel: 'Cancelar', confirm: 'Aprobar' }
        }).then(function (value) {
            if (value) {
                var obs  = $('#cc-sup-obs').val().trim();
                var csrf = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').first().val();
                $.post('/ccmuestrasuper/' + id + '/actuar',
                    { _token: csrf, accion: 'aprobar', obs: obs, updated_at: updatedAt },
                    function (resp) {
                        $('#modal-cc-supervisar').modal('hide');
                        if (resp.ok) {
                            Biblioteca.notificaciones(resp.msg, 'CC Supervisión', 'success');
                            $('#fila' + id).remove();
                        } else {
                            Biblioteca.notificaciones(resp.msg, 'CC Supervisión', 'error');
                        }
                    }
                ).fail(function () {
                    $('#modal-cc-supervisar').modal('hide');
                    Biblioteca.notificaciones('Error al aprobar la muestra.', 'CC Supervisión', 'error');
                });
            }
        });
    });

    // Rechazar
    $('#btn-cc-sup-rechazar').on('click', function () {
        var id        = $(this).data('id');
        var updatedAt = $(this).data('updated_at');
        var obs       = $('#cc-sup-obs').val().trim();
        if (!obs) {
            Biblioteca.notificaciones('Debe ingresar la observación del rechazo.', 'CC Supervisión', 'error');
            return;
        }
        swal({
            title: '¿Rechazar muestra CC #' + id + '?',
            text: 'La muestra volverá al listado del operario con su observación.',
            icon: 'warning',
            buttons: { cancel: 'Cancelar', confirm: 'Rechazar' }
        }).then(function (value) {
            if (value) {
                var csrf = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').first().val();
                $.post('/ccmuestrasuper/' + id + '/actuar',
                    { _token: csrf, accion: 'rechazar', obs: obs, updated_at: updatedAt },
                    function (resp) {
                        $('#modal-cc-supervisar').modal('hide');
                        if (resp.ok) {
                            Biblioteca.notificaciones(resp.msg, 'CC Supervisión', 'success');
                            $('#fila' + id).remove();
                        } else {
                            Biblioteca.notificaciones(resp.msg, 'CC Supervisión', 'error');
                        }
                    }
                ).fail(function () {
                    $('#modal-cc-supervisar').modal('hide');
                    Biblioteca.notificaciones('Error al rechazar la muestra.', 'CC Supervisión', 'error');
                });
            }
        });
    });
});
