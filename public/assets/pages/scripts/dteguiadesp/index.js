$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    i = 0;
    $('#tabla-data-dteguiadespacho').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "dteguiadesppage",
        "order": [[ 0, "desc" ]],
        'columns'     : [
            {data: 'id'},
            {data: 'fechahora'},
            {data: 'fchemis'},
            {data: 'razonsocial'},
            {data: 'oc_id'},
            {data: 'notaventa_id'},
            {data: 'despachosol_id'},
            {data: 'despachoord_id'},
            {data: 'nrodocto'},
            {data: 'cmnarecep'},
            {data: 'kgtotal'},
            {data: 'tipoentrega_nombre'},
            {data: 'icono',className:"ocultar"},
            {data: 'clientebloqueado_descripcion',className:"ocultar"},
            {data: 'oc_file',className:"ocultar"},
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
            //"<a href='#' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + "</a>";
            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Guia despacho: " + data.nrodocto + "' onclick='genpdfGD(" + data.nrodocto + ",\"\")'>" +
                    + data.id +
                "</a>";
            $('td', row).eq(0).html(aux_text);

            $('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));
            $('td', row).eq(2).attr('data-order',data.fchemis);
            aux_fecha = new Date(data.fchemis +" 00:00:00");
            $('td', row).eq(2).html(fechaddmmaaaa(aux_fecha));

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
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Solicitud Despacho' onclick='genpdfSD(" + data.despachosol_id + ",1)'>" + 
                    data.despachosol_id + 
                "</a>";
            $('td', row).eq(6).html(aux_text);
            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Orden Despacho' onclick='genpdfOD(" + data.despachoord_id + ",1)'>" + 
                    data.despachoord_id + 
                "</a>";
            $('td', row).eq(7).html(aux_text);
            aux_text = 
                    "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Guia Despacho' onclick='genpdfGD(" + data.nrodocto + ",\"\")'>" +
                        data.nrodocto +
                    "</a>";
            $('td', row).eq(8).html(aux_text);
            $('td', row).eq(8).attr('style','text-align:center');
            $('td', row).eq(10).attr('data-order',data.kgtotal);
            $('td', row).eq(10).attr('style','text-align:right');
            aux_text = MASKLA(data.kgtotal,2);
            $('td', row).eq(10).html(aux_text);
            $('td', row).eq(10).addClass('subtotalkg');

            
            aux_text = 
                "<i class='fa fa-fw " + data.icono + " tooltipsC' title='" + data.tipoentrega_nombre + "'></i>";
            $('td', row).eq(12).html(aux_text);

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
                "<a href='/despachoord/aproborddesp' class='btn-accion-tabla btn-sm tooltipsC btnaprobar' title='Aprobar Orden Despacho'>" +
                    "<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>"+
                "</a>"+
*/
                aux_text = 
                "<a id='bntaproord'" + data.id + " name='bntaproord'" + data.id + " class='btn-accion-tabla btn-sm tooltipsC' onclick='procesar(" + data.id + "," + data.despachoord_id + ")' title='Enviar a procesados'>"+
                    "<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>"+
                "</a>";
                /*
                "<a href='dteguiadesp' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>"+
                    "<i class='fa fa-fw fa-pencil'></i>"
                "</a>";
                */
            }
            $('td', row).eq(15).addClass('updated_at');
            $('td', row).eq(15).attr('id','updated_at' + data.id);
            $('td', row).eq(15).attr('name','updated_at' + data.id);

            aux_text = aux_text +
            "<a onclick='anularguiafact(" + data.id + "," + data.despachoord_id + ",\"dteguiadesp\")' class='btn-accion-tabla btn-sm tooltipsC' title='Anular registro y devolver a Orden de Despacho' data-toggle='tooltip'>"+
                "<span class='glyphicon glyphicon-remove text-danger'></span>"
            "</a>";
            $('td', row).eq(16).html(aux_text);
        }
    });

    totalizarTabla();

});


function totalizarTabla(){
    let  table = $('#tabla-data-dteguiadespacho').DataTable();
    //console.log(table);
    table
    .on('draw', function () {
        eventFired( 'Page' );
    });

    $.ajax({
        url: '/dteguiadesp/totalizarindex',
        type: 'GET',
        success: function (datos) {
            //console.log(datos);
            $("#totalkg").html(MASKLA(datos.kgtotal,2));
            $("#totalkg").attr('valor',datos.kgtotal);
            //$("#totaldinero").html(MASKLA(datos.aux_totaldinero,0));
        }
    });

}


var eventFired = function ( type ) {
    totalizarpagina();
}

function totalizarpagina(){
	total = 0;
	$("#tabla-data-dteguiadespacho tr .subtotalkg").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    //console.log(total);
    $("#subtotalkg").html(MASKLA(total,2))
}

function ajaxRequest(data,url,funcion) {
    datatemp = data;
    /*
    if (funcion=='eliminar') {
        console.log(data);
        console.log(url);
        return 0;
    }
    */
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			
			if(funcion=='guardarguiadesp'){
				if (respuesta.mensaje == "ok") {
                    /*
					swal({
						title: '¿ Desea ver Guia Despacho ?',
						text: "",
						icon: 'success',
						buttons: {
							cancel: "Cancelar",
							confirm: "Aceptar"
						},
					}).then((value) => {
						if (value) {
							genpdfGD(respuesta.nrodocto,"_U");
						}
						$("#fila"+datatemp.nfila).remove();
					});
                    */
                    $("#fila"+datatemp.nfila).remove();
                    totalizarpagina();
                    totalizarTabla();
                    //genpdfGD(respuesta.nrodocto,"_U");
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
                    swal({
						//title: 'Error',
						text: respuesta.mensaje,
						icon: 'error',
						buttons: {
							confirm: "Cerrar"
						},
					});

					//Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
				}
			}
            if(funcion=='buscarBodegaDespachoAsignarGuia'){
                if(respuesta.datas.length > 0){
                    if(respuesta.datas.length == 1){
                        /*console.log(respuesta.datas);
                        console.log(datatemp);
                        */
                        /*
						var data = {
							id    : $("#idg").val(),
							guiadespacho : $("#guiadespachom").val(),
							nfila : $("#nfila").val(),
							status : $("#status").val(),
							invbodega_id : respuesta.datas[0].id,
							_token: $('input[name=_token]').val()
						};
                        */
                        var data = {
                            despachoord_id : datatemp.id,
                            guiadesp_id    : datatemp.nfila,
                            nfila          : datatemp.nfila,
                            invbodega_id   : respuesta.datas[0].id,
                            updated_at : $("#updated_at" + datatemp.nfila).html(),
                            _token: $('input[name=_token]').val()
                        };
                        var ruta = '/dteguiadesp/guardardteguiadesp';
                        //var ruta = '/guiadesp/dteguiadesp';
                        //console.log(data);
                        swal({
                            title: '¿ Procesar DTE Guia Despacho ?',
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
                        swal({
                            title: 'Existe mas de una Bodega de Despacho para Sucursal',
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
                        title: 'No existe Bodega de Despacho para Sucursal',
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
            if(funcion=='consultaranularguiafact'){
				if (respuesta.mensaje == "ok") {
					//alert(respuesta.despachoord.guiadespacho);
					//$("#guiadespachoanul").val(respuesta.despachoord.guiadespacho);
					//$(".requeridos").keyup();
					quitarvalidacioneach();
                    $("#guiadesp_id").val(datatemp.guiadesp_id);
                    $("#updated_at").val(datatemp.updated_at);
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
                            dte_id      : $("#nfilaanul").val(),
							guiadesp_id : $("#nfilaanul").val(),
							despachoord_id : $("#idanul").val(),
							observacion : $("#observacionanul").val(),
							obs : $("#observacionanul").val(),
                            motanul_id : 4,
                            moddevgiadesp_id : 'GD',
							statusM : $("#statusM").val(),
							invbodega_id : respuesta.datas[0].id,
							pantalla_origen  : 1, //Para saber de donde viene la anulacion en este caso de la pantalla Generar Guia despacho SII
                            updated_at : datatemp.updated_at,
                            rutarecarga : "dteguiadesp",
                            guiadesp_id : $("#guiadesp_id").val(),   
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
                    var ruta = '/despachoord/consultarod';
                    ajaxRequest(datatemp,ruta,'consultaranularguiafact');    
                } else {
                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                    //console.log(datatemp.rutarecarga);
                    redirigirARuta(datatemp.rutarecarga); //Muestra el mensaje de registro modificado y luego espera 2.5 seg y recarga pagina
                }
            }
            if (funcion=='eliminar') { //Elimino desde aqui porque debo hacer previamente varias validaciones
                if (respuesta.mensaje == "ok") {
                    var ruta = '/guardaranularguia';
                    delete datatemp._method;
                    ajaxRequest(datatemp,ruta,'guardaranularguia');
                } else {
                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                }
            }
            if (funcion=='validarupdated1') {
                if (respuesta.mensaje == "ok") {
                    var ruta = '/dteguiadesp/guiadespanul';
                    ajaxRequest(datatemp,ruta,'guardarguiadespanul');    
                } else {
                    $("#myModalanularguiafact").modal('hide');
                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                    redirigirARuta(datatemp.rutarecarga);
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
                    aux_nfila = $("#nfilaanul").val();
					$("#fila" + aux_nfila).remove();
					$("#myModalanularguiafact").modal('hide');
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
					//Biblioteca.notificaciones('Registro no fue guardado.', 'Plastiservi', 'error');
				}
			}
		},
		error: function () {
		}
	});
}


function procesar(i,id){
    var data = {
        id         : id,
        nfila      : i,
        tipobodega : 3, //Codigo de tipo de bodega = 3 (Bodegas de despacho)
        _token: $('input[name=_token]').val()
    };
    var ruta = '/invbodega/buscarTipoBodegaOrdDesp';
    respuesta = ajaxRequest(data,ruta,'buscarBodegaDespachoAsignarGuia');
}

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

function anularguiafact(nfila,id,aux_rutacargs){
	$("#idanul").val(id);
	$("#guiadespachoanul").val(nfila);
    $("#id2").html("Id Guia Despacho:");
	$("#nfilaanul").val(nfila);
    $("#tituloAGFAC").html("Devolver a Orden de Despacho");
	var data = {
		id    : id,
		nfila : nfila,
		guiadesp_id : nfila,
		updated_at : $("#updated_at" + nfila).html(),
        rutarecarga : aux_rutacargs,
		_token: $('input[name=_token]').val()
	};
    var ruta = "/dteguiadesp/validarupdated";
    ajaxRequest(data,ruta,'validarupdated');
    return 0;

	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
            if (respuesta.mensaje == "ok") {
                var ruta = '/despachoord/consultarod';
                ajaxRequest(data,ruta,'consultaranularguiafact');
            } else {
                Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
            }
		},
		error: function () {
		}
	});


}


$("#btnGuardarGanul").click(function(event)
{
	event.preventDefault();
	if(verificarAnulGuia())
	{
		var data = {
			id          : $("#idanul").val(),
			nfila       : $("#nfilaanul").val(),
			guiadesp_id : $("#guiadesp_id").val(),
			dte_id      : $("#guiadesp_id").val(),
			updated_at  : $("#updated_at").val(),
			tipobodega  : 3, //Codigo de tipo de bodega = 3 (Bodegas de despacho)
			_token: $('input[name=_token]').val()
		};
        //console.log(data);
		var ruta = '/invbodega/buscarTipoBodegaOrdDesp';
		respuesta = ajaxRequest(data,ruta,'buscarTipoBodegaOrdDesp');
	}else{
		alertify.error("Falta incluir informacion");
	}
});