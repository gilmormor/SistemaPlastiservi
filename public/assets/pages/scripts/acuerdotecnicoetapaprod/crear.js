$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#nombre").focus();

    /* Al abrir, cada fila queda con el combo "Contar tambien en" habilitado o no
       segun la unidad de salida que ya tenga guardada. */
    $('.selUnidadSalAt').each(function () {
        aplicarReglaContarEnAt($(this).data('etapa'));
    });
});

/**
 * Habilita o no el combo "Contar tambien en" de una etapa.
 *
 * Solo se pide cuando la etapa entrega en kilos: si entrega en unidades, la
 * cantidad de salida YA son las unidades y no hay nada que contar aparte. Al
 * deshabilitarlo se limpia, para no dejar configurado algo que no se usa.
 * Misma regla que el modal de otitemprogramacion.
 */
function aplicarReglaContarEnAt(etapa_id) {
    var salida = $('.selUnidadSalAt[data-etapa="' + etapa_id + '"]').val();
    var $cant  = $('.selUnidadCantAt[data-etapa="' + etapa_id + '"]');

    if (!$cant.length) { return; }

    var esKg = (typeof AT_UNIDAD_KG_ID !== 'undefined' && AT_UNIDAD_KG_ID !== null
                && String(salida) === String(AT_UNIDAD_KG_ID));

    if (esKg) {
        $cant.prop('disabled', false).attr('title', '');
    } else {
        $cant.val('').prop('disabled', true)
             .attr('title', 'Solo se pide cuando la etapa entrega en kilos.');
    }
}

/* La unidad de salida se puede cambiar en cualquier momento, asi que la regla se
   vuelve a aplicar en cada cambio. */
$(document).on('change', '.selUnidadSalAt', function () {
    aplicarReglaContarEnAt($(this).data('etapa'));
});

/* Un combo deshabilitado no viaja en el POST, pero si la etapa no esta marcada su
   unidad tampoco interesa: el servidor solo lee las unidades de las etapas
   marcadas (ver armarSyncEtapas). */

/**
 * Valida antes de enviar que toda etapa marcada tenga unidad de salida.
 *
 * Va en el click del boton y no en el submit del formulario porque jQuery
 * Validate maneja el submit y termina llamando al submit nativo, que no dispara
 * los manejadores de jQuery. El servidor valida igual (armarSyncEtapas): esto
 * solo evita el viaje de ida y vuelta.
 */
$(document).on('click', '#form-general button[type="submit"]', function (e) {
    var aux_faltan = 0;

    /* Al menos una etapa marcada. El select multiple que habia antes era required y
       el navegador no dejaba enviarlo vacio; con casillas hay que validarlo aqui, si
       no un guardado en blanco borraria la ruta productiva del producto. */
    if ($('#tabla-etapas-at').length && $('#tabla-etapas-at .chkEtapaAt:checked').length === 0) {
        e.preventDefault();
        alertify.error('Marque al menos una etapa de producción.');
        return false;
    }

    $('#tabla-etapas-at .chkEtapaAt:checked').each(function () {
        var aux_etapa = $(this).val();
        var $sal = $('.selUnidadSalAt[data-etapa="' + aux_etapa + '"]');
        if (!$sal.val()) {
            aux_faltan++;
            $sal.closest('tr').addClass('has-error');
        } else {
            $sal.closest('tr').removeClass('has-error');
        }
    });

    if (aux_faltan > 0) {
        e.preventDefault();
        alertify.error('Indique la unidad de salida en las ' + aux_faltan + ' etapa(s) marcadas que no la tienen.');
        return false;
    }
});
