$(document).ready(function () {
	Biblioteca.validacionGeneral('form-general');
    i = 0;
	data = datosdespguia();
    $('#tabla-data-despachoordguia').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
		"order"       : [[ 0, "desc" ]],
        'ajax'        : "despachoordguiapage/" + data.data2,
        'columns'     : [
            {data: 'id'},
            {data: 'fechahora'},
            {data: 'fechaestdesp'},
            {data: 'razonsocial'},
            {data: 'id'},
            {data: 'despachosol_id'},
            {data: 'oc_id'},
            {data: 'notaventa_id'},
            {data: 'comuna_nombre'},
            {data: 'aux_totalkg'},
            {data: 'subtotal'},
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
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Orden despacho: " + data.id + "' onclick='genpdfOD(" + data.id + ",1)'>"+
                    + data.id +
                "</a>";
            $('td', row).eq(0).html(aux_text);

            $('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));

            $('td', row).eq(2).attr('data-order',data.fechaestdesp);
            aux_fecha = new Date(data.fechaestdesp + " 12:00:00");
            $('td', row).eq(2).html(fechaddmmaaaa(aux_fecha));

			aux_text = 
				"<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Orden despacho: " + data.id + "' onclick='genpdfOD(" + data.id + ",1)'>"+
					+ data.id +
				"</a>";
			$('td', row).eq(4).html(aux_text);


			aux_text = 
				"<a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfSD(" + data.despachosol_id + ",1)'>" +
					data.despachosol_id +
				"</a>";
			$('td', row).eq(5).html(aux_text);

            if(data.oc_file != "" && data.oc_file != null){
                aux_text = 
                    "<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Orden de Compra' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + 
                        data.oc_id + 
                    "</a>";
                $('td', row).eq(6).html(aux_text);
				if(data.dte_nrodocto != null){
                    let cadena = data.dte_nrodocto
					if(cadena.includes(";")){
                        aux_nroguia = cadena.split(";")[0]; 
                        aux_ocid = cadena.split(";")[1]; 
						aux_folderNamefile = cadena.split(";")[2];
					}
                    aux_title = `Orden de Compra ${data.oc_id}, tiene Guia de despacho generada previamente: ${aux_nroguia}`;
                    colorinfo = `text-red`;
                    aux_text +=
                        `<br><a class="btn-sm tooltipsC" title="${aux_title}" style="padding-left: 0px;padding-right: 0px;">
                            <i class="fa fa-fw fa-question-circle ${colorinfo}"></i>
                        </a>`;

                    aux_text += 
                    `<a class="btn-accion-tabla btn-sm tooltipsC" onclick="genpdfGD('${aux_nroguia}','')" data-original-title="Guia despacho:${aux_nroguia}" style='color:#bc3c3c'>
                        ${aux_nroguia}
                    </a>,`;

                    aux_text += 
                    `<a class="btn-accion-tabla btn-sm tooltipsC" title="Orden de Compra" onclick="verpdf2('${aux_folderNamefile}',2)" style='color:#bc3c3c'>
                        ${aux_ocid}
                    </a>`;

                    $('td', row).eq(6).html(aux_text);
                }
            }

			aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV(" + data.notaventa_id + ",1)'>" +
                    data.notaventa_id +
                "</a>";
            $('td', row).eq(7).html(aux_text);


            $('td', row).eq(9).attr('data-order',data.aux_totalkg);
            $('td', row).eq(9).attr('style','text-align:right');
            aux_text = MASKLA(data.aux_totalkg,2);
            $('td', row).eq(9).html(aux_text);
            $('td', row).eq(9).addClass('subtotalkg');

            $('td', row).eq(10).attr('data-order',data.subtotal);
            $('td', row).eq(10).attr('style','text-align:right');
            aux_text = MASKLA(data.subtotal,0);
            $('td', row).eq(10).html(aux_text);
            $('td', row).eq(10).addClass('subtotal');

            aux_text = 
                "<i class='fa fa-fw " + data.icono + " tooltipsC' title='" + data.tipoentrega_nombre + "'></i>";
            $('td', row).eq(11).html(aux_text);

			$('td', row).eq(15).addClass('updated_at');
            $('td', row).eq(15).attr('item',data.id);
            $('td', row).eq(15).attr('id','updated_at'+data.id);
            $('td', row).eq(15).attr('name','updated_at'+data.id);

			aux_text = 
			`<a onclick="guiadesp(${data.id},${data.id},1)" class="btn btn-primary btn-xs tooltipsC" title="Guia de despacho">
				Guia
			</a> | 
			<a onclick="anularguiafact(${data.id},${data.id})" class="btn btn-danger btn-xs" title="Anular Guia" data-toggle="tooltip">
				Anular
			</a>`;
			/*
			aux_text = aux_text +
            "<a href='despachoord' class='btn-accion-tabla btn-sm btnAnular tooltipsC' title='Anular Orden Despacho' data-toggle='tooltip'>"+
                "<span class='glyphicon glyphicon-remove text-danger'></span>"
            "</a>";
            */

            $('td', row).eq(16).html(aux_text);
        }
    });

	$('.datepicker').datepicker({
		language: "es",
		autoclose: true,
		todayHighlight: true
	}).datepicker("setDate");

	$(".numerico").numeric({ negative : false });

	totalizar();
	$("#btnconsultar").click(function(){
		consultar();
    });

});

function consultar(){
	data = datosdespguia();
	$('#tabla-data-despachoordguia').DataTable().ajax.url( "despachoordguiapage/" + data.data2 ).load();
	totalizar();
}

$('#sucursal_id').on('change', function () {
	consultar();
});


function totalizar(){
    let  table = $('#tabla-data-despachoordguia').DataTable();
    //console.log(table);
    table
        .on('draw', function () {
            eventFired( 'Page' );
        });
	data = datosdespguia();
    $.ajax({
        url: '/despachoordguia/totalizarindex/' + data.data2,
        type: 'GET',
        success: function (datos) {
            $("#totalkg").html(MASKLA(datos.aux_totalkg,2));
            $("#total").html(MASKLA(datos.aux_subtotal,0));
		}
    });

}

var eventFired = function ( type ) {
	total = 0;
	$("#tabla-data-despachoordguia tr .subtotalkg").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#subtotalkg").html(MASKLA(total,2))
	total = 0;
	$("#tabla-data-despachoordguia tr .subtotal").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#subtotal").html(MASKLA(total,0))

}

function ajaxRequest(datas,url,funcion) {
	aux_datas = datas;
	$.ajax({
		url: url,
		type: 'POST',
		data: datas,
		success: function (respuesta) {
			//console.log(datas);
			if(funcion=='guardarguiadesp'){
                if(respuesta.status == "1"){
					if(aux_datas['status']=='1'){
						$("#fila" + aux_datas['nfila']).remove();
					}else{
						$("#guiadespacho" + aux_datas['nfila']).html(respuesta.despachoord.guiadespacho);
						$("#fechaguia" + aux_datas['nfila']).html(respuesta.guiadespachofec);	
					}
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				}else{
                    swal({
                        title: respuesta.title,
                        text: respuesta.mensaje,
                        icon: respuesta.tipo_alert,
                        buttons: {
                            cancel: "Cerrar",
                        },
                    });
                }
				$("#myModalguiadesp").modal('hide');
				return 0
				if (respuesta.mensaje == "ok") {
					//alert(data['nfila']);
					if(datas['status']=='1'){
						$("#fila" + datas['nfila']).remove();
					}else{
						$("#guiadespacho" + datas['nfila']).html(respuesta.despachoord.guiadespacho);
						$("#fechaguia" + datas['nfila']).html(respuesta.guiadespachofec);	
					}
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					Biblioteca.notificaciones('Registro no fue guardado.', 'Plastiservi', 'error');
					if(respuesta.mensaje != "ng"){
						Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', 'error');
						swal({
							title: 'Producto sin stock suficiente.',
							text: respuesta.mensaje,
								icon: 'warning',
							buttons: {
								confirm: "Aceptar"
							},
						})
					}
				}
				$("#myModalguiadesp").modal('hide');
			}
			if(funcion=='guardarfactdesp'){
				if (respuesta.mensaje == "ok") {
					if(datas['status'] == 1){
						$("#fila" + datas['nfila']).remove();
					}else{
						$("#numfactura" + datas['nfila']).html(datas['numfactura']);
						$("#fechafactura" + datas['nfila']).html(respuesta.despachoord.fechafactura);	
					}
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					Biblioteca.notificaciones('Registro no fue guardado.', 'Plastiservi', 'error');
					if(respuesta.mensaje != "ng"){
						Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', 'error');
					}
				}
				$("#myModalnumfactura").modal('hide');
			}
			if(funcion=='consultarguiadespachood'){
				if (respuesta.mensaje == "ok") {
					if(datas['status']=='1'){
						$("#guiadespachom").val(respuesta.despachoord.guiadespacho);
						quitarvalidacioneach();
					}else{
						quitarvalidacioneach();
						$("#guiadespachom").val(respuesta.despachoord.guiadespacho);
					}
					$("#myModalguiadesp").modal('show');
				} else {
					Biblioteca.notificaciones('Registro no encontrado.', 'Plastiservi', 'error');
				}
			}
			if(funcion=='consultarnumfacturaod'){
				if (respuesta.mensaje == "ok") {
					//alert(respuesta.despachoord.numfactura);
					if(datas['status']=='1'){
						quitarvalidacioneach();	
					}else{
						quitarvalidacioneach();	
						$("#numfacturam").val(respuesta.despachoord.numfactura);
						$("#fechafacturam").val(respuesta.fechafactura);
						$("#fechafacturam").datepicker("setDate",respuesta.fechafactura)	
					}
					
					//$(".requeridos").keyup();
					$("#myModalnumfactura").modal('show');
				} else {
					Biblioteca.notificaciones('Registro no encontrado.', 'Plastiservi', 'error');
				}
			}
			if(funcion=='consultaranularguiafact'){
				if (respuesta.mensaje == "ok") {
					//alert(respuesta.despachoord.guiadespacho);
					$("#guiadespachoanul").val(respuesta.despachoord.guiadespacho);
					//$(".requeridos").keyup();
					quitarvalidacioneach();
					$("#myModalanularguiafact").modal('show');
				} else {
					Biblioteca.notificaciones('Registro no encontrado.', 'Plastiservi', 'error');
				}
			}
			
			if(funcion=='guardaranularguia'){
				if (respuesta.status == "1") {
					$("#fila" + respuesta.nfila).remove();
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					swal({
                        title: respuesta.title,
                        text: respuesta.mensaje,
                        icon: respuesta.tipo_alert,
                        buttons: {
                            cancel: "Cerrar",
                        },
                    });
				}
				$("#myModalanularguiafact").modal('hide');
			}

			if(funcion=='buscarTipoBodegaOrdDesp'){
                if(respuesta.datas.length > 0){
                    if(respuesta.datas.length == 1){	
						var data = {
							id    : $("#idanul").val(),
							nfila : $("#nfilaanul").val(),
							observacion : $("#observacionanul").val(),
							statusM : $("#statusM").val(),
							invbodega_id : respuesta.datas[0].id,
							pantalla_origen  : 1, //Para saber de donde viene la anulacion en este caso de la pantalla Asignar Guia
							_token: $('input[name=_token]').val()
						};
						var ruta = '/guardaranularguia';
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
								ajaxRequest(data,ruta,'guardaranularguia');
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

			if(funcion=='buscarBodegaDespachoAsignarGuia'){
                if(respuesta.datas.length > 0){
                    if(respuesta.datas.length == 1){

						var data = {
							id    : $("#idg").val(),
							guiadespacho : $("#guiadespachom").val(),
							nfila : $("#nfila").val(),
							status : $("#status").val(),
							invbodega_id : respuesta.datas[0].id,
							_token: $('input[name=_token]').val()
						};
						var ruta = '/despachoord/guardarguiadesp';
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
								ajaxRequest(data,ruta,'guardarguiadesp');
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
			if(funcion=='staverfacdesp'){
				if (respuesta.error == 0) {
                    //$("#dtefac_updated_at" + aux_data.dte_id).html(respuesta.dtefac_updated_at);
				} else {
                    estaSeleccionado = $("#aux_staverfacdesp" + aux_data.dte_id).is(":checked");
                    if(estaSeleccionado){
                        $("#aux_staverfacdesp" + aux_data.dte_id).prop('checked',false);
                    }else{
                        $("#aux_staverfacdesp" + aux_data.dte_id).prop('checked',true);
                    }
				}
                Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
            }


		},
		error: function () {
		}
	});
}

function guiadesp(nfila,id,status){
	$("#idg").val(id);
	$("#nfila").val(nfila);
	$("#guiadespachom").val('');
	$("#status").val(status);
	var data = {
		id    : id,
		nfila : nfila,
		status : status,
		_token: $('input[name=_token]').val()
	};
	var ruta = '/despachoord/consultarod';
	ajaxRequest(data,ruta,'consultarguiadespachood');
}

$("#btnGuardarG").click(function(event)
{
	event.preventDefault();
	if(verificarGuia())
	{
		var data = {
			guiadespacho: $("#guiadespachom").val(),
			_token: $('input[name=_token]').val()
		};
		$.ajax({
			url: '/despachoord/buscarguiadesp',
			type: 'POST',
			data: data,
			success: function (respuesta) {
				if(respuesta.mensaje == 'ok'){
					swal({
						title: 'Guia despacho N°.' +$("#guiadespachom").val()+ ' ya existe.',
						text: "",
						icon: 'error',
						buttons: {
							confirm: "Aceptar"
						},
					}).then((value) => {
						if (value) {
							//$("#oc_id").focus();
						}
					});
				
				}else{
					aux_updated_at = $("#updated_at" + $("#idg").val()).html();
					var data = {
						id    : $("#idg").val(),
						guiadespacho : $("#guiadespachom").val(),
						nfila : $("#nfila").val(),
						status : $("#status").val(),
						updated_at : aux_updated_at,
						//invbodega_id : respuesta.datas[0].id,
						_token: $('input[name=_token]').val()
					};
					var ruta = '/despachoord/guardarguiadesp';
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
							ajaxRequest(data,ruta,'guardarguiadesp');
						}
					});
					return 0;

					var data = {
						id         : $("#idg").val(),
						nfila      : $("#nfila").val(),
						tipobodega : 3, //Codigo de tipo de bodega = 3 (Bodegas de despacho)
						_token: $('input[name=_token]').val()
					};
					var ruta = '/invbodega/buscarTipoBodegaOrdDesp';
					respuesta = ajaxRequest(data,ruta,'buscarBodegaDespachoAsignarGuia');
					return 0;

					var data = {
						id    : $("#idg").val(),
						guiadespacho : $("#guiadespachom").val(),
						nfila : $("#nfila").val(),
						status : $("#status").val(),
						_token: $('input[name=_token]').val()
					};
					var ruta = '/despachoord/guardarguiadesp';
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
							ajaxRequest(data,ruta,'guardarguiadesp');
						}
					});			
				}
			}
		});		
		
	}else{
		alertify.error("Falta incluir informacion");
	}
	
});

$("#btnGuardarGanul").click(function(event)
{
	event.preventDefault();
	if(verificarAnulGuia())
	{
		aux_updated_at = $("#updated_at" + $("#idanul").val()).html();
		var data = {
			id    : $("#idanul").val(),
			nfila : $("#nfilaanul").val(),
			despachoord_id    : $("#idanul").val(),
			observacion : $("#observacionanul").val(),
			statusM : $("#statusM").val(),
			//invbodega_id : respuesta.datas[0].id,
			pantalla_origen  : 1, //Para saber de donde viene la anulacion en este caso de la pantalla Asignar Guia
			updated_at : aux_updated_at,
			_token: $('input[name=_token]').val()
		};
		var ruta = '/guardaranularguia';
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
				ajaxRequest(data,ruta,'guardaranularguia');
			}
		});
		return 0;
		var data = {
			id         : $("#idanul").val(),
			nfila      : $("#nfilaanul").val(),
			tipobodega : 3, //Codigo de tipo de bodega = 3 (Bodegas de despacho)
			_token: $('input[name=_token]').val()
		};
		var ruta = '/invbodega/buscarTipoBodegaOrdDesp';
		respuesta = ajaxRequest(data,ruta,'buscarTipoBodegaOrdDesp');
		return 0;

		var data = {
			id    : $("#idanul").val(),
			nfila : $("#nfilaanul").val(),
			observacion : $("#observacionanul").val(),
			statusM : $("#statusM").val(),
			_token: $('input[name=_token]').val()
		};
		var ruta = '/guardaranularguia';
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
				ajaxRequest(data,ruta,'guardaranularguia');
			}
		});

	}else{
		alertify.error("Falta incluir informacion");
	}
	
});



function numfactura(nfila,id,status){
	$("#idf").val(id);
	$("#numfacturam").val('');
	$("#fechafacturam").val('');
	$("#nfilaf").val(nfila);
	$("#status").val(status);
	var data = {
		id    : id,
		nfila : nfila,
		status: status,
		_token: $('input[name=_token]').val()
	};
	var ruta = '/despachoord/consultarod';
	ajaxRequest(data,ruta,'consultarnumfacturaod');
}

function anularguiafact(nfila,id){
	$("#idanul").val(id);
	$("#guiadespachoanul").val('');
	$("#nfilaanul").val(nfila);
	var data = {
		id    : id,
		nfila : nfila,
		_token: $('input[name=_token]').val()
	};
	var ruta = '/despachoord/consultarod';
	ajaxRequest(data,ruta,'consultaranularguiafact');
}


$("#btnGuardarF").click(function(event)
{
	event.preventDefault();
	if(verificarFact())
	{
		var data = {
			id    : $("#idf").val(),
			numfactura   : $("#numfacturam").val(),
			fechafactura : $("#fechafacturam").val(),
			nfila : $("#nfilaf").val(),
			status : $("#status").val(),
			_token: $('input[name=_token]').val()
		};
		var ruta = '/despachoord/guardarfactdesp';
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
				ajaxRequest(data,ruta,'guardarfactdesp');
			}
		});

	}else{
		alertify.error("Falta incluir informacion");
	}
	
});


$(".requeridos").keyup(function(){
	//alert($(this).parent().attr('class'));
	quitarValidacion($(this).prop('name'),$(this).attr('tipoval'));
});

$(".requeridos").change(function(){
	//alert($(this).parent().attr('class'));
	quitarValidacion($(this).prop('name'),$(this).attr('tipoval'));
});


function verificarGuia()
{
	var v1=0;
	
	v1=validacion('guiadespachom','texto');
	if (v1===false)
	{
		return false;
	}else{
		return true;
	}
}

function verificarFact()
{
	var v1=0;
	var v2=0;
	
	v1=validacion('numfacturam','texto');
	v2=validacion('fechafacturam','texto');
	if (v1===false || v2===false)
	{
		return false;
	}else{
		return true;
	}
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

function clickbloquearhacerguia(obj){
    let item = $(obj).attr("item");
    var data = {
        dte_id : item,
        updated_at : $("#updated_at" + item).html(),
        dtefac_updated_at : $("#dtefac_updated_at" + item).html(),
        staverfacdesp : $(obj).prop('checked'),
        _token : $('input[name=_token]').val()
    };
    var ruta = '/despachoordguia/bloquearhacerguia'; //Guardar Fecha estimada de despacho
    ajaxRequest(data,ruta,'staverfacdesp');

}

function datosdespguia(){
    var data1 = {
        sucursal_id       : $("#sucursal_id").val(),
        _token            : $('input[name=_token]').val()
    };
    var data2 = "?sucursal_id="+data1.sucursal_id
    var data = {
        data1 : data1,
        data2 : data2
    };
    return data;
}