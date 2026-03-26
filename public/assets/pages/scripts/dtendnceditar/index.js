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
        data = datosFac();
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
            'ajax'        : "/dtendnceditar/dtendnceditarpage/" + data.data2, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
            'columns'     : [
                {data: 'id'}, // 0
                {data: 'fechahora'}, // 1
                {data: 'rut'}, // 2
                {data: 'razonsocial'}, // 3
                {data: 'nombre_comuna'}, // 4
                {data: 'dteorigen_nrodocto'}, // 5
                {data: 'nrodocto'}, // 6
                {data: 'codref'}, // 7
                {data: 'codref'}, // 8
                {data: 'dteanul_obs',className:"ocultar"}, //9
                {data: 'dteanulcreated_at',className:"ocultar"}, //10
                {data: 'clientebloqueado_descripcion',className:"ocultar"}, //11
                {defaultContent : ""}
            ],
            "language": {
                //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "createdRow": function ( row, data, index ) {
                $(row).attr('id','fila' + data.id);
                $(row).attr('name','fila' + data.id);
                $(row).attr('updated_at', data.updated_at);
                //"<a href='#' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + "</a>";
                if (data.dteanul_obs != null) {
                    aux_fecha = new Date(data.dteanulcreated_at);
                    aux_text = data.id +
                    "<a class='btn-accion-tabla tooltipsC' title='Anulada " + fechaddmmaaaa(aux_fecha) + "'>" +
                        "<small class='label label-danger'>A</small>" +
                    "</a>";
                    $('td', row).eq(0).html(aux_text);
                }
                $('td', row).eq(0).attr('data-order',data.id);

                $('td', row).eq(1).attr('data-order',data.fechahora);
                aux_fecha = new Date(data.fechahora);
                $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));
        
                aux_text = "";
                if(data.dteorigen_nrodocto != null){
                    let id_str = data.dteorigen_nrodocto.toString();
                    id_str = data.nombrepdf + id_str.padStart(8, "0");
                    aux_text = 
                    `<a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="PDF Origen" onclick="genpdfFACDin('${data.dteorigen_id}',0)">
                        ${data.doc} ${data.dteorigen_nrodocto}
                    </a>`;
                }
                $('td', row).eq(5).html(aux_text);

                aux_text = "";
                if(data.nrodocto != null){
                    aux_text = 
                    `<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='PDF ${data.foliocontroldte_desc}' onclick="genpdfFACDin('${data.id}',0)">
                        ${data.foliocontroldte_doc} ${data.nrodocto}
                    </a>`;
                }
                $('td', row).eq(6).html(aux_text);

                aux_text = 
                `<a href="dtendnceditar" class="btn-accion-tabla tooltipsC btnEditar" title="Editar este registro">
                    <i class="fa fa-fw fa-pencil"></i>
                </a>`;
                $('td', row).eq(8).html(aux_text);


            }
        });
    }

    totalizar();

    $("#btnconsultar").click(function()
    {
        data = datosFac();
        $('#tabla-data-consulta').DataTable().ajax.url( "/dtendnceditar/dtendnceditarpage/" + data.data2 ).load();
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
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
    });    
}

function datosFac(){
    var data1 = {
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        rut               : eliminarFormatoRutret($("#rut").val()),
        sucursal_id       : $("#sucursal_id").val(),
        vendedor_id       : $("#vendedor_id").val(),
        oc_id             : $("#oc_id").val(),
        giro_id           : $("#giro_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        tipoentrega_id    : $("#tipoentrega_id").val(),
        notaventa_id      : $("#notaventa_id").val(),
        aprobstatus       : $("#aprobstatus").val(),
        comuna_id         : $("#comuna_id").val(),
        dte_id            : $("#dte_id").val(),
        producto_id       : $("#producto_idPxP").val(),
        filtro            : 1,
        nrodoctofac       : $("#nrodoctofac").val(),
        nrodocto          : $("#nrodocto").val(),
        codref            : $("#codref").val(),
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
    "&vendedor_id="+data1.vendedor_id +
    "&oc_id="+data1.oc_id +
    "&giro_id="+data1.giro_id +
    "&areaproduccion_id="+data1.areaproduccion_id +
    "&tipoentrega_id="+data1.tipoentrega_id +
    "&notaventa_id="+data1.notaventa_id +
    "&aprobstatus="+data1.aprobstatus +
    "&comuna_id="+data1.comuna_id +
    "&dte_id="+data1.dte_id +
    "&producto_id="+data1.producto_id +
    "&filtro="+data1.filtro +
    "&nrodoctofac="+data1.nrodoctofac +
    "&nrodocto="+data1.nrodocto +
    "&codref="+data1.codref +
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