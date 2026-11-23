/* ====================================================================
   REGISTRAR PRODUCCION POR LOTE — buscador.

   El operario escribe el numero de la etiqueta del rollo y, antes de
   continuar, ve a que producto y a que etapa corresponde. Es la confirmacion
   de que tecleo el lote correcto: equivocarse de rollo significa registrar
   produccion contra otra orden.

   La validacion de verdad esta en el servidor (RegProdLoteController); esto
   solo adelanta el dato para no hacerle perder el viaje.
   ==================================================================== */

var RPL_TIMER = null;

$(document).ready(function () {

    /* Se consulta mientras escribe, con una pausa: asi no se dispara una
       consulta por cada tecla de un numero de 2 o 3 cifras. */
    $('#lote_id').on('input', function () {
        var aux_val = $.trim($(this).val());

        clearTimeout(RPL_TIMER);
        if (aux_val === '') {
            ocultarFicha();
            return;
        }
        RPL_TIMER = setTimeout(function () { consultarLote(aux_val); }, 400);
    });

    /* Enter envia, que es como se va a usar: teclear el numero y pulsar Enter
       sin soltar el rollo. */
    $('#lote_id').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#form-lote').submit();
        }
    });
});

function ocultarFicha() {
    $('#rpl-ficha').hide();
    $('#rpl-error').hide();
}

function consultarLote(id) {
    $.ajax({
        url: RPL_RUTA_CONSULTA + '/' + id,
        type: 'GET',
        success: function (resp) {
            if (!resp.ok) {
                $('#rpl-ficha').hide();
                $('#rpl-error').text(resp.mensaje).show();
                return;
            }

            $('#rpl-error').hide();
            $('#rpl-lote').text('#' + resp.lote_id);
            $('#rpl-producto').text(resp.producto);
            $('#rpl-kg').text(resp.kgprod + ' kg');
            $('#rpl-origen').text(resp.etapaorigen + (resp.op_id ? ' — OP-' + resp.op_id : ''));

            /* Si no tiene acceso a la etapa siguiente se avisa aqui mismo, en
               vez de dejar que pulse Continuar para que el servidor lo rechace. */
            if (!resp.etapadest) {
                $('#rpl-destino').html('<span class="text-red">Última etapa: no hay etapa siguiente</span>');
            } else if (!resp.acceso) {
                $('#rpl-destino').html('<span class="text-red">' + resp.etapadest +
                                       ' — usted no tiene acceso a esta etapa</span>');
            } else {
                $('#rpl-destino').html('<strong class="text-green">' + resp.etapadest + '</strong>');
            }
            $('#rpl-ficha').show();
        },
        error: function () {
            ocultarFicha();
        }
    });
}
