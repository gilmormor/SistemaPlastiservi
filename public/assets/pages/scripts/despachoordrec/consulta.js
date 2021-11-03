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
    //consultar(datos());
    $("#btnconsultar").click(function()
    {
        consultar(datos());
    });

    $("#btnconsultarpage").click(function()
    {
        consultarpage(datos());
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

    consultarpage(datos());
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
        id                : $("#id").val(),
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        fechadfac         : $("#fechadfac").val(),
        fechahfac         : $("#fechahfac").val(),
        fechaestdesp      : $("#fechaestdesp").val(),
        rut               : eliminarFormatoRutret($("#rut").val()),
        vendedor_id       : $("#vendedor_id").val(),
        oc_id             : $("#oc_id").val(),
        giro_id           : $("#giro_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        tipoentrega_id    : $("#tipoentrega_id").val(),
        notaventa_id      : $("#notaventa_id").val(),
        statusOD          : $("#statusOD").val(),
        comuna_id         : $("#comuna_id").val(),
        guiadespacho      : $("#guiadespacho").val(),
        numfactura        : $("#numfactura").val(),
        aux_verestado     : $("#aux_verestado").val(),
        _token            : $('input[name=_token]').val()
    };
    return data;
}

function consultar(data){
    $.ajax({
        url: '/reportorddespguiafact/reporte',
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

function consultarpage(data){
    aux_titulo = "";
    cadena = "?id=" +
            "&fechad="+data.fechad+"&fechah="+data.fechah +
            "&fechadfac="+data.fechadfac+"&fechahfac="+data.fechahfac +
            "&fechaestdesp="+data.fechaestdesp +
            "&rut="+data.rut +
            "&oc_id="+data.oc_id +
            "&vendedor_id=" + data.vendedor_id+"&giro_id="+data.giro_id + 
            "&tipoentrega_id="+data.tipoentrega_id +
            "&notaventa_id="+data.notaventa_id +
            "&statusOD=" + data.statusOD +
            "&areaproduccion_id="+data.areaproduccion_id +
            "&comuna_id="+data.comuna_id +
            "&aux_titulo="+aux_titulo +
            "&guiadespacho="+data.guiadespacho +
            "&aux_verestado="+data.aux_verestado

    $("#tabla-data-consulta").dataTable().fnDestroy();
    //return 0;
    $('#tabla-data-consulta').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "/despachoordrec/reporte/" + cadena,
        'order': [[ 0, "desc" ]],
        'columns'     : [
            {data: 'id'},
            {data: 'fechahora'},
            {data: 'razonsocial'},
            {data: 'despachosol_id'},
            {data: 'oc_id'},
            {data: 'notaventa_id'},
            {data: 'comunanombre'},
            {data: 'totalkilos'},
            {data: 'subtotal'},
            {data: 'guiadespacho'},
            {data: 'guiadespachofec'},
            {data: 'numfactura'},
            {data: 'fechafactura'},
            {data: 'oc_file',className:"ocultar"},
            {data: 'oc_file'}
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Despacho' onclick='genpdfOD(" + data.id + ",1)'>"+
                    data.id +
                "</a>";
            $('td', row).eq(0).html(aux_text);
            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Solicitud de Despacho' onclick='genpdfSD(" + data.despachosol_id + ",1)'>" +
                    data.despachosol_id +
                "</a>";
            $('td', row).eq(3).html(aux_text);
            if(data.oc_file != "" && data.oc_file != null){
                aux_text = 
                    "<a class='btn-accion-tabla btn-sm tooltipsC' title='Solicitud de Despacho' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + 
                        data.oc_id + 
                    "</a>";
                $('td', row).eq(4).html(aux_text);
            }
            aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV(" + data.notaventa_id + ",1)'>" +
                    + data.notaventa_id +
                "</a>";
            $('td', row).eq(5).html(aux_text);

            /*
            aux_fecha = data.fechahora
            dia = aux_fecha.substr(8,2);
            mes = aux_fecha.substr(5,2);
            anno = aux_fecha.substr(0,4);
*/
            $('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));
           
            if(data.guiadespachofec != "" && data.guiadespachofec != null){
                $('td', row).eq(10).attr('data-order',data.guiadespachofec);
                aux_fecha = new Date(data.guiadespachofec);
                $('td', row).eq(10).html(fechaddmmaaaa(aux_fecha));    
            }

            if(data.fechafactura != "" && data.fechafactura != null){
                $('td', row).eq(12).attr('data-order',data.fechafactura);
                aux_fecha = new Date(data.fechafactura);
                $('td', row).eq(12).html(fechaddmmaaaa(aux_fecha));
            }

            $('td', row).eq(7).attr('data-order',data.totalkilos);
            $('td', row).eq(7).attr('style','text-align:right');
            $('td', row).eq(8).attr('data-order',data.subtotal);
            $('td', row).eq(8).attr('style','text-align:right');
            aux_text = MASKLA(data.totalkilos,2);
            $('td', row).eq(7).html(aux_text);
            aux_text = MASKLA(data.subtotal,0);
            $('td', row).eq(8).html(aux_text);

            $('td', row).eq(9).attr('style','text-align:center');
            $('td', row).eq(11).attr('style','text-align:center');

            aux_text =
                "<a id='btndespachoordrec' name='btndespachoordrec' href='" + $("#rutacrearrec").val() + data.id + "' class='btn-accion-tabla tooltipsC btndespachoordrec' title='Hacer Rechazo' valor='" + data.id + "'>" +
                    "<button type='button' class='btn btn-default btn-xs'>" +
                        "<i class='fa fa-fw fa-undo'></i>" +
                    "</button>" +
                "</a>";

            $('td', row).eq(14).html(aux_text);
        }
      });
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

$("#btnpdf").click(function()
{
    var data = datos();
    $.ajax({
        url: '/indicadores/imagengrafico',
        type: 'POST',
        data: data,
        success: function (respuesta) {
            aux_titulo = "Orden Despacho";
            data = datos();
            cadena = "?id=" +
                    "&fechad="+data.fechad+"&fechah="+data.fechah +
                    "&fechadfac="+data.fechadfac+"&fechahfac="+data.fechahfac +
                    "&fechaestdesp="+data.fechaestdesp +
                    "&rut="+data.rut +
                    "&oc_id="+data.oc_id +
                    "&vendedor_id=" + data.vendedor_id+"&giro_id="+data.giro_id + 
                    "&tipoentrega_id="+data.tipoentrega_id +
                    "&notaventa_id="+data.notaventa_id +
                    "&statusOD=" + data.statusOD +
                    "&areaproduccion_id="+data.areaproduccion_id +
                    "&comuna_id="+data.comuna_id +
                    "&aux_titulo="+aux_titulo +
                    "&guiadespacho="+data.guiadespacho +
                    "&aux_verestado="+data.aux_verestado
            $('#contpdf').attr('src', '/reportorddespguiafact/exportPdf/'+cadena);
            $("#myModalpdf").modal('show');
        },
        error: function () {
        }
    });
    
});

//$(".btndespachoordrec").click(function(event)
$(document).on("click", ".btndespachoordrec", function(event)
{
    event.preventDefault();
    aux_ruta=$(this).attr('href');
    var data = {
        id     : $(this).attr('valor'),
        _token : $('input[name=_token]').val()
    };
    $.ajax({
        url: '/despachoordrec/valNCCerrada',
        type: 'POST',
        data: data,
        success: function (datos) {
            if(datos.respuesta==1){
                swal({
                    title: datos.mensaje,
                    text: "",
                    icon: 'error',
                    buttons: {
                        confirm: "Cerrar",
                    },
                }).then((value) => {
                });
            }else{
                var loc = window.location;
                window.location = aux_ruta;
            }
        }
    });
});