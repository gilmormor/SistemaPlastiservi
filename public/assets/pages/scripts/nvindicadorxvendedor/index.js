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
    $('.col-xs-12').css({'margin-bottom':'0px','margin-left': '0px','margin-right': '0px','padding-left': '5px','padding-right': '5px'});
    $("#btnconsultar").click(function()
    {
        aux_consultaid = $("#consulta_id").val();
        consultar(datos(aux_consultaid));
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
    

    configurarTabla('.tablas');

    $("#areaproduccion_id").val('1'); 

});

function datos(idcons){
    var data = {
        fechad: $("#fechad").val(),
        fechah: $("#fechah").val(),
        vendedor_id: JSON.stringify($("#vendedor_id").val()),
        giro_id: $("#giro_id").val(),
        categoriaprod_id: $("#categoriaprod_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        idcons : idcons,
        statusact_id : $("#statusact_id").val(),
        _token: $('input[name=_token]').val()
    };
    return data;
}

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
		},
		error: function () {
		}
	});
}

function consultar(data){
    $("#graficos").hide();
    $.ajax({
        url: '/nvindicadorxvend/reporte',
        type: 'POST',
        data: data,
        success: function (datos) {
            if(datos['tabla'].length>0){
                aux_titulo = $("#consulta_id option:selected").html();
                $("#titulo_grafico").html('Indicadores ' +aux_titulo+ ' por Vendedor');
                $("#titulo_grafico1").html($("#titulo_grafico").html());
                $("#titulo_grafico2").html('Indicadores ' +aux_titulo);
                $("#tablaconsulta").html(datos['tabla']);
                $("#tablaconsultadinero").html(datos['tabladinero']);
                $("#tablaconsultaproducto").html(datos['tablaagruxproducto']);
                $("#tituloPie1").html("Gráfico " + aux_titulo+ " por Vendedor");
                $("#tituloPie2").html($("#tituloPie1").html() + " $");
            
                configurarTabla('.tablascons');
                grafico(datos);
            }
        }
    });
}

function consultarpdf(data){
    $.ajax({
        url: '/nvindicadorxvend/exportPdf',
        type: 'GET',
        data: data,
        success: function (datos) {
            //$("#midiv").html(datos);
            /*
            if(datos['tabla'].length>0){
                $("#tablaconsulta").html(datos['tabla']);
                configurarTabla();
            }
            */
        }
    });
}


function grafico(datos){
    $("#graficos").show();
    $("#graficos1").show();
    $("#graficos2").show();
    $("#reporte1").show();
    $('.resultadosPie1').html('<canvas id="graficoPie1"></canvas>');
    $('.resultadosPie2').html('<canvas id="graficoPie2"></canvas>');
    var config1 = {
        type: 'pie',
        data: {
            datasets: [{
                data: datos['totalkilos'],
                backgroundColor: [
                    window.chartColors.blue,
                    window.chartColors.orange,
                    window.chartColors.red,
                    window.chartColors.purple,
                    window.chartColors.yellow,  
                ],
                label: 'Dataset 1'
            }],
            labels: datos['nombre']
        },
        options: {
            responsive: true
        }
    };

    var ctxPie1 = document.getElementById('graficoPie1').getContext('2d');
    window.myPie1 = new Chart(ctxPie1, config1);
    myPie1.clear();

    var config2 = {
        type: 'pie',
        data: {
            datasets: [{
                data: datos['totaldinero'],
                backgroundColor: [
                    window.chartColors.blue,
                    window.chartColors.orange,
                    window.chartColors.red,
                    window.chartColors.purple,
                    window.chartColors.yellow,  
                ],
                label: 'Dataset 1'
            }],
            labels: datos['nombre']
        },
        options: {
            responsive: true
        }
    };

    var ctxPie2 = document.getElementById('graficoPie2').getContext('2d');
    window.myPie2 = new Chart(ctxPie2, config2);
    myPie2.clear();
	$("#graficos").show();
	$("#graficos1").show();
	$("#graficos2").show();
    $("#reporte1").show();
}

$("#btnpdf").click(function(event){
    aux_consultaid = $("#consulta_id").val();
    consultar(datos(aux_consultaid));
    aux_titulo = 'Indicadores ' + $("#consulta_id option:selected").html();
    aux_consultaid = $("#consulta_id").val()
    data = datos(aux_consultaid);
    cadena = "?fechad="+data.fechad+"&fechah="+data.fechah +
            "&vendedor_id=" + data.vendedor_id+"&giro_id="+data.giro_id + 
            "&categoriaprod_id=" + data.categoriaprod_id +
            "&areaproduccion_id="+data.areaproduccion_id +
            "&idcons="+data.idcons + "&statusact_id="+data.statusact_id +
            "&aux_titulo="+aux_titulo
    $('#contpdf').attr('src', '/nvindicadorxvend/exportPdfkg/'+cadena);
	$("#myModalpdf").modal('show');
});

function btnpdfKg(){
    aux_titulo = 'Indicadores ' + $("#consulta_id option:selected").html();
    aux_consultaid = $("#consulta_id").val()
    data = datos(aux_consultaid);
    cadena = "?fechad="+data.fechad+"&fechah="+data.fechah +
            "&vendedor_id=" + data.vendedor_id+"&giro_id="+data.giro_id + 
            "&categoriaprod_id=" + data.categoriaprod_id +
            "&areaproduccion_id="+data.areaproduccion_id +
            "&idcons="+data.idcons + "&statusact_id="+data.statusact_id +
            "&aux_titulo="+aux_titulo
    $('#contpdf').attr('src', '/nvindicadorxvend/exportPdfkg/'+cadena);
	$("#myModalpdf").modal('show');
}