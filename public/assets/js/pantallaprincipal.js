$(document).ready(function () {
    //alert('prueba');
    var data = {
        prueba : 'prueba1',
        _token : $('input[name=_token]').val()
    };
    var ruta = '/noconformidadrecep/notificaciones/';
    funcion = 'notificaciones';
    ajaxRequest(data,ruta,funcion);
});
//_token : $('input[name=_token]').val()

function ajaxRequest(data,url,funcion){
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
            if(funcion=='notificaciones'){
                $("#notificaciones").html(respuesta.htmlNotif);
                $("#idnotifnum").html(respuesta.totalNotif);
                //alert(respuesta.htmlNotif)

                return 0;
            }
            if (respuesta.mensaje == "ok") {
                Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
            } else {
                if (respuesta.mensaje == "sp"){
                    Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
                }else{
                    if(respuesta.mensaje=="img"){

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