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
        'ajax'        : "cotizaciontranspage",
        "order": [[ 0, "desc" ]],
        'columns'     : [
            {data: 'id'},
            {data: 'fechahora'},
            {data: 'razonsocial'},
            {data: 'aprobstatus',className:"ocultar"},
            {data: 'aprobobs',className:"ocultar"},
            {data: 'cliente_id',className:"ocultar"},
            {data: 'clientetemp_id',className:"ocultar"},
            {data: 'updated_at',className:"ocultar"},
            {data: 'estado'},
            {data: 'pdfcot'},
            {data: 'pdfcot'}
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            aux_mensaje= "";
            aux_icono = "";
            aux_color = "";
            if (data.aprobstatus=='1'){
                aux_mensaje = "Aprobado Vendedor";
                aux_icono = "glyphicon glyphicon-thumbs-up";
                aux_color = "btn btn-success";
            }else{
                if (data.aprobstatus=='2'){
                    aux_mensaje= "Precio menor en Tabla - Debe ser Aprobado";
                    aux_icono = "glyphicon glyphicon-thumbs-down";
                    aux_color = "btn btn-danger";            
                }else{
                    if (data.aprobstatus=='3'){
                        aux_mensaje= "Precio menor Aprobado por supervisor";
                        aux_icono = "glyphicon glyphicon-thumbs-up";
                        aux_color = "btn btn-success";
                    }else{
                        if (data.aprobstatus=='5'){
                            aux_mensaje= "Cotizacion contiene acuerdo técnico - Debe ser validado";
                            aux_icono = "glyphicon glyphicon-thumbs-down";
                            aux_color = "btn btn-danger";            
                        }else{
                            if (data.aprobstatus=='6'){
                                aux_mensaje= "Cotizacion con acuerdo técnico Aprobado";
                                aux_icono = "glyphicon glyphicon-thumbs-up";
                                aux_color = "btn btn-success";            
                            }
                        }
                    }
                }
            }
            if (data.aprobstatus != '2'){
                codigo = data.cliente_id;
                validacioncliente_id = (codigo == null || codigo.length == 0 || /^\s+$/.test(codigo));
                if( validacioncliente_id ){
                    aux_mensaje = aux_mensaje + " - Cliente Nuevo debe ser Validado";
                    aux_icono = "glyphicon glyphicon-thumbs-down";
                    aux_color = "btn btn-danger";
                }else{
                    codigo = data.clientetemp_id;
                    aux_validacion = (codigo == null || codigo.length == 0 || /^\s+$/.test(codigo))
                    if ( aux_validacion != true ){
                        aux_mensaje= aux_mensaje + " - Cliente Nuevo";
                        aux_icono = "glyphicon glyphicon-thumbs-up";
                        aux_color = "btn btn-success";
                    }
                }    
            }
            $('td', row).eq(7).html(data.updated_at);
            $('td', row).eq(7).attr("id","updated_at"+data.id);
            $('td', row).eq(7).attr("name","updated_at"+data.id);
            aux_text = 
                `<a class="btn-xs tooltipsC ${aux_color}" title="${aux_mensaje}">
                    <span class="${aux_icono}" style="bottom: 0px;top: 2px;"></span>
                </a>`;
            $('td', row).eq(8).html(aux_text);
            aux_text = 
                `<a class="btn-accion-tabla btn-sm tooltipsC" title="Cotizacion: ${data.id}" onclick="genpdfCOT(${data.id},1)">
                    <i class='fa fa-fw fa-file-pdf-o'></i>
                </a>`;
            $('td', row).eq(9).html(aux_text);
            aux_text = 
                `<a href="cotizacion/devolveracrecot" class="btn-accion-tabla btnVar tooltipsC" title="Devolver a Crear Cotizacion" updated_at="${data.updated_at}">
                    <button type="button" class="btn btn-warning btn-xs">
                        <i class="fa fa-fw fa-reply"></i>
                    </button>
                </a>
                <a href="cotizacion" class="btn-accion-tabla btnEliminar tooltipsC" title="Eliminar este registro">
                    <button type="button" class="btn btn-danger btn-xs">
                        <i class="fa fa-fw fa-trash"></i>
                    </button>
                </a>`;
            $('td', row).eq(10).html(aux_text);

            if ( data.contador * 1 > 0 ) {
                //console.log(row);
                ///$('tr').addClass('preciomenor');
                //$('td', row).parent().addClass('preciomenor tooltipsC');
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
                //$('td', row).parent().prop("title","Precio menor al valor en tabla")
            }
        }
    });


});

