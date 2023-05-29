var Biblioteca = function(){
    return {
        validacionGeneral: function(id,reglas,mensajes){
            const formulario = $('#' + id);
            formulario.validate({
                rules: reglas,
                messages: mensajes,
                errorElement: 'span',
                errorClass: 'help-block help-block-error',
                focusInvalid: false,
                ignore: "",
                highlight: function(element,errorClass,validClass){
                    $(element).closest('.form-group').addClass('has-error');

                    aux_obj = $(element).closest('.form-group');
                    //alertify.error("Falta incluir información: " + aux_obj.children('label').html());
                    if(aux_obj.children('input').attr('type') != 'email'){
                        aux_label = "Información.";
                        if(aux_obj.children('label').html() != undefined){
                            aux_label = aux_obj.children('label').html();
                        }
                        alertify.error("Falta: " + aux_label);
                    }
                },
                unhighlight: function(element){
                    $(element).closest('.form-group').removeClass('has-error');
                },
                success: function(label){
                    label.closest('.form-group').removeClass('has-error');
                },
                errorPlacement: function(error,element){
                    if ($(element).is('select') && element.hasClass('bs-select')) {
                        error.insertAfter(element);
                    } else if ($(element).is('select') && element.hasClass('select2-hidden-accessible')){
                        element.next().after(error);
                    } else if (element.attr("date-error-container")){
                        error.appenTo(element.attr("data-error-container"));
                    } else {
                        error.insertAfter(element);
                    }
                },
                invalidHandler: function(event, validator) {

                },
                submitHandler: function(form){
                    $("#btnguardargen").prop("disabled", true);
                    return true;
                }
            });
        },
        notificaciones: function (mensaje, titulo, tipo) {
            /*toastr.options = {
                closeButton: true,
                newestOnTop: true,
                positionClass: 'toast-top-right',
                preventDuplicates: true,
                timeOut: '5000'
            };*/
            //alert(tipo);
            if (tipo == 'error') {
                //toastr.error(mensaje, titulo);
                alertify.error(mensaje);
            } else if (tipo == 'success') {
                //toastr.success(mensaje, titulo);
                alertify.success(mensaje);
            } else if (tipo == 'info') {
                //toastr.info(mensaje, titulo);
                alertify.info(mensaje);
            } else if (tipo == 'warning') {
                //toastr.warning(mensaje, titulo);
                alertify.warning(mensaje);
            }
        },
    }
}();


notificaciones();

function notificaciones(){
    var data = {
        prueba : 'prueba1',
    };
    var url = '/notificaciones';
    funcion = 'notificaciones';
    $.ajax({
        url: url,
        type: 'GET',
        success: function (respuesta) {
            if(funcion=='notificaciones'){
                $("#notificaciones").html(respuesta.htmlNotif);
                if(respuesta.totalNotif>0){
                    $("#idnotifnum").html(respuesta.totalNotif);
                }
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


function marcarTodasVistas(){
	swal({
		title: 'Eliminar todas las Notificaciones?',
		text: "Acción no se puede deshacer!",
		icon: "warning",
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
},
	}).then((value) => {
		if (value) {
			var data = {
				_token: $('input[name=_token]').val()
			};
		
			$.ajax({
				url: '/notificaciones/marcarTodasVista',
				type: 'POST',
				data: data,
				success: function (respuesta) {
					// *** REDIRECCIONA A UNA RUTA*** 
					var loc = window.location;
					window.location = respuesta; //loc.protocol+"//"+loc.hostname+"/notaventaaprobar";
					// ****************************** 
					
				}
			});		
		}
	});

}