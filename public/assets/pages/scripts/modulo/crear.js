$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#nombre").focus();

    if($("#stanvdc").val() == '1'){
        $("#aux_stanvdc").prop("checked", true);    
    }else{
        $("#aux_stanvdc").prop("checked", false);    
    }

    $("#aux_stanvdc").change(function() {
        estaSeleccionado = $("#aux_stanvdc").is(":checked");
        $("#stanvdc").val('0');
        if(estaSeleccionado){
            $("#stanvdc").val('1');
        }
    });
});
