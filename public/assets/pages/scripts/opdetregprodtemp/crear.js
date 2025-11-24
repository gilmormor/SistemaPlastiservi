$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#nombre").focus();

    $(".validarsaldokg").blur(function(e){
        let datos = sumarkg();
        console.log("Valor this: " + $(this).attr("valor"));
        aux_val = 0;
        let valorStr = $(this).attr("valor");
        let aux_valor = parseFloat(valorStr) || 0;
        console.log(aux_valor);
        console.log('Unidad Medida:' + $("#unidadmedida_id").val());
        console.log('Suma:' + datos.aux_suma);
        console.log('Saldo:' + datos.aux_saldo);
        aux_unidadmedida_id = $("#unidadmedida_id").val();
        if (aux_unidadmedida_id == 7){  //SI LA UNIDAD DE MEDIDA ES KG 
            $("#aux_cant").attr("valor",$("#aux_kg").attr("valor"));
            $("#aux_cant").val($("#aux_kg").val());
            $("#cant").val($("#aux_kg").attr("valor"));
        }

        if(datos.aux_suma > datos.aux_saldo){
            swal({
                title: '',
                text: "La suma de Kg y Kg Scrap no puede ser mayor al Kg Faltante.",
                icon: 'error',
                buttons: {
                    confirm: "Aceptar"
                },
            }).then((value) => {
                if (value) {
                    if($(this).attr("id") == "aux_kg"){
                        $("#aux_cant").attr("valor","0");
                        $("#aux_cant").val("0,00");
                        $("#cant").val("0");
                    }

                    $("#" + $(this).attr("nomcamp")).val(0); //ASIGNO EL VALOR 0 AL CAMPO CORRESPONDIENTE
                    $(this).val("");
                    $(this).attr("valor", "");
                    $(this).focus();
                }
            });
        }
    });
});

function sumarkg(){
    let valorkgrecStr = $("#kgrec").attr("valor");
    let aux_kgrec = parseFloat(valorkgrecStr) || 0;

    let valorkgprodStr = $("#kgprod").attr("valor");
    let aux_kgprod = parseFloat(valorkgprodStr) || 0;

    let valorStr = $("#saldokg").attr("valor");
    let aux_saldo = parseFloat(valorStr) || 0;

    let valorStrkg = $("#aux_kg").attr("valor");
    let aux_kg = parseFloat(valorStrkg) || 0;

    let valorStrkgscrap = $("#aux_kgscrap").attr("valor");
    let aux_kgscrap = parseFloat(valorStrkgscrap) || 0;

    let valorcantrec = $("#cantrec").val();
    let aux_cantrec = parseFloat(valorcantrec) || 0;

    let valorunidadmedida_id = $("#unidadmedida_id").val();
    let aux_unidadmedida_id = parseFloat(valorunidadmedida_id) || 0;


    $("#kg").val(aux_kg);
    $("#kgscrap").val(aux_kgscrap);

    aux_saldo = aux_kgrec - aux_kgprod;

    console.log("aux_kg: " + aux_kg);
    console.log("aux_kgscrap: " + aux_kgscrap);
    console.log("aux_saldo: " + aux_saldo);
    aux_suma = aux_kg + aux_kgscrap;
    console.log("aux_kg + aux_kgscrap: " + aux_suma);
    console.log("aux_unidadmedida_id: " + aux_unidadmedida_id);
    if(aux_unidadmedida_id == 7){ //SI LA UNIDAD DE MEDIDA ES KG
        $("#aux_cant").attr("valor",aux_kg);
        $("#aux_cant").val(MASKLA(aux_kg,2));
        $("#cant").val(aux_kg);
    }else{
        aux_cantunidades = (aux_kg * aux_cantrec) / aux_kgrec;
        console.log("aux_cantunidades: " + aux_cantunidades);
        $("#aux_cant").attr("valor",aux_cantunidades);
        $("#aux_cant").val(MASKLA(aux_cantunidades,2));
        $("#cant").val(aux_cantunidades);
    }
    return {
        aux_saldo: aux_saldo,
        aux_kg: aux_kg,
        aux_kgscrap: aux_kgscrap,
        aux_suma: aux_suma
    };
}