$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    configTablaDteGuiaDesp();
});

function configTablaDteGuiaDesp(){
    aux_nfila = 0;
    data = datosdteguiadesp(1);
    $("#tabla-data-dteguiadesp").attr('style','');
    //$("#tabla-data-dteguiadesp").DataTable().fnDestroy();
    $('#tabla-data-dteguiadesp').DataTable({
        'paging'      : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "/dtefactura/listarguiadesppage/" + data.data2,
        'columns'     : [
            {data: 'id'},
            {data: 'fchemis'},
            {data: 'cotizacion_id'},
            {data: 'oc_id'},
            {data: 'notaventa_id'},
            {data: 'despachosol_id'},
            {data: 'despachoord_id'},
            {data: 'nrodocto'},
            {data: 'comuna_nombre'},
            {data: 'te'},
            {data: 'te'},
            {data: 'tipoentrega_nombre',className:"ocultar"},
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
            aux_nfila++;
            $(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);
            $(row).attr('nrodocto','fila' + data.id);
/*
            if($("#selecmultprod").val()){
                aux_onclick = "llenarlistaprod(" + aux_nfila + "," + data.id + ")";
            }else{
                aux_onclick = "copiar_codprod(" + data.id + ",'')";
                //aux_onclick = "insertarTabla(" + data.id + ",'" + data.nombre + "'," + data.acuerdotecnico_id + ")";
            }
*/
            aux_onclick = "llenarselectGD(" + aux_nfila + "," + data.id + "," + data.nrodocto + ")";

            $(row).attr('name', 'fila' + aux_nfila);
            $(row).attr('id', 'fila' + aux_nfila);
            $(row).attr('prodid', 'tooltip');
            $(row).attr('class', "btn-accion-tabla copiar_id");
            $(row).attr('data-toggle', data.id);
            //$(row).attr('title', "Click para seleccionar Guia");
            //$(row).attr('onClick', aux_onclick + ';');


            //"<a href='#' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + "</a>";

            $('td', row).eq(1).attr('data-order',data.fchemis);
            aux_fecha = data.fchemis.substring(8, 10) + "/" + data.fchemis.substring(5, 7) + "/" + data.fchemis.substring(0, 4);
            $('td', row).eq(1).html(aux_fecha);
            aux_venmodant = "\"myModalBuscardteguiadesp\"";
			codigo = data.cotizacion_id;
			if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)){
				aux_text = "";
			}else{
				aux_text = 
				"<a href='#'  class='tooltipsC' title='Cotizacion' " +
				"onclick='genpdfCOT(\"" + data.cotizacion_id + "\",1," + aux_venmodant + ")'>" + data.cotizacion_id + 
				"</a>";
			}
			$('td', row).eq(2).html(aux_text);


            if(data.oc_file != "" && data.oc_file != null){
                aux_text = 
                    "<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Orden de Compra' onclick='verpdf2(\"" + data.oc_file + "\",2," + aux_venmodant + ")'>" + 
                        data.oc_id + 
                    "</a>";
                $('td', row).eq(3).html(aux_text);
            }
            if(data.notaventa_id === null){
                aux_text = "";
            }else{
                aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV(" + data.notaventa_id + ",1," + aux_venmodant + ")'>" +
                    data.notaventa_id +
                "</a>";
            }
            $('td', row).eq(4).html(aux_text);
            if(data.despachosol_id === null){
                aux_text = "";
            }else{
                aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Solicitud de Despacho' onclick='genpdfSD(" + data.despachosol_id + ",1," + aux_venmodant + ")'>" + 
                    data.despachosol_id + 
                "</a>";
            }
            $('td', row).eq(5).html(aux_text);
            if(data.despachoord_id === null){
                aux_text = "";
            }else{
                aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Orden despacho: " + data.despachoord_id + "' onclick='genpdfOD(" + data.despachoord_id + ",1," + aux_venmodant + ")'>"+
                    + data.despachoord_id +
                "</a>";
            }
            $('td', row).eq(6).html(aux_text);

            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Guia Despacho: " + data.nrodocto + "' onclick='genpdfGD(" + data.nrodocto + ",\"\"," + aux_venmodant + ")'>"+
                    + data.nrodocto +
                "</a>";
            $('td', row).eq(7).html(aux_text);
           
            aux_text = 
                "<i class='fa fa-fw " + data.icono + " tooltipsC' title='" + data.tipoentrega_nombre + "'></i>";

            aux_text1 = "";
            if(data.clientebloqueado_descripcion != null){
                /*
                aux_text = 
                    "<a class='btn-accion-tabla btn-sm tooltipsC' title='Cliente Bloqueado: " + data.clientebloqueado_descripcion + "'>"+
                        "<span class='fa fa-fw fa-lock text-danger text-danger' style='bottom: 0px;top: 2px;'></span>"+
                    "</a>";
                */
                aux_text1 = 
                    " | <i class='fa fa-fw fa-lock text-danger text-danger tooltipsC' title='" + data.clientebloqueado_descripcion + "'></i>";
    
            }
            $('td', row).eq(9).html(aux_text + aux_text1);
            $('td', row).eq(9).attr('style','text-align:center');

            if(data.clientebloqueado_descripcion != null){
                aux_text = 
                    "<a class='btn-accion-tabla btn-sm tooltipsC' title='Cliente Bloqueado: " + data.clientebloqueado_descripcion + "'>"+
                        "<span class='fa fa-fw fa-lock text-danger text-danger' style='bottom: 0px;top: 2px;'></span>"+
                    "</a>";
            }else{
                aux_text = 
                "<a id='bntaproord'" + data.id + " name='bntaproord'" + data.id + " class='btn-accion-tabla btn-sm' onclick='aprobarord(" + data.id + "," + data.id + ")' title='Aprobar Orden Despacho' data-toggle='tooltip'>"+
                    "<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>"+
                "</a>"+
                "<a href='despachoord' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>"+
                    "<i class='fa fa-fw fa-pencil'></i>"
                "</a>";
            }

            let strdte_id = $("#selectguiadesp").val();
            strdte_id = strdte_id.trim();
            let arrdte_id = strdte_id.split(',');

            let str = data.id.toString();
            indice = arrdte_id.indexOf(str);
            let aux_checked = "";
            if(indice != -1){
                aux_checked = "checked";
                //$("#llenarselGD" + data.id).prop("checked", true);
            }

            aux_text = 
                "<div class='checkbox' style='padding-top: 0px;'>" +
                    "<label style='font-size: 1.0em'>" +
                        "<input type='checkbox' class='checkllenarCantSol' id='llenarselGD" + data.id + "' name='llenarselGD[]' onclick='" + aux_onclick + "' " + aux_checked+ ">" +
                        "<span class='cr'><i class='cr-icon fa fa-check'></i></span>" +
                    "</label>" +
                    "<input type='text' name='dte_idGD[]' id='dte_idGD" + (i+1) + "' class='form-control' value='" + data.id + "' style='display:none;'/>" +
                    "<input type='text' name='updated_atGD[]' id='updated_atGD" + (i+1) + "' class='form-control' value='" + data.updated_at + "' style='display:none;'/>" +
                "</div>";

            $('td', row).eq(10).html(aux_text);
            
            $('td', row).eq(15).addClass('updated_at');
            $('td', row).eq(15).attr('id','updated_at' + data.id);
            $('td', row).eq(15).attr('name','updated_at' + data.id);
        }
    });
}

function datosdteguiadesp(dteguiausada = ""){
    var data1 = {
        rut         : eliminarFormatoRutret($("#rut").val()),
        cliente_id  : $("#cliente_id").val(),
        sucursal_id : $("#sucursal_id").val(),
        dtenotnull  : 1, //Estatus que se envia a la consulta para mostrar o no los dte anulados (1=no se trae los anulados ""=empty se trae todo sin importar que esta anulado)
        dteguiausada : dteguiausada,
        indtraslado  : 1,
        _token      : $('input[name=_token]').val()
    };

    var data2 = "?rut="+data1.rut + 
    "&cliente_id="+data1.cliente_id +
    "&sucursal_id="+data1.sucursal_id +
    "&dtenotnull="+data1.dtenotnull +
    "&dteguiausada="+data1.dteguiausada +
    "&indtraslado="+data1.indtraslado

    var data = {
        data1 : data1,
        data2 : data2
    };
    return data;
}
