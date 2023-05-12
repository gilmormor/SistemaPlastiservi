$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $('#tabla-data').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "clientetemppage",
        'columns'     : [
            {data: 'id'},
            {data: 'rut'},
            {data: 'razonsocial'},
            {data: 'vendedor_nombre'},
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : 
                "<a href='clientetemp' class='btn-accion-tabla tooltipsC btnEditar' title='Editar registro'>" + 
                    "<i class='fa fa-fw fa-pencil'></i>" + 
                "</a>"
                /*
                "<a href='clientetemp' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar registro'>"+
                    "<i class='fa fa-fw fa-trash text-danger'></i>"+
                "</a>"
                */
            }
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            aux_text = data.vendedor_nombre + " " + data.vendedor_apellido;
            $('td', row).eq(3).attr('data-order',aux_text);
            $('td', row).eq(3).html(aux_text);
        }
    });
});