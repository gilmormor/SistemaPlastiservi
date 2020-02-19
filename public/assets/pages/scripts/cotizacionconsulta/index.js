$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');


    $("#btnconsultar").click(function()
    {
        consultar($("#fechad").val(),$("#fechah").val());
    });

    $("#btnpdf1").click(function()
    {
        consultarpdf($("#fechad").val(),$("#fechah").val());
    });

    //alert(aux_nfila);
    $('.datepicker').datepicker({
		language: "es",
		autoclose: true,
		todayHighlight: true
	}).datepicker("setDate");

});

function configurarTabla(){
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

function consultar(fechad,fechah){
    var data = {
        fechad: fechad,
        fechah: fechah,
        _token: $('input[name=_token]').val()
    };
    $.ajax({
        url: '/cotizacionconsulta/reporte',
        type: 'POST',
        data: data,
        success: function (datos) {
            if(datos['tabla'].length>0){
                $("#tablaconsulta").html(datos['tabla']);
                configurarTabla();
            }
        }
    });
}

function consultarpdf(fechad,fechah){
    var data = {
        fechad: fechad,
        fechah: fechah,
        _token: $('input[name=_token]').val()
    };
    $.ajax({
        url: '/cotizacionconsulta/exportPdf',
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