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
        'ajax'        : "areaproduccionsuclineapage",
        'columns'     : [
            {data: 'id'},
            {data: 'nombre'},
            {data: 'desc'},
            {data: 'obs'},
            {data: 'activo'},
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : 
                "<a href='areaproduccionsuclinea' class='btn-accion-tabla tooltipsC btnEditar' title='Editar registro'>" + 
                    "<i class='fa fa-fw fa-pencil'></i>" + 
                "</a>"+
                "<a href='areaproduccionsuclinea' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar registro'>"+
                    "<i class='fa fa-fw fa-trash text-danger'></i>"+
                "</a>"
            }
        ],
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            aux_text = "Activo";
            if(data.activo == 0){
                aux_text = "Inactivo";
            }
            $('td', row).eq(4).html(aux_text);
        }
    });
});