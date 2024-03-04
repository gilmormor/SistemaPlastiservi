$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    configurarTabla1('#tabla-data-consulta');

    function configurarTabla1(aux_tabla){
        //datax = datosPentxProd(1);
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
            'ajax'        : "/reportcategoriaprod_giro/reportcategoriaprod_giropage/",
            'columns'     : [
                {data: 'nombre'}, // 0
                {data: 'distribuidor'}, // 1
                {data: 'comercializadora'}, // 2
                {data: 'clientefinal'}, // 3
                {data: 'meson'}, // 4
                {data: 'meson'}, // 5
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "createdRow": function ( row, data, index ) {
                $('td', row).eq(1).attr('style','text-align:right');
                $('td', row).eq(1).attr('data-order',data.distribuidor);
                $('td', row).eq(1).html(MASKLA(data.distribuidor,2));
                $('td', row).eq(2).attr('style','text-align:right');
                $('td', row).eq(2).attr('data-order',data.comercializadora);
                $('td', row).eq(2).html(MASKLA(data.comercializadora,2));
                $('td', row).eq(3).attr('style','text-align:right');
                $('td', row).eq(3).attr('data-order',data.clientefinal);
                $('td', row).eq(3).html(MASKLA(data.clientefinal,2));
                $('td', row).eq(4).attr('style','text-align:right');
                $('td', row).eq(4).attr('data-order',data.meson);
                $('td', row).eq(4).html(MASKLA(data.meson,2));

                promedio = (data.distribuidor + data.comercializadora + data.clientefinal + data.meson) / 4
                $('td', row).eq(5).attr('style','text-align:right');
                $('td', row).eq(5).attr('data-order',promedio);
                $('td', row).eq(5).html(MASKLA(promedio,2));
            }
        });
    }
});

function ejecutarConsulta(aux_cod){
    if(aux_cod == 2){
        consultarJS();
    }
    if(aux_cod == 3){
        exportarExcel();    
    }
}

function consultarJS(){
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
        url: '/reportcategoriaprod_giro/reportcategoriaprod_giropage',
        type: 'GET',
        success: function (datos) {
            //console.log(datos.data[0].datosAdicionales);
            //return 0;
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
                pdf.save('PrecioxCategoriaGiro.pdf');    
            }
        }
    });
}

function headRows() {
    return [
      { 
        nombre: "Categoria", 
        dist: "Distribuidor", 
        comer: "Comercializadora", 
        clienfinal: "Cliente Final", 
        meson: "Meson", 
        promedio: "Promedio"}
    ]
}

function bodyRows(datos) {
    var body = []
    aux_contreg = 0;
    datos.data.forEach(function(registro) {
        aux_promedio = (registro.distribuidor + registro.comercializadora + registro.clientefinal + registro.meson) /4;
        body.push({
            nombre: registro.nombre,
            dist: MASKLA(registro.distribuidor,2),
            comer: MASKLA(registro.comercializadora,2),
            clienfinal: MASKLA(registro.clientefinal,2),
            meson: MASKLA(registro.meson,2),
            promedio: MASKLA(aux_promedio,2)
        })
        aux_contreg++;
    });
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
 
    var doc = new jsPDF('p')
    var totalPagesExp = '{total_pages_count_string}'
  
    doc.autoTable({
      startY: 25,
      head: headRows(),
      body: bodyRows(datos),
      theme: 'grid',
      styles: {
        fontSize: 10, // Tamaño de letra para los encabezados
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
        0: { cellWidth: 60 },  // Ancho columna
        1: { cellWidth: 25, halign: 'right' },  // Ancho columna
        2: { cellWidth: 25, halign: 'right' },  // Ancho columna
        3: { cellWidth: 25, halign: 'right' },  // Ancho columna
        4: { cellWidth: 25, halign: 'right' },  // Ancho columna
        5: { cellWidth: 25, halign: 'right' },  // Ancho columna

        // ... especifica el ancho de las demás columnas
      },
      willDrawPage: function (data) {
        // Header
        doc.setFontSize(12)
        doc.setTextColor(20)
        if (base64Img) {
          doc.addImage(base64Img, 'JPEG', data.settings.margin.left, 6, 30, 10)
        }
        doc.text('Precios por Categoria y Giro', data.settings.margin.left + 70, 12);
        doc.setFontSize(8)
        //doc.text('Sucursal: ' + $("#sucursal_id option:selected").html(), data.settings.margin.left + 120, 16);
        doc.text('Fecha: ' + datos.data[0].datosAdicionales.fecha, data.settings.margin.left + 160, 11);
        doc.text('Hora: ' + datos.data[0].datosAdicionales.hora, data.settings.margin.left + 160, 14);
/*        doc.text('Area Producción: ' + $("#areaproduccion_id option:selected").html(), data.settings.margin.left + 220, 8);
        doc.text('Vendedor: ' + $("#vendedor_id option:selected").html(), data.settings.margin.left + 220, 11);
        doc.text('Vendedor: ' + descripElementoSelectMult("vendedor_id"), data.settings.margin.left + 220, 11);
        doc.text('Giro: ' + $("#giro_id option:selected").html() + ' Estatus: ' + $("#aprobstatus option:selected").html(), data.settings.margin.left + 220, 14);
        doc.text('Nota Venta Desde: ' + $("#fechad").val() + " al " + $("#fechah").val(), data.settings.margin.left + 220, 17);
        doc.text('Plazo de Entrega: ' + $("#plazoentregad").val() + " al " + $("#plazoentregah").val(), data.settings.margin.left + 220, 20);
 */
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
        url: "/reportcategoriaprod_giro/reportcategoriaprod_giropage/" + data.data2, // ajusta la URL de la solicitud al endpoint correcto
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
        if(aux_totalMonto != 0){
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