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
        'ajax'        : "productopage",
        'columns'     : [
            {data: 'id'},
            {data: 'nombre'},
            {data: 'categorianombre'},
            {data: 'diametro'},
            {data: 'espesor'},
            {data: 'long'},
            {data: 'peso'},
            {data: 'tipounion'},
            {data: 'precioneto'},
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : "<a href='producto' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'><i class='fa fa-fw fa-pencil'></i></a><a href='producto' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar este registro'><i class='fa fa-fw fa-trash text-danger'></i></a>"}
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {

            numero = Number(data.peso.toFixed(2));
            $('td', row).eq(6).attr('data-order',data.peso);
            $('td', row).eq(6).attr('data-search',data.peso);
            $('td', row).eq(6).html(numero);

            $('td', row).eq(8).attr('data-order',data.precioneto);
            $('td', row).eq(8).attr('data-search',data.precioneto);
            $('td', row).eq(8).attr('style','text-align:right');
            $('td', row).eq(8).html(MASKLA(data.precioneto,2));
        }
      });

});
