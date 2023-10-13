$(document).ready(function () {
	Biblioteca.validacionGeneral('form-general');
	aux_obs = $("#aux_obs").val();
	$("#obs").val(aux_obs);

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

	var dateToday = new Date(); 
	var date = new Date();
	var ultimoDia = new Date(date.getFullYear(), date.getMonth() + 1, 0);
	/*
	$("#fchemis").datepicker({
		language: "es",
		autoclose: true,
		endDate: ultimoDia,
		minDate: dateToday,
		startDate: new Date(),
		todayHighlight: true
	}).datepicker("setDate");
	/*


	$("#mdialTamanio").css({'width': '50% !important'});
	//$(".control-label").css({'padding-top': '2px'});
	
	/*
    var styles = {
		backgroundColor : "#ddd",
		fontWeight: ""
	  };
	$( this ).css( styles );*/
	aux_sta = $("#aux_sta").val();
	//$("#rut").numeric();
	$("#cantM").numeric();
	$("#precioM").numeric({decimalPlaces: 2});
	$(".numerico").numeric();
	//$( "#myModal" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$( "#myModalBusqueda" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$( "#myModalBuscarProd" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$(".modal-body label").css("margin-bottom", -2);
	$(".help-block").css("margin-top", -2);
	if($("#aux_fechaphp").val()!=''){
		$("#fechahora").val($("#aux_fechaphp").val());
	}

	iniciarFileinput();

	totalizar();

});

function iniciarFileinput(){
	$('#oc_file').fileinput({
		language: 'es',
		allowedFileExtensions: ['jpg', 'jpeg', 'png', 'pdf'],
		maxFileSize: 400,
		/*
		initialPreview: [
			// PDF DATA
			'/storage/imagenes/notaventa/'+$("#imagen").val(),
		],*/
		initialPreviewShowDelete: false,
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
	}).on('fileclear', function(event) {
		//console.log("fileclear");
		$('#oc_file').attr("data-initial-preview","");
		$("#imagen").val("");
		//alert('entro');
	}).on('fileimageloaded', function(e, params) {
		//console.log('Paso');
		//console.log('File uploaded params', params);
		//console.log($('#oc_file').val());
		$("#imagen").val($('#oc_file').val());
	});
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
			if(funcion=="listardtedet"){
				//console.log(respuesta.length);
				//console.log(respuesta);
				if(respuesta.length>0){
					aux_oc_id = respuesta[0].oc_id;
					for (i = 0; i < respuesta.length; i++) {
						if(respuesta[i].oc_id != aux_oc_id){
							/*
							swal({
								title: 'Orden de compra.',
								text: "Solo puede facturar una orden de compra.",
								icon: 'error',
								buttons: {
									confirm: "Aceptar"
								},				
							})
							*/
							swal({
								title: "Orden de compra.",
								text: "Quieres facturar Guias con diferente Nro. de OC?",
								icon: 'warning',
								buttons: {
									confirm: "Si",
									cancel: "No"
								},
							}).then((value) => {
								if (value == null) {
									$('#centroeconomico_id').val(""); // Select the option with a value of '1'
									$('#vendedor_id').val(""); // Select the option with a value of '1'		
									$('#tabla-data tbody').html("");
									totalizar();
									$('.select2').trigger('change'); // Notify any JS components that the value changed
									return 0;
								}
							});
						}
					}
					if(aux_oc_id == "" || aux_oc_id == null){
						$("#ocnv_id").val("");
						$("#lblocnv_id").html("OC");
						$("#ocnv_id").attr("disabled",true)

						$("#oc_id").attr("disabled", false);
						$('#group_oc_file').show()
					}else{
						$("#ocnv_id").val(aux_oc_id);
						aux_href = "<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' onclick='verpdf2(\"" + respuesta[0].oc_file + "\",2)'>" + 
										aux_oc_id + 
									"</a>";
						$("#ocnv_id").attr("disabled",false)
						$("#lblocnv_id").html("OC: " + aux_href);
						$("#oc_id").attr("disabled", true);
						$('#group_oc_file').hide();

					}
					$('#centroeconomico_id').val(respuesta[0].centroeconomico_id); // Select the option with a value of '1'
					$('#vendedor_id').val(respuesta[0].vendedor_id); // Select the option with a value of '1'
					$("#notaventa_id").val(respuesta[0].notaventa_id);
					$('.select2').trigger('change'); // Notify any JS components that the value changed
					//$("#centroeconomico_id option[value='"+ respuesta[0].centroeconomico_id +"']").attr("selected",true);
					//$(".selectpicker").selectpicker('refresh');
					llenarItemFact(respuesta)
				}
			}
		},
		error: function () {
		}
	});
}

$(".requeridos").keyup(function(){
	//alert($(this).parent().attr('class'));
	validacion($(this).prop('name'),$(this).attr('tipoval'));
});
$(".requeridos").change(function(){
	//alert($(this).parent().attr('class'));
	validacion($(this).prop('name'),$(this).attr('tipoval'));
});


function editKilos(id){
	let input = document.createElement("input");
	aux_kilos = $("#aux_kilos" + id).html();
	input.value = aux_kilos.trim();
	input.type = 'text';
	input.className = 'swal-content__input';

	swal({
		text: "Editar Kilos",
		content: input,
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			$("#aux_kilos" + id).html(input.value)
			$("#totalkilos" + id).val(input.value)
			$("#itemkg" + id).val(input.value)
		}
	});
	
}

$("#btnbuscarcliente").click(function(event){
    $("#rut").val("");
	$('#botonNewGuia').hide();
    $("#myModalBusqueda").modal('show');
});

function copiar_rut(id,rut){
	$("#myModalBusqueda").modal('hide');
	$("#rut").val(rut);
	//$("#rut").focus();
	$("#rut").blur();
}

$("#rut").blur(function(){
	blanquearDatos();
	codigo = $("#rut").val();
	//limpiarCampos();
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
				rut: codigo,
				_token: $('input[name=_token]').val()
			};
			$.ajax({
				//url: '/cliente/buscarCliId',
				url: '/cliente/buscarCliRut',
				type: 'POST',
				data: data,
				success: function (respuesta) {
					if(respuesta.cliente.length>0){
						//alert(respuesta[0]['vendedor_id']);
						if(respuesta.cliente[0].descripcion==null){
							formato_rut($("#rut"));

							$("#razonsocial").val(respuesta.cliente[0].razonsocial);
							$("#telefono").val(respuesta.cliente[0].telefono);
							$("#email").val(respuesta.cliente[0].email);
							$("#direccion").val(respuesta.cliente[0].direccion);

							$("#comuna_nombre").val(respuesta.cliente[0].comuna_nombre);
							$("#provincia_nombre").val(respuesta.cliente[0].provincia_nombre);


							$("#direccioncot").val(respuesta.cliente[0].direccion);
							$("#cliente_id").val(respuesta.cliente[0].id)
							$("#contacto").val(respuesta.cliente[0].contactonombre);
							//$("#vendedor_id").val(respuesta[0]['vendedor_id']);
							//$("#vendedor_idD").val(respuesta[0]['vendedor_id']);
							$("#region_id").val(respuesta.cliente[0].regionp_id);
							$("#provincia_id").val(respuesta.cliente[0].provinciap_id);
							$("#comuna_id").val(respuesta.cliente[0].comunap_id);
							$("#comuna_idD").val(respuesta.cliente[0].comunap_id);

							$("#vendedor_idD").val(respuesta.cliente[0].vendedor_id);

							$("#formapago_desc").val(respuesta.cliente[0].formapago_desc);
							$("#plazopago").val(respuesta.cliente[0].plazopago_dias);
							$("#fchemis").change();
							
							/*
							$("#giro_id").val(respuesta.cliente[0].giro_id);
							$("#giro_idD").val(respuesta.cliente[0].giro_id);
							$("#plazopago_id").val(respuesta.cliente[0].plazopago_id);
							$("#plazopago_idD").val(respuesta.cliente[0].plazopago_id);
							$("#formapago_id").val(respuesta.cliente[0].formapago_id);
							$("#formapago_idD").val(respuesta.cliente[0].formapago_id);

							$("#sucursal_id option").remove();
							$("#sucursal_id").prop("disabled",false);
							$("#sucursal_id").prop("readonly",false);	
							$('#sucursal_id').attr("required", true);
							$("#sucursal_id").append("<option value=''>Seleccione...</option>")
							for(var i=0;i<respuesta.sucursales.length;i++){
								$("#sucursal_id").append("<option value='" + respuesta.sucursales[i].id + "'>" + respuesta.sucursales[i].nombre + "</option>")
							}
							if (respuesta.sucursales.length == 1){
								$("#sucursal_id").val(respuesta.sucursales[0].id);
							}	
							activar_controles();
							*/
							$('#botonNewGuia').show();
							data = datosdteguiadesp(1);
							$('#tabla-data-dteguiadesp').DataTable().ajax.url( "/dtefactura/listarguiadesppage/" + data.data2, ).load();
						
							$(".selectpicker").selectpicker('refresh');
						}else{
							swal({
								title: 'Cliente Bloqueado.',
								text: respuesta.cliente[0].descripcion,
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

$("#rut").focus(function(){
	blanquearDatos();
	eliminarFormatoRut($("#rut"));
	$('#botonNewGuia').hide();
	//$("#rut").val(aux_rut);
})

$("#botonNewGuia").click(function(event){
    //$("#rut").val("");

	$(this).val("");
	$(".input-sm").val('');
	data = datosdteguiadesp(1);
	$('#tabla-data-dteguiadesp').DataTable().ajax.url( "/dtefactura/listarguiadesppage/" + data.data2, ).load();

    $("#myModalBuscardteguiadesp").modal('show');

});



$("#btnaceptarGD").click(function(event){
	//console.log($("#selectguiadesp").val());
	let strdte_id = $("#selectguiadesp").val();
	strdte_id = strdte_id.trim();
	let arrdte_id = strdte_id.split(','); 

	var data = {
        arrdte_id   : arrdte_id,
		strdte_id   : strdte_id,
        dtenotnull  : 1, //Estatus que se envia a la consulta para mostrar o no los dte anulados (1=no se trae los anulados ""=empty se trae todo sin importar que esta anulado)
        _token      : $('input[name=_token]').val()
    };
	url = "/dtefactura/listardtedet";
	funcion = "listardtedet";
	ajaxRequest(data,url,funcion);
});


function llenarItemFact(data){
	let htmlTags = "";
	$('#tabla-data tbody').html(htmlTags);
	for (i = 0; i < data.length; i++) {
		htmlTags = '<tr name="fila' + (i+1) + '" id="fila' + (i+1) + '" class="proditems ' + data[i].nrodocto + '">' +
			'<td style="text-align:center">' +
				(i + 1) +
				'<input type="text" name="det_id[]" id="det_id' + (i+1) + '" class="form-control" value="0" style="display:none;"/>' +
				'<input type="text" name="nrolindet[]" id="nrolindet' + (i+1) + '" class="form-control" value="' + (i + 1) + '" style="display:none;"/>' +
				'<input type="text" name="despachoorddet_id[]" id="despachoorddet_id' + (i+1) + '" class="form-control" value="' + data[i].dtedet_id + '" style="display:none;"/>' +
				'<input type="text" name="notaventadetalle_id[]" id="notaventadetalle_id' + (i+1) + '" class="form-control" value="' + data[i].notaventadetalle_id + '" style="display:none;"/>' +
				'<input type="text" name="dte_id[]" id="dte_id' + (i+1) + '" class="form-control" value="' + $("#dte_id").val() + '" style="display:none;"/>' +
				'<input type="text" name="dtedet_id[]" id="dtedet_id' + (i+1) + '" class="form-control" value="' + data[i].dtedet_id + '" style="display:none;"/>' +
				'<input type="text" name="dteorigen_id[]" id="dteorigen_id' + (i+1) + '" class="form-control" value="' + data[i].id + '" style="display:none;"/>' +
				'<input type="text" name="obsdet[]" id="obsdet' + (i+1) + '" class="form-control" value="' + data[i].obsdet + '" style="display:none;"/>' +
			'</td>' +
			'<td style="text-align:center" name="producto_idTD' + (i+1) + '" id="producto_idTD' + (i+1) + '" >' +
				data[i].producto_id +
				'<input type="text" name="producto_id[]" id="producto_id' + (i+1) + '" class="form-control" value="' + data[i].producto_id +'" style="display:none;"/>' +
			'</td>' +
			'<td name="nrodoctoTD' + (i+1) + '" id="nrodoctoTD' + (i+1) + '" style="text-align:right">' +
				'<a id="nrodocto' + (i+1) + '" name="nrodocto' + (i+1) + '" class="btn-accion-tabla btn-sm verguiasii" title="Editar valor" data-toggle="tooltip" nomcampo="nrodocto" valor="' + data[i].nrodocto + '" title="Guia Despacho: ' + data[i].nrodocto + '" onclick="verGD(' + data[i].nrodocto + ')">' +
					data[i].nrodocto + 
				'</a>' +
			'</td>' +
			'<td name="cantTD' + (i+1) + '" id="cantTD' + (i+1) + '" style="text-align:right" class="subtotalcant" valor="' + data[i].qtyitem + '">' +
					data[i].qtyitem +
				'<input type="text" name="cant[]" id="cant' + (i+1) + '" class="form-control" value="' + data[i].qtyitem + '" style="display:none;"/>' +
				'<input type="text" name="qtyitem[]" id="qtyitem' + (i+1) + '" class="form-control" value="' + data[i].qtyitem + '" style="display:none;"/>' +
			'</td>' +
			'<td name="unidadmedida_nombre' + (i+1) + '" id="unidadmedida_nombre' + (i+1) + '" valor="' + data[i].unmditem + '">' +
					data[i].unmditem +
				'<input type="text" name="unidadmedida_id[]" id="unidadmedida_id' + (i+1) + '" class="form-control" value="' + data[i].unidadmedida_id + '" style="display:none;"/>' +
				'<input type="text" name="unmditem[]" id="unmditem' + (i+1) + '" class="form-control" value="' + data[i].unmditem + '" style="display:none;"/>' +
			'</td>' +
			'<td name="nombreProdTD' + (i+1) + '" id="nombreProdTD' + (i+1) + '" valor="' + data[i].nmbitem + '">' +
					data[i].nmbitem +
				'<input type="text" name="nmbitem[]" id="nmbitem' + (i+1) + '" class="form-control" value="' + data[i].nmbitem + '" style="display:none;"/>' +
				'<input type="text" name="dscitem[]" id="dscitem' + (i+1) + '" class="form-control" value="' + data[i].dscitem + '" style="display:none;"/>' +
			'</td>' +
			'<td style="text-align:right;" class="subtotalkg" valor="' + data[i].itemkg + '">' +
					MASKLA(data[i].itemkg,2) +
				'<input type="text" name="totalkilos[]" id="totalkilos' + (i+1) + '" class="form-control" value="' + data[i].itemkg + '" style="display:none;" valor="' + data[i].itemkg + '" fila="' + (i+1) + '"/>' +
				'<input type="text" name="itemkg[]" id="itemkg' + (i+1) + '" class="form-control" value="' + data[i].itemkg + '" style="display:none;"/>' +
			'</td>' +
			'<td name="descuentoTD' + (i+1) + '" id="descuentoTD' + (i+1) + '" style="text-align:right;display:none;">' +
				'0%' +
			'</td>' +
			'<td style="text-align:right;display:none;">' + 
				'<input type="text" name="descuento[]" id="descuento' + (i+1) + '" class="form-control" value="0" style="display:none;"/>' +
			'</td>' +
			'<td style="text-align:right;display:none;">' +
				'<input type="text" name="descuentoval[]" id="descuentoval' + (i+1) + '" class="form-control" value="0" style="display:none;"/>' +
			'</td>' +
			'<td name="preciounitTD' + (i+1) + '" id="preciounitTD' + (i+1) + '" style="text-align:right;">' +
				MASKLA(data[i].prcitem,0) +
				'<input type="text" name="preciounit[]" id="preciounit' + (i+1) + '" class="form-control" value="' + data[i].prcitem +'" style="display:none;"/>' +
				'<input type="text" name="prcitem[]" id="prcitem' + (i+1) + '" class="form-control" value="' + data[i].prcitem + '" style="display:none;"/>' +
			'</td>' +
			'<td style="display:none;" name="precioxkiloTD' + (i+1) + '" id="precioxkiloTD' + (i+1) + '" style="text-align:right">' +
				data[i].precioxkilo +
			'</td>' +
			'<td style="text-align:right;display:none;">' +
				'<input type="text" name="precioxkilo[]" id="precioxkilo' + (i+1) + '" class="form-control" value="' + data[i].precioxkilo + '" style="display:none;"/>' +
			'</td>' +
			'<td style="text-align:right;display:none;">' +
				'<input type="text" name="precioxkiloreal[]" id="precioxkiloreal' + (i+1) + '" class="form-control" value="' + data[i].precioxkiloreal +'" style="display:none;"/>' +
			'</td>' +
			'<td name="subtotalFactDet' + (i+1) + '" id="subtotalFactDet' + (i+1) + '" class="subtotalFactDet" style="text-align:right">' +
				MASKLA(data[i].montoitem,0) +
				'<input type="text" name="subtotal[]" id="subtotal' + (i+1) + '" class="form-control" value="' + data[i].montoitem + '" style="display:none;"/>' +
				'<input type="text" name="montoitem[]" id="montoitem' + (i+1) + '" class="form-control" value="' + data[i].montoitem + '" style="display:none;"/>' +
			'</td>' +
			'<td name="subtotalSFTD' + (i+1) + '" id="subtotalSFTD' + (i+1) + '" class="subtotal" style="text-align:right;display:none;">' +
				data[i].montoitem +
			'</td>' +
			'<td name="accion' + (i+1) + '" id="accion' + (i+1) + '" style="text-align:center">' +
				'<a class="btn-accion-tabla btn-sm tooltipsC" onclick="delguiadespfactdet(' + data[i].nrodocto + ',' + (i+1) + ',' + data[i].id + ')" title="Eliminar Guia ' + data[i].nrodocto + '">' +
					'<span class="glyphicon glyphicon-erase" style="bottom: 0px;top: 2px;"></span>' +
				'</a>' +
			'</td>' +
		'</tr>';
		$('#tabla-data tbody').append(htmlTags);
	}
	totalizar();
}


function verGD(nrodocto){
	genpdfGD(nrodocto,"","");
}
$("#fchemis").change(function(){
	let aux_fecha = $(this).val();
	aux_fecha = aux_fecha.split("/").reverse().join("/");
	let f = new Date(aux_fecha);

	var dias = parseInt($("#plazopago").val()); // Número de días a agregar

	aux_fechad = sumarDias(f, dias);

	$("#fchvenc").val(fechaddmmaaaa(aux_fechad));

});


function blanquearDatos(){
	$("#razonsocial").val("");
	$("#telefono").val("");
	$("#email").val("");
	$("#direccion").val("");
	$("#comuna_nombre").val("");
	$("#provincia_nombre").val("");
	$("#direccioncot").val("");
	$("#cliente_id").val("")
	$("#contacto").val("");
	$("#region_id").val("");
	$("#provincia_id").val("");
	$("#comuna_id").val("");
	$("#comuna_idD").val("");
	$("#formapago_desc").val("");
	$("#plazopago").val("");
	//$("#fchemis").val("");
	$("#fchvenc").val("");
	$("#vendedor_id").val("");
	$("#centroeconomico_id").val("");
	$("#hep").val("");
	//$("#foliocontrol_id").val("");
	$("#obs").val("");
	$('.select2').trigger('change');
	$('#tabla-data tbody').html("");
	$("#lblocnv_id").html("OC");
	$("#ocnv_id").val("");
	$("#ocnv_id").attr("disabled", true);
	$("#oc_id").attr("disabled", true);
	$("#notaventa_id").val("");
	$('#group_oc_file').hide()
	totalizar();
}

$(".form-horizontal").on("submit", function(event){
	/*
	validarItemVacios();
	event.preventDefault();
	swal({
		title: '¿ Seguro desea continuar ?',
		text: "Esta acción no se puede deshacer!",
			icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			return true;
		}else{
			event.preventDefault();
		}
	});			
	*/

	if($("#imagen").val() ==""){
		$("#imagen").val($('#oc_file').val());
	}
	$('#group_oc_id').removeClass('has-error');
	$('#group_oc_file').removeClass('has-error');
	if($("#sucursal_id option:selected").attr('value') == 3){
		$('#oc_id').prop('required', false);
	}
	$("#oc_file-error").hide();
	$('#oc_fileaux').prop('required', false);
	aux_ocarchivo = $.trim($('#oc_file').val()) + $.trim($('#oc_file').attr("data-initial-preview"));
	//if (($('#oc_id').val().length == 0) && (($('#oc_file').val().length != 0) || ($('#oc_file').attr("data-initial-preview").length != 0))) {
	if ( (aux_ocarchivo.length != 0) && ($('#oc_id').val().length == 0) ) {
		alertify.error("El campo Nro OrdenCompra es requerido cuando Adjuntar OC está presente.");
		//$("#oc_id").addClass('has-error');
		$('#oc_id').prop('required', true);
		return false;
	}
	//if (($('#oc_id').val().length != 0) && (($('#oc_file').val().length == 0) && ($('#oc_file').attr("data-initial-preview").length == 0))) {
	if (($('#oc_id').val().length != 0) && (aux_ocarchivo.length == 0)) {
		alertify.error("El campo Adjuntar OC es requerido cuando Nro OrdenCompra está presente.");
		$("#oc_file-error").show();
		$("#group_oc_file").addClass('has-error');
		$('#oc_fileaux').prop('required', true);
		//$('#oc_file').prop('required', true);
		return false;
	}
});