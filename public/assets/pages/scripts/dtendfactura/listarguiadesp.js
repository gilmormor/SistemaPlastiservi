$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    data = datosGD();

    $('#tabla-data-guiadesp').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        "order": [[ 0, "asc" ]],
        'ajax'        : "listarguiadesppage/"+data.data2,
        'columns'     : [
            {data: 'id'},
            {data: 'fchemis'},
            {data: 'razonsocial'},
            {data: 'cotizacion_id'},
            {data: 'oc_id'},
            {data: 'notaventa_id'},
            {data: 'despachosol_id'},
            {data: 'despachoord_id'},
            {data: 'nrodocto'},
            {data: 'comuna_nombre'},
            {data: 'aux_totalkg'},
            {data: 'tipoentrega_nombre',className:"ocultar"},
            {data: 'icono',className:"ocultar"},
            {data: 'clientebloqueado_descripcion',className:"ocultar"},
            {data: 'oc_file',className:"ocultar"},
            {data: 'rutacrear',className:"ocultar"},
            {data: 'updated_at',className:"ocultar"},            
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : ""}
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            $(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);
            $(row).attr('nrodocto','fila' + data.id);
            //"<a href='#' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + "</a>";

            $('td', row).eq(1).attr('data-order',data.fchemis);
            aux_fecha = data.fchemis.substring(8, 10) + "/" + data.fchemis.substring(5, 7) + "/" + data.fchemis.substring(0, 4);
            $('td', row).eq(1).html(aux_fecha);

			codigo = data.cotizacion_id;
			if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)){
				aux_text = "";
			}else{
				aux_text = 
				"<a href='#'  class='tooltipsC' title='Cotizacion' " +
				"onclick='genpdfCOT(\"" + data.cotizacion_id + "\",1)'>" + data.cotizacion_id + 
				"</a>";
			}
			$('td', row).eq(3).html(aux_text);


            if(data.oc_file != "" && data.oc_file != null){
                aux_text = 
                    "<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Orden de Compra' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + 
                        data.oc_id + 
                    "</a>";
                $('td', row).eq(4).html(aux_text);
            }
            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV(" + data.notaventa_id + ",1)'>" +
                    data.notaventa_id +
                "</a>";
            $('td', row).eq(5).html(aux_text);
            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Solicitud de Despacho' onclick='genpdfSD(" + data.despachosol_id + ",1)'>" + 
                    data.despachosol_id + 
                "</a>";
            $('td', row).eq(6).html(aux_text);

            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Orden despacho: " + data.despachoord_id + "' onclick='genpdfOD(" + data.despachoord_id + ",1)'>"+
                    + data.despachoord_id +
                "</a>";
            $('td', row).eq(7).html(aux_text);

            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Guia Despacho: " + data.nrodocto + "' onclick='genpdfGD(" + data.nrodocto + ",\"\")'>"+
                    + data.nrodocto +
                "</a>";
            $('td', row).eq(8).html(aux_text);


            $('td', row).eq(10).attr('data-order',data.aux_totalkg);
            $('td', row).eq(10).attr('style','text-align:right');
            aux_text = MASKLA(data.aux_totalkg,2);
            $('td', row).eq(10).html(aux_text);
            $('td', row).eq(10).addClass('subtotalkg');

            
            aux_text = 
                "<i class='fa fa-fw " + data.icono + " tooltipsC' title='" + data.tipoentrega_nombre + "'></i>";
            $('td', row).eq(11).html(aux_text);
            $('td', row).eq(11).attr('style','text-align:center');

            if(data.clientebloqueado_descripcion != null){
                aux_text = 
                    "<a class='btn-accion-tabla btn-sm tooltipsC' title='Cliente Bloqueado: " + data.clientebloqueado_descripcion + "'>"+
                        "<span class='fa fa-fw fa-lock text-danger text-danger' style='bottom: 0px;top: 2px;'></span>"+
                    "</a>";
            }else{
                /*
                "<a class='btn-accion-tabla btn-sm tooltipsC' onclick='aprobarsol(" + i + "," + data.id + ")' title='Aprobar Orden Despacho'>" +
                    "<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>"+
                "</a>"+*/
/*
                "<a href='/despachoord/aprobguiadesp' class='btn-accion-tabla btn-sm tooltipsC btnaprobar' title='Aprobar Orden Despacho'>" +
                    "<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>"+
                "</a>"+
*/
                aux_text = 
                "<a id='bntaproord'" + data.id + " name='bntaproord'" + data.id + " class='btn-accion-tabla btn-sm' onclick='aprobarord(" + data.id + "," + data.id + ")' title='Aprobar Orden Despacho' data-toggle='tooltip'>"+
                    "<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>"+
                "</a>"+
                "<a href='despachoord' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>"+
                    "<i class='fa fa-fw fa-pencil'></i>"
                "</a>";
            }
            $('td', row).eq(16).addClass('updated_at');
            $('td', row).eq(16).attr('id','updated_at' + data.id);
            $('td', row).eq(16).attr('name','updated_at' + data.id);
            /*
            aux_text = "<a href='" + data.rutacrear + "' class='btn-accion-tabla tooltipsC' title='Hacer Factura: " + data.tipoentrega_nombre + "'>" +
                            "<button type='button' class='btn btn-default btn-xs'>" +
                                "<i class='fa fa-fw " + data.icono + "'></i>"+
                            "</button>" +
                        "</a>|" +
                        "<a onclick='anularguiafact(" + data.id + "," + data.despachoord_id + ")' class='btn-accion-tabla btn-sm tooltipsC btndevord' title='Anular Guia Despacho' data-toggle='tooltip'>" +
                            "<button type='button' class='btn btn-default btn-xs'>" +
                                "<i class='fa fa-fw fa-remove text-red'></i>"+
                            "</button>" +
                        "</a>";
            $('td', row).eq(17).html(aux_text);
            */
        }
    });

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
    //consultar(datosGD());
    $("#btnconsultar").click(function()
    {
        //consultar(datosGD());
        data = datosGD();
        $('#tabla-data-guiadesp').DataTable().ajax.url( "listarguiadesppage/"+data.data2 ).load();
    });

    $("#btnpdf1").click(function()
    {
        consultarpdf(datosGD());
    });

    //alert(aux_nfila);
    $('.datepicker').datepicker({
		language: "es",
        autoclose: true,
        clearBtn : true,
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
        "order"       : [[ 0, "desc" ]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
    });    
}


function ajaxRequest(data,url,funcion) {
    datatemp = data;
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
            if(funcion=='vistonotaventa'){
				if (respuesta.mensaje == "ok") {
					//$("#fila"+data['nfila']).remove();
                    Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
                    
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
			}
            if(funcion=='btndevsol'){
                if (respuesta.mensaje == "ok") {
                    //form.parents('tr').remove();
                    $("#fila"+data['nfila']).remove();
                    Biblioteca.notificaciones('El registro fue procesado correctamente.', 'Plastiservi', 'success');
                } else {
                    if (respuesta.mensaje == "sp"){
                        Biblioteca.notificaciones('Usuario no tiene permiso para eliminar.', 'Plastiservi', 'error');
                    }else{
                        if(respuesta.mensaje == "hijos"){
                            Biblioteca.notificaciones('No puede ser eliminado: ID tiene registros relacionados en otras tablas.', 'Plastiservi', 'error');
                        }else{
                            if(respuesta.mensaje == "ne"){
                                Biblioteca.notificaciones('No tiene permiso para eliminar.', 'Plastiservi', 'error');
                            }else{
                                if(respuesta.mensaje.length > 10){
                                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', 'error');
                                }else{
                                    Biblioteca.notificaciones('El registro no pudo ser eliminado, hay recursos usandolo.', 'Plastiservi', 'error');
                                }
                            }
                        }
                    }
                }
                $("#myModaldevsoldeps").modal('hide');
            }
            if(funcion=='btncerrarsol'){
                if (respuesta.mensaje == "ok") {
                    //form.parents('tr').remove();
                    $("#fila"+data['nfila']).remove();
                    Biblioteca.notificaciones('El registro fue procesado correctamente.', 'Plastiservi', 'success');
                } else {
                    if (respuesta.mensaje == "sp"){
                        Biblioteca.notificaciones('Usuario no tiene permiso para eliminar.', 'Plastiservi', 'error');
                    }else{
                        if(respuesta.mensaje == "hijos"){
                            Biblioteca.notificaciones('No puede ser eliminado: ID tiene registros relacionados en otras tablas.', 'Plastiservi', 'error');
                        }else{
                            if(respuesta.mensaje == "ne"){
                                Biblioteca.notificaciones('No tiene permiso para eliminar.', 'Plastiservi', 'error');
                            }else{
                                Biblioteca.notificaciones('El registro no pudo ser eliminado, hay recursos usandolo.', 'Plastiservi', 'error');
                            }
                        }
                    }
                }
                $("#myModaldevsoldeps").modal('hide');
            }
            if(funcion=="guardarfechaed"){
                Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                restbotoneditfeced(datatemp.i)
                if(respuesta.error == 0){
                    $("#fechaestdesp" + datatemp.i).html($("#fechaed" + datatemp.i).val());
                    $("#savefed" + datatemp.i).attr('updated_at',respuesta.updated_at);
                }
            }
            if(funcion=='consultaranularguiafact'){
				if (respuesta.mensaje == "ok") {
                    //console.log(respuesta);
					//alert(respuesta.despachoord.guiadespacho);
                    $("#id1").html("Id Guia");
                    $("#id2").html("Guia Despacho SII");
					$("#guiadespachoanul").val(respuesta.dte.nrodocto);
                    $("#guiadespachoanul").attr("despachoord_id",respuesta.despachoord_id)
                    $("#guiadespachoanul").attr("updated_at",datatemp.updated_at)
                    
					//$(".requeridos").keyup();
					quitarvalidacioneach();
                    $("#tituloAGFAC").html("Anular Guia Despacho");
                    $("#statusM").val('2');
                    $(".selectpicker").selectpicker('refresh');
					$("#myModalanularguiafact").modal('show');
				} else {
					Biblioteca.notificaciones('Registro no encontrado.', 'Plastiservi', 'error');
				}
			}

            if(funcion=='buscarTipoBodegaOrdDesp'){
                if(respuesta.datas.length > 0){
                    if(respuesta.datas.length == 1){
						var data = {
							id    : $("#idanul").val(),
							nfila : $("#nfilaanul").val(),
							dte_id : $("#idanul").val(),
                            guiadesp_id : $("#idanul").val(),
							despachoord_id : $("#guiadespachoanul").attr("despachoord_id"),
							observacion : $("#observacionanul").val(),
							obs : $("#observacionanul").val(),
                            motanul_id : 5, //Anulada por Usuario
                            moddevgiadesp_id : 'FC', //Modulo Factura
							statusM : "2", //Para que borre todos los valores de guia de despacho y factura
							invbodega_id : respuesta.datas[0].id,
							pantalla_origen  : 2, //Para saber de donde viene la anulacion en este caso de la pantalla Asignar Guia
                            updated_at : datatemp.updated_at,
                            rutarecarga : "factura/listarguiadesp",
                            //_method: "delete",             
							_token: $('input[name=_token]').val()
						};
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
                                var ruta = "/dteguiadesp/validarupdated";
                                ajaxRequest(data,ruta,'validarupdated1');
                                //ajaxRequest(data,'guiadesp/'+data.guiadesp_id,'eliminar');
                                /*
                                var ruta = '/guardaranularguia';
                                ajaxRequest(data,ruta,'guardaranularguia');
                                */
							}
						});

                    }else{
                        swal({
                            title: 'Existe mas de una Bodega de Despacho',
                            text: "Debe seleccionar una",
                            icon: 'warning',
                            buttons: {
                                confirm: "Aceptar"
                            },
                        }).then((value) => {
                            if (value) {
                            }
                        });
    
                    }
                }else{
                    swal({
                        title: 'No existe Bodega de Despacho',
                        text: "Debe ser creada la bodega de Despacho",
                        icon: 'warning',
                        buttons: {
                            confirm: "Aceptar"
                        },
                    }).then((value) => {
                        if (value) {
                        }
                    });

                }
                return respuesta;
            }

            if (funcion=='validarupdated') {
                if (respuesta.mensaje == "ok") {
                    var ruta = '/dteguiadesp/consultardteguiadesp';
                    ajaxRequest(datatemp,ruta,'consultaranularguiafact');
                } else {
                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                    redirigirARuta(datatemp.rutarecarga); //Muestra el mensaje de registro modificado y luego espera 2.5 seg y recarga pagina
                }
            }

            if (funcion=='validarupdated1') {
                if (respuesta.mensaje == "ok") {
                    //var ruta = '/guiadespanul/store';
                    var ruta = '/dteguiadesp/guiadespanul';
                    ajaxRequest(datatemp,ruta,'guardarguiadespanul');
                } else {
                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                    redirigirARuta(datatemp.rutarecarga); //Muestra el mensaje de registro modificado y luego espera 2.5 seg y recarga pagina
                }
            }


            if (funcion=='guardarguiadespanul') {
                if (respuesta.mensaje == "ok") {
                    var ruta = '/guardaranularguia';
                    ajaxRequest(datatemp,ruta,'guardaranularguia');
                } else {
                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                }
            }
            if(funcion=='guardaranularguia'){
				if (respuesta.mensaje == "ok") {
                    //console.log(datatemp);
                    //console.log("#fila" + datatemp.guiadesp_id);
					$("#fila" + datatemp.guiadesp_id).remove();
					$("#myModalanularguiafact").modal('hide');
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', 'error');
                    //redirigirARuta(datatemp.rutarecarga); //Muestra el mensaje de registro modificado y luego espera 2.5 seg y recarga pagina

				}
			}
		},
		error: function () {
		}
	});
}

function datosGD(){
    var data = {
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        rut               : eliminarFormatoRutret($("#rut").val()),
        vendedor_id       : $("#vendedor_id").val(),
        oc_id             : $("#oc_id").val(),
        tipoentrega_id    : $("#tipoentrega_id").val(),
        notaventa_id      : $("#notaventa_id").val(),
        aprobstatus       : $("#aprobstatus").val(),
        comuna_id         : $("#comuna_id").val(),
        despachoord_id    : $("#despachoord_id").val(),
        producto_id       : $("#producto_idPxP").val(),
        filtro            : 1,
        dtenotnull        : 1, //Estatus que se envia a la consulta para mostrar o no los dte anulados (1=no se trae los anulados ""=empty se trae todo sin importar que esta anulado)
        dteguiausada      : 1,
        _token            : $('input[name=_token]').val()
    };

    var data2 = "?fechad="+data.fechad+"&fechah="+data.fechah +
    "&rut=" + data.rut +
    "&vendedor_id=" + data.vendedor_id +
    "&oc_id=" + data.oc_id +
    "&tipoentrega_id=" + data.tipoentrega_id +
    "&notaventa_id=" + data.notaventa_id +
    "&aprobstatus=" + data.aprobstatus +
    "&comuna_id=" + data.comuna_id +
    "&despachoord_id=" + data.despachoord_id +
    "&filtro=" + data.filtro +
    "&dtenotnull=" + data.dtenotnull +
    "&dteguiausada=" + data.dteguiausada;

    var data = {
        data1 : data,
        data2 : data2
    };

    return data;
}

function consultar(data){
    $.ajax({
        url: '/despachosol/reportesoldesp',
        type: 'POST',
        data: data,
        success: function (datos) {
            if(datos['tabla'].length>0){
                $("#tablaconsulta").html(datos['tabla']);
                configurarTabla('#pendientesoldesp');
                $('.datepickerfed').datepicker({
                    language: "es",
                    autoclose: true,
                    todayHighlight: true
                }).datepicker("setDate");
                let  table = $('#pendientesoldesp').DataTable();
                table
                    .on('draw', function () {
                        eventFired( 'Page' );
                    });
            
            }
        }
    });
}

function consultarcerrarNV(data){
    $.ajax({
        url: '/despachosol/reportesoldespcerrarNV',
        type: 'POST',
        data: data,
        success: function (datos) {
            if(datos['tabla'].length>0){
                $("#tablaconsulta").html(datos['tabla']);
                configurarTabla('.tablascons');
            }
        }
    });
}

function consultarpdf(data){
    $.ajax({
        url: '/notaventaconsulta/exportPdf',
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

function visto(id,visto){
    //alert($(this).attr("value"));
    var data = {
        id     : id,
        _token : $('input[name=_token]').val()
    };
    var ruta = '/notaventa/visto/' + id;
    ajaxRequest(data,ruta,'vistonotaventa');
}

$(document).on("click", ".btndevsol", function(event){
    event.preventDefault();
    fila = $(this).closest("tr");
    form = $(this);
    id = fila.find('td:eq(0)').text();
    $('.modal-title').html('Devolver Solicitud Despacho');
    $("#despachosol_id").val(id);
    $("#nfilaDel").val(form.attr('fila'));
    $("#ruta").val(form.attr('href'));
    $("#observacion").val("");
    $("#status").val("1");
    $("#boton").val("btndevsol");
    quitarValidacion($(".requeridos").prop('name'),$(".requeridos").attr('tipoval'));
    $("#myModaldevsoldeps").modal('show');
    
});

$(document).on("click", ".btncerrarsol", function(event){
    event.preventDefault();
    fila = $(this).closest("tr");
    form = $(this);
    id = fila.find('td:eq(0)').text();
    $('.modal-title').html('Cerrar Solicitud Despacho');
    $("#despachosol_id").val(id);
    $("#nfilaDel").val(form.attr('fila'));
    $("#ruta").val(form.attr('href'));
    $("#observacion").val("");
    $("#status").val("2");
    $("#boton").val("btncerrarsol");
    quitarValidacion($(".requeridos").prop('name'),$(".requeridos").attr('tipoval'));
    $("#myModaldevsoldeps").modal('show');
    
});

$("#btnGuardarDSD").click(function(event){
    if(verificarFact())
	{
        swal({
            title: '¿ Desea ' + $('.modal-title').html() + ' ?',
            text: "Esta acción no se puede deshacer!",
            icon: 'warning',
            buttons: {
                cancel: "Cancelar",
                confirm: "Aceptar"
            },
        }).then((value) => {
            /*
            fila = $(this).closest("tr");
            form = $(this);
            id = fila.find('td:eq(0)').text();
                //alert(id);
            */
            var data = {
                id     : $("#despachosol_id").val(),
                nfila  : $("#nfilaDel").val(),
                obs    : $("#observacion").val(),
                status : $("#status").val(),
                _token : $('input[name=_token]').val()
            };
            if (value) {
                ajaxRequest(data,$("#ruta").val(),$("#boton").val(),form);
            }
        });
    }else{
		alertify.error("Falta incluir informacion");
	}
});


function verificarFact()
{
	var v1=0;
	var v2=0;
	
	v1=validacion('observacion','texto');
	v2=true;
	if (v1===false || v2===false)
	{
		return false;
	}else{
		return true;
	}
}


$(".requeridos").keyup(function(){
	//alert($(this).parent().attr('class'));
	quitarValidacion($(this).prop('name'),$(this).attr('tipoval'));
});

$(".requeridos").change(function(){
	//alert($(this).parent().attr('class'));
	quitarValidacion($(this).prop('name'),$(this).attr('tipoval'));
});

function btnpdf(numrep){
    if(numrep==1){
        aux_titulo = 'Indicadores ' + $("#consulta_id option:selected").html();
        data = datosGD();
        cadena = "?fechad="+data.fechad+"&fechah="+data.fechah +
                "&rut=" + data.rut +
                "&vendedor_id=" + data.vendedor_id +
                "&oc_id=" + data.oc_id +
                "&tipoentrega_id=" + data.tipoentrega_id +
                "&notaventa_id=" + data.notaventa_id +
                "&aprobstatus=" + data.aprobstatus +
                "&comuna_id=" + data.comuna_id +
                "&id=" + data.id +
                "&filtro=" + data.filtro;
        $('#contpdf').attr('src', '/despachosol/pdfpendientesoldesp/'+cadena);
        $("#myModalpdf").modal('show'); 
    }
}

$("#btnpdf2").click(function()
{
    aux_titulo = 'Pendientes Solicitud Despacho';
    data = datosGD();
    cadena = "?fechad="+data.fechad+"&fechah="+data.fechah +
            "&rut=" + data.rut +
            "&vendedor_id=" + data.vendedor_id +
            "&oc_id=" + data.oc_id +
            "&tipoentrega_id=" + data.tipoentrega_id +
            "&notaventa_id=" + data.notaventa_id +
            "&aprobstatus=" + data.aprobstatus +
            "&comuna_id=" + data.comuna_id +
            "&id=" + data.id +
            "&filtro=" + data.filtro +
            "&producto_id=" + data.producto_id +
            "&aux_titulo=" + aux_titulo;
    $('#contpdf').attr('src', '/despachosol/pdfpendientesoldesp/'+cadena);
    $("#myModalpdf").modal('show'); 
});


var eventFired = function ( type ) {
	total = 0;
	$("#pendientesoldesp tr .kgpend").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#totalkg").html(MASKLA(total,2))
	total = 0;
	$("#pendientesoldesp tr .dinpend").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#totaldinero").html(MASKLA(total,0))

}

function editfeced(id,i){
    $(".fechaed").hide();
    $(".editfed").show();
    $(".savefed").hide();
    $("#fechaed" + i).show();
    $("#editfed" + i).hide();
    $("#savefed" + i).show();
    $("#fechaed" + i).datepicker({
        language: "es",
        autoclose: true,
        todayHighlight: true
    }).datepicker("setDate");
    $("#fechaed" + i).datepicker("refresh");
    $("#fechaed" + i).focus();
    //alert(i);
}


function savefeced(id,aux_i){
    swal({
        title: '¿ Seguro desea actualizar el registro ?',
        text: "Esta acción no se puede deshacer!",
        icon: 'warning',
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
        if (value) {
            var data = {
                id : id,
                i  : aux_i,
                aux_fechaestdesp : $("#fechaed" + aux_i).val(),
                updated_at : $("#savefed" + aux_i).attr('updated_at'),
                _token : $('input[name=_token]').val()
            };
            var ruta = '/despachosol/guardarfechaed'; //Guardar Fecha estimada de despacho
            ajaxRequest(data,ruta,'guardarfechaed');
        }else{
            restbotoneditfeced(aux_i);
        }
    });
}

function restbotoneditfeced(i){
    $("#fechaestdesp" + i).show();
    $("#fechaed" + i).hide();
    $("#editfed" + i).show();
    $("#savefed" + i).hide();
    $(".datepicker").datepicker("refresh");
}

function anularguiafact(id,despachoord_id){
	$("#idanul").val(id);
	$("#guiadespachoanul").val('');
	$("#nfilaanul").val(id);
	var data = {
		id    : id,
		nfila : id,
        guiadesp_id : id,
        despachoord_id : despachoord_id,
        updated_at : $("#updated_at" + id).html(),
        rutarecarga : "factura/listarguiadesp",
		_token: $('input[name=_token]').val()
	};
    var ruta = "/dteguiadesp/validarupdated";
    ajaxRequest(data,ruta,'validarupdated');
    return 0;

	var ruta = '/guiadesp/consultarguiadesp';
	ajaxRequest(data,ruta,'consultaranularguiafact');
}


$("#btnGuardarGanul").click(function(event)
{
	event.preventDefault();
	if(verificarAnulGuia())
	{
		var data = {
			id         : $("#idanul").val(),
			nfila      : $("#nfilaanul").val(),
            updated_at : $("#guiadespachoanul").attr("updated_at"),
			tipobodega : 3, //Codigo de tipo de bodega = 3 (Bodegas de despacho)
			_token: $('input[name=_token]').val()
		};
		var ruta = '/invbodega/buscarTipoBodegaOrdDesp';
		respuesta = ajaxRequest(data,ruta,'buscarTipoBodegaOrdDesp');
	}else{
		alertify.error("Falta incluir informacion");
	}
	
});

function verificarAnulGuia()
{
	var v1=0;
	var v2=0;
	v2=validacion('statusM','combobox');
	v1=validacion('observacionanul','texto');
	if (v1===false || v2===false)
	{
		return false;
	}else{
		return true;
	}
}
