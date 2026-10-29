/*
$(document).ready(function () {
	$(document).on("contextmenu", function(e){
		e.preventDefault();
        if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'C' || e.key === 'J'))) {
            e.preventDefault();
        }
	});
});
*/
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
                    /* Un campo con pull-right flota a la derecha y sale del flujo, de modo
                       que el mensaje se dibujaba a su izquierda en la misma linea en vez de
                       debajo. El clear lo baja sin alterar el resto del formulario: solo
                       afecta al span del error y solo en los campos que llevan esa clase. */
                    if (element.hasClass('pull-right')){
                        error.css('clear','both');
                    }
                    if ($(element).is('select') && element.hasClass('bs-select')) {
                        error.insertAfter(element);
                    } else if ($(element).is('select') && element.hasClass('select2-hidden-accessible')){
                        element.next().after(error);
                    } else if ($(element).is('select') && element.hasClass('selectpicker')){
                        /* bootstrap-select mete el select original como PRIMER hijo de su
                           envoltorio .bootstrap-select, antes del boton que es lo que se ve
                           en pantalla. Por eso insertar el error despues del select lo
                           dibujaba encima del control: aqui se inserta despues de todo el
                           envoltorio, para que quede debajo igual que en los demas campos.
                           Si bootstrap-select no llego a inicializarse no hay envoltorio, y
                           entonces se mantiene el comportamiento anterior. */
                        var aux_wrapbs = element.closest('.bootstrap-select');
                        if(aux_wrapbs.length){
                            aux_wrapbs.after(error);
                        }else{
                            error.insertAfter(element);
                        }
                    } else if (element.attr("data-error-container")){
                        /* Se corrigen dos errores de esta rama, que hasta ahora nunca se
                           ejecutaba porque ninguna vista usa el atributo: la condicion leia
                           "date-error-container" en vez de "data-error-container", y appenTo
                           no existe en jQuery, el metodo es appendTo. */
                        error.appendTo(element.attr("data-error-container"));
                    } else if (element.closest('.input-group').length){
                        /* En Bootstrap 3 el .input-group se dibuja como una tabla y sus hijos
                           como celdas, de modo que insertar el error despues del input lo
                           dejaba como una celda mas, a la derecha y en la misma linea del
                           boton o del addon. Se inserta despues de todo el grupo para que
                           quede debajo, igual que en un input suelto. */
                        element.closest('.input-group').after(error);
                    } else {
                        error.insertAfter(element);
                    }
                },
                invalidHandler: function(event, validator) {
                    // Cuando hay errores de validación, el botón se mantiene activo
                    $("#btnguardargen").prop("disabled", false);
                },
                submitHandler: function(form){
                    // Solo cuando TODAS las validaciones (incluyendo las de transporte) pasan
                    // se deshabilita el botón y se envía el formulario
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


//notificaciones();

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