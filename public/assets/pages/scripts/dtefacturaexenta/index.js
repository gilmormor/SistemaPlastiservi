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
        'ajax'        : "dtefacturaexentapage",
        'columns'     : [
            {data: 'id'}, // 0
            {data: 'fechahora'}, // 1
            {data: 'rut'}, // 2
            {data: 'razonsocial'}, // 3
            {data: 'oc_id'}, // 4
            {data: 'nrodocto'}, // 5
            {data: 'nombre_comuna'}, // 6
            {data: 'clientebloqueado_descripcion',className:"ocultar"}, //7
            {data: 'oc_file',className:"ocultar"}, //8
            {data: 'oc_file',className:"ocultar"}, //9
            {data: 'nombrepdf',className:"ocultar"}, //10           
            {data: 'updated_at',className:"ocultar"}, //11
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : ""}
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

            let id_str = data.nrodocto.toString();
            id_str = data.nombrepdf + id_str.padStart(8, "0");
            aux_text = 
            "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Factura' onclick='genpdfFAC(\"" + id_str + "\",\"\")'>" +
                data.id +
            "</a>";
            $('td', row).eq(0).html(aux_text);

            $('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));


            aux_text = "";
            if(data.oc_file != "" && data.oc_file != null){
                aux_text = 
                "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Compra' onclick='verpdf3(\"" + data.oc_file + "\",2,\"" + data.oc_folder + "\")'>" + 
                    data.oc_id + 
                "</a>";
                $('td', row).eq(4).html(aux_text);
            }



            aux_text = 
            "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Factura' onclick='genpdfFAC(\"" + id_str + "\",\"\")'>" +
                data.nrodocto +
            "</a>," +
            "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Cedible: " + data.nrodocto + "' onclick='genpdfFAC(\"" + id_str + "\",\"_cedible\")'>" +
                "<i class='fa fa-fw fa-file-pdf-o'></i>" +
            "</a>";
            $('td', row).eq(5).html(aux_text);

            $('td', row).eq(11).addClass('updated_at');
            $('td', row).eq(11).attr('id','updated_at' + data.id);
            $('td', row).eq(11).attr('name','updated_at' + data.id);

            /* aux_clienteBloqueado = validarClienteBloqueadoxModulo(data); 
            aux_displaybtnac = ``;
            aux_displaybtnbl = ``;
            if(aux_clienteBloqueado == ""){
                aux_displaybtnac = ``;
                aux_displaybtnbl = `style="display:none;"`;
            }else{
                aux_displaybtnac = `style="display:none;"`;
                aux_displaybtnbl = ``;
            } */
            aux_clienteBloqueado = "";
            aux_displaybtnac = ``;
            aux_displaybtnbl = `style="display:none;"`;
    
            stasubsii_text = 
            `<a ${aux_displaybtnac} id="stasubsii${data.id}" name="stasubsii${data.id}" onclick="volverGenDTE(${data.id})" class="btn-accion-tabla btn-sm tooltipsC botonac${data.id}" title="Generar DTE SII" data-toggle="tooltip">
                <span class="fa fa-upload text-danger"></span>
            </a>`;    
            $('td',row).eq(12).attr('stasubsii',stasubsii_text);

            stasubcob_text = 
            `<a ${aux_displaybtnac} id="stasubcob${data.id}" name="stasubcob${data.id}" onclick="volverSubirDteSisCob(${data.id})" class="btn-accion-tabla btn-sm tooltipsC botonac${data.id}" title="Subir DTE a Sistema Cobranza" data-toggle="tooltip"">
                <span class="fa fa-upload text-yellow"></span>
            </a>`;    
            $('td',row).eq(12).attr('stasubcob',stasubcob_text);

            bntaproord_text = 
            `<a ${aux_displaybtnac} id="bntaproord${data.id}" name="bntaproord${data.id}" class="btn-accion-tabla btn-sm tooltipsC botonac${data.id}" onclick="procesarDTE(${data.id},18)" title="Enviar a procesados">
                <span class="glyphicon glyphicon-floppy-save" style="bottom: 0px;top: 2px;"></span>
            </a>`;
            $('td',row).eq(12).attr('bntaproord',bntaproord_text);

            aux_text = '';
            if(data.stasubsii == 0 || data.stasubcob == 0){
                if(data.stasubsii == 0){
                    aux_text = stasubsii_text;
                }
                if(data.stasubcob == 0){
                    aux_text += stasubcob_text;
                }
            }else{
                aux_text = bntaproord_text;
            }
            aux_text += 
            `<a ${aux_displaybtnbl} class="btn-accion-tabla btn-sm tooltipsC botonbloq${data.id}" title="Condición financiera en revisión: ${aux_clienteBloqueado}" onclick="llenartablaDataCobranza(${data.id},${data.cliente_id},0,0)">
                <span class="fa fa-fw fa-lock text-danger text-danger" style="bottom: 0px;top: 2px;"></span>
            </a>`;

            $('td',row).eq(12).attr('id','accion' + data.id);
            $('td',row).eq(12).attr('name','accion' + data.id);

            $('td', row).eq(12).html(aux_text);        }
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
                    //genpdfFAC(respuesta.nrodocto,"_U");
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
