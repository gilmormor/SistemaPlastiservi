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
        'pageLength'  : 10,
        'columns'     : [
            {data: 'id'},
            {data: 'sku'},
            {data: 'nombre_producto'},
            {data: 'categorianombre'},
            {data: 'gru_nombre'},
            {data: 'grupocatprom_nombre'},
            {data: 'cla_nombre'},
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
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {

            $('td', row).eq(10).attr('data-order',data.peso);
            $('td', row).eq(10).attr('data-search',data.peso);
            $('td', row).eq(10).html(MASKLA(data.peso,3));

            $('td', row).eq(12).attr('data-order',data.precioneto);
            $('td', row).eq(12).attr('data-search',data.precioneto);
            $('td', row).eq(12).attr('style','text-align:right');
            $('td', row).eq(12).html(MASKLA(data.precioneto,2));
        }
      });

});
