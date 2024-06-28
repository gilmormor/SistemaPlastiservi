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
        'ajax'        : "grupocatprompage",
        'columns'     : [
            {data: 'id'},
            {data: 'nombre'},
            {data: 'categoriaprod_nombre'},
            {defaultContent : 
                "<a href='grupocatprom' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>"+
                    "<i class='fa fa-fw fa-pencil'></i>" +
                "</a>" +
                "<a href='grupocatprom' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar este registro'>" + 
                    "<i class='fa fa-fw fa-trash text-danger'></i>" + 
                "</a>"}
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
      });

});