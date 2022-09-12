$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    configTablaCliente();
});

function configTablaCliente(){
    aux_nfila = 0;
    $("#tabla-data-clientes").attr('style','');
    //$("#tabla-data-clientes").DataTable().fnDestroy();
    $('#tabla-data-clientes').DataTable({
        'paging'      : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "cliente/clientebuscarpage",
        'columns'     : [
            {data: 'id'},
            {data: 'rut'},
            {data: 'razonsocial'},
            {data: 'direccion'},
            {data: 'telefono'}
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            aux_nfila++;
            selecmultprod = true;
            //aux_onclick = "llenarlistaprod(" + aux_nfila + "," + data.id + ")";
            aux_onclick = "copiar_rut(" + data.id + ",'" + data.rut + "')";

            $(row).attr('name', 'fila' + aux_nfila);
            $(row).attr('id', 'fila' + aux_nfila);
            $(row).attr('prodid', 'tooltip');
            $(row).attr('class', "btn-accion-tabla copiar_id");
            $(row).attr('data-toggle', data.id);
            $(row).attr('title', "Click para seleccionar cliente");
            $(row).attr('onClick', aux_onclick + ';');
        }
    });
}