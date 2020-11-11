$(document).ready(function () {
	Biblioteca.validacionGeneral('form-general');
	$('.datepicker').datepicker({
		language: "es",
		autoclose: true,
		todayHighlight: true
	}).datepicker("setDate");

	$(".numerico").numeric({ negative : false });
});



function ajaxRequest(data,url,funcion) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='guardarguiadesp'){
				if (respuesta.mensaje == "ok") {
					//alert(data['nfila']);
					$("#fila" + data['nfila']).remove();
					$("#myModalguiadesp").modal('hide');
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					Biblioteca.notificaciones('Registro no fue guardado.', 'Plastiservi', 'error');
				}
			}
			if(funcion=='guardarfactdesp'){
				if (respuesta.mensaje == "ok") {
					$("#fila" + data['nfila']).remove();
					$("#myModalnumfactura").modal('hide');
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					Biblioteca.notificaciones('Registro no fue guardado.', 'Plastiservi', 'error');
				}
			}
			if(funcion=='consultarguiadespachood'){
				if (respuesta.mensaje == "ok") {
					$("#guiadespacho").val(respuesta.despachoord.guiadespacho);
					$(".requeridos").keyup();
					$("#myModalguiadesp").modal('show');
				} else {
					Biblioteca.notificaciones('Registro no encontrado.', 'Plastiservi', 'error');
				}
			}
			if(funcion=='consultarnumfacturaod'){
				if (respuesta.mensaje == "ok") {
					//alert(respuesta.despachoord.numfactura);
					$("#numfactura").val(respuesta.despachoord.numfactura);
					$(".requeridos").keyup();
					$("#myModalnumfactura").modal('show');
				} else {
					Biblioteca.notificaciones('Registro no encontrado.', 'Plastiservi', 'error');
				}
			}
			if(funcion=='consultaranularguiafact'){
				if (respuesta.mensaje == "ok") {
					//alert(respuesta.despachoord.guiadespacho);
					$("#guiadespachoanul").val(respuesta.despachoord.guiadespacho);
					//$(".requeridos").keyup();
					quitarvalidacioneach();
					$("#myModalanularguiafact").modal('show');
				} else {
					Biblioteca.notificaciones('Registro no encontrado.', 'Plastiservi', 'error');
				}
			}
			
			if(funcion=='guardaranularguia'){
				if (respuesta.mensaje == "ok") {
					//alert(data['nfila']);
					$("#fila" + data['nfila']).remove();
					$("#myModalanularguiafact").modal('hide');
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					Biblioteca.notificaciones('Registro no fue guardado.', 'Plastiservi', 'error');
				}
			}

		},
		error: function () {
		}
	});
}

function guiadesp(nfila,id){
	$("#idg").val(id);
	$("#nfila").val(nfila);
	$("#guiadespacho").val('');
	var data = {
		id    : id,
		nfila : nfila,
		_token: $('input[name=_token]').val()
	};
	var ruta = '/despachoord/consultarod';
	ajaxRequest(data,ruta,'consultarguiadespachood');
}

$("#btnGuardarG").click(function(event)
{
	event.preventDefault();
	if(verificarGuia())
	{
		var data = {
			id    : $("#idg").val(),
			guiadespacho : $("#guiadespacho").val(),
			nfila : $("#nfila").val(),
			_token: $('input[name=_token]').val()
		};
		var ruta = '/despachoord/guardarguiadesp';
		swal({
			title: '¿ Seguro desea continuar ?',
			text: "Esta acción no se puede deshacer!",
				icon: 'warning',
			buttons: {
				cancel: "Cancelar",
				confirm: "Aceptar"
			},
		}).then((value) => {
			if (value) {
				ajaxRequest(data,ruta,'guardarguiadesp');
			}
		});

	}else{
		alertify.error("Falta incluir informacion");
	}
	
});

$("#btnGuardarGanul").click(function(event)
{
	event.preventDefault();
	if(verificarAnulGuia())
	{
		var data = {
			id    : $("#idanul").val(),
			nfila : $("#nfilaanul").val(),
			observacion : $("#observacionanul").val(),
			status : $("#status").val(),
			_token: $('input[name=_token]').val()
		};
		var ruta = '/guardaranularguia';
		swal({
			title: '¿ Seguro desea continuar ?',
			text: "Esta acción no se puede deshacer!",
				icon: 'warning',
			buttons: {
				cancel: "Cancelar",
				confirm: "Aceptar"
			},
		}).then((value) => {
			if (value) {
				ajaxRequest(data,ruta,'guardaranularguia');
			}
		});

	}else{
		alertify.error("Falta incluir informacion");
	}
	
});



function numfactura(nfila,id){
	$("#idf").val(id);
	$("#numfactura").val('');
	$("#fechafactura").val('');
	$("#nfilaf").val(nfila);
	var data = {
		id    : id,
		nfila : nfila,
		_token: $('input[name=_token]').val()
	};
	var ruta = '/despachoord/consultarod';
	ajaxRequest(data,ruta,'consultarnumfacturaod');
}

function anularguiafact(nfila,id){
	$("#idanul").val(id);
	$("#guiadespachoanul").val('');
	$("#nfilaanul").val(nfila);
	var data = {
		id    : id,
		nfila : nfila,
		_token: $('input[name=_token]').val()
	};
	var ruta = '/despachoord/consultarod';
	ajaxRequest(data,ruta,'consultaranularguiafact');
}

$("#btnGuardarF").click(function(event)
{
	event.preventDefault();
	if(verificarFact())
	{
		var data = {
			id    : $("#idf").val(),
			numfactura   : $("#numfactura").val(),
			fechafactura : $("#fechafactura").val(),
			nfila : $("#nfilaf").val(),
			_token: $('input[name=_token]').val()
		};
		var ruta = '/despachoord/guardarfactdesp';
		swal({
			title: '¿ Seguro desea continuar ?',
			text: "Esta acción no se puede deshacer!",
				icon: 'warning',
			buttons: {
				cancel: "Cancelar",
				confirm: "Aceptar"
			},
		}).then((value) => {
			if (value) {
				ajaxRequest(data,ruta,'guardarfactdesp');
			}
		});

	}else{
		alertify.error("Falta incluir informacion");
	}
	
});


$(".requeridos").keyup(function(){
	//alert($(this).parent().attr('class'));
	quitarValidacion($(this).prop('name'),$(this).attr('tipoval'));
});

$(".requeridos").change(function(){
	//alert($(this).parent().attr('class'));
	quitarValidacion($(this).prop('name'),$(this).attr('tipoval'));
});


function verificarGuia()
{
	var v1=0;
	
	v1=validacion('guiadespacho','texto');
	if (v1===false)
	{
		return false;
	}else{
		return true;
	}
}

function verificarFact()
{
	var v1=0;
	var v2=0;
	
	v1=validacion('numfactura','texto');
	v2=validacion('fechafactura','texto');
	if (v1===false || v2===false)
	{
		return false;
	}else{
		return true;
	}
}


function verificarAnulGuia()
{
	var v1=0;
	
	v2=validacion('status','combobox');
	v1=validacion('observacionanul','texto');
	if (v1===false || v2===false)
	{
		return false;
	}else{
		return true;
	}
}
