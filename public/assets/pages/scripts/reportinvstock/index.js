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

    configurarTabla('#tabla-data-invstock');

    function configurarTabla(aux_tabla){
        data = datosinvstock();
        $(aux_tabla).DataTable({
            'paging'      : true, 
            'lengthChange': true,
            'ordering'    : true,
            'info'        : true,
            'autoWidth'   : false,
            'processing'  : true,
            'serverSide'  : true,
            'ajax'        : "reportinvstockpage/" + data.data2, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
            "order": [[ 1, "asc" ]],
            'columns'     : [
                {data: 'producto_id'},
                {data: 'producto_nombre'},
                {data: 'categoria_nombre'},
                {data: 'cla_nombre'},
                {data: 'diametro'},
                {data: 'long'},
                {data: 'peso'},
                {data: 'tipounion'},
                {data: 'invbodega_nombre'},
                {data: 'stockini'},
                {data: 'mov_in'},
                {data: 'mov_out'},
                {data: 'stock'},
                {data: 'stockkg'}
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "createdRow": function ( row, data, index ) {
                /*
                if(data.stock <= 0){
                    $(row).hide();                    
                }*/
                $('td', row).eq(0).attr('style','text-align:center');
                $('td', row).eq(3).attr('style','text-align:center');

                $('td', row).eq(4).attr('style','text-align:center');
                $('td', row).eq(5).attr('style','text-align:center');
                $('td', row).eq(6).attr('style','text-align:center');
                if(data.acuerdotecnico_id){
                    $('td', row).eq(4).html(NUM(data.at_ancho, 2));
                    $('td', row).eq(5).html(NUM(data.at_largo, 2));
                    $('td', row).eq(6).html(MASKLA(data.at_espesor, 3));    
                }else{
                    $('td', row).eq(6).html(NUM(data.peso, 2));
                }
                $('td', row).eq(9).attr('style','text-align:center');
                $('td', row).eq(10).attr('style','text-align:center');
                $('td', row).eq(11).attr('style','text-align:center');
                $('td', row).eq(12).attr('style','text-align:right');
                $('td', row).eq(12).attr('data-order',data.stock);
                $('td', row).eq(12).attr('data-search',data.stock);
                $('td', row).eq(12).html(MASKLA(data.stock,0));
                $('td', row).eq(12).addClass('subtotalstock');

                $('td', row).eq(13).attr('style','text-align:right');
                //$('td', row).eq(13).html(MASK(0, data.stockkg, '-###,###,###,##0.00',1));
                if(data.peso <= 0){
                    stockKg = data.stockkg;
                }else{
                    stockKg = data.stock * data.peso;
                }
                $('td', row).eq(13).attr('data-order',stockKg);
                $('td', row).eq(13).attr('data-search',stockKg);
                $('td', row).eq(13).html(MASKLA(stockKg,2));
                $('td', row).eq(13).addClass('subtotalkg');
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
            }
        });
    }

    totalizar();

    $("#btnconsultar").click(function()
    {
        data = datosinvstock();
        $('#tabla-data-invstock').DataTable().ajax.url( "invcontrolpage/" + data.data2 ).load();
        totalizar();
    });

    arrayBodegas = [];
    $("#invbodega_id option").each(function(){
        //console.log(this);
        //console.log('Opcion: '+$(this).text()+' Valor: '+ $(this).attr('value')+' Sucursal: '+ $(this).attr('sucursal_id'));
        var objeto =   {
            id: $(this).attr('value'),
            nombre: $(this).text(),
            sucursal_id: $(this).attr('sucursal_id')
        };
        arrayBodegas.push(objeto);
    });
    //Lo agregas al array.
    $("#invbodega_id").empty();
    $(".selectpicker").selectpicker('refresh');
    if($("#sucursal_id").val() > 0){
		llenarbodegas($("#sucursal_id").val())
	}
/* //Santa Ester
        $('#tabla-data-consulta').DataTable().ajax.url( "invcontrolpage/" + data.data2 ).load();
        totalizar();
    });

    tablascolsultainv($("#sucursal_id").val());
*/

});

function totalizar(){
    let  table = $('#tabla-data-invstock').DataTable();
    //console.log(table);
    table
        .on('draw', function () {
            eventFired( 'Page' );
        });
    data = datosinvstock();
    $.ajax({
        url: '/reportinvstock/totalizarindex/' + data.data2,
        type: 'GET',
        success: function (datos) {
            $("#totalstock").html(MASKLA(datos.aux_totalstock,0));
            $("#totalkg").html(MASKLA(datos.aux_totalkg,2));
            //$("#totaldinero").html(MASKLA(datos.aux_totaldinero,0));
        }
    });
}

var eventFired = function ( type ) {
    subtotalstock = 0
    $("#tabla-data-invstock tr .subtotalstock").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		subtotalstock += valorNum;
	});
    $("#subtotalstock").html(MASKLA(subtotalstock,0))

	total = 0;
	$("#tabla-data-invstock tr .subtotalkg").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#subtotalkg").html(MASKLA(total,2))
}

function datosinvstock(){
    var data1 = {
        mesanno           : $("#annomes").val(),
        sucursal_id       : $("#sucursal_id").val(),
        invbodega_id      : $("#invbodega_id").val(),
        producto_id       : $("#producto_idPxP").val(),
        categoriaprod_id  : $("#categoriaprod_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        _token            : $('input[name=_token]').val()
    };

    var data2 = "?mesanno="+data1.mesanno +
    "&sucursal_id="+data1.sucursal_id +
    "&invbodega_id="+data1.invbodega_id +
    "&producto_id="+data1.producto_id +
    "&categoriaprod_id="+data1.categoriaprod_id +
    "&areaproduccion_id="+data1.areaproduccion_id


    var data = {
        data1 : data1,
        data2 : data2
    };
    return data;
}

$("#btnbuscarproducto").click(function(event){
    $(this).val("");
    $(".input-sm").val('');
    data = datosinvstock();
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
    data = datosinvstock();
    //alert(cadena);
    $('#contpdf').attr('src', '/reportinvstock/exportPdf/'+data.data2);
    //$('#contpdf').attr('src', '/notaventa/'+id+'/'+stareport+'/exportPdf');
	$("#myModalpdf").modal('show')
});

$("#sucursal_id").change(function(){
	id = $(this).val();
	llenarbodegas(id)
});

function llenarbodegas(sucursal_id){
    $("#invbodega_id").empty();
    for (let i = 0; i < arrayBodegas.length; i++) {
        if(sucursal_id == arrayBodegas[i].sucursal_id){
            $("#invbodega_id").append(`<option value="${arrayBodegas[i].id}" sucursal_id="${arrayBodegas[i].sucursal_id}">${arrayBodegas[i].nombre}</option>`)
        }
    }
    $(".selectpicker").selectpicker('refresh');
/*  //Llenar Bodegas con ajax Santa Ester, lo cambie por llenarlas en javascript
    //Me traigo las bodegas previamente de php
    tablascolsultainv($("#sucursal_id").val());
*/
}
//Esta funcion era para llenar en tiempo real el select de bodegas
///Ahora lo hago desde phph y en JS recorreo el arreglo de bodegas
function tablascolsultainv(id){
    $("#invbodega_id").empty();
    $("#categoriaprod_id").empty();
    if((id == "" || id == "0" || id == "x") == false){
        var data = {
            id: id,
            _token: $('input[name=_token]').val()
        };
        //console.log(data);
        
        $.ajax({
            url: '/sucursal/tablascolsultainv',
            type: 'POST',
            data: data,
            success: function (respuesta) {
                $.each(respuesta.invbodegas, function(index,value){
                    $("#invbodega_id").append("<option value='" + value.id + "'>" + value.nombre + "</option>")
                });
                $.each(respuesta.categoria, function(index,value){
                    $("#categoriaprod_id").append("<option value='" + value.id + "'>" + value.nombre + "</option>")
                });

                $(".selectpicker").selectpicker('refresh');
            }
        });    
    }else{
        $(".selectpicker").selectpicker('refresh');
    }
}