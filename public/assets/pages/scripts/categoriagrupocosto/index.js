$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $('.date-picker').datepicker({
        language: "es",
        format: "MM yyyy",
        viewMode: "years", 
        minViewMode: "months",
        autoclose: true,
        clearBtn : true,
		todayHighlight: true
    }).datepicker("setDate");

    configurarTabla('#tabla-data');

    $('#annomes').on('change', function () {
        $('#tabla-data').DataTable().ajax.url( "categoriagrupocostopage/" + $("#annomes").val() ).load();
        //configurarTabla('#tabla-data');

    });

    function configurarTabla(aux_tabla){
        $(aux_tabla).DataTable({
            'paging'      : true, 
            'lengthChange': true,
            'searching'   : true,
            'ordering'    : true,
            'info'        : true,
            'autoWidth'   : false,
            'processing'  : true,
            'serverSide'  : true,
            'ajax'        : "categoriagrupocostopage/" + $("#annomes").val(),
            "order": [[ 1, "asc" ]],
            'columns'     : [
                {data: 'id'},
                {data: 'annomes'},
                {data: 'categorianombre'},
                {data: 'gru_nombre'},
                {data: 'costo'},
                {defaultContent : 
                    "<a href='categoriagrupocosto' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>"+
                        "<i class='fa fa-fw fa-pencil'></i>"+
                    "</a><a href='categoriagrupocosto' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar este registro'>"+
                        "<i class='fa fa-fw fa-trash text-danger'></i>"+
                    "</a>"}
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "createdRow": function ( row, data, index ) {
                $('td', row).eq(1).html(mesanno(data.annomes));
                $('td', row).eq(1).attr('data-search',mesanno(data.annomes));
                $('td', row).eq(4).attr('data-order',data.costo);
                $('td', row).eq(4).attr('data-search',data.costo);
                $('td', row).eq(4).attr('style','text-align:right');
                $('td', row).eq(4).html(MASK(0, data.costo, '-###,###,###,##0.00',1));
            }
          });
    }


});
