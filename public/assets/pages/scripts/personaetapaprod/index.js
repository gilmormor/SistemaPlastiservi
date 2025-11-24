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
        'ajax'        : "personaetapaprodpage",
        'columns'     : [
            {data: 'id'},
            {data: 'rut'},
            {data: 'nombreapellido'},
            {data: 'email'},
            {defaultContent : 
                `<a href="personaetapaprod" class="btn-accion-tabla tooltipsC btnEditar" title="Editar este registro">
                    <i class="fa fa-fw fa-pencil"></i>
                </a>`
            }
        ],
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
      });

});