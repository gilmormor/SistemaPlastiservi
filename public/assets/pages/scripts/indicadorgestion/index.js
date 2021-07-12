$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#btnconsultar").click(function()
    {
        //$( "#tabs" ).tabs({ active: "tab_1" });
        //$('a[href="#tab_1"]').click();
        consultar(datos());
    });
/*
    $("#tab2").click(function(){
        //$(".hideshowdinero").css({'display':'none'});
        //$(".hideshowdinero").show();
        $(".hskilos").hide();
    });
*/
    $("#tab5").click(function(){
        //$(".hideshowdinero").css({'display':'none'});
        $(".hideshowdinero").hide()    
        $(".hskilos").show();
    });

    fecha = charToDate($("#fechah").val());
    $("#fechad").datepicker({
		language: "es",
        autoclose: true,
        clearBtn : true,
		todayHighlight: true,
        endDate: fecha
    }).datepicker("setDate");

    fecha = charToDate($("#fechad").val());
	$("#fechah").datepicker({
		language: "es",
		autoclose: true,
        clearBtn : true,
		startDate: fecha,
		todayHighlight: true
	}).datepicker("setDate");

    configurarTabla('.tablas');

    $("#areaproduccion_id").val('1');
    $('.date-picker').datepicker({
        language: "es",
        format: "yyyy",
        viewMode: "years", 
        minViewMode: "years",
        autoclose: true,
		todayHighlight: true
    }).datepicker("setDate");


});

function charToDate(fechachar){
    var arregloFecha = fechachar.split("/");
    var anio = arregloFecha[2];
    var mes = arregloFecha[1] - 1;
    var dia = arregloFecha[0];
    var fecha = new Date(anio, mes, dia); 
    return fecha;
}

$('#fechad').on('change', function () {
    getfecd = $('#fechad').datepicker("getDate");
    $("#fechah").datepicker("destroy");
    $("#fechah").datepicker({
		language: "es",
		autoclose: true,
        clearBtn : true,
		startDate: getfecd,
		todayHighlight: true,
    });
    $("#fechah").datepicker("refresh");
});


$('#fechah').on('change', function () {
    getfech = $('#fechah').datepicker("getDate");
    $("#fechad").datepicker("destroy");
    $("#fechad").datepicker({
		language: "es",
		autoclose: true,
        clearBtn : true,
		endDate: getfech,
		todayHighlight: true,
    });
    $("#fechad").datepicker("refresh");

});


function datos(){
    var data = {
        fechad: $("#fechad").val(),
        fechah: $("#fechah").val(),
        vendedor_id: JSON.stringify($("#vendedor_id").val()),
        giro_id: $("#giro_id").val(),
        categoriaprod_id: $("#categoriaprod_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        idcons : $("#consulta_id").val(),
        statusact_id : $("#statusact_id").val(),
        anno : $("#anno").val(),
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
    $("#grafbarra1").hide();
    $("#graficosAP1").hide();
    $("#margen").hide();
    
    $.ajax({
        url: '/indicadores/reportegestion',
        type: 'POST',
        data: data,
        success: function (datos) {
            if(datos['tabladinero'].length>0){
                aux_titulo = $("#consulta_id option:selected").html();
                $("#titulo_grafico1").html('Indicadores ' +aux_titulo+ ' por Vendedor');
                $("#tablaconsultadinero").html(datos['tabladinero']);
                $("#tablaconsultaproductomargen").html(datos['tablaagruxproductomargen']);
                //console.log(datos['tabladinero']);
                $("#tablaAP").html(datos['tablaareaproduccion']);


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
    grafico_pie2(datos);
    grafico_VentasMesxAreaProd(datos);
    $("#graficos1").show();
    $("#reporte1").show();
    $("#grafbarra1").show();
    $("#graficosAP1").show();
    $("#margen").show();
    $("#graficoVentasxMes").show();
    $("#graficoVentasMesAP").show();
    $('.resultadosPie2').html('<canvas id="graficoPie2" act="0"></canvas>');
    $('.resultadosBarra2').html('<canvas id="graficoBarra2" act="0"></canvas>');

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
            labels: datos['nombredinero']
        },
        options: {
            responsive: true,
            /*animation: {
                onComplete: function() {
                    //console.log(myPie2.toBase64Image());
                    generarPNGgraf(myPie2.toBase64Image(),"graficoPie2");
                }
            },*/
            title: {
                display: true,
                text: 'Venta por Vendedor $'
            }
        }
    };
    var ctxPie2 = document.getElementById('graficoPie2').getContext('2d');
    window.myPie2 = new Chart(ctxPie2, config2);
    myPie2.clear();

    //console.log(datos);
    //GRAFICO BARRAS
    var color = Chart.helpers.color;

    var Datos = {
        labels : datos['nombrebar'],
        datasets : [{
                label: 'Nota Venta',
                backgroundColor: color(window.chartColors.purple).alpha(0.5).rgbString(),
                borderColor: window.chartColors.purple,
                borderWidth: 1,
                data : datos['totaldinerobarNV']
            },
            {
                label: 'Facturado (Fecha NV)',
                backgroundColor: color(window.chartColors.yellow).alpha(0.8).rgbString(),
                borderColor: window.chartColors.yellow,
                borderWidth: 1,
                data : datos['totaldineroFecNV']
            },
            {
                label: 'Pendiente',
                backgroundColor: color(window.chartColors.red).alpha(0.8).rgbString(),
                borderColor: window.chartColors.red,
                borderWidth: 1,
                data : datos['totaldineroNVPendiente']
            },
            {
                label: 'Facturado (Fecha FC)',
                backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
                borderColor: window.chartColors.blue,
                borderWidth: 1,
                data : datos['totaldineroFecFC']
            }
        ]
    }

    var ctxbar2 = document.getElementById('graficoBarra2').getContext('2d');

    window.myBar2 = new Chart(ctxbar2, {
        type: 'bar',
        data: Datos,
        options: {
            responsive: true,
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Nota Ventas vs Facturado ($)'
            },/*
            animation: {
                onComplete: function() {
                    generarPNGgraf(myBar2.toBase64Image(),"graficoBarra2");
                }
            }*/
        }
    });

    //myBar2.clear();
    //


    const DATA_COUNT = 7;
    const NUMBER_CFG = {count: DATA_COUNT, min: -100, max: 100};
    
    const data = {
      labels: datos['vetasxmesmeses'],
      datasets: [
        {
          label: 'Ventas x Mes $',
          data: datos['ventasxmesdinero'],
          borderColor: window.chartColors.blue,
          backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
        }
      ]
    };

    const config3 = {
        type: 'line',
        data: data,
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
            },
            title: {
              display: true,
              text: 'Chart.js Line Chart'
            }
          }
        },
      };

    var ctxline1 = document.getElementById('graficoline1').getContext('2d');
    window.myline1 = new Chart(ctxline1,config3);
    myline1.clear();



	$("#graficos1").show();
    $("#reporte1").show();
    $("#grafbarra1").show();
    $("#graficosAP1").show();
    $("#margen").show();

}

function generarPNGgraf(base64,filename){
    //alert($("#"+filename).attr("act"))
    if($("#"+filename).attr("act")=="0"){
        var data = {
            filename : filename,
            base64 : base64,
            _token: $('input[name=_token]').val()
        };
        $.ajax({
            url: '/nvindicadorxvend/imagengrafico',
            type: 'POST',
            data: data,
            success: function (datos) {
                //alert(datos);
        
            }
        });
        $("#"+filename).attr("act","1")
    } 
}

function grafico_pie2(datos){
    google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        arraygrafico = [
            ['Vendedores','Kilos']
        ];
        for (i = 0; i < datos['nombredinero'].length; i++) {
            arraygrafico.push([datos['nombredinero'][i],datos['totaldinero'][i]]);        
        }
        var data = google.visualization.arrayToDataTable(arraygrafico);
        var options = {
          title: 'Ventas por Vendedor $',
          is3D: true
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d2'));
        chart.draw(data, options);

        $("#base64pie2").val(chart.getImageURI());

        //console.log(arraygrafico);
      }
}

function grafico_VentasMesxAreaProd(datos){
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawVisualization);

    function drawVisualization() {
        // Some raw data (not necessarily accurate)
        var data = google.visualization.arrayToDataTable(datos['ventasmesxareaprod']);
        var options = {
            title : 'Ventas por Area de Producción Año '+$("#anno").val(),
            vAxis: {title: '$'},
            hAxis: {title: 'Meses'},
            seriesType: 'bars',
            series: {5: {type: 'line'}}
        };
        var chart = new google.visualization.ComboChart(document.getElementById('graficoventasmesAP'));
        chart.draw(data, options);
        $("#base64ventasmesAP").val(chart.getImageURI());
    }
}

function btnpdf(numrep){
    base64 = "";
    base64b1 = "";
    base64b2 = "";
    if(numrep==2){
        //base64 = myPie2.toBase64Image();
        base64 = $("#base64pie2").val();
    }
    if(numrep==4){
        base64b2 = myBar2.toBase64Image();
    }
    var data = {
        numrep : numrep,
        filename : "graficoPie1",
        base64 : base64,
        base64b1 : base64b1,
        base64b2 : base64b2,
        _token: $('input[name=_token]').val()
    };
    $.ajax({
        url: '/indicadores/imagengrafico',
        type: 'POST',
        data: data,
        success: function (respuesta) {
            aux_titulo = 'Indicadores ' + $("#consulta_id option:selected").html();
            data = datos();
            cadena = "?fechad="+data.fechad+"&fechah="+data.fechah +
                    "&vendedor_id=" + data.vendedor_id+"&giro_id="+data.giro_id + 
                    "&categoriaprod_id=" + data.categoriaprod_id +
                    "&areaproduccion_id="+data.areaproduccion_id +
                    "&idcons="+data.idcons + "&statusact_id="+data.statusact_id +
                    "&aux_titulo="+aux_titulo +
                    "&numrep="+numrep
            $('#contpdf').attr('src', '/indicadores/gestionPdfkg/'+cadena);
            $("#myModalpdf").modal('show');
    
        },
        error: function () {
        }
    }); 
}