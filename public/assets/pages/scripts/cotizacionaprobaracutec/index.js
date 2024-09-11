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
        'ajax'        : "cotizacionaprobaracutecpage",
        "order": [[ 0, "desc" ]],
        'columns'     : [
            {data: 'id'},
            {data: 'fechahora'},
            {data: 'razonsocial'},
            {data: 'vendedor_nombre'},
            {data: 'pdfcot'},
            {data: 'aprobstatus',className:"ocultar"},
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : 
                "<a href='cotizacionaprobaracutec' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>"+
                    "<i class='fa fa-fw fa-pencil'></i>"+
                "</a>"}
        ],
        "language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            $('td', row).eq(0).attr("updated_at",data.updated_at);
            aux_text = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Cotizacion: " + data.id + "' onclick='genpdfCOT(" + data.id + ",1)'>"+
                            "<i class='fa fa-fw fa-file-pdf-o'></i>"+
                        "</a>"
            $('td', row).eq(4).html(aux_text);    
        }
    });
});