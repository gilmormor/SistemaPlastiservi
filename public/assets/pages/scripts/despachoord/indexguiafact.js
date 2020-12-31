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
					if(data['status']=='1'){
						$("#fila" + data['nfila']).remove();
					}else{
						$("#guiadespacho" + data['nfila']).html(respuesta.despachoord.guiadespacho);
						$("#fechaguia" + data['nfila']).html(respuesta.guiadespachofec);	
					}
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					Biblioteca.notificaciones('Registro no fue guardado.', 'Plastiservi', 'error');
					if(respuesta.mensaje != "ng"){
						Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', 'error');
					}
				}
				$("#myModalguiadesp").modal('hide');
			}
			if(funcion=='guardarfactdesp'){
				if (respuesta.mensaje == "ok") {
					if(data['status'] == 1){
						$("#fila" + data['nfila']).remove();
					}else{
						$("#numfactura" + data['nfila']).html(data['numfactura']);
						$("#fechafactura" + data['nfila']).html(respuesta.despachoord.fechafactura);	
					}
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					Biblioteca.notificaciones('Registro no fue guardado.', 'Plastiservi', 'error');
					if(respuesta.mensaje != "ng"){
						Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', 'error');
					}
				}
				$("#myModalnumfactura").modal('hide');
			}
			if(funcion=='consultarguiadespachood'){
				if (respuesta.mensaje == "ok") {
					if(data['status']=='1'){
						$("#guiadespachom").val(respuesta.despachoord.guiadespacho);
						quitarvalidacioneach();
					}else{
						quitarvalidacioneach();
						$("#guiadespachom").val(respuesta.despachoord.guiadespacho);
					}
					$("#myModalguiadesp").modal('show');
				} else {
					Biblioteca.notificaciones('Registro no encontrado.', 'Plastiservi', 'error');
				}
			}
			if(funcion=='consultarnumfacturaod'){
				if (respuesta.mensaje == "ok") {
					//alert(respuesta.despachoord.numfactura);
					if(data['status']=='1'){
						quitarvalidacioneach();	
					}else{
						quitarvalidacioneach();	
						$("#numfacturam").val(respuesta.despachoord.numfactura);
						$("#fechafacturam").val(respuesta.fechafactura);
						$("#fechafacturam").datepicker("setDate",respuesta.fechafactura)	
					}
					
					//$(".requeridos").keyup();
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

function guiadesp(nfila,id,status){
	$("#idg").val(id);
	$("#nfila").val(nfila);
	$("#guiadespachom").val('');
	$("#status").val(status);
	var data = {
		id    : id,
		nfila : nfila,
		status : status,
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
			guiadespacho: $("#guiadespachom").val(),
			_token: $('input[name=_token]').val()
		};
		$.ajax({
			url: '/despachoord/buscarguiadesp',
			type: 'POST',
			data: data,
			success: function (respuesta) {
				if(respuesta.mensaje == 'ok'){
					swal({
						title: 'Guia despacho N°.' +$("#guiadespachom").val()+ ' ya existe.',
						text: "",
						icon: 'error',
						buttons: {
							confirm: "Aceptar"
						},
					}).then((value) => {
						if (value) {
							//$("#oc_id").focus();
						}
					});
				
				}else{
					var data = {
						id    : $("#idg").val(),
						guiadespacho : $("#guiadespachom").val(),
						nfila : $("#nfila").val(),
						status : $("#status").val(),
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
				}
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



function numfactura(nfila,id,status){
	$("#idf").val(id);
	$("#numfacturam").val('');
	$("#fechafacturam").val('');
	$("#nfilaf").val(nfila);
	$("#status").val(status);
	var data = {
		id    : id,
		nfila : nfila,
		status: status,
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
			numfactura   : $("#numfacturam").val(),
			fechafactura : $("#fechafacturam").val(),
			nfila : $("#nfilaf").val(),
			status : $("#status").val(),
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
	
	v1=validacion('guiadespachom','texto');
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
	
	v1=validacion('numfacturam','texto');
	v2=validacion('fechafacturam','texto');
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
