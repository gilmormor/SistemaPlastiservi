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
    if(aux_filtro != 0){
        aux_sucursal_id = -1;
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

    $("#btnpdfJS").click(function()
    {
        data = datosPentxProd();
        consultarJS(data);
    });

  
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
            return 0;
            var datosExcel = [];
            //datosExcel.push(["NV", "OC", "Fecha", "Plazo Entrega", "Razón Social", "Comuna", "Cod", "Descripción", "Clase Sello", "Diam Ancho", "L", "Peso Esp", "TU", "Stock", "Picking", "Cant", "Cant Desp", "Cant Pend", "Kilos Pend", "Precio Kg", "$"]);
            console.log(datos);
            count = 0;
            datos.data.forEach(function(registro) {
                aux_fecha = new Date(registro.fechahora);
                var filaExcel = [
                    registro.notaventa_id,
                    registro.oc_id,
                    fechaddmmaaaa(aux_fecha),
                    registro.plazoentrega,
                    registro.razonsocial,
                    registro.comunanombre,
                    registro.producto_id,
                    registro.nombre,
                    registro.cla_nombre,
                    registro.diametro,
                    registro.long,
                    registro.peso,
                    registro.tipounion,
                    registro.stockbpt,
                    registro.picking,
                    registro.cant,
                    registro.cantdesp,
                    registro.cantsaldo,
                    registro.kgpend,
                    registro.precioxkilo,
                    registro.subtotalplata
                ];
                count++;
    
                datosExcel.push(filaExcel);
            });
            console.log(datosExcel);

            // Suponiendo que recibes los datos en el objeto 'datosDesdePHP'

            // Crear un nuevo documento PDF
            var pdf = new jsPDF('landscape');  // Orientación horizontal

            // Definir la posición y tamaño del encabezado
            var headerX = 10;
            var headerY = 10;
            var headerWidth = pdf.internal.pageSize.width - 20;
            var headerHeight = 30;

            // Dibujar el encabezado
            pdf.rect(headerX, headerY, headerWidth, headerHeight);
            pdf.text("Logo", headerX + 10, headerY + 20);  // Reemplazar con el logo de la empresa
            pdf.text("Nombre de la empresa", headerX + 50, headerY + 20);  // Reemplazar con el nombre de la empresa
            // Otros detalles del encabezado...

            // Crear la tabla
            var columns = ["NV", "OC", "Fecha", "Plazo Entrega", "Razón Social", "Comuna", "Cod", "Descripción", "Clase Sello", "Diam Ancho", "L", "Peso Esp", "TU", "Stock", "Picking", "Cant", "Cant Desp", "Cant Pend", "Kilos Pend", "Precio Kg", "$"];
            var rows = datosExcel;  // Supongamos que los datos se encuentran en un arreglo

            /* pdf.autoTable({
                head: [columns],
                body: rows,
                startY: headerY + headerHeight + 10,  // Posición de inicio de la tabla
            }); */

            // Calcular totales
            // ...

            // Dibujar totales y promedios
            // ...

            // Guardar el PDF
            pdf.save('reporte.pdf');

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
        doc.text('Vendedor: ' + $("#vendedor_id option:selected").html(), data.settings.margin.left + 220, 11);
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
