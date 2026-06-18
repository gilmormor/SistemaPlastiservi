/**
 * Gestión de Parámetros CC por etapa en areaproduccionsucetapaprod/editar
 * Modal AJAX — similar al modal de campos adicionales (campos.js)
 */

var _ccParamApsucId = null;  // apsucetapaprod_id activo en el modal

function abrirModalCcParams(apsucetapaprod_id, etapaNombre) {
    _ccParamApsucId = apsucetapaprod_id;
    $('#ccparam_apsucetapaprod_id').val(apsucetapaprod_id);
    $('#modalCcParamsEtapaNombre').text(etapaNombre);
    cancelarEdicionCcParam();
    cargarCcParams(apsucetapaprod_id);
    cargarCcParamsDisponibles(apsucetapaprod_id);
    $('#modalCcParamsEtapa').modal('show');
}

function cargarCcParams(apsucetapaprod_id) {
    $('#tablaCcParamsBody').html('<tr><td colspan="8" class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Cargando...</td></tr>');
    $.get('/ccparam_apsucetapaprod/' + apsucetapaprod_id + '/listar', function(resp) {
        var html = '';
        if (!resp.params || resp.params.length === 0) {
            html = '<tr><td colspan="8" class="text-center text-muted">Sin parámetros CC configurados para esta etapa.</td></tr>';
        } else {
            $.each(resp.params, function(i, p) {
                var req = p.requerido == 1 ? '<span class="label label-danger">Sí</span>' : '<span class="label label-default">No</span>';
                var min = (p.valor_min !== null && p.valor_min !== '') ? p.valor_min : '—';
                var max = (p.valor_max !== null && p.valor_max !== '') ? p.valor_max : '—';
                var tipoMap = {number: 'Numérico', text: 'Texto', boolean: 'Sí/No'};
                html += '<tr id="ccparam-row-' + p.id + '">' +
                    '<td>' + p.orden + '</td>' +
                    '<td><strong>' + p.etiqueta + '</strong><br><small class="text-muted">' + p.nombre + '</small></td>' +
                    '<td>' + (tipoMap[p.tipo] || p.tipo) + '</td>' +
                    '<td>' + (p.unidad || '—') + '</td>' +
                    '<td>' + min + '</td>' +
                    '<td>' + max + '</td>' +
                    '<td>' + req + '</td>' +
                    '<td>' +
                        '<a href="javascript:void(0)" onclick="editarCcParam(' + p.id + ',' + JSON.stringify(p) + ')" class="btn btn-xs btn-default" title="Editar"><i class="fa fa-pencil"></i></a> ' +
                        '<a href="javascript:void(0)" onclick="eliminarCcParam(' + p.id + ',' + apsucetapaprod_id + ')" class="btn btn-xs btn-danger" title="Eliminar"><i class="fa fa-trash"></i></a>' +
                    '</td>' +
                '</tr>';
            });
        }
        $('#tablaCcParamsBody').html(html);
    }).fail(function() {
        $('#tablaCcParamsBody').html('<tr><td colspan="8" class="text-center text-danger">Error al cargar parámetros.</td></tr>');
    });
}

function cargarCcParamsDisponibles(apsucetapaprod_id) {
    $.get('/ccparam_apsucetapaprod/' + apsucetapaprod_id + '/disponibles', function(resp) {
        var options = '<option value="">-- Seleccione --</option>';
        $.each(resp.params, function(i, p) {
            options += '<option value="' + p.id + '">' + p.etiqueta + (p.unidad ? ' (' + p.unidad + ')' : '') + '</option>';
        });
        $('#ccparam_ccparam_id').html(options);
    });
}

function editarCcParam(id, p) {
    $('#ccparam_ap_id_editar').val(id);
    $('#formCcParamTitulo').html('<i class="fa fa-pencil"></i> Editar parámetro CC');
    $('#ccparam_ccparam_id').val(p.ccparam_id).prop('disabled', true);
    $('#ccparam_valor_min').val(p.valor_min !== null ? p.valor_min : '');
    $('#ccparam_valor_max').val(p.valor_max !== null ? p.valor_max : '');
    $('#ccparam_orden').val(p.orden);
    $('#ccparam_requerido').prop('checked', p.requerido == 1);
}

function cancelarEdicionCcParam() {
    $('#ccparam_ap_id_editar').val('');
    $('#formCcParamTitulo').html('<i class="fa fa-plus"></i> Agregar parámetro CC');
    $('#ccparam_ccparam_id').val('').prop('disabled', false);
    $('#ccparam_valor_min').val('');
    $('#ccparam_valor_max').val('');
    $('#ccparam_orden').val(0);
    $('#ccparam_requerido').prop('checked', true);
}

function guardarCcParam() {
    var idEditar   = $('#ccparam_ap_id_editar').val();
    var apsucId    = $('#ccparam_apsucetapaprod_id').val();
    var ccparamId  = $('#ccparam_ccparam_id').val();
    var valorMin   = $('#ccparam_valor_min').val();
    var valorMax   = $('#ccparam_valor_max').val();
    var orden      = $('#ccparam_orden').val();
    var requerido  = $('#ccparam_requerido').is(':checked') ? 1 : 0;

    if (!idEditar && !ccparamId) {
        Biblioteca.notificaciones('Seleccione un parámetro CC.', 'Params CC', 'error');
        return;
    }

    // Fallback: algunos layouts no incluyen el meta csrf-token, se toma del input del form
    var csrfToken = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').first().val();
    var data   = { valor_min: valorMin || null, valor_max: valorMax || null, requerido: requerido, orden: orden, _token: csrfToken };
    var url    = '/ccparam_apsucetapaprod';
    var method = 'POST';

    if (idEditar) {
        url    = '/ccparam_apsucetapaprod/' + idEditar;
        method = 'PUT';
    } else {
        data.apsucetapaprod_id = apsucId;
        data.ccparam_id        = ccparamId;
    }

    $.ajax({
        url: url, method: method, data: data,
        success: function(resp) {
            if (resp.tipo_alert === 'success') {
                Biblioteca.notificaciones(resp.mensaje, 'Params CC', 'success');
                cancelarEdicionCcParam();
                cargarCcParams(apsucId);
                actualizarResumenCcParam(apsucId);
            } else {
                Biblioteca.notificaciones(resp.mensaje, 'Params CC', 'error');
            }
        },
        error: function() { Biblioteca.notificaciones('Error al guardar.', 'Params CC', 'error'); }
    });
}

function eliminarCcParam(id, apsucetapaprod_id) {
    if (!confirm('¿Eliminar este parámetro CC de la etapa?')) return;
    $.ajax({
        url: '/ccparam_apsucetapaprod/' + id,
        method: 'DELETE',
        data: { _token: $('meta[name=csrf-token]').attr('content') },
        success: function(resp) {
            if (resp.mensaje === 'ok') {
                Biblioteca.notificaciones('Parámetro CC eliminado.', 'Params CC', 'success');
                cargarCcParams(apsucetapaprod_id);
                actualizarResumenCcParam(apsucetapaprod_id);
            } else if (resp.mensaje === 'ne') {
                Biblioteca.notificaciones('No tiene permisos.', 'Params CC', 'error');
            } else {
                Biblioteca.notificaciones('Error al eliminar.', 'Params CC', 'error');
            }
        },
        error: function() { Biblioteca.notificaciones('Error al eliminar.', 'Params CC', 'error'); }
    });
}

// Actualiza el resumen de la fila en la tabla sin recargar la página
function actualizarResumenCcParam(apsucetapaprod_id) {
    $.get('/ccparam_apsucetapaprod/' + apsucetapaprod_id + '/listar', function(resp) {
        var $cel = $('#resumen-ccparams-' + apsucetapaprod_id);
        if (!resp.params || resp.params.length === 0) {
            $cel.html('<span class="text-muted">Sin parámetros CC</span>');
        } else {
            var etiquetas = resp.params.map(function(p){ return p.etiqueta; }).join(', ');
            $cel.html('<span class="badge" style="background:#f39c12;">' + resp.params.length + '</span> ' + etiquetas);
        }
    });
}
