$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

/*
    $('.tablas').DataTable({
		'paging'      : true, 
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		'autoWidth'   : false,
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
	});
*/

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
        consultarpage(datos());
    });

    $("#btnpdf").click(function()
    {
        btnpdf(datos());
    });

});

function datos(){
    var data1 = {
        id                : $("#id").val(),
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        fechaestdesp      : $("#fechaestdesp").val(),
        rut               : eliminarFormatoRutret($("#rut").val()),
        vendedor_id       : $("#vendedor_id").val(),
        oc_id             : $("#oc_id").val(),
        giro_id           : $("#giro_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        tipoentrega_id    : $("#tipoentrega_id").val(),
        notaventa_id      : $("#notaventa_id").val(),
        aprobstatus       : $("#aprobstatus").val(),
        comuna_id         : $("#comuna_id").val(),
        despachosol_id    : $("#despachosol_id").val(),
        _token            : $('input[name=_token]').val()
    };

    var data2 = "?id="+data1.id +
    "&fechad="+data1.fechad +
    "&fechah="+data1.fechah +
    "&fechaestdesp="+data1.fechaestdesp +
    "&rut="+data1.rut +
    "&vendedor_id=" + data1.vendedor_id + 
    "&oc_id="+data1.oc_id +
    "&giro_id="+data1.giro_id +            
    "&areaproduccion_id="+data1.areaproduccion_id +
    "&tipoentrega_id="+data1.tipoentrega_id +
    "&notaventa_id="+data1.notaventa_id +
    "&aprobstatus="+data1.aprobstatus +
    "&comuna_id="+data1.comuna_id +
    "&despachosol_id="+data1.despachosol_id

    var data = {
        data1 : data1,
        data2 : data2
    };
    return data;
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
    $("#myModalBusqueda").modal('show');
});

function copiar_rut(id,rut){
	$("#myModalBusqueda").modal('hide');
	$("#rut").val(rut);
	//$("#rut").focus();
	$("#rut").blur();
}

function btnpdf(data){
    //console.log(data);
    //alert('entro');
    $('#contpdf').attr('src', '/reportorddesp/exportPdf/'+data.data2);
    $("#myModalpdf").modal('show');
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
        'ajax'        : "/reportorddesp/reporte/" + data.data2,
        'order': [[ 0, "desc" ]],
        'columns'     : [
            {data: 'id'},
            {data: 'fechahora'},
            {data: 'fechaestdesp'},
            {data: 'razonsocial'},
            {data: 'despachoord_id'},
            {data: 'despachosol_id'},
            {data: 'oc_id'},
            {data: 'notaventa_id'},
            {data: 'comunanombre'},
            {data: 'totalkilos'},
            {data: 'tipentnombre'},
            {data: 'aprguiadesp'},
            {data: 'oc_file',className:"ocultar"},
            {data: 'icono',className:"ocultar"},
            {data: 'aprguiadespfh',className:"ocultar"},
            {data: 'despachoordrec_id',className:"ocultar"}
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            if(data.despachoordanul_fechahora){
                aux_fecha = new Date(data.despachoordanul_fechahora);
                aux_text =  "<a class='btn-accion-tabla tooltipsC' title='Anulada: " + fechaddmmaaaa(aux_fecha) + "'>" +
                                "<small class='label label-danger'>A</small>" +
                            "</a>";
                $('td', row).eq(0).html(data.id + " " +aux_text);    
            }
            
            $('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));

            $('td', row).eq(2).attr('data-order',data.fechaestdesp);
            aux_fecha = new Date(data.fechaestdesp);
            $('td', row).eq(2).html(fechaddmmaaaa(aux_fecha));

            aprguiadesp = "<i class='glyphicon glyphicon-floppy-save text-warning tooltipsC' title='Pendiente Aprobar'></i>";
            imprOrdDesp = "";
            if(data.aprguiadesp == 1){
                aux_fecha = new Date(data.aprguiadespfh);
                fechaaprob = fechaddmmaaaa(aux_fecha);
                aprguiadesp = "<i class='glyphicon glyphicon-floppy-save text-primary tooltipsC' title='Fecha: " + fechaaprob + "'></i>";
                imprOrdDesp =   "<a class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Despacho' onclick='genpdfOD(" + data.id +",1)'>" +
                                    data.id +
                                "</a>";
            }

            aux_rechazos= "";
            aux_contrec = 0;
            if(data.despachoordrec_id){
                let str = data.despachoordrec_id;
                let arr = str.split(',');
                //console.log(arr); 
                aux_sep = "";
                $.each(arr, function (ind, elem) { 
                    if(ind>0){
                        aux_sep = ",";
                    }
                    aux_rechazos =  "(" + aux_rechazos + aux_sep + "<a class='btn-accion-tabla btn-sm tooltipsC' title='Rechazo OD' onclick='genpdfODRec(" + elem + ",1)'>" +
                                        elem +
                                    "</a>";
                    aux_contrec++;

                });
            }
            if(aux_contrec == 0){
                aux_rechazos = "";
            }else{
                aux_rechazos += ")";
            }

            aux_text = imprOrdDesp;
            $('td', row).eq(4).html(aux_text + aux_rechazos);

            aux_text = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Solicitud de Despacho' onclick='genpdfSD(" + data.despachosol_id + ",1)'>" +
                data.despachosol_id +
            "</a>";
            $('td', row).eq(5).html(aux_text);

            $('td', row).eq(6).html("");
            if(data.oc_file != "" && data.oc_file != null){
                aux_text = 
                    "<a class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Compra' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + 
                        data.oc_id + 
                    "</a>";
                $('td', row).eq(6).html(aux_text);
            }

            aux_text =  "<a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV(" + data.notaventa_id + ",1)'>" +
                            data.notaventa_id
                        "</a>";
            $('td', row).eq(7).html(aux_text);

            $('td', row).eq(9).attr('data-order',data.totalkilos);
            $('td', row).eq(9).attr('style','text-align:right');
            $('td', row).eq(9).addClass('kg');
            aux_text = MASKLA(data.totalkilos,2);
            $('td', row).eq(9).html(aux_text);

            aux_text =  "<i class='fa fa-fw " + data.icono + " tooltipsC' title='" + data.tipentnombre + "'></i>";
            $('td', row).eq(10).html(aux_text);

            aux_text = aprguiadesp;
            $('td', row).eq(11).html(aux_text);

        }
    });

    let  table = $('#tabla-data-consulta').DataTable();
    //console.log(table);
    table
        .on('draw', function () {
            eventFired( 'Page' );
        });

    $.ajax({
        url: '/reportorddesp/totalizarRep' + data.data2,
        type: 'GET',
        data: data,
        success: function (datos) {
            //console.log(datos);
            $("#totalkg").html(MASKLA(datos.aux_totalkg,2));
            $("#totaldinero").html(MASKLA(datos.aux_totaldinero,0));
        }
    });

}

var eventFired = function ( type ) {
	total = 0;
	$("#tabla-data-consulta tr .kg").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#subtotalkg").html(MASKLA(total,2))
}