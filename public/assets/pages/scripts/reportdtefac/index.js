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

    configurarTabla('#tabla-data-consulta');

    function configurarTabla(aux_tabla){
        data = datosFac(1);
        $(aux_tabla).DataTable({
            'paging'      : true, 
            'lengthChange': true,
            'searching'   : true,
            'ordering'    : true,
            'info'        : true,
            'autoWidth'   : false,
            'processing'  : true,
            'serverSide'  : true,
            "order"       : [[ 1, "asc" ],[ 11, "asc" ]],
            'ajax'        : "/reportdtefac/reportdtefacpage/" + data.data2, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
            'columns'     : [
                {data: 'id'}, // 0
                {data: 'fchemis'}, // 1
                {data: 'rut'}, // 2
                {data: 'razonsocial'}, // 3
                {data: 'cotizacion_id'}, // 4
                {data: 'oc_id'}, // 5
                {data: 'notaventa_id'}, // 6
                {data: 'despachosol_id'}, // 7
                {data: 'despachoord_id'}, // 8
                {data: 'nrodocto_guiadesp'}, // 9
                {data: 'nrodocto_guiadesp'}, // 10
                {data: 'nrodocto'}, // 11
                {data: 'nombre_comuna'}, // 12
                {data: 'dteanul_obs',className:"ocultar"}, //13
                {data: 'dteanulcreated_at',className:"ocultar"}, //14
                {data: 'clientebloqueado_descripcion',className:"ocultar"}, //15
                {data: 'oc_file',className:"ocultar"}, //16
                {data: 'nombrepdf',className:"ocultar"}, //17
                {data: 'staverfacdesp',className:"ocultar"}, //18
                {data: 'updated_at',className:"ocultar"}, //19
                {data: 'dtefac_updated_at',className:"ocultar"}, //20
                {defaultContent : ""}
            ],
            "language": {
                //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "createdRow": function ( row, data, index ) {
                $(row).attr('id','fila' + data.id);
                $(row).attr('name','fila' + data.id);
                //"<a href='#' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + "</a>";
                if (data.dteanul_obs != null) {
                    aux_fecha = new Date(data.dteanulcreated_at);
                    aux_text = data.id +
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

    
                $('td', row).eq(1).attr('data-order',data.fchemis);
                aux_fecha = new Date(data.fchemis + " 00:00:00");
                $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));
    
                if(data.cotizacion_id != null){
                    let arr_cotizacion_id = data.cotizacion_id.split(','); 
                    aux_text = "";
                    for (let i = 0; i < arr_cotizacion_id.length; i++) {
                        aux_text += 
                        "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Cotizacion' onclick='genpdfCOT(" + arr_cotizacion_id[i] + ",1)'>" +
                            arr_cotizacion_id[i] +
                        "</a>";
                    }    
                }else{
                    aux_text = "";
                }
                $('td', row).eq(4).html(aux_text);
    
                aux_text = "";
                if(data.oc_file != "" && data.oc_file != null){
                    let arr_oc_id = data.oc_id.split(','); 
                    let arr_oc_file = data.oc_file.split(','); 
                    for (let i = 0; i < arr_oc_file.length; i++) {
                        aux_text += 
                        "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Compra' onclick='verpdf2(\"" + arr_oc_file[i] + "\",2)'>" + 
                            arr_oc_id[i] + 
                        "</a>";
                        if((i+1) < arr_oc_file.length){
                            aux_text += ",";
                        }
                    }
                }
                $('td', row).eq(5).html(aux_text);
                aux_text = "";
                if(data.notaventa_id != "" && data.notaventa_id != null){
                    let arr_notaventa_id = data.notaventa_id.split(','); 
                    for (let i = 0; i < arr_notaventa_id.length; i++){
                        aux_text += 
                        "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV(" + arr_notaventa_id[i] + ",1)'>" +
                            arr_notaventa_id[i] +
                        "</a>";
                        if((i+1) < arr_notaventa_id.length){
                            aux_text += ",";
                        }
                    }    
                }
                if(data.dteguiadespnv_notaventa_id != "" && data.dteguiadespnv_notaventa_id != null){
                    aux_text = 
                        "<a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV(" + data.dteguiadespnv_notaventa_id + ",1)'>" +
                            data.dteguiadespnv_notaventa_id +
                        "</a>";
                }

                $('td', row).eq(6).html(aux_text);
    
                aux_text = "";
                if(data.despachosol_id != "" && data.despachosol_id != null){
                    let arr_despachosol_id = data.despachosol_id.split(','); 
                    for (let i = 0; i < arr_despachosol_id.length; i++){
                        aux_text += 
                        "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Solicitud Despacho' onclick='genpdfSD(" + arr_despachosol_id[i] + ",1)'>" +
                            arr_despachosol_id[i] +
                        "</a>";
                        if((i+1) < arr_despachosol_id.length){
                            aux_text += ",";
                        }
                    }
                }
                $('td', row).eq(7).html(aux_text);
    
                aux_text = "";
                if(data.despachoord_id != "" && data.despachoord_id != null){
                    let arr_despachoord_id = data.despachoord_id.split(','); 
                    for (let i = 0; i < arr_despachoord_id.length; i++){
                        aux_text += 
                        `<a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="Orden Despacho" onclick="genpdfOD('${arr_despachoord_id[i]}',1)">
                            ${arr_despachoord_id[i]}
                        </a>`;
                        if((i+1) < arr_despachoord_id.length){
                            aux_text += `,`;
                        }
                    }    
                }
                $('td', row).eq(8).html(aux_text);
    
                aux_text = "";
                if(data.nrodocto_guiadesp != null){
                    let arr_nrodocto_guiadesp = data.nrodocto_guiadesp.split(','); 
                    for (let i = 0; i < arr_nrodocto_guiadesp.length; i++){
                        id_strgd = arr_nrodocto_guiadesp[i].toString();
                        id_strgd = data.nombrepdf_guiadesp + id_strgd.padStart(8, "0");
                        aux_text += 
                        `<a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="Guia Despacho" onclick="genpdfGD('${arr_nrodocto_guiadesp[i]}','')">
                            ${arr_nrodocto_guiadesp[i]}
                        </a>
                        <a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="Descargar XML Guia" onclick="descArcTXT('${id_strgd}.xml')">
                            <i class="fa fa-fw fa-cloud-download"></i>
                        </a>`;
                        if((i+1) < arr_nrodocto_guiadesp.length){
                            aux_text += `,`;
                        }
                    }
                }
                $('td', row).eq(9).attr("class","action-buttons");
                $('td', row).eq(9).html(aux_text);
    
                aux_text = "";
                if(data.nrodocto_guiadesp != null){
                    let arr_nrodocto_guiadespced = data.nrodocto_guiadesp.split(','); 
                    for (let i = 0; i < arr_nrodocto_guiadespced.length; i++){
                        aux_text += 
                        "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Guia Despacho cedible' onclick='genpdfGD(" + arr_nrodocto_guiadespced[i] + ",\"_cedible\")'>" +
                            arr_nrodocto_guiadespced[i] +
                        "</a>";
                        if((i+1) < arr_nrodocto_guiadespced.length){
                            aux_text += ",";
                        }
                    }    
                }
                $('td', row).eq(10).attr("class","action-buttons");
                $('td', row).eq(10).html(aux_text);

                let id_str = data.nrodocto.toString();
                id_str = data.nombrepdf + id_str.padStart(8, "0");
                aux_text = "";
                if(data.nrodocto != null){
                    aux_text = 
                    `<a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="Factura" onclick="genpdfFAC('${id_str}','')">
                        ${data.nrodocto}
                    </a>
                    <a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="Cedible" onclick="genpdfFAC('${id_str}','_cedible')">
                        <i class="fa fa-fw fa-file-pdf-o"></i>
                    </a>
                    <a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="Descargar XML Factura" onclick="descArcTXT('${id_str}.xml')">
                        <i class="fa fa-fw fa-cloud-download"></i>
                    </a>`;
                    
                    /* `<a id="stasubcob${data.id}" name="stasubcob${data.id}" onclick="volverSubirDteSisCob(${data.id})" class="btn-accion-tabla btn-sm tooltipsC" title="Subir DTE a Sistema Cobranza" data-toggle="tooltip"">
                        <span class="fa fa-upload text-yellow"></span>
                    </a>`; */
        ;
                }
                $('td', row).eq(11).attr("class","action-buttons");
                $('td', row).eq(11).html(aux_text);
                aux_checkstaex = "";
                if(data.staverfacdesp == 1){
                    aux_checkstaex = "checked";
                }
                /*
                aux_text = 
                `<div class="checkbox">
                    <label style="font-size: 1.2em;padding-left: 0px;">
                        <input type="hidden" id="staverfacdesp${data.id}" name="staverfacdesp${data.id}" value="${data.staverfacdesp}">
                        <input type="checkbox" class="checkstaex" id="aux_staverfacdesp${data.id}" name="aux_staverfacdesp${data.id}" onchange="clickstaverfacdesp(this)" item=${data.id} ${aux_checkstaex}>
                        <span class='cr'><i class='cr-icon fa fa-check'></i></span>
                    </label>
                </div>`;
                $('td', row).eq(12).html(aux_text);
                */

                $('td', row).eq(19).addClass('updated_at');
                $('td', row).eq(19).attr('id','updated_at' + data.id);
                $('td', row).eq(19).attr('name','updated_at' + data.id);

                $('td', row).eq(20).addClass('dtefac_updated_at');
                $('td', row).eq(20).attr('id','dtefac_updated_at' + data.id);
                $('td', row).eq(20).attr('name','dtefac_updated_at' + data.id);

            }
        });
    }

    totalizar();

    $("#btnconsultar").click(function()
    {
        data = datosFac();
        $('#tabla-data-consulta').DataTable().ajax.url( "/reportdtefac/reportdtefacpage/" + data.data2 ).load();
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

function datosFac(filtro = 0){
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
        giro_id           : $("#giro_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        tipoentrega_id    : $("#tipoentrega_id").val(),
        notaventa_id      : $("#notaventa_id").val(),
        aprobstatus       : $("#aprobstatus").val(),
        aprobstatusdesc   : $("#aprobstatus option:selected").html(),
        comuna_id         : $("#comuna_id").val(),
        dte_id            : $("#dte_id").val(),
        producto_id       : $("#producto_idPxP").val(),
        filtro            : 1,
        nrodocto          : $("#nrodocto").val(),
        statusgen         : 1,
        _token            : $('input[name=_token]').val()
    };
/*
    var data1 = {
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        rut               : eliminarFormatoRutret($("#rut").val()),
        vendedor_id       : $("#vendedor_id").val(),
        oc_id             : $("#oc_id").val(),
        giro_id           : $("#giro_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        tipoentrega_id    : $("#tipoentrega_id").val(),
        notaventa_id      : $("#notaventa_id").val(),
        aprobstatus       : $("#aprobstatus").val(),
        comuna_id         : $("#comuna_id").val(),
        dte_id       : $("#dte_id").val(),
        producto_id       : $("#producto_idPxP").val(),
        filtro            : 1,
        _token            : $('input[name=_token]').val()
    };
*/
    var data2 = "?fechad="+data1.fechad +
    "&fechah="+data1.fechah +
    "&rut="+data1.rut +
    "&sucursal_id="+data1.sucursal_id +
    "&centroeconomico_id="+data1.centroeconomico_id +
    "&vendedor_id="+data1.vendedor_id +
    "&oc_id="+data1.oc_id +
    "&giro_id="+data1.giro_id +
    "&areaproduccion_id="+data1.areaproduccion_id +
    "&tipoentrega_id="+data1.tipoentrega_id +
    "&notaventa_id="+data1.notaventa_id +
    "&aprobstatus="+data1.aprobstatus +
    "&aprobstatusdesc="+data1.aprobstatusdesc +
    "&comuna_id="+data1.comuna_id +
    "&dte_id="+data1.dte_id +
    "&producto_id="+data1.producto_id +
    "&filtro="+data1.filtro +
    "&nrodocto="+data1.nrodocto +
    "&statusgen="+data1.statusgen +
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
        data = datosFac();
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
    data = datosFac();
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
    data = datosFac();
    // Obtener todos los registros mediante una solicitud AJAX
    $.ajax({
        url: "/reportdtefac/reportdtefacpage/" + data.data2, // ajusta la URL de la solicitud al endpoint correcto
        type: 'POST',
        dataType: 'json',
        success: function(data) {
        //return 0;
        //console.log(data);
        if(data.data.length == 0){
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
        aux_rangofecha = $("#fechad").val() + " al " + $("#fechah").val()
        datosExcel.push(["Facturas Emitidas","","","","","","","","",fechaactual()]);
        datosExcel.push(["Centro Economico: " + aux_centroeconomicoNombre + " Entre: " + aux_rangofecha,"","","","","","","",""]);
        aux_totalkgtotal = 0;
        aux_totalmnttotal = 0;
        datosExcel.push(["","","","","","","","",""]);
        datosExcel.push(["NDoc","Fecha","RUT","Razon Social","Comuna","Kg","Monto","Estado","OC","NV"]);
        data.data.forEach(function(registro) {
            aux_totalkgtotal += registro.kgtotal;
            aux_totalmnttotal += registro.mnttotal;
            filainifusionar++;
            aux_fecha = new Date(registro.fchemis + " 00:00:00");

            aux_estado = "";
            if(registro.indtraslado == "6"){
                aux_estado = "Guia solo traslado";
            }
            if(registro.indtraslado == "1"){
                if(registro.dter_id == null){
                    aux_estado = "Pendiente de Fac";
                }else{
                    aux_estado = `Guia Fac (${registro.fact_nrodocto})`;
                }
            }
            if(registro.dteanul_obs != null){
                aux_estado = "Anulada";
            }
            var filaExcel = [
                registro.nrodocto, //"NDoc",
                fechaddmmaaaa(aux_fecha), //"Fecha",
                registro.rut, //"RUT",
                registro.razonsocial, //"Cliente",
                registro.nombre_comuna, //"CodProd",
                registro.kgtotal, //"Producto",
                registro.mnttotal, //"Ancho"
                aux_estado, //,"Largo"
                registro.oc_id, //,"Espesor",
                registro.notaventa_id
            ];
            aux_vendedor_id = registro.vendedor_id;
            count++;

            datosExcel.push(filaExcel);
        });
        /* if(aux_totalkgtotal != 0){
            datosExcel.push(["","","","","Total: ",aux_totalkgtotal,aux_totalmnttotal,"","",""]);
        } */
        datosExcel.push(["","","","","Total: ",aux_totalkgtotal,aux_totalmnttotal,"","",""]);

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
        cell.numFmt = "#,##0";
        }
    });

    // Establecer el formato de centrado horizontal y vertical para las celdas de la columna 8 desde la fila 4 hasta la fila 58
    for (let i = 4; i <= datosExcel.length; i++) {
        const cell8 = worksheet.getCell(i, 8);
        cell8.alignment = { horizontal: "center", vertical: "middle" };
        const cell9 = worksheet.getCell(i, 9);
        cell9.alignment = { horizontal: "center", vertical: "middle" };
        const cell10 = worksheet.getCell(i, 10);
        cell10.alignment = { horizontal: "center", vertical: "middle" };

        const cell = worksheet.getCell(i, 13);
        cell.alignment = { horizontal: "center", vertical: "middle" };

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
    for (let i = 1; i <= 6; i++) {
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
      a.download = "facturasEmitidas.xlsx";
      a.click();

      // Limpiar el objeto Blob
      window.URL.revokeObjectURL(url);
    });
}

function exportarExcelDTEDet() {
    data = datosFac();
    // Obtener todos los registros mediante una solicitud AJAX
    $.ajax({
        url: "/reportdtefac/listardtedet/" + data.data2, // ajusta la URL de la solicitud al endpoint correcto
        type: 'POST',
        dataType: 'json',
        success: function(data) {
        //console.log(data.datos);
        //return 0;
        if(data.datos.length == 0){
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
        aux_nrodocto = "";
        count = 0;

        cellLengthRazonSoc = 0;
        cellLengthProducto = 0;
        filainifusionar = -1
        arrayTituloDetalle = [];
        //console.log(data);
        aux_centroeconomicoNombre = $("#centroeconomico_id option:selected").html();
        if(aux_centroeconomicoNombre == "Seleccione..."){
            aux_centroeconomicoNombre = "";
        }
        aux_rangofecha = $("#fechad").val() + " al " + $("#fechah").val()
        datosExcel.push(["Informe Facturas Emitidas","","","","","","","","","","Fecha: "+fechaactual()]);
        datosExcel.push(["Centro Economico: " + aux_centroeconomicoNombre + " Entre: " + aux_rangofecha,"","","","","","","",""]);
        aux_totalkgtotal = 0;
        aux_totalmnttotal = 0;
        datosExcel.push(["","","","","","","","",""]);
        datosExcel.push(["NDoc","Fecha","RUT Razon Social","NV","Comuna","Kg","","Total+IVA","Estado","OC","Vendedor"]);
        data.datos.forEach(function(registro) {
            filainifusionar += 3;
            if (registro.nrodocto != aux_nrodocto){
                if(aux_nrodocto == ""){
                    filaTituloDet = 6;
                }else{
                    datosExcel.push(["","","","","","","","","",""]);
                    filaTituloDet += 3;
                }
                /*
                if(aux_vendedor_id == ""){
                    datosExcel.push(["","","","","","","","","","",""]);
                }else{
                    datosExcel.push(["","","","","","","Total: ",aux_totalMonto,"",aux_totalComision]);
                }*/
                aux_totalMonto = 0;
                aux_totalComision = 0;        
                //datosExcel.push(["","","","","","","","","",""]);
                aux_fecha = new Date(registro.fchemis + " 00:00:00");
                aux_estado = "";
                if(registro.indtraslado == "6"){
                    aux_estado = "Guia solo traslado";
                }
                if(registro.indtraslado == "1"){
                    if(registro.dter_id == null){
                        aux_estado = "Pendiente de Fac";
                    }else{
                        aux_estado = `Guia Fac (${registro.fact_nrodocto})`;
                    }
                }
                if(registro.dteanul_obs != null){
                    aux_estado = "Anulada";
                }
                datosExcel.push([
                                registro.nrodocto,
                                fechaddmmaaaa(aux_fecha),
                                registro.rut + " " + registro.razonsocial.substring(0, 35),
                                registro.notaventa_id,
                                registro.comuna_nombre,
                                registro.kgtotal,
                                "",
                                registro.mnttotal,
                                aux_estado,
                                registro.oc_id,
                                registro.vendedor_rut + " - " + registro.vendedor_nombre
                            ]);
                arrayTituloDetalle.push(filaTituloDet);
                datosExcel.push(["Ln","CodPro","Producto","Cant","UnidMed","Kg","P/U","Monto","","",""]);
                aux_ln = 1;

            }

            aux_totalkgtotal += registro.kgtotal;
            aux_totalmnttotal += registro.mnttotal;
            filainifusionar++;
            filaTituloDet++;

            var filaExcel = [
                aux_ln++, //"Ln",
                registro.producto_id, //"CodPro",
                registro.nmbitem, //"Producto",
                registro.qtyitem, //"Cant",
                registro.unmditem, //"Unid",
                registro.itemkg, //"Kg",
                registro.prcitem, //"P/U"
                registro.montoitem, //"Monto"
                "" //,"Desc",
            ];
            aux_nrodocto = registro.nrodocto;
            count++;

            datosExcel.push(filaExcel);
        });
        /*
        if(aux_totalkgtotal > 0){
            datosExcel.push(["","","","","Total: ",aux_totalkgtotal,aux_totalmnttotal,"",""]);
        }*/

        createExcelDet(datosExcel);

      },
      error: function(xhr, status, error) {
        console.log(error);
      }
    });


    // Llamar a la función para crear el archivo Excel

}

function createExcelDet(datosExcel) {
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
    ajustarcolumnaexcel(worksheet,"K");
    ajustarcolumnaexcel(worksheet,"L");
    ajustarcolumnaexcel(worksheet,"M");

    // Combinar celdas desde [4,0] hasta [4,2]
    arrayTituloDetalle.forEach(function(fila) {
        /*
        // Establecer negrita a totales
        row = worksheet.getRow(fila + 2 -2);
        for (let i = 1; i <= 10; i++) {
            cell = row.getCell(i);
            cell.font = { bold: true };
            cell.alignment = { horizontal: "right" };
            cell.numFmt = "#,##0";
        }

        row = worksheet.getRow(datosExcel.length);
        for (let i = 1; i <= 10; i++) {
            cell = row.getCell(i);
            cell.font = { bold: true };
            cell.alignment = { horizontal: "right" };
            cell.numFmt = "#,##0";
        }

        // Establecer negrita en la celda de Vendedor
        const row5 = worksheet.getRow(fila + 2);
        const cellA5 = row5.getCell(1);
        cellA5.font = { bold: true };
        */
        //Establecer negrita a todos los titulos del detalle
        const row6 = worksheet.getRow(fila);
        for (let i = 1; i <= 9; i++) {
            cell = row6.getCell(i);
            cell.font = { bold: true };
        }

        //Establecer alineacion a la derecha de titulos de detalle desde la columna 4 a la 8
        const row7 = worksheet.getRow(fila);
        for (let i = 4; i <= 8; i++) {
            cell = row7.getCell(i);
            cell.alignment = { horizontal: "right" };
        }
        cell = row7.getCell(1);
        cell.alignment = { horizontal: "center" };

        const row8 = worksheet.getRow(fila);
        cell = row8.getCell(5);
        cell.alignment = { horizontal: "left" };

        //Fusionar celdas de vendedor
        /*
        const startCol = 0;
        const endCol = 4;
        worksheet.mergeCells(fila + 2, startCol, fila + 2, endCol);*/
        
        // Establecer el formato de negrita en la celda superior izquierda del rango fusionado
    });


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
        cell.numFmt = "#,##0";
        }
    });

    // Recorrer la columna R y dar formato con punto para separar los miles
    const columnH = worksheet.getColumn(8);
    columnH.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });
    
    // Establecer el formato de centrado horizontal y vertical para las celdas de la columna 8 desde la fila 4 hasta la fila 58
    for (let i = 4; i <= datosExcel.length; i++) {
        /*
        const cell8 = worksheet.getCell(i, 8);
        cell8.alignment = { horizontal: "center", vertical: "middle" };
        */
        const cell1 = worksheet.getCell(i, 1);
        cell1.alignment = { horizontal: "center", vertical: "middle" };
        const cell2 = worksheet.getCell(i, 2);
        cell2.alignment = { horizontal: "center", vertical: "middle" };
        const cell4 = worksheet.getCell(i, 4);
        cell4.alignment = { horizontal: "center", vertical: "middle" };
        const cell9 = worksheet.getCell(i, 9);
        cell9.alignment = { horizontal: "center", vertical: "middle" };
        const cell10 = worksheet.getCell(i, 10);
        cell10.alignment = { horizontal: "center", vertical: "middle" };

        const cell = worksheet.getCell(i, 13);
        cell.alignment = { horizontal: "center", vertical: "middle" };

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
    cell = row2.getCell(11);
    cell.alignment = { horizontal: "center", vertical: "middle" };


    //Fusionar celdas de Titulo
    const startCol = 0;
    const endCol = 10;
    worksheet.mergeCells(1, startCol, 1, endCol);

    //Negrita Columna Sucursal
    const row3 = worksheet.getRow(2);
    cell = row3.getCell(1);
    cell.alignment = { horizontal: "center", vertical: "middle" };
    
    //Fusionar celdas Sucursal
    const startCol1 = 0;
    const endCol1 = 10;
    worksheet.mergeCells(2, startCol1, 2, endCol1);

    /* // Establecer negrita a totales
    row = worksheet.getRow(datosExcel.length);
    for (let i = 1; i <= 6; i++) {
        cell = row.getCell(i);
        cell.font = { bold: true };
        cell.alignment = { horizontal: "right" };
        cell.numFmt = "#,##0.00";
    } */
/*
    cell = row.getCell(7);
    cell.font = { bold: true };
    cell.alignment = { horizontal: "right" };
    cell.numFmt = "#,##0.00";

    cell = row.getCell(8);
    cell.font = { bold: true };
    cell.alignment = { horizontal: "right" };
    cell.numFmt = "#,##0";
*/

    // Guardar el archivo
    workbook.xlsx.writeBuffer().then(function(buffer) {
      // Crear un objeto Blob para el archivo Excel
      const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });

      // Crear un enlace de descarga
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = "guiasdespachoDet.xlsx";
      a.click();

      // Limpiar el objeto Blob
      window.URL.revokeObjectURL(url);
    });
}