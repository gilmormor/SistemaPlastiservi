$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#btnconsultar").click(function()
    {
        consultar(datos());
    });

    $("#tab2").click(function(){
        $(".hideshowdinero").show();
        $(".hskilos").hide();
    });

    $("#tab5").click(function(){
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
        areaproduccion_id : JSON.stringify($("#areaproduccion_id").val()),
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
    $("#consulta1").hide();
    $.ajax({
        url: '/indicadores/reportekilostipoentrega',
        type: 'POST',
        data: data,
        success: function (datos) {
            if(datos['tabla'].length>0){
                /*
                aux_titulo = $("#consulta_id option:selected").html();
                $("#titulo_grafico").html('Indicadores ' +aux_titulo+ ' por Vendedor');
                $("#titulo_grafico2").html('Indicadores ' +aux_titulo);
                */
                $("#tablaconsulta1").html(datos['tabla']);
                $("#consulta1").show();

                configurarTabla('.tablascons');
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


function btnpdf(numrep){
    aux_titulo = 'Kg por tipo de entrega';
    data = datos();
    cadena = "?fechad="+data.fechad+
            "&fechah="+data.fechah +
            "&vendedor_id=" + data.vendedor_id+
            "&giro_id="+data.giro_id + 
            "&categoriaprod_id=" + data.categoriaprod_id +
            "&areaproduccion_id="+data.areaproduccion_id +
            "&idcons="+data.idcons + 
            "&statusact_id="+data.statusact_id +
            "&aux_titulo="+aux_titulo;
    $('#contpdf').attr('src', '/indicadores/reportekilostipoentregapdf/'+cadena);
    $("#myModalpdf").modal('show');
}