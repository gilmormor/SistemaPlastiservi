$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

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
					$("#myModalguiadesp").modal('show');
				} else {
					Biblioteca.notificaciones('Registro no encontrado.', 'Plastiservi', 'error');
				}
			}
			if(funcion=='consultarnumfacturaod'){
				if (respuesta.mensaje == "ok") {
					//alert(respuesta.despachoord.numfactura);
					$("#numfactura").val(respuesta.despachoord.numfactura);
					$("#myModalnumfactura").modal('show');
				} else {
					Biblioteca.notificaciones('Registro no encontrado.', 'Plastiservi', 'error');
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


function numfactura(nfila,id){
	$("#idf").val(id);
	$("#numfactura").val('');
	$("#nfilaf").val(nfila);
	var data = {
		id    : id,
		nfila : nfila,
		_token: $('input[name=_token]').val()
	};
	var ruta = '/despachoord/consultarod';
	ajaxRequest(data,ruta,'consultarnumfacturaod');
}

$("#btnGuardarF").click(function(event)
{
	event.preventDefault();
	if(verificarFact())
	{
		var data = {
			id    : $("#idf").val(),
			numfactura : $("#numfactura").val(),
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
	validacion($(this).prop('name'),$(this).attr('tipoval'));
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
	
	v1=validacion('numfactura','texto');
	if (v1===false)
	{
		return false;
	}else{
		return true;
	}
}
