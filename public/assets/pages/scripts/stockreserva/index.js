/* ====================================================================
   RESERVA DE STOCK — apartar sin mover.

   El picking es una reserva dura: saca el stock de la bodega de venta. Pero
   exige que exista una nota de venta con el item cargado, asi que no sirve
   para apartar lo que entro por una OT libre o por carga de inventario antes
   de que haya venta. Esta pantalla cubre ese hueco.

   Lo que el usuario tiene que ver ANTES de reservar no es cuanto hay, sino
   cuanto queda LIBRE: de nada sirve saber que hay 500 si 400 ya estan
   apartados para otro cliente. Por eso la tabla de bodegas muestra las tres
   cifras.

   La validacion de verdad esta en el servidor: dos personas pueden estar
   reservando el mismo stock a la vez.
   ==================================================================== */

var SR_TABLA = null;
var SR_LIBRE_SEL = 0;   // libre de la bodega elegida, para avisar al escribir

$(document).ready(function () {

    cargarTabla();

    /* Buscar producto: mismo patron que ccdesbloqueo y las demas pantallas. */
    $('#btnbuscarproducto').on('click', function () {
        $(".input-sm").val('');
        $('#tabla-data-productos').DataTable().ajax
            .url('producto/productobuscarpage/?cliente_id=&sucursal_id=&producto_id=').load();
        $('#myModalBuscarProd').modal('show');
    });

    /* Al cambiar el producto se consultan sus bodegas, pero NUNCA por cada
       tecla: escuchando 'input' escribir 2069 lanzaba cuatro consultas al
       servidor -2, 20, 206, 2069- y si la respuesta de una lenta llegaba
       despues de la ultima, la pantalla terminaba mostrando las bodegas del
       codigo equivocado.

       'change' dispara al salir del campo y solo si el valor cambio, que es
       justo lo que se quiere. Mismo patron que el filtro #sr-f-producto de
       mas abajo. */
    $('#producto_idPxP').on('change', function () {
        consultarProductoEscrito();
    });

    /* Enter consulta igual, aunque el codigo no haya cambiado: es la forma
       natural de reintentar sin tener que salir y volver al campo. */
    $('#producto_idPxP').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            consultarProductoEscrito();
        }
    });

    /* Al aceptar en el modal de busqueda, general.js escribe el codigo con
       .val(), que no dispara ningun evento: sin esto, elegir del modal se
       quedaria sin consultar. Su handler se registra antes que este -general.js
       se carga primero-, asi que aqui el campo ya tiene el valor.
       Solo se AGREGA un oyente desde el js de esta pantalla: general.js no se
       toca, que lo cargan todas las demas. */
    $('#aceptarmbp').on('click', function () {
        consultarProductoEscrito();
    });

    /* Solo digitos en los campos de id */
    $('#producto_idPxP, #sr-lote, #sr-nvdet').on('input', function () {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
    });

    /* Elegir bodega */
    $(document).on('change', '.sr-bodega-radio', function () {
        $('#sr-ibp').val($(this).val());
        SR_LIBRE_SEL = parseFloat($(this).attr('data-libre')) || 0;
        $('#sr-libre-aviso').html('Libre en esa bodega: <strong>' + MASKLA(SR_LIBRE_SEL, 2) + '</strong>');
        validarBoton();
    });

    $('#sr-cant').on('input', function () {
        $(this).val($(this).val().replace(/[^0-9.,]/g, ''));
        validarBoton();
    });

    /* Aviso temprano si se pasa del libre. El servidor lo vuelve a comprobar:
       esto solo evita que llene el formulario para que se lo rechacen. */
    $('#sr-cant').on('blur', function () {
        var aux_cant = numeroDe($(this).val());
        if (SR_LIBRE_SEL > 0 && aux_cant > SR_LIBRE_SEL) {
            $('#sr-libre-aviso').html('<span class="text-red">Solo quedan ' +
                MASKLA(SR_LIBRE_SEL, 2) + ' libres en esa bodega</span>');
        }
    });

    $('#sr-vertodas').on('change', function () {
        cargarTabla();
    });

    /* Filtros del listado. El cliente recarga al elegir; el codigo de producto
       al salir del campo, para no consultar por cada tecla. */
    $('#sr-f-cliente').on('change', cargarTabla);
    $('#sr-f-producto').on('blur', cargarTabla);
    $('#sr-f-producto').on('input', function () {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
    });
    $('#sr-f-producto').on('keypress', function (e) {
        if (e.which === 13) { e.preventDefault(); $(this).blur(); }
    });

    $('#sr-f-limpiar').on('click', function () {
        $('#sr-f-cliente').val('').selectpicker('refresh');
        $('#sr-f-producto').val('');
        cargarTabla();
    });

    /* Liberar: el stock vuelve a quedar disponible. Se pide el motivo porque
       la fila no se borra y despues hay que poder explicar que paso. */
    $(document).on('click', '.sr-liberar', function () {
        var aux_id = $(this).attr('data-id');
        var aux_upd = $(this).attr('data-upd');
        swal({
            title: '¿Liberar la reserva #' + aux_id + '?',
            text: 'El stock vuelve a quedar disponible. Indique el motivo:',
            content: 'input',
            icon: 'warning',
            buttons: { cancel: 'Cancelar', confirm: 'Liberar' }
        }).then(function (aux_motivo) {
            if (aux_motivo === null) { return; }
            $.ajax({
                url: 'stockreserva/' + aux_id + '/liberar',
                type: 'POST',
                data: {
                    motivo: aux_motivo,
                    updated_at: aux_upd,
                    _token: $('meta[name="csrf-token"]').attr('content') || $('input[name=_token]').first().val()
                },
                success: function (resp) {
                    if (resp.id == 1) {
                        Biblioteca.notificaciones('Reserva liberada', 'Plastiservi', 'success');
                        cargarTabla();
                        /* Si estaba mirando ese producto, los numeros cambiaron */
                        var aux_prod = $.trim($('#producto_idPxP').val());
                        if (aux_prod !== '') { consultarStock(aux_prod.split(',')[0]); }
                    } else {
                        swal({ title: resp.mensaje, icon: 'error', buttons: { confirm: 'Cerrar' } });
                    }
                }
            });
        });
    });
});

/* Convierte lo escrito por el usuario a numero. Acepta 1.234,56 y 1234.56 */
function numeroDe(aux_txt) {
    aux_txt = (aux_txt || '').toString().trim();
    if (aux_txt.indexOf(',') >= 0) {
        aux_txt = aux_txt.replace(/\./g, '').replace(',', '.');
    }
    return parseFloat(aux_txt) || 0;
}

/* Consulta las bodegas del codigo que haya en el campo. Si esta vacio, limpia:
   es el mismo comportamiento que tenia el handler anterior. */
function consultarProductoEscrito() {
    var aux_id = $.trim($('#producto_idPxP').val()).split(',')[0];
    if (aux_id === '') {
        limpiarBodegas();
        return;
    }
    consultarStock(aux_id);
}

function limpiarBodegas() {
    $('#sr-bodegas').hide();
    $('#sr-sinstock').hide();
    $('#sr-bodegas-body').html('');
    $('#sr-producto-nombre').text('');
    $('#sr-ibp').val('');
    SR_LIBRE_SEL = 0;
    $('#sr-libre-aviso').html('');
    validarBoton();
}

function consultarStock(producto_id) {
    $.ajax({
        url: SR_RUTA_STOCK + '/' + producto_id,
        type: 'GET',
        success: function (resp) {
            $('#sr-bodegas-body').html('');
            $('#sr-ibp').val('');
            SR_LIBRE_SEL = 0;
            $('#sr-libre-aviso').html('');

            if (!resp.ok) { limpiarBodegas(); return; }
            $('#sr-producto-nombre').text(resp.producto);

            if (!resp.bodegas.length) {
                $('#sr-bodegas').hide();
                $('#sr-sinstock').show();
                validarBoton();
                return;
            }

            $('#sr-sinstock').hide();
            for (var i = 0; i < resp.bodegas.length; i++) {
                var b = resp.bodegas[i];
                /* Sin nada libre, la bodega se muestra pero no se puede elegir:
                   es informacion util (ahi hay stock, pero es de otro). */
                var aux_dis = (b.libre <= 0) ? ' disabled' : '';
                $('#sr-bodegas-body').append(
                    '<tr' + (b.libre <= 0 ? ' class="text-muted"' : '') + '>' +
                    '<td class="text-center"><input type="radio" name="sr-bodega" class="sr-bodega-radio" ' +
                    'value="' + b.id + '" data-libre="' + b.libre + '"' + aux_dis + '></td>' +
                    '<td>' + b.bodega_nombre + '</td>' +
                    '<td class="text-right">' + MASKLA(b.stock, 2) + '</td>' +
                    '<td class="text-right">' + (b.reservado > 0
                        ? '<span class="text-red">' + MASKLA(b.reservado, 2) + '</span>'
                        : MASKLA(0, 2)) + '</td>' +
                    '<td class="text-right"><strong>' + MASKLA(b.libre, 2) + '</strong></td>' +
                    '</tr>'
                );
            }
            $('#sr-bodegas').show();
            validarBoton();
        },
        error: function () { limpiarBodegas(); }
    });
}

/* El boton se habilita solo con lo minimo: bodega, cantidad y un destino. */
function validarBoton() {
    var aux_ok = $('#sr-ibp').val() !== '' &&
                 numeroDe($('#sr-cant').val()) > 0 &&
                 ($('#sr-cliente').val() || $.trim($('#sr-nvdet').val()) !== '');
    $('#sr-guardar').prop('disabled', !aux_ok);
}

$(document).on('change', '#sr-cliente, #sr-nvdet', validarBoton);

function cargarTabla() {
    var aux_url = SR_RUTA_PAGE + '?vertodas=' + ($('#sr-vertodas').is(':checked') ? 1 : 0) +
        '&cliente_id=' + ($('#sr-f-cliente').val() || '') +
        '&producto_id=' + $.trim($('#sr-f-producto').val());

    if (SR_TABLA) {
        SR_TABLA.ajax.url(aux_url).load();
        return;
    }

    SR_TABLA = $('#tabla-reservas').DataTable({
        'paging': true,
        'lengthChange': true,
        'ordering': true,
        'info': true,
        'autoWidth': false,
        'ajax': aux_url,
        'order': [[0, 'desc']],
        'columns': [
            { data: 'id' },
            { data: 'producto_id' },
            { data: 'producto_nombre' },
            { data: 'bodega_nombre' },
            { data: 'opdetregprod_id' },
            { data: 'cant' },
            { data: 'cliente_nombre' },
            { data: 'fechavence' },
            { data: 'motivo' },
            { data: 'usuario_nombre' },
            { data: 'estado' },
            { data: 'id' }
        ],
        'language': { 'url': 'https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json' },
        'createdRow': function (row, data) {
            /* Del 2 en adelante: la columna CodProd corrio los indices uno. */
            $('td', row).eq(1).attr('style', 'text-align:center');

            $('td', row).eq(4).html(data.opdetregprod_id ? '#' + data.opdetregprod_id : '—');
            $('td', row).eq(4).attr('style', 'text-align:center');

            $('td', row).eq(5).attr('style', 'text-align:right');
            $('td', row).eq(5).attr('data-order', data.cant);
            $('td', row).eq(5).html(MASKLA(data.cant, 2));

            /* Para quien: cliente, item de NV, o los dos */
            var aux_para = [];
            if (data.cliente_nombre) { aux_para.push(data.cliente_nombre); }
            if (data.notaventa_id) { aux_para.push('NV ' + data.notaventa_id); }
            $('td', row).eq(6).html(aux_para.length ? aux_para.join('<br>') : '—');

            /* Estado con color: vigente verde, vencida naranja, el resto gris */
            var aux_clase = 'label-default';
            if (data.estado === 'Vigente') { aux_clase = 'label-success'; }
            if (data.estado === 'Vencida') { aux_clase = 'label-warning'; }
            if (data.estado === 'Liberada') { aux_clase = 'label-danger'; }
            $('td', row).eq(10).html('<span class="label ' + aux_clase + '">' + data.estado + '</span>');
            $('td', row).eq(10).attr('style', 'text-align:center');

            /* Una reserva cumplida no se libera: el stock ya se movio */
            if (data.puede_liberar == 1) {
                $('td', row).eq(11).html(
                    '<a class="btn-accion-tabla tooltipsC sr-liberar" style="cursor:pointer;" ' +
                    'title="Liberar esta reserva" data-id="' + data.id + '" data-upd="' + data.updatednum_at + '">' +
                    '<i class="fa fa-fw fa-unlock text-danger"></i></a>'
                );
            } else {
                $('td', row).eq(11).html('');
            }
            $('td', row).eq(11).attr('style', 'text-align:center');

            /* El motivo de la liberacion se muestra junto al original: explica
               por que ese stock volvio a estar disponible. */
            if (data.anulmotivo) {
                $('td', row).eq(8).html((data.motivo || '') +
                    '<br><small class="text-red">Liberada: ' + data.anulmotivo + '</small>');
            }
        }
    });
}
