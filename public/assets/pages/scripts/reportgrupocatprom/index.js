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
        consultarpage(datosgrupocatprom("",0));
    });
    $("#btnpdf2").click(function()
    {
        btnpdf(datosgrupocatprom("",0));
    });
    consultarpage(datosgrupocatprom("",0));
});

function consultarpage(aux_data){
    //return 0;
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
        'ajax'        : "/reportgrupocatprom/reportgrupocatprompage/" + aux_data.data2, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
        'columns'     : [
            {data: 'vendedor_nombre'},     // 0
            {data: 'nombre'},     // 1
            {data: 'subtotal'}, // 2
            {data: 'totalkilos'}, // 3
            {data: 'promedio'}, // 4
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            $(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);

            /* $('td', row).eq(11).attr('data-order',data.montoitem);
            $('td', row).eq(11).attr('data-search',data.montoitem);
            $('td', row).eq(11).html(MASKLA(data.montoitem,0));
            $('td', row).eq(11).addClass('subtotalmonto'); */

            $('td', row).eq(2).attr('style','text-align:right');
            $('td', row).eq(3).attr('style','text-align:right');
            $('td', row).eq(4).attr('style','text-align:right');

            aux_text = MASKLA(data.subtotal,0);
            $('td', row).eq(2).html(aux_text);
            aux_text = MASKLA(data.totalkilos,2);
            $('td', row).eq(3).html(aux_text);
            aux_text = MASKLA(data.promedio,0);
            $('td', row).eq(4).html(aux_text);

        }
    });
    //totalizar();
}


function totalizar(){
    let  table = $('#tabla-data-consulta').DataTable();
    //console.log(table);
    table
        .on('draw', function () {
            eventFired( 'Page' );
        });
    data = datosgrupocatprom("",0);
    $.ajax({
        url: '/reportdteguiadespdet/totalizarindex/' + data.data2,
        type: 'GET',
        success: function (datos) {
            $("#totalmonto").html(MASKLA(datos.aux_total,0));
            $("#totalcomision").html(MASKLA(datos.aux_totalcomision,0));
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
    total = 0;
    $("#tabla-data-consulta tr .subtotalmontocomision").each(function() {
        valor = $(this).attr('data-order') ;
        valorNum = parseFloat(valor);
        total += valorNum;
    });
    $("#subtotalcomision").html(MASKLA(total,0))

    
}

function datosgrupocatprom(orderby = "",aux_genexcel){
    var data1 = {
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        sucursal_id       : $("#sucursal_id").val(),
        rut               : eliminarFormatoRutret($("#rut").val()),
        vendedor_id       : $("#vendedor_id").val(),
        filtro            : 1,
        statusgen         : 1,
        foliocontrol_id   : "",
        orderby           : orderby,
        groupby           : "",
        fechahoy          : "",
        genexcel          : aux_genexcel,
        areaproduccion_id : $("#areaproduccion_id").val(),
        producto_id       : $("#producto_idPxP").val(),
        indtraslado       : $("#indtraslado").val(),
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
    "&orderby="+data1.orderby +
    "&genexcel="+data1.genexcel +
    "&areaproduccion_id="+data1.areaproduccion_id +
    "&producto_id="+data1.producto_id +
    "&indtraslado="+data1.indtraslado +
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
    $('#contpdf').attr('src', '/reportdteguiadespdet/exportPdf/'+data.data2);
    $("#myModalpdf").modal('show');
}

function exportarExcel() {
    var tabla = $('#tabla-data-consulta').DataTable();
    orderby = " order by foliocontrol.doc,dte.id ";
    data = datosgrupocatprom(orderby,1);
    // Obtener todos los registros mediante una solicitud AJAX
    $.ajax({
      url: "/reportgrupocatprom/reportgrupocatprompage/" + data.data2, // ajusta la URL de la solicitud al endpoint correcto
      type: 'POST',
      dataType: 'json',
      success: function(data) {
        if(data.datos.length == 0){
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
		count = 0;

        cellLengthRazonSoc = 0;
        cellLengthProducto = 0;
        filainifusionar = -1
        //console.log(data);
        aux_sucursalNombre = $("#sucursal_id option:selected").html();
        aux_rangofecha = $("#fechad").val() + " al " + $("#fechah").val()
        datosExcel.push(["Precio promedio NV","","",data.fechaact]);
        datosExcel.push(["Centro Economico: " + aux_sucursalNombre + " Entre: " + aux_rangofecha,"","",""]);
        //console.log(data);
        aux_vendedor_id = "";
        arrayfusionarCelNomVend = [];
        data.datos.forEach(function(registro) {
            if (registro.vendedor_id != aux_vendedor_id){
                filainifusionar += 3;
                datosExcel.push(["","","",""]);
                datosExcel.push(["Vendedor: " + registro.vendedor_nombre,"","","","","","","","",""]);
                datosExcel.push(["Nombre Grupo","Total $","Total Kg","Promedio"]);        
                arrayfusionarCelNomVend.push(filainifusionar);
            }
            
            filainifusionar++;
            var filaExcel = [
                registro.nombre,
                registro.subtotal,
                registro.totalkilos,
                registro.promedio
            ];
            aux_vendedor_id = registro.vendedor_id;
            count++;

            datosExcel.push(filaExcel);
        });
        /* if(aux_totalMonto != 0){
            datosExcel.push(["","","","","","","","","","","","","","","Total: ",aux_totalMonto]);
        } */
        //datosExcel.push(["","","","","","","","","","","","","","","","Total: ",aux_totalMonto]);

        createExcel(datosExcel,arrayfusionarCelNomVend);

      },
      error: function(xhr, status, error) {
        console.log(error);
      }
    });


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
function createExcel(datosExcel,arrayfusionarCelNomVend) {
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
    ajustarcolumnaexcel(worksheet,"F");

    // Combinar celdas desde [4,0] hasta [4,2]
    arrayfusionarCelNomVend.forEach(function(fila) {
    
        // Establecer negrita a totales
        /* row = worksheet.getRow(fila + 2 -2);
        for (let i = 1; i <= 11; i++) {
            cell = row.getCell(i);
            cell.font = { bold: true };
            cell.alignment = { horizontal: "right" };
            cell.numFmt = "#,##0";
        }

        row = worksheet.getRow(datosExcel.length);
        for (let i = 1; i <= 11; i++) {
            cell = row.getCell(i);
            cell.font = { bold: true };
            cell.alignment = { horizontal: "right" };
            cell.numFmt = "#,##0";
        } */

        // Establecer negrita en la celda de Vendedor
        const row5 = worksheet.getRow(fila + 2);
        const cellA5 = row5.getCell(1);
        cellA5.font = { bold: true };
        cellA5.alignment = { horizontal: "center", vertical: "middle" };

        //Establecer negrita a todos los titulos debajo de vendedor
        const row6 = worksheet.getRow(fila + 3);
        for (let i = 1; i <= 11; i++) {
            cell = row6.getCell(i);
            cell.font = { bold: true };
            if(i>1){
                cell.alignment = { horizontal: "right", vertical: "middle" };
            }
        }

        //Fusionar celdas de vendedor
        const startCol = 0;
        const endCol = 4;
        worksheet.mergeCells(fila + 2, startCol, fila + 2, endCol);
        
        // Establecer el formato de negrita en la celda superior izquierda del rango fusionado
    });
    

    /* const row6 = worksheet.getRow(4);
    for (let i = 1; i <= 4; i++) {
        cell = row6.getCell(i);
        cell.font = { bold: true };
        if(i>1){
            cell.alignment = { horizontal: "right", vertical: "middle" };
        }
    } */


    // Recorrer la columna 7 y dar formato con punto para separar los miles
    const columnB = worksheet.getColumn(2);
    columnB.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });
    const columnC = worksheet.getColumn(3);
    columnC.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });
    const columnD = worksheet.getColumn(4);
    columnD.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
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
    const endCol = 3;
    worksheet.mergeCells(1, startCol, 1, endCol);

    //Negrita Columna Sucursal
    const row3 = worksheet.getRow(2);
    cell = row3.getCell(1);
    cell.alignment = { horizontal: "center", vertical: "middle" };
    
    //Fusionar celdas Sucursal
    const startCol1 = 0;
    const endCol1 = 4;
    worksheet.mergeCells(2, startCol1, 2, endCol1);

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
      a.download = "PrecioPromNV.xlsx";
      a.click();

      // Limpiar el objeto Blob
      window.URL.revokeObjectURL(url);
    });
}

function exportarExcelLosPinos() {
    var tabla = $('#tabla-data-consulta').DataTable();
    orderby = " order by foliocontrol.doc,dte.id ";
    data = datosgrupocatprom(orderby,1);
    // Obtener todos los registros mediante una solicitud AJAX
    $.ajax({
        url: "/reportgrupocatprom/reportgrupocatprompage/" + data.data2, // ajusta la URL de la solicitud al endpoint correcto
        type: 'POST',
        dataType: 'json',
        success: function(data) {
        if(data.datos.length == 0){
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
        count = 0;

        cellLengthRazonSoc = 0;
        cellLengthProducto = 0;
        filainifusionar = -1
        //console.log(data);
        aux_sucursalNombre = $("#sucursal_id option:selected").html();
        aux_rangofecha = $("#fechad").val() + " al " + $("#fechah").val()
        datosExcel.push(["Estadistica Ventas","","","","","","","","","","","","","","",data.fechaact]);
        datosExcel.push(["Centro Economico: " + aux_sucursalNombre + " Entre: " + aux_rangofecha,"","","","","","","","","","","","",""]);
        aux_totalMonto = 0;
        aux_totalComision = 0;
        datosExcel.push(["","","","","","","","","","","","","","",""]);
        datosExcel.push(["Tipo Doc","NDoc","Fecha","Cliente","RUT","CodProd","Producto","Ancho","Largo","Espesor","MateriaPrima","Cant","UN","Vendedor","Kg","Neto","Categoria","Precio","Peso Nominal","Peso Real","Kg Desp","Precio x Kg","Venta $","Costo Formula Nom","Margen x Kg","Margen Total","Margen real ventas","Kg Desp Nom","Precio UniNom","Precio x Kg Nom","Ventas $ Nom","Costo Formula Nom","Margen x Unid Nom","Margen Total Nom","Margen % peso Nom"]);
        data.datos.forEach(function(registro) {
            aux_totalMonto += registro.montoitem;
            aux_totalComision += registro.comision;
            filainifusionar++;
            aux_length = registro.razonsocial.toString().length
            if(aux_length > cellLengthRazonSoc){
                cellLengthRazonSoc = aux_length;
            }
            aux_length = registro.nmbitem.toString().length
            if(aux_length > cellLengthProducto){
                cellLengthProducto = aux_length;
            }
            aux_precioxkg = 0;
            if(registro.itemkg>0){
                aux_precioxkg = registro.montoitem/registro.itemkg;

            }
            aux_fecha = new Date(registro.fchemis + " 00:00:00");

            var filaExcel = [
                registro.foliocontrol_doc, //"Tipo Doc",
                registro.nrodocto, //"NDoc",
                fechaddmmaaaa(aux_fecha), //"Fecha",
                registro.razonsocial, //"Cliente",
                registro.rut, //"RUT",
                registro.producto_id, //"CodProd",
                registro.nmbitem, //"Producto",
                registro.at_ancho, //"Ancho"
                registro.at_largo, //,"Largo"
                registro.at_espesor, //,"Espesor",
                registro.materiaprima_nombre, //"MateriaPrima",
                registro.qtyitem, //"Cant",
                registro.unidadmedida_nombre, //"UN"
                registro.vendedor_nombre, //,"Vendedor"
                registro.itemkg, //,"Kg",
                registro.montoitem, //"Neto",
                registro.gru_nombre, //"Categoria",
                registro.prcitem, //"Precio",
                registro.itemkg/registro.qtyitem, //"Peso Nominal",
                registro.itemkg/registro.qtyitem, //"Peso Real",
                0, //"Kg Desp",
                aux_precioxkg, //"Precio x Kg"
                registro.montoitem, //,"Venta $",
                registro.costo, //"Costo Formula Nom",
                "=L7-R7", //"Margen x Kg",
                "=S7*O7", //"Margen Total",
                "=J7*M7", //"Margen real ventas",
                registro.itemkg, //"Kg Desp Nom"
                registro.prcitem, //"Precio UniNom",
                aux_precioxkg, //"Precio x Kg Nom",
                registro.montoitem,//"Ventas $ Nom",
                registro.costo, //"Costo Formula Nom",
                0, //"Margen x Unid Nom",
                0, //"Margen Total Nom"
                0, //"Margen % peso Nom"
            ];
            aux_vendedor_id = registro.vendedor_id;
            count++;

            datosExcel.push(filaExcel);
        });
        /* if(aux_totalMonto != 0){
            datosExcel.push(["","","","","","","","","","","","","","","Total: ",aux_totalMonto,""]);
        } */
        datosExcel.push(["","","","","","","","","","","","","","","Total: ",aux_totalMonto,""]);

        createExcelLosPinos(datosExcel);

      },
      error: function(xhr, status, error) {
        console.log(error);
      }
    });


    // Llamar a la función para crear el archivo Excel

}

function createExcelLosPinos(datosExcel) {
    // Crear un nuevo libro de trabajo y una nueva hoja
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Datos");

    // Insertar los datos en la hoja de trabajo
    worksheet.addRows(datosExcel);

    // Establecer negrita en la celda A1
    //worksheet.getCell("A5").font = { bold: true };


    // Ajustar automáticamente el ancho de la columna B al contenido
    ajustarcolumnaexcel(worksheet,"C");
    ajustarcolumnaexcel(worksheet,"D");
    ajustarcolumnaexcel(worksheet,"E");
    ajustarcolumnaexcel(worksheet,"F");
    ajustarcolumnaexcel(worksheet,"G");
    ajustarcolumnaexcel(worksheet,"H");
    ajustarcolumnaexcel(worksheet,"I");
    ajustarcolumnaexcel(worksheet,"N");
    ajustarcolumnaexcel(worksheet,"Q");
    ajustarcolumnaexcel(worksheet,"AJ");
    ajustarcolumnaexcel(worksheet,"AK");
    ajustarcolumnaexcel(worksheet,"AL");
    
    //Establecer negrilla a titulo de columnas Fila 4
    const row6 = worksheet.getRow(4);
    for (let i = 1; i <= 38; i++) {
        cell = row6.getCell(i);
        cell.font = { bold: true };
        cell.autosize = true;
    }

    // Obtén el objeto de la columna y establece la propiedad hidden en true
    columnhidden = worksheet.getColumn("H");
    columnhidden.hidden = true;
    columnhidden = worksheet.getColumn("I");
    columnhidden.hidden = true;
    columnhidden = worksheet.getColumn("J");
    columnhidden.hidden = true;
    columnhidden = worksheet.getColumn("K");
    columnhidden.hidden = true;
    /*
    columnhidden = worksheet.getColumn("N");
    columnhidden.hidden = true;
    */

    //AJUSTAR EL TEXTO CELDAS A4:AI4
    // Supongamos que deseas ajustar el texto en la fila 4 y hacer que las celdas en negrita
    fila = 4;

    // Iterar a través de las celdas en la fila y configurar el formato
    for (let i = 1; i <= 38; i++) {
        columna = getColumnLetter(i); // Obten la letra de la columna correspondiente
        const celda = worksheet.getCell(`${columna}${fila}`);
        celda.alignment = { wrapText: true, vertical: 'middle' };
        celda.autosize = true;
    }    


    // Recorrer la columna 7 y dar formato con punto para separar los miles
    const columnG = worksheet.getColumn(16);
    columnG.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });

    // Recorrer la columna R y dar formato con punto para separar los miles
    const columnR = worksheet.getColumn(18);
    columnR.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });

    // Recorrer la columna S y dar formato con punto para separar los miles
    const columnS = worksheet.getColumn(19);
    columnS.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });

    // Recorrer la columna T y dar formato con punto para separar los miles
    const columnT = worksheet.getColumn(20);
    columnT.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });
    
    // Recorrer la columna U y dar formato con punto para separar los miles
    const columnU = worksheet.getColumn(21);
    columnU.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });

    // Recorrer la columna V y dar formato con punto para separar los miles
    const columnV = worksheet.getColumn(22);
    columnV.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });

    // Recorrer la columna W y dar formato con punto para separar los miles
    const columnW = worksheet.getColumn(23);
    columnW.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });

    // Recorrer la columna X y dar formato con punto para separar los miles
    const columnX = worksheet.getColumn(24);
    columnX.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });

    // Recorrer la columna Y y dar formato con punto para separar los miles
    const columnY = worksheet.getColumn(25);
    columnY.eachCell({ includeEmpty: true }, (cell) => {
        cell.numFmt = "#,##0.00";
    });
    
    // Recorrer la columna Z y dar formato con punto para separar los miles
    const columnZ = worksheet.getColumn(26);
    columnZ.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null) {
        cell.numFmt = "#,##0";
        }
    });

    // Recorrer la columna AA y dar formato con punto para separar los miles
    const columnAA = worksheet.getColumn(27);
    columnAA.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null) {
        cell.numFmt = "0%";
        }
    });

    // Recorrer la columna AB y dar formato con punto para separar los miles
    const columnAB = worksheet.getColumn(28);
    columnAB.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });

    // Recorrer la columna AC y dar formato con punto para separar los miles
    const columnAC = worksheet.getColumn(29);
    columnAC.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });

    // Recorrer la columna AD y dar formato con punto para separar los miles
    const columnAD = worksheet.getColumn(30);
    columnAD.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });

    // Recorrer la columna AE y dar formato con punto para separar los miles
    const columnAE = worksheet.getColumn(31);
    columnAE.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });

    // Recorrer la columna AF y dar formato con punto para separar los miles
    const columnAF = worksheet.getColumn(32);
    columnAF.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });

    // Recorrer la columna AG y dar formato con punto para separar los miles
    const columnAG = worksheet.getColumn(33);
    columnAG.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });

    // Recorrer la columna AH y dar formato con punto para separar los miles
    const columnAH = worksheet.getColumn(34);
    columnAH.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });

    // Recorrer la columna AI y dar formato con punto para separar los miles
    const columnAI = worksheet.getColumn(35);
    columnAI.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "0%";
        }
    });
    

    // Establecer el formato de centrado horizontal y vertical para las celdas de la columna 8 desde la fila 4 hasta la fila 58
    for (let i = 4; i <= datosExcel.length; i++) {
        const cell8 = worksheet.getCell(i, 8);
        cell8.alignment = { horizontal: "center", vertical: "middle" };
        const cell9 = worksheet.getCell(i, 9);
        cell9.alignment = { horizontal: "center", vertical: "middle" };
        const cell10 = worksheet.getCell(i, 10);
        cell10.alignment = { horizontal: "center", vertical: "middle" };

        const cell13 = worksheet.getCell(i, 13);
        cell13.alignment = { horizontal: "center", vertical: "middle" };

        const cell36 = worksheet.getCell(i, 36);
        cell36.alignment = { horizontal: "center", vertical: "middle" };
        const cell38 = worksheet.getCell(i, 38);
        cell38.alignment = { horizontal: "center", vertical: "middle" };
    }

    for (let i = 5; i <= datosExcel.length-1; i++) {
        //Kg Desp
        var formula = `T${i}*L${i}`;
        worksheet.getCell(`U${i}`).value = { formula: formula };
        /*
        //Precio x Kg
        formula = `SI.ERROR(P${i}/U${i}, 0)`; 
        worksheet.getCell(`V${i}`).value = { formula: formula };
        */
        //Margen x Kg
        formula = `V${i}-X${i}`; 
        worksheet.getCell(`Y${i}`).value = { formula: formula };
        //Margen Total
        formula = `Y${i}*U${i}`; 
        worksheet.getCell(`Z${i}`).value = { formula: formula };
        //Margen real ventas
        formula = `Z${i}/W${i}`; 
        worksheet.getCell(`AA${i}`).value = { formula: formula };
        //Margen x Unid Nom
        formula = `AD${i}-AF${i}`; 
        worksheet.getCell(`AG${i}`).value = { formula: formula };
        //Margen Total Nom
        formula = `AB${i}*AG${i}`; 
        worksheet.getCell(`AH${i}`).value = { formula: formula };
        //Margen Total Nom
        formula = `AH${i}/AE${i}`; 
        worksheet.getCell(`AI${i}`).value = { formula: formula };
    }

    /*
    // Ajustar automáticamente el ancho de las columnas al contenido
    worksheet.columns.forEach((column) => {
        let maxLength = 0;
        column.eachCell({ includeEmpty: true }, (cell) => {
        const length = cell.value ? cell.value.toString().length : 0;
        if (length > maxLength) {
            maxLength = length;
        }
        });
        column.width = maxLength < 10 ? 10 : maxLength;
    });
    */

    //Negrita Columna Titulo
    const row1 = worksheet.getRow(1);
    cell = row1.getCell(1);
    cell.font = { bold: true, size: 20 };
    cell.alignment = { horizontal: "center", vertical: "middle" };

    //Fecha Reporte
    const row2 = worksheet.getRow(1);
    cell = row2.getCell(16);
    cell.alignment = { horizontal: "center", vertical: "middle" };


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
    row = worksheet.getRow(datosExcel.length);
    for (let i = 1; i <= 16; i++) {
        cell = row.getCell(i);
        cell.font = { bold: true };
        cell.alignment = { horizontal: "right" };
        cell.numFmt = "#,##0";
    }
    

    // Guardar el archivo
    workbook.xlsx.writeBuffer().then(function(buffer) {
      // Crear un objeto Blob para el archivo Excel
      const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });

      // Crear un enlace de descarga
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = "EstadistivaVentas.xlsx";
      a.click();

      // Limpiar el objeto Blob
      window.URL.revokeObjectURL(url);
    });
}