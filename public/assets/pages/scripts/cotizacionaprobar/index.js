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
        'ajax'        : "cotizacionaprobarpage",
        "order": [[ 0, "desc" ]],
        'columns'     : [
            {data: 'id'},
            {data: 'fechahora'},
            {data: 'razonsocial'},
            {data: 'vendedor_nombre'},
            {data: 'pdfcot'},
            {data: 'aprobstatus',className:"ocultar"},
            {data: 'contador',className:"ocultar"},
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : 
                "<a href='cotizacionaprobar' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>"+
                    "<i class='fa fa-fw fa-pencil'></i>"+
                "</a>"}
        ],
        "language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            aux_mensaje= "";
            aux_icono = "";
            aux_color = "";
            if (data.aprobstatus=='1'){
                aux_mensaje = "Aprobado Vendedor";
                aux_icono = "glyphicon glyphicon-thumbs-up";
                aux_color = "btn btn-success";
            }
            if (data.aprobstatus=='2'){
                aux_mensaje= "Precio menor en Tabla";
                aux_icono = "glyphicon glyphicon-thumbs-down";
                aux_color = "btn btn-danger";            
            }
            if (data.aprobstatus=='3'){
                aux_mensaje= "Precio menor Aprobado por supervisor";
                aux_icono = "glyphicon glyphicon-thumbs-up";
                aux_color = "btn btn-success";
            }
            codigo = data.cliente_id;
            if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)){
                aux_mensaje = aux_mensaje + " - Cliente Nuevo debe ser Validado";
                aux_icono = "glyphicon glyphicon-thumbs-down";
                aux_color = "btn btn-danger";
            }else{
                codigo = data.clientetemp_id;
                if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)){
                    aux_mensaje= aux_mensaje + " - Cliente Nuevo";
                    aux_icono = "glyphicon glyphicon-thumbs-up";
                    aux_color = "btn btn-success";
                }
            }
            aux_text = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Cotizacion: " + data.id + "' onclick='genpdfCOT(" + data.id + ",1)'>"+
                            "<i class='fa fa-fw fa-file-pdf-o'></i>"+
                        "</a>"
            $('td', row).eq(4).html(aux_text);
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

