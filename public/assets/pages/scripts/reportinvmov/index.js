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
        consultarpage(datos());
    });


    $("#btnpdf1").click(function()
    {
        consultarpdf(datos());
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

function datos(){
    var data = {
        annomes           : $("#annomes").val(),
        sucursal_id       : $("#sucursal_id").val(),
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        producto_idPxP    : $("#producto_idPxP").val(),
        invbodega_id      : $("#invbodega_id").val(),
        _token            : $('input[name=_token]').val()
    };
    return data;
}

function consultarpage(data){


    aux_titulo = "";
    cadena = "?annomes="+data.annomes +
            "&sucursal_id="+data.sucursal_id +
            "&fechad="+data.fechad +
            "&fechah="+data.fechah +
            "&areaproduccion_id="+data.areaproduccion_id +
            "&producto_idPxP="+data.producto_idPxP +
            "&invbodega_id="+data.invbodega_id

    $.ajax({
        url: '/reportinvmov/totalizarRep/' + cadena,
        type: 'GET',
        success: function (datos) {
            console.log(datos);
            $("#totalcant").html(MASKLA(datos.aux_totalcant,2));
            //$("#totaldinero").html(MASKLA(datos.aux_totaldinero,0));
        }
    });
        

    $("#tabla-data-consulta").dataTable().fnDestroy();
    //return 0;
    $('#tabla-data-consulta').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "/reportinvmov/reporte/" + cadena,
        'order': [[ 0, "asc" ]],
        'columns'     : [
            {data: 'id'},
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
            $('td', row).eq(0).attr('data-search',data.id);

            $('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));

            $('td', row).eq(7).attr('data-order',data.cant);
            $('td', row).eq(7).attr('style','text-align:right');
            aux_text = MASKLA(data.cant,2);
            $('td', row).eq(7).html(aux_text);
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

$("#btnpdf").click(function()
{
    var data = datos();
    $.ajax({
        url: '/indicadores/imagengrafico',
        type: 'POST',
        data: data,
        success: function (respuesta) {
            aux_titulo = "Orden Despacho";
            data = datos();
            cadena = "?id=" +
                    "&fechad="+data.fechad+"&fechah="+data.fechah +
                    "&fechadfac="+data.fechadfac+"&fechahfac="+data.fechahfac +
                    "&fechaestdesp="+data.fechaestdesp +
                    "&rut="+data.rut +
                    "&oc_id="+data.oc_id +
                    "&vendedor_id=" + data.vendedor_id+"&giro_id="+data.giro_id + 
                    "&tipoentrega_id="+data.tipoentrega_id +
                    "&notaventa_id="+data.notaventa_id +
                    "&statusOD=" + data.statusOD +
                    "&areaproduccion_id="+data.areaproduccion_id +
                    "&comuna_id="+data.comuna_id +
                    "&aux_titulo="+aux_titulo +
                    "&guiadespacho="+data.guiadespacho +
                    "&numfactura="+data.numfactura +
                    "&despachosol_id="+data.despachosol_id +
                    "&despachoord_id="+data.despachoord_id +
                    "&aux_verestado="+data.aux_verestado
            $('#contpdf').attr('src', '/reportorddespguiafact/exportPdf/'+cadena);
            $("#myModalpdf").modal('show');
        },
        error: function () {
        }
    });
    
});

$("#btnbuscarproducto").click(function(event){
    //$(this).val("");
    $(".input-sm").val('');
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
    $("#myModalBuscarProd").modal('show');
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