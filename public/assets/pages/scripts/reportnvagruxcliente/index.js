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

    configurarTabla('#tabla-data-consulta');

    function configurarTabla(aux_tabla){
        datax = datosPentxProd(1);
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
            'ajax'        : "/reportnvagruxcliente/reportnvagruxclientepage/" + datax.data2, //$("#annomes").val() + "/sucursal/" + $("#sucursal_id").val(),
            'columns'     : [
                {data: 'cliente_id'}, // 0
                {data: 'rut'}, // 1
                {data: 'razonsocial'}, // 2
                {data: 'comunanombre'}, // 3
                {data: 'pvckg'}, //4
                {data: 'cankg'}, //5
                {data: 'total'}, //6
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            },
            "createdRow": function ( row, data, index ) {
                $(row).attr('id','fila' + data.id);
                $(row).attr('name','fila' + data.id);

                style='text-align:right' 
                $('td', row).eq(4).attr('style','text-align:right');
                $('td', row).eq(4).attr('data-order',data.pvckg);
                $('td', row).eq(4).addClass('pvckg');
                $('td', row).eq(4).html(MASKLA(data.pvckg,2));
                $('td', row).eq(5).attr('style','text-align:right');
                $('td', row).eq(5).attr('data-order',data.cankg);
                $('td', row).eq(5).addClass('cankg');
                $('td', row).eq(5).html(MASKLA(data.cankg,2));
                $('td', row).eq(6).attr('style','text-align:right');
                $('td', row).eq(6).attr('data-order',data.total);
                $('td', row).eq(6).addClass('total');
                $('td', row).eq(6).html(MASKLA(data.total,0));
            }
        });
        totalizar(datax);
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
            $('#tabla-data-consulta').DataTable().ajax.url( "/reportnvagruxcliente/reportnvagruxclientepage/" + data.data2 ).load();
            totalizar(data)
        }
        if(aux_cod == 2){
            consultarJS(data);    
        }
        if(aux_cod == 3){
            exportarExcel();    
        }
    }

}

function totalizar(data){
    $.ajax({
        url: '/reportnvagruxcliente/totalizarRep/' + data.data2,
        type: 'GET',
        success: function (datos) {
            //console.log(datos);
            $("#totalkgpvc").html(MASKLA(datos.aux_totalkgpvc,2));
            $("#totalkg").html(MASKLA(datos.aux_totalkg,2));
            $("#totaldinero").html(MASKLA(datos.aux_totaldinero,0));
        }
    });

}

var eventFired = function ( type ) {
    total = 0;
    $("#tabla-data-consulta tr .pvckg").each(function() {
        valor = $(this).attr('data-order') ;
        valorNum = parseFloat(valor);
        total += valorNum;
    });
    $("#subkgpvc").html(MASKLA(total,2))
    total = 0;
    $("#tabla-data-consulta tr .cankg").each(function() {
        valor = $(this).attr('data-order') ;
        valorNum = parseFloat(valor);
        total += valorNum;
    });
    $("#subkg").html(MASKLA(total,2))
    total = 0;
    $("#tabla-data-consulta tr .total").each(function() {
        valor = $(this).attr('data-order') ;
        valorNum = parseFloat(valor);
        total += valorNum;
    });
    $("#subdinero").html(MASKLA(total,0))
}

$('#tabla-data-consulta').on('draw.dt', function () {
    // Aquí puedes ejecutar la función que deseas que se ejecute cuando se termine de llenar la tabla
    // Llamar a tu función aquí
    eventFired( 'Page' );
});


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
        url: '/reportnvagruxcliente/reportnvagruxclientepage',
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
                pdf.save('NVAgruxCliente.pdf');    
            }
        }
    });
}

function headRows() {
    return [
      //{ id: "NV", cliente_rut: "OC", fecha: "Fecha", plazoentrega: "PlazoEnt", razonsocial: "Razón Social", comuna: "Comuna", cod: "Cod", desc: "Descripción", clase: "ClaSello", diam: "Diam Anch", l: "L", pesoesp: "Peso Esp", tu: "TU", stock: "Stock", picking: "Pick", cant: "Cant", cantdesp: "Cant Desp", cantpend: "Cant Pend", kilos: "Kilos Pend", preciokg: "Precio Kg", pesos: "$"},
      { id: "ID", cliente_rut: "RUT", razonsocial: "Razón Social", comuna: "Comuna", pvckg: "Kg PVC", cankg: "Kg", total: "Total"},
    ]
}

function bodyRows(datos) {
    var body = []
    aux_contreg = 0;
    pvckg = 0;
    cankg = 0;
    total = 0;
    datos.data.forEach(function(registro) {
        /* aux_fecha = new Date(registro.fechahora);
        aux_plazoentrega = new Date(registro.plazoentrega + " 00:00:00")
        aux_productonomb = registro.nombre.replace(/&quot;/g, '"');
        aux_productonomb = aux_productonomb.replace(/&#039;/g, "'"); */
        body.push({
            id: registro.cliente_id,
            cliente_rut: registro.rut,
            razonsocial: registro.razonsocial,
            comuna: registro.comunanombre,
            pvckg: MASKLA(registro.pvckg,2),
            cankg: MASKLA(registro.cankg,2),
            total: MASKLA(registro.total,0),
        })
        pvckg += registro.pvckg;
        cankg += registro.cankg;
        total += registro.total;
        aux_contreg++;
    });
    if(total > 0){
        body.push({
            id: "",
            cliente_rut: "",
            razonsocial: "",
            comuna: "Total",
            pvckg: MASKLA(pvckg,2),
            cankg: MASKLA(cankg,2),
            total: MASKLA(total,0)
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
 
    var doc = new jsPDF()
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
        2: { cellWidth: 70 },  // Ancho columna
        3: { cellWidth: 20 },  // Ancho columna
        4: { cellWidth: 20, halign: 'right' },  // Ancho columna
        5: { cellWidth: 20, halign: 'right' },  // Ancho columna
        6: { cellWidth: 20, halign: 'right' },  // Ancho columna

        // ... especifica el ancho de las demás columnas
      },
      willDrawPage: function (data) {
        // Header
        doc.setFontSize(12)
        doc.setTextColor(20)
        if (base64Img) {
          doc.addImage(base64Img, 'JPEG', data.settings.margin.left, 6, 30, 10)
        }
        doc.text('Total Notas de Venta Agrupada x Cliente', data.settings.margin.left + 55, 12);
        doc.setFontSize(8)
        doc.text('Sucursal: ' + $("#sucursal_id option:selected").html(), data.settings.margin.left + 73, 16);
        doc.text('Fecha: ' + fechaactual(), data.settings.margin.left + 220, 5);
        doc.text('Area Producción: ' + $("#areaproduccion_id option:selected").html(), data.settings.margin.left + 140, 8);
        //doc.text('Vendedor: ' + $("#vendedor_id option:selected").html(), data.settings.margin.left + 140, 11);
        doc.text('Vendedor: ' + descripElementoSelectMult("vendedor_id"), data.settings.margin.left + 140, 11);
        doc.text('Giro: ' + $("#giro_id option:selected").html() + ' Estatus: ' + $("#aprobstatus option:selected").html(), data.settings.margin.left + 140, 14);
        doc.text('Período: ' + $("#fechad").val() + " al " + $("#fechah").val(), data.settings.margin.left + 140, 17);
        //doc.text('Plazo de Entrega: ' + $("#plazoentregad").val() + " al " + $("#plazoentregah").val(), data.settings.margin.left + 220, 20);

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
        url: "/reportnvagruxcliente/reportnvagruxclientepage/" + data.data2, // ajusta la URL de la solicitud al endpoint correcto
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
        datosExcel.push(["Total Notas de Venta Agrupada x Cliente","","","","","",fechaactual()]);
        datosExcel.push(["Centro Economico: " + aux_sucursalNombre + " Periodo: " + aux_rangofecha + " Vendedor: " + descripElementoSelectMult("vendedor_id"),"","","","","","","",""]);
        aux_cont = 0;
        pvckg = 0;
        cankg = 0;
        total = 0;    
        datosExcel.push(["","","","","","","                   "]);
        datosExcel.push(["ID","RUT","Razón Social","Comuna","Total $","Kg PVC","Kg"]);
        data.data.forEach(function(registro) {
            aux_cont++;
            /* aux_fecha = new Date(registro.fechahora);
            aux_plazoentrega = new Date(registro.plazoentrega + " 00:00:00")
            aux_productonomb = registro.nombre.replace(/&quot;/g, '"');
            aux_productonomb = aux_productonomb.replace(/&#039;/g, "'"); */
            var filaExcel = [
                registro.cliente_id,
                registro.rut,
                registro.razonsocial,
                registro.comunanombre,
                registro.pvckg,
                registro.cankg,
                registro.total
            ];
            pvckg += registro.pvckg;
            cankg += registro.cankg;
            total += registro.total;
            //aux_vendedor_id = registro.vendedor_id;

            datosExcel.push(filaExcel);
        });
        if(total > 0){
            datosExcel.push(["","","","Total:",pvckg,cankg,total]);
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

    //Establecer negrilla a titulo de columnas Fila 4
    const row6 = worksheet.getRow(4);
    for (let i = 1; i <= 7; i++) {
        cell = row6.getCell(i);
        cell.font = { bold: true };
        //cell.autosize = true;
    }

    row = worksheet.getRow(datosExcel.length);
    for (let i = 1; i <= 7; i++) {
        cell = row.getCell(i);
        cell.font = { bold: true };
        cell.alignment = { horizontal: "right" };
        //cell.numFmt = "#,##0";
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
    for (let i = 1; i <= 7; i++) {
        columna = getColumnLetter(i); // Obten la letra de la columna correspondiente
        const celda = worksheet.getCell(`${columna}${fila}`);
        celda.alignment = { wrapText: true, vertical: 'middle' };
        celda.autosize = true;
    }    

    const columnI = worksheet.getColumn(5);
    columnI.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });

    const columnJ = worksheet.getColumn(6);
    columnJ.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0.00";
        }
    });

    const columnK = worksheet.getColumn(7);
    columnK.eachCell({ includeEmpty: true }, (cell) => {
        if (cell.value !== null && typeof cell.value === "number") {
        cell.numFmt = "#,##0";
        }
    });


    // Establecer el formato de centrado horizontal y vertical para las celdas de la columna 8 desde la fila 4 hasta la fila 58
    /* for (let i = 4; i <= datosExcel.length; i++) {
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

    } */


    //Negrita Columna Titulo
    const row1 = worksheet.getRow(1);
    cell = row1.getCell(1);
    cell.font = { bold: true, size: 20 };
    cell.alignment = { horizontal: "center", vertical: "middle" };


    //Titulo Kg PVC
    rowX = worksheet.getRow(4);
    cell = rowX.getCell(5);
    cell.alignment = { horizontal: "center", vertical: "middle" };

    //Titulo Kg
    rowX = worksheet.getRow(4);
    cell = rowX.getCell(6);
    cell.alignment = { horizontal: "center", vertical: "middle" };
    
    //Titulo Total
    rowX = worksheet.getRow(4);
    cell = rowX.getCell(7);
    cell.alignment = { horizontal: "center", vertical: "middle" };
    

    //Fecha Reporte
    const row2 = worksheet.getRow(1);
    cell = row2.getCell(10);
    cell.alignment = { horizontal: "center", vertical: "middle" };

    //Fusionar celdas de Titulo
    const startCol = 0;
    const endCol = 6;
    worksheet.mergeCells(1, startCol, 1, endCol);

    //Negrita Columna Sucursal
    const row3 = worksheet.getRow(2);
    cell = row3.getCell(1);
    cell.alignment = { horizontal: "center", vertical: "middle" };
    
    //Fusionar celdas Sucursal
    const startCol1 = 0;
    const endCol1 = 6;
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
      a.download = "NVAgruxCliente.xlsx";
      a.click();

      // Limpiar el objeto Blob
      window.URL.revokeObjectURL(url);
    });
}