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
var PR_ULTIMO = null;      // ultimo lote consultado, para no repetir la consulta
var PR_DISPONIBLE = 0;     // cuanto se puede entregar del lote en pantalla
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

    $('#pr-cant').on('input', function () {
        $(this).val($(this).val().replace(/[^0-9.,]/g, ''));
        validar();
    });

    /* Aviso temprano al pasarse. El servidor lo vuelve a comprobar: entre que se
       cargo la ficha y el usuario confirma, otro pudo reservar o entregar. */
    $('#pr-cant').on('blur', function () {
        if (numeroDe($(this).val()) > PR_DISPONIBLE) {
            $('#pr-cant-aviso').html('<span class="text-red">Solo hay ' +
                MASKLA(PR_DISPONIBLE, 2) + ' disponibles</span>');
        } else {
            $('#pr-cant-aviso').html('Disponible: <strong>' + MASKLA(PR_DISPONIBLE, 2) + '</strong>');
        }
    });

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
    $('#pr-cant').val('');
    $('#pr-cant-aviso').html('');
    PR_DISPONIBLE = 0;
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

            /* Lo apartado para otro cliente en esa misma bodega. El material es el
               mismo aunque la reserva no este anclada a este lote. */
            if (resp.reservado > 0) {
                $('#pr-f-reservado').html(MASKLA(resp.reservado, 2) +
                    '<br><small>' + (resp.reservado_de || '') + '</small>');
                $('#pr-f-fila-reservado').show();
            } else {
                $('#pr-f-fila-reservado').hide();
            }
            if (resp.ya_entregado > 0) {
                /* Se dice cuanto de lo entregado ya se retiro: sin ese dato los
                   numeros no cuadran a la vista. De 430 en bodega, entregadas
                   400 y retiradas 170, quedan 200 disponibles — no 30. Lo que
                   ya salio de la bodega no se puede descontar otra vez. */
                $('#pr-f-entregado').html(MASKLA(resp.ya_entregado, 2) +
                    (resp.ya_retirado > 0
                        ? ' <small class="text-muted">(' + MASKLA(resp.ya_retirado, 2) + ' ya retiradas)</small>'
                        : '') +
                    '<br><small class="text-muted">' + (resp.ya_reasignado || '') + '</small>');
                $('#pr-f-fila-entregado').show();
            } else {
                $('#pr-f-fila-entregado').hide();
            }

            $('#pr-f-disponible').html('<strong>' + MASKLA(resp.disponible, 2) + '</strong>');
            PR_DISPONIBLE = resp.disponible;
            /* Se propone el total disponible: lo habitual es entregar todo lo que
               queda, y quien necesite menos lo corrige. */
            $('#pr-cant').val(MASKLA(resp.disponible, 2));
            $('#pr-cant-aviso').html('Disponible: <strong>' + MASKLA(resp.disponible, 2) + '</strong>');

            /* Para quien se fabrico. Si no tiene nota de venta es una OT libre:
               no hay a quien quitarselo, pero igual se puede asignar. */
            if (resp.nv_origen) {
                $('#pr-f-origen').html('<strong>NV ' + resp.nv_origen + '</strong> — ' +
                    (resp.cliente_origen || ''));
            } else {
                $('#pr-f-origen').html('<span class="text-muted">Sin nota de venta (producción para stock)</span>');
            }
            $('#pr-ficha').show();

            /* Un lote se puede repartir entre varias notas de venta: 400 a una y
               el resto a otra. Antes la pantalla lo impedia -habia que deshacer
               la anterior- aunque el calculo del disponible y la tabla de abajo
               estan hechos para varios destinos. El motivo de entonces era no
               perder de vista donde termino el material, y eso ya esta cubierto:
               la ficha dice a quien se entrego cada parte y la tabla lista cada
               reasignacion con su destino. El tope del campo protege el resto. */
            if (resp.saldo <= 0) {
                $('#pr-error').text('El lote ya no tiene material en bodega: fue despachado.').show();
                $('#pr-destino-box').hide();
                validar();
                return;
            }

            /* Sin nada disponible no hay nada que entregar: se explica por que, en
               vez de dejar que llene el formulario para rechazarlo al final. */
            if (resp.disponible <= 0) {
                var aux_txt = 'No queda nada disponible de este lote: de los ' + MASKLA(resp.saldo, 2) + ' en bodega, ';
                var aux_mot = [];
                if (resp.reservado > 0) {
                    aux_mot.push('<strong>' + MASKLA(resp.reservado, 2) + '</strong> están reservados para ' +
                        (resp.reservado_de || 'otro cliente'));
                }
                if (resp.ya_entregado > 0) {
                    aux_mot.push('<strong>' + MASKLA(resp.ya_entregado, 2) + '</strong> ya se entregaron');
                }
                $('#pr-error').html(aux_txt + aux_mot.join(' y ') +
                    '. Para liberar una reserva: <em>Inventario &rarr; Reserva de Stock</em>.').show();
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

/* Convierte lo escrito a numero. Acepta 1.234,56 y 1234.56 */
function numeroDe(aux_txt) {
    aux_txt = (aux_txt || '').toString().trim();
    if (aux_txt.indexOf(',') >= 0) {
        aux_txt = aux_txt.replace(/\./g, '').replace(',', '.');
    }
    return parseFloat(aux_txt) || 0;
}

/* El motivo y la cantidad son obligatorios de verdad: no basta con pedirlos en
   el servidor. */
function validar() {
    var aux_ok = $('#pr-nvdet-destino').val() !== '' &&
                 $.trim($('#pr-motivo').val()) !== '' &&
                 numeroDe($('#pr-cant').val()) > 0;
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
            { data: 'bodegas' },
            { data: 'nv_origen' },
            { data: 'nv_destino' },
            { data: 'cant' },
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
            $('td', row).eq(5).html(data.nv_origen
                ? 'NV ' + data.nv_origen + '<br><small class="text-muted">' + (data.cliente_origen || '') + '</small>'
                : '<span class="text-muted">Sin NV</span>');
            /* La nota de venta destino se abre como en el resto del sistema.
               Sin permiso para verla se deja el numero en texto plano. */
            var aux_nvd = '';
            if (data.nv_destino) {
                aux_nvd = PR_VER_NV
                    ? 'NV <a class="btn-accion-tabla tooltipsC" style="cursor:pointer;" ' +
                      'onclick="genpdfNV(' + data.nv_destino + ',1)" ' +
                      'title="Nota de venta ' + data.nv_destino + '">' + data.nv_destino + '</a><br>'
                    : 'NV ' + data.nv_destino + '<br>';
            }
            $('td', row).eq(6).html(aux_nvd +
                '<small class="text-muted">' + (data.cliente_destino || '') + '</small>');

            /* Cuanto se entrego y cuanto se llevo ya el destinatario: con los dos
               datos se ve de un vistazo que entregas siguen pendientes y cuales
               estan consumidas. En las deshechas no se muestra nada retirado,
               porque el material volvio a su nota de venta de origen.
               Del 7 en adelante los indices corren uno por esta columna. */
            var aux_cel = '<strong>' + MASKLA(data.cant, 2) + '</strong>';
            if (data.usado !== null && data.usado !== undefined) {
                /* El numero es clicable: abre las solicitudes que lo componen.
                   Un total suelto no se puede verificar, y es justo lo que se
                   quiere revisar cuando una entrega no cuadra. */
                var aux_usado = data.usado > 0
                    ? '<a class="pr-ver-sd" style="cursor:pointer;text-decoration:underline;" ' +
                      'data-id="' + data.id + '" title="Ver las solicitudes de despacho">' +
                      MASKLA(data.usado, 2) + '</a>'
                    : MASKLA(data.usado, 2);

                if (data.pendiente <= 0) {
                    aux_cel += '<br><small class="text-muted">' + aux_usado + ' en SolDesp</small>';
                } else if (data.usado > 0) {
                    aux_cel += '<br><small class="text-muted">' + aux_usado + ' en SolDesp</small>' +
                               '<br><small class="text-green">quedan ' + MASKLA(data.pendiente, 2) + '</small>';
                } else {
                    aux_cel += '<br><small class="text-muted">sin SolDesp</small>';
                }
            }
            $('td', row).eq(7).html(aux_cel);
            $('td', row).eq(7).attr('style', 'text-align:right');

            $('td', row).eq(9).html(data.created_at ? data.created_at.substring(0, 10) : '');

            var aux_clase = (data.estado === 'Vigente') ? 'label-success' : 'label-default';
            $('td', row).eq(11).html('<span class="label ' + aux_clase + '">' + data.estado + '</span>');
            $('td', row).eq(11).attr('style', 'text-align:center');

            if (data.estado === 'Vigente') {
                $('td', row).eq(12).html(
                    '<a class="btn-accion-tabla tooltipsC pr-anular" style="cursor:pointer;" ' +
                    'title="Deshacer: el lote vuelve a su nota de venta de origen" data-id="' + data.id + '">' +
                    '<i class="fa fa-fw fa-undo text-danger"></i></a>'
                );
            } else {
                $('td', row).eq(12).html('');
                if (data.anulmotivo) {
                    $('td', row).eq(8).html((data.motivo || '') +
                        '<br><small class="text-red">Deshecha: ' + data.anulmotivo + '</small>');
                }
            }
            $('td', row).eq(12).attr('style', 'text-align:center');
        }
    });
}

/* ---------------------------------------------------------------------------
   Las solicitudes de despacho detras del numero "en SolDesp".
   --------------------------------------------------------------------------- */
$(document).on('click', '.pr-ver-sd', function () {
    var aux_id = $(this).data('id');
    $.ajax({
        url: PR_RUTA_SOLDESP + '/' + aux_id,
        type: 'GET',
        success: function (resp) {
            if (!resp.ok) {
                swal({ title: 'Atencion', text: resp.mensaje || 'No se pudo consultar.', type: 'warning' });
                return;
            }
            $('#pr-sd-titulo').text('Lote #' + resp.lote + ' — solicitudes de despacho');

            if (!resp.detalles.length) {
                $('#pr-sd-body').html('<p class="text-muted">Todavia no hay solicitudes de despacho ' +
                    'que tomen de esta entrega.</p>');
            } else {
                var h = '<table class="table table-condensed table-bordered" style="font-size:12px;">' +
                        '<thead><tr><th>N° SolDesp</th><th>Fecha</th><th>Nota de venta</th>' +
                        '<th>Cliente</th><th class="text-right">Cantidad</th><th>Estado</th></tr></thead><tbody>';
                for (var i = 0; i < resp.detalles.length; i++) {
                    var d = resp.detalles[i];

                    /* Al abrir la solicitud se le pasa este modal como padre: se
                       oculta y vuelve solo al cerrar el PDF. Sin eso quedarian
                       los dos abiertos, uno encima del otro. */
                    var aux_sd = PR_VER_SD
                        ? '<a class="btn-accion-tabla tooltipsC" style="cursor:pointer;" ' +
                          'onclick="genpdfSD(' + d.despachosol_id + ',1,\'modalSolDespReasig\')" ' +
                          'title="Ver Solicitud de Despacho">' + d.despachosol_id + '</a>'
                        : d.despachosol_id;

                    var aux_nv = (d.notaventa_id && PR_VER_NV)
                        ? '<a class="btn-accion-tabla tooltipsC" style="cursor:pointer;" ' +
                          'onclick="genpdfNV(' + d.notaventa_id + ',1,\'modalSolDespReasig\')" ' +
                          'title="Nota de venta ' + d.notaventa_id + '">' + d.notaventa_id + '</a>'
                        : (d.notaventa_id || '');

                    /* Donde esta hoy ese material: aprobada lo mando a picking, y
                       si ya tiene orden de despacho salio de la bodega. */
                    var aux_est = d.ordenes > 0
                        ? '<span class="label label-success">En orden de despacho</span>'
                        : (d.aprorddesp == 1
                            ? '<span class="label label-info">En picking</span>'
                            : '<span class="label label-default">Sin aprobar</span>');

                    h += '<tr><td>' + aux_sd + '</td>' +
                         '<td>' + (d.fechahora ? d.fechahora.substring(0, 10) : '') + '</td>' +
                         '<td>' + aux_nv + '</td>' +
                         '<td><small>' + (d.razonsocial || '') + '</small></td>' +
                         '<td class="text-right"><strong>' + MASKLA(d.cant, 2) + '</strong></td>' +
                         '<td>' + aux_est + '</td></tr>';
                }
                h += '</tbody><tfoot><tr><th colspan="4" class="text-right">Total</th>' +
                     '<th class="text-right">' + MASKLA(resp.total, 2) + '</th><th></th></tr></tfoot></table>';
                $('#pr-sd-body').html(h);
            }
            $('#modalSolDespReasig').modal('show');
        }
    });
});
