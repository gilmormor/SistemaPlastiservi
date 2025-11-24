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

    $("#kg").val(aux_kg);
    $("#kgscrap").val(aux_kgscrap);

    aux_saldo = aux_kgrec - aux_kgprod;

    console.log("aux_kg: " + aux_kg);
    console.log("aux_kgscrap: " + aux_kgscrap);
    console.log("aux_saldo: " + aux_saldo);
    aux_suma = aux_kg + aux_kgscrap;
    console.log("aux_kg + aux_kgscrap: " + aux_suma);
    return {
        aux_saldo: aux_saldo,
        aux_kg: aux_kg,
        aux_kgscrap: aux_kgscrap,
        aux_suma: aux_suma
    };
}