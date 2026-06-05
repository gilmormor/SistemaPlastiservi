/**
 * campos.js — Gestión AJAX de campos adicionales por etapa de producción.
 * Se carga en areaproduccionsucetapaprod/editar.blade.php
 */

var _apsucetapaprod_id_actual = null;

/**
 * Abre el modal y carga los campos de la etapa indicada.
 */
function abrirModalCampos(apsucetapaprod_id) {
    _apsucetapaprod_id_actual = apsucetapaprod_id;
    $('#campo_apsucetapaprod_id').val(apsucetapaprod_id);
    cancelarEdicionCampo(); // limpiar formulario

    $('#tablaCamposBody').html('<tr><td colspan="10" class="text-center"><i class="fa fa-spinner fa-spin"></i> Cargando...</td></tr>');
    $('#modalCamposEtapa').modal('show');

    $.ajax({
        url: '/etapaprodcampo/' + apsucetapaprod_id + '/listar',
        type: 'GET',
        success: function (resp) {
            $('#modalCamposEtapaNombre').text(resp.etapa_nombre);
            renderizarTablaCampos(resp.campos);
        },
        error: function () {
            $('#tablaCamposBody').html('<tr><td colspan="10" class="text-danger text-center">Error al cargar los campos.</td></tr>');
        }
    });
}

/**
 * Renderiza las filas de la tabla de campos.
 */
function renderizarTablaCampos(campos) {
    if (!campos || campos.length === 0) {
        $('#tablaCamposBody').html('<tr><td colspan="10" class="text-center text-muted">Sin campos configurados. Agrega uno abajo.</td></tr>');
        return;
    }

    var html = '';
    $.each(campos, function (i, c) {
        var tipoLabel = { number: 'Número', text: 'Texto', calculated: 'Calculado' }[c.tipo] || c.tipo;
        html += '<tr id="fila-campo-' + c.id + '">' +
            '<td style="text-align:center;">' + c.orden + '</td>' +
            '<td>' + htmlEscape(c.etiqueta) + '</td>' +
            '<td><code>' + htmlEscape(c.nombre) + '</code></td>' +
            '<td>' + tipoLabel + '</td>' +
            '<td style="font-size:11px;">' + (c.formula ? htmlEscape(c.formula) : '—') + '</td>' +
            '<td>' + (c.unidad || '—') + '</td>' +
            '<td style="text-align:center;">' + c.decimales + '</td>' +
            '<td style="text-align:center;">' + (c.requerido ? '<i class="fa fa-check text-green"></i>' : '—') + '</td>' +
            '<td>' + (c.mapea_campo ? '<code>' + c.mapea_campo + '</code>' : '—') + '</td>' +
            '<td>' +
                '<button type="button" class="btn btn-xs btn-warning" onclick="editarCampo(' + JSON.stringify(c) + ')" title="Editar">' +
                    '<i class="fa fa-pencil"></i>' +
                '</button> ' +
                '<button type="button" class="btn btn-xs btn-danger" onclick="eliminarCampo(' + c.id + ')" title="Eliminar">' +
                    '<i class="fa fa-trash"></i>' +
                '</button>' +
            '</td>' +
        '</tr>';
    });
    $('#tablaCamposBody').html(html);
}

/**
 * Muestra u oculta el campo de fórmula según el tipo seleccionado.
 */
function toggleFormula() {
    if ($('#campo_tipo').val() === 'calculated') {
        $('#filaFormula').show();
    } else {
        $('#filaFormula').hide();
        $('#campo_formula').val('');
    }
}

/**
 * Carga los datos de un campo en el formulario para editar.
 */
function editarCampo(campo) {
    $('#campoid_editar').val(campo.id);
    $('#campo_orden').val(campo.orden);
    $('#campo_etiqueta').val(campo.etiqueta);
    $('#campo_nombre').val(campo.nombre).prop('disabled', true); // nombre no editable
    $('#campo_tipo').val(campo.tipo).trigger('change');
    $('#campo_formula').val(campo.formula || '');
    $('#campo_unidad').val(campo.unidad || '');
    $('#campo_decimales').val(campo.decimales);
    $('#campo_mapea_campo').val(campo.mapea_campo || '');
    $('#campo_requerido').prop('checked', campo.requerido == 1 || campo.requerido === true);
    $('#formCampoTitulo').html('<i class="fa fa-pencil"></i> Editando campo: <strong>' + htmlEscape(campo.etiqueta) + '</strong>');
    toggleFormula();

    // Scroll al formulario
    $('html, body').animate({ scrollTop: $('#formCampoTitulo').offset().top - 20 }, 300);
}

/**
 * Resetea el formulario a modo "Agregar".
 */
function cancelarEdicionCampo() {
    $('#campoid_editar').val('');
    $('#campo_orden').val('0');
    $('#campo_etiqueta').val('');
    $('#campo_nombre').val('').prop('disabled', false);
    $('#campo_tipo').val('number');
    $('#campo_formula').val('');
    $('#campo_unidad').val('');
    $('#campo_decimales').val('2');
    $('#campo_mapea_campo').val('');
    $('#campo_requerido').prop('checked', false);
    $('#formCampoTitulo').html('<i class="fa fa-plus"></i> Agregar campo');
    $('#filaFormula').hide();
}

/**
 * Guarda (crear o actualizar) un campo via AJAX.
 */
function guardarCampo() {
    var id       = $('#campoid_editar').val();
    var apsucId  = $('#campo_apsucetapaprod_id').val();
    var etiqueta = $.trim($('#campo_etiqueta').val());
    var nombre   = $.trim($('#campo_nombre').val());
    var tipo     = $('#campo_tipo').val();
    var formula  = $.trim($('#campo_formula').val());

    // Validaciones básicas
    if (!etiqueta) { Biblioteca.notificaciones('La etiqueta es obligatoria.', 'Campos', 'error'); return; }
    if (!id && !nombre) { Biblioteca.notificaciones('El nombre interno es obligatorio.', 'Campos', 'error'); return; }
    if (tipo === 'calculated' && !formula) { Biblioteca.notificaciones('Debe ingresar la fórmula para un campo calculado.', 'Campos', 'error'); return; }

    var data = {
        apsucetapaprod_id : apsucId,
        nombre            : nombre,
        etiqueta          : etiqueta,
        tipo              : tipo,
        formula           : formula,
        unidad            : $('#campo_unidad').val(),
        decimales         : $('#campo_decimales').val(),
        requerido         : $('#campo_requerido').is(':checked') ? 1 : 0,
        orden             : $('#campo_orden').val(),
        mapea_campo       : $('#campo_mapea_campo').val(),
        _token            : $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').first().val()
    };

    var url    = '/etapaprodcampo';
    var method = 'POST';
    if (id) {
        url    = '/etapaprodcampo/' + id;
        method = 'PUT';
        data._method = 'PUT';
    }

    $.ajax({
        url    : url,
        type   : 'POST', // siempre POST; PUT se simula con _method
        data   : data,
        success: function (resp) {
            if (resp.resp === 1) {
                Biblioteca.notificaciones(resp.mensaje, 'Campos', 'success');
                cancelarEdicionCampo();
                // Recargar tabla y resumen
                recargarCampos(apsucId);
            } else {
                Biblioteca.notificaciones(resp.mensaje, 'Campos', 'error');
            }
        },
        error: function () {
            Biblioteca.notificaciones('Error de conexión al guardar.', 'Campos', 'error');
        }
    });
}

/**
 * Elimina un campo previa confirmación.
 */
function eliminarCampo(id) {
    swal({
        title  : '¿Eliminar campo?',
        text   : 'Esta acción no se puede deshacer.',
        icon   : 'warning',
        buttons: { cancel: 'Cancelar', confirm: 'Eliminar' }
    }).then(function (value) {
        if (!value) return;
        $.ajax({
            url : '/etapaprodcampo/' + id,
            type: 'POST',
            data: {
                _method: 'DELETE',
                _token : $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').first().val()
            },
            success: function (resp) {
                if (resp.resp === 1) {
                    Biblioteca.notificaciones(resp.mensaje, 'Campos', 'success');
                    recargarCampos(_apsucetapaprod_id_actual);
                } else {
                    Biblioteca.notificaciones(resp.mensaje, 'Campos', 'error');
                }
            },
            error: function () {
                Biblioteca.notificaciones('Error de conexión al eliminar.', 'Campos', 'error');
            }
        });
    });
}

/**
 * Recarga la tabla del modal y el resumen en la fila de la etapa.
 */
function recargarCampos(apsucetapaprod_id) {
    $.ajax({
        url    : '/etapaprodcampo/' + apsucetapaprod_id + '/listar',
        type   : 'GET',
        success: function (resp) {
            renderizarTablaCampos(resp.campos);
            // Actualizar resumen en la tabla de la página
            var $resumen = $('#resumen-campos-' + apsucetapaprod_id);
            if (resp.campos.length === 0) {
                $resumen.html('<span class="text-muted">Sin campos</span>');
            } else {
                var etiquetas = $.map(resp.campos, function (c) { return c.etiqueta; }).join(', ');
                $resumen.html('<span class="badge" style="background:#00a65a;">' + resp.campos.length + '</span> ' + etiquetas);
            }
        }
    });
}

/**
 * Escapa HTML para evitar XSS en datos dinámicos.
 */
function htmlEscape(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
