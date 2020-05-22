$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

/*
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
*/
    consultar(datos());
    $("#btnconsultar").click(function()
    {
        consultar(datos());
    });
    $("#tab2").click(function()
    {
        consultar(datos());
    });

    $("#btnpdf1").click(function()
    {
        consultarpdf(datos());
    });

    //alert(aux_nfila);
    $('.datepicker').datepicker({
		language: "es",
		autoclose: true,
		todayHighlight: true
    }).datepicker("setDate");
    
    $("#rut").focus(function(){
        eliminarFormatoRut($(this));
    });

    configurarTabla('.tablas');

});

function configurarTabla(aux_tabla){
    $(aux_tabla).DataTable({
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
}

function datos(){
    var data = {
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        rut               : eliminarFormatoRutret($("#rut").val()),
        vendedor_id       : $("#vendedor_id").val(),
        oc_id             : $("#oc_id").val(),
        giro_id           : $("#giro_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        tipoentrega_id    : $("#tipoentrega_id").val(),
        notaventa_id      : $("#notaventa_id").val(),
        _token            : $('input[name=_token]').val()
    };
    return data;
}

function consultar(data){
    $.ajax({
        url: '/despachotempnotaventa/reporte',
        type: 'POST',
        data: data,
        success: function (datos) {
            if(datos['tabla'].length>0){
                $("#tablaconsulta").html(datos['tabla']);
                $("#tablaconsulta2").html(datos['tabla2']);
                configurarTabla('.tablascons');
                $('.btn-warning').tooltip({title: "Finalizar Despacho"});
                $('.btn-success').tooltip({title: "Iniciar Despacho"});
                $('.guiadespacho').tooltip({title: "Guia Despacho"});
                

            }
        }
    });
}


function consultarpdf(data){
    $.ajax({
        url: '/despachotempnotaventa/exportPdf',
        type: 'GET',
        data: data,
        success: function (datos) {
            $("#midiv").html(datos);
            /*
            if(datos['tabla'].length>0){
                $("#tablaconsulta").html(datos['tabla']);
                configurarTabla();
            }
            */
        }
    });
}

$("#rut").blur(function(){
	codigo = $("#rut").val();
	aux_sta = $("#aux_sta").val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		//totalizar();
		if(!dgv(codigo.substr(0, codigo.length-1))){
			swal({
				title: 'Dígito verificador no es Válido.',
				text: "",
				icon: 'error',
				buttons: {
					confirm: "Aceptar"
				},
			}).then((value) => {
				if (value) {
					//ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
					$("#rut").focus();
				}
			});
			//$(this).val('');
		}else{
			var data = {
				rut: $("#rut").val(),
				_token: $('input[name=_token]').val()
			};
			$.ajax({
				url: '/cliente/buscarCli',
				type: 'POST',
				data: data,
				success: function (respuesta) {
					if(respuesta.length>0){
						formato_rut($("#rut"));
					}else{
                        formato_rut($("#rut"));
                        swal({
                            title: 'Cliente no existe.',
                            text: "Aceptar para crear cliente temporal",
                            icon: 'error',
                            buttons: {
                                confirm: "Aceptar",
                                cancel: "Cancelar"
                            },
                        }).then((value) => {
                            if (value) {
                                limpiarclientemp();
                                
                                $("#myModalClienteTemp").modal('show');
                            }else{
                                $("#rut").focus();
                                //$("#rut").val('');
                            }
                        });		
					}
				}
			});
		}
	}
});

$("#btnbuscarcliente").click(function(event){
    $("#rut").val("");
    $("#myModalBusqueda").modal('show');
});

function copiar_rut(id,rut){
	$("#myModalBusqueda").modal('hide');
	$("#rut").val(rut);
	//$("#rut").focus();
	$("#rut").blur();
}

function inidespacho1(id,i){
    var data = {
        id     : id,
        i      : i,
        _token : $('input[name=_token]').val()
    };
    var ruta = '/notaventa/inidespacho/' + id;
    ajaxRequest(data,ruta,'inidespacho');
}

function inidespacho(id,i){

    var confirm= alertify.confirm('Mensaje','Iniciar Despacho?',null,null).set('labels', {ok:'Confirmar', cancel:'Cancelar'}); 	
    confirm.set({transition:'slide'});   	

    confirm.set('onok', function(){ //callbak al pulsar botón positivo
        var data = {
            id     : id,
            i      : i,
            _token : $('input[name=_token]').val()
        };
        var ruta = '/notaventa/inidespacho/' + id;
        ajaxRequest(data,ruta,'inidespacho');
    });
    confirm.set('oncancel', function(){ //callbak al pulsar botón negativo
        alertify.error('Has Cancelado la Solicitud');
        $('.parametros').slideDown();
    });
}

function guiadespacho(id,i){
    $(".input-sm").val('');
    $("#titulomodal").html("Guias Despacho"+' - NV: '+$("#id"+i).html());
    var data = {
        id     : id,
        i      : i,
        _token : $('input[name=_token]').val()
    };
    var ruta = '/notaventa/buscarguiadespacho/' + id;
    ajaxRequest(data,ruta,'guiadespacho');
}

$("#guardarGD").click(function(event){
    //$("#guiasdespacho").val("");
    $("#myModalguiadespacho").modal('hide');
    id = $("#notaventa_idhide").val();
    var data = {
        id            : id,
        i             : i,
        guiasdespacho : $("#guiasdespacho").val(),
        _token : $('input[name=_token]').val()
    };
    var ruta = '/notaventa/actguiadespacho/' + id;
    ajaxRequest(data,ruta,'actguiadespacho');

});

function findespacho(i){
    var confirm= alertify.confirm('Mensaje','Finalizar Despacho?',null,null).set('labels', {ok:'Confirmar', cancel:'Cancelar'}); 	
    confirm.set({transition:'slide'});
    confirm.set('onok', function(){ //callbak al pulsar botón positivo
        id = $("#id"+i).html();
        var data = {
            id     : id,
            i      : i,
            _token : $('input[name=_token]').val()
        };
        var ruta = '/notaventa/findespacho/' + id;
        ajaxRequest(data,ruta,'findespacho');
    });
    confirm.set('oncancel', function(){ //callbak al pulsar botón negativo
        alertify.error('Has Cancelado la Solicitud');
        $('.parametros').slideDown();
    });

}


function ajaxRequest(data,url,funcion) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='aprobarcotvend'){
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
            if(funcion=='inidespacho'){
				if (respuesta.mensaje == "ok") {
                    aux_colormens = "";
                    i = data['i'];
                    $("#guiadespacho"+data['i']).removeClass("disabled");
                    $("#fila"+i).fadeOut(500);
					$('#initdespacho'+i).attr("class", "btn btn-warning btn-sm");
					$('#initdespacho'+i).attr("onclick", "findespacho("+i+")");
					$('.btn-warning').tooltip({title: "Finalizar Despacho"});
					$('#initdespacho'+i).attr("data-original-title", "Finalizar Despacho");
					$('#glypcnbtnInitdespacho'+i).attr("class", "glyphicon glyphicon-stop");
					$('#guiadespacho'+i).attr("class", "btn btn-primary btn-sm");
					$("#fila"+i).fadeIn(500);

                    Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', "success");                    
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
            }
            if(funcion=='guiadespacho'){
                $("#notaventa_idhide").val(data['id']);
                $("#guiasdespacho").val(respuesta.guiasdespacho);
                $("#myModalguiadespacho").modal('show')
            }
            if(funcion=='actguiadespacho'){
				if (respuesta.mensaje == "ok") {
                    Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');                    
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
            }
            if(funcion=='findespacho'){
				if (respuesta.mensaje == "ok") {
                    i = data['i'];
                    $("#fila" + i).fadeOut(2000);
                    Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', "success");                    
				} else {
                    if(respuesta.mensaje == "empty"){
                        swal({
                            title: 'Registro no fue procesado',
                            text: "El campo Guia de despacho esta Vacio.",
                            icon: 'error',
                            buttons: {
                                cancel: "Salir"
                            },
                        }).then((value) => {
                        });
                    }else{
                        if (respuesta.mensaje == "sp"){
                            Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
                        }else{
                            Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
                        }    
                    }
				}
            }

		},
		error: function () {
		}
	});
}