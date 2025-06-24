$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    i = 0;
    $('#tabla-data-otnv').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        "order"       : [[ 0, "desc" ]],
        'ajax'        : "otnvpage",
        'columns'     : [
            {data: 'id'},
            {data: 'fechahora'},
            {data: 'razonsocial'},
            {data: 'oc_id'},
            {data: 'notaventa_id'},
            {data: 'notaventa_id'},
            {data: 'comuna_nombre'},
            {data: 'aux_totalkg'},
            {data: 'clientebloqueado_descripcion',className:"ocultar"},
            {data: 'oc_file',className:"ocultar"},
            {data: 'obsdev',className:"ocultar"},
            {data: 'updated_at',className:"ocultar"},
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : ""}
        ],
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            i++;
/*             $(row).attr('id','fila'+i);
            $(row).attr('name','fila'+i);
            $(row).attr('item',data.id);
 */
            $(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);
            $(row).attr('updated_at', data.updated_at);


            //"<a href='#' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + "</a>";
            /* aux_text = 
                "<a class='btn-accion-tabla btn-sm' title='Ver Solicitud despacho: " + data.id + "' onclick='genpdfSD(" + data.id + ",1)'>"+
                    + data.id +
                "</a>"; */
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


            $('td', row).eq(0).html(aux_text);

            $('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));

            if(data.obsdev != "" && data.obsdev != null){
				aux_text = data.razonsocial +
				" <a class='btn-sm' title='" + data.obsdev + "'>" +
					"<i class='fa fa-fw fa-question-circle text-red'></i>" + 
				"</a>";
				$('td', row).eq(2).html(aux_text);
			}


            if(data.oc_file != "" && data.oc_file != null){
                aux_text = 
                    "<a class='btn-accion-tabla btn-sm' title='Ver Orden de Compra' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + 
                        data.oc_id + 
                    "</a>";
                $('td', row).eq(3).html(aux_text);
                /*EN COMENTARIO PORQUE NO HACE FALTA MOSTRAR ESTO, IGUALMETE QUEDA EN COMENTARIO
                if(data.dte_nrodocto != null){
                    let cadena = data.dte_nrodocto
					if(cadena.includes(";")){
                        aux_nroguia = cadena.split(";")[0]; 
                        aux_ocid = cadena.split(";")[1]; 
						aux_folderNamefile = cadena.split(";")[2];
					}
                    aux_title = `Orden de Compra ${data.oc_id}, tiene Guia de despacho generada previamente: ${aux_nroguia}`;
                    colorinfo = `text-red`;
                    aux_text +=
                        `<br>(<a class="btn-sm" title="${aux_title}" style="padding-left: 0px;padding-right: 0px;">
                            <i class="fa fa-fw fa-question-circle ${colorinfo}"></i>
                        </a>`;

                    aux_text += 
                    `<a class="btn-accion-tabla btn-sm" onclick="genpdfGD('${aux_nroguia}','')" data-original-title="Guia despacho:${aux_nroguia}" style='color:#bc3c3c'>
                        ${aux_nroguia}
                    </a>,`;

                    aux_text += 
                    `<a class="btn-accion-tabla btn-sm" title="Orden de Compra" onclick="verpdf2('${aux_folderNamefile}',2)" style='color:#bc3c3c'>
                        ${aux_ocid}
                    </a>)`;

                    $('td', row).eq(3).html(aux_text);

                    //$('td', row).eq(4).html($('td', row).eq(4).html() + aux_text);
                }
                */
            }
            aux_text = 
                "<a class='btn-accion-tabla btn-sm' title='Nota de Venta' onclick='genpdfNV(" + data.notaventa_id + ",1)'>" +
                    data.notaventa_id +
                "</a>";
            $('td', row).eq(4).html(aux_text);

            aux_text = 
                "<a class='btn-accion-tabla btn-sm' title='Precio x Kg PDF' onclick='genpdfNV(" + data.notaventa_id + ",2)'>" +
                    "<i class='fa fa-fw fa-file-pdf-o'></i>" +
                "</a>";
            $('td', row).eq(5).html(aux_text);

            $('td', row).eq(7).attr('data-order',data.aux_totalkg);
            $('td', row).eq(7).attr('style','text-align:right');
            aux_text = MASKLA(data.aux_totalkg,2);
            $('td', row).eq(7).html(aux_text);
            $('td', row).eq(7).addClass('subtotalkg');

            

            $('td', row).eq(12).attr('class','action-buttons');
            aux_clienteBloqueado = validarClienteBloqueadoxModulo(data); 
            aux_displaybtnac = `style="padding-left: 0px;"`;
            aux_displaybtnbl = `style="padding-left: 0px;"`;
            aux_iconobloqueo = "fa-lock text-danger";
            aux_mensajebloqueo = `Condición financiera en revisión: ${aux_clienteBloqueado}`;
            if(aux_clienteBloqueado == ""){
                aux_displaybtnac = `style="padding-left: 0px;"`;
                aux_displaybtnbl = `style="display:none;padding-left: 0px;"`;
            }else{
                aux_displaybtnac = `style="display:none;padding-left: 0px;"`;
                aux_displaybtnbl = `style="padding-left: 0px;"`;
                if(data.modulo_id_orddesp  !== null){
                    aux_iconobloqueo = "fa-unlock text-yellow";
                    aux_mensajebloqueo = `Cliente Habilitado.`;
                }
            }
            aux_displaybtnac = `style="padding-left: 0px;"`;

            if(data.modulo_id_orddesp  !== null){
                aux_displaybtnbl = `style="padding-left: 0px;"`;
                aux_iconobloqueo = "fa-unlock text-yellow";
                aux_mensajebloqueo = `Cliente Habilitado.`;
            }


            /* if(data.clientebloqueado_descripcion != null){
                aux_text = 
                    "<a class='btn-accion-tabla btn-sm' title='Condición financiera en revisión: " + data.clientebloqueado_descripcion + "'>"+
                        "<span class='fa fa-fw fa-lock text-danger text-danger' style='bottom: 0px;top: 2px;'></span>"+
                    "</a>";
            }else{

                aux_text = 
                `<a href="/otnv/aproborddesp" class="btn-accion-tabla btn-sm btnaprobar" title="Aprobar Solicitud Despacho" item="${data.id}">
                    <span class="glyphicon glyphicon-floppy-save" style="bottom: 0px;top: 2px;"></span>
                </a>
                <a href="ot" class="btn-accion-tabla btnEditar" title="Editar este registro" item="${data.id}">
                    <i class='fa fa-fw fa-pencil'></i>
                </a>`;
            } */
            aux_text = 
            `<a ${aux_displaybtnac} class="btn-accion-tabla btn-sm botonac${data.id}" title="Aprobar OT" item="${data.id}" onclick="procesarReg(${data.id},32,'/otnv/procesar',${data.updatednum_at},'Procesar',0)">
                <i class="fa fa-fw fa-save fa-lg"></i>
            </a>
            <a href="${data.rutaeditar}" style="padding-left: 0px;" class="btn-accion-tabla botonac${data.id}" title="Editar este registro" item="${data.id}">
                <i class='fa fa-fw fa-pencil fa-lg'></i>
            </a>`;
            $('td', row).eq(11).addClass('updated_at');
            $('td', row).eq(11).attr('item',data.id);
            $('td', row).eq(11).attr('id','updated_at'+data.id);
            $('td', row).eq(11).attr('name','updated_at'+data.id);
            aux_text = aux_text +
            `<a style="padding-left: 0px;" class="btn-accion-tabla btn-sm" title="Anular OT" data-toggle="tooltip" item="${data.id}" onclick="procesarReg(${data.id},32,'/otnv/anular',${data.updatednum_at},'Anular',1)">
                <span class="glyphicon glyphicon-remove text-danger"></span>
            </a>
            <a ${aux_displaybtnbl} class="btn-accion-tabla btn-sm botonbloq${data.id}" title="${aux_mensajebloqueo}" onclick="llenartablaDataCobranza(${data.id},${data.cliente_id},${data.notaventa_id},0)">
                <i class="fa fa-fw ${aux_iconobloqueo} fa-lg"></i>
            </a>`;
            $('td', row).eq(12).html(aux_text);
            /* Santa Ester
            $('td', row).eq(11).addClass('updated_at');

            aux_text = aux_text +
            "<a href='ot' class='btn-accion-tabla btn-sm btnAnular' title='Anular Solicitud Despacho' data-toggle='tooltip'>"+
                "<span class='glyphicon glyphicon-remove text-danger'></span>"
            "</a>";
            $('td', row).eq(12).html(aux_text);
            */
        }
    });

    let  table = $('#tabla-data-otnv').DataTable();
    //console.log(table);
    table
        .on('draw', function () {
            eventFired( 'Page' );
        });

    $.ajax({
        url: '/otnv/totalizarindex',
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
	$("#tabla-data-otnv tr .subtotalkg").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#subtotalkg").html(MASKLA(total,2))
}
