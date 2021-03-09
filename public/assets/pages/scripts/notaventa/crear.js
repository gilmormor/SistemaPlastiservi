$(document).ready(function () {
	Biblioteca.validacionGeneral('form-general');
	/*
	$('#tabla-data-clientes').DataTable({
		'paging'      : true, 
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		'autoWidth'   : false,
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
	});*/
	$('.tablas').DataTable({
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




	//$('.form-group').css({'margin-bottom':'0px','margin-left': '0px','margin-right': '0px','padding-left' : '5px','padding-right': '5px'});
	//$('.table').css({'margin-bottom':'0px','padding-top': '0px','padding-bottom': '0px'});
	//$(".box-body").css({'padding-top': '5px','padding-bottom': '0px'});
	//$(".box").css({'margin-bottom': '0px'});
	//$(".box-header").css({'padding-bottom': '5px'});
	$("#mdialTamanio").css({'width': '50% !important'});
	//$(".control-label").css({'padding-top': '2px'});
	
	/*
    var styles = {
		backgroundColor : "#ddd",
		fontWeight: ""
	  };
	$( this ).css( styles );*/
	formato_rut($('#rut'));
	aux_sta = $("#aux_sta").val();
	if(aux_sta==1){
		$( "#rut" ).focus();
	}else{
		if(aux_sta==2)
			$("#direccion").focus();
		else
			$("#oc_file").focus();
	}
	//$("#rut").numeric();
	$("#cantM").numeric();
	$("#precioM").numeric({decimalPlaces: 2});
	$(".numerico").numeric();
	$( "#myModal" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$( "#myModalBusqueda" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$( "#myModalBuscarProd" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$(".modal-body label").css("margin-bottom", -2);
	$(".help-block").css("margin-top", -2);
	if($("#aux_fechaphp").val()!=''){
		$("#fechahora").val($("#aux_fechaphp").val());
	}
	//alert($("#aux_sta").val());

	$("#clientedirec_id").change(function(){
		
		comuna_id = $("#clientedirec_id option:selected").attr('comuna_id');
		region_id = $("#clientedirec_id option:selected").attr('region_id');
		provincia_id = $("#clientedirec_id option:selected").attr('provincia_id');
		plazopago_id = $("#clientedirec_id option:selected").attr('plazopago_id');
		formapago_id = $("#clientedirec_id option:selected").attr('formapago_id');

		$("#comuna_id").val(comuna_id);
		$("#comuna_idD").val(comuna_id);
		$("#region_id").val(region_id);
		$("#provincia_id").val(provincia_id);
		$("#plazopago_id").val(plazopago_id);
		$("#plazopago_idD").val(plazopago_id);
		$("#formapago_id").val(formapago_id);
		$("#formapago_idD").val(formapago_id);

		//$(".select2").selectmenu('refresh', true);
		$(".selectpicker").selectpicker('refresh');
		//alert($("#formapago_id").val());
	});


	$("#cantM").keyup(function(){
		//alert($(this).val());
		totalizarItem(0);
		/*
		aux_tk = $(this).val()*$("#pesoM").val();
		$("#totalkilosM").val(aux_tk.toFixed(2));
		aux_total = ($(this).val() * $("#pesoM").val() * $("#precioM").val()) * ($("#descuentoM").val())
		$("#subtotalM").val(aux_total.toFixed(2));
		aux_precdesc = $("#precioM").val() * $("#descuentoM").val();
		$("#precioM").val(aux_precdesc);
		*/
	});

	$("#descuentoM").change(function(){
		totalizarItem(1);
		//$("#cantM").change();
	});
	$("#rut").keyup(function(event){
		if(event.which==113){
			$(this).val("");
			$(".input-sm").val('');
			$("#myModalBusqueda").modal('show');
		}
	});
	$("#btnbuscarcliente").click(function(event){
			$("#rut").val("");
			$(".input-sm").val('');
			$("#myModalBusqueda").modal('show');
	});
	$("#producto_idM").keyup(function(event){
		if(event.which==113){
			$(this).val("");
			$(".input-sm").val('');
			$("#myModal").modal('hide');
			$("#myModalBuscarProd").modal('show');
		}
	});
	$("#btnbuscarproducto").click(function(e){
		e.preventDefault();
		$(this).val("");
		$(".input-sm").val('');
		//$("#myModal").modal('hide');
		//$("#myModalBuscarProd").modal('show');

		$('#myModal')
               .modal('hide')
               .on('hidden.bs.modal', function (e) {
                   $('#myModalBuscarProd').modal('show');

                   $(this).off('hidden.bs.modal'); // Remove the 'on' event binding
               });

	});

	
	$("#precioM").blur(function(event){
		totalizarItem(0);
	});

	$('.datepicker').datepicker({
		language: "es",
		autoclose: true,
		todayHighlight: true
	}).datepicker("setDate");

	//$('.tooltip').tooltipster();

	if(aux_sta==2 || aux_sta==3){
		totalizar();
	}
	aux_nomarc = $("#imagen").val();
	//alert(aux_nomarc.indexOf("."));
	//alert(aux_nomarc.substr(aux_nomarc.indexOf(".") + 1, 6));

	$("#btnguardaraprob").click(function(event){
		//alert('Entro');
		$("#myModalaprobcot").modal('show');
	});
	aux_imagen = $("#imagen").val();
	$('#oc_file').fileinput({
		language: 'es',
		allowedFileExtensions: ['jpg', 'jpeg', 'png', "pdf"],
		maxFileSize: 4000,
		initialPreview: [
			// PDF DATA
			'/storage/imagenes/notaventa/'+$("#imagen").val(),
		],
		initialPreviewAsData: true, // identify if you are sending preview data only and not the raw markup
		initialPreviewFileType: 'image', // image is the default and can be overridden in config below
		initialPreviewDownloadUrl: 'https://kartik-v.github.io/bootstrap-fileinput-samples/samples/{filename}', // includes the dynamic `filename` tag to be replaced for each config
		initialPreviewConfig: [
			{type: "pdf", size: 8000, caption: $("#imagen").val(), url: "/file-upload-batch/2", key: 10, downloadUrl: false}, // disable download
		],
        showUpload: false,
        showClose: false,
        initialPreviewAsData: true,
		dropZoneEnabled: false,
		maxFileCount: 5,
        theme: "fa",
	});

	$("#input-pd").fileinput({
		uploadUrl: "/file-upload-batch/1",
		uploadAsync: false,
		minFileCount: 2,
		maxFileCount: 5,
		overwriteInitial: false,
		initialPreview: [
			// PDF DATA
			'/storage/imagenes/notaventa/238.pdf',
		],
		initialPreviewAsData: true, // identify if you are sending preview data only and not the raw markup
		initialPreviewFileType: 'image', // image is the default and can be overridden in config below
		initialPreviewDownloadUrl: 'https://kartik-v.github.io/bootstrap-fileinput-samples/samples/{filename}', // includes the dynamic `filename` tag to be replaced for each config
		initialPreviewConfig: [
			{type: "pdf", size: 8000, caption: "238.pdf", url: "/file-upload-batch/2", key: 10, downloadUrl: false}, // disable download
		],
		purifyHtml: true, // this by default purifies HTML data for preview
		uploadExtraData: {
			img_key: "1000",
			img_keywords: "happy, places"
		}
	}).on('filesorted', function(e, params) {
		console.log('File sorted params', params);
	}).on('fileuploaded', function(e, params) {
		console.log('File uploaded params', params);
	});

	$('#foto').fileinput({
        language: 'es',
        allowedFileExtensions: ['jpg', 'jpeg', 'png'],
        maxFileSize: 4000,
        showUpload: false,
        showClose: false,
        initialPreviewAsData: true,
        dropZoneEnabled: false,
        maxFileCount: 5,
        theme: "fa",
    });

	if($("#vendedor_id").val() == '0'){
		$("#vendedor_idD").removeAttr("disabled");
		$("#vendedor_idD").removeAttr("readonly");
		$("#vendedor_idD").val("");
	}
	$("#vendedor_idD").change(function(){
		//alert($("#vendedor_idD").val());
		$("#vendedor_id").val($("#vendedor_idD").val());
	});

/*
	$("#oc_file").fileinput({
		autoReplace: true,
		overwriteInitial: true,
		showUploadedThumbs: false,
		maxFileCount: 1,
		initialPreview: [
			"<img class='kv-preview-data file-preview-image' src='/storage/imagenes/notaventa/238.pdf'>"
		],
		initialCaption: '238.pdf',
		initialPreviewShowDelete: false,
		showRemove: false,
		showClose: false,
		layoutTemplates: {actionDelete: ''}, // disable thumbnail deletion
		allowedFileExtensions: ["jpg", "png", "gif", "pdf"]
	});
*/
});



$("#botonNewProd").click(function(event)
{
	clientedirec_id = $("#clientedirec_id").val();
	aux_rut = $("#rut").val();
	if(aux_rut==""){
		mensaje('Debes Incluir RUT del cliente','','error');
		return 0;
	}
	if(aux_rut!=""){
		event.preventDefault();
		limpiarInputOT();
		quitarverificar();
		$("#aux_sta").val('1');
		$("#myModal").modal('show');
		$("#producto_idM").focus();	
	}
});


function insertarTabla(){
	$("#trneto").remove();
	$("#triva").remove();
	$("#trtotal").remove();
	//aux_nfila = 1; 
	var aux_nfila = $("#tabla-data tbody tr").length;
	aux_nfila++;
	//alert(aux_nfila);
	aux_nombre = $("#nombreprodM").val();
	codintprod = $("#codintprodM").val();
	aux_porciva = $("#aux_iva").val()
	aux_porciva = parseFloat(aux_porciva);
	aux_iva = $("#subtotalM").attr("valor") * (aux_porciva/100);
	aux_total = $("#subtotalM").attr("valor") + aux_iva;
	aux_descuento = $("#descuentoM option:selected").attr('porc');
	aux_precioxkilo = $("#precioM").attr("valor");
	aux_precioxkiloreal = $("#precioxkilorealM").val();
	if($("#pesoM").val()==0)
	{
		aux_precioxkilo = 0; //$("#precioM").attr("valor");
		aux_precioxkiloreal = 0; // $("#precioxkilorealM").val();
	}


    var htmlTags = '<tr name="fila'+ aux_nfila + '" id="fila'+ aux_nfila + '">'+
			'<td name="NVdet_idTD'+ aux_nfila + '" id="NVdet_idTD'+ aux_nfila + '">'+ 
				'0'+
			'</td>'+
			'<td style="display:none;">'+
				'<input type="text" name="NVdet_id[]" id="NVdet_id'+ aux_nfila + '" class="form-control" value="0" style="display:none;"/>'+
			'</td>'+
			'<td name="producto_idTD'+ aux_nfila + '" id="producto_idTD'+ aux_nfila + '" style="display:none;">'+ 
				'<input type="text" name="producto_id[]" id="producto_id'+ aux_nfila + '" class="form-control" value="'+ $("#producto_idM").val() +'" style="display:none;"/>'+
			'</td>'+
			'<td name="codintprodTD'+ aux_nfila + '" id="codintprodTD'+ aux_nfila + '">'+ 
				codintprod+
			'</td>'+
			'<td style="display:none;">'+ 
				'<input type="text" name="codintprod[]" id="codintprod'+ aux_nfila + '" class="form-control" value="'+ codintprod +'" style="display:none;"/>'+
			'</td>'+
			'<td name="cantTD'+ aux_nfila + '" id="cantTD'+ aux_nfila + '" style="text-align:right">'+ 
				$("#cantM").val()+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="cant[]" id="cant'+ aux_nfila + '" class="form-control" value="'+ $("#cantM").val() +'" style="display:none;"/>'+
			'</td>'+
			'<td name="nombreProdTD'+ aux_nfila + '" id="nombreProdTD'+ aux_nfila + '">'+ 
				aux_nombre+
			'</td>'+
			'<td style="display:none;">'+ 
				'<input type="text" name="unidadmedida_id[]" id="unidadmedida_id'+ aux_nfila + '" class="form-control" value="4" style="display:none;"/>'+
			'</td>'+
			'<td name="cla_nombreTD'+ aux_nfila + '" id="cla_nombreTD'+ aux_nfila + '">'+ 
				$("#cla_nombreM").val()+
			'</td>'+
			'<td name="diamextmmTD'+ aux_nfila + '" id="diamextmmTD'+ aux_nfila + '" style="text-align:right">'+ 
				$("#diamextmmM").val()+
			'</td>'+
			'<td style="display:none;">'+ 
				'<input type="text" name="diamextmm[]" id="diamextmm'+ aux_nfila + '" class="form-control" value="'+ $("#diamextmmM").val() +'" style="display:none;"/>'+
			'</td>'+
			'<td name="espesorTD'+ aux_nfila + '" id="espesorTD'+ aux_nfila + '" style="text-align:right">'+ 
				$("#espesorM").val()+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="espesor[]" id="espesor'+ aux_nfila + '" class="form-control" value="'+ $("#espesorM").val() +'" style="display:none;"/>'+
			'</td>'+
			'<td name="longTD'+ aux_nfila + '" id="longTD'+ aux_nfila + '" style="text-align:right">'+ 
				$("#longM").val()+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="long[]" id="long'+ aux_nfila + '" class="form-control" value="'+ $("#longM").val() +'" style="display:none;"/>'+
			'</td>'+
			'<td name="pesoTD'+ aux_nfila + '" id="pesoTD'+ aux_nfila + '" style="text-align:right;">'+ 
				$("#pesoM").val()+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="peso[]" id="peso'+ aux_nfila + '" class="form-control" value="'+ $("#pesoM").val() +'" style="display:none;"/>'+
			'</td>'+
			'<td name="tipounionTD'+ aux_nfila + '" id="tipounionTD'+ aux_nfila + '">'+ 
				$("#tipounionM").val()+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="tipounion[]" id="tipounion'+ aux_nfila + '" class="form-control" value="'+ $("#tipounionM").val() +'" style="display:none;"/>'+
			'</td>'+
			'<td name="descuentoTD'+ aux_nfila + '" id="descuentoTD'+ aux_nfila + '" style="text-align:right">'+ 
				$("#descuentoM option:selected").html()+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="descuento[]" id="descuento'+ aux_nfila + '" class="form-control" value="'+ aux_descuento +'" style="display:none;"/>'+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="descuentoval[]" id="descuentoval'+ aux_nfila + '" class="form-control" value="'+ $("#descuentoM option:selected").attr('value') +'" style="display:none;"/>'+
			'</td>'+
			'<td name="preciounitTD'+ aux_nfila + '" id="preciounitTD'+ aux_nfila + '" style="text-align:right">'+ 
				MASK(0, $("#precionetoM").attr("valor"), '-##,###,##0.00',1)+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="preciounit[]" id="preciounit'+ aux_nfila + '" class="form-control" value="'+ $("#precionetoM").attr("valor") +'" style="display:none;"/>'+
			'</td>'+
			'<td name="precioxkiloTD'+ aux_nfila + '" id="precioxkiloTD'+ aux_nfila + '" style="text-align:right">'+ 
				MASK(0, aux_precioxkilo, '-##,###,##0.00',1)+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="precioxkilo[]" id="precioxkilo'+ aux_nfila + '" class="form-control" value="'+ aux_precioxkilo +'" style="display:none;"/>'+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="precioxkiloreal[]" id="precioxkiloreal'+ aux_nfila + '" class="form-control" value="'+ aux_precioxkiloreal +'" style="display:none;"/>'+
			'</td>'+
			'<td name="totalkilosTD'+ aux_nfila + '" id="totalkilosTD'+ aux_nfila + '" style="text-align:right">'+ 
				MASK(0, $("#totalkilosM").attr("valor"), '-##,###,##0.00',1)+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="totalkilos[]" id="totalkilos'+ aux_nfila + '" class="form-control" value="'+ $("#totalkilosM").attr("valor") +'" style="display:none;"/>'+
			'</td>'+
			'<td name="subtotalCFTD'+ aux_nfila + '" id="subtotalCFTD'+ aux_nfila + '" class="subtotalCF" style="text-align:right">'+ 
				MASK(0, $("#subtotalM").attr("valor"), '-##,###,##0.00',1)+
			'</td>'+
			'<td class="subtotalCF" style="text-align:right;display:none;">'+ 
				'<input type="text" name="subtotal[]" id="subtotal'+ aux_nfila + '" class="form-control" value="'+ $("#subtotalM").attr("valor") +'" style="display:none;"/>'+
			'</td>'+
			'<td name="subtotalSFTD'+ aux_nfila + '" id="subtotalSFTD'+ aux_nfila + '" class="subtotal" style="text-align:right;display:none;">'+ 
				$("#subtotalM").attr("valor")+
			'</td>'+
			'<td>' + 
				'<a href="#" class="btn-accion-tabla tooltipsC" title="Editar este registro" onclick="editarRegistro('+ aux_nfila +')">'+
				'<i class="fa fa-fw fa-pencil"></i>'+
				'</a>'+
				'<a href="#" class="btn-accion-tabla eliminar tooltipsC" title="Eliminar este registro" onclick="eliminarRegistro('+ aux_nfila +')">'+
				'<i class="fa fa-fw fa-trash text-danger"></i></a>'+
			'</td>'+
		'</tr>'+
		'<tr id="trneto" name="trneto">'+
			'<td colspan="14" style="text-align:right"><b>Neto</b></td>'+
			'<td id="tdneto" name="tdneto" style="text-align:right">0.00</td>'+
		'</tr>'+
		'<tr id="triva" name="triva">'+
			'<td colspan="14" style="text-align:right"><b>IVA ' + $("#aux_iva").val() + '%</b></td>'+
			'<td id="tdiva" name="tdiva" style="text-align:right">0.00</td>'+
		'</tr>'+
		'<tr id="trtotal" name="trtotal">'+
			'<td colspan="14" style="text-align:right"><b>Total</b></td>'+
			'<td id="tdtotal" name="tdtotal" style="text-align:right">0.00</td>'+
		'</tr>';
	
	$('#tabla-data tbody').append(htmlTags);
	totalizar();
}



function llenarProvincia(obj,i){
	var data = {
        region_id: $(obj).val(),
        _token: $('input[name=_token]').val()
    };
    $.ajax({
        url: '/sucursal/obtProvincias',
        type: 'POST',
        data: data,
        success: function (provincias) {
            $("#provincia_idM").empty();
            //$(".provincia_id").append("<option value=''>Seleccione...</option>");
            $("#comuna_idM").empty();
            //$(".comuna_id").append("<option value=''>Seleccione...</option>");
            $.each(provincias, function(index,value){
                $("#provincia_idM").append("<option value='" + index + "'>" + value + "</option>")
			});
			$(".selectpicker").selectpicker('refresh');
			if(i>0){
				$("#provincia_idM").val($("#provincia_id"+i).val());
				llenarComuna("#provincia_id"+i,i);
			}
			$(".selectpicker").selectpicker('refresh');
		}
    });
}

$('.provincia_id').on('change', function () {
    llenarComuna(this,0);
});




function eliminarRegistro(i){
	//event.preventDefault();
	//alert($('input[name=_token]').val());
	var data = {
		id: $("#NVdet_idTD"+i).html(),
		nfila : i,
		_token: $('input[name=_token]').val()
	};
	var ruta = '/notaventa/eliminarDetalle/'+i;
	swal({
		title: '¿ Está seguro que desea eliminar el registro ?',
		text: "Esta acción no se puede deshacer!",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			mensajeEliminarRegistro(data);
			/*
			if(data['id']=='0'){
				mensajeEliminarRegistro(data);
			}else{
				ajaxRequest(data,ruta,'eliminar');
			}
			*/
		}
	});
}

function mensajeEliminarRegistro(data){
	$("#fila"+data['nfila']).remove();
	Biblioteca.notificaciones('Registro eliminado. Debes actualizar o guardar para que los cambios surtan efecto.', 'Plastiservi', 'success');
	totalizar();
}

function ajaxRequest(data,url,funcion) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='eliminar'){
				if (respuesta.mensaje == "ok" || data['id']=='0') {
					mensajeEliminarRegistro(data);
					/*
					$("#fila"+data['nfila']).remove();
					Biblioteca.notificaciones('El registro fue eliminado correctamente', 'Plastiservi', 'success');
					totalizar();*/
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso para eliminar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser eliminado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
			}
			if(funcion=='verUsuario'){
				$('#myModal .modal-body').html(respuesta);
				$("#myModal").modal('show');
			}
			if(funcion=='aprobarnvsup'){
				if (respuesta.mensaje == "ok") {
					Biblioteca.notificaciones('El registro fue actualizado correctamente', 'Plastiservi', 'success');
					// *** REDIRECCIONA A UNA RUTA*** 
					var loc = window.location;
    				window.location = loc.protocol+"//"+loc.hostname+"/notaventaaprobar";
					// ****************************** 
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no puso se actualizado.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no puso se actualizado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
			}
		},
		error: function () {
		}
	});
}

// formatea un numero según una mascara dada ej: "-$###,###,##0.00"
//
// elm   = elemento html <input> donde colocar el resultado
// n     = numero a formatear
// mask  = mascara ej: "-$###,###,##0.00"
// force = formatea el numero aun si n es igual a 0
//
// La función devuelve el numero formateado

function MASK(form, n, mask, format) {
	if (format == "undefined") format = false;
	if (format || NUM(n)) {
	  dec = 0, point = 0;
	  x = mask.indexOf(".")+1;
	  if (x) { dec = mask.length - x; }
  
	  if (dec) {
		n = NUM(n, dec)+"";
		x = n.indexOf(".")+1;
		if (x) { point = n.length - x; } else { n += "."; }
	  } else {
		n = NUM(n, 0)+"";
	  } 
	  for (var x = point; x < dec ; x++) {
		n += "0";
	  }
	  x = n.length, y = mask.length, XMASK = "";
	  while ( x || y ) {
		if ( x ) {
		  while ( y && "#0.".indexOf(mask.charAt(y-1)) == -1 ) {
			if ( n.charAt(x-1) != "-")
			  XMASK = mask.charAt(y-1) + XMASK;
			y--;
		  }
		  XMASK = n.charAt(x-1) + XMASK, x--;
		} else if ( y && "$0".indexOf(mask.charAt(y-1))+1 ) {
		  XMASK = mask.charAt(y-1) + XMASK;
		}
		if ( y ) { y-- }
	  }
	} else {
	   XMASK="";
	}
	/*
	if (form) { 
	  form.value = XMASK;
	  if (NUM(n)<0) {
		form.style.color="#FF0000";
	  } else {
		form.style.color="#000000";
	  }
	}
	*/
	return XMASK;
  }
  
  // Convierte una cadena alfanumérica a numérica (incluyendo formulas aritméticas)
  //
  // s   = cadena a ser convertida a numérica
  // dec = numero de decimales a redondear
  //
  // La función devuelve el numero redondeado
  
  function NUM(s, dec) {
	for (var s = s+"", num = "", x = 0 ; x < s.length ; x++) {
	  c = s.charAt(x);
	  if (".-+/*".indexOf(c)+1 || c != " " && !isNaN(c)) { num+=c; }
	}
	if (isNaN(num)) { num = eval(num); }
	if (num == "")  { num=0; } else { num = parseFloat(num); }
	if (dec != undefined) {
	  r=.5; if (num<0) r=-r;
	  e=Math.pow(10, (dec>0) ? dec : 0 );
	  return parseInt(num*e+r) / e;
	} else {
	  return num;
	}
  }

function copiar_rut(id,rut){
	$("#myModalBusqueda").modal('hide');
	$("#rut").val(rut);
	//$("#rut").focus();
	$("#rut").blur();
	$("#razonsocial").focus();
}

$("#rut").focus(function(){
	$("#clientedirec_id").prop("disabled",true);
	eliminarFormatoRut($(this));
});

$("#oc_id").blur(function(){
	aux_ocid = $.trim($("#oc_id").val());
	$("#oc_id").val(aux_ocid);
	if(aux_ocid != "" ){
		var data = {
			oc_id: $("#oc_id").val(),
			_token: $('input[name=_token]').val()
		};
		$.ajax({
			url: '/notaventa/buscaroc_id',
			type: 'POST',
			data: data,
			success: function (respuesta) {
				if(respuesta.mensaje == 'ok'){
					swal({
						title: 'Orden de compra N°.' +data.oc_id+ ' ya existe.',
						text: "",
						icon: 'error',
						buttons: {
							confirm: "Aceptar"
						},
					}).then((value) => {
						if (value) {
							//$("#oc_id").focus();
						}
					});
				
				}
			}
		});
	
	}
});

function copiar_codprod(id,codintprod){
	//$("#myModalBuscarProd").modal('hide');
	//$("#myModal").modal('show');
	$('#myModalBuscarProd')
               .modal('hide')
               .on('hidden.bs.modal', function (e) {
                   $('#myModal').modal('show');

                   $(this).off('hidden.bs.modal'); // Remove the 'on' event binding
			   });
			   
	$("#producto_idM").val(id);
	$("#producto_idM").blur();
	$("#cantM").focus();
	

}

$("#rut").blur(function(){
	codigo = $("#rut").val();
	limpiarCampos();
	aux_sta = $("#aux_sta").val();
	formato_rut($("#rut"));
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
				rut: codigo,
				_token: $('input[name=_token]').val()
			};
			$.ajax({
				url: '/cliente/buscarCliId',
				type: 'POST',
				data: data,
				success: function (respuesta) {
					if(respuesta.length>0){
						//alert(respuesta[0]['vendedor_id']);
						if(respuesta[0]['descripcion']==null){
							$("#razonsocial").val(respuesta[0]['razonsocial']);
							$("#telefono").val(respuesta[0]['telefono']);
							$("#email").val(respuesta[0]['email']);
							$("#direccion").val(respuesta[0]['direccion']);
							$("#direccioncot").val(respuesta[0]['direccion']);
							$("#cliente_id").val(respuesta[0]['id'])
							$("#contacto").val(respuesta[0]['contactonombre']);
							/*
							$("#vendedor_id").val(respuesta[0]['vendedor_id']);
							$("#vendedor_idD").val(respuesta[0]['vendedor_id']);
							*/
							$("#region_id").val(respuesta[0]['regionp_id']);
							//alert($("#region_id").val());
							$("#provincia_id").val(respuesta[0]['provinciap_id']);
							$("#comuna_id").val(respuesta[0]['comunap_id']);
							$("#comuna_idD").val(respuesta[0]['comunap_id']);
							$("#giro_id").val(respuesta[0]['giro_id']);
							$("#giro_idD").val(respuesta[0]['giro_id']);
							$("#plazopago_id").val(respuesta[0]['plazopago_id']);
							$("#plazopago_idD").val(respuesta[0]['plazopago_id']);
							$("#formapago_id").val(respuesta[0]['formapago_id']);
							$("#formapago_idD").val(respuesta[0]['formapago_id']);
	
	
							$("#clientedirec_id option").remove();
							if(respuesta[0]['direcciondetalle']!=null){
								$("#clientedirec_id").prop("disabled",false);
								$("#clientedirec_id").prop("readonly",false);	
								$('#lblclientedirec_id').attr("class", 'requerido');
								$('#clientedirec_id').attr("required", true);
								$("#clientedirec_id").append("<option value=''>Seleccione...</option>")
	
								for(var i=0;i<respuesta.length;i++){
									//alert(respuesta[i]['direccion']);
									$("#clientedirec_id").append("<option provincia_id='" + respuesta[i]['provincia_id'] + "' region_id='" + respuesta[i]['region_id'] + "' comuna_id='" + respuesta[i]['comuna_id'] + "' formapago_id='" + respuesta[i]['formapago_id'] + "' plazopago_id='" + respuesta[i]['plazopago_id'] + "' value='" + respuesta[i]['direc_id'] + "'>" + respuesta[i]['direcciondetalle'] + "</option>")
								}
							}
							activar_controles();
	
							$(".selectpicker").selectpicker('refresh');
						}else{
							swal({
								title: 'Cliente Bloqueado.',
								text: respuesta[0]['descripcion'],
								icon: 'error',
								buttons: {
									confirm: "Aceptar"
								},
							}).then((value) => {
								if (value) {
									//ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
									$("#rut").val('');
									$("#rut").focus();
								}
							});
						}

					}else{
						swal({
							title: 'Cliente no existe.',
							text: "Presione F2 para buscar",
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
					}
				}
			});
		}
	}
});

$("#producto_idM").blur(function(){
	codigo = $("#producto_idM").val();
	//limpiarCampos();
	aux_sta = $("#aux_sta").val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		//totalizar();
		var data = {
			id: $("#producto_idM").val(),
			_token: $('input[name=_token]').val()
		};
		$.ajax({
			url: '/producto/buscarUnProducto',
			type: 'POST',
			data: data,
			success: function (respuesta) {
				if(respuesta.length>0){

					$("#nombreprodM").val(respuesta[0]['nombre']);
					$("#codintprodM").val(respuesta[0]['codintprod']);
					$("#cla_nombreM").val(respuesta[0]['cla_nombre']);
					if (respuesta[0]['unidadmedida_id']=='3'){
						$("#diamextmmM").val(respuesta[0]['diamextpg']);
					}else{
						$("#diamextmmM").val(respuesta[0]['diamextmm']+'mm');
					}
					$("#espesorM").val(respuesta[0]['espesor']);
					$("#longM").val(respuesta[0]['long']);
					$("#pesoM").val(respuesta[0]['peso']);
					$("#tipounionM").val(respuesta[0]['tipounion']);
					$("#precioM").val(respuesta[0]['precio']);
					$("#precioM").attr('valor',respuesta[0]['precio']);
					$("#precioxkilorealM").val(respuesta[0]['precio']);
					$("#precioxkilorealM").attr('valor',respuesta[0]['precio']);
					$("#precionetoM").val(respuesta[0]['precioneto']);
					$("#precionetoM").attr('valor',respuesta[0]['precioneto']);
					//alert(respuesta[0]['precio']);
					
					
					//$("#cantM").change();
					$("#cantM").focus();
					totalizarItem(1);
					
				}else{
					swal({
						title: 'Producto no existe.',
						text: "Presione F2 para buscar",
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
				}
			}
		});
	}
});

function mensaje(titulo,texto,icono){
	swal({
		title: titulo,
		text: texto,
		icon: icono,
		buttons: {
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			//ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
			//$("#rut").focus();
		}
	});
}

function activar_controles(){
	$("#observacion").prop("disabled",false);
	$("#observacion").prop("readonly",false);
	$("#lugarentrega").prop("disabled",false);
	$("#lugarentrega").prop("readonly",false);	
}

function desactivar_controles(){
	$("#clientedirec_id").prop("disabled",true);
	$("#observacion").prop("disabled",true);
	$("#observacion").prop("readonly",true);
	$("#lugarentrega").prop("disabled",true);
	$("#lugarentrega").prop("readonly",true);	
}


function limpiarCampos(){

	$("#razonsocial").val('');
	$("#telefono").val('');
	$("#email").val('');
	$("#direccion").val('');
	$("#direccioncot").val('');
	$("#cliente_id").val('')
	$("#contacto").val('');
	/*
	$("#vendedor_id").val('');
	$("#vendedor_idD").val('');
	*/
	$("#region_id").val('');
	//alert($("#region_id").val());
	$("#provincia_id").val('');
	$("#comuna_id").val('');
	$("#comuna_idD").val('');

	$("#clientedirec_id option").remove();

	$("#direccioncot").val('');
	$("#cliente_id").val('');
	$("#formapago_id").val('');
	$("#formapago_idD").val('');
	$("#plazopago_id").val('');
	$("#plazopago_idD").val('');
	$("#giro_id").val('');
	$("#giro_idD").val('');
	
	$("#contacto").val('');
	$("#region_id").val('');
	$("#provincia_id").val('');
	//$("#usuario_id").val('');
	$("#neto").val('');
	$("#iva").val('');
	$("#total").val('');
	totalizar();
}

$("#btnaprobarM").click(function(event)
{
	event.preventDefault();
	//alert($('input[name=_token]').val());
	var data = {
		id    : $("#id").val(),
		valor : 3,
		obs   : $("#aprobobs").val(),
        _token: $('input[name=_token]').val()
	};
	var ruta = '/notaventa/aprobarnvsup/'+data['id'];
	swal({
		title: '¿ Está seguro que desea Aprobar la Nota de Venta ?',
		text: "Esta acción no se puede deshacer!",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			ajaxRequest(data,ruta,'aprobarnvsup');
		}
	});
});

$("#btnrechazarM").click(function(event)
{
	event.preventDefault();
	if(verificarAproRech())
	{
		var data = {
			id    : $("#id").val(),
			valor : 4,
			obs   : $("#aprobobs").val(),
			_token: $('input[name=_token]').val()
		};
		var ruta = '/notaventa/aprobarnvsup/'+data['id'];
		swal({
			title: '¿ Está seguro que desea Rechazar la Nota de Venta ?',
			text: "Esta acción no se puede deshacer!",
			icon: 'warning',
			buttons: {
				cancel: "Cancelar",
				confirm: "Aceptar"
			},
		}).then((value) => {
			if (value) {
				ajaxRequest(data,ruta,'aprobarnvsup');
			}
		});

	}else{
		alertify.error("Falta incluir informacion");
	}
	
});
$(".requeridos").keyup(function(){
	//alert($(this).parent().attr('class'));
	validacion($(this).prop('name'),$(this).attr('tipoval'));
});
function verificarAproRech()
{
	var v1=0;
	
	v1=validacion('aprobobs','texto');
	if (v1===false)
	{
		return false;
	}else{
		return true;
	}
}

$("#btnfotooc").click(function(){
	$("#myModalFotoOC").modal('show');
});

$("#btnverfoto").click(function(){
	$("#myModalverfoto").modal('show');
});


$('#form-general').submit(function() {
	//alert('prueba');
	$("#cotizacion_id").prop('disabled', false);
	$("#clientedirec_id").prop('disabled', false);
	$("#plazoentrega").prop('disabled', false);
	$("#lugarentrega").prop('disabled', false);
	$("#tipoentrega_id").prop('disabled', false);
    //Rest of code
})
