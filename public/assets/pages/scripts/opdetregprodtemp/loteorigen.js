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

    /* Cambiar de máquina cambia el margen: la que viene programada puede no ser
       la que el operario usó, y el margen es de la máquina REAL. Si pasa a una
       con menos margen (o sin él), lo ya escrito se vuelve a topar; si pasa a
       una con más, no se toca nada de lo que escribió. */
    $(document).on("change", "#maquina_id", function () {
        $(".lote-origen-input").each(function () {
            clampFilaLoteOrigen($(this));
        });
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
        /* El numero de lote abre su etiqueta, para que el operario la compare
           con la que viene pegada en el producto que va a procesar. Usa la
           misma funcion y el mismo modal que op/seguimiento y las pantallas
           de CC, con su validacion de permiso. */
        $tr.append(
            '<td><a class="btn-accion-tabla tooltipsC" style="cursor:pointer;" ' +
            'title="Ver la etiqueta de este lote" ' +
            'onclick="verEtiquetaEtapaConPermiso(' + lote.id + ')">' +
            '<strong>#' + lote.id + '</strong></a></td>'
        );
        $tr.append(
            '<td>registrado por ' + (lote.registrado_por || '—') +
            '<br><span style="color:#888;font-size:11px;">aprobado ' + (lote.fecha_hora || '—') + '</span></td>'
        );
        /* Junto a los kg se muestra cuantas unidades llegaron de la etapa
           anterior, en su propia UM. Informativo: se reparte en kg. */
        var textoDisp = MASKLA(lote.disponible_kg, 2) + ' kg';

        /* Si la etapa anterior conto unidades aparte, se muestran esas: son
           el dato real. disponible_cant sale de cantprod/kgprod, que en una
           etapa que salio en kilos vale 1 y mostraria los kilos otra vez. */
        if (lote.disponible_unid !== null && lote.disponible_unid !== undefined) {
            textoDisp += '<br><span style="color:#777;font-size:11px;">' +
                         MASKLA(lote.disponible_unid, 2) + ' ' +
                         (lote.um_cont_nombre || 'unid') + '</span>';
        } else if (lote.um_nombre && String(lote.um_nombre).toLowerCase() !== 'kgs') {
            textoDisp += '<br><span style="color:#777;font-size:11px;">' +
                         MASKLA(lote.disponible_cant, 2) + ' ' + lote.um_nombre + '</span>';
        }
        $tr.append('<td class="text-right lote-origen-disponible-cell">' + textoDisp + '</td>');

        // Estos inputs usan el plugin jquery.numeric configurado con "." como
        // decimal (numeric('.')); su formato de VISUALIZACIÓN es por lo tanto
        // con punto decimal plano (no MASKLA, que usa coma — el plugin borra
        // cualquier carácter que no sea dígito o el separador configurado).
        var $inputProd = $('<input type="text" class="form-control lote-origen-input lote-origen-prod" style="text-align:right;" />')
            .attr('data-lote-id', lote.id)
            .attr('data-disponible', lote.disponible_kg)
            // relacion unidad/kg del lote, para convertir los kg asignados
            .attr('data-unidxkg', lote.unid_por_kg || 0)
            // relacion de las unidades contadas aparte, si el lote las tiene
            .attr('data-unidxkg-cont', lote.unid_por_kg_cont || 0)
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
 * Kg que la máquina elegida puede procesar por encima del lote.
 *
 * Las extrusoras sin cuenta metros no avisan cuándo se llegó a los kilos del
 * lote, y el depósito nunca queda vacío: el operario corta a ojo y se pasa unos
 * kilos, que salen del lote siguiente. Ese margen se configura por máquina y
 * aquí se suma al disponible para que la pantalla deje escribirlos.
 *
 * Vale 0 cuando no hay máquina elegida o no está configurada, que es el caso
 * normal: ahí el tope es el de siempre.
 */
function excesoMaquina() {
    var $sel = $('#maquina_id');
    if (!$sel.length) { return 0; }
    var aux = parseFloat($sel.find('option:selected').data('exceso'));
    return isNaN(aux) ? 0 : aux;
}

/**
 * Topa el valor de un campo (producción o scrap) para que, junto con el otro
 * campo de la MISMA fila, no supere el disponible de ese lote más el margen de
 * la máquina (ver excesoMaquina).
 * No auto-rellena nada: solo evita que la suma de la fila exceda ese tope.
 */
function clampFilaLoteOrigen($input) {
    var $fila = $input.closest('tr');
    var disponible = parseFloat($input.data('disponible')) || 0;
    var $otro = $fila.find('.lote-origen-input').not($input);
    var valorOtro = parseNumeroLocal($otro.val());

    var valor = parseNumeroLocal($input.val());
    var maxPermitido = Math.max(disponible + excesoMaquina() - valorOtro, 0);
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

/**
 * Unidades que representan los kg de produccion asignados a los lotes.
 * Cada lote aporta sus propias unidades por kilo, porque pueden venir de
 * registros distintos de la etapa anterior.
 * Devuelve null si ningun lote tiene la relacion cargada (no hay de donde
 * calcular y quien llame decidira que hacer).
 */
/**
 * Suma las unidades CONTADAS que aportan los lotes elegidos, segun los kg
 * que se tomen de cada uno.
 *
 * Es la contraparte de unidadesDesdeLotesOrigen() para el campo de unidades
 * del registro: aquella convierte a la unidad de salida del lote, esta usa
 * las unidades que el operario conto aparte en la etapa anterior.
 *
 * Devuelve null si ningun lote las tiene, para que quien llame no escriba un
 * cero que parezca un dato.
 */
function unidadesContadasDesdeLotesOrigen() {
    var total = 0;
    var hayDato = false;
    $(".lote-origen-prod").each(function () {
        var kg    = parseNumeroLocal($(this).val());
        var ratio = parseFloat($(this).attr('data-unidxkg-cont')) || 0;
        if (ratio > 0) { hayDato = true; }
        total += kg * ratio;
    });
    return hayDato ? total : null;
}

function unidadesDesdeLotesOrigen() {
    var total = 0;
    var hayDato = false;
    $(".lote-origen-prod").each(function () {
        var kg    = parseNumeroLocal($(this).val());
        var ratio = parseFloat($(this).attr('data-unidxkg')) || 0;
        if (ratio > 0) { hayDato = true; }
        total += kg * ratio;
    });
    return hayDato ? total : null;
}

function actualizarTotalesLoteOrigen(sumaProd, sumaScrap) {
    /* Cambio el reparto entre lotes: las unidades que vienen de ellos
       cambian tambien, asi que se vuelve a sugerir. */
    if (typeof sugerirUnidadesContadas === 'function') {
        sugerirUnidadesContadas();
    }

    var kgProdObjetivo = parseFloat($("#aux_kgprod").attr("valor")) || 0;
    var kgScrapObjetivo = parseFloat($("#aux_kgscrap").attr("valor")) || 0;

    $("#lote-origen-kg-registro").text(MASKLA(kgProdObjetivo, 2));
    $("#lote-origen-kg-asignado").text(MASKLA(sumaProd, 2));
    $("#lote-origen-scrap-registro").text(MASKLA(kgScrapObjetivo, 2));
    $("#lote-origen-scrap-asignado").text(MASKLA(sumaScrap, 2));

    pintarPieLoteOrigen(sumaProd, sumaScrap);

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

/**
 * Elige qué mostrar en el pie del bloque de lotes.
 *
 * Con UN solo lote, el control de reparto es tautológico: lo que el operario
 * escribe en la fila se copia al campo Kg Producción, así que los dos números
 * coinciden siempre. En su lugar se muestra el saldo del lote: cuánto tenía,
 * cuánto se lleva este registro y cuánto queda para los siguientes.
 *
 * Con varios lotes vuelve el control del reparto, que ahí sí evita que la suma
 * se quede corta o se pase.
 */
function pintarPieLoteOrigen(sumaProd, sumaScrap) {
    var unico = (LOTEORIGEN_DATA.length === 1);

    $("#lote-origen-pie-saldo").toggle(unico);
    $("#lote-origen-pie-reparto").toggle(!unico);
    if (!unico) { return; }

    var lote      = LOTEORIGEN_DATA[0];
    var disponible = parseFloat(lote.disponible_kg) || 0;
    var usa        = Math.round((sumaProd + sumaScrap) * 100) / 100;

    /* Lo que se tome por encima del saldo no sale de este lote sino del
       siguiente, ya cargado en la máquina (margen por máquina). El lote queda
       en 0, nunca en negativo, y el exceso se señala aparte. */
    var queda  = Math.round((disponible - usa) * 100) / 100;
    var exceso = 0;
    if (queda < 0) {
        exceso = Math.abs(queda);
        queda  = 0;
    }

    $("#lote-origen-saldo-nro").text('#' + lote.id);
    $("#lote-origen-saldo-disp").text(MASKLA(disponible, 2));
    $("#lote-origen-saldo-usa").text(MASKLA(usa, 2));
    $("#lote-origen-saldo-queda").text(MASKLA(queda, 2));

    if (exceso > 0) {
        $("#lote-origen-saldo-exceso")
            .html('<i class="fa fa-warning"></i> ' + MASKLA(exceso, 2) +
                  ' kg de más, del lote siguiente en la máquina')
            .show();
    } else {
        $("#lote-origen-saldo-exceso").hide();
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
