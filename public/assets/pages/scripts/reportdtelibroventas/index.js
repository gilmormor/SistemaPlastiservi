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
        'ajax'        : "/reportdtelibroventas/reportdtelibroventaspage/" + aux_data.data2, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
        'columns'     : [
            {data: 'id'}, // 0
            {data: 'fechahora'}, // 1
            {data: 'rut'}, // 2
            {data: 'razonsocial'}, // 3
            {data: 'cotizacion_id'}, // 4
            {data: 'oc_id'}, // 5
            {data: 'notaventa_id'}, // 6
            {data: 'despachosol_id'}, // 7
            {data: 'despachoord_id'}, // 8
            {data: 'nrodocto_origen'}, // 9
            {data: 'nrodocto'}, // 10
            {data: 'foliocontrol_doc'}, // 11
            {data: 'mnttotal'}, // 12
            {data: 'dteanul_obs',className:"ocultar"}, //13
            {data: 'dteanulcreated_at',className:"ocultar"}, //14
            {data: 'clientebloqueado_descripcion',className:"ocultar"}, //15
            {data: 'oc_file',className:"ocultar"}, //16
            {data: 'nombrepdf',className:"ocultar"}, //17
            {data: 'staverfacdesp',className:"ocultar"}, //18
            {data: 'updated_at',className:"ocultar"}, //19
            {data: 'dtefac_updated_at',className:"ocultar"}, //20
            {data: 'foliocontrol_desc',className:"ocultar"}, //21
            {data: 'pdftipodte_origen',className:"ocultar"}, //22
            {data: 'foliocontroldesc_origen',className:"ocultar"}, //23
            {defaultContent : ""}
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
            /*
            aux_text = 
            "<a class='btn-accion-tabla btn-sm tooltipsC' onclick='generarFactSii(" + data.id + ")' title='Generar DTE Factura SII'>"+
                + data.id + 
            "</a>";
            $('td', row).eq(0).html(aux_text);
            */
            $('td', row).eq(0).attr('data-order',data.id);


            $('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));

            if(data.cotizacion_id != null){
                let arr_cotizacion_id = data.cotizacion_id.split(','); 
                aux_text = "";
                for (let i = 0; i < arr_cotizacion_id.length; i++) {
                    aux_text += 
                    "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Cotizacion' onclick='genpdfCOT(" + arr_cotizacion_id[i] + ",1)'>" +
                        arr_cotizacion_id[i] +
                    "</a>";
                }    
            }else{
                aux_text = "";
            }
            $('td', row).eq(4).html(aux_text);

            aux_text = "";
            if(data.oc_file != "" && data.oc_file != null){
                let arr_oc_id = data.oc_id.split(','); 
                let arr_oc_file = data.oc_file.split(','); 
                for (let i = 0; i < arr_oc_file.length; i++) {
                    aux_text += 
                    "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Compra' onclick='verpdf2(\"" + arr_oc_file[i] + "\",2)'>" + 
                        arr_oc_id[i] + 
                    "</a>";
                    if((i+1) < arr_oc_file.length){
                        aux_text += ",";
                    }
                }
                $('td', row).eq(5).html(aux_text);
            }
            aux_text = "";
            if(data.notaventa_id != null){
                let arr_notaventa_id = data.notaventa_id.split(','); 
                for (let i = 0; i < arr_notaventa_id.length; i++){
                    aux_text += 
                    "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV(" + arr_notaventa_id[i] + ",1)'>" +
                        arr_notaventa_id[i] +
                    "</a>";
                    if((i+1) < arr_notaventa_id.length){
                        aux_text += ",";
                    }
                }    
            }
            $('td', row).eq(6).html(aux_text);

            aux_text = "";
            if(data.despachosol_id != null){
                let arr_despachosol_id = data.despachosol_id.split(','); 
                for (let i = 0; i < arr_despachosol_id.length; i++){
                    aux_text += 
                    "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Solicitud Despacho' onclick='genpdfSD(" + arr_despachosol_id[i] + ",1)'>" +
                        arr_despachosol_id[i] +
                    "</a>";
                    if((i+1) < arr_despachosol_id.length){
                        aux_text += ",";
                    }
                }    
            }
            $('td', row).eq(7).html(aux_text);

            aux_text = "";
            if(data.despachosol_id != null){
                let arr_despachoord_id = data.despachoord_id.split(','); 
                for (let i = 0; i < arr_despachoord_id.length; i++){
                    aux_text += 
                    "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Orden Despacho' onclick='genpdfOD(" + arr_despachoord_id[i] + ",1)'>" +
                        arr_despachoord_id[i] +
                    "</a>";
                    if((i+1) < arr_despachoord_id.length){
                        aux_text += ",";
                    }
                }    
            }
            $('td', row).eq(8).html(aux_text);


            aux_text = "";
            if(data.nrodocto_origen != null){
                let arr_nrodocto_origen = data.nrodocto_origen.split(',');
                let arr_pdftipodte_origen = data.pdftipodte_origen.split(',');
                let arr_foliocontroldesc_origen = data.foliocontroldesc_origen.split(',');
                for (let i = 0; i < arr_nrodocto_origen.length; i++){
                    let id_str = arr_nrodocto_origen[i].toString();
                    id_str = arr_pdftipodte_origen[i] + id_str.padStart(8, "0");
                    
                    aux_text += 
                    `<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='${arr_foliocontroldesc_origen[i]}' onclick="genpdfFAC('${id_str}','')">
                        ${arr_nrodocto_origen[i]}
                    </a>
                    <a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='${arr_foliocontroldesc_origen[i]} Cedible:${arr_nrodocto_origen[i]}' onclick="genpdfFAC('${id_str}','_cedible')">
                        <i class="fa fa-fw fa-file-pdf-o"></i>
                    </a>`;
                    if((i+1) < arr_nrodocto_origen.length){
                        aux_text += ",";
                    }
                }    
            }
            $('td', row).eq(9).html(aux_text);

            $('td', row).eq(10).html(aux_text);
            aux_text = "";
            if(data.nrodocto != null){
                let id_str = data.nrodocto.toString();
                id_str = data.nombrepdf + id_str.padStart(8, "0");
                if(data.nrodocto != null){
                    aux_text = 
                    `<a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="${data.foliocontrol_desc}" onclick="genpdfFAC('${id_str}','')">
                        ${data.nrodocto}
                    </a>
                    <a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="${data.foliocontrol_desc} Cedible:${data.nrodocto}" onclick="genpdfFAC('${id_str}','_cedible')">
                        <i class="fa fa-fw fa-file-pdf-o"></i>
                    </a>`;
                }    
            }
            $('td', row).eq(10).html(aux_text);

            $('td', row).eq(12).attr('style','text-align:right');
            $('td', row).eq(12).attr('data-order',data.mnttotal);
            $('td', row).eq(12).attr('data-search',data.mnttotal);
            $('td', row).eq(12).html(MASKLA(data.mnttotal,0));
            $('td', row).eq(12).addClass('subtotalmonto');

            
            $('td', row).eq(19).addClass('updated_at');
            $('td', row).eq(19).attr('id','updated_at' + data.id);
            $('td', row).eq(19).attr('name','updated_at' + data.id);

            $('td', row).eq(20).addClass('dtefac_updated_at');
            $('td', row).eq(20).attr('id','dtefac_updated_at' + data.id);
            $('td', row).eq(20).attr('name','dtefac_updated_at' + data.id);
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
        url: '/reportdtelibroventas/totalizarindex/' + data.data2,
        type: 'GET',
        success: function (datos) {
            $("#totalmonto").html(MASKLA(datos.aux_total,0));
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
}

function datosFac(orderby = "",aux_genexcel){
    var data1 = {
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        sucursal_id       : $("#sucursal_id").val(),
        rut               : eliminarFormatoRutret($("#rut").val()),
        filtro            : 1,
        statusgen         : 1,
        foliocontrol_id   : "",
        orderby           : "",
        groupby           : "",
        genexcel          : aux_genexcel,
        _token            : $('input[name=_token]').val()
    };

    var data2 = "?fechad="+data1.fechad +
    "&fechah="+data1.fechah +
    "&sucursal_id="+data1.sucursal_id +
    "&rut="+data1.rut +
    "&filtro="+data1.filtro +
    "&statusgen="+data1.statusgen +
    "&foliocontrol_id="+data1.foliocontrol_id +
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
    $('#contpdf').attr('src', '/reportdtelibroventas/exportPdf/'+data.data2);
    $("#myModalpdf").modal('show');
}

function exportarExcel() {
    var tabla = $('#tabla-data-consulta').DataTable();
    orderby = " order by dte.sucursal_id asc,foliocontrol.doc,dte.id ";
    data = datosFac(orderby,1);
    // Obtener todos los registros mediante una solicitud AJAX
    $.ajax({
      url: "/reportdtelibroventas/reportdtelibroventaspage/" + data.data2, // ajusta la URL de la solicitud al endpoint correcto
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
        aux_sucursal_id = "";
		count = 0;

        cellLengthRazonSoc = 0;
        cellLengthProducto = 0;
        filainifusionar = -1
        arrayfusionarCelNomVend = [];
        //console.log(data);
        aux_sucursalNombre = $("#sucursal_id option:selected").html();
        aux_rangofecha = $("#fechad").val() + " al " + $("#fechah").val()
        datosExcel.push(["Libro de Ventas","","","","","","","","","","",data.fechaact]);
        datosExcel.push(["Centro Economico: " + aux_sucursalNombre + " Entre: " + aux_rangofecha,"","","","","","","","","","",""]);
        aux_totalNeto = 0;
        aux_totalIva = 0;
        aux_total = 0;
        datosExcel.push(["","","","","","","","","","","",""]);
        datosExcel.push(["Suc","Doc","Fecha","Numero","FechaVenc","RUT","Razon Social","Vendedor","FormaPago","Neto","IVA","Total"]);
        data.datos.forEach(function(registro) {
            console.log(registro);
            aux_totalNeto += registro.mntneto;
            aux_totalIva += registro.iva;
            aux_total += registro.mnttotal_a;
            filainifusionar++;
            aux_length = registro.razonsocial.toString().length
            if(aux_length > cellLengthRazonSoc){
                cellLengthRazonSoc = aux_length;
            }
            aux_length = registro.mntneto.toString().length
            if(aux_length > cellLengthProducto){
                cellLengthProducto = aux_length;
            }
            
            aux_fecha = new Date(registro.fchemis + " 01:00:00");
            aux_fechavenc = new Date(registro.fchvenc + " 01:00:00");
            var filaExcel = [
                registro.sucursal_nombre,
                registro.foliocontrol_doc,
                fechaddmmaaaa(aux_fecha),
                registro.nrodocto,
                fechaddmmaaaa(aux_fechavenc),
                registro.rut,
                registro.razonsocial,
                registro.vendedor_nombre,
                registro.formapago_descripcion,
                registro.mntneto,
                registro.iva,
                registro.mnttotal_a
            ];
            aux_sucursal_id = registro.sucursal_id;
            count++;

            datosExcel.push(filaExcel);
        });
        if(aux_totalNeto > 0){
            datosExcel.push(["","","","","","","","","Total: ",aux_totalNeto,aux_totalIva,aux_total]);
        }

        createExcel(datosExcel);

      },
      error: function(xhr, status, error) {
        console.log(error);
      }
    });
}

// Función para crear el archivo Excel
function createExcel(datosExcel) {
    aux_filas = datosExcel.length;
    // Crear un nuevo libro de trabajo y una nueva hoja
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Datos");

    // Insertar los datos en la hoja de trabajo
    worksheet.addRows(datosExcel);
    // Establecer negrita en la celda A1
    //worksheet.getCell("A5").font = { bold: true };

    //Negrita Columna Titulo
    const row1 = worksheet.getRow(1);
    cell = row1.getCell(1);
    cell.font = { bold: true, size: 20 };
    cell.alignment = { horizontal: "center", vertical: "middle" };


    // Ajustar automáticamente el ancho de la columna B al contenido
    ajustarcolumnaexcel(worksheet,"C");
    ajustarcolumnaexcel(worksheet,"D");
    ajustarcolumnaexcel(worksheet,"E");
    ajustarcolumnaexcel(worksheet,"F");
    ajustarcolumnaexcel(worksheet,"G");
    ajustarcolumnaexcel(worksheet,"H");
    ajustarcolumnaexcel(worksheet,"I");
    ajustarcolumnaexcel(worksheet,"J");
    ajustarcolumnaexcel(worksheet,"K");
    ajustarcolumnaexcel(worksheet,"L");
    
    // Combinar celdas desde [4,0] hasta [4,2]

    // Recorrer la columna 7 y dar formato con punto para separar los miles
    const columnG = worksheet.getColumn(10);
    columnG.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });

    // Recorrer la columna 8 y dar formato con punto para separar los miles
    const columnH = worksheet.getColumn(11);
    columnH.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });

    // Recorrer la columna 9 y dar formato con punto para separar los miles
    const columnI = worksheet.getColumn(12);
    columnI.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });
    /*

    // Establecer el formato de centrado horizontal y vertical para las celdas de la columna 8 desde la fila 4 hasta la fila 58
    for (let i = 4; i <= datosExcel.length; i++) {
    const cell = worksheet.getCell(i, 9);
    cell.alignment = { horizontal: "center", vertical: "middle" };
    }





    
*/

    //Establecer negrita a titulos
    const row6 = worksheet.getRow(4);
    for (let i = 1; i <= 12; i++) {
        cell = row6.getCell(i);
        cell.font = { bold: true };
    }

    // Establecer negrita a totales
    row = worksheet.getRow(aux_filas);
    for (let i = 9; i <= 12; i++) {
        cell = row.getCell(i);
        cell.font = { bold: true };
        cell.alignment = { horizontal: "right" };
        cell.numFmt = "#,##0";
    }
    

    //Fusionar celdas de Titulo
    const startCol = 0;
    const endCol = 11;
    worksheet.mergeCells(1, startCol, 1, endCol);
    //Fusionar celdas Sucursal
    const startCol1 = 0;
    const endCol1 = 11;
    worksheet.mergeCells(2, startCol1, 2, endCol1);
    //Negrita Columna Sucursal
    const row3 = worksheet.getRow(2);
    cell = row3.getCell(1);
    cell.alignment = { horizontal: "center", vertical: "middle" };

    //Fecha Reporte
    const row2 = worksheet.getRow(1);
    cell = row2.getCell(12);
    cell.alignment = { horizontal: "center", vertical: "middle" };
    

    // Guardar el archivo
    workbook.xlsx.writeBuffer().then(function(buffer) {
      // Crear un objeto Blob para el archivo Excel
      const blob = new Blob([buffer], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });

      // Crear un enlace de descarga
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = "libroVentas.xlsx";
      a.click();

      // Limpiar el objeto Blob
      window.URL.revokeObjectURL(url);
    });
}

function ajustarcolumnaexcel(worksheet,columna){
    const columnB = worksheet.getColumn(columna);
    let maxLengthB = 0;
    columnB.eachCell({ includeEmpty: true }, (cell) => {
      const length = cell.value ? cell.value.toString().length : 0;
      if (length > maxLengthB) {
        maxLengthB = length;
      }
    });
    columnB.width = maxLengthB < 10 ? 10 : maxLengthB;

}