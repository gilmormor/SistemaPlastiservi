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


var data = {
    prueba : 'prueba1',
    _token : $('input[name=_token]').val()
};
var url = '/noconformidadrecep/notificaciones/';
funcion = 'notificaciones';
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