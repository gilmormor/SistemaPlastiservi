$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    i = 0;
    let name_table = '#tabla-data-factura';
    let table =$(name_table).DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        "order"       : [[ 0, "desc" ]],
        'ajax'        : "otaprobarpage",
        'columns'     : [
            {
                className: 'dt-control',
                orderable: false,
                data: null,
                defaultContent: '<i class="btn-accion-tabla btn-sm glyphicon glyphicon-triangle-right text-aqua" title="Mostrar Detalle"></i>'
            },
            {data: 'id'}, // 1
            {data: 'fechahora'}, // 2
            {data: 'rut'}, // 3
            {data: 'razonsocial'}, // 4
            {data: 'oc_id'}, // 5
            {data: 'notaventa_id'}, // 6
            {data: 'comuna_nombre'}, // 7
            {data: 'clientebloqueado_descripcion',className:"ocultar"}, //8
            {data: 'oc_file',className:"ocultar"}, //9
            {data: 'oc_file',className:"ocultar"}, //10
            {data: 'updated_at',className:"ocultar"}, //11
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
            $('td', row).eq(1).html(aux_text);



            $('td', row).eq(2).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(2).html(fechaddmmaaaa(aux_fecha));


            aux_text = "";
            if(data.oc_file != "" && data.oc_file != null){
                if(data.notaventa_id != "" && data.notaventa_id != null){
                    aux_text = 
                    `<a class="btn-accion-tabla btn-sm" title="Ver NV: ${data.notaventa_id}" onclick='genpdfNV(${data.notaventa_id},1)'>
                        ${data.notaventa_id}
                    </a>`;
                    $('td', row).eq(6).html(aux_text);
                    aux_text = 
                    `<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm' title='Orden de Compra' onclick='verpdf2("${data.notaventa_id}.pdf",2)'>
                        ${data.oc_id} 
                    </a>`;    
                }else{
                    aux_text = 
                    `<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm' title='Orden de Compra' onclick='verpdf3("${data.oc_file}",2,"oc")'>
                        ${data.oc_id} 
                    </a>`;
                }
                $('td', row).eq(5).html(aux_text);
            }

            $('td', row).eq(11).addClass('updated_at');
            $('td', row).eq(11).attr('id','updated_at' + data.id);
            $('td', row).eq(11).attr('name','updated_at' + data.id);

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
					<a ${aux_displaybtnbl} class="btn-accion-tabla botonbloq${data.id}" title="Condición financiera en revisión: ${aux_clienteBloqueado}" onclick="llenartablaDataCobranza(${data.id},${data.cliente_id},0,0)">
						<i class="fa fa-fw fa-lock text-danger fa-lg"></i>
					</a>
					<a ${aux_displaybtnac} id="bntaprobnv${data.id}" name="bntaprobnv${data.id}" class="btn-accion-tabla btn-sm action-buttons botonac${data.id}" onclick="procesarReg(${data.id},31,'/otaprobar/aprobar',${data.updatednum_at},'Aprobar',0,'','bntaprobnv${data.id}')" title="Aprobar">
                        <button type="button" class="btn btn-default btn-xs">
						    <i class="fa fa-thumbs-o-up fa-lg text-aqua"></i>
                        </button>
					</a>
                    <a ${aux_displaybtnac} id="bntrechazarbnv${data.id}" name="bntrechazarbnv${data.id}" class="btn-accion-tabla btn-sm action-buttons botonac${data.id}" onclick="procesarReg(${data.id},31,'/otaprobar/rechazar',${data.updatednum_at},'Rechazar',1,'','bntrechazarbnv${data.id}')" title="Rechazar">
                        <button type="button" class="btn btn-default btn-xs">
						    <i class="fa fa-thumbs-o-down fa-lg text-red"></i>
                        </button>
					</a>

				</div>`;
			//$('td', row).eq(12).attr('style','padding-top: 0px;padding-bottom: 0px;');
			$('td', row).eq(12).html(aux_text);

        }
    });

    // Add event listener for opening and closing details
    table.on('click', 'td.dt-control', function (e) {
        let tr = e.target.closest('tr');
        let row = table.row(tr);
    
        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            $(this).html('<i class="btn-accion-tabla btn-sm glyphicon glyphicon-triangle-right text-aqua" title="Mostrar Detalle"></i>');
        }
        else {
            // Open this row
            row.child(format(row.data())).show();
            $(this).html('<i class="btn-accion-tabla btn-sm glyphicon glyphicon-triangle-bottom text-aqua" title="Mostrar Detalle"></i>');
        }
    });
    

});

// Función para decodificar entidades HTML con jQuery
function decodeHtml(html) {
    return $('<textarea/>').html(html).text();
}

// Formatting function for row details - modify as you need
function format(d) {
    // Descomponer el campo nvdetalle
    //console.log(d);
    //console.log(d.nvdetalle);
    const detalleArray = decodeHtml(d.nvdetalle).split(';').map(detalle => {
        const [producto_id, cant, precio,subtotal,kg,producto_nombre,kgprod,id,unidadmedida_nombre] = detalle.split('|');
        return {
            producto_id: parseInt(producto_id),
            cant: parseInt(cant),
            producto_nombre: producto_nombre,
            kg: parseFloat(kg),
            kgprod: parseFloat(kgprod),
            precio: parseFloat(precio),
            subtotal: parseInt(subtotal),
            acuerdotecnico_id: id,
            unidadmedida_nombre : unidadmedida_nombre
        };
    });
    // Generar tabla HTML
    let tableHtml = `<div style="display: flex; align-items: flex-start;"> <!-- Contenedor Flex (flecha al principio) -->
            <div style="margin-left: 20px;">&#8627;</div> <!-- Flecha desplazada un poco a la derecha -->
            <div class="table-responsive">
            <table class="table table-bordered table-striped AllDataTables table-hover table-condensed" style="width: auto; margin-left: 5px;">
                <thead>
                    <tr>
                        <th style="text-align: center;" title="Id Producto">ID Prod</th>
                        <th>Nombre</th>
                        <th style="text-align: center;" title="Cantidad Unidades">Cant</th>
                        <th style="text-align: center;" title="Unidad Medida">UM</th>
                        <th style="text-align: center;" title="Kg segun formula">Kg</th>
                    </tr>
                </thead>
                <tbody>
    `;

    // Recorrer los detalles y agregar filas a la tabla
    detalleArray.forEach(detalle => {
        aux_producto_id = detalle.producto_id;
        if(detalle.acuerdotecnico_id != 0){
            aux_producto_id = 
            `<a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="Acuerdo Técnico" onclick='genpdfAcuTec(${detalle.acuerdotecnico_id},${d.cliente_id},"")'>
                ${detalle.producto_id}
            </a>`;    
        }
        tableHtml += `
            <tr>
                <td style="text-align: center;">${aux_producto_id}</td>
                <td>${detalle.producto_nombre}</td>
                <td style="text-align: center;">${detalle.cant}</td>
                <td style="text-align: center;">${detalle.unidadmedida_nombre}</td>
                <td style="text-align: right;">${detalle.kg}</td>
            </tr>
        `;
    });

    // Cerrar la tabla
    tableHtml += `
                    </tbody>
                </table>
            </div>
        </div> <!-- Fin del contenedor Flex -->
    `;

    // Devolver la tabla HTML
    return tableHtml;
    
}

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


