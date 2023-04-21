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

    $('.datepicker').datepicker({
		language: "es",
        autoclose: true,
        clearBtn : true,
		todayHighlight: true
    }).datepicker("setDate");

    aux_nombretablarep = "#tabla-data-reporte-pesaje-agrupar-grupocat";

    configurarTabla(aux_nombretablarep);

    function configurarTabla(aux_tabla){
        data = datospesaje(0);
        $(aux_tabla).DataTable({
            'paging'      : true, 
            'lengthChange': true,
            'ordering'    : true,
            'info'        : true,
            'autoWidth'   : false,
            'processing'  : true,
            'serverSide'  : true,
            'ajax'        : "reportpesajegrupopage/" + data.data2, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
            "order": [
                [ 0, "asc" ],
            ],
            'columns'     : [
                {data: 'categoriaprodgrupo_nombre'},
                {data: 'pesototalnorma'},
                {data: 'pesototalprodbal'},
                {data: 'difkg'},
                {data: 'difkg'}
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "createdRow": function ( row, data, index ) {
                $('td', row).eq(0).attr('style','text-align:left');

                $('td', row).eq(1).attr('style','text-align:right');
                $('td', row).eq(1).attr('data-search',data.pesototalprodbal);
                $('td', row).eq(1).attr('data-order',data.pesototalprodbal);
                $('td', row).eq(1).html(MASKLA(data.pesototalprodbal,2));

                $('td', row).eq(2).attr('style','text-align:right');
                $('td', row).eq(2).attr('data-search',data.pesototalnorma);
                $('td', row).eq(2).attr('data-order',data.pesototalnorma);
                $('td', row).eq(2).html(MASKLA(data.pesototalnorma,2));

                $('td', row).eq(3).attr('style','text-align:right');
                $('td', row).eq(3).attr('data-search',data.difkg);
                $('td', row).eq(3).attr('data-order',data.difkg);
                $('td', row).eq(3).html(MASKLA(data.difkg,2));

                aux_var = (data.difkg / data.pesototalnorma) * 100;
                $('td', row).eq(4).attr('style','text-align:right');
                $('td', row).eq(4).attr('data-search',aux_var);
                $('td', row).eq(4).attr('data-order',aux_var);
                $('td', row).eq(4).html(MASKLA(aux_var.toFixed(5),5));
            }
        });
    }

    totalizar();

    $("#btnconsultar").click(function()
    {
        data = datospesaje(0);
        $("#Total").html("");
        $("#TotalPeriodo").html("");
        $(aux_nombretablarep).DataTable().ajax.url( "reportpesajegrupopage/" + data.data2 ).load();
        totalizar();
    });

});

function totalizar(){
    let  table = $('#tabla-data-reporte-pesaje').DataTable();
    //console.log(table);
    table
        .on('draw', function () {
            eventFired( 'Page' );
        });
    data = datospesaje(1);
    $.ajax({
        url: '/reportpesajegrupo/totalizarindex/' + data.data2,
        type: 'GET',
        success: function (datos) {
            aux_subtotalVar = 0;
            if(datos.subtotalPesoTotalNorma > 0){
                aux_subtotalVar = (datos.subtotalDifKg / datos.subtotalPesoTotalNorma) * 100
            }
            $("#subtotalTara").html(MASKLA(datos.subtotalTara,2));
            $("#subtotalPesoBal").html(MASKLA(datos.subtotalPesoBal,2));
            $("#subtotalPesoTotalProdBal").html(MASKLA(datos.subtotalPesoTotalProdBal,2));
            $("#subtotalPesoTotalNorma").html(MASKLA(datos.subtotalPesoTotalNorma,2));
            $("#subtotalDifKg").html(MASKLA(datos.subtotalDifKg,2));
            $("#subtotalVar").html(MASKLA(aux_subtotalVar.toFixed(5),5));
            //$("#Total").html("Total: " + $("#fechah").val())
        }
    });
}

var eventFired = function ( type ) {
	total = 0;
	$("#tabla-data-reporte-pesaje tr .subtotalkg").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#subtotalkg").html(MASKLA(total,2))
}

function datospesaje(aux_statusSumPeriodo){
    var data1 = {
        statusSumPeriodo  : aux_statusSumPeriodo,
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        mesanno           : $("#annomes").val(),
        sucursal_id       : $("#sucursal_id").val(),
        producto_id       : $("#producto_idPxP").val(),
        categoriaprod_id  : $("#categoriaprod_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        tipobodega        : $("#tipobodega").val(),
        agrurep_id        : $("#agrurep_id").val(),
        _token            : $("input[name=_token]").val()
    };

    var data2 = "?mesanno="+data1.mesanno +
    "&statusSumPeriodo="+data1.statusSumPeriodo +
    "&fechad="+data1.fechad +
    "&fechah="+data1.fechah +
    "&sucursal_id="+data1.sucursal_id +
    "&producto_id="+data1.producto_id +
    "&categoriaprod_id="+data1.categoriaprod_id +
    "&areaproduccion_id="+data1.areaproduccion_id +
    "&tipobodega="+data1.tipobodega +
    "&agrurep_id="+data1.agrurep_id


    var data = {
        data1 : data1,
        data2 : data2
    };
    return data;
}

$("#btnbuscarproducto").click(function(event){
    $(this).val("");
    $(".input-sm").val('');
    data = datos();
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
    data = datospesaje(0);
    //alert(cadena);
    $('#contpdf').attr('src', '/reportpesajegrupo/exportPdf/'+data.data2);
    //$('#contpdf').attr('src', '/notaventa/'+id+'/'+stareport+'/exportPdf');
	$("#myModalpdf").modal('show')
});