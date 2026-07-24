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
        'ajax'        : 'ccparampage',
        'columns'     : [
            {data: 'id'},
            {data: 'nombre'},
            {data: 'etiqueta'},
            {data: 'tipo', render: function(d) {
                var map = {number: 'Numérico', text: 'Texto', boolean: 'Sí/No'};
                return map[d] || d;
            }},
            {data: 'unidad', defaultContent: '—'},
            {data: 'orden'},
            {defaultContent:
                "<a href='ccparam' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>" +
                    "<i class='fa fa-fw fa-pencil'></i>" +
                "</a>" +
                "<a href='ccparam' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar este registro'>" +
                    "<i class='fa fa-fw fa-trash text-danger'></i>" +
                "</a>"
            }
        ],
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
    });
});
