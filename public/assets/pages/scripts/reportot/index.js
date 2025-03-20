$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    
    $('.datepicker').datepicker({
		language: "es",
        autoclose: true,
        clearBtn : true,
		todayHighlight: true
    }).datepicker("setDate");
    
    $("#rut").focus(function(){
        eliminarFormatoRut($(this));
    });
    $(".numerico").numeric();

    configurarTabla("#tabla-data-consulta");

    function configurarTabla(aux_tabla){
        data = datosRepOt(1,0);
        $(aux_tabla).DataTable({
            'paging'      : true, 
            'lengthChange': true,
            'searching'   : true,
            'ordering'    : true,
            'info'        : true,
            'autoWidth'   : false,
            'processing'  : true,
            'serverSide'  : true,
            "order"       : [[ 0, "asc" ]],
            'ajax'        : "/reportot/reportotpage/" + data.data2, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
            'columns'     : [
                {data: 'id'}, // 0
                {data: 'fechahora'}, // 1
                {data: 'rut'}, // 2
                {data: 'razonsocial'}, // 3
                {data: 'cotizacion_id'}, // 4
                {data: 'oc_id'}, // 5
                {data: 'notaventa_id'}, // 6
                {data: 'nombre_comuna'}, // 7
                {data: 'aux_totalkg'}, // 8
            ],
            "language": {
                //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "createdRow": function ( row, data, index ) {
                $(row).attr('id','fila' + data.id);
                $(row).attr('name','fila' + data.id);
                //"<a href='#' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + "</a>";
                $('td', row).eq(0).attr('style','text-align:center');
                aux_text = 
                    `<a class="btn-accion-tabla btn-sm" title="Ver OT: ${data.id}" onclick='genpdf(${data.id},"","ver-pdf-guia-despacho","/ot/exportPdf/${data.id}")'>
                        ${data.id}
                    </a>`;
                $('td', row).eq(0).html(aux_text);

                if (data.otanulcreated_at != null) {
                    aux_fecha = new Date(data.otanulcreated_at);
                    aux_text = aux_text +
                    "<a class='btn-accion-tabla tooltipsC' title='Anulada " + fechaddmmaaaa(aux_fecha) + "'>" +
                        "<small class='label label-danger'>A</small>" +
                    "</a>";
                    $('td', row).eq(0).html(aux_text);
                }
                /*
                aux_text = 
                "<a class='btn-accion-tabla btn-sm tooltipsC' onclick='generarFactSii(" + data.id + ")' title='Generar DTE Factura SII'>"+
                    + data.id + 
                "</a>";
                $('td', row).eq(0).html(aux_text);
                */
                $('td', row).eq(0).attr('data-order',data.id);

    
                $('td', row).eq(1).attr('data-order',data.fechahora);
                aux_fecha = new Date(data.fechahora);
                $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));
    
                aux_text = "";
                if(data.cotizacion_id != null){
                    /* let arr_cotizacion_id = data.cotizacion_id.split(','); 
                    aux_text = "";
                    for (let i = 0; i < arr_cotizacion_id.length; i++) {
                        aux_text += 
                        "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Cotizacion' onclick='genpdfCOT(" + arr_cotizacion_id[i] + ",1)'>" +
                            arr_cotizacion_id[i] +
                        "</a>";
                    } */    
                    aux_text = 
                    `<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Cotizacion' onclick='genpdfCOT(${data.cotizacion_id},1)'>
                        ${data.cotizacion_id}
                    </a>`;
                }
                $('td', row).eq(4).html(aux_text);
    
                aux_text = "";
                if(data.oc_file != "" && data.oc_file != null){
                    /* let arr_oc_id = data.oc_id.split(','); 
                    let arr_oc_file = data.oc_file.split(','); 
                    for (let i = 0; i < arr_oc_file.length; i++) {
                        aux_text += 
                        "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Compra' onclick='verpdf2(\"" + arr_oc_file[i] + "\",2)'>" + 
                            arr_oc_id[i] + 
                        "</a>";
                        if((i+1) < arr_oc_file.length){
                            aux_text += ",";
                        }
                    } */
                    if(data.staus_oc == null){
                        aux_dir_oc = `notaventa`;
                    }else{
                        aux_dir_oc = `oc`;
                    }
                    aux_text = 
                    `<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Compra' onclick='verpdf3("${data.oc_file}",2,"${aux_dir_oc}")'>
                        ${data.oc_id}
                    </a>`;

                        
                }
                $('td', row).eq(5).html(aux_text);

                aux_text = "";
                if(data.notaventa_id != "" && data.notaventa_id != null){
                    /* let arr_notaventa_id = data.notaventa_id.split(','); 
                    for (let i = 0; i < arr_notaventa_id.length; i++){
                        aux_text += 
                        "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV(" + arr_notaventa_id[i] + ",1)'>" +
                            arr_notaventa_id[i] +
                        "</a>";
                        if((i+1) < arr_notaventa_id.length){
                            aux_text += ",";
                        }
                    } */
                    aux_text = 
                    `<a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV(${data.notaventa_id},1)'>
                        ${data.notaventa_id}
                    </a>`;

                }
                /* if(data.dteguiadespnv_notaventa_id != "" && data.dteguiadespnv_notaventa_id != null){
                    aux_text = 
                        "<a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV(" + data.dteguiadespnv_notaventa_id + ",1)'>" +
                            data.dteguiadespnv_notaventa_id +
                        "</a>";
                } */

                $('td', row).eq(6).html(aux_text);

                $('td', row).eq(8).attr('style','text-align:right');
                $('td', row).eq(8).attr('data-order',data.aux_totalkg);
                $('td', row).eq(8).html(MASKLA(data.aux_totalkg,2));

            }
        });
    }

    //totalizar();

    $("#btnconsultar").click(function()
    {
        data = datosRepOt();
        $('#tabla-data-consulta').DataTable().ajax.url( "/reportot/reportotpage/" + data.data2 ).load();
    });
});

function configurarTabla(aux_tabla){
    $(aux_tabla).DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        "order"       : [[ 0, "desc" ]],
        "language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
    });    
}


function ajaxRequest(data,url,funcion) {
    aux_data = data;
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
            if(funcion=='btndevsol'){
                if (respuesta.mensaje == "ok") {
                    //form.parents('tr').remove();
                    $("#fila"+data['nfila']).remove();
                    Biblioteca.notificaciones('El registro fue procesado correctamente.', 'Plastiservi', 'success');
                } else {
                    if (respuesta.mensaje == "sp"){
                        Biblioteca.notificaciones('Usuario no tiene permiso para eliminar.', 'Plastiservi', 'error');
                    }else{
                        if(respuesta.mensaje == "hijos"){
                            Biblioteca.notificaciones('No puede ser eliminado: ID tiene registros relacionados en otras tablas.', 'Plastiservi', 'error');
                        }else{
                            if(respuesta.mensaje == "ne"){
                                Biblioteca.notificaciones('No tiene permiso para eliminar.', 'Plastiservi', 'error');
                            }else{
                                if(respuesta.mensaje.length > 10){
                                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', 'error');
                                }else{
                                    Biblioteca.notificaciones('El registro no pudo ser eliminado, hay recursos usandolo.', 'Plastiservi', 'error');
                                }
                            }
                        }
                    }
                }
                $("#myModaldevsoldeps").modal('hide');
            }
            if(funcion=='btncerrarsol'){
                if (respuesta.mensaje == "ok") {
                    //form.parents('tr').remove();
                    $("#fila"+data['nfila']).remove();
                    Biblioteca.notificaciones('El registro fue procesado correctamente.', 'Plastiservi', 'success');
                } else {
                    if (respuesta.mensaje == "sp"){
                        Biblioteca.notificaciones('Usuario no tiene permiso para eliminar.', 'Plastiservi', 'error');
                    }else{
                        if(respuesta.mensaje == "hijos"){
                            Biblioteca.notificaciones('No puede ser eliminado: ID tiene registros relacionados en otras tablas.', 'Plastiservi', 'error');
                        }else{
                            if(respuesta.mensaje == "ne"){
                                Biblioteca.notificaciones('No tiene permiso para eliminar.', 'Plastiservi', 'error');
                            }else{
                                Biblioteca.notificaciones('El registro no pudo ser eliminado, hay recursos usandolo.', 'Plastiservi', 'error');
                            }
                        }
                    }
                }
                $("#myModaldevsoldeps").modal('hide');
            }
            if(funcion=="guardarfechaed"){
                Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                restbotoneditfeced(aux_data.i)
                if(respuesta.error == 0){
                    $("#fechaestdesp" + aux_data.i).html($("#fechaed" + aux_data.i).val());
                    $("#savefed" + aux_data.i).attr('updated_at',respuesta.updated_at);
                }
            }
            if(funcion=='staverfacdesp'){
				if (respuesta.error == 0) {
                    $("#dtefac_updated_at" + aux_data.dte_id).html(respuesta.dtefac_updated_at);
				} else {
                    estaSeleccionado = $("#aux_staverfacdesp" + aux_data.dte_id).is(":checked");
                    if(estaSeleccionado){
                        $("#aux_staverfacdesp" + aux_data.dte_id).prop('checked',false);
                    }else{
                        $("#aux_staverfacdesp" + aux_data.dte_id).prop('checked',true);
                    }
				}
                Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
            }
		},
		error: function () {
		}
	});
}

function ajaxRequestGet(data,url,funcion) {
    aux_data = data;
	$.ajax({
		url: url,
		type: 'GET',
		data: data,
		success: function (respuesta) {

            if(funcion=='consultarJS'){
                //console.log(respuesta);
                //return 0;
                pdf = pdfjs(respuesta);
                pdf.save('ot.pdf');    
            }
		},
		error: function () {
		}
	});
}

function datosRepOt(filtro = 0,GenExcel = 0){
    aux_sucursal_id = $("#sucursal_id").val();
    if(filtro == 1){
        aux_sucursal_id = -1;
    }
    var data1 = {
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        rut               : eliminarFormatoRutret($("#rut").val()),
        sucursal_id       : aux_sucursal_id,
        centroeconomico_id: $("#centroeconomico_id").val(),
        vendedor_id       : $("#vendedor_id").val(),
        oc_id             : $("#oc_id").val(),
        /* areaproduccion_id : $("#areaproduccion_id").val(), */
        notaventa_id      : $("#notaventa_id").val(),
        aprobstatus       : $("#aprobstatus").val(),
        aprobstatusdesc   : $("#aprobstatus option:selected").html(),
        aux_estado        : $("#aux_estado").val(),
        comuna_id         : $("#comuna_id").val(),
        ot_id            : $("#ot_id").val(),
        producto_id       : $("#producto_idPxP").val(),
        filtro            : 1,
        statusgen         : 1,
        GenExcel          : GenExcel,
        _token            : $('input[name=_token]').val()
    };
    var data2 = "?fechad="+data1.fechad +
    "&fechah="+data1.fechah +
    "&rut="+data1.rut +
    "&sucursal_id="+data1.sucursal_id +
    "&centroeconomico_id="+data1.centroeconomico_id +
    "&vendedor_id="+data1.vendedor_id +
    "&oc_id="+data1.oc_id +
    /* "&areaproduccion_id="+data1.areaproduccion_id + */
    "&notaventa_id="+data1.notaventa_id +
    "&aprobstatus="+data1.aprobstatus +
    "&aprobstatusdesc="+data1.aprobstatusdesc +
    "&aux_estado="+data1.aux_estado +
    "&comuna_id="+data1.comuna_id +
    "&ot_id="+data1.ot_id +
    "&producto_id="+data1.producto_id +
    "&filtro="+data1.filtro +
    "&statusgen="+data1.statusgen +
    "&GenExcel="+data1.GenExcel +
    "&_token="+data1._token

    var data = {
        data1 : data1,
        data2 : data2
    };
    //console.log(data);
    return data;
}
function consultar(data){
    $.ajax({
        url: '/despachosol/reportesoldesp',
        type: 'POST',
        data: data,
        success: function (datos) {
            if(datos['tabla'].length>0){
                $("#tablaconsulta").html(datos['tabla']);
                configurarTabla('#pendientesoldesp');
                $('.datepickerfed').datepicker({
                    language: "es",
                    autoclose: true,
                    todayHighlight: true
                }).datepicker("setDate");
                let  table = $('#pendientesoldesp').DataTable();
                table
                    .on('draw', function () {
                        eventFired( 'Page' );
                    });
            
            }
        }
    });
}

function consultarcerrarNV(data){
    $.ajax({
        url: '/despachosol/reportesoldespcerrarNV',
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

$(document).on("click", ".btndevsol", function(event){
    event.preventDefault();
    fila = $(this).closest("tr");
    form = $(this);
    id = fila.find('td:eq(0)').text();
    $('.modal-title').html('Devolver Solicitud Despacho');
    $("#despachosol_id").val(id);
    $("#nfilaDel").val(form.attr('fila'));
    $("#ruta").val(form.attr('href'));
    $("#observacion").val("");
    $("#status").val("1");
    $("#boton").val("btndevsol");
    quitarValidacion($(".requeridos").prop('name'),$(".requeridos").attr('tipoval'));
    $("#myModaldevsoldeps").modal('show');
    
});

$(document).on("click", ".btncerrarsol", function(event){
    event.preventDefault();
    fila = $(this).closest("tr");
    form = $(this);
    id = fila.find('td:eq(0)').text();
    $('.modal-title').html('Cerrar Solicitud Despacho');
    $("#despachosol_id").val(id);
    $("#nfilaDel").val(form.attr('fila'));
    $("#ruta").val(form.attr('href'));
    $("#observacion").val("");
    $("#status").val("2");
    $("#boton").val("btncerrarsol");
    quitarValidacion($(".requeridos").prop('name'),$(".requeridos").attr('tipoval'));
    $("#myModaldevsoldeps").modal('show');
    
});

$("#btnGuardarDSD").click(function(event){
    if(verificarFact())
	{
        swal({
            title: '¿ Desea ' + $('.modal-title').html() + ' ?',
            text: "Esta acción no se puede deshacer!",
            icon: 'warning',
            buttons: {
                cancel: "Cancelar",
                confirm: "Aceptar"
            },
        }).then((value) => {
            /*
            fila = $(this).closest("tr");
            form = $(this);
            id = fila.find('td:eq(0)').text();
                //alert(id);
            */
            var data = {
                id     : $("#despachosol_id").val(),
                nfila  : $("#nfilaDel").val(),
                obs    : $("#observacion").val(),
                status : $("#status").val(),
                _token : $('input[name=_token]').val()
            };
            if (value) {
                ajaxRequest(data,$("#ruta").val(),$("#boton").val(),form);
            }
        });
    }else{
		alertify.error("Falta incluir informacion");
	}
});


function verificarFact()
{
	var v1=0;
	var v2=0;
	
	v1=validacion('observacion','texto');
	v2=true;
	if (v1===false || v2===false)
	{
		return false;
	}else{
		return true;
	}
}


$(".requeridos").keyup(function(){
	//alert($(this).parent().attr('class'));
	quitarValidacion($(this).prop('name'),$(this).attr('tipoval'));
});

$(".requeridos").change(function(){
	//alert($(this).parent().attr('class'));
	quitarValidacion($(this).prop('name'),$(this).attr('tipoval'));
});

function btnpdf(numrep){
    if(numrep==1){
        aux_titulo = 'Indicadores ' + $("#consulta_id option:selected").html();
        data = datosRepOt();
        cadena = "?fechad="+data.fechad+"&fechah="+data.fechah +
                "&fechaestdesp=" + data.fechaestdesp +
                "&rut=" + data.rut +
                "&vendedor_id=" + data.vendedor_id +
                "&oc_id=" + data.oc_id +
                "&giro_id=" + data.giro_id + 
                "&areaproduccion_id=" + data.areaproduccion_id +
                "&tipoentrega_id=" + data.tipoentrega_id +
                "&notaventa_id=" + data.notaventa_id +
                "&aprobstatus=" + data.aprobstatus +
                "&comuna_id=" + data.comuna_id +
                "&id=" + data.id +
                "&filtro=" + data.filtro;
        $('#contpdf').attr('src', '/despachosol/pdfpendientesoldesp/'+cadena);
        $("#myModalpdf").modal('show'); 
    }
}

$("#btnpdf2").click(function()
{
    aux_titulo = 'Pendientes Solicitud Despacho';
    data = datosRepOt();
    $('#contpdf').attr('src', '/reportdtefac/exportPdf/' + data.data2);
    $("#myModalpdf").modal('show'); 
});


var eventFired = function ( type ) {
	total = 0;
	$("#pendientesoldesp tr .kgpend").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#totalkg").html(MASKLA(total,2))
	total = 0;
	$("#pendientesoldesp tr .dinpend").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#totaldinero").html(MASKLA(total,0))

}

function editfeced(id,i){
    $(".fechaestdesp").show();
    $(".fechaed").hide();
    $(".editfed").show();
    $(".savefed").hide();
    $("#fechaestdesp" + i).hide();
    $("#fechaed" + i).val($("#fechaestdesp" + i).html());
    $("#fechaed" + i).show();
    $("#editfed" + i).hide();
    $("#savefed" + i).show();
    $("#fechaed" + i).datepicker({
        language: "es",
        autoclose: true,
        todayHighlight: true
    }).datepicker("setDate");
    $("#fechaed" + i).datepicker("refresh");
    $("#fechaed" + i).focus();
    //alert(i);
}


function savefeced(id,aux_i){
    swal({
        title: '¿ Seguro desea actualizar el registro ?',
        text: "Esta acción no se puede deshacer!",
        icon: 'warning',
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
        if (value) {
            var data = {
                id : id,
                i  : aux_i,
                aux_fechaestdesp : $("#fechaed" + aux_i).val(),
                updated_at : $("#savefed" + aux_i).attr('updated_at'),
                _token : $('input[name=_token]').val()
            };
            var ruta = '/despachosol/guardarfechaed'; //Guardar Fecha estimada de despacho
            ajaxRequest(data,ruta,'guardarfechaed');
        }else{
            restbotoneditfeced(aux_i);
        }
    });
}

function restbotoneditfeced(i){
    $("#fechaestdesp" + i).show();
    $("#fechaed" + i).hide();
    $("#editfed" + i).show();
    $("#savefed" + i).hide();
    $(".datepicker").datepicker("refresh");
}

function clickstaverfacdesp(obj){
    let item = $(obj).attr("item");
    var data = {
        dte_id : item,
        updated_at : $("#updated_at" + item).html(),
        dtefac_updated_at : $("#dtefac_updated_at" + item).html(),
        staverfacdesp : $(obj).prop('checked'),
        _token : $('input[name=_token]').val()
    };
    var ruta = '/dtefactura/staverfacdesp'; //Guardar Fecha estimada de despacho
    ajaxRequest(data,ruta,'staverfacdesp');

}

function exportarExcel() {
    orderby = " order by foliocontrol.doc,dte.id ";
    data = datosRepOt(0,1);
    // Obtener todos los registros mediante una solicitud AJAX
    $.ajax({
        url: "/reportot/reportotpage/" + data.data2, // ajusta la URL de la solicitud al endpoint correcto
        type: 'POST',
        dataType: 'json',
        success: function(data) {
        //return 0;
        //console.log(data);
        if(data.respuesta.length == 0){
            swal({
                title: 'Información no encontrada!',
                text: "",
                icon: 'warning',
                buttons: {
                    confirm: "Aceptar"
                },
            }).then((value) => {
                if (value) {
                    //ajaxRequest(data,ruta,'accionnotaventa');
                }
            });
            return 0;
        }
        //console.log(data);
        // Crear una matriz para los datos de Excel
        var datosExcel = [];
        // Agregar los datos de la tabla al arreglo
        aux_vendedor_id = "";
        count = 0;

        cellLengthRazonSoc = 0;
        cellLengthProducto = 0;
        filainifusionar = -1
        //console.log(data);
        aux_centroeconomicoNombre = $("#centroeconomico_id option:selected").html();
        if(aux_centroeconomicoNombre == "Seleccione..."){
            aux_centroeconomicoNombre = "";
        }
        aux_centroeconomicoNombre = "";
        aux_rangofecha = $("#fechad").val() + " al " + $("#fechah").val()
        datosExcel.push(["Orden de Trabajo","","","","","","","","",fechaactual()]);
        datosExcel.push(["Periodo: " + aux_rangofecha,"","","","","","","",""]);
        aux_totalkgtotal = 0;
        datosExcel.push(["","","","","","","","",""]);
        datosExcel.push(["OT","Fecha","RUT","Razon Social","Cot","OC","NV","Comuna","Kg","Estado"]);
        data.respuesta.forEach(function(registro) {
            aux_totalkgtotal += registro.aux_totalkg;
            filainifusionar++;
            aux_fecha = new Date(registro.fechahora);

            aux_estado = registro.aprobstatus;
            if (registro.otanulcreated_at != null) {
                aux_estado = "Anulada";
            }
            var filaExcel = [
                registro.id,
                fechaddmmaaaa(aux_fecha),
                registro.rut,
                registro.razonsocial,
                registro.cotizacion_id,
                registro.oc_id,
                registro.notaventa_id,
                registro.nombre_comuna,
                registro.aux_totalkg,
                aux_estado
            ];
            aux_vendedor_id = registro.vendedor_id;
            count++;

            datosExcel.push(filaExcel);
        });
        /* if(aux_totalkgtotal != 0){
            datosExcel.push(["","","","","Total: ",aux_totalkgtotal,aux_totalmnttotal,"","",""]);
        } */
        datosExcel.push(["","","","","","","","Total: ",aux_totalkgtotal,"","","",""]);

        createExcel(datosExcel);

      },
      error: function(xhr, status, error) {
        console.log(error);
      }
    });


    // Llamar a la función para crear el archivo Excel

}

function createExcel(datosExcel) {
    // Crear un nuevo libro de trabajo y una nueva hoja
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Datos");

    // Insertar los datos en la hoja de trabajo
    worksheet.addRows(datosExcel);

    // Establecer negrita en la celda A1
    //worksheet.getCell("A5").font = { bold: true };


    // Ajustar automáticamente el ancho de la columna B al contenido
    ajustarcolumnaexcel(worksheet,"B");
    ajustarcolumnaexcel(worksheet,"C");
    ajustarcolumnaexcel(worksheet,"D");
    ajustarcolumnaexcel(worksheet,"E");
    ajustarcolumnaexcel(worksheet,"F");
    ajustarcolumnaexcel(worksheet,"G");
    ajustarcolumnaexcel(worksheet,"H");
    ajustarcolumnaexcel(worksheet,"I");
    ajustarcolumnaexcel(worksheet,"J");

    //Establecer negrilla a titulo de columnas Fila 4
    const row6 = worksheet.getRow(4);
    for (let i = 1; i <= 10; i++) {
        cell = row6.getCell(i);
        cell.font = { bold: true };
        cell.autosize = true;
    }

    // Obtén el objeto de la columna y establece la propiedad hidden en true
    /* columnhidden = worksheet.getColumn("H");
    columnhidden.hidden = true;
    columnhidden = worksheet.getColumn("I");
    columnhidden.hidden = true;
    columnhidden = worksheet.getColumn("J");
    columnhidden.hidden = true;
    columnhidden = worksheet.getColumn("K");
    columnhidden.hidden = true; */
    /*
    columnhidden = worksheet.getColumn("N");
    columnhidden.hidden = true;
    */

    //AJUSTAR EL TEXTO CELDAS A4:AI4
    // Supongamos que deseas ajustar el texto en la fila 4 y hacer que las celdas en negrita
    fila = 4;

    // Iterar a través de las celdas en la fila y configurar el formato
    for (let i = 1; i <= 10; i++) {
        columna = getColumnLetter(i); // Obten la letra de la columna correspondiente
        const celda = worksheet.getCell(`${columna}${fila}`);
        celda.alignment = { wrapText: true, vertical: 'middle' };
        celda.autosize = true;
    }    


    // Recorrer la columna 7 y dar formato con punto para separar los miles
    const columnG = worksheet.getColumn(6);
    columnG.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });

    // Recorrer la columna R y dar formato con punto para separar los miles
    const columnR = worksheet.getColumn(7);
    columnR.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "###0";
        }
    });

    // Recorrer la columna 7 y dar formato con punto para separar los miles
    const columnI = worksheet.getColumn(9);
    columnI.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });


    // Establecer el formato de centrado horizontal y vertical para las celdas de la columna 8 desde la fila 4 hasta la fila 58
    for (let i = 4; i <= datosExcel.length; i++) {
        const cell1 = worksheet.getCell(i, 1);
        cell1.alignment = { horizontal: "center", vertical: "middle" };
        const cell5 = worksheet.getCell(i, 5);
        cell5.alignment = { horizontal: "center", vertical: "middle" };
        const cell6 = worksheet.getCell(i, 6);
        cell6.alignment = { horizontal: "center", vertical: "middle" };
        const cell7 = worksheet.getCell(i, 7);
        cell7.alignment = { horizontal: "center", vertical: "middle" };
        /* const cell8 = worksheet.getCell(i, 8);
        cell8.alignment = { horizontal: "center", vertical: "middle" }; */
        /* const cell9 = worksheet.getCell(i, 9);
        cell9.alignment = { horizontal: "center", vertical: "middle" }; */
        const cell10 = worksheet.getCell(i, 10);
        cell10.alignment = { horizontal: "center", vertical: "middle" };

        /* const cell = worksheet.getCell(i, 13);
        cell.alignment = { horizontal: "center", vertical: "middle" }; */

    }


    //Negrita Columna Titulo
    const row1 = worksheet.getRow(1);
    cell = row1.getCell(1);
    cell.font = { bold: true, size: 20 };
    cell.alignment = { horizontal: "center", vertical: "middle" };


    //Titulo Kg
    rowX = worksheet.getRow(4);
    cell = rowX.getCell(6);
    cell.alignment = { horizontal: "center", vertical: "middle" };

    //Titulo Monto
    rowX = worksheet.getRow(4);
    cell = rowX.getCell(7);
    cell.alignment = { horizontal: "center", vertical: "middle" };
    

    //Fecha Reporte
    const row2 = worksheet.getRow(1);
    cell = row2.getCell(10);
    cell.alignment = { horizontal: "center", vertical: "middle" };


    //Fusionar celdas de Titulo
    const startCol = 0;
    const endCol = 9;
    worksheet.mergeCells(1, startCol, 1, endCol);

    //Negrita Columna Sucursal
    const row3 = worksheet.getRow(2);
    cell = row3.getCell(1);
    cell.alignment = { horizontal: "center", vertical: "middle" };
    
    //Fusionar celdas Sucursal
    const startCol1 = 0;
    const endCol1 = 9;
    worksheet.mergeCells(2, startCol1, 2, endCol1);

    // Establecer negrita a totales
    row = worksheet.getRow(datosExcel.length);
    for (let i = 1; i <= 9; i++) {
        cell = row.getCell(i);
        cell.font = { bold: true };
        cell.alignment = { horizontal: "right" };
        cell.numFmt = "#,##0.00";
    }

    cell = row.getCell(7);
    cell.font = { bold: true };
    cell.alignment = { horizontal: "right" };
    cell.numFmt = "#,##0";


    // Guardar el archivo
    workbook.xlsx.writeBuffer().then(function(buffer) {
      // Crear un objeto Blob para el archivo Excel
      const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });

      // Crear un enlace de descarga
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = "OT.xlsx";
      a.click();

      // Limpiar el objeto Blob
      window.URL.revokeObjectURL(url);
    });
}


function ejecutarConsulta(aux_cod){
    /* if(aux_cod == 1){
        cliente_id = $("#rut").attr("cliente_id");
        if(cliente_id != ""){
            data = datosRepOt(1,0);
            var ruta = '/reportot/reportotpage';
            ajaxRequest(data.data1,ruta,'consulta');        
        }else{
            swal({
                title: 'Falta RUT Cliente',
                //text: "Presione F2 para buscar",
                icon: 'warning',
                buttons: {
                    confirm: "Aceptar"
                },
            }).then((value) => {
                if (value) {
                    //ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
                    $("#rut").focus();
                }
            });
        }
    
    } */
    if(aux_cod == 2){
        data = datosRepOt(0,1);
        consultarJS(data);    
    }
    if(aux_cod == 3){
        data = datosFac(1);
        consultarExcel(data);    
    }
    if(aux_cod == 4){
        data = datosFac(1);
        enviarcorreo(data);
    }

}

function consultarJS(data){
    var ruta = '/reportot/reportotpage';
    ajaxRequestGet(data.data1,ruta,'consultarJS');
}

function pdfjs(datos) {
    var base64Img = imgToBase64('assets/lte/dist/img/LOGO-PLASTISERVI.jpg');
    var doc = new jsPDF();
    var totalPagesExp = '{total_pages_count_string}';

    // Agrupar datos por vendedor

    // Imprimir encabezado una vez
    doc.setFontSize(12);
    doc.setTextColor(20);
    if (base64Img) {
        doc.addImage(base64Img, 'JPEG', 14, 6, 30, 10);
    }
    doc.text('Ordenes de Trabajo', 69, 12);
    doc.setFontSize(8);
    doc.text('Sucursal: ' + $("#sucursal_id option:selected").html(), 70, 16);
    doc.text('Fecha: ' + datos.fechaacthora, 150, 11);
    //doc.text('Periodo: ' + $("#fechad").val() + " al " + $("#fechah").val(), 160, 14);

    doc.autoTable({
        startY: 15,
        //head: headRows(),
        body: [],
        theme: 'grid',
        styles: {
            fontSize: 6, // Tamaño de letra para los encabezados
        },
        headStyles: {
            fillColor: '#0077FF', // Color de fondo azul
            textColor: '#FFFFFF', // texto blanco solo para el encabezado
            valign: 'middle' // Centrar verticalmente los títulos en el encabezado
        },
        margin: { top: 20 },
    });


    doc.autoTable({
        startY: doc.autoTable.previous.finalY + 7,
        head: headRows(),
        body: bodyRows(datos.respuesta),
        theme: 'grid',
        styles: {
            fontSize: 6, // Tamaño de letra para los encabezados
        },
        headStyles: { 
            fillColor: '#0077FF', // Color de fondo azul
            textColor: '#FFFFFF', //texto blanco solo para el encabezado
            valign: 'middle', // Centrar verticalmente los títulos en el encabezado
        },        
        columnStyles: {
            0: { cellWidth: 10, halign: 'center',valign: 'middle'  },  // Ancho columna
            1: { cellWidth: 15, halign: 'center',valign: 'middle' },  // Ancho columna
            2: { cellWidth: 15, halign: 'center',valign: 'middle' },  // Ancho columna
            3: { cellWidth: 50, halign: 'left',valign: 'middle' },  // Ancho columna
            4: { cellWidth: 10, halign: 'center',valign: 'middle' },  // Ancho columna
            5: { cellWidth: 15, halign: 'center',valign: 'middle' },  // Ancho columna
            6: { cellWidth: 15, halign: 'center',valign: 'middle' },  // Ancho columna
            7: { cellWidth: 15, halign: 'left',valign: 'middle' },  // Ancho columna
            8: { cellWidth: 15, halign: 'right',valign: 'middle' },  // Ancho columna
            9: { cellWidth: 15, halign: 'center',valign: 'middle' },  // Ancho columna
        },
        didParseCell: function(data) {
            if (data.section === 'body') {
                // Aplica estilo si la factura está vencida
                //if (vencimiento < today) {
                /* if (fechaaaaammdd(data.row.raw[2]) <= datos.fechaactaaaammdd) {
                    if (data.column.index === 2 || data.column.index === 4) { // Verifica si es la columna de fecha de vencimiento o deuda
                        data.cell.styles.fillColor = [255, 230, 230]; // Fondo rojo claro
                        data.cell.styles.textColor = [255, 0, 0]; // Texto rojo
                    }
                } */
            }
            if (data.section === 'head' && (data.column.index >= 4 && data.column.index <= 7 )) { // Solo para el encabezado de la columna "Comuna"
                data.cell.styles.halign = 'center'; // Centra el texto horizontalmente
            }
            if (data.section === 'head' && data.column.index === 8) { // Solo para el encabezado de la columna "Comuna"
                data.cell.styles.halign = 'right'; // Alinear texto a la derecha
            }
            if (data.section === 'head' && data.column.index === 9) { // Solo para el encabezado de la columna "Comuna"
                data.cell.styles.halign = 'center'; // Centra el texto horizontalmente
            }
        },
        willDrawPage: function (data) {
            // Footer
            var str = 'Pag ' + doc.internal.getNumberOfPages();
            if (typeof doc.putTotalPages === 'function') {
                str = str + ' de ' + totalPagesExp;
            }
            doc.setFontSize(7);
            var pageSize = doc.internal.pageSize;
            var pageHeight = pageSize.height ? pageSize.height : pageSize.getHeight();
            doc.text(str, data.settings.margin.left, pageHeight - 10);
        },
        margin: { top: 20, left: 20, right: 20 },
    });

    // Add Total Page Count if needed
    if (typeof doc.putTotalPages === 'function') {
        doc.putTotalPages(totalPagesExp);
    }

    return doc;
}

function headRows() {
    return [
      { Ot: "OT", Fecha: "Fecha", Rut: "RUT", RazonSocial: "Razon Social", Cotizacion: "Cot", OC: "OC", NotaVenta: "NV", Comuna: "Comuna", Kg: "Kg", Estado: "Estado"},
    ]
}

function bodyRows(data) {
    const body = [];
    data.forEach(row => {
        //console.log(row);
        aux_estado = row.aprobstatus;
        if (row.otanulcreated_at != null) {
            aux_estado = "Anulada";
        }

        body.push([row.id,fechaddmmaaaa(new Date(row.fechahora)),row.rut,row.razonsocial,row.cotizacion_id,row.oc_id,row.notaventa_id,row.comuna_nombre,MASKLA(row.aux_totalkg,2),aux_estado]);
        // Aplica el fondo rojo claro si la factura está vencida
        //const rowStyle = isVencida ? {fillColor: [255, 204, 204], textColor: [255, 0, 0]} : {};
        //body.push({row: rowArray, styles: rowStyle});
    });
    return body;
}