$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $('.date-picker').datepicker({
        language: "es",
        format: "MM yyyy",
        viewMode: "years", 
        minViewMode: "months",
        autoclose: true,
		todayHighlight: true
    }).datepicker("setDate");

    configurarTabla('#tabla-data');

    $('#annomes').on('change', function () {
        $('#tabla-data').DataTable().ajax.url( "categoriagrupovalmespage/" + $("#annomes").val() ).load();
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
            'ajax'        : "categoriagrupovalmespage/" + $("#annomes").val(),
            "order": [[ 1, "asc" ]],
            'columns'     : [
                {data: 'id'},
                {data: 'annomes'},
                {data: 'categorianombre'},
                {data: 'gru_nombre'},
                {data: 'costo'},
                {data: 'metacomerkg'},
                {defaultContent : 
                    "<a href='categoriagrupovalmes' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>"+
                        "<i class='fa fa-fw fa-pencil'></i>"+
                    "</a><a href='categoriagrupovalmes' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar este registro'>"+
                        "<i class='fa fa-fw fa-trash text-danger'></i>"+
                    "</a>"}
            ],
            "language": {
                //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "createdRow": function ( row, data, index ) {
                aux_mesanno = mesanno(data.annomes);
                $('td', row).eq(1).html(aux_mesanno);
                $('td', row).eq(1).attr('data-search',aux_mesanno);
                $('td', row).eq(4).attr('data-order',data.costo);
                $('td', row).eq(4).attr('data-search',data.costo);
                $('td', row).eq(4).attr('style','text-align:right');
                $('td', row).eq(4).html(MASK(0, data.costo, '-###,###,###,##0.00',1));
                $('td', row).eq(5).attr('data-order',data.metacomerkg);
                $('td', row).eq(5).attr('data-search',data.metacomerkg);
                $('td', row).eq(5).attr('style','text-align:right');
                $('td', row).eq(5).html(MASK(0, data.metacomerkg, '-###,###,###,##0.00',1));
            }
          });
    }


});
