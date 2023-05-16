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

    $("#btnconsultar").click(function()
    {
        consultarpage(datosFac());
    });
    $("#btnpdf2").click(function()
    {
        btnpdf(datosFac());
    });
    consultarpage(datosFac())

});

function consultarpage(aux_data){
    $("#tabla-data-consulta").attr('style','')
    $("#tabla-data-consulta").dataTable().fnDestroy();
    $('#tabla-data-consulta').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        "order"       : [[ 0, "asc" ]],
        'ajax'        : "/reportdteventasxvend/reportdteventasxvendpage/" + aux_data.data2, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
        'columns'     : [
            {data: 'id'}, // 0
            {data: 'fechahora'}, // 1
            {data: 'rut'}, // 2
            {data: 'razonsocial'}, // 3
            {data: 'cotizacion_id'}, // 4
            {data: 'oc_id'}, // 5
            {data: 'notaventa_id'}, // 6
            {data: 'despachosol_id'}, // 7
            {data: 'despachoord_id'}, // 8
            {data: 'nrodocto_origen'}, // 9
            {data: 'nrodocto'}, // 10
            {data: 'foliocontrol_doc'}, // 11
            {data: 'mnttotal'}, // 12
            {data: 'dteanul_obs',className:"ocultar"}, //13
            {data: 'dteanulcreated_at',className:"ocultar"}, //14
            {data: 'clientebloqueado_descripcion',className:"ocultar"}, //15
            {data: 'oc_file',className:"ocultar"}, //16
            {data: 'nombrepdf',className:"ocultar"}, //17
            {data: 'staverfacdesp',className:"ocultar"}, //18
            {data: 'updated_at',className:"ocultar"}, //19
            {data: 'dtefac_updated_at',className:"ocultar"}, //20
            {data: 'foliocontrol_desc',className:"ocultar"}, //21
            {data: 'pdftipodte_origen',className:"ocultar"}, //22
            {data: 'foliocontroldesc_origen',className:"ocultar"}, //23
            {defaultContent : ""}
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
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


            $('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
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
                $('td', row).eq(5).html(aux_text);
            }
            aux_text = "";
            if(data.notaventa_id != null){
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
            if(data.despachosol_id != null){
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
            if(data.despachosol_id != null){
                let arr_despachoord_id = data.despachoord_id.split(','); 
                for (let i = 0; i < arr_despachoord_id.length; i++){
                    aux_text += 
                    "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Orden Despacho' onclick='genpdfOD(" + arr_despachoord_id[i] + ",1)'>" +
                        arr_despachoord_id[i] +
                    "</a>";
                    if((i+1) < arr_despachoord_id.length){
                        aux_text += ",";
                    }
                }    
            }
            $('td', row).eq(8).html(aux_text);


            aux_text = "";
            if(data.nrodocto_origen != null){
                let arr_nrodocto_origen = data.nrodocto_origen.split(',');
                let arr_pdftipodte_origen = data.pdftipodte_origen.split(',');
                let arr_foliocontroldesc_origen = data.foliocontroldesc_origen.split(',');
                for (let i = 0; i < arr_nrodocto_origen.length; i++){
                    let id_str = arr_nrodocto_origen[i].toString();
                    id_str = arr_pdftipodte_origen[i] + id_str.padStart(8, "0");
                    
                    aux_text += 
                    `<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='${arr_foliocontroldesc_origen[i]}' onclick="genpdfFAC('${id_str}','')">
                        ${arr_nrodocto_origen[i]}
                    </a>:
                    <a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='${arr_foliocontroldesc_origen[i]}' onclick="genpdfFAC('${id_str}','_cedible')">
                        ${arr_nrodocto_origen[i]}
                    </a>`;
                    if((i+1) < arr_nrodocto_origen.length){
                        aux_text += ",";
                    }
                }    
            }
            $('td', row).eq(9).html(aux_text);

            $('td', row).eq(10).html(aux_text);
            aux_text = "";
            if(data.nrodocto != null){
                let id_str = data.nrodocto.toString();
                id_str = data.nombrepdf + id_str.padStart(8, "0");
                if(data.nrodocto != null){
                    aux_text = 
                    `<a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="${data.foliocontrol_desc}" onclick="genpdfFAC('${id_str}','')">
                        ${data.nrodocto}
                    </a>:
                    <a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="${data.foliocontrol_desc} Cedible" onclick="genpdfFAC('${id_str}','_cedible')">
                        ${data.nrodocto}
                    </a>`;
                }    
            }
            $('td', row).eq(10).html(aux_text);

            $('td', row).eq(12).attr('style','text-align:right');
            $('td', row).eq(12).attr('data-order',data.mnttotal);
            $('td', row).eq(12).attr('data-search',data.mnttotal);
            $('td', row).eq(12).html(MASKLA(data.mnttotal,0));
            $('td', row).eq(12).addClass('subtotalmonto');

            
            $('td', row).eq(19).addClass('updated_at');
            $('td', row).eq(19).attr('id','updated_at' + data.id);
            $('td', row).eq(19).attr('name','updated_at' + data.id);

            $('td', row).eq(20).addClass('dtefac_updated_at');
            $('td', row).eq(20).attr('id','dtefac_updated_at' + data.id);
            $('td', row).eq(20).attr('name','dtefac_updated_at' + data.id);
        }
    });
    totalizar();
}


function totalizar(){
    let  table = $('#tabla-data-consulta').DataTable();
    //console.log(table);
    table
        .on('draw', function () {
            eventFired( 'Page' );
        });
    data = datosFac();
    $.ajax({
        url: '/reportdteventasxvend/totalizarindex/' + data.data2,
        type: 'GET',
        success: function (datos) {
            $("#totalmonto").html(MASKLA(datos.aux_total,0));
            //$("#totaldinero").html(MASKLA(datos.aux_totaldinero,0));
        }
    });
}

var eventFired = function ( type ) {
    total = 0;
    $("#tabla-data-consulta tr .subtotalmonto").each(function() {
        valor = $(this).attr('data-order') ;
        valorNum = parseFloat(valor);
        total += valorNum;
    });
    $("#subtotalmonto").html(MASKLA(total,0))
}

function datosFac(){
    var data1 = {
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        sucursal_id       : $("#sucursal_id").val(),
        rut               : eliminarFormatoRutret($("#rut").val()),
        vendedor_id       : $("#vendedor_id").val(),
        filtro            : 1,
        statusgen         : 1,
        foliocontrol_id   : "",
        orderby           : "",
        groupby           : "",
        _token            : $('input[name=_token]').val()
    };

    var data2 = "?fechad="+data1.fechad +
    "&fechah="+data1.fechah +
    "&sucursal_id="+data1.sucursal_id +
    "&rut="+data1.rut +
    "&vendedor_id="+data1.vendedor_id +
    "&filtro="+data1.filtro +
    "&statusgen="+data1.statusgen +
    "&foliocontrol_id="+data1.foliocontrol_id +
    "&_token="+data1._token

    var data = {
        data1 : data1,
        data2 : data2
    };
    //console.log(data);
    return data;
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

function btnpdf(data){
    //console.log(data);
    //alert('entro');
    $('#contpdf').attr('src', '/reportdteventasxvend/exportPdf/'+data.data2);
    $("#myModalpdf").modal('show');
}