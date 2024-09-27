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
    'ajax'        : "cotizacionpage",
    "order": [[ 0, "desc" ]],
    'columns'     : [
        {data: 'id'},
        {data: 'fechahora'},
        {data: 'razonsocial'},
        {data: 'pdfcot'},
        {data: 'aprobstatus',className:"ocultar"},
        {data: 'aprobobs',className:"ocultar"},
        {data: 'contador',className:"ocultar"},
        {data: 'updated_at',className:"ocultar"},
        {data: 'contacutec',className:"ocultar"},
        //El boton eliminar esta en comentario Gilmer 23/02/2021
        {defaultContent : 
            ""}
    ],
    "language": {
        //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
    },
    "createdRow": function ( row, data, index ) {

        //"<a href='#' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + "</a>";
        aux_text = 
            "<a class='btn-accion-tabla btn-sm tooltipsC action-buttons' title='Cotizacion: " + data.id + "' onclick='genpdfCOT(" + data.id + ",1)'>"+
                "<i class='fa fa-fw fa-file-pdf-o'></i>"+
            "</a>";
        $('td', row).eq(3).html(aux_text);

        $('td', row).eq(1).attr('data-order',data.fechahora);
        aux_fecha = new Date(data.fechahora);
        $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));

        if ( (data.contador * 1 > 0) || (data.aprobstatus == "7" )) {
            //console.log(row);
            ///$('tr').addClass('preciomenor');
            //$('td', row).parent().addClass('preciomenor tooltipsC');
            /*
            $('td', row).eq(0).html(
                "<a href='#' class='dropdown-toggle tooltipsC' data-toggle='dropdown' title='Precio menor al valor en tabla'>"+
                $('td', row).eq(0).html()+
                "</a>"
            );
            $('td', row).eq(1).html(
                "<a href='#' class='dropdown-toggle tooltipsC' data-toggle='dropdown' title='Precio menor al valor en tabla'>"+
                $('td', row).eq(1).html()+
                "</a>"
            );
            $('td', row).eq(2).html(
                "<a href='#' class='dropdown-toggle tooltipsC' data-toggle='dropdown' title='Precio menor al valor en tabla'>"+
                $('td', row).eq(2).html()+
                "</a>"
            );
            */
            aux_title = 'Precio menor al valor en tabla';
            colorinfo = 'text-aqua';
            aux_text =
				"<a class='btn-sm tooltipsC action-buttons' title='" + aux_title + "'>" +
					"<i class='fa fa-fw fa-question-circle " + colorinfo + "'></i>" + 
				"</a>";
            $('td', row).eq(3).html($('td', row).eq(3).html() + aux_text);
            //$('td', row).parent().prop("title","Precio menor al valor en tabla")
        }
        //console.log(data.aprobstatus);
        if(data.aprobstatus != null){

            aux_title = data.aprobobs;
            colorinfo = 'text-red';
            aux_text =
				"<a class='btn-sm tooltipsC' title='" + aux_title + "'>" +
					"<i class='fa fa-fw fa-question-circle " + colorinfo + "'></i>" + 
				"</a>";
            $('td', row).eq(3).html($('td', row).eq(3).html() + aux_text);

        }
        $('td', row).eq(7).html(data.updated_at);
        $('td', row).eq(7).attr("id","updated_at"+data.id);
        $('td', row).eq(7).attr("name","updated_at"+data.id);

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

        aux_text = 
        `<div class="tools11">
                <a ${aux_displaybtnbl} class="btn-accion-tabla tooltipsC botonbloq${data.id}" title="Condición financiera en revisión: ${aux_clienteBloqueado}" onclick="llenartablaDataCobranza(${data.id},${data.cliente_id},0,0)" style="padding-left: 0px;">
                    <i class="fa fa-fw fa-lock text-danger accioness fa-lg"></i>
                </a>
                <a ${aux_displaybtnac} href="cotizacion" class="btn-accion-tabla btn-sm tooltipsC btnEnviarNV action-buttons botonac${data.id}" title="Enviar a Nota de venta" style="padding-left: 0px;">
                    <i class="fa fa-fw fa-save accioness fa-lg"></i>
                </a>
                <a href="cotizacion" class="btn-accion-tabla tooltipsC btnEditar action-buttons" title="Editar este registro">
                    <i class="fa fa-fw fa-pencil accioness fa-lg"></i>
                </a>
                <a href="cotizacion" class="btn-accion-tabla btnEliminar tooltipsC action-buttons" title="Eliminar este registro">
                    <i class="fa fa-fw fa-trash text-danger accioness fa-lg"></i>
                </a>
        </div>`;

        //$('td', row).eq(9).attr('style','padding-top: 0px;padding-bottom: 0px;');
        $('td', row).eq(9).html(aux_text);

    }
    });
});

$(document).on("click", ".btnEnviarNV", function(event){
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
        updated_at  : $("#updated_at" + id).html(),
        _token: $('input[name=_token]').val()
	};
	var ruta = '/cotizacion/aprobarcotvend/'+id;
	swal({
		title: '¿ Enviar a nota de venta ?',
		text: "Esta acción no se puede deshacer!",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			ajaxRequest(data,ruta,'aprobarcotvend',form);
		}
	});
    
});

function aprobarcotvend(i,id,aprobstatus){
	event.preventDefault();
	//alert($('input[name=_token]').val());
	var data = {
		id: id,
        nfila : i,
        aprobstatus : aprobstatus,
        _token: $('input[name=_token]').val()
	};
	var ruta = '/cotizacion/aprobarcotvend/'+i;
	swal({
		title: '¿ Enviar a nota de venta ?',
		text: "Esta acción no se puede deshacer!",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			ajaxRequest(data,ruta,'aprobarcotvend');
		}
	});
}
function ajaxRequest(data,url,funcion,form = false) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='aprobarcotvend'){
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
            if(funcion=='eliminar'){
                if (respuesta.mensaje == "ok") {
                    form.parents('tr').remove();
                    Biblioteca.notificaciones('El registro fue eliminado correctamente', 'Plastiservi', 'success');
                } else {
                    if (respuesta.mensaje == "sp"){
                        Biblioteca.notificaciones('Usuario no tiene permiso para eliminar.', 'Plastiservi', 'error');
                    }else{
                        Biblioteca.notificaciones('El registro no pudo ser eliminado, hay recursos usandolo', 'Plastiservi', 'error');
                    }
                }
            }
		},
		error: function () {
		}
	});
}
