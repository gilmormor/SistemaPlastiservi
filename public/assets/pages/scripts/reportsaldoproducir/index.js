/* ====================================================================
   SALDO POR PRODUCIR — de donde sale el numero que ve el vendedor.

   En la nota de venta, el campo de stock muestra cuanto falta FABRICAR para
   cubrir todo lo comprometido, no cuanto hay. Un negativo es sobreventa. Al
   vendedor se le da solo ese numero a proposito: si viera el stock pensaria
   que puede venderlo y agravaria el problema.

   Pero entonces nadie puede explicarlo. Esta pantalla lo desarma: cada bodega,
   cada nota de venta y cada reserva que lo forman.
   ==================================================================== */

var SP_TABLA = null;

$(document).ready(function () {

    /* Buscador de producto, el mismo patron que reportinvstock.

       Con producto/buscarnew.js la tabla del modal NO esta inicializada hasta
       que se llama a configTablaProd(). Recargarla antes -como hacen las
       pantallas que usan el buscar.js viejo- la crea sin columnas y DataTables
       falla con "Requested unknown parameter '0' for row 0". */
    $('#btnbuscarproducto').on('click', function () {
        $(this).val('');
        $(".input-sm").val('');

        if (typeof tablaProductoInicializada !== 'undefined' && !tablaProductoInicializada) {
            configTablaProd();
            tablaProductoInicializada = true;
        }

        /* Los codigos ya escritos se muestran como seleccionados, para que al
           elegir otro se sumen en vez de reemplazarlos. */
        var aux_id = $('#producto_idPxP').val();
        if (aux_id == null || aux_id.length === 0 || /^\s+$/.test(aux_id)) {
            $('#divprodselec').hide();
            $('#productos').html('');
        } else {
            var aux_arr = aux_id.split(',');
            $('#productos').html('');
            for (var i = 0; i < aux_arr.length; i++) {
                $('#productos').append("<option value='" + aux_arr[i] + "' selected>" + aux_arr[i] + "</option>");
            }
            $('#divprodselec').show();
        }

        $('#myModalBuscarProd').modal('show');
    });

    /* Solo digitos y comas: el campo acepta varios codigos */
    $('#producto_idPxP').on('input', function () {
        $(this).val($(this).val().replace(/[^0-9,]/g, ''));
    });

    $('#btnconsultar').on('click', consultar);

    $('#producto_idPxP').on('keypress', function (e) {
        if (e.which === 13) { e.preventDefault(); consultar(); }
    });

    $(document).on('click', '.sp-detalle', function () {
        verDetalle($(this).attr('data-prod'));
    });
});

function consultar() {
    var aux_prod = $.trim($('#producto_idPxP').val());
    if (aux_prod === '') {
        swal({ title: 'Indique al menos un producto', icon: 'info', buttons: { confirm: 'Cerrar' } });
        return;
    }

    var aux_url = SP_RUTA_PAGE + '?sucursal_id=' + $('#sucursal_id').val() +
                  '&producto_id=' + aux_prod;

    if (SP_TABLA) {
        SP_TABLA.ajax.url(aux_url).load();
        return;
    }

    SP_TABLA = $('#tabla-saldo').DataTable({
        'paging': true,
        'lengthChange': true,
        'ordering': true,
        'info': true,
        'autoWidth': false,
        'ajax': aux_url,
        'columns': [
            { data: 'producto_id' },
            { data: 'producto_nombre' },
            { data: 'stock' },
            { data: 'pendiente' },
            { data: 'reservado' },
            { data: 'saldo' },
            { data: 'producto_id' }
        ],
        'language': { 'url': 'https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json' },
        'createdRow': function (row, data) {
            $('td', row).eq(0).attr('style', 'text-align:center');

            for (var i = 2; i <= 5; i++) {
                $('td', row).eq(i).attr('style', 'text-align:right');
            }
            $('td', row).eq(2).html(MASKLA(data.stock, 2));
            /* Debajo de cada cifra, de quien es: sin esto hay que abrir el
               detalle para saber algo que cabe en una linea. */
            $('td', row).eq(3).html(MASKLA(data.pendiente, 2) +
                (data.pendiente_para ? '<br><small class="text-muted">' + data.pendiente_para + '</small>' : ''));

            $('td', row).eq(4).html((data.reservado > 0
                ? '<span class="text-red">' + MASKLA(data.reservado, 2) + '</span>'
                : MASKLA(0, 2)) +
                (data.reservado_para ? '<br><small class="text-muted">' + data.reservado_para + '</small>' : ''));

            /* El saldo es lo que importa: en rojo y en negrita cuando es negativo,
               porque significa que el producto esta sobrevendido. */
            $('td', row).eq(5).html(data.saldo < 0
                ? '<strong class="text-red">' + MASKLA(data.saldo, 2) + '</strong>'
                : '<strong class="text-green">' + MASKLA(data.saldo, 2) + '</strong>');

            $('td', row).eq(6).attr('style', 'text-align:center');
            $('td', row).eq(6).html(
                '<a class="btn-accion-tabla tooltipsC sp-detalle" style="cursor:pointer;" ' +
                'title="Ver de dónde sale este saldo" data-prod="' + data.producto_id + '">' +
                '<i class="fa fa-fw fa-search-plus"></i></a>'
            );
        }
    });
}

function verDetalle(producto_id) {
    $.ajax({
        url: SP_RUTA_DETALLE + '/' + producto_id + '?sucursal_id=' + $('#sucursal_id').val(),
        type: 'GET',
        success: function (resp) {
            if (!resp.ok) { return; }
            $('#sp-det-titulo').text(producto_id + ' — ' + resp.producto);

            var r = resp.resumen;
            var h = '';

            /* Las tres piezas, en el mismo orden que la formula */
            h += bloque('Stock vendible', 'fa-cubes',
                tabla(['Bodega', 'Tipo', 'Stock'], resp.bodegas.map(function (b) {
                    return [b.bodega,
                            b.tipo == 1 ? 'Picking' : 'Venta',
                            MASKLA(b.stock, 2)];
                }), r ? MASKLA(r.stock, 2) : '0'));

            h += bloque('Pendiente por despachar', 'fa-truck',
                tabla(['N° NV', 'Cliente', 'Pidió', 'Despachado', 'Pendiente'],
                    resp.ventas.map(function (v) {
                        return ['NV ' + v.notaventa_id, v.razonsocial,
                                MASKLA(v.cant, 2), MASKLA(v.despachado, 2),
                                '<strong>' + MASKLA(v.pendiente, 2) + '</strong>'];
                    }), r ? '− ' + MASKLA(r.pendiente, 2) : '0'));

            h += bloque('Reservado', 'fa-lock',
                tabla(['Para', 'Bodega', 'Vence', 'Cantidad'],
                    resp.reservas.map(function (s) {
                        return [(s.razonsocial || '') + (s.notaventa_id ? ' · NV ' + s.notaventa_id : ''),
                                s.bodega, fechaCorta(s.fechavence), MASKLA(s.cant, 2)];
                    }), r ? '− ' + MASKLA(r.reservado, 2) : '0'));

            /* El resultado, que es el numero que ve el vendedor */
            if (r) {
                h += '<div class="alert ' + (r.saldo < 0 ? 'alert-danger' : 'alert-success') +
                     '" style="margin-top:10px;margin-bottom:0;">' +
                     '<strong style="font-size:15px;">Saldo por producir: ' + MASKLA(r.saldo, 2) + '</strong>' +
                     (r.saldo < 0
                        ? '<br><small>Sobrevendido: faltan ' + MASKLA(Math.abs(r.saldo), 2) +
                          ' unidades por fabricar para cubrir todo lo comprometido.</small>'
                        : '<br><small>Alcanza para cubrir lo comprometido.</small>') +
                     '</div>';
            }

            $('#sp-det-body').html(h);
            $('#modalDetalleSaldo').modal('show');
        }
    });
}

function bloque(titulo, icono, contenido, total) {
    return '<h5 style="margin-top:14px;"><i class="fa ' + icono + '"></i> <strong>' + titulo + '</strong></h5>' + contenido;
}

/* Tabla chica con su total; si no hay filas lo dice en vez de dejar un hueco */
function tabla(cols, filas, total) {
    if (!filas.length) {
        return '<p class="text-muted" style="margin-left:18px;">Sin registros. Total: <strong>' + total + '</strong></p>';
    }
    var h = '<table class="table table-condensed table-bordered" style="font-size:12px;"><thead><tr>';
    for (var i = 0; i < cols.length; i++) {
        h += '<th' + (i >= cols.length - 1 ? ' class="text-right"' : '') + '>' + cols[i] + '</th>';
    }
    h += '</tr></thead><tbody>';
    for (var f = 0; f < filas.length; f++) {
        h += '<tr>';
        for (var c = 0; c < filas[f].length; c++) {
            h += '<td' + (c >= 2 ? ' class="text-right"' : '') + '>' + filas[f][c] + '</td>';
        }
        h += '</tr>';
    }
    h += '</tbody><tfoot><tr><th colspan="' + (cols.length - 1) + '" class="text-right">Total</th>' +
         '<th class="text-right">' + total + '</th></tr></tfoot></table>';
    return h;
}

function fechaCorta(aux_f) {
    if (!aux_f) { return ''; }
    var p = aux_f.toString().substring(0, 10).split('-');
    return p.length === 3 ? p[2] + '/' + p[1] + '/' + p[0] : aux_f;
}
