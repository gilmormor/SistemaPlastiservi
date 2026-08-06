$(document).ready(function () {
    $('#tabla-data').DataTable({
        'paging'      : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "usuaprobmodulopage",
        'columns'     : [
            {data: 'id'},
            {data: 'usuario_nombre'},
            {data: 'modulo_nombre'},
            {data: 'usuarios_permitidos'},
            {data: 'bodegas_permitidas', defaultContent: '<span style="color:#aaa;">Todas</span>'},
            {defaultContent :
                "<a href='usuaprobmodulo' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>" +
                    "<i class='fa fa-fw fa-pencil'></i>" +
                "</a>"+
                "<a href='usuaprobmodulo' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar este registro'>"+
                    "<i class='fa fa-fw fa-trash text-danger'></i>"+
                "</a>"
            }
        ],
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
    });
});
