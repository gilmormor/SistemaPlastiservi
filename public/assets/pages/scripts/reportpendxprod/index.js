$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

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

    configurarTabla1('#tabla-data-consulta');

    function configurarTabla1(aux_tabla){
        datax = datosPentxProd(1);
        datax
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
                {data: 'cla_nombre'}, // 8
                {data: 'diametro'}, // 9
                {data: 'long'}, // 10
                {data: 'at_espesor'}, // 11
                {data: 'tipounion'}, // 12
                {data: 'stockbpt'}, // 13
                {data: 'picking'}, // 14
                {data: 'cant'}, //15
                {data: 'sumacantdesp'}, //16
                {data: 'cantsaldo'}, //17
                {data: 'kgpend'}, //18
                {data: 'precioxkilo'}, //19
                {data: 'subtotalplata'} //20
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "createdRow": function ( row, data, index ) {
                if ('datosAdicionales' in data) {
                    console.log(data);
                    $("#prom_precioxkilo").html(MASKLA(data.datosAdicionales.prom_precioxkilo,2));
                    $("#total_totalplata").html(MASKLA(data.datosAdicionales.total_totalplata,0));                
                }
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

                style='text-align:right' 
                $('td', row).eq(13).attr('style','text-align:right');
                $('td', row).eq(13).html(MASKLA(data.stockbpt,0));
                $('td', row).eq(14).attr('style','text-align:right');
                $('td', row).eq(14).html(MASKLA(data.picking,0));
                $('td', row).eq(15).attr('style','text-align:right');
                $('td', row).eq(15).attr('data-order',data.cant);
                $('td', row).eq(15).addClass('cant');
                $('td', row).eq(15).html(MASKLA(data.cant,0));

                aux_text = "0"
                if(data.sumacantdesp > 0){
                    aux_text = 
                    `<a class="btn-accion-tabla btn-sm tooltipsC" onclick="listarorddespxNV(${data.notaventa_id},${data.producto_id})" title='Ver detalle despacho' data-toggle='tooltip'>
                        ${MASKLA(data.sumacantdesp,0)}
                    </a>`
                }
                $('td', row).eq(16).attr('style','text-align:right');
                $('td', row).eq(16).attr('data-order',data.sumacantdesp);
                $('td', row).eq(16).addClass('sumacantdesp');
                $('td', row).eq(16).html(aux_text);
                $('td', row).eq(17).attr('style','text-align:right');
                $('td', row).eq(17).attr('data-order',data.cantsaldo);
                $('td', row).eq(17).addClass('cantsaldo');
                $('td', row).eq(17).html(MASKLA(data.cantsaldo,0));
                $('td', row).eq(18).attr('style','text-align:right');
                $('td', row).eq(18).attr('data-order',data.kgpend);
                $('td', row).eq(18).addClass('kgpend');
                $('td', row).eq(18).html(MASKLA(data.kgpend,0));
                $('td', row).eq(19).attr('style','text-align:right');
                $('td', row).eq(19).html(MASKLA(data.precioxkilo,2));
                $('td', row).eq(20).attr('style','text-align:right');
                $('td', row).eq(20).attr('data-order',data.subtotalplata);
                $('td', row).eq(20).addClass('totalplata');
                $('td', row).eq(20).html(MASKLA(data.subtotalplata,0));
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

function totalizar(){
    let  table = $('#tabla-data-consulta').DataTable();
    //console.log(table);
    table
        .on('draw', function () {
            eventFired( 'Page' );
        });
        /*
    data = datosPentxProd();
    $.ajax({
        url: '/reportinvstock/totalizarindex/' + data.data2,
        type: 'GET',
        success: function (datos) {
            $("#totalkg").html(MASKLA(datos.aux_totalkg,2));
            //$("#totaldinero").html(MASKLA(datos.aux_totaldinero,0));
        }
    });
    */
}

var eventFired = function ( type ) {
	total = 0;
    var subcant = 0;
    var subsumacantdesp = 0;
    subcantsaldo = 0;
    subkgpend = 0;
    subtotalplata = 0;
	$("#tabla-data-consulta tr").each(function() {
        var cant = parseFloat($(this).find(".cant").attr("data-order"));
        var sumacantdesp = parseFloat($(this).find(".sumacantdesp").attr("data-order"));
        var cantsaldo = parseFloat($(this).find(".cantsaldo").attr("data-order"));
        var kgpend = parseFloat($(this).find(".kgpend").attr("data-order"));
        var totalplata = parseFloat($(this).find(".totalplata").attr("data-order"));        
        if (!isNaN(cant)) {
            subcant += cant;
        }
        if (!isNaN(sumacantdesp)) {
            subsumacantdesp += sumacantdesp;
        }
        if (!isNaN(cantsaldo)) {
            subcantsaldo += cantsaldo;
        }
        if (!isNaN(kgpend)) {
            subkgpend += kgpend;
        }
        if (!isNaN(totalplata)) {
            subtotalplata += totalplata;
        }

	});
    $("#subcant").html(MASKLA(subcant,0))
    $("#subsumacantdesp").html(MASKLA(subsumacantdesp,0))
    $("#subcantsaldo").html(MASKLA(subcantsaldo,0))
    $("#subkgpend").html(MASKLA(subkgpend,0))
    $("#subtotalplata").html(MASKLA(subtotalplata,0))
}


function datosPentxProd(aux_filtro = 0){
    aux_rut = eliminarFormatoRutret($("#rut").val());
    if(aux_filtro != 0){
        aux_rut = -1;
    }
    var data1 = {
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        plazoentregad     : $("#plazoentregad").val(),
        plazoentregah     : $("#plazoentregah").val(),
        rut               : aux_rut,
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
    $('#contpdf').attr('src', '/reportpendientexprod/exportPdf/'+cadena);
    //$('#contpdf').attr('src', '/notaventa/'+id+'/'+stareport+'/exportPdf');
	$("#myModalpdf").modal('show')
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
/*
$('#tabla-data-consulta').on('draw.dt', function () {
    var api = $(this).DataTable();
    var data = api.ajax.json(); // Acceder a los datos de la respuesta JSON
  
    // Realizar acciones con los datos, por ejemplo:
//    console.log(data.input.prom_precioxkilo); // Imprimir los datos en la consola
    console.log(data.input); // Imprimir los datos en la consola
    $("#prom_precioxkilo").html(MASKLA(data.input.prom_precioxkilo,2));
    $("#total_totalplata").html(MASKLA(data.input.total_totalplata,0));

    // También puedes recorrer y manipular los datos, por ejemplo:
    $.each(data, function (index, row) {
      // Hacer algo con cada fila de datos
    });
  });
  
  */
  
  