$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#doc").focus();
	if($("#activo").val()=="1"){
		$("#activoT").prop("checked", true);
	}else{
		$("#activoT").prop("checked", false);
	}
	$('.numerico').numeric('.');
});

$("#activoT").click(function(event)
{
    if($("#activoT").prop("checked")){
		$("#activo").val('1');
	}else{
		$("#activo").val('0');
	}
});