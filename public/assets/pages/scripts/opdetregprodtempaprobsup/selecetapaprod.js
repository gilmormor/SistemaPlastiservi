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
        'serverSide'  : false,
        'ajax'        : RUTA_AJAX,
        'order'       : [[ 0, "asc" ]],
        'columns'     : [
            {data: 'orden'},
            {data: 'areaproduccionsucetapaprod_id'},
            {data: 'etapaprod_nombre'},
            {data: 'sucursal_nombre'},
            {defaultContent : ``},
        ],
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            aux_text = `<a href='set/${data.etapaprod_id}' class='btn-accion-tabla tooltipsC btnIndex' title='Ingresar Registro Produccion'>
                            <i class='fa fa-fw fa-sign-in'></i>
                        </a>`;
            $('td', row).eq(4).html(aux_text);
        }
      });

});

{/* <a href='opdetregprodtemp' class='btn-accion-tabla tooltipsC btnIndex' title='Ingresar Registro Produccion'>
                    <i class='fa fa-fw fa-pencil'></i>
                </a> */}