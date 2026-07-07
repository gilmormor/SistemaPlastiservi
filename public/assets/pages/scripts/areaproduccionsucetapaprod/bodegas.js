/**
 * Gestión de bodegas de inventario por etapa de producción.
 * Permite asignar qué bodegas tiene disponible cada apsucetapaprod
 * para generar movimientos de inventario al aprobar un registro de producción.
 */

function abrirModalBodegas(apsucetapaprod_id, etapaNombre) {
    $('#bodega_apsucetapaprod_id').val(apsucetapaprod_id);
    $('#modalBodegasEtapaNombre').text(etapaNombre);
    $('#tablaBodegasBody').html('<tr><td colspan="2" class="text-center"><i class="fa fa-spinner fa-spin"></i> Cargando...</td></tr>');
    $('#bodega_invbodega_id').html('<option value="">-- Cargando... --</option>');

    $.get('/areaproduccionsucetapaprod/' + apsucetapaprod_id + '/bodegas', function(resp) {
        renderizarTablaBodag(resp.asignadas);
        renderizarSelectBodegas(resp.disponibles);
    }).fail(function() {
        alertify.error('Error al cargar bodegas.');
    });

    $('#modalBodegasEtapa').modal('show');
}

function renderizarTablaBodag(asignadas) {
    if (!asignadas || asignadas.length === 0) {
        $('#tablaBodegasBody').html(
            '<tr><td colspan="2" class="text-center text-muted">Sin bodegas asignadas</td></tr>'
        );
        return;
    }
    var html = '';
    asignadas.forEach(function(b) {
        html += '<tr id="fila-bodega-' + b.id + '">' +
            '<td>' + b.bodega_nombre + '</td>' +
            '<td style="text-align:center;">' +
                '<a onclick="eliminarBodegaEtapa(' + b.id + ')" style="cursor:pointer;" title="Quitar bodega">' +
                    '<i class="fa fa-times text-danger"></i>' +
                '</a>' +
            '</td>' +
        '</tr>';
    });
    $('#tablaBodegasBody').html(html);
}

function renderizarSelectBodegas(disponibles) {
    if (!disponibles || disponibles.length === 0) {
        $('#bodega_invbodega_id').html('<option value="">-- Sin bodegas disponibles --</option>');
        return;
    }
    var options = '<option value="">-- Seleccione --</option>';
    disponibles.forEach(function(b) {
        options += '<option value="' + b.id + '">' + b.nombre + '</option>';
    });
    $('#bodega_invbodega_id').html(options);
}

function guardarBodegaEtapa() {
    var apsucetapaprod_id = $('#bodega_apsucetapaprod_id').val();
    var invbodega_id      = $('#bodega_invbodega_id').val();
    if (!invbodega_id) {
        alertify.error('Seleccione una bodega.');
        return;
    }

    $.ajax({
        url:  '/areaproduccionsucetapaprod/' + apsucetapaprod_id + '/bodegas',
        type: 'POST',
        data: {
            invbodega_id: invbodega_id,
            _token: $('input[name=_token]').val()
        },
        success: function(resp) {
            if (resp.resp == 1) {
                alertify.success(resp.mensaje);
                // Recargar el modal con datos actualizados
                abrirModalBodegas(
                    apsucetapaprod_id,
                    $('#modalBodegasEtapaNombre').text()
                );
                // Actualizar el resumen en la tabla principal
                actualizarResumenBodegas(apsucetapaprod_id);
            } else {
                alertify.error(resp.mensaje);
            }
        },
        error: function() {
            alertify.error('Error al guardar bodega.');
        }
    });
}

function eliminarBodegaEtapa(id) {
    var apsucetapaprod_id = $('#bodega_apsucetapaprod_id').val();
    if (!confirm('¿Quitar esta bodega de la etapa?')) return;

    $.ajax({
        url:  '/areaproduccionsucetapaprod/bodega/' + id,
        type: 'POST',
        data: {
            _token:  $('input[name=_token]').val(),
            _method: 'DELETE'
        },
        success: function(resp) {
            if (resp.resp == 1) {
                alertify.success(resp.mensaje);
                abrirModalBodegas(
                    apsucetapaprod_id,
                    $('#modalBodegasEtapaNombre').text()
                );
                actualizarResumenBodegas(apsucetapaprod_id);
            } else {
                alertify.error(resp.mensaje);
            }
        },
        error: function() {
            alertify.error('Error al eliminar bodega.');
        }
    });
}

function actualizarResumenBodegas(apsucetapaprod_id) {
    $.get('/areaproduccionsucetapaprod/' + apsucetapaprod_id + '/bodegas', function(resp) {
        var cel = $('#resumen-bodegas-' + apsucetapaprod_id);
        if (!resp.asignadas || resp.asignadas.length === 0) {
            cel.html('<span class="text-muted">Sin bodegas (no genera movimiento de inv.)</span>');
        } else {
            var nombres = resp.asignadas.map(function(b){ return b.bodega_nombre; }).join(', ');
            cel.html(
                '<span class="badge" style="background:#3c8dbc;">' + resp.asignadas.length + '</span> ' + nombres
            );
        }
    });
}
