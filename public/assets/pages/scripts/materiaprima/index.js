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
        'ajax'        : "materiaprimapage",
        'columns'     : [
            {data: 'id'},
            {data: 'nombre'},
            {data: 'desc'},
            // Aptitud para alimentos: sin definir se muestra en gris, para que se
            // note cuales le faltan por clasificar a Control de Calidad.
            {data: 'staaptoalimento', render: function (d) {
                if (d === null || d === '') return '<span class="text-muted">Sin definir</span>';
                return (parseInt(d) === 1)
                    ? '<span class="label label-success">Apto</span>'
                    : '<span class="label label-danger">No apto</span>';
            }},
            {defaultContent : 
                "<a href='materiaprima' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>"+
                    "<i class='fa fa-fw fa-pencil'></i>" +
                "</a>" +
                "<a href='materiaprima' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar este registro'>" + 
                    "<i class='fa fa-fw fa-trash text-danger'></i>" + 
                "</a>"}
        ],
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
      });

});