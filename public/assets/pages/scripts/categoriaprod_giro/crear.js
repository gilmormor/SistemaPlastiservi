$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#nombre").focus();
    //$("#cantM").numeric();
    $(".numericopositivosindec").numeric({decimalPlaces: 2, negative : false });
});
