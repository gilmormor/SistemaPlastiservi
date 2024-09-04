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
        'ajax'        : "reportverfacdesppage",
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
            {data: 'nrodocto_guiadesp'}, // 10
            {data: 'nrodocto_factura'}, // 11
            {data: 'nombre_comuna'}, // 12
            {data: 'clientebloqueado_descripcion',className:"ocultar"}, //13
            {data: 'oc_file',className:"ocultar"}, //14
            {data: 'nombrepdf',className:"ocultar"}, //15
            {data: 'updated_at',className:"ocultar"}, //16
            {data: 'dtefac_updated_at',className:"ocultar"}, //21
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
            //"<a href='#' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + "</a>";

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
                $('td', row).eq(5).html(aux_text);
            }
            aux_text = "";
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
            $('td', row).eq(6).html(aux_text);

            aux_text = "";
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
            $('td', row).eq(7).html(aux_text);

            aux_text = "";
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
            $('td', row).eq(8).html(aux_text);


            aux_text = "";
            let arr_nrodocto_guiadesp = data.nrodocto_guiadesp.split(','); 
            for (let i = 0; i < arr_nrodocto_guiadesp.length; i++){
                aux_text += 
                "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Guia Despacho' onclick='genpdfGD(" + arr_nrodocto_guiadesp[i] + ",\"\")'>" +
                    arr_nrodocto_guiadesp[i] +
                "</a>";
                if((i+1) < arr_nrodocto_guiadesp.length){
                    aux_text += ",";
                }
            }
            $('td', row).eq(9).html(aux_text);

            aux_text = "";
            let arr_nrodocto_guiadespced = data.nrodocto_guiadesp.split(','); 
            for (let i = 0; i < arr_nrodocto_guiadespced.length; i++){
                aux_text += 
                "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Guia Despacho cedible' onclick='genpdfGD(" + arr_nrodocto_guiadespced[i] + ",\"_cedible\")'>" +
                    arr_nrodocto_guiadespced[i] +
                "</a>";
                if((i+1) < arr_nrodocto_guiadespced.length){
                    aux_text += ",";
                }
            }
            $('td', row).eq(10).html(aux_text);

            let id_str = data.nrodocto_factura.toString();
            id_str = data.nombrepdf + id_str.padStart(8, "0");
            aux_text = 
                `<a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="Factura" onclick="cargarvistoFac(this,'${id_str}','')" item=${data.id}>
                    ${data.nrodocto_factura}
                </a>,
                <a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="Factura Cedible" onclick="cargarvistoFac(this,'${id_str}','_cedible')" item=${data.id}>
                    ${data.nrodocto_factura}
                </a>`;
            $('td', row).eq(11).html(aux_text);

            $('td', row).eq(16).addClass('updated_at');
            $('td', row).eq(16).attr('id','updated_at' + data.id);
            $('td', row).eq(16).attr('name','updated_at' + data.id);
            $('td', row).eq(17).addClass('dtefac_updated_at');
            $('td', row).eq(17).attr('id','dtefac_updated_at' + data.id);
            $('td', row).eq(17).attr('name','dtefac_updated_at' + data.id);


        }
    });

});

function cargarvistoFac(obj,id_str,cedible){
    let item = $(obj).attr("item");
    var data = {
        dte_id : item,
        updated_at : $("#updated_at" + item).html(),
        dtefac_updated_at : $("#dtefac_updated_at" + item).html(),
        staverfacdesp : false,
        id_str : id_str,
        cedible : cedible,
        _token : $('input[name=_token]').val()
    };
    //console.log(data);
    //return 0;
    var ruta = '/dtefactura/staverfacdesp'; //Guardar Fecha estimada de despacho
    ajaxRequest(data,ruta,'staverfacdesp');
}


function ajaxRequest(data,url,funcion) {
    aux_data = data;
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
            if(funcion=='staverfacdesp'){
				if (respuesta.error == 0) {
                    $("#fila" + aux_data.dte_id).remove();
                    genpdfFAC(aux_data.id_str,aux_data.cedible);
                    //$("#dtefac_updated_at" + aux_data.dte_id).html(respuesta.dtefac_updated_at);
				}
            }
		},
		error: function () {
		}
	});
}
