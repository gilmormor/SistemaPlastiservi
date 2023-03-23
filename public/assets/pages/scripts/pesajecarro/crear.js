$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
	$(".numerico").numeric({decimalPlaces: 2, negative : false });
    $("#nombre").focus();
});
