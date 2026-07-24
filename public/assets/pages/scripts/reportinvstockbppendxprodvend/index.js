$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $('.date-picker').datepicker({
        language: "es",
        format: "MM yyyy",
        viewMode: "years", 
        minViewMode: "months",
        autoclose: true,
		todayHighlight: true
    }).datepicker("setDate");

    configurarTabla('#tabla-data-reporte-stockpicking');

    // No se debe consultar automáticamente al ingresar a la pantalla: solo al presionar
    // #btnconsultar, #btnpdf o #btnexportarExcel. Esta bandera bloquea la carga inicial del DataTable.
    var aux_consultarSolicitado = false;

    function configurarTabla(aux_tabla){
        $(aux_tabla).DataTable({
            'paging'      : true,
            'lengthChange': true,
            'ordering'    : true,
            'info'        : true,
            'autoWidth'   : false,
            'processing'  : true,
            'serverSide'  : true,
            'ajax'        : function(dtParams, callback, settings){
                if(!aux_consultarSolicitado){
                    // Tabla se inicializa vacía, sin consultar al servidor hasta que el usuario presione Consultar
                    callback({ draw: dtParams.draw, recordsTotal: 0, recordsFiltered: 0, data: [] });
                    return;
                }
                var data = datosstockpicking();
                $.ajax({
                    url: "reportinvstockbppendxprodpage/" + data.data2,
                    type: 'GET',
                    data: dtParams,
                    dataType: 'json',
                    success: function(json){
                        callback(json);
                    }
                });
            },
            "order": [[ 12, "asc" ]],
            //Versión vendedor: se ocultan Bodega, Picking, Stock y Pend (índices 8-11); solo se muestra la diferencia (columna "Stock")
            'columnDefs'  : [
                { "targets": [8, 9, 10, 11], "visible": false }
            ],
            'columns'     : [
                {data: 'producto_id'},
                {data: 'producto_nombre'},
                {data: 'categoria_nombre'},
                {data: 'cla_nombre'},
                {data: 'diametro'},
                {data: 'largo'},
                {data: 'peso'},
                {data: 'tipounion'},
                {data: 'stockBodProdTerm'},
                {data: 'stockPiking'},
                {data: 'stock'},
                {data: 'cantpend'},
                {data: 'difcantpend'},
                //{data: 'stockkg'}
            ],
            "language": {
                //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "createdRow": function ( row, data, index ) {
                $('td', row).eq(8).attr('style','text-align:center');
                //MASKLA(data.aux_totalkg,2);
                /*
                aux_mesanno = mesanno(data.annomes);
                $('td', row).eq(1).html(aux_mesanno);
                $('td', row).eq(1).attr('data-search',aux_mesanno);
                $('td', row).eq(4).attr('data-order',data.costo);
                $('td', row).eq(4).attr('data-search',data.costo);
                $('td', row).eq(4).attr('style','text-align:right');
                $('td', row).eq(4).html(MASK(0, data.costo, '-###,###,###,##0.00',1));
                $('td', row).eq(5).attr('data-order',data.metacomerkg);
                $('td', row).eq(5).attr('data-search',data.metacomerkg);
                $('td', row).eq(5).attr('style','text-align:right');
                $('td', row).eq(5).html(MASK(0, data.metacomerkg, '-###,###,###,##0.00',1));
                */
                $('td', row).eq(0).attr('style','text-align:center');
                stockKg = data.stock * data.peso

                $('td', row).eq(4).attr('style','text-align:center');
                $('td', row).eq(5).attr('style','text-align:center');
                $('td', row).eq(6).attr('style','text-align:center');
                if(data.acuerdotecnico_id){
                    $('td', row).eq(4).html(NUM(data.at_ancho, 2));
                    $('td', row).eq(5).html(NUM(data.at_largo, 2));
                    $('td', row).eq(6).html(MASKLA(data.at_espesor, 3));    
                }else{
                    $('td', row).eq(6).html(NUM(data.peso, 2));
                }
                $('td', row).eq(7).attr('style','text-align:center');
                $('td', row).eq(9).attr('style','text-align:center');
                $('td', row).eq(9).attr('data-order',data.stockBodProdTerm);
                $('td', row).eq(9).attr('data-search',data.stockBodProdTerm);
                $('td', row).eq(10).attr('style','text-align:center');
                $('td', row).eq(10).attr('data-order',data.stockPiking);
                $('td', row).eq(10).attr('data-search',data.stockPiking);

                $('td', row).eq(11).attr('style','text-align:center');
                $('td', row).eq(11).html(NUM(data.cantpend, 0));

                $('td', row).eq(12).attr('style','text-align:center');
/*
                $('td', row).eq(13).attr('style','text-align:right');
                $('td', row).eq(13).attr('data-order',stockKg);
                $('td', row).eq(13).attr('data-search',stockKg);
                $('td', row).eq(13).html(MASKLA(stockKg,2));
                $('td', row).eq(13).addClass('subtotalkg');
*/
                //console.log(stockKg);

            }
        });
    }

    //totalizar();

    $("#btnconsultar").click(function()
    {
        aux_consultarSolicitado = true;
        $('#tabla-data-reporte-stockpicking').DataTable().ajax.reload();
        //totalizar();
    });

});

function totalizar(){
    let  table = $('#tabla-data-reporte-stockpicking').DataTable();
    //console.log(table);
    table
        .on('draw', function () {
            eventFired( 'Page' );
        });
    data = datosstockpicking();
    $.ajax({
        url: '/reportinvstockbppendxprod/totalizarindex/' + data.data2,
        type: 'GET',
        success: function (datos) {
            //console.log(datos);
            $("#totalkg").html(MASKLA(datos.aux_totalkg,2));
            //$("#totaldinero").html(MASKLA(datos.aux_totaldinero,0));
        }
    });
}

var eventFired = function ( type ) {
	total = 0;
	$("#tabla-data-reporte-stockpicking tr .subtotalkg").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#subtotalkg").html(MASKLA(total,2))
}

function datosstockpicking(){
    var data1 = {
        mesanno           : $("#annomes").val(),
        sucursal_id       : $("#sucursal_id").val(),
        producto_id       : $("#producto_idPxP").val(),
        categoriaprod_id  : $("#categoriaprod_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        tipobodega        : $("#tipobodega").val(),
        aprobstatus       : $("#aprobstatus").val(),
        aprobstatusdesc   : $("#aprobstatus option:selected").html(),
        //orden             : ordentablaGen($('#tabla-data-reporte-stockpicking').DataTable()),
        claseprod_id      : $("#claseprod_id").val(),
        _token            : $('input[name=_token]').val()
    };

    var data2 = "?mesanno="+data1.mesanno +
    "&sucursal_id="+data1.sucursal_id +
    "&producto_id="+data1.producto_id +
    "&categoriaprod_id="+data1.categoriaprod_id +
    "&areaproduccion_id="+data1.areaproduccion_id +
    "&tipobodega="+data1.tipobodega +
    "&aprobstatus="+data1.aprobstatus +
    "&aprobstatusdesc="+data1.aprobstatusdesc +
    "&claseprod_id="+data1.claseprod_id;
    //"&orden=" + ordentablaGen($('#tabla-data-reporte-stockpicking').DataTable())


    var data = {
        data1 : data1,
        data2 : data2
    };
    return data;
}

$("#btnbuscarproducto").click(function(event){
    $(this).val("");
    $(".input-sm").val('');
    if (!tablaProductoInicializada) {
        configTablaProd(); // ← AQUÍ recién se inicializa
        tablaProductoInicializada = true;
    }
    /* data = datos();
    $('#tabla-data-productos').DataTable().ajax.url( "producto/productobuscarpage/" + data.data2 + "&producto_id=" ).load(); */
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
    data = datosstockpicking();
    table = $('#tabla-data-reporte-stockpicking').DataTable();
    aux_orden = ordentablaGen(table);
    // Acceder al th de esa columna en el thead
    var thElement = table.columns(aux_orden[0]).header();
    let aux_ordenString = $(thElement).attr("nomcampo") + "," + aux_orden[1];

    //alert(cadena);
    $('#contpdf').attr('src', '/reportinvstockbppendxprodvend/exportPdf/'+data.data2+"&aux_orden="+aux_ordenString);
    //$('#contpdf').attr('src', '/notaventa/'+id+'/'+stareport+'/exportPdf');
	$("#myModalpdf").modal('show')
});


function exportarExcel() {
    data = datosstockpicking();
    // Obtener todos los registros mediante una solicitud AJAX
    $.ajax({
        url: "reportinvstockbppendxprodpage/" + data.data2, // ajusta la URL de la solicitud al endpoint correcto
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
        //return 0;
        // Crear una matriz para los datos de Excel
        var datosExcel = [];
        // Agregar los datos de la tabla al arreglo
        aux_vendedor_id = "";
        count = 0;

        cellLengthRazonSoc = 0;
        cellLengthProducto = 0;
        filainifusionar = -1
        //console.log(data);
        aux_mesanno = $("#annomes").val();
        datosExcel.push(["Pendiente por Producto","","","","","","","","","","","",fechaactual()]);
        datosExcel.push(["Fecha: " + aux_mesanno,"","","","","","","",""]);
        aux_totalStock = 0;
        datosExcel.push(["","","","","","","","",""]);
        //Versión vendedor: sin Bodega, Picking, Stock ni Pend; la diferencia se muestra como "Stock"
        datosExcel.push(["Cod","Producto","Categoria","Clase/Sello","Diam/Ancho","L","Peso/Esp","TU","Stock"]);
        data.data.forEach(function(registro) {
            aux_totalStock += registro.stock;
            filainifusionar++;
            if(registro.cantpend <= 0){
                registro.cantpend = 0;
            }

            var filaExcel = [
                registro.producto_id,
                registro.producto_nombre,
                registro.categoria_nombre,
                registro.cla_nombre,
                registro.diametro,
                registro.largo,
                registro.peso,
                registro.tipounion,
                registro.difcantpend
            ];
            count++;

            datosExcel.push(filaExcel);
        });
        //datosExcel.push(["","","","","","","","","","","Total: ",aux_totalStock]);
        datosExcel.push(["","","","","","","","","","",""]);

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
    //ajustarcolumnaexcel(worksheet,"G");
    ajustarcolumnaexcel(worksheet,"H");
    ajustarcolumnaexcel(worksheet,"I");
    ajustarcolumnaexcel(worksheet,"J");
    ajustarcolumnaexcel(worksheet,"K");
    //ajustarcolumnaexcel(worksheet,"L");

    //Establecer negrilla a titulo de columnas Fila 4
    const row6 = worksheet.getRow(4);
    for (let i = 1; i <= 13; i++) {
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
    // Supongamos que deseas ajustar el texto en la fila 4 y hacer que las celdas en negKita
    // Supongamos que deseas ajustar el texto en la fila 4 y hacer que las celdas en negLita
    fila = 4;

    // Iterar a través de las celdas en la fila y configurar el formato
    for (let i = 1; i <= 13; i++) {
        columna = getColumnLetter(i); // Obten la letra de la columna correspondiente
        const celda = worksheet.getCell(`${columna}${fila}`);
        celda.alignment = { wrapText: true, vertical: 'middle' };
        celda.autosize = true;
    }    


    // Recorrer la columna 7 y dar formato con punto para separar los miles
    const columnG = worksheet.getColumn(6);
    columnG.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
            cell.numFmt = "#,##0.00";
        }
    });

    // Recorrer la columna R y dar formato con punto para separar los miles
    const columnR = worksheet.getColumn(7);
    columnR.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });

    const columnI = worksheet.getColumn(9);
    columnI.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });

    const columnJ = worksheet.getColumn(10);
    columnJ.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });

    const columnK = worksheet.getColumn(11);
    columnK.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });

    const columnL = worksheet.getColumn(12);
    columnL.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });


    const columnM = worksheet.getColumn(13);
    columnM.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });

    // Establecer el formato de centrado horizontal y vertical para las celdas de la columna 8 desde la fila 4 hasta la fila 58
    for (let i = 4; i <= datosExcel.length; i++) {
        const cell8 = worksheet.getCell(i, 8);
        cell8.alignment = { horizontal: "center", vertical: "middle" };
        /* const cell9 = worksheet.getCell(i, 9);
        cell9.alignment = { horizontal: "center", vertical: "middle" }; */
        /* const cell10 = worksheet.getCell(i, 10);
        cell10.alignment = { horizontal: "center", vertical: "middle" }; */

        /* const cell = worksheet.getCell(i, 13);
        cell.alignment = { horizontal: "center", vertical: "middle" }; */

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

    //Titulo Monto
    rowX = worksheet.getRow(4);
    cell = rowX.getCell(10);
    cell.alignment = { horizontal: "center", vertical: "middle" };

    //Titulo Monto
    rowX = worksheet.getRow(4);
    cell = rowX.getCell(11);
    cell.alignment = { horizontal: "center", vertical: "middle" };

    //Titulo Monto
    rowX = worksheet.getRow(4);
    cell = rowX.getCell(12);
    cell.alignment = { horizontal: "center", vertical: "middle" };

    //Titulo Monto
    rowX = worksheet.getRow(4);
    cell = rowX.getCell(13);
    cell.alignment = { horizontal: "center", vertical: "middle" };

    //Fecha Reporte
    const row2 = worksheet.getRow(1);
    cell = row2.getCell(10);
    cell.alignment = { horizontal: "center", vertical: "middle" };


    //Fusionar celdas de Titulo
    const startCol = 0;
    const endCol = 12;
    worksheet.mergeCells(1, startCol, 1, endCol);

    //Negrita Columna Sucursal
    const row3 = worksheet.getRow(2);
    cell = row3.getCell(1);
    cell.alignment = { horizontal: "center", vertical: "middle" };
    
    //Fusionar celdas Sucursal
    const startCol1 = 0;
    const endCol1 = 12;
    worksheet.mergeCells(2, startCol1, 2, endCol1);

    // Establecer negrita a totales
    row = worksheet.getRow(datosExcel.length);
    for (let i = 1; i <= 12; i++) {
        cell = row.getCell(i);
        cell.font = { bold: true };
        cell.alignment = { horizontal: "right" };
        cell.numFmt = "#,##0.00";
    }

    cell = row.getCell(7);
    cell.font = { bold: true };
    cell.alignment = { horizontal: "right" };
    cell.numFmt = "#,##0";


    // Guardar el archivo
    workbook.xlsx.writeBuffer().then(function(buffer) {
      // Crear un objeto Blob para el archivo Excel
      const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });

      // Crear un enlace de descarga
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = "invpendxprod.xlsx";
      a.click();

      // Limpiar el objeto Blob
      window.URL.revokeObjectURL(url);
    });
}