$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#nombre").focus();

    if($("#stacieinimes").val() == '1'){
        $("#aux_stacieinimes").prop("checked", true);    
    }else{
        $("#aux_stacieinimes").prop("checked", false);    
    }
});

$("#aux_stacieinimes").change(function() {
    estaSeleccionado = $("#aux_stacieinimes").is(":checked");
    $("#stacieinimes").val('0');
    if(estaSeleccionado){
        $("#stacieinimes").val('1');
    }
});