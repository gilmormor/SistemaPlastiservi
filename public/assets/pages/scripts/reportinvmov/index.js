$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    $('.date-pickermes').datepicker({
        language: "es",
        format: "MM yyyy",
        viewMode: "years", 
        minViewMode: "months",
        autoclose: true,
		todayHighlight: true
    }).datepicker("setDate");


    $("#btnconsultarpage").click(function()
    {
        consultarpage(datosInvmov());
    });


    $("#btnpdf1").click(function()
    {
        consultarpdf(datosInvmov());
    });

    //alert(aux_nfila);
    $('.datepicker').datepicker({
		language: "es",
        autoclose: true,
        clearBtn : true,
		todayHighlight: true
    }).datepicker("setDate");
    
    $("#rut").focus(function(){
        eliminarFormatoRut($(this));
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


});



function ajaxRequest(data,url,funcion) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='aprobarcotvend'){
				if (respuesta.mensaje == "ok") {
					$("#fila"+data['nfila']).remove();
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
            }
            if(funcion=='vistonotaventa'){
				if (respuesta.mensaje == "ok") {
					//$("#fila"+data['nfila']).remove();
                    Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
                    
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
            }
		},
		error: function () {
		}
	});
}

function datosInvmov(){
    var data1 = {
        annomes           : $("#annomes").val(),
        sucursal_id       : $("#sucursal_id").val(),
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        producto_idPxP    : $("#producto_idPxP").val(),
        invbodega_id      : $("#invbodega_id").val(),
        _token            : $('input[name=_token]').val()
    };

    var data2 = "?annomes="+data1.annomes +
    "&sucursal_id="+data1.sucursal_id +
    "&fechad="+data1.fechad +
    "&fechah="+data1.fechah +
    "&areaproduccion_id="+data1.areaproduccion_id +
    "&producto_idPxP="+data1.producto_idPxP +
    "&invbodega_id="+data1.invbodega_id

    var data = {
        data1 : data1,
        data2 : data2
    };

    return data;
}

function consultarpage(data){
    $.ajax({
        url: '/reportinvmov/totalizarRep/' + data.data2,
        type: 'GET',
        success: function (datos) {
            console.log(datos);
            $("#totalcant").html(MASKLA(datos.aux_totalcant,2));
            //$("#totaldinero").html(MASKLA(datos.aux_totaldinero,0));
        }
    });
        

    $("#tabla-data-invmov").dataTable().fnDestroy();
    //return 0;
    $('#tabla-data-invmov').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "/reportinvmov/reporte/" + data.data2,
        'order': [[ 1, "asc" ]],
        'columns'     : [
            {data: 'id'},
            {data: 'invmovdet_id'},
            {data: 'fechahora'},
            {data: 'desc'},
            {data: 'producto_id'},
            {data: 'producto_nombre'},
            {data: 'invmovmodulo_nombre'},
            {data: 'invbodega_nombre'},
            {data: 'cant'}
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            /*
            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Despacho' onclick='genpdfOD(" + data.id + ",1)'>"+
                    data.id +
                "</a>";
            $('td', row).eq(0).html(aux_text);
            */
            aux_text = 
			"<a class='btn-accion-tabla btn-sm tooltipsC' title='Movimiento de Inv' onclick='genpdfINVMOV(" + data.id + ",1)'>"+
				data.id +
			"</a>";
			$('td', row).eq(0).html(aux_text);

            $('td', row).eq(0).attr('data-search',data.id);
            $('td', row).eq(1).attr('data-search',data.invmovdet_id);

            $('td', row).eq(2).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(2).html(fechaddmmaaaa(aux_fecha));

            $('td', row).eq(8).attr('data-order',data.cant);
            $('td', row).eq(8).attr('style','text-align:right');
            aux_text = MASKLA(data.cant,2);
            $('td', row).eq(8).html(aux_text);
        }
      });
}


function consultarpdf(data){
    $.ajax({
        url: '/notaventaconsulta/exportPdf',
        type: 'GET',
        data: data,
        success: function (datos) {
            $("#midiv").html(datos);
            /*
            if(datos['tabla'].length>0){
                $("#tablaconsulta").html(datos['tabla']);
            }
            */
        }
    });
}


$("#btnbuscarproducto").click(function(event){
    $(this).val("");
    $(".input-sm").val('');
    data = datosInvmov();
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
    data = datosInvmov();
    //alert(cadena);
    $('#contpdf').attr('src', '/reportinvmov/exportPdf/'+data.data2);
    //$('#contpdf').attr('src', '/notaventa/'+id+'/'+stareport+'/exportPdf');
	$("#myModalpdf").modal('show')
});

$("#sucursal_id").change(function(){
	id = $(this).val();
	llenarbodegas(id)
});

function llenarbodegas(sucursal_id){
    $("#invbodega_id").empty();
    console.log(arrayBodegas);
    for (let i = 0; i < arrayBodegas.length; i++) {
        if(sucursal_id == arrayBodegas[i].sucursal_id){
            $("#invbodega_id").append(`<option value="${arrayBodegas[i].id}" sucursal_id="${arrayBodegas[i].sucursal_id}">${arrayBodegas[i].nombre}</option>`)
        }
    }
    $(".selectpicker").selectpicker('refresh');
}