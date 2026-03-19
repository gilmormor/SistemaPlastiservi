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

    //configurarTabla('#tabla-data-invstock');


    arrayBodegas = [];
    $("#invbodega_id option").each(function(){
        //console.log(this);
        //console.log('Opcion: '+$(this).text()+' Valor: '+ $(this).attr('value')+' Sucursal: '+ $(this).attr('sucursal_id'));
        var objeto =   {
            id: $(this).attr('value'),
            nombre: $(this).text(),
            sucursal_id: $(this).attr('sucursal_id')
        };
        arrayBodegas.push(objeto);
    });
    //Lo agregas al array.
    $("#invbodega_id").empty();
    $(".selectpicker").selectpicker('refresh');
    if($("#sucursal_id").val() > 0){
		llenarbodegas($("#sucursal_id").val())
	}
/* //Santa Ester
        $('#tabla-data-consulta').DataTable().ajax.url( "invcontrolpage/" + data.data2 ).load();
        totalizar();
    });

    tablascolsultainv($("#sucursal_id").val());
*/
    configurarTabla("#tabla-data-invstock","",false)

});

var tabla;

function configurarTabla(nombreTabla,url,serverSide) {
    // Si ya hay una tabla inicializada, la destruimos
    if ($.fn.DataTable.isDataTable(nombreTabla)) {
        $(nombreTabla).DataTable().destroy();
        $(nombreTabla).empty(); // Limpia la tabla para evitar errores de redibujado
    }
    //Asegura que la tabla tiene el encabezado correcto
    if ($(nombreTabla + " thead").length === 0) {
        $(nombreTabla).append(`
            <thead>
                <tr>
                    <th class="width70 tooltipsC" title="Codigo Producto" style='text-align:center'>Cod</th>
                    <th>Producto</th>
                    <th>Categoria</th>
                    <th>Clase<br>Sello</th>
                    <th>Diam<br>Ancho</th>
                    <th>Largo</th>
                    <th>Peso<br>Esp</th>
                    <th title="Tipo de Union">TU</th>
                    <th>Bodega</th>
                    <th style='text-align:center' title="Stock inicio de mes">Ini</th>
                    <th style='text-align:center' title="Suma total entradas del mes">Ent</th>
                    <th style='text-align:center' title="Suma total salidas del mes">Sal</th>
                    <th style='text-align:center' title="Stock bodega">Stock</th>
                    <th style='text-align:right' title="Stock Kg bodega">Stock Kg</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                </tr>
                <tr>
                    <th colspan='12' style='text-align:right'>Total página</th>
                    <th id='subtotalstock' name='subtotalstock' style='text-align:right'>0</th>
                    <th id='subtotalkg' name='subtotalkg' style='text-align:right'>0,00</th>
                </tr>
                <tr>
                    <th colspan='12' style='text-align:right'>TOTAL GENERAL</th>
                    <th id='totalstock' name='totalstock' style='text-align:right'>0</th>
                    <th id='totalkg' name='totalkg' style='text-align:right'>0,00</th>
                </tr>
            </tfoot>

        `);
    }

    // Configura DataTable con `serverSide` dinámico
    tabla = $(nombreTabla).DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        //'serverSide'  : true,
        //'ajax'        : "reportinvstockpage/" + data.data2, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
        'serverSide'  : serverSide,
        'ajax'        : url, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
        "order": [[ 1, "asc" ]],
        'columns'     : [
            {data: 'producto_id'},
            {data: 'producto_nombre'},
            {data: 'categoria_nombre'},
            {data: 'cla_nombre'},
            {data: 'diametro'},
            {data: 'long'},
            {data: 'peso'},
            {data: 'tipounion'},
            {data: 'invbodega_nombre'},
            {data: 'stockini'},
            {data: 'mov_in'},
            {data: 'mov_out'},
            {data: 'stock'},
            {data: 'stockkg'}
        ],
        "language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            /*
            if(data.stock <= 0){
                $(row).hide();                    
            }*/
            $('td', row).eq(0).attr('style','text-align:center');
            $('td', row).eq(3).attr('style','text-align:center');

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
            $('td', row).eq(9).attr('style','text-align:center');
            $('td', row).eq(10).attr('style','text-align:center');
            $('td', row).eq(11).attr('style','text-align:center');
            $('td', row).eq(12).attr('style','text-align:right');
            $('td', row).eq(12).attr('data-order',data.stock);
            $('td', row).eq(12).attr('data-search',data.stock);
            $('td', row).eq(12).html(MASKLA(data.stock,0));
            $('td', row).eq(12).addClass('subtotalstock');

            $('td', row).eq(13).attr('style','text-align:right');
            //$('td', row).eq(13).html(MASK(0, data.stockkg, '-###,###,###,##0.00',1));
            if(data.peso <= 0){
                stockKg = data.stockkg;
            }else{
                stockKg = data.stock * data.peso;
            }
            $('td', row).eq(13).attr('data-order',stockKg);
            $('td', row).eq(13).attr('data-search',stockKg);
            $('td', row).eq(13).html(MASKLA(stockKg,2));
            $('td', row).eq(13).addClass('subtotalkg');
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
        }
    });
}

totalizar();

$("#btnconsultar").click(function()
{
    data = datosinvstock();
    var newUrl = "invcontrolpage/" + data.data2;
    
    configurarTabla("#tabla-data-invstock",newUrl,true); // Reinicia DataTable con `serverSide: true`

    //$('#tabla-data-invstock').DataTable().ajax.url( "invcontrolpage/" + data.data2 ).load();
    totalizar();
});

function totalizar(){
    let  table = $('#tabla-data-invstock').DataTable();
    //console.log(table);
    table
        .on('draw', function () {
            eventFired( 'Page' );
        });
    data = datosinvstock();
    $.ajax({
        url: '/reportinvstock/totalizarindex/' + data.data2,
        type: 'GET',
        success: function (datos) {
            $("#totalstock").html(MASKLA(datos.aux_totalstock,0));
            $("#totalkg").html(MASKLA(datos.aux_totalkg,2));
            //$("#totaldinero").html(MASKLA(datos.aux_totaldinero,0));
        }
    });
}

var eventFired = function ( type ) {
    subtotalstock = 0
    $("#tabla-data-invstock tr .subtotalstock").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		subtotalstock += valorNum;
	});
    $("#subtotalstock").html(MASKLA(subtotalstock,0))

	total = 0;
	$("#tabla-data-invstock tr .subtotalkg").each(function() {
		valor = $(this).attr('data-order') ;
		valorNum = parseFloat(valor);
		total += valorNum;
	});
    $("#subtotalkg").html(MASKLA(total,2))
}

function datosinvstock(){
    var data1 = {
        mesanno           : $("#annomes").val(),
        sucursal_id       : $("#sucursal_id").val(),
        invbodega_id      : $("#invbodega_id").val(),
        producto_id       : $("#producto_idPxP").val(),
        categoriaprod_id  : $("#categoriaprod_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        _token            : $('input[name=_token]').val()
    };

    var data2 = "?mesanno="+data1.mesanno +
    "&sucursal_id="+data1.sucursal_id +
    "&invbodega_id="+data1.invbodega_id +
    "&producto_id="+data1.producto_id +
    "&categoriaprod_id="+data1.categoriaprod_id +
    "&areaproduccion_id="+data1.areaproduccion_id


    var data = {
        data1 : data1,
        data2 : data2
    };
    return data;
}

$("#btnbuscarproducto").click(function(event){
    $(this).val("");
    $(".input-sm").val('');
    data = datosinvstock();
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
    data = datosinvstock();
    //alert(cadena);
    $('#contpdf').attr('src', '/reportinvstock/exportPdf/'+data.data2);
    //$('#contpdf').attr('src', '/notaventa/'+id+'/'+stareport+'/exportPdf');
	$("#myModalpdf").modal('show')
});

$("#sucursal_id").change(function(){
	id = $(this).val();
	llenarbodegas(id)
});

function llenarbodegas(sucursal_id){
    $("#invbodega_id").empty();
    for (let i = 0; i < arrayBodegas.length; i++) {
        if(sucursal_id == arrayBodegas[i].sucursal_id){
            $("#invbodega_id").append(`<option value="${arrayBodegas[i].id}" sucursal_id="${arrayBodegas[i].sucursal_id}">${arrayBodegas[i].nombre}</option>`)
        }
    }
    $(".selectpicker").selectpicker('refresh');
/*  //Llenar Bodegas con ajax Santa Ester, lo cambie por llenarlas en javascript
    //Me traigo las bodegas previamente de php
    tablascolsultainv($("#sucursal_id").val());
*/
}
//Esta funcion era para llenar en tiempo real el select de bodegas
///Ahora lo hago desde phph y en JS recorreo el arreglo de bodegas
function tablascolsultainv(id){
    $("#invbodega_id").empty();
    $("#categoriaprod_id").empty();
    if((id == "" || id == "0" || id == "x") == false){
        var data = {
            id: id,
            _token: $('input[name=_token]').val()
        };
        //console.log(data);
        
        $.ajax({
            url: '/sucursal/tablascolsultainv',
            type: 'POST',
            data: data,
            success: function (respuesta) {
                $.each(respuesta.invbodegas, function(index,value){
                    $("#invbodega_id").append("<option value='" + value.id + "'>" + value.nombre + "</option>")
                });
                $.each(respuesta.categoria, function(index,value){
                    $("#categoriaprod_id").append("<option value='" + value.id + "'>" + value.nombre + "</option>")
                });

                $(".selectpicker").selectpicker('refresh');
            }
        });    
    }else{
        $(".selectpicker").selectpicker('refresh');
    }
}

function exportarExcel() {
    data = datosinvstock();
    var newUrl = "invcontrolpage/" + data.data2;
    // Obtener todos los registros mediante una solicitud AJAX
    $.ajax({
        url: "invcontrolpage/" + data.data2, // ajusta la URL de la solicitud al endpoint correcto
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
        count = 0;

        cellLengthRazonSoc = 0;
        cellLengthProducto = 0;
        filainifusionar = -1
        //console.log(data);
        aux_mesanno = $("#annomes").val();
        datosExcel.push(["Stock Productos","","","","","","","","","","",fechaactual()]);
        datosExcel.push(["Fecha: " + aux_mesanno,"","","","","","","",""]);
        aux_totalStock = 0;
        aux_totalkg = 0;
        datosExcel.push(["","","","","","","","",""]);
        datosExcel.push(["Cod","Producto","Categoria","Clase/Sello","Diam/Ancho","L","Peso/Esp","TU","Bodega","Ini","Stock","Kg"]);
        data.data.forEach(function(registro) {
            aux_totalStock += registro.stock;
            if(registro.peso <= 0){
                stockKg = registro.stockkg;
            }else{
                stockKg = registro.stock * registro.peso;
            }
            aux_totalkg += stockKg;
            filainifusionar++;

            var filaExcel = [
                registro.producto_id,
                registro.producto_nombre,
                registro.categoria_nombre,
                registro.cla_nombre,
                registro.diametro,
                registro.largo,
                registro.peso,
                registro.tipounion,
                registro.invbodega_nombre,
                registro.stockini,
                registro.stock,
                stockKg
            ];
            count++;

            datosExcel.push(filaExcel);
        });
        datosExcel.push(["","","","","","","","","","Total: ",aux_totalStock,aux_totalkg]);

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
    for (let i = 1; i <= 12; i++) {
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
    for (let i = 1; i <= 12; i++) {
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

    // Establecer el formato de centrado horizontal y vertical para las celdas de la columna 8 desde la fila 4 hasta la fila 58
    for (let i = 4; i <= datosExcel.length; i++) {
        const cell8 = worksheet.getCell(i, 8);
        cell8.alignment = { horizontal: "center", vertical: "middle" };
        /* const cell9 = worksheet.getCell(i, 9);
        cell9.alignment = { horizontal: "center", vertical: "middle" }; */
        /* const cell10 = worksheet.getCell(i, 10);
        cell10.alignment = { horizontal: "center", vertical: "middle" }; */

        const cell = worksheet.getCell(i, 13);
        cell.alignment = { horizontal: "center", vertical: "middle" };

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


    //Fecha Reporte
    const row2 = worksheet.getRow(1);
    cell = row2.getCell(10);
    cell.alignment = { horizontal: "center", vertical: "middle" };


    //Fusionar celdas de Titulo
    const startCol = 0;
    const endCol = 11;
    worksheet.mergeCells(1, startCol, 1, endCol);

    //Negrita Columna Sucursal
    const row3 = worksheet.getRow(2);
    cell = row3.getCell(1);
    cell.alignment = { horizontal: "center", vertical: "middle" };
    
    //Fusionar celdas Sucursal
    const startCol1 = 0;
    const endCol1 = 11;
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
      a.download = "invstock.xlsx";
      a.click();

      // Limpiar el objeto Blob
      window.URL.revokeObjectURL(url);
    });
}