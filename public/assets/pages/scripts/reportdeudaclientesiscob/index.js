$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    
    $("#rut").focus(function(){
        eliminarFormatoRut($(this));
        $("#rut").attr("cliente_id","");

        $("#razonsocial").val("");
        $("#limitecredito").val("");
        $("#TDeuda").val("");
        $("#TDeudaFec").val("");

        $('#tabla-data-consulta').DataTable().clear();
        $('#tabla-data-consulta').DataTable().draw();
        totalizar([]);

    });

    //configurarTabla('#tabla-data-consulta');
    configurarTabla("#tabla-data-consulta",[]);

});

function ejecutarConsulta(aux_cod){
    if(aux_cod == 1){
        data = datosFac(0);
        var ruta = '/reportdeudaclientesiscob/consulta';
        ajaxRequest(data.data1,ruta,'consulta');    
    }
    if(aux_cod == 2){
        data = datosFac(1);
        consultarJS(data);    
    }
    if(aux_cod == 3){
        data = datosFac(1);
        consultarExcel(data);    
    }

}

aux_nombrepdf = "";

function totalizar(datas){
    totalfac = 0;
    totaldeuda = 0;
    datas.forEach(element => {
        totalfac += element.mnttot;
        totaldeuda += parseInt(element.Deuda);
    });
    $("#totalfac").html(MASKLA(totalfac,0))
    $("#totaldeuda").html(MASKLA(totaldeuda,0))
}

var eventFired = function ( type ) {
    total = 0;
    $("#tabla-data-consulta tr .subtotalfac").each(function() {
        valor = $(this).attr('data-order') ;
        valorNum = parseFloat(valor);
        total += valorNum;
    });
    $("#subtotalfac").html(MASKLA(total,0))
    total = 0;
    $("#tabla-data-consulta tr .subtotaldeuda").each(function() {
        valor = $(this).attr('data-order') ;
        valorNum = parseFloat(valor);
        total += valorNum;
    });
    $("#subtotaldeuda").html(MASKLA(total,0))
}

$('#tabla-data-consulta').on('draw.dt', function () {
    // Aquí puedes ejecutar la función que deseas que se ejecute cuando se termine de llenar la tabla
    // Llamar a tu función aquí
    eventFired( 'Page' );
});

function configurarTabla(aux_tabla,datos){
    $(aux_tabla).DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        "order"       : [[ 0, "asc" ]],
        'data'        : datos,
        'columns'     : [
            {data: 'NroFAV'}, // 0
            {data: 'fecfact'}, // 1
            {data: 'fecvenc'}, // 2
            {data: 'mnttot'}, // 3
            {data: 'Deuda'}, // 4
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            $('td', row).eq(0).attr('style','text-align:center;');
            $('td', row).eq(1).attr('style','text-align:center');
            $('td', row).eq(1).attr('data-order',data.fecfact);
            aux_fecha = new Date(data.fecfact + " 00:00:00");
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));
            $('td', row).eq(2).attr('style','text-align:center');
            $('td', row).eq(2).attr('data-order',data.fecvenc);
            aux_fecha = new Date(data.fecvenc + " 00:00:00");
            $('td', row).eq(2).html(fechaddmmaaaa(aux_fecha));

            $('td', row).eq(3).addClass('subtotalfac');
            $('td', row).eq(3).attr('style','text-align:right');
            $('td', row).eq(3).attr('data-order',data.mnttot);
            $('td', row).eq(3).html(MASKLA(data.mnttot,0));
            $('td', row).eq(4).addClass('subtotaldeuda');
            $('td', row).eq(4).attr('style','text-align:right');
            $('td', row).eq(4).attr('data-order',data.Deuda);
            $('td', row).eq(4).html(MASKLA(data.Deuda,0));

            let id_str = data.NroFAV.toString();
            id_str = aux_nombrepdf + id_str.padStart(8, "0");
            aux_colorvenc = "";
            aux_icohand = "fa-thumbs-o-up";
            aux_titlehand = "Vigente";
            if (data.staVencida){
                aux_colorvenc = "text-red";
                aux_icohand = "fa-thumbs-o-down";
                aux_titlehand = "Vencida";
            }

            aux_text = "";
            if(data.NroFAV != null){
                aux_text = 
                `<a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="Factura" onclick="genpdfFAC('${id_str}','')">
                    ${data.NroFAV}
                </a>
                <a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="Cedible" onclick="genpdfFAC('${id_str}','_cedible')">
                    <i class="fa fa-fw fa-file-pdf-o"></i>
                </a>
                <a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="Descargar XML Factura" onclick="descArcTXT('${id_str}.xml')">
                    <i class="fa fa-fw fa-cloud-download"></i>
                </a>
                <a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="${aux_titlehand}">
                    <i class="fa fa-fw ${aux_icohand} ${aux_colorvenc}"></i>
                </a>`;
            }
            $('td', row).eq(0).attr("class","action-buttons");
            $('td', row).eq(0).html(aux_text);


            /* $(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id); */
        }
    });
}

function ajaxRequest(data,url,funcion) {
    aux_data = data;
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta1) {
            if(funcion=='consulta'){
                respuesta = respuesta1[0].datos.datacobranza;
                $("#razonsocial").val(respuesta.razonsocial);
                $("#limitecredito").val(MASKLA(respuesta.limitecredito,0));
                $("#TDeuda").val(MASKLA(respuesta.TDeuda,0));
                $("#TDeudaFec").val(MASKLA(respuesta.TDeudaFec,0));
                //configurarTabla("#tabla-data-consulta",respuesta.datosFacDeuda);
                // Limpiar los datos existentes de la tabla
                $('#tabla-data-consulta').DataTable().clear();
                // Agregar los nuevos datos al arreglo
                aux_nombrepdf = respuesta.nombrepdf
                $('#tabla-data-consulta').DataTable().rows.add(respuesta.datosTodasFacDeuda);

                // Redibujar la tabla para mostrar los nuevos datos
                $('#tabla-data-consulta').DataTable().draw();

                totalizar(respuesta.datosTodasFacDeuda);                
            }
            if(funcion=='consultarJS'){
                if (respuesta1.clientes.length <= 0) {
                    aux_text = "Verifique los filtros";
                    /* if(data.data1.sucursal_id == ""){
                        aux_text = "Debe seleccionar la Sucursal";
                    } */
                    swal({
                        title: 'Informacion no encontrada.',
                        text: aux_text,
                            icon: 'warning',
                        buttons: {
                            confirm: "Aceptar"
                        },
                    });    
                }else{
                    pdf = pdfjs(respuesta1);
                    pdf.save('deudacliente.pdf');    
                }
            }
            if(funcion=='consultarExcel'){
                if (respuesta1.clientes.length <= 0) {
                    aux_text = "Verifique los filtros";
                    /* if(data.data1.sucursal_id == ""){
                        aux_text = "Debe seleccionar la Sucursal";
                    } */
                    swal({
                        title: 'Informacion no encontrada.',
                        text: aux_text,
                            icon: 'warning',
                        buttons: {
                            confirm: "Aceptar"
                        },
                    });    
                }else{
                    exportarExcel(respuesta1)
                }
            }

		},
        error: function(xhr, status, error) {
            console.log(error);
        }
	});
}

function datosFac(GenExcel){
    aux_vendedor_id = "";
    if($("#vendedor_id").val().length > 0){
        aux_vendedor_id = $("#vendedor_id").val();
    }
    var data1 = {
        id         : $("#rut").attr("cliente_id"),
        cliente_id : $("#rut").attr("cliente_id"),
        rut        : eliminarFormatoRutret($("#rut").val()),
        sucursal_id: $("#sucursal_id").val(),
        vendedor_id: aux_vendedor_id,
        GenExcel   : GenExcel,
        statusDeuda: $("#statusDeuda").val(),
        _token     : $('input[name=_token]').val()
    };
    var data2 = "?&rut="+data1.rut +
    "&cliente_id="+data1.cliente_id +
    "&sucursal_id="+data1.sucursal_id +
    "&vendedor_id="+data1.vendedor_id +
    "&GenExcel="+data1.GenExcel +
    "&statusDeuda="+data1.statusDeuda +
    "&_token="+data1._token

    var data = {
        data1 : data1,
        data2 : data2
    };
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
                        $("#rut").attr("cliente_id",respuesta[0].id);
                        $("#razonsocial").val(respuesta[0].razonsocial);
                        $("#limitecredito").val(MASKLA(respuesta[0].limitecredito,0));
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



function consultarJS(data){
    var ruta = '/reportdeudaclientesiscob/consulta';
    ajaxRequest(data.data1,ruta,'consultarJS');
}

function pdfjs(datos) {
    var base64Img = imgToBase64('assets/lte/dist/img/LOGO-PLASTISERVI.jpg');
    var doc = new jsPDF();
    var totalPagesExp = '{total_pages_count_string}';

    // Agrupar datos por vendedor

    // Imprimir encabezado una vez
    doc.setFontSize(12);
    doc.setTextColor(20);
    if (base64Img) {
        doc.addImage(base64Img, 'JPEG', 14, 6, 30, 10);
    }
    doc.text('Estado de Cuenta Cobranza', 69, 12);
    doc.setFontSize(8);
    doc.text('Sucursal: ' + $("#sucursal_id option:selected").html(), 87, 16);
    doc.text('Fecha: ' + datos.fechaacthora, 150, 11);
    //doc.text('Periodo: ' + $("#fechad").val() + " al " + $("#fechah").val(), 160, 14);

    doc.autoTable({
        startY: 15,
        //head: headRows(),
        body: [],
        theme: 'grid',
        styles: {
            fontSize: 6, // Tamaño de letra para los encabezados
        },
        headStyles: {
            fillColor: '#0077FF', // Color de fondo azul
            textColor: '#FFFFFF', // texto blanco solo para el encabezado
            valign: 'middle' // Centrar verticalmente los títulos en el encabezado
        },
        margin: { top: 20 },
    });


    // Imprimir datos agrupados por cliente sin repetir encabezado
    datos.clientes.forEach((cliente, index) => {

        // Imprimir encabezados de columnas
        if(cliente.datos.datacobranza.datosTodasFacDeuda.length > 0){
            // Agregar título de vendedor
            doc.setFontSize(8);
            // Poner en negrita los títulos
            doc.setFont('helvetica', 'bold');
            doc.text(`RUT:`, 40, doc.autoTable.previous.finalY + 10);
            doc.text(`Razon Social:`, 70, doc.autoTable.previous.finalY + 10);
            doc.text(`Limite Crédito:`, 40, doc.autoTable.previous.finalY + 14);
            doc.text(`Deuda:`, 80, doc.autoTable.previous.finalY + 14);
            doc.text(`Deuda Vencida:`, 120, doc.autoTable.previous.finalY + 14);

            // Volver a fuente normal para los datos
            doc.setFont('helvetica', 'normal');
            doc.text(`${formato_rutVar(cliente.datos.rut)}`, 48, doc.autoTable.previous.finalY + 10);
            doc.text(`${cliente.datos.razonsocial}`, 90, doc.autoTable.previous.finalY + 10);
            doc.text(`${MASKLA(cliente.datos.limitecredito,0)}`, 61, doc.autoTable.previous.finalY + 14);
            doc.text(`${MASKLA(cliente.datos.datacobranza.TDeuda,0)}`, 91, doc.autoTable.previous.finalY + 14);
            doc.text(`${MASKLA(cliente.datos.datacobranza.TDeudaFec,0)}`, 146, doc.autoTable.previous.finalY + 14);

            doc.autoTable({
                startY: doc.autoTable.previous.finalY + 15,
                head: headRows(),
                body: bodyRows(cliente.datos.datacobranza.datosTodasFacDeuda),
                theme: 'grid',
                styles: {
                    fontSize: 6, // Tamaño de letra para los encabezados
                },
                headStyles: { 
                    fillColor: '#0077FF', // Color de fondo azul
                    textColor: '#FFFFFF', //texto blanco solo para el encabezado
                    valign: 'middle', // Centrar verticalmente los títulos en el encabezado
                },        
                columnStyles: {
                    0: { cellWidth: 20, halign: 'center'  },  // Ancho columna
                    1: { cellWidth: 20, halign: 'center' },  // Ancho columna
                    2: { cellWidth: 20, halign: 'center' },  // Ancho columna
                    3: { cellWidth: 30, halign: 'right' },  // Ancho columna
                    4: { cellWidth: 30, halign: 'right' },  // Ancho columna
                },
                didParseCell: function(data) {
                    if (data.section === 'body') {
                        const vencimiento = new Date(fechaaaaammdd(data.row.raw[2]) + " 00:00:00"); // Obtén la fecha de vencimiento
                        const today = new Date(); // Fecha actual

                        // Aplica estilo si la factura está vencida
                        if (vencimiento < today) {
                            if (data.column.index === 2 || data.column.index === 4) { // Verifica si es la columna de fecha de vencimiento o deuda
                                data.cell.styles.fillColor = [255, 230, 230]; // Fondo rojo claro
                                data.cell.styles.textColor = [255, 0, 0]; // Texto rojo
                            }
                        }
                    }
                },
                willDrawPage: function (data) {
                    // Footer
                    var str = 'Pag ' + doc.internal.getNumberOfPages();
                    if (typeof doc.putTotalPages === 'function') {
                        str = str + ' de ' + totalPagesExp;
                    }
                    doc.setFontSize(7);
                    var pageSize = doc.internal.pageSize;
                    var pageHeight = pageSize.height ? pageSize.height : pageSize.getHeight();
                    doc.text(str, data.settings.margin.left, pageHeight - 10);
                },
                margin: { top: 20, left: 40, right: 20 },
            });
        }


    });

    // Add Total Page Count if needed
    if (typeof doc.putTotalPages === 'function') {
        doc.putTotalPages(totalPagesExp);
    }

    return doc;
}

function headRows() {
    return [
      //{ id: "NV", cliente_rut: "OC", fecha: "Fecha", plazoentrega: "PlazoEnt", razonsocial: "Razón Social", comuna: "Comuna", cod: "Cod", desc: "Descripción", clase: "ClaSello", diam: "Diam Anch", l: "L", pesoesp: "Peso Esp", tu: "TU", stock: "Stock", picking: "Pick", cant: "Cant", cantdesp: "Cant Desp", cantpend: "Cant Pend", kilos: "Kilos Pend", preciokg: "Precio Kg", pesos: "$"},
      { nrofac: "N° Factura", FechaFac: "FechaFac", fechaVenc: "Fecha Venc", MontoFact: "Monto Factura", deuda: "Deuda"},
    ]
}

function bodyRows(data) {
    const body = [];
    const today = new Date(); // Fecha actual para comparación
    data.forEach(row => {
        const rowArray = [row.NroFAV,fechaddmmaaaa(new Date(row.fecfact + " 00:00:00")),fechaddmmaaaa(new Date(row.fecvenc + " 00:00:00")),MASKLA(row.mnttot,0),MASKLA(row.Deuda,0)];
        // Aplica el fondo gris si la deuda es mayor a 0
        //const rowStyle = row.Deuda > 0 ? {fillColor: [240, 240, 240]} : {};
        //body.push({row: rowArray});

        // Comprobación de si la fecha de vencimiento ha pasado
        const vencimiento = new Date(row.fecvenc);
        const isVencida = vencimiento < today;
        
        body.push([row.NroFAV,fechaddmmaaaa(new Date(row.fecfact + " 00:00:00")),fechaddmmaaaa(new Date(row.fecvenc + " 00:00:00")),MASKLA(row.mnttot,0),MASKLA(row.Deuda,0)]);
        // Aplica el fondo rojo claro si la factura está vencida
        //const rowStyle = isVencida ? {fillColor: [255, 204, 204], textColor: [255, 0, 0]} : {};

        //body.push({row: rowArray, styles: rowStyle});
    });
    return body;
}

function consultarExcel(data){
    var ruta = '/reportdeudaclientesiscob/consulta';
    ajaxRequest(data.data1,ruta,'consultarExcel');
}

function exportarExcel(datos) {
    //console.log(data);
    // Crear una matriz para los datos de Excel
    var datosExcel = [];
    // Agregar los datos de la tabla al arreglo
    aux_vendedor_id = "";
    count = 3;

    cellLengthRazonSoc = 0;
    cellLengthProducto = 0;
    filainifusionar = -1
    //console.log(data);
    aux_sucursalNombre = $("#sucursal_id option:selected").html();
    datosExcel.push(["Estado de cuenta Cobranza","","","",datos.fechaact]);
    datosExcel.push(["Centro Economico: " + aux_sucursalNombre,"","","",""]);
    //console.log(data);
    aux_rut = "";
    arrayfusionarTitulos = [];
    arrayTotales = [];
    arrayTitulos = [];
    datos.clientes.forEach(function(cliente) {
        if(cliente.datos.datacobranza.datosTodasFacDeuda.length > 0){
        /* if (cliente.vendedor_id != aux_vendedor_id){
            filainifusionar += 3;
            datosExcel.push(["","","",""]);
            datosExcel.push(["Vendedor: " + cliente.vendedor_nombre,"","","","","","","","",""]);
            datosExcel.push(["Nombre Grupo","Total $","Total Kg","Promedio"]);        
            arrayfusionarTitulos.push(filainifusionar);
        } */
            var filaExcel = [
                "",
                "",
                "",
                "",
                ""
            ];
            count++;    
            datosExcel.push(filaExcel);

            if (cliente.datos.rut != aux_rut){
                arrayfusionarTitulos.push(count);
            }
       
            filainifusionar++;
            var filaExcel = [
                "RUT: " + formato_rutVar(cliente.datos.rut) + " Razon Social: " + cliente.datos.razonsocial
            ];
            count++;
            datosExcel.push(filaExcel);
            var filaExcel = [
                "Limite Credito: " + MASKLA(cliente.datos.limitecredito,0) + "    Deuda: " + MASKLA(cliente.datos.datacobranza.TDeuda,0) + "    Deuda Vencida: " +MASKLA(cliente.datos.datacobranza.TDeudaFec,0)
            ];
            count++;    
            datosExcel.push(filaExcel);
            var filaExcel = [
                "N° Factura: ",
                "Fecha Fac",
                "Fecha Venc",
                "Monto Factura",
                "Deuda"

            ];
            count++;    
            datosExcel.push(filaExcel);
            aux_totaldeuda = 0;
            aux_totaldeudaVenc = 0;
            cliente.datos.datacobranza.datosTodasFacDeuda.forEach(function(deuda) {
                let valorNumericoDeuda = parseFloat(deuda.Deuda);
                valorNumericoDeuda = isNaN(valorNumericoDeuda) ? 0 : valorNumericoDeuda;
                var filaExcel = [
                    deuda.NroFAV,
                    fechaddmmaaaa(new Date(deuda.fecfact + " 00:00:00")),
                    fechaddmmaaaa(new Date(deuda.fecvenc + " 00:00:00")),
                    deuda.mnttot,
                    valorNumericoDeuda
                ];
                aux_totaldeuda += deuda.mnttot;
                aux_totaldeudaVenc += valorNumericoDeuda;
                count++;
                datosExcel.push(filaExcel);
    
            });
            var filaExcel = [
                "",
                "",
                "Total",
                aux_totaldeuda,
                aux_totaldeudaVenc
            ];
            arrayTotales.push(count);
            count++;    
            datosExcel.push(filaExcel);

            var filaExcel = [
                "",
                "",
                "",
                "",
                ""
            ];
            count++;    
            datosExcel.push(filaExcel);
            
        }

    });
    /* if(aux_totalMonto != 0){
        datosExcel.push(["","","","","","","","","","","","","","","Total: ",aux_totalMonto]);
    } */
    //datosExcel.push(["","","","","","","","","","","","","","","","Total: ",aux_totalMonto]);

    createExcel(datosExcel,arrayfusionarTitulos);
    // Llamar a la función para crear el archivo Excel

}

function getCellWidth(cellValue) {
    // Puedes ajustar un valor constante para el ancho mínimo que deseas asignar a la columna.
    const minimumColumnWidth = 10;

    // Calcula la longitud del valor en la celda y agrega un poco de espacio adicional.
    const cellLength = cellValue.toString().length + 2;

    // Retorna el ancho máximo entre el ancho calculado y el ancho mínimo establecido.
    return Math.max(cellLength, minimumColumnWidth);
}


  // Función para crear el archivo Excel
function createExcel(datosExcel,arrayfusionarTitulos) {
    // Crear un nuevo libro de trabajo y una nueva hoja
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Datos");

    // Insertar los datos en la hoja de trabajo
    worksheet.addRows(datosExcel);

    // Establecer negrita en la celda A1
    //worksheet.getCell("A5").font = { bold: true };


    // Ajustar automáticamente el ancho de la columna B al contenido
    ajustarcolumnaexcel(worksheet,"A");
    ajustarcolumnaexcel(worksheet,"B");
    ajustarcolumnaexcel(worksheet,"C");
    ajustarcolumnaexcel(worksheet,"D");
    ajustarcolumnaexcel(worksheet,"E");

    // Combinar celdas desde [4,0] hasta [4,2]
    arrayfusionarTitulos.forEach(function(fila) {
        // Establecer negrita en la celda Titulo por Cliente
        const row5 = worksheet.getRow(fila);
        const cellA5 = row5.getCell(1);
        cellA5.font = { bold: true };
        cellA5.alignment = { horizontal: "center", vertical: "middle" };

        //Establecer negrita a todos los titulos 2
        const row6 = worksheet.getRow(fila + 1);
        for (let i = 1; i <= 11; i++) {
            cell = row6.getCell(i);
            cell.font = { bold: true };
            cell.alignment = { horizontal: "center", vertical: "middle" };
        }

        //Establecer negrita a todos los titulos 2
        const row7 = worksheet.getRow(fila + 2);
        for (let i = 1; i <= 11; i++) {
            cell = row7.getCell(i);
            cell.font = { bold: true };
        }
        
        //Fusionar celdas Titulo Cliente
        const startCol = 0;
        const endCol = 5;
        worksheet.mergeCells(fila , startCol, fila , endCol);
        worksheet.mergeCells(fila + 1, startCol, fila + 1, endCol);
        
        // Establecer el formato de negrita en la celda superior izquierda del rango fusionado
    });

    arrayTotales.forEach(function(fila) {
        //Establecer negrita a totales
        row7 = worksheet.getRow(fila);
        for (let i = 1; i <= 6; i++) {
            cell = row7.getCell(i);
            cell.font = { bold: true };
            cell.alignment = { horizontal: "right", vertical: "middle" };
        }
    });    

    /* const row6 = worksheet.getRow(4);
    for (let i = 1; i <= 4; i++) {
        cell = row6.getCell(i);
        cell.font = { bold: true };
        if(i>1){
            cell.alignment = { horizontal: "right", vertical: "middle" };
        }
    } */

    //CENTRAR 3 1ras. COLUMNAS
    const columnA = worksheet.getColumn(1);
    columnA.eachCell({ includeEmpty: true }, (cell) => {
        cell.alignment = { horizontal: "center", vertical: "middle" };
    });
    const columnB = worksheet.getColumn(2);
    columnB.eachCell({ includeEmpty: true }, (cell) => {
        cell.alignment = { horizontal: "center", vertical: "middle" };
    });
    const columnC = worksheet.getColumn(3);
    columnC.eachCell({ includeEmpty: true }, (cell) => {
        cell.alignment = { horizontal: "center", vertical: "middle" };
    });
    // Recorrer la columna 7 y dar formato con punto para separar los miles
    const columnD = worksheet.getColumn(4);
    columnD.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });
    const columnE = worksheet.getColumn(5);
    columnE.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });

    // Establecer el formato de centrado horizontal y vertical para las celdas de la columna 8 desde la fila 4 hasta la fila 58
    /* for (let i = 4; i <= datosExcel.length; i++) {
        const cell10 = worksheet.getCell(i, 10);
        cell10.alignment = { horizontal: "center", vertical: "middle" };
        const cell11 = worksheet.getCell(i, 11);
        cell11.alignment = { horizontal: "center", vertical: "middle" };
        const cell12 = worksheet.getCell(i, 12);
        cell12.alignment = { horizontal: "center", vertical: "middle" };

        const cell14 = worksheet.getCell(i, 14);
        cell14.alignment = { horizontal: "center", vertical: "middle" };
        const cell18 = worksheet.getCell(i, 18);
        cell18.alignment = { horizontal: "center", vertical: "middle" };
    } */


    //Negrita Columna Titulo
    const row1 = worksheet.getRow(1);
    cell = row1.getCell(1);
    cell.font = { bold: true, size: 15 };
    cell.alignment = { horizontal: "center", vertical: "middle" };

    //Fecha Reporte
    const row2 = worksheet.getRow(1);
    cell = row2.getCell(4);
    cell.alignment = { horizontal: "center", vertical: "middle" };


    //Fusionar celdas de Titulo
    const startCol = 0;
    const endCol = 4;
    worksheet.mergeCells(1, startCol, 1, endCol);

    //Negrita Columna Sucursal
    const row3 = worksheet.getRow(2);
    cell = row3.getCell(1);
    cell.alignment = { horizontal: "center", vertical: "middle" };
    
    //Fusionar celdas Sucursal
    const startCol1 = 0;
    const endCol1 = 4;
    worksheet.mergeCells(2, startCol1, 2, endCol1);

    // Establecer ancho de la columna A
    worksheet.getColumn('A').width = 12;
    worksheet.getColumn('B').width = 15;
    worksheet.getColumn('C').width = 15;
    worksheet.getColumn('D').width = 15;
    worksheet.getColumn('E').width = 15;

    // Establecer negrita a totales
    /* row = worksheet.getRow(datosExcel.length);
    for (let i = 1; i <= 17; i++) {
        cell = row.getCell(i);
        cell.font = { bold: true };
        cell.alignment = { horizontal: "right" };
        cell.numFmt = "#,##0";
    } */
    

    // Guardar el archivo
    workbook.xlsx.writeBuffer().then(function(buffer) {
      // Crear un objeto Blob para el archivo Excel
      const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });

      // Crear un enlace de descarga
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = "DeudaCliente.xlsx";
      a.click();

      // Limpiar el objeto Blob
      window.URL.revokeObjectURL(url);
    });
}