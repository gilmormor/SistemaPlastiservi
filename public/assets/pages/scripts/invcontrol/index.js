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
        data = datos();
        $('#tabla-data').DataTable().ajax.url( "invcontrolpage/" + data.data2 ).load();
        //$('#tabla-data').DataTable().ajax.url( "invcontrolpage/" + $("#annomes").val() + "/sucursal/" + $("#sucursal_id").val() ).load();
        //configurarTabla('#tabla-data');

    });
    $('#sucursal_id').on('change', function () {
        data = datos();
        $('#tabla-data').DataTable().ajax.url( "invcontrolpage/" + data.data2 ).load();
        //$('#tabla-data').DataTable().ajax.url( "invcontrolpage/" + $("#annomes").val() + "/sucursal/" + $("#sucursal_id").val() ).load();
        //configurarTabla('#tabla-data');

    });

    function configurarTabla(aux_tabla){
        data = datos();
        $(aux_tabla).DataTable({
            'paging'      : true, 
            'lengthChange': true,
            'searching'   : true,
            'ordering'    : true,
            'info'        : true,
            'autoWidth'   : false,
            'processing'  : true,
            'serverSide'  : true,
            'ajax'        : "invcontrolpage/" + data.data2, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
            "order": [[ 0, "asc" ]],
            'columns'     : [
                {data: 'producto_id'},
                {data: 'producto_nombre'},
                {data: 'categoria_nombre'},
                {data: 'invbodega_nombre'},
                {data: 'stock'}
            ],
            "language": {
                //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "createdRow": function ( row, data, index ) {
                $('td', row).eq(4).attr('style','text-align:center');

                /*
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
                */
            }
        });
    }

    $("#btnprocesar").click(function()
    {

        var data = {
            annomes           : $("#annomes").val(),
            sucursal_id       : $("#sucursal_id").val(),
            _token            : $('input[name=_token]').val()
        };
    

        var ruta = '/invcontrol/procesarcierreini';
        swal({
            title: '¿ Seguro desea continuar ?',
            text: "Esta acción no se puede deshacer!",
                icon: 'warning',
            buttons: {
                cancel: "Cancelar",
                confirm: "Aceptar"
            },
        }).then((value) => {
            if (value) {
                ajaxRequest(data,ruta,'btnprocesar');
            }
        });
    });

});

function datos(){
    var data1 = {
        mesanno           : $("#annomes").val(),
        sucursal_id       : $("#sucursal_id").val(),
        _token            : $('input[name=_token]').val()
    };

    var data2 = "?mesanno="+data1.mesanno +
    "&sucursal_id="+data1.sucursal_id

    var data = {
        data1 : data1,
        data2 : data2
    };
    return data;
}

function ajaxRequest(data,url,funcion,form = false) {
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success: function (respuesta) {

            if(funcion=='btnprocesar'){
                Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipomensaje);    
            }
        },
        error: function () {
        }
    });
}