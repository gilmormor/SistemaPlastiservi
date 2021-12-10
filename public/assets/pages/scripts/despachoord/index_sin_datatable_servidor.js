$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
	let  table = $('#tabla-data').DataTable();
    table
        .on('draw', function () {
            eventFired( 'Page' );
        });
});

var eventFired = function ( type ) {
	total = 0;
	$("#tabla-data tr .kilos").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
	$("#totalKg").html(MASKLA(total,2))
}

function anular(i,id){
	//alert($('input[name=_token]').val());
	var data = {
		id: id,
        nfila : i,
        _token: $('input[name=_token]').val()
	};
	var ruta = '/despachoord/anular/'+id;
	swal({
		title: '¿ Está seguro que desea anular Solicitud Despacho ?',
		text: "Esta acción no se puede deshacer!",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			ajaxRequest(data,ruta,'anularOD');
		}
	});
}


function ajaxRequest(data,url,funcion) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='anularOD'){
				if (respuesta.mensaje == "guidesp_factura") {
					Biblioteca.notificaciones('Registro tiene Guia de despacho y Factura Asociadas. No se puede anular.', 'Plastiservi', 'error');
					return 0;
				}else{
					if (respuesta.mensaje == "ok") {
						//$("#fila"+data['nfila']).remove();
						$("#accion"+data['nfila']).html('<small class="label pull-left bg-red">Anulado</small>')
						Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
					} else {
						if (respuesta.mensaje == "sp"){
							Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
						}else{
							Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
						}
					}
				}
			}
			if(funcion=='guardarguiadesp'){
				alert('entro');
			}
			if(funcion=='aproborddesp'){
				if (respuesta.mensaje == "ok") {
					swal({
						title: '¿ Desea ver PDF Orden Despacho ?',
						text: "",
						icon: 'success',
						buttons: {
							cancel: "Cancelar",
							confirm: "Aceptar"
						},
					}).then((value) => {
						if (value) {
							genpdfOD(data.id,1);
						}
						$("#fila"+data['nfila']).remove();
					});
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
			}
			
		},
		error: function () {
		}
	});
}


function guiadesp(nfila,id){
	$("#id").val(id);
	$("#myModalguiadesp").modal('show');
}

$("#btnGuardarG").click(function(event)
{
	event.preventDefault();
	if(verificarGuia())
	{
		var data = {
			id    : $("#id").val(),
			guiadespacho : $("#guiadespacho").val(),
			_token: $('input[name=_token]').val()
		};
		var ruta = '/despachoord/guardarguiadesp/'+data['id'];
		swal({
			title: '¿ Está seguro desea continuar ?',
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


function aprobarord(i,id){
	var data = {
		id: id,
        nfila : i,
        _token: $('input[name=_token]').val()
	};
	var ruta = '/despachoord/aproborddesp/'+id;
	swal({
		title: '¿ Aprobar Orden de Despacho ?',
		text: "Esta acción no se puede deshacer!",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			ajaxRequest(data,ruta,'aproborddesp');
		}
	});
}