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

    configurarTabla('#tabla-data-invstockvend');

    function configurarTabla(aux_tabla){
        data = datosinvstockvend();
        $(aux_tabla).DataTable({
            'paging'      : true, 
            'lengthChange': true,
            'ordering'    : true,
            'info'        : true,
            'autoWidth'   : false,
            'processing'  : true,
            'serverSide'  : true,
            'ajax'        : "reportinvstockvendpage/" + data.data2, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
            "order": [[ 1, "asc" ]],
            'columns'     : [
                {data: 'producto_id'},
                {data: 'producto_nombre'},
                {data: 'categoria_nombre'},
                {data: 'diametro'},
                {data: 'cla_nombre'},
                {data: 'long'},
                {data: 'peso'},
                {data: 'tipounion'},
                {data: 'stock'},
                {data: 'stockkg'}
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "createdRow": function ( row, data, index ) {
                $('td', row).eq(8).attr('style','text-align:center');
                //MASKLA(data.aux_totalkg,2);
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
                $('td', row).eq(0).attr('style','text-align:center');
                stockKg = data.stock * data.peso
                $('td', row).eq(6).html(NUM(data.peso, 2));
                $('td', row).eq(6).attr('style','text-align:right');
                $('td', row).eq(9).attr('style','text-align:right');
                $('td', row).eq(9).attr('data-order',stockKg);
                $('td', row).eq(9).attr('data-search',stockKg);
                $('td', row).eq(9).html(MASKLA(stockKg,2));
                $('td', row).eq(9).addClass('subtotalkg');

            }
        });
    }

    totalizar();

    $("#btnconsultar").click(function()
    {
        data = datosinvstockvend();
        $('#tabla-data-invstockvend').DataTable().ajax.url( "reportinvstockvendpage/" + data.data2 ).load();
        totalizar();
    });

});

function totalizar(){
    let  table = $('#tabla-data-invstockvend').DataTable();
    //console.log(table);
    table
        .on('draw', function () {
            eventFired( 'Page' );
        });
    data = datosinvstockvend();
    $.ajax({
        url: '/reportinvstock/totalizarindex/' + data.data2,
        type: 'GET',
        success: function (datos) {
            console.log(datos);
            $("#totalkg").html(MASKLA(datos.aux_totalkg,2));
            //$("#totaldinero").html(MASKLA(datos.aux_totaldinero,0));
        }
    });
}

var eventFired = function ( type ) {
	total = 0;
	$("#tabla-data-invstockvend tr .subtotalkg").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#subtotalkg").html(MASKLA(total,2))
}

function datosinvstockvend(){
    var data1 = {
        mesanno           : $("#annomes").val(),
        sucursal_id       : $("#sucursal_id").val(),
        producto_id       : $("#producto_idPxP").val(),
        categoriaprod_id  : $("#categoriaprod_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        tipobodega        : $("#tipobodega").val(),
        _token            : $('input[name=_token]').val()
    };

    var data2 = "?mesanno="+data1.mesanno +
    "&sucursal_id="+data1.sucursal_id +
    "&producto_id="+data1.producto_id +
    "&categoriaprod_id="+data1.categoriaprod_id +
    "&areaproduccion_id="+data1.areaproduccion_id +
    "&tipobodega="+data1.tipobodega


    var data = {
        data1 : data1,
        data2 : data2
    };
    return data;
}

$("#btnbuscarproducto").click(function(event){
    $(this).val("");
    $(".input-sm").val('');
    data = datosinvstockvend();
    $('#tabla-data-productos').DataTable().ajax.url( "producto/productobuscarpage/" + data.data2 + "&producto_id=" ).load();
    aux_id = $("#producto_idPxP").val();
    if( aux_id == null || aux_id.length == 0 || /^\s+$/.test(aux_id) ){
        $("#divprodselec").hide();
        $("#productos").html("");
    }else{
        arraynew = aux_id.split(',')
        $("#productos").html("");
        for(var i = 0; i < arraynew.length; i++){
            $("#productos").append("<option value='" + arraynew[i] + "' selected>" + arraynew[i] + "</option>")
        }
        $("#divprodselec").show();
    }
    $('#myModalBuscarProd').modal('show');
});

function copiar_codprod(id,codintprod){
    $("#myModalBuscarProd").modal('hide');
    aux_id = $("#producto_idPxP").val();
    if( aux_id == null || aux_id.length == 0 || /^\s+$/.test(aux_id) ){
        $("#producto_idPxP").val(id);
    }else{
        $("#producto_idPxP").val(aux_id + "," + id);
    }
	//$("#producto_idM").blur();
	$("#producto_idPxP").focus();
}

$("#btnpdf").click(function(event){
    data = datosinvstockvend();
    //alert(cadena);
    $('#contpdf').attr('src', '/reportinvstockvend/exportPdf/'+data.data2);
    //$('#contpdf').attr('src', '/notaventa/'+id+'/'+stareport+'/exportPdf');
	$("#myModalpdf").modal('show')
});