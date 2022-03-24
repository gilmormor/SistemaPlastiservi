$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    i = 0;
    $('#tabla-data-guiadespacho').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "guiadespachopage",
        'columns'     : [
            {data: 'id'},
            {data: 'fechahora'},
            {data: 'fechaestdesp'},
            {data: 'razonsocial'},
            {data: 'oc_id'},
            {data: 'notaventa_id'},
            {data: 'despachoord_id'},
            {data: 'despachosol_id'},
            {data: 'comuna_nombre'},
            {data: 'aux_totalkg'},
            {data: 'tipoentrega_nombre'},
            {data: 'icono',className:"ocultar"},
            {data: 'clientebloqueado_descripcion',className:"ocultar"},
            {data: 'oc_file',className:"ocultar"},
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
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Guia despacho: " + data.id + "' onclick='genpdfOD(" + data.id + ",1)'>"+
                    + data.id +
                "</a>";
            $('td', row).eq(0).html(aux_text);

            $('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));

            $('td', row).eq(2).attr('data-order',data.fechaestdesp);
            aux_fecha = new Date(data.fechaestdesp);
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
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Orden Despacho' onclick='genpdfSD(" + data.despachosol_id + ",1)'>" + 
                    data.despachosol_id + 
                "</a>";
            $('td', row).eq(6).html(aux_text);

            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Solicitud Despacho' onclick='genpdfSD(" + data.despachosol_id + ",1)'>" + 
                    data.despachosol_id + 
                "</a>";
            $('td', row).eq(7).html(aux_text);
            
            $('td', row).eq(9).attr('data-order',data.aux_totalkg);
            $('td', row).eq(9).attr('style','text-align:right');
            aux_text = MASKLA(data.aux_totalkg,2);
            $('td', row).eq(9).html(aux_text);
            $('td', row).eq(9).addClass('subtotalkg');

            
            aux_text = 
                "<i class='fa fa-fw " + data.icono + " tooltipsC' title='" + data.tipoentrega_nombre + "'></i>";
            $('td', row).eq(9).html(aux_text);

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
                "<a id='bntaproord'" + data.id + " name='bntaproord'" + data.id + " class='btn-accion-tabla btn-sm' onclick='aprobarord(" + data.id + "," + data.id + ")' title='Aprobar Orden Despacho' data-toggle='tooltip'>"+
                    "<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>"+
                "</a>"+
                "<a href='guiadespacho' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>"+
                    "<i class='fa fa-fw fa-pencil'></i>"
                "</a>";
            }
            aux_text = aux_text +
            "<a href='guiadespacho' class='btn-accion-tabla btn-sm btnAnular tooltipsC' title='Anular Guia Despacho' data-toggle='tooltip'>"+
                "<span class='glyphicon glyphicon-remove text-danger'></span>"
            "</a>";
            $('td', row).eq(14).html(aux_text);
        }
    });

    let  table = $('#tabla-data-guiadespacho').DataTable();
    //console.log(table);
    table
        .on('draw', function () {
            eventFired( 'Page' );
        });

    $.ajax({
        url: '/guiadespacho/totalizarindex',
        type: 'GET',
        success: function (datos) {
            //console.log(datos);
            $("#totalkg").html(MASKLA(datos.aux_totalkg,2));
            //$("#totaldinero").html(MASKLA(datos.aux_totaldinero,0));
        }
    });
});

var eventFired = function ( type ) {
	total = 0;
	$("#tabla-data-guiadespacho tr .subtotalkg").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#subtotalkg").html(MASKLA(total,2))
}


function ajaxRequest(data,url,funcion) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			
			if(funcion=='aproborddesp'){
				if (respuesta.mensaje == "ok") {
					swal({
						title: '¿ Desea ver PDF Orden Despacho ?',
						text: "",
						icon: 'success',
						buttons: {
							cancel: "Cancelar",
							confirm: "Aceptar"
						},
					}).then((value) => {
						if (value) {
							genpdfOD(data.id,1);
						}
						$("#fila"+data['nfila']).remove();
					});
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
			}
			
		},
		error: function () {
		}
	});
}


function aprobarord(i,id){
	var data = {
		id: id,
        nfila : i,
        _token: $('input[name=_token]').val()
	};
	var ruta = '/despachoord/aproborddesp/'+id;
	swal({
		title: '¿ Aprobar Orden de Despacho ?',
		text: "Esta acción no se puede deshacer!",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			ajaxRequest(data,ruta,'aproborddesp');
		}
	});
}