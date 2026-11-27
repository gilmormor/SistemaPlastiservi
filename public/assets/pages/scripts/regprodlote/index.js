/* ====================================================================
   REGISTRAR PRODUCCION POR LOTE — buscador.

   El operario escribe el numero de la etiqueta del rollo y, antes de
   continuar, ve a que producto y a que etapa corresponde. Es la confirmacion
   de que tecleo el lote correcto: equivocarse de rollo significa registrar
   produccion contra otra orden.

   La validacion de verdad esta en el servidor (RegProdLoteController); esto
   solo adelanta el dato para no hacerle perder el viaje.
   ==================================================================== */

/* Lote cuya ficha esta a la vista. Sirve para dos cosas: saber si el Enter que
   llega es el de "ver" o el de "continuar", y borrar la ficha en cuanto el
   numero deja de coincidir, para que no queden en pantalla los datos de un
   rollo y el numero de otro. */
var RPL_LOTE_VISTO = null;

$(document).ready(function () {

    /* Mientras escribe NO se consulta nada: solo se retira la ficha si el
       numero ya no es el que se consulto. Dejarla puesta seria peor que no
       mostrarla, porque el operario estaria viendo un rollo y el campo otro. */
    $('#lote_id').on('input', function () {
        if ($.trim($(this).val()) !== RPL_LOTE_VISTO) {
            ocultarFicha();
        }
    });

    /* La consulta sale al salir del campo. 'change' solo dispara si el valor
       cambio, asi que volver a entrar y salir sin tocar nada no repite la
       consulta. */
    $('#lote_id').on('change', function () {
        consultarLote($.trim($(this).val()));
    });

    /* Enter: el primero muestra la ficha, el segundo continua.
       Antes enviaba siempre, y asi el operario nunca llegaba a ver la ficha
       -que existe justo para confirmar que es el rollo correcto-: equivocarse
       de rollo es registrar produccion contra otra orden. Sigue siendo la misma
       tecla repetida, de modo que no hay que soltar el rollo.
       Si la consulta devolvio un error, el segundo Enter tampoco envia: el
       mensaje ya esta a la vista y entrar solo serviria para que el servidor
       repita el rechazo. Para forzar queda el boton Continuar. */
    $('#lote_id').on('keypress', function (e) {
        if (e.which !== 13) { return; }
        e.preventDefault();

        var aux_val = $.trim($(this).val());
        if (aux_val !== '' && aux_val === RPL_LOTE_VISTO && $('#rpl-ficha').is(':visible')) {
            $('#form-lote').submit();
            return;
        }
        consultarLote(aux_val);
    });
});

function ocultarFicha() {
    $('#rpl-ficha').hide();
    $('#rpl-error').hide();
    RPL_LOTE_VISTO = null;
}

function consultarLote(id) {
    /* Sin numero no hay nada que consultar: se limpia y se sale. Lo hacia el
       handler de 'input' que se quito. */
    if ($.trim(id) === '') {
        ocultarFicha();
        return;
    }

    $.ajax({
        url: RPL_RUTA_CONSULTA + '/' + id,
        type: 'GET',
        success: function (resp) {
            if (!resp.ok) {
                $('#rpl-ficha').hide();
                $('#rpl-error').text(resp.mensaje).show();
                RPL_LOTE_VISTO = null;   // con error, Enter no continua
                return;
            }

            $('#rpl-error').hide();
            RPL_LOTE_VISTO = $.trim(String(id));
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
