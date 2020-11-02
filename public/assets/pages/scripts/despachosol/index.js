$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

});

function anular(i,id){
	//alert($('input[name=_token]').val());
	var data = {
		id: id,
        nfila : i,
        _token: $('input[name=_token]').val()
	};
	var ruta = '/despachosol/anular/'+id;
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
			ajaxRequest(data,ruta,'anularSD');
		}
	});
}


function ajaxRequest(data,url,funcion) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='anularSD'){
				if (respuesta.mensaje == "hijo") {
					Biblioteca.notificaciones('Registro tiene Ordenes de Despacho Asociadas. No se puede anular.', 'Plastiservi', 'error');
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
			if(funcion=='aproborddesp'){
				if (respuesta.mensaje == "ok") {
					$("#fila"+data['nfila']).remove();
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


function aprobarsol(i,id){
	//alert($('input[name=_token]').val());
	var data = {
		id: id,
        nfila : i,
        _token: $('input[name=_token]').val()
	};
	var ruta = '/despachosol/aproborddesp/'+id;
	swal({
		title: '¿ Seguro desea aprobar Solicitud ?',
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