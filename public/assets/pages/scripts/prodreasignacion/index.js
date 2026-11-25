/* ====================================================================
   REASIGNAR PRODUCCION — un lote fabricado para una nota de venta se
   entrega a otro cliente o a otra nota de venta.

   El orden de la pantalla es el de la decision: primero QUE lote, luego se
   confirma para quien se habia fabricado -porque a alguien se le va a quitar-
   y recien ahi a quien se le entrega y por que.

   Nada se borra: el vinculo con la nota de venta de origen queda intacto y lo
   que se registra es el hecho nuevo.
   ==================================================================== */

var PR_TABLA = null;
var PR_ULTIMO = null;   // ultimo lote consultado, para no repetir la consulta
var PR_PRODUCTO = null;
var PR_NVDET_ORIGEN = null;

$(document).ready(function () {

    cargarTabla();

    /* Solo digitos. Va en input -corrige a medida que escribe- pero no dispara
       ninguna consulta. */
    $('#pr-lote').on('input', function () {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
    });

    /* La consulta se dispara al SALIR del campo, no al teclear: escribiendo "66"
       se consultaria primero el lote 6, que es otro lote y otra ficha. */
    $('#pr-lote').on('blur', function () {
        var aux_val = $.trim($(this).val());
        if (aux_val === '') { PR_ULTIMO = null; limpiar(); return; }
        if (aux_val === PR_ULTIMO) { return; }   // ya esta en pantalla
        PR_ULTIMO = aux_val;
        consultarLote(aux_val);
    });

    /* Enter: consulta sin tener que salir del campo con el mouse o el tabulador. */
    $('#pr-lote').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $(this).blur();
        }
    });

    $(document).on('change', '.pr-destino-radio', function () {
        $('#pr-nvdet-destino').val($(this).val());
        validar();
    });

    $('#pr-motivo').on('input', validar);

    $('#pr-vertodas').on('change', cargarTabla);

    /* Deshacer: el lote vuelve a su nota de venta de origen. */
    $(document).on('click', '.pr-anular', function () {
        var aux_id = $(this).attr('data-id');
        swal({
            title: '¿Deshacer la reasignación #' + aux_id + '?',
            text: 'El lote vuelve a quedar para su nota de venta de origen. Indique el motivo:',
            content: 'input',
            icon: 'warning',
            buttons: { cancel: 'Cancelar', confirm: 'Deshacer' }
        }).then(function (aux_motivo) {
            if (aux_motivo === null) { return; }
            $.ajax({
                url: 'prodreasignacion/' + aux_id + '/anular',
                type: 'POST',
                data: {
                    motivo: aux_motivo,
                    _token: $('meta[name="csrf-token"]').attr('content') || $('input[name=_token]').first().val()
                },
                success: function (resp) {
                    if (resp.id == 1) {
                        Biblioteca.notificaciones('Reasignación deshecha', 'Plastiservi', 'success');
                        cargarTabla();
                        var aux_lote = $.trim($('#pr-lote').val());
                        if (aux_lote !== '') { consultarLote(aux_lote); }
                    } else {
                        swal({ title: resp.mensaje, icon: 'error', buttons: { confirm: 'Cerrar' } });
                    }
                }
            });
        });
    });
});

function limpiar() {
    $('#pr-ficha').hide();
    $('#pr-error').hide();
    $('#pr-destino-box').hide();
    $('#pr-destinos-body').html('');
    $('#pr-nvdet-destino').val('');
    PR_PRODUCTO = null;
    PR_NVDET_ORIGEN = null;
    validar();
}

function consultarLote(id) {
    $.ajax({
        url: PR_RUTA_LOTE + '/' + id,
        type: 'GET',
        success: function (resp) {
            if (!resp.ok) {
                limpiar();
                $('#pr-error').text(resp.mensaje).show();
                return;
            }
            $('#pr-error').hide();
            $('#pr-f-lote').html('<strong>#' + resp.lote_id + '</strong>');
            /* El codigo delante del nombre: es como se identifica el producto en
               el resto del sistema y lo que la gente tiene a mano. */
            $('#pr-f-producto').html('<strong>' + resp.producto_id + '</strong> &mdash; ' + resp.producto);
            $('#pr-f-bodega').html(MASKLA(resp.saldo, 2) + ' en ' + resp.bodega);

            /* Para quien se fabrico. Si no tiene nota de venta es una OT libre:
               no hay a quien quitarselo, pero igual se puede asignar. */
            if (resp.nv_origen) {
                $('#pr-f-origen').html('<strong>NV ' + resp.nv_origen + '</strong> — ' +
                    (resp.cliente_origen || ''));
            } else {
                $('#pr-f-origen').html('<span class="text-muted">Sin nota de venta (producción para stock)</span>');
            }
            $('#pr-ficha').show();

            /* Ya reasignado: no se encadenan reasignaciones sin deshacer la
               anterior, o nadie entenderia donde termino el material. */
            if (resp.ya_reasignado) {
                $('#pr-error').html('Este lote ya fue reasignado a <strong>' + resp.ya_reasignado +
                    '</strong>. Deshaga esa reasignación antes de hacer otra.').show();
                $('#pr-destino-box').hide();
                validar();
                return;
            }
            if (resp.saldo <= 0) {
                $('#pr-error').text('El lote ya no tiene material en bodega: fue despachado.').show();
                $('#pr-destino-box').hide();
                validar();
                return;
            }

            PR_PRODUCTO = resp.producto_id;
            PR_NVDET_ORIGEN = resp.nvdet_origen;
            cargarDestinos();
        },
        error: function () { limpiar(); }
    });
}

function cargarDestinos() {
    $.ajax({
        url: PR_RUTA_DESTINOS + '/' + PR_PRODUCTO + '?nvdet_excluir=' + (PR_NVDET_ORIGEN || ''),
        type: 'GET',
        success: function (resp) {
            $('#pr-destinos-body').html('');
            $('#pr-nvdet-destino').val('');

            if (!resp.ok || !resp.items.length) {
                $('#pr-destinos-body').append(
                    '<tr><td colspan="6" class="text-muted">' +
                    'No hay otras notas de venta de este producto a las que entregarlo.</td></tr>'
                );
                $('#pr-destino-box').show();
                validar();
                return;
            }

            for (var i = 0; i < resp.items.length; i++) {
                var it = resp.items[i];
                /* Se marca cual requiere fabricacion: esas son las que estan
                   esperando material y las candidatas naturales. */
                var aux_fab = (it.requiere_fabricacion == 1)
                    ? '<span class="label label-warning">Requiere fabricación</span>'
                    : '<span class="text-muted">Con stock</span>';
                $('#pr-destinos-body').append(
                    '<tr>' +
                    '<td class="text-center"><input type="radio" name="pr-destino" ' +
                    'class="pr-destino-radio" value="' + it.id + '"></td>' +
                    '<td>' + it.notaventa_id + '</td>' +
                    '<td>' + it.razonsocial + '</td>' +
                    '<td class="text-right">' + MASKLA(it.cant, 2) + '</td>' +
                    '<td class="text-right"><strong>' + MASKLA(it.pendiente, 2) + '</strong></td>' +
                    '<td>' + aux_fab + '</td>' +
                    '</tr>'
                );
            }
            $('#pr-destino-box').show();
            validar();
        }
    });
}

/* El motivo es obligatorio de verdad: no basta con pedirlo en el servidor. */
function validar() {
    var aux_ok = $('#pr-nvdet-destino').val() !== '' &&
                 $.trim($('#pr-motivo').val()) !== '';
    $('#pr-guardar').prop('disabled', !aux_ok);
}

function cargarTabla() {
    var aux_url = PR_RUTA_PAGE + '?vertodas=' + ($('#pr-vertodas').is(':checked') ? 1 : 0);

    if (PR_TABLA) {
        PR_TABLA.ajax.url(aux_url).load();
        return;
    }

    PR_TABLA = $('#tabla-reasig').DataTable({
        'paging': true,
        'lengthChange': true,
        'ordering': true,
        'info': true,
        'autoWidth': false,
        'ajax': aux_url,
        'order': [[0, 'desc']],
        'columns': [
            { data: 'id' },
            { data: 'opdetregprod_id' },
            { data: 'producto_id' },
            { data: 'producto_nombre' },
            { data: 'nv_origen' },
            { data: 'nv_destino' },
            { data: 'motivo' },
            { data: 'created_at' },
            { data: 'usuario_nombre' },
            { data: 'estado' },
            { data: 'id' }
        ],
        'language': { 'url': 'https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json' },
        'createdRow': function (row, data) {
            $('td', row).eq(1).html('<strong>#' + data.opdetregprod_id + '</strong>');
            $('td', row).eq(1).attr('style', 'text-align:center');

            $('td', row).eq(2).attr('style', 'text-align:center');

            /* Las dos puntas de la historia, cada una con su cliente.
               Del 4 en adelante: la columna CodProd corrio los indices uno. */
            $('td', row).eq(4).html(data.nv_origen
                ? 'NV ' + data.nv_origen + '<br><small class="text-muted">' + (data.cliente_origen || '') + '</small>'
                : '<span class="text-muted">Sin NV</span>');
            $('td', row).eq(5).html((data.nv_destino ? 'NV ' + data.nv_destino + '<br>' : '') +
                '<small class="text-muted">' + (data.cliente_destino || '') + '</small>');

            $('td', row).eq(7).html(data.created_at ? data.created_at.substring(0, 10) : '');

            var aux_clase = (data.estado === 'Vigente') ? 'label-success' : 'label-default';
            $('td', row).eq(9).html('<span class="label ' + aux_clase + '">' + data.estado + '</span>');
            $('td', row).eq(9).attr('style', 'text-align:center');

            if (data.estado === 'Vigente') {
                $('td', row).eq(10).html(
                    '<a class="btn-accion-tabla tooltipsC pr-anular" style="cursor:pointer;" ' +
                    'title="Deshacer: el lote vuelve a su nota de venta de origen" data-id="' + data.id + '">' +
                    '<i class="fa fa-fw fa-undo text-danger"></i></a>'
                );
            } else {
                $('td', row).eq(10).html('');
                if (data.anulmotivo) {
                    $('td', row).eq(6).html((data.motivo || '') +
                        '<br><small class="text-red">Deshecha: ' + data.anulmotivo + '</small>');
                }
            }
            $('td', row).eq(10).attr('style', 'text-align:center');
        }
    });
}
