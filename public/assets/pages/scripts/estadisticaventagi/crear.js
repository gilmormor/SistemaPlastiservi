$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
	$('.datepicker').datepicker({
        language: "es",
        autoclose: true,
        todayHighlight: true
	}).datepicker("setDate");
	//$("#rut").numeric();
	//$( "#myModal" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$(".numerico").numeric();
	$('#razonsocial').toUpperCase();
});

function mayus(e) {
    e.value = e.value.toUpperCase();
}