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
        consultarpage(datosFac());
    });
    $("#btnpdf2").click(function()
    {
        btnpdf(datosFac());
    });
    consultarpage(datosFac());
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
            aux_nombreprod = data.nmbitem + aux_diametro + aux_cla_nombre + aux_long + aux_tipounion;
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
    data = datosFac();
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

function datosFac(orderby = ""){
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
    $('#contpdf').attr('src', '/reportdtecomisionxvend/exportPdf/'+data.data2);
    $("#myModalpdf").modal('show');
}

function exportarExcel() {
    var tabla = $('#tabla-data-consulta').DataTable();
    orderby = " order by dte.vendedor_id asc,foliocontrol.doc,dte.id ";
    data = datosFac(orderby);
    // Obtener todos los registros mediante una solicitud AJAX
    $.ajax({
      url: "/reportdtecomisionxvend/reportdtecomisionxvendpage/" + data.data2, // ajusta la URL de la solicitud al endpoint correcto
      type: 'POST',
      dataType: 'json',
      success: function(data) {
        //console.log(data);
        // Crear una matriz para los datos de Excel
        var datosExcel = [];
        //console.log(datosExcel);
        
        // Agregar encabezados de columna al arreglo
        /*
        var encabezados = tabla.columns().header().toArray();
        var encabezadosExcel = encabezados.map(function(encabezado) {
          return encabezado.innerHTML;
        });
        datosExcel.push(encabezadosExcel);
        */
        // Agregar los datos de la tabla al arreglo
        aux_vendedor_id = "";
		count = 0;
        //console.log(data);
        data.data.forEach(function(registro) {
            if (registro.vendedor_id != aux_vendedor_id){
                datosExcel.push([registro.vendedor_nombre,"","","","","","","",""]);
                datosExcel.push(["Tipo Doc","NDoc","Fecha","Cliente","RUT","Producto","Neto","Comisión %","Comisión $"]);
            }
            aux_fecha = new Date(registro.fechahora);
            var filaExcel = [
                registro.foliocontrol_doc,
                registro.nrodocto,
                fechaddmmaaaa(aux_fecha),
                registro.razonsocial,
                registro.rut,
                registro.nmbitem,
                registro.montoitem,
                registro.porc_comision,
                registro.comision
            ];
            aux_vendedor_id = registro.vendedor_id;
            count++;

            datosExcel.push(filaExcel);
        });
        
        // Crear el libro de Excel
        var libro = XLSX.utils.book_new();
        var hoja = XLSX.utils.aoa_to_sheet(datosExcel);
        XLSX.utils.book_append_sheet(libro, hoja, 'Datos');

        hoja['!autofilter'] = [{ ref: "A:A" }];
        

        /*
        if (!hoja['!cols']) hoja['!cols'] = [];
        hoja['!cols'][3] = { width: 20 };
        hoja['!cols'][5] = { width: 20 };
        */

        // Generar el archivo Excel y descargarlo
        XLSX.writeFile(libro, 'comisonxVend.xlsx');
      },
      error: function(xhr, status, error) {
        console.log(error);
      }
    });
  }