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
            "order"       : [[ 0, "desc" ]],
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
                    //console.log(data);
                    $("#subpicking").html(MASKLA(data.datosAdicionales.total_sumapicking,0))
                    $("#subcant").html(MASKLA(data.datosAdicionales.total_sumacant,0))
                    $("#subsumacantdesp").html(MASKLA(data.datosAdicionales.total_sumacantdesp,0))
                    $("#subcantsaldo").html(MASKLA(data.datosAdicionales.total_cantsaldo,0))
                    $("#subkgpend").html(MASKLA(data.datosAdicionales.total_kgpend,0))
                    $("#subtotalplata").html(MASKLA(data.datosAdicionales.total_totalplata,0))
                
                    $("#prom_precioxkilo").html(MASKLA(data.datosAdicionales.prom_precioxkilo,2));
                    $("#prom_totalplata").html(MASKLA(data.datosAdicionales.prom_totalplata,0));                
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
                $('td', row).eq(13).attr('data-order',data.stockbpt);
                $('td', row).eq(13).addClass('stock');
                $('td', row).eq(13).html(MASKLA(data.stockbpt,0));
                $('td', row).eq(14).attr('style','text-align:right');
                $('td', row).eq(14).attr('data-order',data.picking);
                $('td', row).eq(14).addClass('picking');
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

    //totalizar();
/*
    $("#btnconsultar").click(function()
    {
        data = datosPentxProd();
        if(data.data1.sucursal_id == ""){
            swal({
                title: 'Debes seleccionar Sucursal',
                icon: 'error',
                buttons: {
                    confirm: "Cerrar",
                },
            });
            aux_sucursal_id = -1;
        }else{
            $('#tabla-data-consulta').DataTable().ajax.url( "/reportpendxprod/reportpendxprodpage/" + data.data2 ).load();
        }
    
    });
*/

});

function ejecutarConsulta(aux_cod){
    data = datosPentxProd();
    if(data.data1.sucursal_id == ""){
        swal({
            title: 'Debes seleccionar Sucursal',
            icon: 'error',
            buttons: {
                confirm: "Cerrar",
            },
        });
    }else{
        if(aux_cod == 1){
            $('#tabla-data-consulta').DataTable().ajax.url( "/reportpendxprod/reportpendxprodpage/" + data.data2 ).load();
        }
        if(aux_cod == 2){
            consultarJS(data);    
        }
        if(aux_cod == 3){
            exportarExcel();    
        }
    }

}

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
    subpicking = 0;
	total = 0;
    var subcant = 0;
    var subsumacantdesp = 0;
    subcantsaldo = 0;
    subkgpend = 0;
    subtotalplata = 0;
	$("#tabla-data-consulta tr").each(function() {
        var picking = parseFloat($(this).find(".picking").attr("data-order"));
        var cant = parseFloat($(this).find(".cant").attr("data-order"));
        var sumacantdesp = parseFloat($(this).find(".sumacantdesp").attr("data-order"));
        var cantsaldo = parseFloat($(this).find(".cantsaldo").attr("data-order"));
        var kgpend = parseFloat($(this).find(".kgpend").attr("data-order"));
        var totalplata = parseFloat($(this).find(".totalplata").attr("data-order"));        
        if (!isNaN(picking)) {
            subpicking += picking;
        }
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
    $("#subpicking").html(MASKLA(subpicking,0))
    $("#subcant").html(MASKLA(subcant,0))
    $("#subsumacantdesp").html(MASKLA(subsumacantdesp,0))
    $("#subcantsaldo").html(MASKLA(subcantsaldo,0))
    $("#subkgpend").html(MASKLA(subkgpend,0))
    $("#subtotalplata").html(MASKLA(subtotalplata,0))
}


function datosPentxProd(aux_filtro = 0){
    aux_rut = eliminarFormatoRutret($("#rut").val());
    aux_sucursal_id = $("#sucursal_id").val();
    /*
    if(aux_sucursal_id == "" && aux_filtro == 0 ){
        swal({
            title: 'Debes seleccionar Sucursal',
            icon: 'error',
            buttons: {
                confirm: "Cerrar",
            },
        });
        aux_sucursal_id = -1;
    }
    */
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
        sucursal_id       : aux_sucursal_id,
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
    /*
    $("#btnpdfJS").click(function()
    {
        data = datosPentxProd();
        consultarJS(data);
    });
    */
  
    function sumarColumna(columna) {
        return datos.reduce(function (total, fila) {
            return total + parseFloat(fila[columna] || 0);
        }, 0);
    }

    function promediarColumna(columna) {
        return sumarColumna(columna) / datos.length;
    }

  function consultarJS(data){
    /*
    // Ejemplo de uso
    var rutaArchivo = 'assets/lte/dist/img/LOGO-PLASTISERVI1x.jpg';
    
    verificarArchivo(rutaArchivo, function(existe) {
        if (existe) {
        console.log('El archivo existe en la ruta especificada.');
        } else {
        console.log('El archivo no existe en la ruta especificada.');
        }
    });
    return 0;*/
    $.ajax({
        url: '/reportpendxprod/reportpendxprodpage',
        type: 'GET',
        data: data.data1,
        success: function (datos) {
            //console.log(datos);
            if (datos.recordsTotal <= 0) {
                aux_text = "Verifique los filtros";
                if(data.data1.sucursal_id == ""){
                    aux_text = "Debe seleccionar la Sucursal";
                }
                swal({
                    title: 'Informacion no encontrada.',
                    text: aux_text,
                        icon: 'warning',
                    buttons: {
                        confirm: "Aceptar"
                    },
                });    
            }else{
                pdf = pdfjs(datos);
                pdf.save('PendXProd.pdf');    
            }
        }
    });
}

function headRows() {
    return [
      //{ nv: "NV", oc: "OC", fecha: "Fecha", plazoentrega: "PlazoEnt", razonsocial: "Razón Social", comuna: "Comuna", cod: "Cod", desc: "Descripción", clase: "ClaSello", diam: "Diam Anch", l: "L", pesoesp: "Peso Esp", tu: "TU", stock: "Stock", picking: "Pick", cant: "Cant", cantdesp: "Cant Desp", cantpend: "Cant Pend", kilos: "Kilos Pend", preciokg: "Precio Kg", pesos: "$"},
      { nv: "NV", oc: "OC", fecha: "Fecha", plazoentrega: "PlazoEnt", razonsocial: "Razón Social", comuna: "Comuna", cod: "Cod", desc: "Descripción", stock: "Stock", picking: "Pick", cant: "Cant", cantdesp: "Cant Desp", cantpend: "Cant Pend", kilos: "Kilos Pend", preciokg: "Precio Kg", pesos: "$"},
    ]
}

function bodyRows(datos) {
    var body = []
    total_sumapicking = 0;
    total_sumacant = 0;
    total_sumacantdesp = 0;
    total_cantsaldo = 0;
    total_kgpend = 0;
    total_totalplata = 0;
    total_precioxkilo = 0;
    aux_contreg = 0;
    datos.data.forEach(function(registro) {
        aux_fecha = new Date(registro.fechahora);
        aux_plazoentrega = new Date(registro.plazoentrega + " 00:00:00")
        aux_productonomb = registro.nombre.replace(/&quot;/g, '"');
        aux_productonomb = aux_productonomb.replace(/&#039;/g, "'");
        body.push({
            nv: registro.notaventa_id,
            oc: registro.oc_id,
            fecha: fechaddmmaaaa(aux_fecha),
            plazoentrega: fechaddmmaaaa(aux_plazoentrega),
            razonsocial: registro.razonsocial,
            comuna: registro.comunanombre,
            cod: registro.producto_id,
            desc: cadenaSinEntidad = aux_productonomb,
            stock: registro.stockbpt,
            picking: registro.picking,
            cant: registro.cant,
            cantdesp: registro.cantdesp,
            cantpend: registro.cantsaldo,
            kilos: MASKLA(registro.kgpend,2),
            preciokg: MASKLA(registro.precioxkilo,2),
            pesos: MASKLA(registro.subtotalplata,0)
        })
        total_sumapicking += registro.picking;
        total_sumacant += registro.cant;
        total_sumacantdesp += registro.cantdesp;
        total_cantsaldo += registro.cantsaldo;
        total_kgpend += registro.kgpend;
        total_totalplata += registro.subtotalplata;
        total_precioxkilo += registro.precioxkilo;
        aux_contreg++;
    });
    if(total_totalplata > 0){
        body.push({
            nv: "",
            oc: "",
            fecha: "",
            plazoentrega: "",
            razonsocial: "",
            comuna: "",
            cod: "",
            desc: "",
            stock: "Totales",
            picking: total_sumapicking,
            cant: total_sumacant,
            cantdesp: total_sumacantdesp,
            cantpend: total_cantsaldo,
            kilos: MASKLA(total_kgpend,2),
            preciokg: "",
            pesos: MASKLA(total_totalplata,0)
        })
        prom_precioxkg = total_precioxkilo / aux_contreg;
        prom_precioxkg = parseFloat(prom_precioxkg.toFixed(2));
        prom_precio = total_totalplata / aux_contreg;
        prom_precio = parseFloat(prom_precio.toFixed(0));
        body.push({
            nv: "",
            oc: "",
            fecha: "",
            plazoentrega: "",
            razonsocial: "",
            comuna: "",
            cod: "",
            desc: "Total ítems: " + aux_contreg,
            stock: "Prom",
            picking: "",
            cant: "",
            cantdesp: "",
            cantpend: "",
            kilos: "",
            preciokg: MASKLA(prom_precioxkg,2),
            pesos: MASKLA(prom_precio,0)
        })
    
    }

    return body
}

function pdfjs(datos) {
    
    /*
    imgToBase64('assets/lte/dist/img/LOGO-PLASTISERVI1.jpg', function(base64) {
        base64Img = base64;
        imgToBase64('assets/lte/dist/img/LOGO-PLASTISERVI1.png', function(base64) {
            coinBase64Img = base64;
            update();
        });
    });*/

    var base64Img, coinBase64Img;
    base64Img = imgToBase64('assets/lte/dist/img/LOGO-PLASTISERVI.jpg');
 
    var doc = new jsPDF('l')
    var totalPagesExp = '{total_pages_count_string}'
  
    doc.autoTable({
      startY: 25,
      head: headRows(),
      body: bodyRows(datos),
      theme: 'grid',
      styles: {
        fontSize: 6, // Tamaño de letra para los encabezados
      },
      headStyles: { 
        fillColor: '#0077FF', // Color de fondo azul
        textColor: '#FFFFFF', //texto blanco solo para el encabezado
        valign: 'middle' // Centrar verticalmente los títulos en el encabezado
      },
      rowStyles: { //NO FUNCIONA ESTA PROPIEDAD, LA DEJE PARA TENER LA REFERENCIA
        // Asumiendo que la fila que contiene el signo "$" es la primera fila (15)
        15: { halign: 'center' }  // Centrar horizontalmente la primera fila del encabezado en la columna 15
      },
      columnStyles: {
        0: { cellWidth: 10 },  // Ancho columna
        1: { cellWidth: 18 },  // Ancho columna
        2: { cellWidth: 14 },  // Ancho columna
        3: { cellWidth: 14 },  // Ancho columna
        4: { cellWidth: 32 },  // Ancho columna
        5: { cellWidth: 17 },  // Ancho columna
        6: { cellWidth: 8, halign: 'center'  },  // Ancho columna
        7: { cellWidth: 58 },  // Ancho columna
        8: { cellWidth: 12, halign: 'right' },  // Ancho columna
        9: { cellWidth: 12, halign: 'right' },  // Ancho columna
        10: { cellWidth: 12, halign: 'right' },  // Ancho columna
        11: { cellWidth: 12, halign: 'right' },  // Ancho columna
        12: { cellWidth: 12, halign: 'right' },  // Ancho columna
        13: { cellWidth: 15, halign: 'right' },  // Ancho columna
        14: { cellWidth: 15, halign: 'right' },  // Ancho columna
        15: { cellWidth: 15, halign: 'right' },  // Ancho columna

        // ... especifica el ancho de las demás columnas
      },
      willDrawPage: function (data) {
        // Header
        doc.setFontSize(12)
        doc.setTextColor(20)
        if (base64Img) {
          doc.addImage(base64Img, 'JPEG', data.settings.margin.left, 6, 30, 10)
        }
        doc.text('Pendiente por Producto', data.settings.margin.left + 110, 12);
        doc.setFontSize(8)
        doc.text('Sucursal: ' + $("#sucursal_id option:selected").html(), data.settings.margin.left + 120, 16);
        doc.text('Fecha: ' + fechaactual(), data.settings.margin.left + 220, 5);
        doc.text('Area Producción: ' + $("#areaproduccion_id option:selected").html(), data.settings.margin.left + 220, 8);
        //doc.text('Vendedor: ' + $("#vendedor_id option:selected").html(), data.settings.margin.left + 220, 11);
        doc.text('Vendedor: ' + descripElementoSelectMult("vendedor_id"), data.settings.margin.left + 220, 11);
        doc.text('Giro: ' + $("#giro_id option:selected").html() + ' Estatus: ' + $("#aprobstatus option:selected").html(), data.settings.margin.left + 220, 14);
        doc.text('Nota Venta Desde: ' + $("#fechad").val() + " al " + $("#fechah").val(), data.settings.margin.left + 220, 17);
        doc.text('Plazo de Entrega: ' + $("#plazoentregad").val() + " al " + $("#plazoentregah").val(), data.settings.margin.left + 220, 20);

        var startY = 21; // Ajusta según sea necesario
        data.cursor.y = startY;

      },
      didDrawPage: function (data) {
        // Footer
        var str = 'Pag ' + doc.internal.getNumberOfPages()
        // Total page number plugin only available in jspdf v1.0+
        if (typeof doc.putTotalPages === 'function') {
          str = str + ' de ' + totalPagesExp
        }
        doc.setFontSize(7)
  
        // jsPDF 1.4+ uses getHeight, <1.4 uses .height
        var pageSize = doc.internal.pageSize
        var pageHeight = pageSize.height ? pageSize.height : pageSize.getHeight()
        doc.text(str, data.settings.margin.left, pageHeight - 10)
      },
      margin: { top: 20},
    })
  
    // Total page number plugin only available in jspdf v1.0+
    if (typeof doc.putTotalPages === 'function') {
      doc.putTotalPages(totalPagesExp)
    }
  
    return doc
}

function imgToBase64(src, callback) {
    var outputFormat = src.substr(-3) === 'png' ? 'image/png' : 'image/jpeg';
    var img = new Image();
    img.crossOrigin = 'Anonymous';
    img.onload = function() {
        var canvas = document.createElement('CANVAS');
        var ctx = canvas.getContext('2d');
        var dataURL;
        canvas.height = this.naturalHeight;
        canvas.width = this.naturalWidth;
        ctx.drawImage(this, 0, 0);
        dataURL = canvas.toDataURL(outputFormat);
        //callback(dataURL);
    };
    img.src = src;
    if (img.complete || img.complete === undefined) {
        img.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
        img.src = src;
    }
    return img;
    console.log(img);
}

function verificarArchivo(url, callback) {
    fetch(url, { method: 'HEAD' })
      .then(response => {
        if (response.ok) {
          // El archivo existe
          callback(true);
        } else {
          // El archivo no existe
          callback(false);
        }
      })
      .catch(error => {
        // Error en la solicitud (puede ser por CORS u otros problemas)
        callback(false);
      });
  }
  

  function update(shouldDownload) {
    var funcStr = window.location.hash.replace(/#/g, '') || 'basic';
    var doc = window.examples[funcStr]();

    doc.setProperties({
        title: 'Example: ' + funcStr,
        subject: 'A jspdf-autotable example pdf (' + funcStr + ')'
    });

    if (shouldDownload) {
        doc.save('table.pdf');
    } else {
        //document.getElementById("output").src = doc.output('datauristring');
        document.getElementById("output").data = doc.output('datauristring');
    }
}


function exportarExcel() {
    orderby = " order by foliocontrol.doc,dte.id ";
    data = datosPentxProd();
    // Obtener todos los registros mediante una solicitud AJAX
    $.ajax({
        url: "/reportpendxprod/reportpendxprodpage/" + data.data2, // ajusta la URL de la solicitud al endpoint correcto
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

        cellLengthRazonSoc = 0;
        cellLengthProducto = 0;
        filainifusionar = -1
        //console.log(data);
        aux_sucursalNombre = $("#sucursal_id option:selected").html();
        if(aux_sucursalNombre == "Seleccione..."){
            aux_sucursalNombre = "";
        }
        aux_rangofecha = $("#fechad").val() + " al " + $("#fechah").val()
        datosExcel.push(["Pendiente por Producto","","","","","","","","","","","","","","",fechaactual()]);
        datosExcel.push(["Centro Economico: " + aux_sucursalNombre + " Entre: " + aux_rangofecha,"","","","","","","",""]);
        aux_cont = 0;
        aux_totalPicking = 0;
        aux_totalCant = 0;
        aux_totalCantDesp = 0;
        aux_totalCantPend = 0;
        aux_totalkgPend = 0;
        aux_totalPreciokg = 0;
        aux_totalMonto = 0;
        aux_totalkgtotal = 0;
        aux_totalmnttotal = 0;
        datosExcel.push(["","","","","","","","",""]);
        datosExcel.push(["NV","OC","Fecha","PlazoEnt","Razon Social","Comuna","CodProd","Descripcion","Stock","Picking","Cant","Cant Desp","Cant Pend","KgPend","Precio Kg","$"]);
        data.data.forEach(function(registro) {
            aux_cont++;
            aux_totalPicking += registro.picking;
            aux_totalCant += registro.cant;
            aux_totalCantDesp += registro.cantdesp;
            aux_totalCantPend += registro.cantsaldo;
            aux_totalkgPend += registro.kgpend;
            aux_totalPreciokg += registro.precioxkilo;
            aux_totalMonto += registro.subtotalplata;

            aux_fecha = new Date(registro.fechahora);
            aux_plazoentrega = new Date(registro.plazoentrega + " 00:00:00")
            aux_productonomb = registro.nombre.replace(/&quot;/g, '"');
            aux_productonomb = aux_productonomb.replace(/&#039;/g, "'");
            var filaExcel = [
                registro.notaventa_id,
                registro.oc_id,
                fechaddmmaaaa(aux_fecha),
                fechaddmmaaaa(aux_plazoentrega),
                registro.razonsocial,
                registro.comunanombre,
                registro.producto_id,
                cadenaSinEntidad = aux_productonomb,
                registro.stockbpt,
                registro.picking,
                registro.cant,
                registro.cantdesp,
                registro.cantsaldo,
                registro.kgpend,
                registro.precioxkilo,
                registro.subtotalplata
            ];
            //aux_vendedor_id = registro.vendedor_id;

            datosExcel.push(filaExcel);
        });
        if(aux_totalMonto > 0){
            prom_preciokg = (aux_totalPreciokg/aux_cont);
            prom_Monto = (aux_totalMonto/aux_cont);
            datosExcel.push(["","","","","","","","","Total:",aux_totalPicking,aux_totalCant,aux_totalCantDesp,aux_totalCantPend,aux_totalkgPend,"",aux_totalMonto]);
            datosExcel.push(["","","","","","","","","Promedio:","","","","","",prom_preciokg,prom_Monto]);
        }

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
    for (let i = 1; i <= 16; i++) {
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
    for (let i = 1; i <= 16; i++) {
        columna = getColumnLetter(i); // Obten la letra de la columna correspondiente
        const celda = worksheet.getCell(`${columna}${fila}`);
        celda.alignment = { wrapText: true, vertical: 'middle' };
        celda.autosize = true;
    }    

    const columnI = worksheet.getColumn(9);
    columnI.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });

    const columnJ = worksheet.getColumn(10);
    columnJ.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });

    const columnK = worksheet.getColumn(11);
    columnK.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });

    const columnL = worksheet.getColumn(12);
    columnL.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });

    const columnM = worksheet.getColumn(13);
    columnM.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });

    const columnN = worksheet.getColumn(14);
    columnN.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });

    const columnO = worksheet.getColumn(15);
    columnO.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });

    const columnP = worksheet.getColumn(16);
    columnP.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });

    // Establecer el formato de centrado horizontal y vertical para las celdas de la columna 8 desde la fila 4 hasta la fila 58
    for (let i = 4; i <= datosExcel.length; i++) {
        const cell7 = worksheet.getCell(i, 7);
        cell7.alignment = { horizontal: "center", vertical: "middle" };

        const cell9 = worksheet.getCell(i, 9);
        cell9.alignment = { wrapText: true, horizontal: "right", vertical: "middle" };

        const cell10 = worksheet.getCell(i, 10);
        cell10.alignment = { wrapText: true, horizontal: "right", vertical: "middle" };

        const cell11 = worksheet.getCell(i, 11);
        cell11.alignment = { wrapText: true, horizontal: "right", vertical: "middle" };

        const cell12 = worksheet.getCell(i, 12);
        cell12.alignment = { wrapText: true, horizontal: "right", vertical: "middle" };

        const cell13 = worksheet.getCell(i, 13);
        cell13.alignment = { wrapText: true, horizontal: "right", vertical: "middle" };

        const cell14 = worksheet.getCell(i, 14);
        cell14.alignment = { wrapText: true, horizontal: "right", vertical: "middle" };

        const cell15 = worksheet.getCell(i, 15);
        cell15.alignment = { wrapText: true, horizontal: "right", vertical: "middle" };

        const cell16 = worksheet.getCell(i, 16);
        cell16.alignment = { wrapText: true, horizontal: "right", vertical: "middle" };

        /*
        const cell9 = worksheet.getCell(i, 9);
        cell9.alignment = { horizontal: "center", vertical: "middle" };
        const cell10 = worksheet.getCell(i, 10);
        cell10.alignment = { horizontal: "center", vertical: "middle" };

        const cell = worksheet.getCell(i, 13);
        cell.alignment = { horizontal: "center", vertical: "middle" };
        */

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


    row = worksheet.getRow(datosExcel.length-1);
    cell = row.getCell(8);
    cell.font = { bold: true };
    cell.alignment = { horizontal: "right" };

    cell = row.getCell(9);
    cell.font = { bold: true };
    cell.alignment = { horizontal: "right" };
    cell.numFmt = "#,##0";

    cell = row.getCell(10);
    cell.font = { bold: true };
    cell.alignment = { horizontal: "right" };
    cell.numFmt = "#,##0";

    cell = row.getCell(11);
    cell.font = { bold: true };
    cell.alignment = { horizontal: "right" };
    cell.numFmt = "#,##0";

    cell = row.getCell(12);
    cell.font = { bold: true };
    cell.alignment = { horizontal: "right" };
    cell.numFmt = "#,##0";

    cell = row.getCell(13);
    cell.font = { bold: true };
    cell.alignment = { horizontal: "right" };
    cell.numFmt = "#,##0";

    cell = row.getCell(14);
    cell.font = { bold: true };
    cell.alignment = { horizontal: "right" };
    cell.numFmt = "#,##0.00";

    cell = row.getCell(15);
    cell.font = { bold: true };
    cell.alignment = { horizontal: "right" };
    cell.numFmt = "#,##0";

    cell = row.getCell(16);
    cell.font = { bold: true };
    cell.alignment = { horizontal: "right" };
    cell.numFmt = "#,##0";

    row = worksheet.getRow(datosExcel.length);
    cell = row.getCell(9);
    cell.font = { bold: true };
    cell.alignment = { horizontal: "right" };

    cell = row.getCell(15);
    cell.font = { bold: true };
    cell.alignment = { horizontal: "right" };
    cell.numFmt = "#,##0.00";

    cell = row.getCell(16);
    cell.font = { bold: true };
    cell.alignment = { horizontal: "right" };
    cell.numFmt = "#,##0";

    ajustarcolumnaexcel(worksheet,"N");
    ajustarcolumnaexcel(worksheet,"P");


    //Fusionar celdas de Titulo
    const startCol = 0;
    const endCol = 15;
    worksheet.mergeCells(1, startCol, 1, endCol);

    //Negrita Columna Sucursal
    const row3 = worksheet.getRow(2);
    cell = row3.getCell(1);
    cell.alignment = { horizontal: "center", vertical: "middle" };
    
    //Fusionar celdas Sucursal
    const startCol1 = 0;
    const endCol1 = 15;
    worksheet.mergeCells(2, startCol1, 2, endCol1);

    // Establecer negrita a totales
    /*
    row = worksheet.getRow(datosExcel.length);
    for (let i = 1; i <= 6; i++) {
        cell = row.getCell(i);
        cell.font = { bold: true };
        cell.alignment = { horizontal: "right" };
        cell.numFmt = "#,##0.00";
    }
    */



    // Guardar el archivo
    workbook.xlsx.writeBuffer().then(function(buffer) {
      // Crear un objeto Blob para el archivo Excel
      const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });

      // Crear un enlace de descarga
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = "PendxProd.xlsx";
      a.click();

      // Limpiar el objeto Blob
      window.URL.revokeObjectURL(url);
    });
}