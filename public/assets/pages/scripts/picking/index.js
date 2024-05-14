$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    configurarTabla('#tabla-data-picking');

    function configurarTabla(aux_tabla){
        data = datospicking();
        $(aux_tabla).DataTable({
            'paging'      : true, 
            'lengthChange': true,
            'ordering'    : true,
            'info'        : true,
            'autoWidth'   : false,
            'processing'  : true,
            'serverSide'  : true,
            'ajax'        : "/pickingpage/" + data.data2, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
            "order": [[ 0, "desc" ]],
            'columns'     : [
                {data: 'id'},
                {data: 'fechahora'},
                {data: 'fechaestdesp'},
                {data: 'razonsocial'},
                {data: 'sucursal_nombre'},
                {data: 'oc_id'},
                {data: 'notaventa_id'},
                {data: 'comunanombre'},
                {data: 'totalkilos'},
                {data: 'totalkilospicking'},
                {data: 'totalcantpicking'},
                {data: 'id'},
                {data: 'icono'},
                {data: 'icono'},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "createdRow": function ( row, data, index ) {
                /*
                if(data.stock <= 0){
                    $(row).hide();                    
                }*/
                $(row).attr('id','fila' + data.id);
                $(row).attr('name','fila' + data.id);    
                $('td', row).eq(0).attr('style','text-align:center');
                aux_text = 
                `<a class="btn-accion-tabla btn-sm tooltipsC" title="Solicitud de Despacho" onclick="genpdfSD(${data.id},1)">
                    ${data.id}
                </a>`;
                $('td', row).eq(0).html(aux_text);

                $('td', row).eq(1).attr('data-order',data.fechahora);
                aux_fecha = new Date(data.fechahora);
                $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));

                $('td', row).eq(2).attr('data-order',data.fechaestdesp);
                aux_fecha = new Date(data.fechaestdesp + " 00:00:00");

                if(data.despachosolenvorddesp_obs != "" && data.despachosolenvorddesp_obs != null){
                    aux_text = data.razonsocial +
                    " <a class='btn-sm tooltipsC' title='" + data.despachosolenvorddesp_obs + "'>" +
                        "<i class='fa fa-fw fa-question-circle text-red'></i>" + 
                    "</a>";
                    $('td', row).eq(3).html(aux_text);
                }
    

                aux_text = 
                `<a id="fechaestdesp${data.id}" name="fechaestdesp${data.id}" class="editfed">
                    ${fechaddmmaaaa(aux_fecha)}
                </a>
                <input type="text" class="form-control datepickerfed savefed" name="fechaed${data.id}" id="fechaed${data.id}" value="${fechaddmmaaaa(aux_fecha)}" style="display:none; width: 70px; height: 21.6px;padding-left: 0px;padding-right: 0px;" readonly>
                <a name="editfed${data.id}" id="editfed${data.id}" class="btn-accion-tabla btn-sm tooltipsC editfed" title="Editar Fecha ED" onclick="editfeced(${data.id},${data.id})">
                    <i class="fa fa-fw fa-pencil-square-o"></i>
                </a>
                <a name="savefed${data.id}" id="savefed${data.id}" class="btn-accion-tabla btn-sm tooltipsC savefed" title="Guardar Fecha ED" onclick="savefeced(${data.id},${data.id})" style="display:none" updated_at="${data.updated_at}">
                    <i class="fa fa-fw fa-save text-red"></i>
                </a>`;
                $('td', row).eq(2).html(aux_text);

                if(data.razonsocial.length > 30){
                    $('td', row).eq(3).attr('class',"btn-accion-tabla");
                    $('td', row).eq(3).attr('title',data.razonsocial);
                    $('td', row).eq(3).html(data.razonsocial.substring(0, 30));    
                }

                if(data.oc_file == "" ||  data.oc_file === null){
                    aux_enlaceoc = "";
                }else{
                    aux_enlaceoc = `<a onclick="verpdf2('${data.oc_file}',2)" class="btn-accion-tabla btn-sm tooltipsC" title="Orden de Compra">${data.oc_id}</a>`;
                }
                $('td', row).eq(5).html(aux_enlaceoc);

                aux_text =
                `<a class="btn-accion-tabla btn-sm tooltipsC" title="Nota de Venta" onclick="genpdfNV(${data.notaventa_id},1)">
                    ${data.notaventa_id}
                </a>`;
                $('td', row).eq(6).html(aux_text);

                aux_totalkilos = data.totalkilos - data.totalkilosdesp;

                $('td', row).eq(8).attr('class','kgpend');
                $('td', row).eq(8).attr('style','text-align:right');
                $('td', row).eq(8).attr('data-order',aux_totalkilos);
                $('td', row).eq(8).attr('data-search',aux_totalkilos);
                $('td', row).eq(8).html(MASKLA(aux_totalkilos, 2));

                $('td', row).eq(9).attr('class','kgpend');
                $('td', row).eq(9).attr('style','text-align:right');
                $('td', row).eq(9).attr('data-order',data.totalkilospicking);
                $('td', row).eq(9).attr('data-search',data.totalkilospicking);
                $('td', row).eq(9).html(MASKLA(data.totalkilospicking, 2));

                $('td', row).eq(10).attr('class','kgpend');
                $('td', row).eq(10).attr('style','text-align:right');
                $('td', row).eq(10).attr('data-order',data.totalcantpicking);
                $('td', row).eq(10).attr('data-search',data.totalcantpicking);
                $('td', row).eq(10).html(MASKLA(data.totalcantpicking, 2));


                aux_subtotal = data.subtotalsoldesp - data.subtotaldesp;
                $('td', row).eq(11).attr('class','dinpend');
                $('td', row).eq(11).attr('style','text-align:right');
                $('td', row).eq(11).attr('data-order',aux_subtotal);
                $('td', row).eq(11).attr('data-search',aux_subtotal);
                $('td', row).eq(11).html(MASKLA(aux_subtotal, 0));

                aux_text =
                `<a class="btn-accion-tabla btn-sm tooltipsC" title="Vista Previa" onclick="genpdfVPOD(${data.id},1)">
                    <i class='fa fa-fw fa-file-pdf-o'></i>
                </a>`;
                $('td', row).eq(12).html(aux_text);

                aux_ruta_crearord = $("#aux_ruta_crearord").val();
                aux_ruta = aux_ruta_crearord.substring(0, aux_ruta_crearord.length - 1);
                if(data.clientebloqueado_descripcion !== null){
                    aux_text = `<a class="btn-accion-tabla tooltipsC" title="Cliente Bloqueado: ${data.clientebloqueado_descripcion}">
                                        <button type="button" class="btn btn-default btn-xs" disabled>
                                            <i class="fa fa-fw fa-lock text-danger"></i>
                                        </button>
                                    </a>`;    
                }else{
                    aux_text = `<a onclick="validareditarpicking(${data.id},'${data.updated_at}','${aux_ruta + data.id}')" class="btn-accion-tabla tooltipsC" title="Editar Picking">
                                    <button type="button" class="btn btn-default btn-xs">
                                        <i class="fa fa-fw ${data.icono}"></i>
                                    </button>
                                </a>
                                <a id="enviardespord${data.id}" name="enviardespord${data.id}" class="btn-accion-tabla tooltipsC" title="Enviar SolDesp a OrdDesp" item="${data.id}" value="0" onclick="enviardespord(${data.id},'${data.updated_at}')">
                                    <button type="button" class="btn btn-default btn-xs">
                                        <i class="fa fa-fw fa-arrow-right text-yellow"></i>
                                    </button>
                                </a>`;

                    /* aux_text = `<a href="${aux_ruta + data.id}" target="_blank" class="btn-accion-tabla tooltipsC" title="Editar Picking">
                                    <button type="button" class="btn btn-default btn-xs">
                                        <i class="fa fa-fw ${data.icono}"></i>
                                    </button>
                                </a>
                                <a id="enviardespord${data.id}" name="enviardespord${data.id}" class="btn-accion-tabla tooltipsC" title="Enviar SolDesp a OrdDesp" item="${data.id}" value="0" onclick="enviardespord(${data.id},'${data.updated_at}')">
                                    <button type="button" class="btn btn-default btn-xs">
                                        <i class="fa fa-upload text-yellow"></i>
                                    </button>
                                </a>`; */
                }
                $('td', row).eq(13).html(aux_text);
                
            }
        });
    }

    //consultar(datoslsd());
    $("#btnconsultar").click(function()
    {
        //consultar(datoslsd());
        data = datospicking();
        $('#tabla-data-picking').DataTable().ajax.url( "/pickingpage/" + data.data2 ).load();

    });



    $("#btnpdf1").click(function()
    {
        consultarpdf(datoslsd());
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
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
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
                if (respuesta.status == "1") {
                    //form.parents('tr').remove();
                    $("#fila"+data['nfila']).remove();
                    Biblioteca.notificaciones('El registro fue procesado correctamente.', 'Plastiservi', 'success');
                } else {
                    swal({
                        title: respuesta.title,
                        text: respuesta.mensaje,
                        icon: respuesta.tipo_alert,
                        buttons: {
                            cancel: "Cerrar",
                        },
                    });
                    return 0;
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
            if(funcion=="enviardespord"){
                if (respuesta.error == 0) {
                    //form.parents('tr').remove();
                    $("#fila"+aux_data.despachosol_id).remove();
                }
                Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
            }
            if(funcion=="validareditarpicking"){
                if (respuesta.error == 0) {
					//var loc = window.location;
    				//window.location = aux_data.ruta;
                    window.open(aux_data.ruta, '_blank');
                }else{
                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                }
            }

		},
		error: function () {
		}
	});
}

function datoslsd(){
    var data = {
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        fechaestdesp      : $("#fechaestdesp").val(),
        rut               : eliminarFormatoRutret($("#rut").val()),
        vendedor_id       : $("#vendedor_id").val(),
        oc_id             : $("#oc_id").val(),
        giro_id           : $("#giro_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        tipoentrega_id    : $("#tipoentrega_id").val(),
        notaventa_id      : $("#notaventa_id").val(),
        aprobstatus       : $("#aprobstatus").val(),
        comuna_id         : $("#comuna_id").val(),
        id                : $("#id").val(),
        producto_id       : $("#producto_idPxP").val(),
        filtro            : 1,
        sucursal_id       : $("#sucursal_id").val(),
        sta_picking       : $("#sta_picking").val(),
        _token            : $('input[name=_token]').val()
    };
    return data;
}

function datospicking(){
    var data1 = {
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        fechaestdesp      : $("#fechaestdesp").val(),
        rut               : eliminarFormatoRutret($("#rut").val()),
        vendedor_id       : $("#vendedor_id").val(),
        oc_id             : $("#oc_id").val(),
        giro_id           : $("#giro_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        tipoentrega_id    : $("#tipoentrega_id").val(),
        notaventa_id      : $("#notaventa_id").val(),
        aprobstatus       : $("#aprobstatus").val(),
        comuna_id         : $("#comuna_id").val(),
        id                : $("#id").val(),
        producto_id       : $("#producto_idPxP").val(),
        filtro            : 1,
        sucursal_id       : $("#sucursal_id").val(),
        sta_picking       : $("#sta_picking").val(),
        _token            : $('input[name=_token]').val()
    };

    var data2 = "?fechad="+data1.fechad +
    "&fechah="+data1.fechah +
    "&fechaestdesp="+data1.fechaestdesp +
    "&rut="+data1.rut +
    "&vendedor_id="+data1.vendedor_id +
    "&oc_id="+data1.oc_id +
    "&giro_id="+data1.giro_id +
    "&areaproduccion_id="+data1.areaproduccion_id +
    "&tipoentrega_id="+data1.tipoentrega_id +
    "&notaventa_id="+data1.notaventa_id +
    "&aprobstatus="+data1.aprobstatus +
    "&comuna_id="+data1.comuna_id +
    "&id="+data1.id +
    "&producto_id="+data1.producto_id +
    "&filtro="+data1.filtro +
    "&sucursal_id="+data1.sucursal_id +
    "&sta_picking="+data1.sta_picking +
    "&_token="+data1._token;

    var data = {
        data1 : data1,
        data2 : data2
    };
    return data;
}

function consultar(data){
    $.ajax({
        url: '/picking/reportesoldesp',
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
            //console.log($("#boton").val());
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
        data = datoslsd();
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
    data = datoslsd();
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
            "&filtro=" + data.filtro +
            "&producto_id=" + data.producto_id +
            "&aux_titulo=" + aux_titulo;
    $('#contpdf').attr('src', '/despachosol/pdfpendientesoldesp/'+cadena);
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


function enviardespord(id,updated_at){
    /* estaSeleccionado = $("#enviardespord" + id).is(":checked");
    if (estaSeleccionado) {
        aux_estado = 1
    } else {
        aux_estado = 0
    } */
    swal({
        title: '¿Enviar registro a Orden de Despacho?',
        text: "Esta acción no se puede deshacer!",
        icon: 'warning',
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
        if (value) {
            var data = {
                despachosol_id : id,
                updated_at : updated_at,
                _token: $('input[name=_token]').val()
            };
            ruta = '/picking/enviardespord';
            ajaxRequest(data,ruta,"enviardespord");    
        }
    });
}

function validareditarpicking(id,updated_at,aux_ruta){
    var data = {
        despachosol_id : id,
        updated_at : updated_at,
        ruta       : aux_ruta,
        _token: $('input[name=_token]').val()
    };
    ruta = '/picking/validareditarpicking';
    ajaxRequest(data,ruta,"validareditarpicking");
}