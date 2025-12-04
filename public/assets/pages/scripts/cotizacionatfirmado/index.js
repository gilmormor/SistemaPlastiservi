$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    $('#tabla-data-cotizacion').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "cotizacionatfirmadopage",
        "order": [[ 0, "desc" ]],
        'columns'     : [
            {data: 'id'},
            {data: 'fechahora'},
            {data: 'razonsocial'},
            {data: 'vendedor_nombre'},
            {data: 'pdfcot'},
            {data: 'contconfirm'},
            {data: 'contsinfirm'},
            {data: 'aprobstatus',className:"ocultar"},
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : 
                "<a href='cotizacionatfirmado' class='btn-accion-tabla btnEditar' title='Editar este registro'>"+
                    "<i class='fa fa-fw fa-pencil'></i>"+
                "</a>"}
        ],
        "language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            $('td', row).eq(0).attr("id","id" + data.id);
            $('td', row).eq(0).attr("id","id" + data.id);
            $('td', row).eq(0).attr("updated_at",data.updated_at);
            aux_text = "<a class='btn-accion-tabla btn-sm  title='Cotizacion: " + data.id + "' onclick='genpdfCOT(" + data.id + ",1)'>"+
                            "<i class='fa fa-fw fa-file-pdf-o'></i>"+
                        "</a>"
            $('td', row).eq(4).html(aux_text);

            $('td', row).eq(5).attr('style','text-align:center');
            $('td', row).eq(6).attr('style','text-align:center');

            aux_text = 
            `<div class="tools11">
                <a class='btn-accion-tabla bntdevolver' title='Devolver: ${data.id}' >
                    <i class='fa fa-fw fa-reply'></i>
                </a>
                <a class="btn-accion-tabla btnEnviarRev botonac${data.id}" title="Enviar revision Acuerdo tecnico" style="padding-left: 0px;">
                    <i class="fa fa-fw fa-save"></i>
                </a>
                <a href="cotizacionatfirmado" class='btn-accion-tabla btnEditar' title='Editar este registro'>
                    <i class='fa fa-fw fa-pencil'></i>
                </a>
            </div>`;

            //$('td', row).eq(9).attr('style','padding-top: 0px;padding-bottom: 0px;');
            $('td', row).eq(8).html(aux_text);

        }
    });
});

$(document).on("click", ".btnEnviarRev", function(event){
    event.preventDefault();
    fila = $(this).closest("tr");
    form = $(this);
    id = fila.find('td:eq(0)').text();
    contador = fila.find('td:eq(6)').text();
    var data = {
		id: id,
        cotizacion_id: id,
        updated_at  : $("#id" + id).attr("updated_at"),
        _token: $('input[name=_token]').val()
	};
	var ruta = '/cotizacionatfirmado/enviarrev/'+id;
	swal({
		title: '¿ Enviar a Revisión ?',
		text: "Esta acción no se puede deshacer!",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			ajaxRequest(data,ruta,'enviarrev',form);
		}
	});
    
});

function ajaxRequest(data,url,funcion,form = false) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='enviarrev'){
                //console.log(Array.isArray(respuesta));
                if ('error' in respuesta){
                    if (respuesta.error == 0){
                        form.parents('tr').remove();
                    }
                    swal({
                        title: 'Informacion',
                        text: respuesta.mensaje,
                        icon: 'warning',
                        buttons: {
                            confirm: "Aceptar"
                        },
                    });
                    //Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                }else{
                    if (respuesta.mensaje == "ok") {
                        form.parents('tr').remove();
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
			}
		},
		error: function () {
		}
	});
}

$(document).on("click", ".bntdevolver", function(event){
    event.preventDefault();
    fila = $(this).closest("tr");
    form = $(this);
    id = fila.find('td:eq(0)').text();
    contador = fila.find('td:eq(6)').text();
    contacutec = fila.find('td:eq(8)').text();
    aprobstatus = 1;
    if(contacutec>0){
        aprobstatus = 5;
    }else{
        if(contador>0){
            aprobstatus = 2;
        }    
    }
    var data = {
		id: id,
        cotizacion_id: id,
        aprobstatus : aprobstatus,
        updated_at  : $("#id" + id).attr("updated_at"),
        _token: $('input[name=_token]').val()
	};
	var ruta = '/cotizacionatfirmado/devolver/'+id;
	swal({
		title: '¿ Devolver a proceso anterior ?',
		text: "Esta acción no se puede deshacer!",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			ajaxRequest(data,ruta,'enviarrev',form);
		}
	});
    
});