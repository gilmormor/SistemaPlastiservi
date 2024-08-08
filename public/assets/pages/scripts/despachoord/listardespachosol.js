$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#auxeditcampoT").attr('maxlength',"100");
    $("#auxeditcampoT").attr('placeholder',"Ingrese Observacion");

    configurarTabla('#tabla-data-pendientesoldesp');

    function configurarTabla(aux_tabla){
        data = datosdespachosol();
        $(aux_tabla).DataTable({
            'paging'      : true, 
            'lengthChange': true,
            'ordering'    : true,
            'info'        : true,
            'autoWidth'   : false,
            'processing'  : true,
            'serverSide'  : true,
            'ajax'        : "/despachosol/listardespachosolpage/" + data.data2, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
            "order": [[ 1, "desc" ]],
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
                if ('datosAdicionales' in data) {
                    //console.log(data);
                    $("#totalkg").html(MASKLA(data.datosAdicionales.aux_totalkilos,2))
                    $("#totaldinero").html(MASKLA(data.datosAdicionales.aux_totaldinero,0));
                }

                $(row).attr('id','fila' + data.id);
                $(row).attr('name','fila' + data.id);    

                $('td', row).eq(0).attr('style','text-align:center');
                aux_text = 
                `<a class="btn-accion-tabla btn-sm tooltipsC" title="Solicitud de Despacho" onclick="genpdfSD(${data.id},1)">
                    ${data.id}
                </a>`;
                $('td', row).eq(0).html(aux_text);

                $('td', row).eq(1).attr('id','fechahora'+data.id);
                $('td', row).eq(1).attr('name','fechahora'+data.id);
                $('td', row).eq(1).attr('data-order',data.fechahora);
                aux_fecha = new Date(data.fechahora);
                $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));

                $('td', row).eq(2).attr('id','fechaestdespTD'+data.id);
                $('td', row).eq(2).attr('name','fechaestdespTD'+data.id);
                $('td', row).eq(2).attr('data-order',data.fechaestdesp);
                aux_fecha = new Date(data.fechaestdesp + " 00:00:00");

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

                aux_subtotalkilos = data.totalkilos - data.totalkilosdesp;

                $('td', row).eq(8).attr('class','kgpend');
                $('td', row).eq(8).attr('style','text-align:right');
                $('td', row).eq(8).attr('data-order',aux_subtotalkilos);
                $('td', row).eq(8).attr('data-search',aux_subtotalkilos);
                $('td', row).eq(8).html(MASKLA(aux_subtotalkilos, 2));

                aux_subtotal = data.subtotalsoldesp - data.subtotaldesp;
                $('td', row).eq(9).attr('class','dinpend');
                $('td', row).eq(9).attr('style','text-align:right');
                $('td', row).eq(9).attr('data-order',aux_subtotal);
                $('td', row).eq(9).attr('data-search',aux_subtotal);
                $('td', row).eq(9).html(MASKLA(aux_subtotal, 0));

                aux_text =
                `<a class="btn-accion-tabla btn-sm tooltipsC" title="Vista Previa" onclick="genpdfVPOD(${data.id},1)" style="display: inline-block;">
                    <i class='fa fa-fw fa-file-pdf-o'></i>
                </a>`;
                $('td', row).eq(10).html(aux_text);

                aux_ruta_crearord = $("#aux_ruta_crearord").val();
                aux_ruta = aux_ruta_crearord.substring(0, aux_ruta_crearord.length - 1);
                aux_solenvord = $("#solenvord").val();

                aux_clienteBloqueado = validarClienteBloqueadoxModulo(data);
                aux_displaybtnac = ``;
                aux_displaybtnbl = ``;
                if(aux_clienteBloqueado == ""){
                    aux_displaybtnac = ``;
                    aux_displaybtnbl = `style="display:none;"`;
                }else{
                    aux_displaybtnac = `style="display:none;"`;
                    aux_displaybtnbl = ``;
                }

                /* if(aux_clienteBloqueado == ""){
                    aux_text = `<a href="${aux_ruta + data.id}" target="_blank" class="btn-accion-tabla tooltipsC" title="Hacer orden despacho: ${data.tipentnombre}">
                                        <button type="button" class="btn btn-default btn-xs">
                                            <i class="fa fa-fw ${data.icono}"></i>
                                        </button>
                                    </a>`;
                    aux_text = `<a onclick="crearord('${data.id}','${aux_ruta + aux_solenvord + '-' + data.id}','${data.updated_at}')" class="btn-accion-tabla tooltipsC" title="Hacer orden despacho: ${data.tipentnombre}" style="display: inline-block;">
                                    <button type="button" class="btn btn-default btn-xs">
                                        <i class="fa fa-fw ${data.icono}"></i>
                                    </button>
                                </a>`;
                    if($("#solenvord").val() == '0'){
                        data.nuevoOrdDesp.forEach(function(nuevoOrdDesp, index) {
                            aux_text += `<a href="${nuevoOrdDesp.a_href}" fila="${nuevoOrdDesp.a_fila}" id="btnanular${nuevoOrdDesp.a_fila}" name="btnanular${nuevoOrdDesp.a_fila}" class="${nuevoOrdDesp.a_class}" title="${nuevoOrdDesp.a_title}" data-toggle="tooltip" style="display: inline-block;" updated_at="${data.updated_at}">
                                            <button type='button' class="${nuevoOrdDesp.b_class}"><i class="${nuevoOrdDesp.i_class}"></i></button>
                                        </a>`;
                            });    
                    }
                }else{
                    aux_text = `<a class="btn-accion-tabla tooltipsC" title="Cliente Bloqueado: ${aux_clienteBloqueado}" style="display: inline-block;">
                                    <button type="button" class="btn btn-default btn-xs" disabled>
                                        <i class="fa fa-fw fa-lock text-danger"></i>
                                    </button>
                                </a>`;
                } */

                aux_text = 
                        `<a ${aux_displaybtnbl} class="btn-accion-tabla tooltipsC botonbloq${data.id}" title="Cliente Bloqueado: ${aux_clienteBloqueado}" style="display: inline-block;" onclick="llenartablaDataCobranza(${data.id},${data.cliente_id},${data.notaventa_id},0)">
                            <button type="button" class="btn btn-default btn-xs">
                                <i class="fa fa-fw fa-lock text-danger"></i>
                            </button>
                        </a><a ${aux_displaybtnac} onclick="crearord('${data.id}','${aux_ruta + aux_solenvord + '-' + data.id}','${data.updated_at}')" class="btn-accion-tabla tooltipsC botonac${data.id}" title="Hacer orden despacho: ${data.tipentnombre}" style="display: inline-block;">
                            <button type="button" class="btn btn-default btn-xs">
                                <i class="fa fa-fw ${data.icono}"></i>
                            </button>
                        </a>`;
                if($("#solenvord").val() == '0'){
                    data.nuevoOrdDesp.forEach(function(nuevoOrdDesp, index) {
                        aux_text += `<a href="${nuevoOrdDesp.a_href}" fila="${nuevoOrdDesp.a_fila}" id="btnanular${nuevoOrdDesp.a_fila}" name="btnanular${nuevoOrdDesp.a_fila}" class="${nuevoOrdDesp.a_class} botonac${data.id}" title="${nuevoOrdDesp.a_title}" data-toggle="tooltip" style="display: inline-block;" updated_at="${data.updated_at}">
                                        <button type='button' class="${nuevoOrdDesp.b_class}"><i class="${nuevoOrdDesp.i_class}"></i></button>
                                    </a>`;
                        });    
                }

                /* if(data.clientebloqueado_descripcion !== null){
                    aux_text = `<a class="btn-accion-tabla tooltipsC" title="Cliente Bloqueado: ${data.clientebloqueado_descripcion}" style="display: inline-block;">
                                        <button type="button" class="btn btn-default btn-xs" disabled>
                                            <i class="fa fa-fw fa-lock text-danger"></i>
                                        </button>
                                    </a>`;
                }else{
                    aux_text = `<a href="${aux_ruta + data.id}" target="_blank" class="btn-accion-tabla tooltipsC" title="Hacer orden despacho: ${data.tipentnombre}">
                                        <button type="button" class="btn btn-default btn-xs">
                                            <i class="fa fa-fw ${data.icono}"></i>
                                        </button>
                                    </a>`;
                    aux_text = `<a onclick="crearord('${data.id}','${aux_ruta + aux_solenvord + '-' + data.id}','${data.updated_at}')" class="btn-accion-tabla tooltipsC" title="Hacer orden despacho: ${data.tipentnombre}" style="display: inline-block;">
                                    <button type="button" class="btn btn-default btn-xs">
                                        <i class="fa fa-fw ${data.icono}"></i>
                                    </button>
                                </a>`;
                    if($("#solenvord").val() == '0'){
                        data.nuevoOrdDesp.forEach(function(nuevoOrdDesp, index) {
                            aux_text += `<a href="${nuevoOrdDesp.a_href}" fila="${nuevoOrdDesp.a_fila}" id="btnanular${nuevoOrdDesp.a_fila}" name="btnanular${nuevoOrdDesp.a_fila}" class="${nuevoOrdDesp.a_class}" title="${nuevoOrdDesp.a_title}" data-toggle="tooltip" style="display: inline-block;" updated_at="${data.updated_at}">
                                            <button type='button' class="${nuevoOrdDesp.b_class}"><i class="${nuevoOrdDesp.i_class}"></i></button>
                                        </a>`;
                            });    
                    }
                } */
                if($("#solenvord").val() == '1'){
                    /* aux_text += `<a onclick="delsolenvord(${data.id},'${data.updated_at}')" class="btn-accion-tabla tooltipsC" title="Eliminar de listado. Solo elimina del listado no afecta Stock picking." style="display: inline-block;">
                                    <button type="button" class="btn btn-default btn-xs">
                                        <i class="fa fa-fw fa-trash-o text-danger"></i>
                                    </button>
                                </a>`; */
                    aux_text += `<a onclick="delsolenvord1(${data.id},'${data.updated_at}')" class="btn-accion-tabla tooltipsC" fila="${data.id}" title="Eliminar de listado. Solo elimina del listado no afecta Stock picking." style="display: inline-block;">
                                    <button type="button" class="btn btn-default btn-xs">
                                        <i class="fa fa-fw fa-trash-o text-danger"></i>
                                    </button>
                                </a>`;
            }
                $('td', row).eq(11).html(aux_text);
                
            }
        });
    }

    
    $('#tabla-data-pendientesoldesp').on('draw.dt', function () {
        // Aquí puedes ejecutar la función que deseas que se ejecute cuando se termine de llenar la tabla
        // Llamar a tu función aquí
        eventFired1( 'Page' );
    });

    var eventFired1 = function ( type ) {
        total = 0;
        $("#tabla-data-pendientesoldesp tr .kgpend").each(function() {
            valor = $(this).attr('data-order') ;
            valorNum = parseFloat(valor);
            total += valorNum;
        });
        $("#subkgpend").html(MASKLA(total,2))
        total = 0;
        $("#tabla-data-pendientesoldesp tr .dinpend").each(function() {
            valor = $(this).attr('data-order') ;
            valorNum = parseFloat(valor);
            total += valorNum;
        });
        $("#subtotaldinero").html(MASKLA(total,0))
    }


    //consultar(datoslsd());
    $("#btnconsultar").click(function()
    {
        //consultar(datoslsd());
        data = datosdespachosol();
        $("#totalkg").html("0,00")
        $("#totaldinero").html("0");

        $('#tabla-data-pendientesoldesp').DataTable().ajax.url( "/despachosol/listardespachosolpage/" + data.data2 ).load();

    });

    //consultar(datoslsd());
    /* $("#btnconsultar").click(function()
    {
        consultar(datoslsd());
    }); */



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
					$("#fila"+aux_data.nfila).remove();
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
					//$("#fila"+aux_data.nfila).remove();
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
                    $("#fila"+aux_data.nfila).remove();
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
                if ('error' in respuesta){
                    if (respuesta.error == 0){
                        $("#fila"+aux_data.nfila).remove();
                    }
                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                }else{
                    if (respuesta.mensaje == "ok") {
                        //form.parents('tr').remove();
                        $("#fila"+aux_data.nfila).remove();
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
                }
                $("#myModaldevsoldeps").modal('hide');
            }
            if(funcion=="guardarfechaed"){
                Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                restbotoneditfeced(aux_data.i)
                if(respuesta.error == 0){
                    $("#fechaestdesp" + aux_data.i).html($("#fechaed" + aux_data.i).val());
                    $("#savefed" + aux_data.i).attr('updated_at',respuesta.updated_at);
                    $("#fechaestdespTD" + aux_data.i).attr('data-order',respuesta.fechaestdesp);
                }
            }
            if(funcion=="delsolenvord"){
                if (respuesta.error == 0) {
                    //form.parents('tr').remove();
                    $("#myModalEditarCampoTex").modal('hide')
                    $("#fila"+aux_data.despachosol_id).remove();
                }
                Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
            }
            if(funcion=="validarregmod"){
                if (respuesta.error == 0) {
					window.location = aux_data.ruta;
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
        sololectura       : $("#sololectura").val(),
        solenvord         : $("#solenvord").val(),
        statusBloqueo     : $("#statusBloqueo").val(),
        _token            : $('input[name=_token]').val()
    };
    return data;
}

function datosdespachosol(){
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
        sololectura       : $("#sololectura").val(),
        solenvord         : $("#solenvord").val(),
        statusBloqueo     : $("#statusBloqueo").val(),
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
    "&sololectura="+data1.sololectura +
    "&solenvord="+data1.solenvord +
    "&statusBloqueo="+data1.statusBloqueo +
    "&_token="+data1._token;

    var data = {
        data1 : data1,
        data2 : data2
    };
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
    //console.log($(this).attr("updated_at"))
    id = fila.find('td:eq(0)').text();
    $('.modal-title').html('Devolver Solicitud Despacho');
    $("#despachosol_id").val(id);
    $("#nfilaDel").val(form.attr('fila'));
    $("#btnGuardarDSD").attr("fila_id",form.attr('fila'));
    $("#btnGuardarDSD").attr("funcion",'btndevsol');
    $("#ruta").val(form.attr('href'));
    $("#observacion").val("");
    $("#status").val("1");
    $("#boton").val("btndevsol");
    $("#status").attr("updated_at",$(this).attr("updated_at"));
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
    $("#btnGuardarDSD").attr("fila_id",form.attr('fila'));
    $("#btnGuardarDSD").attr("funcion",'btncerrarsol');
    $("#ruta").val(form.attr('href'));
    $("#observacion").val("");
    $("#status").val("2");
    $("#status").attr("updated_at",$(this).attr("updated_at"));
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
            if ($("#status[updated_at]").length > 0) {
                // El elemento con el atributo "updated_at" existe dentro de #status
                // Puedes realizar acciones relacionadas con su existencia aquí
                aux_updated_at = $("#status").attr("updated_at");
            } else {
                // El elemento con el atributo "updated_at" no existe dentro de #status
                aux_updated_at = "";
            }
            var data = {
                id     : $("#btnGuardarDSD").attr("fila_id"),
                nfila  : $("#btnGuardarDSD").attr("fila_id"), //$("#nfilaDel").val(),
                obs    : $("#observacion").val(),
                status : $("#status").val(),
                updated_at : aux_updated_at,
                _token : $('input[name=_token]').val()
            };
            if($("#btnGuardarDSD").attr("funcion")){
                form = $("#btnGuardarDSD").attr("funcion");
            }
            //console.log(data);
            //return 0;
            //console.log($("#boton").val());
            //console.log($("#ruta").val());
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
    aux_orden = (ordentablaGen($('#pendientesoldesp').DataTable()));
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
            "&orden=" + ordentablaGen($('#pendientesoldesp').DataTable()) +
            "&sucursal_id=" + data.sucursal_id +
            "&sololectura=" + data.sololectura +
            "&solenvord=" + data.solenvord +
            "&statusBloqueo=" + data.statusBloqueo +
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

function delsolenvord(id,updated_at){
    swal({
        title: '¿Eliminar del listado?',
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
            ruta = '/despachoord/delsolenvord';
            ajaxRequest(data,ruta,"delsolenvord");
        }
    });
}

function crearord(id,aux_ruta,updated_at){
    var data = {
        despachosol_id : id,
        updated_at : updated_at,
        ruta       : aux_ruta,
        _token: $('input[name=_token]').val()
    };
    ruta = '/despachosol/validarregmod';
    ajaxRequest(data,ruta,"validarregmod");
}
function delsolenvord1(id,updated_at){
    //$("#auxeditcampoT").attr('aux_nomcampot',$(this).attr('nomcampo'));
    $("#auxeditcampoT").attr("updated_at",updated_at)
    $("#titeditarcampo").html("Mensaje");
    $("#lbleditarcampo").html("Observacion:");
    $("#auxeditcampoT").attr('fila_id',id);
    $("#auxeditcampoT").val("");
    $("#myModalEditarCampoTex").modal('show');
}

$("#btnaceptarMT").click(function(event){
	event.preventDefault();
	$("#auxeditcampoN").val(1);
	if(verificarDato(".valorrequerido"))
	{
		let auxeditcampoT = $("#auxeditcampoT").val();
		//esto es para reemplazar el caracter comilla doble " de la cadena, para evitar que me trunque los valores en javascript al asignar a attr val 
		auxeditcampoT = auxeditcampoT.replaceAll('"', "'");
		id = $("#auxeditcampoT").attr('fila_id');
        swal({
            title: '¿Eliminar del listado?',
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
                    obs        : auxeditcampoT,
                    updated_at : $("#auxeditcampoT").attr("updated_at"),
                    _token: $('input[name=_token]').val()
                };
                ruta = '/despachoord/delsolenvord';
                ajaxRequest(data,ruta,"delsolenvord");
            }
        });
    
        //$("#myModalEditarCampoTex").modal('hide');
	}else{
		alertify.error("Falta incluir informacion");
	}
});

function verificarDato(aux_nomclass)
{
	aux_resultado = true;
	$(aux_nomclass).serializeArray().map(function(x){
		aux_tipoval = $("#" + x.name).attr('tipoval');
		if (validacion(x.name,aux_tipoval) == false)
		{
			//return false;
			aux_resultado = false;

		}else{
			//return true;
		}
	});
	return aux_resultado;
}
