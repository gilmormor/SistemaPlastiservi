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
        consultarpage(datosFac("",0));
    });
    $("#btnpdf2").click(function()
    {
        btnpdf(datosFac("",0));
    });
    consultarpage(datosFac("",0));
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
        'ajax'        : "/reportdtecomisionxvend/reportdtecomisionxvendpage/" + aux_data.data2, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
        'columns'     : [
            {data: 'id'},     // 0
            {data: 'nrodocto'}, // 1
            {data: 'foliocontrol_doc'}, // 2
            {data: 'fechahora'}, // 3
            {data: 'rut'}, // 4
            {data: 'razonsocial'}, // 5
            {data: 'producto_id'}, // 6
            {data: 'nmbitem'}, // 7
            {data: 'montoitem'}, // 8
            {data: 'porc_comision'}, // 9
            {data: 'comision'}, // 10
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
            $('td', row).eq(0).attr('data-order',data.id);

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
            $('td', row).eq(1).html(aux_text);

            $('td', row).eq(3).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(3).html(fechaddmmaaaa(aux_fecha));

            $('td', row).eq(6).attr('style','text-align:center');

            aux_diametro = data.diametro == "0" || data.diametro == "" ? "" : " D:" + data.diametro;
            aux_cla_nombre = data.cla_nombre == "0" || data.cla_nombre == "" ? "" : " C:" + data.cla_nombre;
            aux_long = data.long == "0" || data.long == "" ? "" : " L:" + data.long;
            aux_tipounion = data.tipounion == "0" || data.tipounion == "" ? "" : " TU:" + data.tipounion;
            aux_nombreprod = data.nmbitem; // + aux_diametro + aux_cla_nombre + aux_long + aux_tipounion;
            $('td', row).eq(7).attr('style','text-align:left');
            $('td', row).eq(7).attr('data-search',aux_nombreprod);
            $('td', row).eq(7).attr('data-order',aux_nombreprod);
            $('td', row).eq(7).html(aux_nombreprod);


            $('td', row).eq(8).attr('style','text-align:right');
            $('td', row).eq(8).attr('data-order',data.montoitem);
            $('td', row).eq(8).attr('data-search',data.montoitem);
            $('td', row).eq(8).html(MASKLA(data.montoitem,0));
            $('td', row).eq(8).addClass('subtotalmonto');
            
            $('td', row).eq(9).attr('style','text-align:center');
            $('td', row).eq(9).attr('data-order',data.porc_comision);
            $('td', row).eq(9).attr('data-search',data.porc_comision);
            $('td', row).eq(9).html(MASKLA(data.porc_comision,1));

            $('td', row).eq(10).attr('style','text-align:right');
            $('td', row).eq(10).attr('data-order',data.comision);
            $('td', row).eq(10).attr('data-search',data.comision);
            $('td', row).eq(10).html(MASKLA(data.comision,0));
            $('td', row).eq(10).addClass('subtotalmontocomision');

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
    data = datosFac("",0);
    $.ajax({
        url: '/reportdtecomisionxvend/totalizarindex/' + data.data2,
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

function datosFac(orderby = "",aux_genexcel){
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
    /*
    $.ajax({
        type: "GET",
        url: "/reportdtecomisionxvend/exportPdf/"+data.data2, // Ajusta la URL según tu estructura
        success: function (data1) {
            // Abre una nueva ventana del navegador para mostrar el PDF
            var blob = new Blob([data], { type: 'application/pdf' });
            var pdfUrl = URL.createObjectURL(blob);
            window.open(pdfUrl, "_blank");
        },
        error: function () {
            alert("Se produjo un error al generar el PDF.");
        }
    });
    */

/*
    swal({
        title: 'Ver!',
        text: "",
        icon: 'success',
        buttons: {
            si: {
                text: "Descargar",
                value: "1",
            },

            no: {
                text: "Pantalla",
                value: "2",
            },
            cancel: "Cancelar"
        },
    }).then((value) => {
        console.log(value);
        if(value){
            switch (value) {

                case "1":
                    data.data2 += "&verpdf=" + value;
                    aux_modal = 'hide';
                    break;
             
                case "2":
                    data.data2 += "&verpdf=" + value;
                    aux_modal = 'show';
                    break;
                default:
                  //swal("Got away safely!");
            }
            $('#contpdf').attr('src', '/reportdtecomisionxvend/exportPdf/'+data.data2);
            $("#myModalpdf").modal(aux_modal);    
        }
    
    });

*/
    $('#contpdf').attr('src', '/reportdtecomisionxvend/exportPdf/'+data.data2);
    $("#myModalpdf").modal('show');    



}

function exportarExcel() {
    var tabla = $('#tabla-data-consulta').DataTable();
    orderby = " order by dte.vendedor_id asc,foliocontrol.doc,dte.id ";
    data = datosFac(orderby,1);
    // Obtener todos los registros mediante una solicitud AJAX
    $.ajax({
      url: "/reportdtecomisionxvend/reportdtecomisionxvendpage/" + data.data2, // ajusta la URL de la solicitud al endpoint correcto
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
        arrayfusionarCelNomVend = [];
        //console.log(data);
        aux_sucursalNombre = $("#sucursal_id option:selected").html();
        aux_rangofecha = $("#fechad").val() + " al " + $("#fechah").val()
        datosExcel.push(["Informe Comisión Vendedores","","","","","","","","",data.fechaact]);
        datosExcel.push(["Centro Economico: " + aux_sucursalNombre + " Entre: " + aux_rangofecha,"","","","","","","","",""]);
        aux_totalMonto = 0;
        aux_totalComision = 0;
        data.datos.forEach(function(registro) {
            if (registro.vendedor_id != aux_vendedor_id){
                filainifusionar += 4;
                if(aux_vendedor_id == ""){
                    datosExcel.push(["","","","","","","","","",""]);
                }else{
                    datosExcel.push(["","","","","","","Total: ",aux_totalMonto,"",aux_totalComision]);
                }
                aux_totalMonto = 0;
                aux_totalComision = 0;        
                datosExcel.push(["","","","","","","","","",""]);
                datosExcel.push(["Vendedor: " + registro.vendedor_rut + " - " + registro.vendedor_nombre,"","","","","","","","",""]);
                datosExcel.push(["Tipo Doc","NDoc","Fecha","Cliente","RUT","CodProd","Producto","Neto","Comisión %","Comisión $"]);
                arrayfusionarCelNomVend.push(filainifusionar);
            }
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
            
            aux_fecha = new Date(registro.fechahora);
            var filaExcel = [
                registro.foliocontrol_doc,
                registro.nrodocto,
                fechaddmmaaaa(aux_fecha),
                registro.razonsocial,
                registro.rut,
                registro.producto_id,
                registro.nmbitem,
                registro.montoitem,
                registro.porc_comision,
                registro.comision
            ];
            aux_vendedor_id = registro.vendedor_id;
            count++;

            datosExcel.push(filaExcel);
        });
        if(aux_totalMonto > 0){
            datosExcel.push(["","","","","","","Total: ",aux_totalMonto,"",aux_totalComision]);
        }

/*
        // Crear el libro de Excel
        var libro = XLSX.utils.book_new();
        var hoja = XLSX.utils.aoa_to_sheet(datosExcel);
        XLSX.utils.book_append_sheet(libro, hoja, 'Datos');

        // Ajustar automáticamente el ancho de la columna A (columna con índice 0) al contenido:
        if (!hoja['!cols']) hoja['!cols'] = [];
        hoja['!cols'][3] = { width: cellLengthRazonSoc + 2};        
        if (!hoja['!cols']) hoja['!cols'] = [];
        hoja['!cols'][5] = { width: cellLengthProducto + 2};


        arrayfusionarCelNomVend.forEach(function(fila) {
            //console.log(celda);
            const mergeRange = { s: { r: fila, c: 0 }, e: { r: fila, c: 3 } };
            if (!hoja['!merges']) hoja['!merges'] = [];
            hoja['!merges'].push(mergeRange);
        });

        // Generar el archivo Excel y descargarlo
        XLSX.writeFile(libro, 'comisonxVend.xlsx');
*/
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
    ajustarcolumnaexcel(worksheet,"C");
    ajustarcolumnaexcel(worksheet,"D");
    ajustarcolumnaexcel(worksheet,"E");
    ajustarcolumnaexcel(worksheet,"F");
    ajustarcolumnaexcel(worksheet,"G");
    ajustarcolumnaexcel(worksheet,"H");
    ajustarcolumnaexcel(worksheet,"I");
    

    // Combinar celdas desde [4,0] hasta [4,2]
    arrayfusionarCelNomVend.forEach(function(fila) {
        
        // Establecer negrita a totales
        row = worksheet.getRow(fila + 2 -2);
        for (let i = 1; i <= 10; i++) {
            cell = row.getCell(i);
            cell.font = { bold: true };
            cell.alignment = { horizontal: "right" };
            cell.numFmt = "#,##0";
        }

        row = worksheet.getRow(datosExcel.length);
        for (let i = 1; i <= 10; i++) {
            cell = row.getCell(i);
            cell.font = { bold: true };
            cell.alignment = { horizontal: "right" };
            cell.numFmt = "#,##0";
        }

        // Establecer negrita en la celda de Vendedor
        const row5 = worksheet.getRow(fila + 2);
        const cellA5 = row5.getCell(1);
        cellA5.font = { bold: true };

        //Establecer negrita a todos los titulos debajo de vendedor
        const row6 = worksheet.getRow(fila + 3);
        for (let i = 1; i <= 10; i++) {
            cell = row6.getCell(i);
            cell.font = { bold: true };
        }

        //Fusionar celdas de vendedor
        const startCol = 0;
        const endCol = 4;
        worksheet.mergeCells(fila + 2, startCol, fila + 2, endCol);
        
        // Establecer el formato de negrita en la celda superior izquierda del rango fusionado
    });

    // Recorrer la columna 7 y dar formato con punto para separar los miles
    const columnG = worksheet.getColumn(8);
    columnG.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });

    // Recorrer la columna 8 y dar formato con punto para separar los miles
    const columnH = worksheet.getColumn(9);
    columnH.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });

    // Recorrer la columna 9 y dar formato con punto para separar los miles
    const columnI = worksheet.getColumn(10);
    columnI.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });
    
    // Establecer el formato de centrado horizontal y vertical para las celdas de la columna 8 desde la fila 4 hasta la fila 58
    for (let i = 4; i <= datosExcel.length; i++) {
    const cell = worksheet.getCell(i, 9);
    cell.alignment = { horizontal: "center", vertical: "middle" };
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
    cell = row2.getCell(9);
    cell.alignment = { horizontal: "center", vertical: "middle" };


    //Fusionar celdas de Titulo
    const startCol = 0;
    const endCol = 9;
    worksheet.mergeCells(1, startCol, 1, endCol);

    //Negrita Columna Sucursal
    const row3 = worksheet.getRow(2);
    cell = row3.getCell(1);
    cell.alignment = { horizontal: "center", vertical: "middle" };
    
    //Fusionar celdas Sucursal
    const startCol1 = 0;
    const endCol1 = 9;
    worksheet.mergeCells(2, startCol1, 2, endCol1);

    aux_nombrearchivo = "comisionVendedores.xlsx";
    //SI ES UN SOLO VENDEDOR TOMO EL NOMBRE DEL VENDEDOR Y LO ASIGNO AL NOMBR DEL ARCHIVO
    // Selecciona el elemento <select> por su ID
    var selectElement = $("#vendedor_id");
    // Verifica cuántos elementos están seleccionados
    var selectedOptions = selectElement.find("option:selected");
    // Si solo un elemento está seleccionado
    if (selectedOptions.length === 1) {
    // Realiza alguna acción o muestra un mensaje
        var aux_nombrearchivo = selectElement.find("option:selected").text();
        aux_nombrearchivo = aux_nombrearchivo + ".xlsx"
        //alert("Solo un elemento está seleccionado.");
    }

    // Guardar el archivo
    workbook.xlsx.writeBuffer().then(function(buffer) {
      // Crear un objeto Blob para el archivo Excel
      const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });

      // Crear un enlace de descarga
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = aux_nombrearchivo;
      a.click();

      // Limpiar el objeto Blob
      window.URL.revokeObjectURL(url);
    });
}