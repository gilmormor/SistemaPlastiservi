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
    //$('.col-xs-12').css({'margin-bottom':'0px','margin-left': '0px','margin-right': '0px','padding-left': '5px','padding-right': '5px'});
    $("#btnconsultar").click(function()
    {
        consultar(datos());
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
    $("#grafbarra1").hide();
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
    grafico_pie1(datos);
    grafico_pie2(datos);
    corechartprueba(datos);
    $("#graficos").show();
    $("#graficos1").show();
    $("#graficos2").show();
    $("#reporte1").show();
    $("#grafbarra1").show();
    $('.resultadosPie1').html('<canvas id="graficoPie1" act="0"></canvas>');
    $('.resultadosPie2').html('<canvas id="graficoPie2" act="0"></canvas>');
    $('.resultadosBarra1').html('<canvas id="graficoBarra1" act="0"></canvas>');
    $('.resultadosBarra2').html('<canvas id="graficoBarra2" act="0"></canvas>');
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
        options: {/*
            responsive: true,
            animation: {
                onComplete: function() {
                    generarPNGgraf(myPie1.toBase64Image(),"graficoPie1");
                }
            },*/
            title: {
                display: true,
                text: 'Venta por Vendedor Kg.'
            }
        }
    };
    var ctxPie1 = document.getElementById('graficoPie1').getContext('2d');
    window.myPie1 = new Chart(ctxPie1,config1);
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

    console.log(datos);
    //GRAFICO BARRAS
    var color = Chart.helpers.color;
    var Datos = {
        labels : datos['nombrebar'],
        datasets : [{
                label: 'Nota Venta',
                backgroundColor: color(window.chartColors.purple).alpha(0.5).rgbString(),
                borderColor: window.chartColors.purple,
                borderWidth: 1,
                data : datos['totalkilosbarNV']
            },
            {
                label: 'Facturado (Fecha NV)',
                backgroundColor: color(window.chartColors.yellow).alpha(0.8).rgbString(),
                borderColor: window.chartColors.yellow,
                borderWidth: 1,
                data : datos['totalkilosbarFecNV']
            },
            {
                label: 'Pendiente',
                backgroundColor: color(window.chartColors.red).alpha(0.8).rgbString(),
                borderColor: window.chartColors.red,
                borderWidth: 1,
                data : datos['totalkilosbarNVPendiente']
            },
            {
                label: 'Facturado (Fecha FC)',
                backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
                borderColor: window.chartColors.blue,
                borderWidth: 1,
                data : datos['totalkilosbarFecFC']
            }
        ]
    }
    var ctxbar1 = document.getElementById('graficoBarra1').getContext('2d');

    window.myBar1 = new Chart(ctxbar1, {
        type: 'bar',
        data: Datos,
        options: {
            responsive: true,
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Nota Ventas vs Facturado (Kg)'
            },/*
            animation: {
                onComplete: function() {
                    generarPNGgraf(myBar1.toBase64Image(),"graficoBarra1");
                }
            }*/
        }
    });
    //myBar1.clear();

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
	$("#graficos").show();
	$("#graficos1").show();
	$("#graficos2").show();
    $("#reporte1").show();
    $("#grafbarra1").show();
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

function corechartprueba(datos){
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawVisualization);

    function drawVisualization() {
      // Some raw data (not necessarily accurate)
      arraygrafico = [
        ['Vendedores','Nota Venta','Fecha FC','Fecha NV','Promedio']
    ];

    for (i = 0; i < datos['nombrebar'].length; i++) {
        promedio = (datos['totalkilosbarNV'][i]+datos['totalkilosbarFecFC'][i]+datos['totalkilosbarFecNV'][i])/3;
        arraygrafico.push([datos['nombrebar'][i],datos['totalkilosbarNV'][i],datos['totalkilosbarFecFC'][i],datos['totalkilosbarFecNV'][i],promedio]);
    }

      var data = google.visualization.arrayToDataTable(arraygrafico);

      var options = {
        title : 'Monthly Coffee Production by Country',
        vAxis: {title: 'Kilos'},
        hAxis: {title: 'Vendedores'},
        seriesType: 'bars',
        series: {3: {type: 'line'}},
        legend: {position: 'top', maxLines: 4},
      };

      var chart = new google.visualization.ComboChart(document.getElementById('chart_div11'));
      chart.draw(data, options);
    }

}

function grafico_pie1(datos){
    google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        arraygrafico = [
            ['Vendedores','Kilos']
        ];
        for (i = 0; i < datos['nombre'].length; i++) {
            arraygrafico.push([datos['nombre'][i],datos['totalkilos'][i]]);        
        }
        var data = google.visualization.arrayToDataTable(arraygrafico);
        var options = {
            title: 'Ventas por Vendedor Kg',
            is3D: true
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d1'));
        chart.draw(data, options);

        $("#base64pie1").val(chart.getImageURI());

        //console.log(chart.getImageURI());
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

        //console.log(chart.getImageURI());
      }


}

function btnpdf(numrep){
    base64 = "";
    base64b1 = "";
    base64b2 = "";
    if(numrep==1){
        //base64 = myPie1.toBase64Image();
        base64 = $("#base64pie1").val();
    }
    if(numrep==2){
        //base64 = myPie2.toBase64Image();
        base64 = $("#base64pie2").val();
    }
    if(numrep==4){
        //base64 = myPie2.toBase64Image();
        base64b1 = myBar1.toBase64Image();
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
        url: '/nvindicadorxvend/imagengrafico',
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
            $('#contpdf').attr('src', '/nvindicadorxvend/exportPdfkg/'+cadena);
            $("#myModalpdf").modal('show');
    
        },
        error: function () {
        }
    }); 
}