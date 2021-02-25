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

    $("#btnconsultar").click(function()
    {
        consultar(datos());
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

    configurarTabla('.tablas');


});

function configurarTabla(aux_tabla){
    $(aux_tabla).DataTable({
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
}
/*
function configurarTabla(aux_tabla){
    $(aux_tabla).DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'dom'         : 'Bfrtip',
        'buttons'     : [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
    });    
}
*/

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
        _token            : $('input[name=_token]').val(),
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        matprimdesc       : $("#matprimdesc").val(),
        producto          : $("#producto").val()
    };
    return data;
}

function consultar(data){
    $("#graficos").hide();
    $.ajax({
        url: '/estadisticaventa/reporte',
        type: 'POST',
        data: data,
        success: function (datos) {
            if(datos['tabla'].length>0){
                //$("#tablaconsultaG").html('');
                $("#tablaconsulta").html(datos['tabla']);
                $("#tablaconsultaT").html(datos['tablaT']);
                configurarTabla('.tablascons');
                grafico(datos);
            }
        }
    });
}

function consultarpdf(data){
    $.ajax({
        url: '/estadisticaventa/exportPdf',
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
    $("#myModalBusqueda").modal('show');
});

function copiar_rut(id,rut){
	$("#myModalBusqueda").modal('hide');
	$("#rut").val(rut);
	//$("#rut").focus();
	$("#rut").blur();
}

function visto(id,visto){
    //alert($(this).attr("value"));
    var data = {
        id     : id,
        _token : $('input[name=_token]').val()
    };
    var ruta = '/notaventa/visto/' + id;
    ajaxRequest(data,ruta,'vistonotaventa');
}

function genreportepdf(){ //GENERAR REPORTE PDF NOTA DE VENTA
    reportepdf(datos());
/*
	$("#myModalpdf").modal('show')
	$('#contpdf').attr('src', 'notaventa/'+id+'/'+stareport+'/exportPdf');
*/
}

function reportepdf(data){
    //htmlexterno = '';
    $('#contpdf').attr('src', '/notaventaconsulta/exportPdf/'+data);
    $("#myModalpdf").modal('show');
    /*
    $('#contpdf').attr('src', function(e){
        $.ajax({
            url: '/notaventaconsulta/exportPdf',
            type: 'POST',
            data: data,
            success: function (datos) {
                $('#contpdf').attr('src', datos);
            }
        });
    });
    $("#myModalpdf").modal('show');
    */

}

function clicbotonactfileoc(id,oc_id){
    $('.fileinput-remove-button').click();
    $("#myModalactualizarFileOC").modal('show');
}

function grafico(datos){
    $("#graficos").show();
    $('.resultadosPie1').html('<canvas id="graficoPie1"></canvas>');
    var config1 = {
        type: 'pie',
        data: {
            datasets: [{
                data: datos['difvals'],
                backgroundColor: [
                    window.chartColors.blue,
                    window.chartColors.orange,
                    window.chartColors.red,
                    window.chartColors.purple,
                    window.chartColors.yellow,  
                    window.chartColors.blue,
                    window.chartColors.orange,
                    window.chartColors.red,
                    window.chartColors.purple,
                    window.chartColors.yellow,  
                ],
                label: 'Dataset 1'
            }],
            labels: datos['matprimdesc']
        },
        options: {
            responsive: true
        }
    };

    var ctxPie1 = document.getElementById('graficoPie1').getContext('2d');
    window.myPie1 = new Chart(ctxPie1, config1);
    myPie1.clear();

    $("#tituloPie1").html("Ingreso X Materia Prima");
	$("#graficos").show();

}

$(".detalle-venta").click(function(event){
    event.preventDefault();
    alert('$(this).html()');
    /*
    const url = $(this).attr('href');
    const data = {
        _token: $('input[name=_token]').val()
    }
    ajaxRequest(data,url,'verUsuario');*/
    //$("#myModal").modal('show');
});

function consultardetalle(matprimdesc){
    event.preventDefault();
    detalleventa(datosdetalle(matprimdesc));

}

function detalleventa(data){
    $.ajax({
        url: '/estadisticaventa/reporte',
        type: 'POST',
        data: data,
        success: function (datos) {
            if(datos['tabla'].length>0){
                //$("#tablaconsultaG").html('');
                $("#tablaconsulta").html(datos['tabla']);
                $("#tablaconsultaT").html(datos['tablaT']);
                
                grafico(datos);


                $("#tabladetalleventa").html(datos['tablaNCorto']);
                //$("#tablaconsulta").html(datos['tabla']);
                //configurarTabla('#tabladespachoorddet');
                configurarTabla('.tablascons');
    
                $("#myDetalleVenta").modal('show');
    


            }
        }
    }); 
}

function datosdetalle(matprimdesc){
    var data = {
        _token            : $('input[name=_token]').val(),
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        matprimdesc       : matprimdesc,
        producto          : ''
    };
    return data;
}