$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#nombre").focus();

    // Si el operario edita aux_cantprod manualmente (caso UM salida != kg sin peso_unitario),
    // replicamos su valor en el hidden cantprod que es lo que se graba en BD.
    $("#aux_cantprod").blur(function(){
        let v = parseFloat($(this).attr("valor")) || 0;
        $("#cantprod").val(v);
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

    // Calcular cantprod según UM salida
    if(aux_unidadmedidaent_id == 7){ // UM entrada = kg → cantprod = kgprod
        $("#cantent").val(aux_kgent);
        $("#aux_cantprod").attr("valor", aux_kgprod);
        $("#aux_cantprod").val(MASKLA(aux_kgprod, 2));
        $("#cantprod").val(aux_kgprod);
    } else {
        $("#aux_cantprod").attr("valor","");
        $("#aux_cantprod").val("");
        $("#cantprod").val("");
        $("#cantent").val(aux_kgent);
        if(aux_unidadmedidaent_id == 5){ // UM entrada = unidades con conversión
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
