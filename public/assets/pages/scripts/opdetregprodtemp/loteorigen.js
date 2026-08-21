/**
 * Selección manual de lote de origen (trazabilidad entre etapas).
 * Se muestra siempre que la etapa tenga una etapa anterior (aunque sea 1 solo
 * lote). Si es la primera etapa (sin etapa anterior), no se muestra y
 * Kg Producción / Kg Scrap se ingresan directo como siempre.
 *
 * Cuando se muestra: aux_kgprod y aux_kgscrap quedan deshabilitados (derivados),
 * el operario reparte producción y scrap por lote, y la suma de cada columna se
 * escribe en aux_kgprod / aux_kgscrap disparando su blur (validarsaldokg + sumarkg).
 * Lo que el operario no asigna en un lote NO se asume como scrap: simplemente
 * queda disponible para un próximo registro.
 */
var LOTEORIGEN_DATA = [];
var LOTEORIGEN_REQUERIDO = false;

$(document).ready(function () {
    refrescarLotesOrigen();

    $("#es_muestra").on("change.loteorigen", function () {
        refrescarLotesOrigen();
    });

    $("#tabla-lote-origen-body").on("input", ".lote-origen-input", function () {
        clampFilaLoteOrigen($(this));
        propagarTotalesADemanda();
    });

    // Si el operario ingresa solo ceros (0, 00, 0000000...), al salir del campo
    // se limpia a blanco: un cero explícito no debe interpretarse como una
    // asignación real a ese lote.
    $("#tabla-lote-origen-body").on("blur", ".lote-origen-input", function () {
        if (parseNumeroLocal($(this).val()) === 0) {
            $(this).val('');
        }
    });

    $("#form-general").on("submit", function (e) {
        if ($("#es_muestra").is(":checked")) return true; // R1: muestra física no requiere lote

        if (LOTEORIGEN_REQUERIDO && !validarLoteOrigenListo()) {
            e.preventDefault();
            swal({
                title: '',
                text: "Debe repartir los kg de producción y scrap entre los lotes de origen; la suma debe cuadrar exactamente.",
                icon: 'error',
                buttons: { confirm: "Aceptar" }
            });
            return false;
        }
    });
});

function refrescarLotesOrigen() {
    if ($("#es_muestra").is(":checked")) {
        $("#div-lote-origen").hide();
        LOTEORIGEN_REQUERIDO = false;
        habilitarCamposManual(true);
        return;
    }

    var opdetId = $("#opdet_id").val();
    var excluirTempId = $("#excluir_temp_id").val();
    if (!opdetId) return;

    $.get(LOTEORIGEN_URL_BASE + "/" + opdetId, { excluir_temp_id: excluirTempId })
        .done(function (resp) {
            LOTEORIGEN_DATA = resp.lotes || [];
            LOTEORIGEN_REQUERIDO = !!resp.requiere_seleccion;
            renderLotesOrigen();
        })
        .fail(function () {
            // Si falla la carga, no se puede asegurar que no hay etapa anterior:
            // se deja oculto pero LOTEORIGEN_REQUERIDO no se togglea a false aquí,
            // así el submit sigue bloqueado si ya se sabía que era requerido.
            $("#div-lote-origen").hide();
        });
}

function habilitarCamposManual(habilitar) {
    $("#aux_kgprod, #aux_kgscrap").prop("disabled", !habilitar);
}

function renderLotesOrigen() {
    if (!LOTEORIGEN_REQUERIDO) {
        $("#div-lote-origen").hide();
        $("#tabla-lote-origen-body").empty();
        habilitarCamposManual(true);
        return;
    }

    habilitarCamposManual(false);
    $("#lote-origen-badge").text(LOTEORIGEN_DATA.length + " lote(s) pendiente(s)");

    var $body = $("#tabla-lote-origen-body");
    $body.empty();

    var existentePorLote = {};
    (LOTEORIGEN_EXISTENTE || []).forEach(function (e) {
        existentePorLote[e.id] = e;
    });

    LOTEORIGEN_DATA.forEach(function (lote) {
        var prev = existentePorLote[lote.id];
        var prodPrev = prev ? prev.kg : 0;
        var scrapPrev = prev ? prev.scrap : 0;

        var $tr = $('<tr></tr>');
        $tr.append('<td><strong>#' + lote.id + '</strong></td>');
        $tr.append(
            '<td>registrado por ' + (lote.registrado_por || '—') +
            '<br><span style="color:#888;font-size:11px;">aprobado ' + (lote.fecha_hora || '—') + '</span></td>'
        );
        $tr.append('<td class="text-right lote-origen-disponible-cell">' + MASKLA(lote.disponible_kg, 2) + ' kg</td>');

        // Estos inputs usan el plugin jquery.numeric configurado con "." como
        // decimal (numeric('.')); su formato de VISUALIZACIÓN es por lo tanto
        // con punto decimal plano (no MASKLA, que usa coma — el plugin borra
        // cualquier carácter que no sea dígito o el separador configurado).
        var $inputProd = $('<input type="text" class="form-control lote-origen-input lote-origen-prod" style="text-align:right;" />')
            .attr('data-lote-id', lote.id)
            .attr('data-disponible', lote.disponible_kg)
            .val(prodPrev ? prodPrev.toFixed(2) : '');
        var $inputScrap = $('<input type="text" class="form-control lote-origen-input lote-origen-scrap" style="text-align:right;" />')
            .attr('data-lote-id', lote.id)
            .attr('data-disponible', lote.disponible_kg)
            .val(scrapPrev ? scrapPrev.toFixed(2) : '');

        $tr.append($('<td class="text-right"></td>').append($inputProd));
        $tr.append($('<td class="text-right"></td>').append($inputScrap));
        $body.append($tr);
    });

    // Los inputs se crean dinámicamente (después de document.ready), así que el
    // plugin jquery.numeric (que se aplica una sola vez a ".numerico" al cargar
    // la página) no los alcanza. Se aplica aquí explícitamente para que solo
    // permitan dígitos y separador decimal.
    if ($.fn.numeric) {
        $(".lote-origen-input").numeric({ decimal: '.', negative: false, decimalPlaces: 2 });
    }

    $("#div-lote-origen").show();
    propagarTotalesADemanda();
}

// Los inputs de la tabla de lotes usan punto decimal plano (jquery.numeric con
// decimal="."); otros campos del sistema usan formato latino (coma decimal,
// punto de miles). Se soportan ambos formatos aquí.
function parseNumeroLocal(v) {
    if (!v) return 0;
    v = String(v);
    if (v.indexOf(',') !== -1) {
        v = v.replace(/\./g, '').replace(',', '.');
    }
    return parseFloat(v) || 0;
}

/**
 * Topa el valor de un campo (producción o scrap) para que, junto con el otro
 * campo de la MISMA fila, no supere el disponible de ese lote.
 * No auto-rellena nada: solo evita que la suma de la fila exceda el saldo.
 */
function clampFilaLoteOrigen($input) {
    var $fila = $input.closest('tr');
    var disponible = parseFloat($input.data('disponible')) || 0;
    var $otro = $fila.find('.lote-origen-input').not($input);
    var valorOtro = parseNumeroLocal($otro.val());

    var valor = parseNumeroLocal($input.val());
    var maxPermitido = Math.max(disponible - valorOtro, 0);
    if (valor > maxPermitido) {
        valor = Math.round(maxPermitido * 100) / 100;
        // Punto decimal plano (no MASKLA): igual que el resto de este input,
        // consistente con jquery.numeric configurado con decimal=".".
        $input.val(valor > 0 ? valor.toFixed(2) : '');
    }
}

function propagarTotalesADemanda() {
    var sumaProd = 0;
    var sumaScrap = 0;
    $(".lote-origen-prod").each(function () { sumaProd += parseNumeroLocal($(this).val()); });
    $(".lote-origen-scrap").each(function () { sumaScrap += parseNumeroLocal($(this).val()); });
    sumaProd = Math.round(sumaProd * 100) / 100;
    sumaScrap = Math.round(sumaScrap * 100) / 100;

    // Escribir el valor CRUDO (decimal con ".", sin separador de miles) y disparar
    // blur: el handler ".numerico" de general.js (bound antes que este script) es
    // el que hace parseFloat + formatea a MASKLA y fija el atributo "valor"; si le
    // entregáramos ya formateado ("2.000,00") su propio parseFloat lo malinterpreta
    // (el "." se lee como decimal) y termina en NaN. Luego corre ".validarsaldokg"
    // (sumarkg) que sí depende de que "valor" ya haya quedado bien seteado.
    $("#aux_kgprod").val(sumaProd > 0 ? sumaProd : '');
    $("#aux_kgscrap").val(sumaScrap > 0 ? sumaScrap : '');
    $("#aux_kgprod").trigger("blur");
    $("#aux_kgscrap").trigger("blur");

    actualizarTotalesLoteOrigen(sumaProd, sumaScrap);
}

function actualizarTotalesLoteOrigen(sumaProd, sumaScrap) {
    var kgProdObjetivo = parseFloat($("#aux_kgprod").attr("valor")) || 0;
    var kgScrapObjetivo = parseFloat($("#aux_kgscrap").attr("valor")) || 0;

    $("#lote-origen-kg-registro").text(MASKLA(kgProdObjetivo, 2));
    $("#lote-origen-kg-asignado").text(MASKLA(sumaProd, 2));
    $("#lote-origen-scrap-registro").text(MASKLA(kgScrapObjetivo, 2));
    $("#lote-origen-scrap-asignado").text(MASKLA(sumaScrap, 2));

    var $alerta = $("#lote-origen-alerta");
    var cuadraProd = Math.abs(sumaProd - kgProdObjetivo) <= 0.01;
    var cuadraScrap = Math.abs(sumaScrap - kgScrapObjetivo) <= 0.01;
    var hayAsignacion = (sumaProd + sumaScrap) > 0;

    if (cuadraProd && cuadraScrap && hayAsignacion) {
        $alerta.removeClass('alert-danger').addClass('alert-success')
            .html('<i class="fa fa-check"></i> La producción y el scrap asignados por lote cuadran. Puede guardar.');
    } else {
        $alerta.removeClass('alert-success').addClass('alert-danger')
            .html('<i class="fa fa-exclamation-triangle"></i> Reparta la producción y el scrap entre los lotes hasta que ambas sumas cuadren.');
    }
}

function validarLoteOrigenListo() {
    if (LOTEORIGEN_DATA.length === 0) return false;

    var kgProdObjetivo = parseFloat($("#aux_kgprod").attr("valor")) || 0;
    var kgScrapObjetivo = parseFloat($("#aux_kgscrap").attr("valor")) || 0;
    var sumaProd = 0;
    var sumaScrap = 0;
    var ids = [];
    var kgs = [];
    var scraps = [];

    $("#tabla-lote-origen-body tr").each(function () {
        var loteId = $(this).find('.lote-origen-prod').data('lote-id');
        var kg = parseNumeroLocal($(this).find('.lote-origen-prod').val());
        var scrap = parseNumeroLocal($(this).find('.lote-origen-scrap').val());
        if (kg > 0 || scrap > 0) {
            ids.push(loteId);
            kgs.push(kg);
            scraps.push(scrap);
        }
        sumaProd += kg;
        sumaScrap += scrap;
    });
    sumaProd = Math.round(sumaProd * 100) / 100;
    sumaScrap = Math.round(sumaScrap * 100) / 100;

    if (ids.length === 0) return false;
    if (Math.abs(sumaProd - kgProdObjetivo) > 0.01) return false;
    if (Math.abs(sumaScrap - kgScrapObjetivo) > 0.01) return false;

    $("#form-general").find("input[name^='origen_lote_id'], input[name^='origen_kg'], input[name^='origen_scrap']").remove();
    ids.forEach(function (id, idx) {
        $("#form-general").append('<input type="hidden" name="origen_lote_id[]" value="' + id + '">');
        $("#form-general").append('<input type="hidden" name="origen_kg[]" value="' + kgs[idx] + '">');
        $("#form-general").append('<input type="hidden" name="origen_scrap[]" value="' + scraps[idx] + '">');
    });
    return true;
}
