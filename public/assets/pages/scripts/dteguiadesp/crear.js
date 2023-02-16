$(document).ready(function () {
	Biblioteca.validacionGeneral('form-general');
	aux_obs = $("#aux_obs").val();
	$("#obs").val(aux_obs);

	$('.tablas').DataTable({
		'paging'      : true, 
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		'autoWidth'   : false,
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
	});

	$("#mdialTamanio").css({'width': '50% !important'});
	//$(".control-label").css({'padding-top': '2px'});
	
	/*
    var styles = {
		backgroundColor : "#ddd",
		fontWeight: ""
	  };
	$( this ).css( styles );*/
	aux_sta = $("#aux_sta").val();
	//$("#rut").numeric();
	$("#cantM").numeric();
	$("#precioM").numeric({decimalPlaces: 2});
	$(".numerico").numeric();
	$("#auxeditcampoN").numeric(
		{
			decimalPlaces: 2,
			negative: false
		},
	);

	//$( "#myModal" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$( "#myModalBusqueda" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$( "#myModalBuscarProd" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$(".modal-body label").css("margin-bottom", -2);
	$(".help-block").css("margin-top", -2);
	if($("#aux_fechaphp").val()!=''){
		$("#fechahora").val($("#aux_fechaphp").val());
	}
	$("#auxeditcampoN").val(1);
	$("#auxeditcampoT").val("1");

	var dateToday = new Date(); 
	var date = new Date();
	var ultimoDia = new Date(date.getFullYear(), date.getMonth() + 1, 0);
	$("#fchemis").datepicker({
		language: "es",
		autoclose: true,
		endDate: ultimoDia,
		minDate: dateToday,
		startDate: new Date(),
		todayHighlight: true
	}).datepicker("setDate");

	$(".selectpicker").selectpicker('refresh');

});

function ajaxRequest(data,url,funcion) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='eliminar'){
				if (respuesta.mensaje == "ok" || data['id']=='0') {
					mensajeEliminarRegistro(data);
					/*
					$("#fila"+data['nfila']).remove();
					Biblioteca.notificaciones('El registro fue eliminado correctamente', 'Plastiservi', 'success');
					totalizar();*/
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso para eliminar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser eliminado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
			}
			if(funcion=='verUsuario'){
				$('#myModal .modal-body').html(respuesta);
				$("#myModal").modal('show');
			}
			if(funcion=='aprobarnvsup'){
				if (respuesta.mensaje == "ok") {
					Biblioteca.notificaciones('El registro fue actualizado correctamente', 'Plastiservi', 'success');
					// *** REDIRECCIONA A UNA RUTA*** 
					var loc = window.location;
    				window.location = loc.protocol+"//"+loc.hostname+"/notaventaaprobar";
					// ****************************** 
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no puso se actualizado.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no puso se actualizado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
			}
		},
		error: function () {
		}
	});
}

$(".requeridos").keyup(function(){
	//alert($(this).parent().attr('class'));
	validacion($(this).prop('name'),$(this).attr('tipoval'));
});
$(".requeridos").change(function(){
	//alert($(this).parent().attr('class'));
	validacion($(this).prop('name'),$(this).attr('tipoval'));
});


function editKilosa(id){
	quitarvalidacioneach();
	//console.log($(this).attr('valor'));
	aux_kilos = $("#aux_kilos" + id).attr('valor');
	$("#auxeditcampo").attr('fila_id',id);
	$("#auxeditcampo").val(aux_kilos.trim());
	$("#myModalEditarCampoNum").modal('show');
	return 0;
	let input = document.createElement("input");
	aux_kilos = $("#aux_kilos" + id).html();
	input.value = aux_kilos.trim();
	input.type = 'text';
	input.className = 'swal-content__input';
	input.onKeyPress="if ( isNaN( String.fromCharCode(event.keyCode) )) return false;"
	input.Id = "auxeditcampo";
	input.Name = "auxeditcampo";
	//$(".swal-content__input").numeric();

	var prueba = {
		element: "input",
		onKeyPress : "if ( isNaN( String.fromCharCode(event.keyCode) )) return false;",
		attributes: {
			value : aux_kilos.trim(),
			placeholder: "Ingrese los Kilos",
			className : 'swal-content__input numerico',
			type: "text",
		},
		
	};
	swal({
		text: "Editar Kilos",
		content: prueba,
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			if ( isNaN( String.fromCharCode(value) )){
				swal('Solo se permiten valores numericos.')
			}else{
				$("#aux_kilos" + id).html(input.value)
				$("#totalkilos" + id).val(input.value)
				$("#itemkg" + id).val(input.value)
			}
		}
	});	
}

$("#btnaceptarMN").click(function(event){
	event.preventDefault();
	$("#auxeditcampoT").val('1');
	if(verificarDato(".valorrequerido"))
	{
		id = $("#auxeditcampoN").attr('fila_id');
		if($("#auxeditcampoN").attr('aux_nomcampon') == "aux_kilos"){
			$("#aux_kilos" + id).html($("#auxeditcampoN").val());
			$("#aux_kilos" + id).attr('valor', $("#auxeditcampoN").val());
			$("#totalkilos" + id).val($("#auxeditcampoN").val());
			$("#totalkilos" + id).attr('valor', $("#auxeditcampoN").val());
			$("#itemkg" + id).val($("#auxeditcampoN").val());	
		}

		if($("#auxeditcampoN").attr('aux_nomcampon') == "qtyitemlbl"){
			$("#qtyitemlbl" + id).attr('valor', $("#auxeditcampoN").val());
			$("#qtyitemlbl" + id).html($("#auxeditcampoN").val());
			$("#cant" + id).val($("#auxeditcampoN").val());
			$("#qtyitem" + id).val($("#auxeditcampoN").val());	
		}

		if($("#auxeditcampoN").attr('aux_nomcampon') == "unmditemlbl"){
			$("#unmditemlbl" + id).attr('valor', $("#auxeditcampoN").val());
			$("#unmditemlbl" + id).html($("#auxeditcampoN").val());
			$("#unmditem" + id).val($("#auxeditcampoN").val());
		}


		$("#myModalEditarCampoNum").modal('hide');
	}else{
		alertify.error("Falta incluir informacion");
	}
});

$(".editarcampoNum").click(function()
{
	quitarvalidacioneach();
	id = $(this).attr('fila');
	aux_valor = $(this).attr('valor');
	$("#auxeditcampoN").attr('aux_nomcampon',$(this).attr('nomcampo'));
	$("#auxeditcampoN").attr('fila_id',id);
	$("#auxeditcampoN").val(aux_valor.trim());
	$("#myModalEditarCampoNum").modal('show');
});


$(".editarcampoTex").click(function()
{
	quitarvalidacioneach();
	id = $(this).attr('fila');
	aux_valor = $(this).attr('valor');
	$("#auxeditcampoT").attr('aux_nomcampot',$(this).attr('nomcampo'));
	$("#auxeditcampoT").attr('fila_id',id);
	$("#auxeditcampoT").val(aux_valor.trim());
	$("#myModalEditarCampoTex").modal('show');
});

$("#btnaceptarMT").click(function(event){
	event.preventDefault();
	$("#auxeditcampoN").val(1);
	if(verificarDato(".valorrequerido"))
	{
		let auxeditcampoT = $("#auxeditcampoT").val();
		//esto es para reemplazar el caracter comilla doble " de la cadena, para evitar que me trunque los valores en javascript al asignar a attr val 
		auxeditcampoT = auxeditcampoT.replaceAll('"', "'");
		id = $("#auxeditcampoT").attr('fila_id');
		if($("#auxeditcampoT").attr('aux_nomcampot') == "producto_nombre"){
			$("#producto_nombre" + id).html(auxeditcampoT);
			$("#producto_nombre" + id).attr('valor', auxeditcampoT);
			$("#nmbitem" + id).val(auxeditcampoT);			
		}
		if($("#auxeditcampoT").attr('aux_nomcampot') == "unmditemlbl"){
			$("#unmditemlbl" + id).html(auxeditcampoT);
			$("#unmditemlbl" + id).attr('valor', auxeditcampoT);
			$("#unmditem" + id).val(auxeditcampoT);			
		}
		$("#myModalEditarCampoTex").modal('hide');
	}else{
		alertify.error("Falta incluir informacion");
	}
});

$(".editarqtyitemlbl").click(function()
{
	quitarvalidacioneach();
	id = $(this).attr('fila');
	aux_valor = $("#qtyitem" + id).val();
	$("#auxeditcampoN").attr('fila_id',id);
	$("#auxeditcampoN").val(aux_valor.trim());
	$("#myModalEditarCampoNum").modal('show');

});


function verificarDato(aux_nomclass)
{
	aux_resultado = true;
	$(aux_nomclass).serializeArray().map(function(x){
		aux_tipoval = $("#" + x.name).attr('tipoval');
		if (validacion(x.name,aux_tipoval) == false)
		{
			//return false;
			aux_resultado = false;

		}else{
			//return true;
		}
	});
	return aux_resultado;
}

$(".valorrequerido").keyup(function(){
	//alert($(this).parent().attr('class'));
	validacion($(this).prop('name'),$(this).attr('tipoval'));
});

$(".valorrequerido").change(function(){
	//alert($(this).parent().attr('class'));
	validacion($(this).prop('name'),$(this).attr('tipoval'));
});

$('#form-general').submit(function(event) {
	$("#auxeditcampoN").val(1);
	$("#auxeditcampoT").val("1");

	//event.preventDefault();
})