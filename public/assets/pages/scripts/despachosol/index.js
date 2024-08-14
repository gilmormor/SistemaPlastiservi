$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    i = 0;
    $('#tabla-data-despachosol').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        "order"       : [[ 0, "desc" ]],
        'ajax'        : "despachosolpage",
        'columns'     : [
            {data: 'id'},
            {data: 'fechahora'},
            {data: 'razonsocial'},
            {data: 'oc_id'},
            {data: 'notaventa_id'},
            {data: 'notaventaxk'},
            {data: 'comuna_nombre'},
            {data: 'aux_totalkg'},
            {data: 'tipoentrega_nombre'},
            {data: 'icono',className:"ocultar"},
            {data: 'clientebloqueado_descripcion',className:"ocultar"},
            {data: 'oc_file',className:"ocultar"},
            {data: 'obsdev',className:"ocultar"},
            {data: 'updated_at',className:"ocultar"},
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : ""}
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            i++;
            $(row).attr('id','fila'+i);
            $(row).attr('name','fila'+i);
            $(row).attr('item',data.id);
            //"<a href='#' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + "</a>";
            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Solicitud despacho: " + data.id + "' onclick='genpdfSD(" + data.id + ",1)'>"+
                    + data.id +
                "</a>";
            $('td', row).eq(0).html(aux_text);

            $('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));

            if(data.obsdev != "" && data.obsdev != null){
				aux_text = data.razonsocial +
				" <a class='btn-sm tooltipsC' title='" + data.obsdev + "'>" +
					"<i class='fa fa-fw fa-question-circle text-red'></i>" + 
				"</a>";
				$('td', row).eq(2).html(aux_text);
			}


            if(data.oc_file != "" && data.oc_file != null){
                aux_text = 
                    "<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Orden de Compra' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + 
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
                        `<br>(<a class="btn-sm tooltipsC" title="${aux_title}" style="padding-left: 0px;padding-right: 0px;">
                            <i class="fa fa-fw fa-question-circle ${colorinfo}"></i>
                        </a>`;

                    aux_text += 
                    `<a class="btn-accion-tabla btn-sm tooltipsC" onclick="genpdfGD('${aux_nroguia}','')" data-original-title="Guia despacho:${aux_nroguia}" style='color:#bc3c3c'>
                        ${aux_nroguia}
                    </a>,`;

                    aux_text += 
                    `<a class="btn-accion-tabla btn-sm tooltipsC" title="Orden de Compra" onclick="verpdf2('${aux_folderNamefile}',2)" style='color:#bc3c3c'>
                        ${aux_ocid}
                    </a>)`;

                    $('td', row).eq(3).html(aux_text);

                    //$('td', row).eq(4).html($('td', row).eq(4).html() + aux_text);
                }
                */
            }
            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV(" + data.notaventa_id + ",1)'>" +
                    data.notaventa_id +
                "</a>";
            $('td', row).eq(4).html(aux_text);

            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Precio x Kg PDF' onclick='genpdfNV(" + data.notaventa_id + ",2)'>" +
                    "<i class='fa fa-fw fa-file-pdf-o'></i>" +
                "</a>";
            $('td', row).eq(5).html(aux_text);

            $('td', row).eq(7).attr('data-order',data.aux_totalkg);
            $('td', row).eq(7).attr('style','text-align:right');
            aux_text = MASKLA(data.aux_totalkg,2);
            $('td', row).eq(7).html(aux_text);
            $('td', row).eq(7).addClass('subtotalkg');

            
            aux_text = 
                "<i class='fa fa-fw " + data.icono + " tooltipsC' title='" + data.tipoentrega_nombre + "'></i>";
            $('td', row).eq(8).html(aux_text);

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

            /* if(data.clientebloqueado_descripcion != null){
                aux_text = 
                    "<a class='btn-accion-tabla btn-sm tooltipsC' title='Cliente Bloqueado: " + data.clientebloqueado_descripcion + "'>"+
                        "<span class='fa fa-fw fa-lock text-danger text-danger' style='bottom: 0px;top: 2px;'></span>"+
                    "</a>";
            }else{

                aux_text = 
                `<a href="/despachosol/aproborddesp" class="btn-accion-tabla btn-sm tooltipsC btnaprobar" title="Aprobar Solicitud Despacho" item="${data.id}">
                    <span class="glyphicon glyphicon-floppy-save" style="bottom: 0px;top: 2px;"></span>
                </a>
                <a href="despachosol" class="btn-accion-tabla tooltipsC btnEditar" title="Editar este registro" item="${data.id}">
                    <i class='fa fa-fw fa-pencil'></i>
                </a>`;
            } */
            aux_text = 
            `<a ${aux_displaybtnbl} class="btn-accion-tabla btn-sm tooltipsC botonbloq${data.id}" title="Cliente Bloqueado: ${aux_clienteBloqueado}" onclick="llenartablaDataCobranza(${data.id},${data.cliente_id},${data.notaventa_id},0)">
                <i class="fa fa-fw fa-lock text-danger fa-lg"></i>
            </a>
            <a ${aux_displaybtnac} href="/despachosol/aproborddesp" class="btn-accion-tabla btn-sm tooltipsC btnaprobar botonac${data.id}" title="Aprobar Solicitud Despacho" item="${data.id}">
                <i class="fa fa-fw fa-save fa-lg"></i>
            </a>
            <a href="despachosol" class="btn-accion-tabla tooltipsC btnEditar botonac${data.id}" title="Editar este registro" item="${data.id}">
                <i class='fa fa-fw fa-pencil fa-lg'></i>
            </a>`;
            $('td', row).eq(13).addClass('updated_at');
            $('td', row).eq(13).attr('item',data.id);
            $('td', row).eq(13).attr('id','updated_at'+data.id);
            $('td', row).eq(13).attr('name','updated_at'+data.id);
            aux_text = aux_text +
            `<a href="despachosol" class="btn-accion-tabla btn-sm btnAnular tooltipsC" title="Anular Solicitud Despacho" data-toggle="tooltip" item="${data.id}">
                <span class="glyphicon glyphicon-remove text-danger"></span>
            </a>`;
            $('td', row).eq(14).html(aux_text);
            /* Santa Ester
            $('td', row).eq(13).addClass('updated_at');

            aux_text = aux_text +
            "<a href='despachosol' class='btn-accion-tabla btn-sm btnAnular tooltipsC' title='Anular Solicitud Despacho' data-toggle='tooltip'>"+
                "<span class='glyphicon glyphicon-remove text-danger'></span>"
            "</a>";
            $('td', row).eq(14).html(aux_text);
            */
        }
    });

    let  table = $('#tabla-data-despachosol').DataTable();
    //console.log(table);
    table
        .on('draw', function () {
            eventFired( 'Page' );
        });

    $.ajax({
        url: '/despachosol/totalizarindex',
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
	$("#tabla-data-despachosol tr .subtotalkg").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#subtotalkg").html(MASKLA(total,2))
}
