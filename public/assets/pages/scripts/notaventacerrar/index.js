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
        'columns'     : [
            {data: 'id'},
            {data: 'observacion'},
            {data: 'notaventa_id'},
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : "<a class='btn-accion-tabla btn-sm btngenpdfNV1 tooltipsC' title='Nota de venta'>" +
                                    "<i class='fa fa-fw fa-file-pdf-o'></i>" +
                               "</a>"+
                               "<a class='btn-accion-tabla btn-sm btngenpdfNV2 tooltipsC' title='Precio x Kg'>" +
                                    "<i class='fa fa-fw fa-file-pdf-o'></i>" +
                               "</a> | " +
                               "<a href='notaventacerrada' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>" +
                                    "<i class='fa fa-fw fa-pencil'></i>" +
                               "</a>" +
                               "<a href='notaventacerrada' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar este registro'>" + 
                                    "<i class='fa fa-fw fa-trash text-danger'></i>" +
                                "</a>"
            }
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
      });

    //{data: 'motcierre_id'},


});

