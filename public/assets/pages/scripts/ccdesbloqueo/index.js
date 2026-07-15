/**
 * Desbloqueo de muestras CC rechazadas.
 * R5: tabla vacía al cargar; se consulta con filtros al presionar "Consultar".
 */
$(document).ready(function () {

    // Inicializar date-pickers
    $('.date-picker').datepicker({
        language      : 'es',
        format        : 'dd/mm/yyyy',
        autoclose     : true,
        todayHighlight: true
    });

    var tabla = null;

    // Recolecta todos los valores de los filtros como query string
    function datosFiltros() {
        return '?rut='              + encodeURIComponent($('#rut1').val())
             + '&opdetregprod_id='  + encodeURIComponent($('#opdetregprod_id').val())
             + '&fecha_desde='      + encodeURIComponent($('#fecha_desde').val())
             + '&fecha_hasta='      + encodeURIComponent($('#fecha_hasta').val())
             + '&ot_id='            + encodeURIComponent($('#ot_id').val())
             + '&op_id='            + encodeURIComponent($('#op_id').val())
             + '&maquina_id='       + $('#maquina_id').val()
             + '&notaventa_id='     + encodeURIComponent($('#notaventa_id').val())
             + '&producto_idPxP='   + encodeURIComponent($('#producto_idPxP').val())
             + '&etapaprod_id='     + $('#etapaprod_id').val()
             + '&sta_desbloqueo='   + $('#sta_desbloqueo').val();
    }

    function configurarTabla(url) {
        // Destruir DataTable anterior si existe
        if ($.fn.dataTable.isDataTable('#tabla-data-ccdesbloqueo')) {
            $('#tabla-data-ccdesbloqueo').dataTable().fnDestroy();
        }

        tabla = $('#tabla-data-ccdesbloqueo').DataTable({
            processing: true,
            serverSide: true,
            ajax: url,
            order: [[0, 'desc']],
            columns: [
                { data: 'id' },               // eq(0)  ID
                { data: 'fechahora' },         // eq(1)  Fecha
                { data: 'op_ot' },             // eq(2)  OP/OT
                { defaultContent: '' },        // eq(3)  NV
                { defaultContent: '' },        // eq(4)  Reg. Prod.
                { defaultContent: '' },        // eq(5)  CodProd
                { data: 'producto_nombre' },   // eq(6)  Producto
                { data: 'etapaprod_nombre' },  // eq(7)  Etapa
                { defaultContent: '' },        // eq(8)  Kg
                { defaultContent: '' },        // eq(9)  Observación muestra
                { defaultContent: '' },        // eq(10) Desbloqueo
                { defaultContent: '' }         // eq(11) Acción
            ],
            createdRow: function (row, data) {
                $(row).attr('id', 'fila' + data.id);

                // Fondo diferente si ya está desbloqueado
                if (data.desbloqueado == 1) {
                    $(row).css('background', '#fffff0');
                } else {
                    $(row).css('background', '#fff5f5');
                }

                // ID — link al PDF de la muestra
                $('td', row).eq(0).html(
                    '<a href="javascript:void(0);" onclick="genpdfCC(' + data.id + ')" class="tooltipsC"'
                    + ' title="Ver PDF muestra #' + data.id + '" style="text-decoration:underline;cursor:pointer;">'
                    + data.id + '</a>'
                );

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

                // Reg. Prod.
                $('td', row).eq(4).html(
                    '<a href="javascript:void(0);" onclick="verEtiquetaEtapaConPermiso(' + data.opdetregprod_id + ',\'ver-etiqueta-regprod\')" class="tooltipsC"'
                    + ' title="Ver etiqueta Reg. Prod. #' + data.opdetregprod_id + '" style="text-decoration:underline;cursor:pointer;">'
                    + data.opdetregprod_id + '</a>'
                );

                // CodProd — con acuerdo técnico si existe
                $('td', row).eq(5).attr('style', 'text-align:center');
                if (data.acuerdotecnico_id != null) {
                    var codHtml = '<a class="btn-accion-tabla btn-sm tooltipsC" title="Acuerdo Técnico"'
                                + ' onclick=\'genpdfAcuTec(' + data.acuerdotecnico_id + ',0,"")\'>'
                                + data.producto_id + '</a>';
                    if (data.at_impresofoto != '' && data.at_impresofoto != null) {
                        codHtml += '<a class="btn-accion-tabla btn-sm tooltipsC" title="Ver Imagen"'
                                 + ' onclick=\'verpdf2("at/' + data.at_impresofoto + '",2,"","ver-arte-acuerdo-tecnico")\'>'
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

                // Observación de la muestra (recorte a 60 chars con tooltip)
                if (data.observacion) {
                    var obs = data.observacion;
                    $('td', row).eq(9).html(
                        '<small class="tooltipsC" title="' + obs + '">'
                        + obs.substring(0, 60) + (obs.length > 60 ? '…' : '')
                        + '</small>'
                    );
                } else {
                    $('td', row).eq(9).html('<span class="text-muted">—</span>');
                }

                // Columna Desbloqueo: estado actual
                if (data.desbloqueado == 1) {
                    var titleDesb = 'Por: ' + (data.desbloqueo_usuario || '—')
                                  + ' | ' + (data.desbloqueo_fecha ? data.desbloqueo_fecha.substring(0, 16) : '—')
                                  + (data.desbloqueo_obs ? ' | ' + data.desbloqueo_obs : '');
                    $('td', row).eq(10).html(
                        '<span class="label label-success tooltipsC" title="' + titleDesb + '">'
                        + '<i class="fa fa-unlock"></i> Desbloqueado</span>'
                    );
                } else {
                    $('td', row).eq(10).html(
                        '<span class="label label-danger"><i class="fa fa-lock"></i> Bloqueado</span>'
                    );
                }

                // Acción: botón desbloquear si aún está bloqueado
                if (data.desbloqueado != 1) {
                    $('td', row).eq(11).html(
                        '<button class="btn btn-xs btn-warning btn-desbloquear" data-id="' + data.id + '"'
                        + ' title="Desbloquear muestra rechazada">'
                        + '<i class="fa fa-unlock-alt"></i> Desbloquear</button>'
                    );
                } else {
                    $('td', row).eq(11).html(
                        '<a href="/ccregistmuestra/' + data.id + '/ver" class="btn btn-xs btn-default" title="Ver detalle">'
                        + '<i class="fa fa-eye"></i></a>'
                    );
                }

                $('td', row).eq(11).attr('class', 'action-buttons');
            },
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            }
        });
    }

    // R5: Botón Consultar — lanza DataTable con los filtros activos
    $('#btnconsultar').on('click', function () {
        var qs = datosFiltros();
        configurarTabla('/ccdesbloqueopage' + qs);
    });

    // R5: Botón buscar cliente — abre modal
    $('#btnbuscarcliente').on('click', function () {
        $('#myModalBusqueda').modal('show');
    });

    // R5: Botón buscar producto — recarga tabla y abre modal
    $('#btnbuscarproducto').on('click', function () {
        $(".input-sm").val('');
        var aux_id = $('#producto_idPxP').val();
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

    // Campos numéricos — solo dígitos
    $('#opdetregprod_id, #ot_id, #op_id, #notaventa_id').on('input', function () {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
    });

    // Producto — solo dígitos y coma
    $('#producto_idPxP').on('input', function () {
        $(this).val($(this).val().replace(/[^0-9,]/g, ''));
    });

    // R5: Validación RUT — solo números y K
    $('#rut1').on('input', function () {
        var rut = $(this).val().replace(/[^0-9kK]/g, '');
        var idxK = rut.toLowerCase().indexOf('k');
        if (idxK !== -1 && idxK !== rut.length - 1) rut = rut.replace(/k/gi, '');
        if (rut.toLowerCase().includes('k')) rut = rut.slice(0, rut.toLowerCase().indexOf('k') + 1);
        $(this).val(rut);
    });

    // R5: Al salir del campo RUT: agregar guión y validar
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

    // Abrir modal desbloqueo
    $(document).on('click', '.btn-desbloquear', function () {
        var id = $(this).data('id');
        $('#desbloquear-id-label').text(id);
        $('#observacion-desbloqueo').val('');
        $('#btn-confirmar-desbloqueo').data('id', id);
        $('#modal-desbloquear-muestra').modal('show');
    });

    // Confirmar desbloqueo vía AJAX
    $('#btn-confirmar-desbloqueo').on('click', function () {
        var id  = $(this).data('id');
        var obs = $('#observacion-desbloqueo').val().trim();
        if (!obs) {
            Biblioteca.notificaciones('Debe ingresar la observación del desbloqueo.', 'CC Desbloqueo', 'error');
            return;
        }
        var csrf = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').first().val();
        $.post('/ccdesbloqueo/' + id + '/desbloquear', { _token: csrf, observacion: obs }, function (resp) {
            $('#modal-desbloquear-muestra').modal('hide');
            if (resp.ok) {
                Biblioteca.notificaciones(resp.msg, 'CC Desbloqueo', 'success');
                // Recargar tabla manteniendo los filtros vigentes
                if (tabla) {
                    tabla.ajax.reload(null, false);
                }
            } else {
                Biblioteca.notificaciones(resp.msg, 'CC Desbloqueo', 'error');
            }
        }).fail(function () {
            $('#modal-desbloquear-muestra').modal('hide');
            Biblioteca.notificaciones('Error al desbloquear la muestra.', 'CC Desbloqueo', 'error');
        });
    });
});

// Cierra modal cliente y copia el RUT al campo #rut1
function copiar_rut(id, rut) {
    $('#myModalBusqueda').modal('hide');
    $('#rut1').val(rut);
    $('#rut1').trigger('blur');
}

// Cierra modal producto y agrega el ID al campo #producto_idPxP (acepta múltiples separados por coma)
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
