$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    //consultar(datosPentxProd());
    $("#btnconsultar").click(function()
    {
        consultar(datosPentxProd());
    });

    $("#btnpdf1").click(function()
    {
        consultarpdf(datosPentxProd());
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

    configurarTabla('#tabla-data-consulta');

    function configurarTabla(aux_tabla){
        datax = datosPentxProd();
        console.log(datax);
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
            'ajax'        : "/reportpendxprod/reportpendxprodpage/" + datax.data2, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
            'columns'     : [
                {data: 'notaventa_id'}, // 0
                {data: 'oc_id'}, // 1
                {data: 'fechahora'}, // 2
                {data: 'plazoentrega'}, // 3
                {data: 'razonsocial'}, // 4
                {data: 'comunanombre'}, // 5
                {data: 'producto_id'}, // 6
                {data: 'nombre'}, // 7
                {data: 'nombre'}, // 8
                {data: 'nombre'}, // 9
                {data: 'nombre'}, // 10
                {data: 'tipounion'}, // 11
                {data: 'tipounion'}, // 12
                {data: 'cant'}, // 13
                {data: 'cant'}, //14
                {data: 'cant'}, //15
                {data: 'cant'}, //16
                {data: 'cant'}, //17
                {data: 'cant'}, //18
                {data: 'cant'} //19
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "createdRow": function ( row, data, index ) {
                $(row).attr('id','fila' + data.id);
                $(row).attr('name','fila' + data.id);
                aux_text = 
                `<a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV(${data.notaventa_id},1)'>
                    ${data.notaventa_id}
                </a>`;
                $('td', row).eq(0).html(aux_text);

                aux_text = "";
                if(data.oc_id != null){
                    aux_text = 
                    `<a class="btn-accion-tabla btn-sm tooltipsC" title="" onclick="verpdf2('${data.oc_file}',2)" data-original-title="Orden de Compra">
                        ${data.oc_id}
                    </a>`;    
                }
                $('td', row).eq(1).html(aux_text);

                $('td', row).eq(2).attr('data-order',data.fechahora);
                aux_fecha = new Date(data.fechahora);
                $('td', row).eq(2).html(fechaddmmaaaa(aux_fecha));

                $('td', row).eq(3).attr('data-order',data.plazoentrega);
                aux_fecha = new Date(data.plazoentrega + " 00:00:00");
                $('td', row).eq(3).html(fechaddmmaaaa(aux_fecha));

                aux_text = `${data.producto_id}`;
                if(data.acuerdotecnico_id != null){
                    aux_text = 
                    `<a class="btn-accion-tabla btn-sm tooltipsC" title="" onclick="genpdfAcuTec(${data.acuerdotecnico_id},${data.cliente_id},1)" data-original-title="Acuerdo Técnico PDF">
                        ${data.producto_id}
                    </a>`;
                }
                $('td', row).eq(6).html(aux_text);
                


                /*
                if (data.dteanul_obs != null) {
                    aux_fecha = new Date(data.dteanulcreated_at);
                    aux_text = data.id +
                    "<a class='btn-accion-tabla tooltipsC' title='Anulada " + fechaddmmaaaa(aux_fecha) + "'>" +
                        "<small class='label label-danger'>A</small>" +
                    "</a>";
                    $('td', row).eq(0).html(aux_text);
                }

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
                }
                $('td', row).eq(11).attr("class","action-buttons");
                $('td', row).eq(11).html(aux_text);
                aux_checkstaex = "";
                if(data.staverfacdesp == 1){
                    aux_checkstaex = "checked";
                }
                aux_text = 
                `<div class="checkbox">
                    <label style="font-size: 1.2em;padding-left: 0px;">
                        <input type="hidden" id="staverfacdesp${data.id}" name="staverfacdesp${data.id}" value="${data.staverfacdesp}">
                        <input type="checkbox" class="checkstaex" id="aux_staverfacdesp${data.id}" name="aux_staverfacdesp${data.id}" onchange="clickstaverfacdesp(this)" item=${data.id} ${aux_checkstaex}>
                        <span class='cr'><i class='cr-icon fa fa-check'></i></span>
                    </label>
                </div>`;
                $('td', row).eq(12).html(aux_text);

                $('td', row).eq(20).addClass('updated_at');
                $('td', row).eq(20).attr('id','updated_at' + data.id);
                $('td', row).eq(20).attr('name','updated_at' + data.id);

                $('td', row).eq(21).addClass('dtefac_updated_at');
                $('td', row).eq(21).attr('id','dtefac_updated_at' + data.id);
                $('td', row).eq(21).attr('name','dtefac_updated_at' + data.id);
                */

            }
        });
    }

    totalizar();

    $("#btnconsultar").click(function()
    {
        data = datosPentxProd();
        $('#tabla-data-consulta').DataTable().ajax.url( "/reportpendxprod/reportpendxprodpage/" + data.data2 ).load();
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
        "order"       : [[ 0, "desc" ]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
    });    
}
/* exportar a excel, pdf etc
'dom'         : 'Bfrtip',
'buttons'     : [
    'copy', 'csv', 'excel', 'pdf', 'print'
],
*/

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

function datosPentxProd(){
    var data1 = {
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        plazoentregad     : $("#plazoentregad").val(),
        plazoentregah     : $("#plazoentregah").val(),
        rut               : eliminarFormatoRutret($("#rut").val()),
        vendedor_id       : $("#vendedor_id").val(),
        oc_id             : $("#oc_id").val(),
        giro_id           : $("#giro_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        tipoentrega_id    : $("#tipoentrega_id").val(),
        notaventa_id      : $("#notaventa_id").val(),
        aprobstatus       : $("#aprobstatus").val(),
        aprobstatusdesc   : $("#aprobstatus option:selected").html(),
        comuna_id         : $("#comuna_id").val(),
        producto_id       : $("#producto_idPxP").val(),
        categoriaprod_id  : $("#categoriaprod_id").val(),
        sucursal_id       : $("#sucursal_id").val(),
        filtro            : 0,
        _token            : $('input[name=_token]').val()
    };


    var data2 = "?fechad="+data1.fechad +
    "&fechah="+data1.fechah +
    "&plazoentregad="+data1.plazoentregad +
    "&plazoentregah="+data1.plazoentregah +
    "&rut="+data1.rut +
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
    "&categoriaprod_id="+data1.categoriaprod_id +
    "&sucursal_id="+data1.sucursal_id +
    "&filtro="+data1.filtro +
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
        url: '/reportpendientexprod/reporte',
        type: 'POST',
        data: data,
        success: function (datos) {
            if(datos['tabla3'].length>0){
                /*
                $("#tablaconsulta").html(datos['tabla']);
                $("#tablaconsulta2").html(datos['tabla2']);
                */
                $("#tablaconsulta3").html(datos['tabla3']);
                
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
    $(".input-sm").val('');
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

$("#btnbuscarproducto").click(function(event){
    $(this).val("");
    $(".input-sm").val('');
    data = datos();
    $('#tabla-data-productos').DataTable().ajax.url( "producto/productobuscarpage/" + data.data2 + "&producto_id=" ).load();
    aux_id = $("#producto_idPxP").val();
    if( aux_id == null || aux_id.length == 0 || /^\s+$/.test(aux_id) ){
        $("#divprodselec").hide();
        $("#productos").html("");
    }else{
        arraynew = aux_id.split(',')
        $("#productos").html("");
        for(var i = 0; i < arraynew.length; i++){
            $("#productos").append("<option value='" + arraynew[i] + "' selected>" + arraynew[i] + "</option>")
        }
        $("#divprodselec").show();
    }
    $('#myModalBuscarProd').modal('show');
});

function copiar_codprod(id,codintprod){
    $("#myModalBuscarProd").modal('hide');
    aux_id = $("#producto_idPxP").val();
    if( aux_id == null || aux_id.length == 0 || /^\s+$/.test(aux_id) ){
        $("#producto_idPxP").val(id);
    }else{
        $("#producto_idPxP").val(aux_id + "," + id);
    }
	//$("#producto_idM").blur();
	$("#producto_idPxP").focus();
}

$("#btnpdf").click(function(event){
    data = datosPentxProd();
    cadena = "?fechad="+data.fechad+
            "&fechah="+data.fechah+
            "&plazoentregad="+data.plazoentregad+
            "&plazoentregah="+data.plazoentregah +
            "&rut=" + data.rut + 
            "&vendedor_id=" + data.vendedor_id +
            "&oc_id="+data.oc_id + 
            "&giro_id="+data.giro_id + 
            "&areaproduccion_id="+data.areaproduccion_id +
            "&tipoentrega_id="+data.tipoentrega_id + 
            "&notaventa_id="+data.notaventa_id + 
            "&aprobstatus="+data.aprobstatus +
            "&aprobstatusdesc=" + $("#aprobstatus option:selected").html() +
            "&comuna_id="+data.comuna_id + 
            "&producto_id="+data.producto_id +
            "&categoriaprod_id="+data.categoriaprod_id
    console.log(cadena);
    $('#contpdf').attr('src', '/reportpendientexprod/exportPdf/'+cadena);
    //$('#contpdf').attr('src', '/notaventa/'+id+'/'+stareport+'/exportPdf');
	$("#myModalpdf").modal('show')
});