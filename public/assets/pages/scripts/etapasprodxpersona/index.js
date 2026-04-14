$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    nombreTabla = "#tabla-data-etapasprodxpersona";
    $(nombreTabla).append(encabezadoTabla());
    configurarTabla(nombreTabla,"",false)

    $('#tabla-data').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "etapasprodxpersonapage",
        'columns'     : [
            {data: 'id'},
            {data: 'rut'},
            {data: 'nombreapellido'},
            {data: 'email'},
            {defaultContent : 
                `<a href="etapasprodxpersona" class="btn-accion-tabla tooltipsC btnEditar" title="Editar este registro">
                    <i class="fa fa-fw fa-pencil"></i>
                </a>`
            }
        ],
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
      });

});

function encabezadoTabla(){
    let html = `
            <thead>
                <tr>
                    <th class="width70">ID</th>
                    <th class="width70">Etapa Produccion</th>
                    <th class="width70"></th>
                </tr>
            </thead>
            <tfoot>
            </tfoot>
        `;
    return html;
}

function configurarTabla(nombreTabla,url,serverSide) {
    // Si ya hay una tabla inicializada, la destruimos
    if ($.fn.DataTable.isDataTable(nombreTabla)) {
        $(nombreTabla).DataTable().destroy();
        $(nombreTabla).empty(); // Limpia la tabla para evitar errores de redibujado
    }

    //Asegura que la tabla tiene el encabezado correcto
    if ($(nombreTabla + " thead").length === 0) {
        $(nombreTabla).append(encabezadoTabla());
    }

    // Configura DataTable con `serverSide` dinámico
    tabla = $(nombreTabla).DataTable({
        'paging'      : true,
        'scrollx'     : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : serverSide, // Se define dinámicamente
        'ajax'        : url, // Se define dinámicamente
        'order'       : [[ 1, "desc" ]],
        'columns'     : [
            {data: 'id'}, // 1
            {data: 'etapaprod_nombre'}, // 2
        ],
        "language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            $(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);
            $(row).attr('updated_at', data.updated_at);
            $('td', row).eq(0).attr("id","aux_detalle" + data.id);
            $('td', row).eq(0).attr("name","aux_detalle" + data.id);
            $('td', row).eq(0).attr("etapaprod",data.detetapaprod_array);
            //console.log(data.detetapaprod_array);
            //"<a href='#' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + "</a>";

            aux_text = 
                `<a id="otdet_id${data.id}" name="otdet_id${data.id}" class="btn-accion-tabla btn-sm" title="Ver OT: ${data.ot_id}" onclick='genpdf(${data.ot_id},"","ver-pdf-guia-despacho","/ot/exportPdf/${data.ot_id}")'>
                    ${data.ot_id}-${data.id}
                </a>`;
            $('td', row).eq(1).html(aux_text);

            //$('td', row).eq(2).attr('style','font-size: 12px;');
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

            aux_text = data.producto_id;
            if(data.acuerdotecnico_id != 0){
                aux_text = 
                `<a style="padding-left: 0px; display: inline-block;" class="btn-accion-tabla btn-sm tooltipsC" title="Acuerdo Técnico" onclick='genpdfAcuTec(${data.acuerdotecnico_id},${data.cliente_id},"")'>
                    ${data.producto_id}
                </a>`;    
            }
            if(data.at_impresofoto != "" && data.at_impresofoto != null){
                aux_text += 
                    `<a style="display: inline-block;" class="btn-accion-tabla btn-sm tooltipsC" title="Ver Imagen" onclick='verpdf2(\"at/${data.at_impresofoto}\",2,"","ver-arte-acuerdo-tecnico")'>
                        <i class="fa fa-fw fa-photo"></i>
                    </a>`;
            }
            aux_obsext = "";
            if(data.at_materiaprimaobs){
                aux_obsext = data.at_materiaprimaobs;
            }
            aux_obssell = "";
            if(data.at_tiposelloobs){
                aux_obssell = data.at_tiposelloobs;
            }
            aux_obsimp = "";
            if(data.at_impresoobs){
                aux_obsimp = data.at_impresoobs;
            }
        
            $('td', row).eq(7).attr('style','font-size: 12px; white-space: nowrap;');
            $('td', row).eq(7).attr('name',"producto_id"+data.id);
            $('td', row).eq(7).attr('id',"producto_id"+data.id);
            $('td', row).eq(7).attr('file'+data.id,data.id);
            $('td', row).eq(7).attr('aux_obsext',aux_obsext);
            $('td', row).eq(7).attr('aux_obsimp',aux_obsimp);
            $('td', row).eq(7).attr('aux_obssell',aux_obssell);
            $('td', row).eq(7).html(aux_text);

            $('td', row).eq(9).attr('style','text-align:center');

            aux_text = MASKLA(data.at_espesor,3);
            aux_textespprod = MASKLA(data.espesorprod,3);
            $('td', row).eq(10).attr('style','text-align:right');
            $('td', row).eq(10).html(aux_text + '/<br>' + aux_textespprod);

            aux_text = MASKLA(data.espesorprod,3);
            $('td', row).eq(11).attr('style','text-align:right');
            $('td', row).eq(11).html(aux_text);


            aux_text = MASKLA(data.cantprod,0);
            $('td', row).eq(12).attr('style','text-align:right');
            $('td', row).eq(12).attr('title','Cantidad Produccion');
            $('td', row).eq(12).html(aux_text);

            aux_text = MASKLA(data.kgprod,2);
            $('td', row).eq(13).attr('id','kgprodOrig' + data.id);
            $('td', row).eq(13).attr('name','kgprodOrig' + data.id);
            $('td', row).eq(13).attr('style','text-align:right');
            $('td', row).eq(13).attr('title','Kilos Produccion.');
            $('td', row).eq(13).attr('valor',data.kgprod);
            $('td', row).eq(13).html(aux_text);

            aux_text = MASKLA(data.kgprog,2);
            $('td', row).eq(14).attr('id','kgprog' + data.id);
            $('td', row).eq(14).attr('name','kgprog' + data.id);
            $('td', row).eq(14).attr('style','text-align:right');
            $('td', row).eq(14).attr('title','Kilos Programados.');
            $('td', row).eq(14).attr('valor',data.kgprog);
            $('td', row).eq(14).html(aux_text);

            aux_kgprod = data.kgprod - data.kgprog;
            aux_kgprod = (aux_kgprod < 0) ? 0 : aux_kgprod; // Asegura que no sea negativo
            aux_text = 
                `<input type="text" name="kgprod[]" id="kgprod${data.id}" class="form-control numerico requerido${data.id}" value="${MASKLA(aux_kgprod,2)}" valor="${aux_kgprod}" valorOriginal="${data.kgprod}" item="${data.id}" style="width: 100px;text-align:right;" maxlength="15"/>`;
            $('td', row).eq(15).html(aux_text);


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

            // Generar el select con las Orden Atencion en la columna 14 (otdet_obs)
            var aux_text = `<select class="form-control requerido${data.id}" name="prioridad${data.id}" id="prioridad${data.id}" data-width="100%" data-size="5">`;
            aux_text += `<option value="">...</option>`;
            for (let i = 1; i < 4; i++) {
                aux_text += `<option value="${i}">${i}</option>`;
                
            }
            aux_text += `</select>`;
            $('td', row).eq(18).html(aux_text); // Insertar el select en la celda correspondiente

            aux_text = 
                `<textarea name="obs[]" id="obs${data.id}" class="form-control" value="" item="${data.id}" style="width: 150px;" placeholder="Observación" maxlength="100"></textarea>`;
            $('td', row).eq(17).html(aux_text);

                        aux_text = 
                `<div class="tools11">
                    <a ${aux_displaybtnbl} class="btn-accion-tabla botonbloq${data.id}" title="Condición financiera en revisión: ${aux_clienteBloqueado}" onclick="llenartablaDataCobranza(${data.id},${data.cliente_id},0,0)">
                        <i class="fa fa-fw fa-lock text-danger fa-lg"></i>
                    </a>
                    <a ${aux_displaybtnac} id="bntaprobnv${data.id}" name="bntaprobnv${data.id}" class="btn-accion-tabla btn-sm action-buttons botonac${data.id}" onclick="procesarRegOt(${data.id},${data.updatednum_at},${data.otupdatednum_at})" title="Enviar a Producción">
                        <i class="fa fa-fw fa-save fa-lg"></i>
                    </a>
                </div>`;
            //$('td', row).eq(12).attr('style','padding-top: 0px;padding-bottom: 0px;');
            $('td', row).eq(19).html(aux_text);            
            
            $('td', row).eq(23).addClass('updated_at');
            $('td', row).eq(23).attr('id','updated_at' + data.id);
            $('td', row).eq(23).attr('name','updated_at' + data.id);

            activarLimpiezaCampoRequerido(data.id);


        }
    });
    // Aplicar el plugin numeric a los inputs dinámicos después de cada redibujado de la tabla
    tabla.on('draw', function() {
        //$('.selectpicker').selectpicker();
        $('.selectpicker').selectpicker({
            container: 'body' // Hace que el dropdown no se esconda dentro del DataTable
        });
    });

    // Add event listener for opening and closing details

    
};