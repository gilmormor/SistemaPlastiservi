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

/*
    consultar(datoslnv());
    $("#btnconsultar").click(function()
    {
        consultar(datoslnv());
    });

    $("#btnpdf1").click(function()
    {
        consultarpdf(datoslnv());
    });
*/
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


    configurarTabla1('#tabla-data-consulta');

    function configurarTabla1(aux_tabla){
        data = datoslnv1();
        $(aux_tabla).DataTable({
            'paging'      : true, 
            'lengthChange': true,
            'ordering'    : true,
            'info'        : true,
            'autoWidth'   : false,
            'processing'  : true,
            'serverSide'  : true,
            'ajax'        : "/despachosol/listarnvpage/" + data.data2, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
            "order": [[ 0, "desc" ]],
            'columns'     : [
                {data: 'id'},
                {data: 'fechahora'},
                {data: 'notaventa_aprobfechahora'},
                {data: 'razonsocial'},
                {data: 'oc_id'},
                {data: 'id'},
                {data: 'comunanombre'},
                {data: 'totalkilos'},
                {data: 'subtotal'},
                {data: 'id'},
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "createdRow": function ( row, data, index ) {
                $(row).attr('id','fila' + data.id);
                $(row).attr('name','fila' + data.id);

                aux_text = `<a class="btn-accion-tabla btn-sm tooltipsC" title="Nota de Venta PDF" onclick="genpdfNV(${data.id},1)">
                    ${data.id}
                </a>`
                $('td', row).eq(0).html(aux_text);
                $('td', row).eq(0).attr('data-order',data.id);

                $('td', row).eq(1).attr('data-order',data.fechahora);
                aux_fecha = new Date(data.fechahora);
                $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha) + " " + data.fechahora.substr(11, 8));
                $('td', row).eq(1).attr("style","font-size:12px");

                $('td', row).eq(2).attr('data-order',data.notaventa_aprobfechahora);
                aux_fecha = new Date(data.notaventa_aprobfechahora);
                $('td', row).eq(2).html(fechaddmmaaaa(aux_fecha) + " " + data.notaventa_aprobfechahora.substr(11, 8));
                $('td', row).eq(2).attr("style","font-size:12px");


                $('td', row).eq(3).attr("style","font-size:13px");
    
                if(data.oc_file != "" && data.oc_file != null){
                    aux_text = 
                        "<a class='btn-accion-tabla btn-sm tooltipsC' title='Ver Orden de Compra' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + 
                            data.oc_id + 
                        "</a>";
                    $('td', row).eq(4).html(aux_text);
                }

                aux_text = 
                `<a class="btn-accion-tabla btn-sm tooltipsC" title="Precio x Kg" onclick="genpdfNV(${data.id},2)">
                    <i class="fa fa-fw fa-file-pdf-o"></i>
                </a>`;
                $('td', row).eq(5).html(aux_text);

                aux_kgpend = data.totalkilos - data.totalkgsoldesp;
                aux_dinpend = data.subtotal - data.totalsubtotalsoldesp;
                $('td', row).eq(7).attr('class', "kgpend");
                $('td', row).eq(7).attr('data-order',aux_kgpend);
                $('td', row).eq(7).attr('data-search',aux_kgpend);
                $('td', row).eq(7).attr('style','text-align:right');
                $('td', row).eq(7).html(MASKLA(aux_kgpend,2));

                $('td', row).eq(8).attr('class', "dinpend");
                $('td', row).eq(8).attr('data-order',aux_dinpend);
                $('td', row).eq(8).attr('data-search',aux_dinpend);
                $('td', row).eq(8).attr('style','text-align:right');
                $('td', row).eq(8).html(MASKLA(aux_dinpend,0));

                aux_text = 
                `<a class="btn-accion-tabla btn-sm tooltipsC" title="Vista Previa SD" onclick="pdfSolDespPrev(${data.id},2)">
                    <i class='fa fa-fw fa-file-pdf-o'></i>                                    
                </a>`;

                if(data.clientebloqueado_desc != "" && data.clientebloqueado_desc != null){
                    aux_text += 
                        `<a class="btn-accion-tabla tooltipsC" title="Cliente Bloqueado: ${data.clientebloqueado_desc}">
                            <i class="fa fa-fw fa-lock text-danger"></i>
                        </a>`;
                }else{
                    aux_text +=
                    `<a href="${data.rutanuevasoldesp}" class="btn-accion-tabla tooltipsC enlace-soldesp" title="Hacer solicitud despacho: ${data.tipentnombre}">
                        <i class="fa fa-fw ${data.icono}"></i>
                    </a>`;
                }
                if(data.anulada != null && data.anulada != ""){
                    aux_text = "";
                    colorFila = 'background-color: #87CEEB;';
                    aux_data_toggle = "tooltip";
                    aux_title = "Anulada Fecha: " + data.anulada;
                    //$nuevoSolDesp = "";
                }
                $('td', row).eq(9).html(aux_text);
            }
        });
    }

    totalizar();

    $("#btnconsultar").click(function()
    {
        data = datoslnv1();
        $('#tabla-data-consulta').DataTable().ajax.url( "/despachosol/listarnvpage/" + data.data2 ).load();
        totalizar();
    });
});

function totalizar(){
    let  table = $('#tabla-data-consulta').DataTable();
    //console.log(table);
    table
        .on('draw', function () {
            eventFired( 'Page' );
        });
    data = datoslnv1();
    $.ajax({
        url: '/despachosol/totalizarlistarnvpage/' + data.data2,
        type: 'GET',
        success: function (datos) {
            $("#totalgenkg").html(MASKLA(datos.aux_kgpend,2));
            $("#totalgendin").html(MASKLA(datos.aux_dinpend,2));
        }
    });
}

var eventFired = function ( type ) {
	total = 0;
	$("#tabla-data-consulta tr .kgpend").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#totalkg").html(MASKLA(total,2))
	total = 0;
	$("#tabla-data-consulta tr .dinpend").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#totaldinero").html(MASKLA(total,0))
}

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
function configurarTabla2(aux_tabla){
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

function datoslnv(){
    var data = {
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
        plazoentrega      : $("#plazoentrega").val(),
        producto_id       : $("#producto_idPxP").val(),
        filtro            : 0,
        sucursal_id       : $("#sucursal_id").val(),
        _token            : $('input[name=_token]').val()
    };
    return data;
}

function datoslnv1(){
    aux_titulo ="";
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
        plazoentrega      : $("#plazoentrega").val(),
        producto_id       : $("#producto_idPxP").val(),
        sucursal_id       : $("#sucursal_id").val(),
        filtro            : 0,
        _token            : $('input[name=_token]').val()
    };

    data2 = "?fechad="+data1.fechad+
            "&fechah="+data1.fechah +
            "&rut=" + data1.rut +
            "&vendedor_id=" + data1.vendedor_id +
            "&oc_id=" + data1.oc_id +
            "&giro_id=" + data1.giro_id + 
            "&areaproduccion_id=" + data1.areaproduccion_id +
            "&tipoentrega_id=" + data1.tipoentrega_id +
            "&notaventa_id=" + data1.notaventa_id +
            "&aprobstatus=" + data1.aprobstatus +
            "&comuna_id=" + data1.comuna_id +
            "&plazoentrega=" + data1.plazoentrega +
            "&filtro=" + data1.filtro +
            "&producto_id=" + data1.producto_id +
            "&sucursal_id=" + data1.sucursal_id +
            "&aux_titulo=" + aux_titulo;
    
    var data = {
        data1 : data1,
        data2 : data2
    };
    return data;
}


function consultar(data){
    $.ajax({
        url: '/despachosol/reporte',
        type: 'POST',
        data: data,
        success: function (datos) {
            if(datos['tabla'].length>0){
                $("#tablaconsulta").html(datos['tabla']);
                $("#tablaconsulta2").html(datos['tabla2']);
                $("#tablaconsulta3").html(datos['tabla3']);

                configurarTabla('#tabla-data-listar1');
                let  table = $('#tabla-data-listar1').DataTable();
                //console.log(table);
                table
                    .on('draw', function () {
                        eventFired( 'Page' );
                    });
                configurarTabla('.tablascons');
                configurarTabla2('.tablascons2');
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

$("#btnpdf21").click(function()
{
    aux_titulo = 'Nota de Venta Pendientes';
    data = datoslnv();
    cadena = "?fechad="+data.fechad+
            "&fechah="+data.fechah +
            "&rut=" + data.rut +
            "&vendedor_id=" + data.vendedor_id +
            "&oc_id=" + data.oc_id +
            "&giro_id=" + data.giro_id + 
            "&areaproduccion_id=" + data.areaproduccion_id +
            "&tipoentrega_id=" + data.tipoentrega_id +
            "&notaventa_id=" + data.notaventa_id +
            "&aprobstatus=" + data.aprobstatus +
            "&comuna_id=" + data.comuna_id +
            "&plazoentrega=" + data.plazoentrega +
            "&filtro=" + data.filtro +
            "&producto_id=" + data.producto_id +
            "&aux_titulo=" + aux_titulo;
    $('#contpdf').attr('src', '/despachosol/pdfnotaventapendiente/'+cadena);
    $("#myModalpdf").modal('show');
});

function btnpdf(numrep){
    data = datoslnv();
    cadena = "?fechad="+data.fechad+
            "&fechah="+data.fechah +
            "&rut=" + data.rut +
            "&vendedor_id=" + data.vendedor_id +
            "&oc_id=" + data.oc_id +
            "&giro_id=" + data.giro_id + 
            "&areaproduccion_id=" + data.areaproduccion_id +
            "&tipoentrega_id=" + data.tipoentrega_id +
            "&notaventa_id=" + data.notaventa_id +
            "&aprobstatus=" + data.aprobstatus +
            "&comuna_id=" + data.comuna_id +
            "&plazoentrega=" + data.plazoentrega +
            "&producto_id=" + data.producto_id +
            "&filtro=" + data.filtro +
            "&numrep=" + numrep;
    if(numrep==1){
        aux_titulo = 'Nota de Venta Pendientes';
        cadena = cadena +
            "&aux_titulo=" + aux_titulo +
            "&aux_sql=1" + 
            "&aux_orden=1";
    }
    if(numrep==2){
        aux_titulo = 'Pendiente por Cliente y comuna';
        cadena = cadena +
            "&aux_titulo=" + aux_titulo +
            "&aux_sql=1" + 
            "&aux_orden=2";
    }
    if(numrep==3){
        aux_titulo = 'Pendiente por Producto';
        cadena = cadena +
            "&aux_titulo=" + aux_titulo +
            "&aux_sql=2" + 
            "&aux_orden=1";
    }
    $('#contpdf').attr('src', '/despachosol/pdfnotaventapendiente/'+cadena);
    $("#myModalpdf").modal('show');
}

/* $(document).on("click", ".enlace-soldesp", function(event){
    // Detenemos el comportamiento predeterminado del enlace
    event.preventDefault();
    // Aquí puedes obtener el href del enlace actual
    var href = $(this).attr("href");

    // Ejecutamos tu consulta jQuery aquí
    // Por ejemplo, aquí podrías hacer una consulta AJAX
    
    // Simulando una consulta AJAX
    swal({
        title: '¿Desea continuar?',
        text: "Esta acción no se puede deshacer!",
        icon: 'warning',
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
        // Si el usuario hace clic en "Aceptar", redirigimos
        if (value) {
            window.location.href = href;
        }
    });
}); */