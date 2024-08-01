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
        'ajax'        : "dteguiadespnvpage",
        'columns'     : [
            {data: 'id'}, // 0
            {data: 'fechahora'}, // 1
            {data: 'rut'}, // 2
            {data: 'razonsocial'}, // 3
            {data: 'oc_id'}, // 4
            {data: 'nrodocto'}, // 5
            {data: 'nombre_comuna'}, // 6
            {data: 'clientebloqueado_descripcion',className:"ocultar"}, //7
            {data: 'oc_file',className:"ocultar"}, //8
            {data: 'oc_file',className:"ocultar"}, //9
            {data: 'nombrepdf',className:"ocultar"}, //10           
            {data: 'updated_at',className:"ocultar"}, //11
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : ""}
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            $(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);
            $(row).attr('updated_at', data.updated_at);
            //"<a href='#' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + "</a>";

            let id_str = data.nrodocto.toString();
            id_str = data.nombrepdf + id_str.padStart(8, "0");
            aux_text = 
            "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Guia Despacho' onclick='genpdfFAC(\"" + id_str + "\",\"\")'>" +
                data.id +
            "</a>";
            $('td', row).eq(0).html(aux_text);

            $('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));


            aux_text = "";
            if(data.oc_file != "" && data.oc_file != null){
                aux_text = 
                "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Compra' onclick='verpdf3(\"" + data.oc_file + "\",2,\"" + data.oc_folder + "\")'>" + 
                    data.oc_id + 
                "</a>";
                $('td', row).eq(4).html(aux_text);
            }



            aux_text = 
            "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Guia despacho' onclick='genpdfFAC(\"" + id_str + "\",\"\")'>" +
                data.nrodocto +
            "</a>:" +
            "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Cedible' onclick='genpdfFAC(\"" + id_str + "\",\"_cedible\")'>" +
                data.nrodocto +
            "</a>";
            $('td', row).eq(5).html(aux_text);

            $('td', row).eq(11).addClass('updated_at');
            $('td', row).eq(11).attr('id','updated_at' + data.id);
            $('td', row).eq(11).attr('name','updated_at' + data.id);

            /* aux_text = 
            `<a id="bntaproord${data.id}" name="bntaproord${data.id}" class="btn-accion-tabla btn-sm tooltipsC" onclick="procesarDTE(${data.id})" title="Enviar a procesados">
                <span class="glyphicon glyphicon-floppy-save" style="bottom: 0px;top: 2px;"></span>
            </a> | 
            <a onclick="volverGenDTE(${data.id})" class="btn-accion-tabla btn-sm tooltipsC" title="Volver a Generar DTE" data-toggle="tooltip">
                <span class="fa fa-upload text-danger"></span>
            </a>`;
            $('td', row).eq(12).html(aux_text); */

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
    

            stasubsii_text = 
            `<a ${aux_displaybtnac} onclick="volverGenDTE(${data.id})" class="btn-accion-tabla btn-sm tooltipsC botonac${data.id}" title="Volver a Generar DTE" data-toggle="tooltip">
                <span class="fa fa-upload text-danger"></span>
            </a>`;
            $('td',row).eq(12).attr('stasubsii',stasubsii_text);

            bntaproord_text = 
            `<a ${aux_displaybtnac} id="bntaproord${data.id}" name="bntaproord${data.id}" class="btn-accion-tabla btn-sm tooltipsC botonac${data.id}" onclick="procesarDTE(${data.id},20)" title="Enviar a procesados">
                <span class="glyphicon glyphicon-floppy-save" style="bottom: 0px;top: 2px;"></span>
            </a>`;
            $('td',row).eq(12).attr('bntaproord',bntaproord_text);

            aux_text = '';
            if(data.stasubsii == 0){
                aux_text = stasubsii_text;
            }else{
                aux_text = bntaproord_text;
            }
            aux_text += 
            `<a ${aux_displaybtnbl} class="btn-accion-tabla btn-sm tooltipsC botonbloq${data.id}" title="Cliente Bloqueado: ${aux_clienteBloqueado}" onclick="llenartablaDataCobranza(${data.id},${data.cliente_id},${data.notaventa_id},0)">
                <span class="fa fa-fw fa-lock text-danger text-danger" style="bottom: 0px;top: 2px;"></span>
            </a>`;

            $('td',row).eq(12).attr('id','accion' + data.id);
            $('td',row).eq(12).attr('name','accion' + data.id);
            $('td', row).eq(12).html(aux_text);





        }
    });
});