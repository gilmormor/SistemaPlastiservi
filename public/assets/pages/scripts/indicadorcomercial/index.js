$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#btnconsultar").click(function()
    {
        //$( "#tabs" ).tabs({ active: "tab_1" });
        //$('a[href="#tab_1"]').click();
        consultar(datos());
    });

    $("#tab2").click(function(){
        //$(".hideshowdinero").css({'display':'none'});
        $(".hideshowdinero").show();
        $(".hskilos").hide();
    });

    $("#tab5").click(function(){
        //$(".hideshowdinero").css({'display':'none'});
        $(".hideshowdinero").hide()    
        $(".hskilos").show();
    });

    //alert(aux_nfila);
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
/*
    validarFechaDesdeHasta('ini','fechad','fechah');
    validarFechaDesdeHasta('fin','fechah','fechad');
*/
    //configurarTabla('.tablas');

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
/*
function validarFechaDesdeHasta(status,objeto1,objeto2){
    if(status=='ini'){
        configurarFechaDesde(objeto1,objeto2);
    }else{
        configurarFechaHasta(objeto1,objeto2);
    }
}

function configurarFechaDesde(objeto1,objeto2){
    fecha = charToDate($("#"+objeto2).val());
    $("#"+objeto1).datepicker({
        language: "es",
        autoclose: true,
        clearBtn : true,
        todayHighlight: true,
        endDate: fecha
    }).datepicker("setDate");

}
function configurarFechaHasta(objeto1,objeto2){
    fecha = charToDate($("#"+objeto2).val());
    $("#"+objeto1).datepicker({
        language: "es",
        autoclose: true,
        clearBtn : true,
        startDate: fecha,
        todayHighlight: true
    }).datepicker("setDate");
}
*/

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
        vendedor_id: $("#vendedor_id").val(), //JSON.stringify($("#vendedor_id").val()),
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
    $("#graficos").hide();
    $("#graficos2").hide();
    $("#grafbarra1").hide();
    $("#graficosMC1").hide();
    $("#graficosAP1").hide();
    
   
    $.ajax({
        url: '/indicadores/reportecomercial',
        type: 'POST',
        data: data,
        success: function (datos) {
            //console.log(datos['tabla']);
            $("#titulo_grafico").html('');
            $("#titulo_grafico2").html('');
            $("#tablaconsulta").html('');
            $("#tablaconsultaproducto").html('');
            $("#tablaAP").html('');
            $("#tablaMC").html('');
            $("#tablaventasmesap").html('');
            $("#tablaventaPVC").html('');
            if(datos['tabla'].length>0){
                aux_titulo = $("#consulta_id option:selected").html();
                $("#titulo_grafico").html('Indicadores ' +aux_titulo+ ' por Vendedor');
                $("#titulo_grafico2").html('Indicadores ' +aux_titulo);
                $("#tablaconsulta").html(datos['tabla']);
                $("#tablaconsultaproducto").html(datos['tablaagruxproducto']);
                //console.log(datos['tablaagruxproductomargen']);
                $("#tablaAP").html(datos['tablaareaproduccion']);

                $("#tablaMC").html(datos['tabladinero']);

                $("#tablaventasmesap").html(datos['tablaventasmesap']);
                $("#tablaventaPVC").html(datos['tablaventaPVC']);

                //console.log(datos['ventasmesxareaprod']);

                configurarTabla('.tablascons');
                grafico(datos);
            }
        }
    });
}

function consultarpdf(data){
    $.ajax({
        url: '/indicadores/comercialPdf',
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
    grafico_pie3(datos);
    grafico_VentasMesxAreaProd(datos);
    grafico_VentasMesPVC(datos);
    //corechartprueba(datos);
    $("#graficos").show();
    $("#graficos2").show();
    $("#reporte1").show();
    $("#grafbarra1").show();
    $("#graficosMC1").show();
    $("#graficosAP1").show();
    $("#graficoVentasxMes").show();
    $("#graficoVentasMesAP").show();
    $("#graficoVentasPVC").show();
    $('.resultadosPie1').html('<canvas id="graficoPie1" act="0"></canvas>');
    $('.resultadosBarra1').html('<canvas id="graficoBarra1" act="0"></canvas>');
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

    const DATA_COUNT = 7;
    const NUMBER_CFG = {count: DATA_COUNT, min: -100, max: 100};
    
    const data = {
      labels: datos['vetasxmesmeses'],
      datasets: [
        {
          label: 'Ventas x Mes Kg',
          data: datos['ventasxmeskilos'],
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
    //myline1.clear();



	$("#graficos").show();
	$("#graficos2").show();
    $("#reporte1").show();
    $("#grafbarra1").show();
    $("#graficosMC1").show();
    $("#graficosAP1").show();
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
            url: '/indicadores/imagengrafico',
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
            title: 'Ventas Kg por Vendedor',
            is3D: true
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d1'));
        chart.draw(data, options);

        $("#base64pie1").val(chart.getImageURI());

        //console.log(chart.getImageURI());
      }
}

function grafico_pie3(datos){
    google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        arraygrafico = [
            ['Productos','Kilos']
        ];
        for (i = 0; i < datos['productos'].length; i++) {
            arraygrafico.push([datos['productos'][i].gru_nombre,datos['productos'][i].totalkilos]);        
        }
        var data = google.visualization.arrayToDataTable(arraygrafico);
        var options = {
            title: 'Kilos por Producto',
            is3D: true,
            sliceVisibilityThreshold: .00006
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d3'));
        chart.draw(data, options);

        $("#base64pie3").val(chart.getImageURI());

        //console.log(arraygrafico);
        //console.log(datos['productos']);
      }
}

function grafico_VentasMesxAreaProd(datos){
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawVisualization);

    function drawVisualization() {
        // Some raw data (not necessarily accurate)
        $("#tituloventasmesxAP").html('Ventas por Area de Producción Año '+$("#anno").val());
        var data = google.visualization.arrayToDataTable(datos['ventasmesxareaprod']);
        var options = {
            title : $("#tituloventasmesxAP").html(),
            vAxis: {title: 'Kg.'+$("#consulta_id option:selected").html()},
            hAxis: {title: 'Meses'},
            seriesType: 'bars',
            //series: {5: {type: 'line'}}
        };
        var chart = new google.visualization.ComboChart(document.getElementById('graficoventasmesAP'));
        chart.draw(data, options);
        $("#base64ventasmesAP").val(chart.getImageURI());
    }
}

function grafico_VentasMesPVC(datos){
    google.charts.load('current', {'packages':['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawStuff);

    function drawStuff() {

        $("#tituloventaspvc").html('Ventas PVC Año '+$("#anno").val());
        var chartDiv = document.getElementById('graficoventaspvc');

        var data = google.visualization.arrayToDataTable(datos['ventasmespvc']);

        var classicOptions = {
            width: 900,
            series: {
                0: {targetAxisIndex: 0},
                1: {targetAxisIndex: 1,type: 'line'}
            },
            title: $("#tituloventaspvc").html(),
            vAxes: {
                // Adds titles to each axis.
                0: {title: 'Kg.'+$("#consulta_id option:selected").html()},
                1: {title: 'Precio Kg($)'}
            },
            hAxis: {title: 'Meses'}
        };
        var classicChart = new google.visualization.ColumnChart(chartDiv);
        classicChart.draw(data, classicOptions);
        $("#base64ventaspvc").val(classicChart.getImageURI());
    };
}

function btnpdf(numrep){
    base64 = "";
    base64b1 = "";
    base64b2 = "";
    if(numrep==1){
        //base64 = myPie1.toBase64Image();
        base64 = $("#base64pie1").val();
    }
    if(numrep==4){
        base64b1 = myBar1.toBase64Image();
    }
    if(numrep==5){
        base64 = $("#base64pie3").val();
    }
    if(numrep==8){
        base64 = myline1.toBase64Image();;
    }
    if(numrep==9){
        base64 = $("#base64ventasmesAP").val();
    }
    if(numrep==10){
        base64 = $("#base64ventaspvc").val();
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
                    "&numrep="+numrep +
                    "&anno="+$("#anno").val()
            $('#contpdf').attr('src', '/indicadores/comercialPdfkg/'+cadena);
            $("#myModalpdf").modal('show');
    
        },
        error: function () {
        }
    }); 
}