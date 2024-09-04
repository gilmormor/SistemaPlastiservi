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
        consultar(datosProdxNV());
    });

    $("#btnpdf1").click(function()
    {
        consultarpdf(datosProdxNV());
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

function datosProdxNV(){
    ordentabla();
    var data = {
        fechad: $("#fechad").val(),
        fechah: $("#fechah").val(),
        categoriaprod_id: $("#categoriaprod_id").val(),
        giro_id: $("#giro_id").val(),
        rut: eliminarFormatoRutret($("#rut").val()),
        vendedor_id: $("#vendedor_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        orden : $("#orden").val(),
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
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
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
    $.ajax({
        url: '/prodxnotaventa/reporte',
        type: 'POST',
        data: data,
        success: function (datos) {
            if(datos['tabla'].length>0){
                $("#tablaconsulta").html(datos['tabla']);
                configurarTabla('.tablascons');
            }
        }
    });
}

function consultarpdf(data){
    $.ajax({
        url: '/prodxnotaventa/exportPdf',
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

$('#form-general').submit(function(event) {
    ordentabla();
})

function ordentabla(){
    var tabla = $('#tablacotizacion').DataTable();
    var ordenActual = tabla.order(); //obtengo el array del orden actual de la tabla
    if(ordenActual == undefined){
        $("#orden").val("0,'1','asc'");
    }else{
        var encabezado = tabla.column(ordenActual[0][0]).header(); //en la posicion [0][0] esta el num de columna
        var nombreCampo = encabezado.getAttribute('nombrecampo'); //a traves del atributo nombrecampo que asigne en php al crear la tabla en los encabezados
        if(ordenActual[0][2]){
            ordenActual[0][2] = nombreCampo;
        }else{
            ordenActual[0].push(nombreCampo); //inserto a array el nombre de campo por el cual quiero ordenar en la consulta SQL en php
        }
        $("#orden").val(ordenActual[0]); //Actualizo el valor del elemento orden para que lo envie a php    
    }
}