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
        'ajax'        : "notaventacerradapage",
        "order": [[ 0, "desc" ]],
        'columns'     : [
            {data: 'id'},
            {data: 'created_at'},
            {data: 'observacion'},
            {data: 'descdet'},
            {data: 'notaventa_id'},
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent :  "<a href='notaventacerrada' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>" +
                                    "<i class='fa fa-fw fa-pencil'></i>" +
                               "</a>" +
                               "<a href='notaventacerrada' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar este registro'>" + 
                                    "<i class='fa fa-fw fa-trash text-danger'></i>" +
                                "</a>"
            }
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            aux_fecha = new Date(data.created_at);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha) + " " + data.created_at.substr(11, 8));

            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de venta: " + data.notaventa_id + "' onclick='genpdfNV(" + data.notaventa_id + ",1)'>" +
                    data.notaventa_id + "<i class='fa fa-fw fa-file-pdf-o'></i>" +
                "</a>"+
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Precio x Kg: " + data.notaventa_id + "' onclick='genpdfNV(" + data.notaventa_id + ",2)'>" +
                    "<i class='fa fa-fw fa-file-pdf-o'></i>" +
                "</a>";
            $('td', row).eq(2).html(aux_text);

        }
      });
});

