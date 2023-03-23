$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    aux_nfilas=parseInt($("#dataTables >tbody >tr").length);
    //alert(aux_nfilas);
    $("#nombre").focus();
});