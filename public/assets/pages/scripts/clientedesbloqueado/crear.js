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
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
	});
    $("#cotizacion_id").numeric();
    $("#cotizacion_id").focus();
    $("#notaventa_id").numeric();
    $("#notaventa_id").focus();
    //formato_rut($("#rutenabled"));
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
                    var ruta = '/clientedesbloqueado/buscarclibloq';
                    ajaxRequest(data,ruta,'buscarclibloq');
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
			if(funcion=='buscarclibloq'){
				if (respuesta.mensaje == "ng") {
                    formato_rut($("#rut"));
                    $("#razonsocial").val(data.razonsocial);
                    $("#cliente_id").val(data.id);
                    $("#obs").focus();
                } else {
                    Biblioteca.notificaciones('Cliente: '+data.razonsocial+' ya está desbloqueado.', 'Plastiservi', 'error');
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

$("#notaventa_id").blur(function(){
    eliminarFormatoRut($(this));
	codigo = $("#notaventa_id").val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		//totalizar();
        var data = {
            id: codigo,
            _token: $('input[name=_token]').val()
        };
        $.ajax({
            url: '/notaventa/buscarNVActiva',
            type: 'POST',
            data: data,
            success: function (respuesta) {
                if(respuesta.id=="1"){
                    $("#cliente_id").val(respuesta.cliente_id);
                    $("#rut").val(respuesta.rut);
                    $("#razonsocial").val(respuesta.razonsocial);
                    formato_rut($("#rut"));
                    /* $("#observacion").focus();
                    $("#vpnv1").attr("onclick","genpdfNV(" + $("#notaventa_id").val() + ",1)");
                    $("#vpnv2").attr("onclick","genpdfNV(" + $("#notaventa_id").val() + ",1)");
                    $('#vistaprevNV').show(); */
                }else{
                    swal({
                        title: respuesta.title,
                        text: "",
                        icon: respuesta.tipo_alert,
                        buttons: {
                            confirm: "Aceptar"
                        },
                    }).then((value) => {
                        if (value) {
                            $("#notaventa_id").focus();
                        }
                    });
                }
            }
        });
	}
});

$("#cotizacion_id").blur(function(){
    eliminarFormatoRut($(this));
	codigo = $("#cotizacion_id").val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		//totalizar();
        var data = {
            id: codigo,
            _token: $('input[name=_token]').val()
        };
        $.ajax({
            url: '/cotizacion/buscarCotGen',
            type: 'POST',
            data: data,
            success: function (respuesta) {
                if(respuesta.id != 0){
                    $("#cliente_id").val(respuesta.cliente_id);
                    $("#rut").val(respuesta.rut);
                    $("#razonsocial").val(respuesta.razonsocial);
                    formato_rut($("#rut"));
                    /* $("#observacion").focus();
                    $("#vpnv1").attr("onclick","genpdfNV(" + $("#cotizacion_id").val() + ",1)");
                    $("#vpnv2").attr("onclick","genpdfNV(" + $("#cotizacion_id").val() + ",1)");
                    $('#vistaprevNV').show(); */
                }else{
                    swal({
                        title: respuesta.title,
                        text: respuesta.text,
                        icon: respuesta.tipo_alert,
                        buttons: {
                            confirm: "Aceptar"
                        },
                    }).then((value) => {
                        if (value) {
                            $("#cotizacion_id").focus();
                        }
                    });
                }
            }
        });
	}
});