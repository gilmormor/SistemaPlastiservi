$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#nombre").focus();

    if($("#mostrarfact").val() == '1'){
        $("#aux_mostrarfact").prop("checked", true);    
    }else{
        $("#aux_mostrarfact").prop("checked", false);    
    }
});

$("#aux_mostrarfact").change(function() {
    estaSeleccionado = $("#aux_mostrarfact").is(":checked");
    $("#mostrarfact").val('0');
    if(estaSeleccionado){
        $("#mostrarfact").val('1');
    }
    //alert(estaSeleccionado);
    //mostrarfact
});
