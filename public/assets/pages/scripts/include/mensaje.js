$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    //$("#divmensaje").hide();
    if($("#mensaje").html()){
        //alert($("#mensaje").html());
        Biblioteca.notificaciones($("#mensaje").html(), 'Plastiservi', 'error');
    }  
});