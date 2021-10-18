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
        'ajax'        : "despachoordrecpage",
        'order': [[ 0, "desc" ]],
        'columns'     : [
            {data: 'id'},
            {data: 'fechahora'},
            {data: 'notaventa_id'},
            {data: 'despachosol_id'},
            {data: 'despachoord_id'},
            {data: 'razonsocial'},
            {data: 'fechahora_aaaammdd',className:"ocultar"},
            {defaultContent : "<a id='btnEditar' href='despachoordrec' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'> " + 
                    "<i class='fa fa-fw fa-pencil'></i>" + 
                "</a>" +
                "<!--<a href='despachoordrec' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar este registro'>" + 
                    "<i class='fa fa-fw fa-trash text-danger'></i>" + 
                "</a> -->" +
                "<a href='despachoordrec' class='btn-accion-tabla btn-sm btnAnular tooltipsC' title='Anular registro'>" +
                    "<span class='glyphicon glyphicon-remove' style='bottom: 0px;top: 2px;'></span>" +
                "</a>"
            }
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ){
            aux_texto = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfODRec(" + data.id + ",1)'>" +
                            data.id +
                        "</a>";
            $('td', row).eq(0).html(aux_texto);
            
            $('td', row).eq(1).attr('data-order',data.fechahora_aaaammdd);
            aux_fecha = new Date(data.fechahora_aaaammdd);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));
            aux_texto = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV(" + data.notaventa_id + ",1)'>" +
                            data.notaventa_id +
                        "</a>";
            $('td', row).eq(2).html(aux_texto);
            aux_texto = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Solicitud de Despacho' onclick='genpdfSD(" + data.despachosol_id + ",1)'>" +
                            data.despachosol_id +
                        "</a>";
            $('td', row).eq(3).html(aux_texto);
            aux_texto = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Despacho' onclick='genpdfOD(" + data.despachoord_id + ",1)'>" +
                            data.despachoord_id +
                        "</a>";
            $('td', row).eq(4).html(aux_texto);
        }
    });
});