$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    $('.datepicker').datepicker({
		language: "es",
        autoclose: true,
        clearBtn : true,
		todayHighlight: true
    }).datepicker("setDate");
    $("#rut").focus(function(){
        eliminarFormatoRut($(this));
    });
    $("#btnconsultarpage").click(function()
    {
        consultarpage(datosOrdDespPrec());
    });
    $("#btnpdf").click(function()
    {
        btnpdf(datosOrdDespPrec());
    });

});

function datosOrdDespPrec(){
    var data1 = {
        id                : $("#id").val(),
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        rut               : eliminarFormatoRutret($("#rut").val()),
        vendedor_id       : $("#vendedor_id").val(),
        oc_id             : $("#oc_id").val(),
        giro_id           : $("#giro_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        tipoentrega_id    : $("#tipoentrega_id").val(),
        notaventa_id      : $("#notaventa_id").val(),
        comuna_id         : $("#comuna_id").val(),
        despachosol_id    : $("#despachosol_id").val(),
        despachoord_id    : $("#despachoord_id").val(),
        guiadespacho      : $("#guiadespacho").val(),
        numfactura        : $("#numfactura").val(),
        aprobstatus       : $("#aprobstatus").val(),
        _token            : $('input[name=_token]').val()
    };
    aux_titulo = "";
    var data2 = "?id="+data1.id +
            "&fechad="+data1.fechad +
            "&fechah="+data1.fechah +
            "&rut="+data1.rut +
            "&vendedor_id=" + data1.vendedor_id + 
            "&notaventa_id="+data1.notaventa_id +
            "&oc_id="+data1.oc_id +
            "&despachosol_id="+data1.despachosol_id +
            "&despachoord_id="+data1.despachoord_id +
            "&areaproduccion_id="+data1.areaproduccion_id +
            "&tipoentrega_id="+data1.tipoentrega_id +
            "&giro_id="+data1.giro_id +            
            "&comuna_id="+data1.comuna_id +
            "&aux_titulo="+aux_titulo +
            "&guiadespacho="+data1.guiadespacho +
            "&numfactura="+data1.numfactura +
            "&aprobstatus="+data1.aprobstatus
    var data = {
        data1 : data1,
        data2 : data2
    };
    return data;
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
                configurarTabla();
            }
            */
        }
    });
}

$("#rut").blur(function(){
	codigo = $("#rut").val();
	aux_sta = $("#aux_sta").val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		//totalizar();
		if(!dgv(codigo.substr(0, codigo.length-1))){
			swal({
				title: 'Dígito verificador no es Válido.',
				text: "",
				icon: 'error',
				buttons: {
					confirm: "Aceptar"
				},
			}).then((value) => {
				if (value) {
					//ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
					$("#rut").focus();
				}
			});
			//$(this).val('');
		}else{
			var data = {
				rut: $("#rut").val(),
				_token: $('input[name=_token]').val()
			};
			$.ajax({
				url: '/cliente/buscarCli',
				type: 'POST',
				data: data,
				success: function (respuesta) {
					if(respuesta.length>0){
						formato_rut($("#rut"));
					}else{
                        formato_rut($("#rut"));
                        swal({
                            title: 'Cliente no existe.',
                            text: "Aceptar para crear cliente temporal",
                            icon: 'error',
                            buttons: {
                                confirm: "Aceptar",
                                cancel: "Cancelar"
                            },
                        }).then((value) => {
                            if (value) {
                                limpiarclientemp();
                                
                                $("#myModalClienteTemp").modal('show');
                            }else{
                                $("#rut").focus();
                                //$("#rut").val('');
                            }
                        });		
					}
				}
			});
		}
	}
});

$("#btnbuscarcliente").click(function(event){
    $("#rut").val("");
    $(".input-sm").val('');
    $("#myModalBusqueda").modal('show');
});
function copiar_rut(id,rut){
	$("#myModalBusqueda").modal('hide');
	$("#rut").val(rut);
	//$("#rut").focus();
	$("#rut").blur();
}


function consultarpage(data){
    $("#tabla-data-consulta").attr('style','')
    $("#tabla-data-consulta").dataTable().fnDestroy();
    $('#tabla-data-consulta').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "/reportorddesprec/reporte/" + data.data2,
        'order': [[ 0, "desc" ]],
        'columns'     : [
            {data: 'id'},
            {data: 'documento_id'},
            {data: 'fechahora'},
            {data: 'razonsocial'},
            {data: 'notaventa_id'},
            {data: 'despachosol_id'},
            {data: 'despachoord_id'},
            {data: 'oc_id'},
            {data: 'comunanombre'},
            {data: 'totalkilos'},
            {data: 'subtotal'},
            {data: 'guiadespacho'},
            {data: 'guiadespachofec'},
            {data: 'numfactura'},
            {data: 'fechafactura'},
            {data: 'recmotivonombre'},
            {data: 'oc_file',className:"ocultar"},
            {data: 'anulada',className:"ocultar"},
            {data: 'sta_anulada',className:"ocultar"},
            {data: 'documento_file',className:"ocultar"},
            {data: 'aprobstatus',className:"ocultar"},
            {data: 'aprobobs',className:"ocultar"}
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Rechazo Orden Despacho' onclick='genpdfODRec(" + data.id + ",1)'>"+
                    data.id +
                "</a>";
            if(data.sta_anulada == 'A'){
                aux_fecha = new Date(data.fechahora);
                aux_text = aux_text +
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Anulada:" + fechaddmmaaaa(aux_fecha) + "'>" +
                    "<span class='glyphicon glyphicon-remove text-danger'></span>" +
                "</a>";
            }

            if(data.aprobstatus == 1){
                aux_text = aux_text +
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='" + data.aprobobs + "'>" +
                    "<span class='glyphicon glyphicon-arrow-right'></span>" +
                "</a>";
            }
            if(data.aprobstatus == 2){
                if(data.aprobobs == null){
                    data.aprobobs = "";
                }
                aux_text = aux_text +
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Aprobado: " + data.aprobobs + "'>" +
                    "<span class='glyphicon glyphicon-thumbs-up'></span>" +
                "</a>";
            }
            if(data.aprobstatus == 3){
                aux_text = aux_text +
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Rechazada: " + data.aprobobs + "'>" +
                    "<span class='glyphicon glyphicon-thumbs-down text-danger'></span>" +
                "</a>";
            }

            $('td', row).eq(0).html(aux_text);
            $('td', row).eq(0).attr('data-order',data.id);

/*
            if($('td', row).eq(1).html()=='A'){
                aux_fecha = new Date(data.fechahora);
                aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Anulada:" + fechaddmmaaaa(aux_fecha) + "'>" +
                    "<span class='glyphicon glyphicon-remove text-danger'></span>" +
                "</a>";
                $('td', row).eq(1).html(aux_text);
            }
*/

            codigo = data.documento_file;
            if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo) ){
                aux_texto = "";
            }else{
                aux_texto = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Documento de Rechazo' onclick='verdocadj(\"" + data.documento_file + "\",\"despachorechazo\")'>" +
                                data.documento_id +
                            "</a>";
            }
            $('td', row).eq(1).html(aux_texto);

            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV(" + data.notaventa_id + ",1)'>" +
                    + data.notaventa_id +
                "</a>";
            $('td', row).eq(4).html(aux_text);
            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Solicitud de Despacho' onclick='genpdfSD(" + data.despachosol_id + ",1)'>" +
                    data.despachosol_id +
                "</a>";
            $('td', row).eq(5).html(aux_text);
            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Despacho' onclick='genpdfOD(" + data.despachoord_id + ",1)'>" +
                    data.despachoord_id +
                "</a>";
            $('td', row).eq(6).html(aux_text);

            if(data.oc_file != "" && data.oc_file != null){
                aux_text = 
                    "<a class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Compra' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + 
                        data.oc_id + 
                    "</a>";
                $('td', row).eq(7).html(aux_text);
            }

            $('td', row).eq(2).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(2).html(fechaddmmaaaa(aux_fecha));
           
            if(data.guiadespachofec != "" && data.guiadespachofec != null){
                $('td', row).eq(12).attr('data-order',data.guiadespachofec);
                aux_fecha = new Date(data.guiadespachofec);
                $('td', row).eq(12).html(fechaddmmaaaa(aux_fecha));    
            }

            if(data.fechafactura != "" && data.fechafactura != null){
                $('td', row).eq(14).attr('data-order',data.fechafactura);
                aux_fecha = new Date(data.fechafactura);
                $('td', row).eq(14).html(fechaddmmaaaa(aux_fecha));
            }

            $('td', row).eq(9).attr('data-order',data.totalkilos);
            $('td', row).eq(9).attr('style','text-align:right');
            $('td', row).eq(9).addClass('kg');
            $('td', row).eq(10).attr('data-order',data.subtotal);
            $('td', row).eq(10).attr('style','text-align:right');
            $('td', row).eq(10).addClass('subtotal');

            aux_text = MASKLA(data.totalkilos,2);
            $('td', row).eq(9).html(aux_text);
            aux_text = MASKLA(data.subtotal,0);
            $('td', row).eq(10).html(aux_text);
            $('td', row).eq(11).attr('style','text-align:center');
            $('td', row).eq(13).attr('style','text-align:center');
        }
    });

    let  table = $('#tabla-data-consulta').DataTable();
    //console.log(table);
    table
        .on('draw', function () {
            eventFired( 'Page' );
        });

    $.ajax({
        url: '/reportorddesprec/totalizarRep' + data.data2,
        type: 'GET',
        data: data,
        success: function (datos) {
            //console.log(datos);
            $("#totalkg").html(MASKLA(datos.aux_totalkg,2));
            $("#totaldinero").html(MASKLA(datos.aux_totaldinero,0));
        }
    });

}

function btnpdf(data){
    $('#contpdf').attr('src', '/reportorddesprec/exportPdf/'+data.data2);
    $("#myModalpdf").modal('show');
}

var eventFired = function ( type ) {
	total = 0;
	$("#tabla-data-consulta tr .kg").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#subtotalkg").html(MASKLA(total,2))
	total = 0;
	$("#tabla-data-consulta tr .subtotal").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#subtotaldinero").html(MASKLA(total,0))

}
