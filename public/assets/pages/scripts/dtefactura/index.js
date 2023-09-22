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
        'ajax'        : "dtefacturapage",
        'columns'     : [
            {data: 'id'}, // 0
            {data: 'fechahora'}, // 1
            {data: 'rut'}, // 2
            {data: 'razonsocial'}, // 3
            {data: 'cotizacion_id'}, // 4
            {data: 'oc_id'}, // 5
            {data: 'notaventa_id'}, // 6
            {data: 'despachosol_id'}, // 7
            {data: 'despachoord_id'}, // 8
            {data: 'nrodocto_guiadesp'}, // 9
            {data: 'nrodocto_factura'}, // 11
            {data: 'nombre_comuna'}, // 12
            {data: 'clientebloqueado_descripcion',className:"ocultar"}, //13
            {data: 'oc_file',className:"ocultar"}, //14
            {data: 'nombrepdf',className:"ocultar"}, //15
            {data: 'updated_at',className:"ocultar"}, //16
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

            let id_str = data.nrodocto_factura.toString();
            id_str = data.nombrepdf + id_str.padStart(8, "0");
            aux_text = 
            "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Factura' onclick='genpdfFAC(\"" + id_str + "\",\"\")'>" +
                data.id +
            "</a>";
            $('td', row).eq(0).html(aux_text);

            $('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));

            if(data.cotizacion_id != null){
                let arr_cotizacion_id = data.cotizacion_id.split(','); 
                aux_text = "";
                for (let i = 0; i < arr_cotizacion_id.length; i++) {
                    aux_text += 
                    "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Cotizacion' onclick='genpdfCOT(" + arr_cotizacion_id[i] + ",1)'>" +
                        arr_cotizacion_id[i] +
                    "</a>";
                }    
            }else{
                aux_text = "";
            }
            $('td', row).eq(4).html(aux_text);

            aux_text = "";
            if(data.oc_file != "" && data.oc_file != null){
                let arr_oc_id = data.oc_id.split(','); 
                let arr_oc_file = data.oc_file.split(','); 
                for (let i = 0; i < arr_oc_file.length; i++) {
                    aux_text += 
                    "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Compra' onclick='verpdf2(\"" + arr_oc_file[i] + "\",2)'>" + 
                        arr_oc_id[i] + 
                    "</a>";
                    if((i+1) < arr_oc_file.length){
                        aux_text += ",";
                    }
                }
            }else{
                if(data.dteoc_oc_file != "" && data.dteoc_oc_file != null){
                    aux_text = 
                    "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Compra' onclick='verpdf3(\"" + data.dteoc_oc_file + "\",2,\"" + data.dteoc_oc_folder + "\")'>" + 
                        data.dteoc_oc_id + 
                    "</a>";    
                }
            }
            $('td', row).eq(5).html(aux_text);
            aux_text = "";
            if(!(data.notaventa_id === null)){
                let arr_notaventa_id = data.notaventa_id.split(','); 
                for (let i = 0; i < arr_notaventa_id.length; i++){
                    aux_text += 
                    "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV(" + arr_notaventa_id[i] + ",1)'>" +
                        arr_notaventa_id[i] +
                    "</a>";
                    if((i+1) < arr_notaventa_id.length){
                        aux_text += ",";
                    }
                }    
            }
            $('td', row).eq(6).html(aux_text);

            aux_text = "";
            if(!(data.despachosol_id === null)){
                let arr_despachosol_id = data.despachosol_id.split(','); 
                for (let i = 0; i < arr_despachosol_id.length; i++){
                    aux_text += 
                    "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Solicitud Despacho' onclick='genpdfSD(" + arr_despachosol_id[i] + ",1)'>" +
                        arr_despachosol_id[i] +
                    "</a>";
                    if((i+1) < arr_despachosol_id.length){
                        aux_text += ",";
                    }
                }    
            }
            $('td', row).eq(7).html(aux_text);

            aux_text = "";
            if(!(data.despachoord_id === null)){
                let arr_despachoord_id = data.despachoord_id.split(','); 
                for (let i = 0; i < arr_despachoord_id.length; i++){
                    aux_text += 
                    "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Orden Despacho' onclick='genpdfOD(" + arr_despachoord_id[i] + ",1)'>" +
                        arr_despachoord_id[i] +
                    "</a>";
                    if((i+1) < arr_despachoord_id.length){
                        aux_text += ",";
                    }
                }
            }
            $('td', row).eq(8).html(aux_text);


            aux_text1 = "";
            if(data.nrodocto_guiadesp !== null){
                let arr_nrodocto_guiadesp = data.nrodocto_guiadesp.split(','); 
                for (let i = 0; i < arr_nrodocto_guiadesp.length; i++){
                    aux_text1 += 
                    "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Guia Despacho' onclick='genpdfGD(" + arr_nrodocto_guiadesp[i] + ",\"\")'>" +
                        arr_nrodocto_guiadesp[i] +
                    "</a>:"+
                    "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Cedible' onclick='genpdfGD(" + arr_nrodocto_guiadesp[i] + ",\"_cedible\")'>" +
                        arr_nrodocto_guiadesp[i] +
                    "</a>";
                    if((i+1) < arr_nrodocto_guiadesp.length){
                        aux_text1 += ",";
                    }
                }    
            }
            //$('td', row).eq(9).html(aux_text1);
/*
            aux_text2 = "";
            let arr_nrodocto_guiadespced = data.nrodocto_guiadesp.split(','); 
            for (let i = 0; i < arr_nrodocto_guiadespced.length; i++){
                aux_text2 += 
                "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Guia Despacho cedible' onclick='genpdfGD(" + arr_nrodocto_guiadespced[i] + ",\"_cedible\")'>" +
                    arr_nrodocto_guiadespced[i] +
                "</a>";
                if((i+1) < arr_nrodocto_guiadespced.length){
                    aux_text2 += ",";
                }
            }
*/
/*
            aux_text2 = "";
            if(data.nrodocto_guiadesp !== null){
                let arr_nrodocto_guiadespced = data.nrodocto_guiadesp.split(','); 
                for (let i = 0; i < arr_nrodocto_guiadespced.length; i++){
                    aux_text2 += 
                    "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Guia Despacho' onclick='genpdfGD(" + arr_nrodocto_guiadespced[i] + ",\"\")'>" +
                        arr_nrodocto_guiadespced[i] +
                    "</a>";
                    if((i+1) < arr_nrodocto_guiadespced.length){
                        aux_text2 += ",";
                    }
                }
            }
*/
            $('td', row).eq(9).html(aux_text1);
            //$('td', row).eq(10).html(aux_text2);

            aux_text = 
            "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Factura' onclick='genpdfFAC(\"" + id_str + "\",\"\")'>" +
                data.nrodocto_factura +
            "</a>:" +
            "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Factura Cedible' onclick='genpdfFAC(\"" + id_str + "\",\"_cedible\")'>" +
                data.nrodocto_factura +
            "</a>";
            $('td', row).eq(10).html(aux_text);

            if(data.clientebloqueado_descripcion != null){
                aux_text = 
                    "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Cliente Bloqueado: " + data.clientebloqueado_descripcion + "'>"+
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
                "<a id='bntaproord'" + data.id + " name='bntaproord'" + data.id + " class='btn-accion-tabla btn-sm tooltipsC' onclick='procesarDTE(" + data.id + ")' title='Generar DTE Factura SII'>"+
                    "<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>"+
                "</a>"+
                "<a href='dtefactura' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>"+
                    "<i class='fa fa-fw fa-pencil'></i>"
                "</a>";
            }

            $('td', row).eq(15).addClass('updated_at');
            $('td', row).eq(15).attr('id','updated_at' + data.id);
            $('td', row).eq(15).attr('name','updated_at' + data.id);

            aux_text = 
            `<a id="bntaproord${data.id}" name="bntaproord${data.id}" class="btn-accion-tabla btn-sm tooltipsC" onclick="procesarDTE(${data.id})" title="Enviar a procesados">
                <span class="glyphicon glyphicon-floppy-save" style="bottom: 0px;top: 2px;"></span>
            </a>|
            <a onclick="volverGenDTE(${data.id})" class="btn-accion-tabla btn-sm tooltipsC" title="Volver a Generar DTE" data-toggle="tooltip">
                <span class="fa fa-upload text-danger"></span>
            </a>`;
            /*ELIMINAR BOTON ANULAR FACTURA, LAS FACTURAS DESPUES DE GENERADAS NO SE PUEDEN ANULAR, SOLO SE PUEDEN ANULAR CON NOTA DE CREDITO
            `|<a onclick="anulardte(${data.id})" class="btn-accion-tabla btn-sm tooltipsC" title="Anular registro" data-toggle="tooltip">
                <span class="glyphicon glyphicon-remove text-danger"></span>
            </a>`
            */
            $('td', row).eq(16).html(aux_text);
        }
    });

    /*
    let  table = $('#tabla-data-factura').DataTable();
    //console.log(table);
    table
        .on('draw', function () {
            eventFired( 'Page' );
        });

    $.ajax({
        url: '/dtefactura/totalizarindex',
        type: 'GET',
        success: function (datos) {
            //console.log(datos);
            $("#totalkg").html(MASKLA(datos.kgtotal,2));
            //$("#totaldinero").html(MASKLA(datos.aux_totaldinero,0));
        }
    });
    */
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
                console.log(respuesta);
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
            if(funcion=='estadoDTE'){
                console.log(respuesta);
			}
		},
		error: function () {
		}
	});
}


function estadoDTE(id){
    var data = {
        dte_id : id,
        nfila  : id,
        updated_at : $("#updated_at" + id).html(),
        _token: $('input[name=_token]').val()
    };
    var ruta = '/dtefactura/estadoDTE';
    //var ruta = '/guiadesp/dteguiadesp';
    swal({
        title: '¿ Ver estado DTE?',
        icon: 'warning',
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
        if (value) {
            ajaxRequest(data,ruta,'estadoDTE');
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

function anularfac(id){
    var data = {
        dte_id : id,
        nfila  : id,
        updated_at : $("#updated_at" + id).html(),
        _token: $('input[name=_token]').val()
    };
    var ruta = '/dtefactura/anular';
    //var ruta = '/guiadesp/dteguiadesp';
    swal({
        title: '¿ Anular Factura ?',
        text: "Esta acción no se puede deshacer!",
        icon: 'warning',
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
        if (value) {
            ajaxRequest(data,ruta,'anularfac');
        }
    });
    return 0;

	$("#idanul").val(id);
	$("#guiadespachoanul").val(nfila);
    $("#id2").html("Id Guia Despacho:");
	$("#nfilaanul").val(nfila);
    $("#tituloAGFAC").html("Devolver a Orden de Despacho");
	var data = {
		id    : id,
		nfila : nfila,
		fac_id : nfila,
		updated_at : $("#updated_at" + nfila).html(),
        rutarecarga : "dtefactura",
		_token: $('input[name=_token]').val()
	};
    var ruta = "/dtefactura/validarupdated";
    ajaxRequest(data,ruta,'validarupdated');
    return 0;


	$("#idanul").val(id);
	$("#guiadespachoanul").val('');
	$("#nfilaanul").val(nfila);
    $("#tituloAGFAC").html("Devolver a Orden de Despacho");
	var data = {
		id    : id,
		nfila : nfila,
		guiadesp_id : nfila,
		updated_at : $("#updated_at" + nfila).html(),
		_token: $('input[name=_token]').val()
	};
    var ruta = "/guiadesp/validarupdated";
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
			id         : $("#idanul").val(),
			nfila      : $("#nfilaanul").val(),
			guiadesp_id : $("#guiadesp_id").val(),
			updated_at  : $("#updated_at").val(),
			tipobodega : 3, //Codigo de tipo de bodega = 3 (Bodegas de despacho)
			_token: $('input[name=_token]').val()
		};
        //console.log(data);
		var ruta = '/invbodega/buscarTipoBodegaOrdDesp';
		respuesta = ajaxRequest(data,ruta,'buscarTipoBodegaOrdDesp');
	}else{
		alertify.error("Falta incluir informacion");
	}
});