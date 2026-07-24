$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    i = 0;
    $('#tabla-data-factura').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        "order"       : [[ 0, "desc" ]],
        'ajax'        : "otpage",
        'columns'     : [
            {data: 'id'}, // 0
            {data: 'fechahora'}, // 1
            {data: 'rut'}, // 2
            {data: 'razonsocial'}, // 3
            {data: 'oc_id'}, // 4
            {data: 'comuna_nombre'}, // 5
            {data: 'clientebloqueado_descripcion',className:"ocultar"}, //6
            {data: 'oc_file',className:"ocultar"}, //7
            {data: 'oc_file',className:"ocultar"}, //8
            {data: 'updated_at',className:"ocultar"}, //9
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : 
                "<a href='/ot/enviaraprobarinventsal' class='btn-accion-tabla btn-sm btnaprobar' title='Aprobar'>" +
                    "<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>"+
                "</a>"+
                "<a href='ot' class='btn-accion-tabla btnEditar' title='Editar este registro'>" + 
                    "<i class='fa fa-fw fa-pencil'></i>" + 
                "</a>"+
                "<a href='ot' class='btn-accion-tabla btnEliminar' title='Eliminar este registro'>"+
                    "<i class='fa fa-fw fa-trash text-danger'></i>"+
                "</a>"
            }
        ],
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            $(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);
            $(row).attr('updated_at', data.updated_at);
            //"<a href='#' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + "</a>";

            aux_text = 
                `<a class="btn-accion-tabla btn-sm" title="Ver OT: ${data.id}" onclick='genpdf(${data.id},"","ver-pdf-guia-despacho","/ot/exportPdf/${data.id}")'>
                    ${data.id}
                </a>`;
            $('td', row).eq(0).html(aux_text);
            if(data.obsrechazo != "" && data.obsrechazo != null){
				aux_text += 
				`<a class='btn-sm tooltipsC' title='${data.obsrechazo}'>
					<i class='fa fa-fw fa-question-circle text-red'></i>
				</a>`;
				$('td', row).eq(0).html(aux_text);
			}



            $('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));


            aux_text = "";
            if(data.oc_file != "" && data.oc_file != null){
                aux_text = 
                "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm' title='Orden de Compra' onclick='verpdf3(\"" + data.oc_file + "\",2,\"oc\")'>" + 
                    data.oc_id + 
                "</a>";
                $('td', row).eq(4).html(aux_text);
            }

            $('td', row).eq(9).addClass('updated_at');
            $('td', row).eq(9).attr('id','updated_at' + data.id);
            $('td', row).eq(9).attr('name','updated_at' + data.id);

			aux_clienteBloqueado = validarClienteBloqueadoxModulo(data); 
			aux_displaybtnac = ``;
			aux_displaybtnbl = ``;
			if(aux_clienteBloqueado == ""){
				aux_displaybtnac = ``;
				aux_displaybtnbl = `style="display:none;"`;
			}else{
				aux_displaybtnac = `style="display:none;"`;
				aux_displaybtnbl = ``;
			}
            aprobstatus = 1;
			aux_text = 
				`<div class="tools11">
					<a ${aux_displaybtnbl} class="btn-accion-tabla botonbloq${data.id}" title="Condición financiera en revisión: ${aux_clienteBloqueado}" onclick="llenartablaDataCobranza(${data.id},${data.cliente_id},0,0)" style="padding-left: 0px;">
						<i class="fa fa-fw fa-lock text-danger fa-lg"></i>
					</a>
					<a ${aux_displaybtnac} id="bntaprobnv${data.id}" name="bntaprobnv${data.id}" class="btn-accion-tabla btn-sm action-buttons botonac${data.id}" onclick="procesarReg(${data.id},31,'/ot/procesar',${data.updatednum_at},'Procesar',0)" title="Aprobar" style="padding-left: 0px;">
						<i class="fa fa-fw fa-save acciones1 fa-lg"></i>
					</a>
					<a href="${data.rutaeditar}" class="btn-accion-tabla action-buttons" title="Editar" style="padding-left: 0px;">
						<i class="fa fa-fw fa-pencil acciones1 fa-lg"></i>
					</a>
					<a id="btnanularnv${data.id}" name="btnanularnv${data.id}" class="btn-accion-tabla btn-sm action-buttons" onclick="procesarReg(${data.id},31,'/ot/anular',${data.updatednum_at},'Anular',1)" title="Anular" style="padding-left: 0px;">
						<i class="fa fa-fw fa-close acciones1 fa-lg text-danger"></i>
					</a>
				</div>`;
			//$('td', row).eq(12).attr('style','padding-top: 0px;padding-bottom: 0px;');
			$('td', row).eq(10).html(aux_text);

        }
    });

});

var eventFired = function ( type ) {
	total = 0;
	$("#tabla-data-factura tr .subtotalkg").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
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
			
			if(funcion=='procesar'){
				if (respuesta.mensaje == "ok") {
                    //genpdfFAC(respuesta.id,"_U");
                    $("#fila"+datatemp.nfila).remove();
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
                    swal({
						//title: 'Error',
						text: respuesta.mensaje,
						icon: 'error',
						buttons: {
							confirm: "Aceptar"
						},
					}).then((value) => {
					});
					//Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
				}
			}
            if(funcion=='consultaranularguiafact'){
				if (respuesta.mensaje == "ok") {
					//alert(respuesta.despachoord.guiadespacho);
					$("#guiadespachoanul").val(respuesta.despachoord.guiadespacho);
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

            if (funcion=='anularfac') {
                if (respuesta.id == "1") {
					$("#fila" + datatemp.dte_id).remove();
                }
                //console.log(respuesta);
                Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
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
                    var ruta = '/guiadespanul/store';
                    ajaxRequest(datatemp,ruta,'guardarguiadespanul');    
                } else {
                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
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
					$("#fila" + respuesta.nfila).remove();
					$("#myModalanularguiafact").modal('hide');
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					Biblioteca.notificaciones('Registro no fue guardado.', 'Plastiservi', 'error');
				}
			}
		},
		error: function () {
		}
	});
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


