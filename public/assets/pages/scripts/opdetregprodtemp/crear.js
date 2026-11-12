$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#nombre").focus();

    // Si la unidad de salida del producto es Kg, la cantidad producida es el
    // mismo valor de Kg Produccion: se muestra calculada y no se vuelve a pedir.
    if (umSalidaEsKg()) {
        $("#aux_cantprod").prop("readonly", true)
                           .css("background-color", "#eee")
                           .attr("title", "La unidad de salida es Kg: toma el valor de Kg Produccion");
    }

    /* Unidades que representan los kilos: el operario las escribe y se copian
       al hidden, igual que la cantidad producida. El campo solo existe en las
       etapas que piden contar en otra unidad; si no esta, el blur no se dispara
       y el hidden queda vacio, que es lo correcto. */
    $("#aux_cantunid").blur(function(){
        let v = parseFloat($(this).attr("valor")) || 0;
        $("#cantunid").val(v);
        // conteo propio del operario: la sugerencia no vuelve a pisarlo
        $(this).attr("data-tocado", "1");
    });

    // Si el operario edita aux_cantprod manualmente (caso UM salida != kg),
    // replicamos su valor en el hidden cantprod que es lo que se graba en BD y
    // marcamos el campo para que el calculo automatico no lo pise despues.
    $("#aux_cantprod").blur(function(){
        let v = parseFloat($(this).attr("valor")) || 0;
        $("#cantprod").val(v);
        if (!umSalidaEsKg()) {
            $(this).attr("data-tocado", "1");
        }
    });

    $(".validarsaldokg").blur(function(e){
        let datos = sumarkg();
        let valorStr = $(this).attr("valor");
        let aux_valor = parseFloat(valorStr) || 0;

        // R1: si es muestra física no se valida el saldo — no consume producción
        if($("#es_muestra").is(":checked")){
            return;
        }

        if(datos.aux_suma > datos.aux_saldo){
            swal({
                title: '',
                text: "La suma de Kg Producción y Kg Scrap no puede ser mayor al Kg Faltante.",
                icon: 'error',
                buttons: {
                    confirm: "Aceptar"
                },
            }).then((value) => {
                if (value) {
                    if($(this).attr("id") == "aux_kgprod"){
                        $("#aux_cantprod").attr("valor","0");
                        $("#aux_cantprod").val("0,00");
                        $("#cantprod").val("0");
                    }

                    $("#" + $(this).attr("nomcamp")).val(0);
                    $(this).val("");
                    $(this).attr("valor", "");
                    $(this).focus();
                }
            });
        }
    });
});

/* La unidad de salida manda sobre la cantidad producida. Sale del hidden
   unidadmedidasal_id, que el servidor llena con la UM configurada para este
   producto en acuerdotecnicoapsucetapaprod (id 7 = Kgs). */
/**
 * Sugiere las unidades del registro a partir de los lotes de origen.
 *
 * Cuando la etapa entrega en kilos pero igual se cuentan unidades, esas
 * unidades ya vienen contadas de la etapa anterior: se toman de los lotes
 * que el operario asigno, proporcional a los kg que saca de cada uno.
 *
 * No se toca nada si el campo no existe -la etapa no pide contar-, si el
 * operario ya escribio su propio conteo, o si los lotes no traen el dato:
 * en ese ultimo caso lo escribe el, que es mejor que mostrarle un cero.
 */
function sugerirUnidadesContadas() {
    var $campo = $("#aux_cantunid");
    if (!$campo.length) { return; }
    if ($campo.attr("data-tocado") == "1") { return; }
    if (typeof unidadesContadasDesdeLotesOrigen !== 'function') { return; }

    var total = unidadesContadasDesdeLotesOrigen();
    if (total === null || !(total > 0)) { return; }

    var v = Math.round(total);
    $campo.attr("valor", v);
    $campo.val(MASKLA(v, 2));
    $("#cantunid").val(v);
    console.log("unidades sugeridas desde lotes de origen: " + v);
}
function umSalidaId(){
    return parseFloat($("#unidadmedidasal_id").val()) || 0;
}

function umSalidaEsKg(){
    return umSalidaId() == 7;
}

function umEntradaId(){
    return parseFloat($("#unidadmedidaent_id").val()) || 0;
}

function sumarkg(){
    // aux_kgprod_opdet = kg ya producidos totales en el opdet (display, no hidden)
    let valorkgprodOpdetStr = $("#aux_kgprod_opdet").attr("valor");
    let aux_kgprod_opdet = parseFloat(valorkgprodOpdetStr) || 0;

    let valorStr = $("#saldokg").attr("valor");
    let aux_saldo = parseFloat(valorStr) || 0;

    // Operario ingresa: kgprod (producidos buenos) y kgscrap
    // kgent = kgprod + kgscrap  (se calcula y se manda en hidden)
    let valorStrkgprod = $("#aux_kgprod").attr("valor");
    let aux_kgprod = parseFloat(valorStrkgprod) || 0;

    let valorStrkgscrap = $("#aux_kgscrap").attr("valor");
    let aux_kgscrap = parseFloat(valorStrkgscrap) || 0;

    let aux_kgent = aux_kgprod + aux_kgscrap;

    let valorkgrecStr = $("#kgrec").attr("valor");
    let aux_kgrec = parseFloat(valorkgrecStr) || 0;

    let valorcantrec = $("#cantrec").val();
    let aux_cantrec = parseFloat(valorcantrec) || 0;

    let valorunidadmedidaent_id = $("#unidadmedidaent_id").val();
    let aux_unidadmedidaent_id = parseFloat(valorunidadmedidaent_id) || 0;

    // Actualizar hiddens
    $("#kgprod").val(aux_kgprod);
    $("#kgscrap").val(aux_kgscrap);
    $("#kgent").val(aux_kgent);   // kgent = kgprod + kgscrap (calculado)

    // cantent = kgent cuando UM entrada es kg
    if(aux_unidadmedidaent_id == 7){
        $("#cantent").val(aux_kgent);
    }

    // Saldo = kgrec - kgprod_opdet (el opdet.saldokg ya lo calcula el servidor,
    // aquí usamos el valor precomputado en el atributo valor del campo saldokg)
    aux_saldo = parseFloat($("#saldokg").attr("valor")) || 0;

    /* console.log("aux_kgprod: " + aux_kgprod);
    console.log("aux_kgscrap: " + aux_kgscrap);
    console.log("aux_kgent (calculado): " + aux_kgent);
    console.log("aux_saldo: " + aux_saldo); */

    // aux_suma = lo que se consumirá del saldo = kgent
    let aux_suma = aux_kgent;

    // Calcular cantprod segun la UM de SALIDA del producto (antes se miraba la
    // de entrada: coincidian solo porque ambas salian de la misma etapa).
    if(umSalidaEsKg()){ // UM salida = kg → cantprod = kgprod, no se pide aparte
        $("#cantent").val(aux_kgent);
        $("#aux_cantprod").attr("valor", aux_kgprod);
        $("#aux_cantprod").val(MASKLA(aux_kgprod, 2));
        $("#cantprod").val(aux_kgprod);
    } else {
        $("#cantent").val(aux_kgent);
        /* La cantidad la ingresa el operario. Si ya escribio un valor no se
           toca; si todavia no, se le sugiere el convertido desde los kg. */
        if($("#aux_cantprod").attr("data-tocado") == "1"){
            return {
                aux_saldo: aux_saldo,
                aux_kgprod: aux_kgprod,
                aux_kgscrap: aux_kgscrap,
                aux_suma: aux_suma
            };
        }
        $("#aux_cantprod").attr("valor","");
        $("#aux_cantprod").val("");
        $("#cantprod").val("");
        /* Si la etapa recibe y entrega en la MISMA unidad, las unidades ya
           vienen contadas de la etapa anterior: se toman de los lotes que el
           operario asigno, proporcional a los kg que saco de cada uno. El peso
           unitario del acuerdo tecnico no sirve aca porque es un estimado de
           fabricacion, no lo que realmente llego. */
        let aux_desdeLotes = (umEntradaId() == umSalidaId() && typeof unidadesDesdeLotesOrigen === 'function')
                             ? unidadesDesdeLotesOrigen()
                             : null;
        if (aux_desdeLotes !== null && aux_desdeLotes > 0) {
            let aux_cantlotes = Math.round(aux_desdeLotes);
            $("#aux_cantprod").attr("valor", aux_cantlotes);
            $("#aux_cantprod").val(MASKLA(aux_cantlotes, 2));
            $("#cantprod").val(aux_cantlotes);
            console.log("cant desde lotes de origen: " + aux_cantlotes);
        } else if(umSalidaId() == 5){ // salida en unidades: convertir desde los kg
            let aux_cantunidades = 0;
            let peso_unit = (typeof PRODUCTO_PESO_UNITARIO !== 'undefined') ? parseFloat(PRODUCTO_PESO_UNITARIO) : 0;
            if (peso_unit > 0) {
                aux_cantunidades = aux_kgprod / peso_unit;
                console.log("conv por peso_unitario (" + peso_unit + "): aux_cantunidades=" + aux_cantunidades);
            } else if (aux_kgrec > 0 && aux_cantrec > 0) {
                aux_cantunidades = (aux_kgprod * aux_cantrec) / aux_kgrec;
                console.log("conv por regla de tres: aux_cantunidades=" + aux_cantunidades);
            } else {
                let manualCant = parseFloat($("#aux_cantprod").attr("valor")) || 0;
                aux_cantunidades = manualCant;
                console.log("sin conversion automatica, usar manual: " + aux_cantunidades);
            }
            //aux_cantunidades = Math.round(aux_cantunidades * 100) / 100;
            aux_cantunidades = Math.round(aux_cantunidades);
            $("#aux_cantprod").attr("valor", aux_cantunidades);
            $("#aux_cantprod").val(MASKLA(aux_cantunidades, 2));
            $("#cantprod").val(aux_cantunidades);
        }
    }
    return {
        aux_saldo: aux_saldo,
        aux_kgprod: aux_kgprod,
        aux_kgscrap: aux_kgscrap,
        aux_suma: aux_suma   // = kgent = lo que descuenta del saldo
    };
}
