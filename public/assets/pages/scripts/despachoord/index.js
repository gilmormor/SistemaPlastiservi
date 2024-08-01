$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    i = 0;
    cadena = "?sucursal_id=" + $("#sucursal_id").val();
    $('#tabla-data-despachoord').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "despachoordpage/" + cadena,
        "order"       : [[ 0, "desc" ]],
        'columns'     : [
            {data: 'id'},
            {data: 'fechahora'},
            {data: 'fechaestdesp'},
            {data: 'razonsocial'},
            {data: 'sucursal_nombre'},
            {data: 'despachosol_id'},
            {data: 'oc_id'},
            {data: 'notaventa_id'},
            {data: 'comuna_nombre'},
            {data: 'aux_totalkg'},
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
            if(data.obsdevolucion !=""){
                aux_text =
                    "<a class='btn-sm tooltipsC' title='" + data.obsdevolucion + "'>" +
                        "<i class='fa fa-fw fa-question-circle text-red'></i>" + 
                    "</a>";
                $('td', row).eq(0).html($('td', row).eq(0).html() + aux_text);
            }

            $('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));

            $('td', row).eq(2).attr('data-order',data.fechaestdesp);
            aux_fecha = new Date(data.fechaestdesp);
            $('td', row).eq(2).html(fechaddmmaaaa(aux_fecha));

            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Solicitud de Despacho' onclick='genpdfSD(" + data.despachosol_id + ",1)'>" + 
                    data.despachosol_id + 
                "</a>";
            $('td', row).eq(5).html(aux_text);
            
            if(data.oc_file != "" && data.oc_file != null){
                aux_text = 
                    "<a class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Compra' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + 
                        data.oc_id + 
                    "</a>";
                $('td', row).eq(6).html(aux_text);
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

            
            aux_text = 
                "<i class='fa fa-fw " + data.icono + " tooltipsC' title='" + data.tipoentrega_nombre + "'></i>";
            $('td', row).eq(10).html(aux_text);

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

            aux_text = 
            `<a ${aux_displaybtnbl} class="btn-accion-tabla btn-sm tooltipsC botonbloq${data.id}" title="Cliente Bloqueado: ${aux_clienteBloqueado}" onclick="llenartablaDataCobranza(${data.id},${data.cliente_id},${data.notaventa_id},0)">
                <span class="fa fa-fw fa-lock text-danger text-danger" style="bottom: 0px;top: 2px;"></span>
            </a>
            <a ${aux_displaybtnac} id="bntaproord${data.id}" name="bntaproord${data.id}" class="btn-accion-tabla btn-sm botonac${data.id}" onclick="aprobarord(${data.id},${data.id})" title="Aprobar Orden Despacho" data-toggle="tooltip">
                <span class="glyphicon glyphicon-floppy-save" style="bottom: 0px;top: 2px;"></span>
            </a>
            <a ${aux_displaybtnac} href="despachoord" class="btn-accion-tabla tooltipsC btnEditar" title="Editar este registro">
                <i class="fa fa-fw fa-pencil"></i>
            </a>`;
            /* if(data.clientebloqueado_descripcion != null){
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
            } */
            $('td', row).eq(14).addClass('updated_at');
            $('td', row).eq(14).attr('id','updated_at' + data.id);
            $('td', row).eq(14).attr('name','updated_at' + data.id);
            aux_text = aux_text +
            "<a href='despachoord' class='btn-accion-tabla btn-sm btnAnular tooltipsC' title='Anular Orden Despacho' data-toggle='tooltip'>"+
                "<span class='glyphicon glyphicon-remove text-danger'></span>"
            "</a>";
            $('td', row).eq(15).html(aux_text);
        }
    });

    let  table = $('#tabla-data-despachoord').DataTable();
    //console.log(table);
    table
        .on('draw', function () {
            eventFired( 'Page' );
        });

    $.ajax({
        url: '/despachoord/totalizarindex',
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
	$("#tabla-data-despachoord tr .subtotalkg").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#subtotalkg").html(MASKLA(total,2))
}


function ajaxRequestOD(data,url,funcion) {
    data1 = data;
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='aproborddesp'){
                if ('error' in respuesta){
                    if (respuesta.error == 0){
                        form.parents('tr').remove();
                    }
                    swal({
                        title: 'Informacion',
                        text: respuesta.mensaje,
                        icon: respuesta.tipo_alert,
                        buttons: {
                            confirm: "Aceptar"
                        },
                    });    
                    //Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                }else{
                    if(respuesta.status == "1"){
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
                                genpdfOD(respuesta.id,1);
                            }
                            //$("#fila"+data['nfila']).remove();                            
                        });
                        $("#fila"+respuesta.nfila).remove();
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
                }

            }
            if(funcion=='buscarTipoBodegaOrdDesp'){
                if(respuesta.datas.length > 0){
                    if(respuesta.datas.length == 1){
                        var data = {
                            id: respuesta.id,
                            nfila : respuesta.nfila,
                            invbodega_id : respuesta.datas[0].id,
                            updated_at   : data1.updated_at,
                            _token: $('input[name=_token]').val()
                        };
                        var ruta = '/despachoord/aproborddesp/'+respuesta.id;
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
                                ajaxRequestOD(data,ruta,'aproborddesp');
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
		},
		error: function () {
		}
	});
}


function aprobarord(i,id){
    var data = {
        id: id,
        nfila : i,
        updated_at   : $("#updated_at" + i).html(),
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
            ajaxRequestOD(data,ruta,'aproborddesp');
        }
    });
    return 0;
    var data = {
		id         : id,
        nfila      : i,
        tipobodega : 3, //Codigo de tipo de bodega = 3 (Bodegas de despacho)
        updated_at : $("#updated_at" + i).html(),
        _token: $('input[name=_token]').val()
	};
	var ruta = '/invbodega/buscarTipoBodegaOrdDesp';
	respuesta = ajaxRequestOD(data,ruta,'buscarTipoBodegaOrdDesp');
    return 0;

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
			ajaxRequestOD(data,ruta,'aproborddesp');
		}
	});
}