/**
 * Listado de muestras CC — patrón estándar modulo: serverSide true + createdRow.
 */
$(document).ready(function () {

    var statusLabel = {
        1: '<span class="label" style="background:#00a65a;">Aprobado</span>',
        2: '<span class="label label-warning">Aprobado c/obs</span>',
        3: '<span class="label label-danger">Rechazado</span>',
        5: '<span class="label" style="background:#7f8c8d;color:#fff;">Sin parámetros</span>'
    };

    var tabla = $('#tabla-data-ccregistmuestra').DataTable({
        processing: true,
        serverSide: true,
        ajax: 'ccregistmuestrapage',
        order: [[0, 'desc']],
        columns: [
            { data: 'id' },                                    // eq(0)
            { data: 'fechahora' },                             // eq(1)
            { data: 'op_ot' },                                 // eq(2) OP / OT
            { defaultContent: '' },                            // eq(3) NV
            { defaultContent: '' },                            // eq(4) Reg. Prod.
            { defaultContent: '' },                            // eq(5) CodProd
            { data: 'producto_nombre' },                       // eq(6)
            { data: 'etapaprod_nombre' },                      // eq(7)
            { defaultContent: '' },                            // eq(8) Kg
            { defaultContent: '' },                            // eq(9) Status CC
            { defaultContent: '' },                            // eq(10) Lib.
            { defaultContent: '' },                            // eq(11) Desbloqueo
            { data: 'sta_env',    className: 'ocultar' },      // eq(12)
            { data: 'status',     className: 'ocultar' },      // eq(13)
            { data: 'updated_at', className: 'ocultar' },      // eq(14)
            { defaultContent: '' }                             // eq(15) Acción
        ],
        createdRow: function (row, data, index) {
            $(row).attr('id', 'fila' + data.id);

            // ID clickeable: abre reporte PDF de la muestra en modal
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
            $('td', row).eq(8).html(data.kgprod ? parseFloat(data.kgprod).toLocaleString('es-CL', {minimumFractionDigits: 2}) : '—');

            // Status CC — si está anulado se muestra etiqueta especial
            if (data.anulado == 1) {
                var titleAnul = 'Anulado por: ' + (data.usuario_anulacion || '—')
                              + ' | Fecha: ' + (data.fechahora_anulacion ? data.fechahora_anulacion.substring(0, 16) : '—')
                              + (data.motivo_anulacion ? ' | Motivo: ' + data.motivo_anulacion : '');
                $('td', row).eq(9).html('<span class="label label-default tooltipsC" title="' + titleAnul + '">Anulado</span>');
                $(row).css('opacity', '0.6');
            } else {
                $('td', row).eq(9).html(statusLabel[data.status] || '<span class="label label-default">—</span>');
            }

            // Lib. — refleja el estado de supervisión
            if (data.sta_env == 2) {
                $('td', row).eq(10).html('<i class="fa fa-check-circle text-success" title="Aprobada por supervisor"></i>');
            } else if (data.sta_env == 3) {
                $(row).css('background', '#fff5f5');
                var obsTitle = data.sta_env_obs ? data.sta_env_obs : '—';
                $('td', row).eq(10).html(
                    '<span class="label label-danger tooltipsC" title="Rechazada por supervisor: ' + obsTitle + '">'
                    + '<i class="fa fa-times-circle"></i> Rechazada</span>'
                );
            } else {
                $('td', row).eq(10).html('<i class="fa fa-clock-o text-muted" title="Pendiente de supervisión"></i>');
            }

            // Desbloqueo — obs supervisor si rechazada, o botón si aprobada+rechazada CC
            if (data.sta_env == 3 && data.sta_env_obs) {
                $('td', row).eq(11).html(
                    '<small class="text-danger tooltipsC" title="' + data.sta_env_obs + '">'
                    + '<i class="fa fa-comment"></i> ' + data.sta_env_obs.substring(0, 40) + (data.sta_env_obs.length > 40 ? '…' : '')
                    + '</small>'
                );
            } else if (data.anulado != 1 && data.status == 3 && data.sta_env == 2) {
                if (data.desbloqueado == 1) {
                    var titleDesb = 'Por: ' + (data.desbloqueo_usuario || '—')
                                  + ' | ' + (data.desbloqueo_fecha ? data.desbloqueo_fecha.substring(0, 16) : '—')
                                  + (data.desbloqueo_obs ? ' | ' + data.desbloqueo_obs : '');
                    $('td', row).eq(11).html('<span class="label label-warning tooltipsC" title="' + titleDesb + '">'
                        + '<i class="fa fa-unlock"></i> Desbloqueado</span>');
                } else {
                    $('td', row).eq(11).html(
                        '<button class="btn btn-xs btn-danger btn-desbloquear" data-id="' + data.id + '" title="Desbloquear muestra rechazada">'
                        + '<i class="fa fa-unlock-alt"></i> Desbloquear</button>'
                    );
                }
            }

            // Botón Ver siempre disponible
            var btns = '<a href="/ccregistmuestra/' + data.id + '/ver" class="btn btn-xs btn-default" title="Ver detalle">'
                     + '<i class="fa fa-eye"></i></a> ';

            // Botones Aprobar / Editar / Anular si sta_env=0 (pendiente) o sta_env=3 (rechazada por supervisor)
            if (data.anulado != 1 && (data.sta_env == 0 || data.sta_env == 3)) {
                var updatedTs = data.updated_at
                    ? Math.floor(new Date(data.updated_at.replace(' ', 'T')).getTime() / 1000)
                    : 0;
                // data-updated_at: timestamp unix para validación optimistic locking en el servidor
                btns += '<button class="btn btn-xs btn-success btn-aprobar" data-id="' + data.id + '" data-updated_at="' + updatedTs + '" title="Enviar al supervisor">'
                      + '<i class="fa fa-paper-plane"></i></button> ';
                btns += '<a href="/ccregistmuestra/' + data.id + '/editar?updated_at=' + updatedTs + '" class="btn btn-xs btn-warning" title="Editar muestra">'
                      + '<i class="fa fa-pencil"></i></a> ';
                btns += '<button class="btn btn-xs btn-danger btn-anular" data-id="' + data.id + '" title="Anular muestra">'
                      + '<i class="fa fa-ban"></i></button>';
            }

            $('td', row).eq(15).html(btns);
            $('td', row).eq(15).attr('class', 'action-buttons');
        },
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
    });

    // Aprobar via AJAX con SweetAlert (estándar del sistema)
    $(document).on('click', '.btn-aprobar', function () {
        var id         = $(this).data('id');
        var updatedAt  = $(this).data('updated_at'); // optimistic locking
        var csrf       = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').first().val();
        swal({
            title: '¿Enviar muestra CC #' + id + ' al supervisor?',
            text: 'La muestra pasará a la cola de revisión del supervisor.',
            icon: 'warning',
            buttons: { cancel: 'Cancelar', confirm: 'Aceptar' }
        }).then(function (value) {
            if (value) {
                $.post('/ccregistmuestra/' + id + '/aprobar', { _token: csrf, updated_at: updatedAt }, function (resp) {
                    if (resp.ok) {
                        Biblioteca.notificaciones(resp.msg, 'CC Muestras', 'success');
                        tabla.ajax.reload(null, false);
                    } else {
                        Biblioteca.notificaciones(resp.msg, 'CC Muestras', 'error');
                    }
                }).fail(function () {
                    Biblioteca.notificaciones('Error al aprobar la muestra.', 'CC Muestras', 'error');
                });
            }
        });
    });

    // Abrir modal Desbloquear muestra rechazada
    $(document).on('click', '.btn-desbloquear', function () {
        var id = $(this).data('id');
        $('#desbloquear-id-label').text(id);
        $('#observacion-desbloqueo').val('');
        $('#btn-confirmar-desbloqueo').data('id', id);
        $('#modal-desbloquear-muestra').modal('show');
    });

    // Confirmar desbloqueo via AJAX
    $('#btn-confirmar-desbloqueo').on('click', function () {
        var id  = $(this).data('id');
        var obs = $('#observacion-desbloqueo').val().trim();
        if (!obs) {
            Biblioteca.notificaciones('Debe ingresar la observación del desbloqueo.', 'CC Muestras', 'error');
            return;
        }
        var csrf = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').first().val();
        $.post('/ccregistmuestra/' + id + '/desbloquear', { _token: csrf, observacion: obs }, function (resp) {
            $('#modal-desbloquear-muestra').modal('hide');
            if (resp.ok) {
                Biblioteca.notificaciones(resp.msg, 'CC Muestras', 'success');
                tabla.ajax.reload(null, false);
            } else {
                Biblioteca.notificaciones(resp.msg, 'CC Muestras', 'error');
            }
        }).fail(function () {
            $('#modal-desbloquear-muestra').modal('hide');
            Biblioteca.notificaciones('Error al desbloquear la muestra.', 'CC Muestras', 'error');
        });
    });

    // Abrir modal Anular
    $(document).on('click', '.btn-anular', function () {
        var id = $(this).data('id');
        $('#anular-id-label').text(id);
        $('#motivo-anulacion').val('');
        $('#form-anular-muestra').attr('action', '/ccregistmuestra/' + id + '/anular');
        $('#modal-anular-muestra').modal('show');
    });

    // Submit del modal Anular — AJAX para mantener en la misma página
    $('#form-anular-muestra').on('submit', function (e) {
        e.preventDefault();
        var motivo = $('#motivo-anulacion').val().trim();
        if (!motivo) {
            Biblioteca.notificaciones('Debe ingresar el motivo de anulación.', 'CC Muestras', 'error');
            return;
        }
        var csrf   = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').first().val();
        var action = $(this).attr('action');
        $.post(action, { _token: csrf, motivo: motivo }, function (resp) {
            $('#modal-anular-muestra').modal('hide');
            if (resp.ok) {
                Biblioteca.notificaciones(resp.msg, 'CC Muestras', 'success');
                tabla.ajax.reload(null, false);
            } else {
                Biblioteca.notificaciones(resp.msg, 'CC Muestras', 'error');
            }
        }).fail(function () {
            $('#modal-anular-muestra').modal('hide');
            Biblioteca.notificaciones('Error al anular la muestra.', 'CC Muestras', 'error');
        });
    });
});
