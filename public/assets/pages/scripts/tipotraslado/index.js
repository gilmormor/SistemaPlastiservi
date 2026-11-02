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
        'ajax'        : "tipoTrasladoPage",
        'columns'     : [
            {data: 'id'},
            {data: 'indtraslado'},
            {data: 'desc'},
            {data: 'activo', render: function(data) {
                return data == 1
                    ? '<span class="label label-success">Activo</span>'
                    : '<span class="label label-default">Inactivo</span>';
            }},
            {defaultContent:
                "<a href='tipotraslado' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>" +
                    "<i class='fa fa-fw fa-pencil'></i>" +
                "</a>" +
                "<a href='tipotraslado' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar este registro'>" +
                    "<i class='fa fa-fw fa-trash text-danger'></i>" +
                "</a>"
            }
        ],
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
    });
});
