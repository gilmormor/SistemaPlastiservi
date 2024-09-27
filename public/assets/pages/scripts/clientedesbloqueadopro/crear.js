$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $('.tablas').DataTable({
		'paging'      : true, 
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		'autoWidth'   : false,
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
	});
    $("#rut" ).focus();
    $("#rut").focus(function(){
        eliminarFormatoRut($(this));
    });
    $("#rut").keyup(function(event){
		if(event.which==113){
			$(this).val("");
			$(".input-sm").val('');
			$("#myModalBusqueda").modal('show');
		}
    });
    $("#btnbuscarcliente").click(function(event){
        $(this).val("");
        $(".input-sm").val('');
        $("#myModalBusqueda").modal('show');
    });
    formato_rut($('#rut'));
    $("#notaventa_id").focus(function(){
        $("#rut").val("");
        $("#razonsocial").val("");
    });
    $("#cotizacion_id").focus(function(){
        $("#rut").val("");
        $("#razonsocial").val("");
    });
});

function copiar_rut(id,rut){
	$("#myModalBusqueda").modal('hide');
    $("#cliente_id").val(id);
    $("#rut").val(rut);
	//$("#rut").focus();
	$("#rut").blur();
	$("#razonsocial").focus();
}

$("#rut").blur(function(){
    eliminarFormatoRut($(this));
	codigo = $("#rut").val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		//totalizar();
        var data = {
            rut: codigo,
            _token: $('input[name=_token]').val()
        };
        $.ajax({
            url: '/cliente/buscarCliId',
            type: 'POST',
            data: data,
            success: function (respuesta) {
                if(respuesta.length>0){
                    //alert(respuesta[0]['vendedor_id']);
                    //$("#rut").val(respuesta[0]['rut']);

                    var data = {
                        id         : respuesta[0]['id'],
                        razonsocial: respuesta[0]['razonsocial'],
                        _token: $('input[name=_token]').val()
                    };
                    var ruta = '/clientedesbloqueadopro/buscarclidesbloq';
                    ajaxRequest(data,ruta,'buscarclidesbloq');
                }else{
                    swal({
                        title: 'Cliente no existe.',
                        text: "Presione F2 para buscar",
                        icon: 'warning',
                        buttons: {
                            confirm: "Aceptar"
                        },
                    }).then((value) => {
                        if (value) {
                            //ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
                            $("#rut").focus();
                        }
                    });
                }
            }
        });
	}
});

function ajaxRequest(data,url,funcion) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='buscarclidesbloq'){
				if (respuesta.mensaje == "ng") {
                    formato_rut($("#rut"));
                    $("#razonsocial").val(data.razonsocial);
                    $("#cliente_id").val(data.id);
                    $("#obs").focus();
                } else {
                    Biblioteca.notificaciones('Cliente: '+data.razonsocial+' ya está Habilitado.', 'Plastiservi', 'error');
                    $("#rut").val('');
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