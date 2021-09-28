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
        'ajax'        : "notaventatranspage",
        "order": [[ 0, "desc" ]],
        'columns'     : [
            {data: 'id'},
            {data: 'fechahora'},
            {data: 'razonsocial'},
            {data: 'aprobstatus',className:"ocultar"},
            {data: 'aprobobs',className:"ocultar"},
            {data: 'estado'},
            {data: 'pdfnv'}
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            aux_mensaje= "";
            aux_icono = "";
            aux_color = "";
            console.log(data.aprobstatus);
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
                    }
                }
            }
            aux_text = 
                "<a class='btn-xs tooltipsC "+aux_color+"' title='"+ aux_mensaje +"'>"+
                    "<span class='"+aux_icono+"' style='bottom: 0px;top: 2px;'></span>"+
                "</a>";
            $('td', row).eq(5).html(aux_text);
            console.log(aux_text);
            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Nota Venta: " + data.id + "' onclick='genpdfNV(" + data.id + ",1)'>"+
                    "<i class='fa fa-fw fa-file-pdf-o'></i>"+
                "</a>";
            $('td', row).eq(6).html(aux_text);
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

