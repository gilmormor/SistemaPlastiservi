$(document).ready(function () {
	Biblioteca.validacionGeneral('form-general');
	aux_obs = $("#aux_obs").val();
	$("#obs").val(aux_obs);
	$("#rut").focus();
	var dateToday = new Date(); 
	var date = new Date();
	var ultimoDia = new Date(date.getFullYear(), date.getMonth() + 1, 0);
	
	//$("#rut").numeric();
	$("#cantM").numeric();
	$("#precioM").numeric({decimalPlaces: 3});
	$(".numerico").numeric();
	//$( "#myModal" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$( "#myModalBusqueda" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$( "#myModalBuscarProd" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$(".modal-body label").css("margin-bottom", -2);
	$(".help-block").css("margin-top", -2);
	if($("#aux_fechaphp").val()!=''){
		$("#fechahora").val($("#aux_fechaphp").val());
	}

	aux_nfilas=parseInt($("#tabla-data >tbody >tr").length);
    //alert(aux_nfilas);
	$("#agregar_reg").click(function()
    {
        agregarFila(2);
    });
	fieldHTML = "<input type='hidden' name='itemAct' id='itemAct'>" //Creo input con campo itemAct=item actual
	$("#DivVerTodosProd").append(fieldHTML)

	iniciarFileinput();

	totalizar();
	aux_staprodxcli = true;
	aux_DivVerTodosProd = true;
	aux_DivchVerAcuTec = false;
});

function iniciarFileinput(){
	$('#oc_file').fileinput({
		language: 'es',
		allowedFileExtensions: ['jpg', 'jpeg', 'png', 'pdf'],
		maxFileSize: 400,
		initialPreview: [
			// PDF DATA
			'/storage/imagenes/notaventa/'+$("#imagen").val(),
		],
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
			if(funcion=='procesar'){
				if (respuesta.mensaje == "ok") {
                    $("#fila"+datatemp.nfila).remove();
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
                    swal({
						//title: 'Error',
						text: respuesta.mensaje,
						icon: 'error',
						buttons: {
							confirm: "Aceptar"
						},
					}).then((value) => {
					});
					//Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
				}
			}
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

$(".requeridos").keyup(function(){
	//alert($(this).parent().attr('class'));
	validacion($(this).prop('name'),$(this).attr('tipoval'));
});
$(".requeridos").change(function(){
	//alert($(this).parent().attr('class'));
	validacion($(this).prop('name'),$(this).attr('tipoval'));
});


// ===== Edición de kilos por ítem (mismo patrón de dteguiadesp, adaptado a filas dinámicas) =====

// Habilita o deshabilita el enlace de edición de kilos según categoriaprod.stakgguiadesp del producto seleccionado
function configurarKilosFila(item){
	let prodId = $("#producto_id" + item).val();
	let enlace = $("#aux_kilos" + item);
	let etiqueta = $("#aux_kiloslbl" + item);
	if(prodId == "" || prodId == null){
		// Producto no válido: ocultar enlace, limpiar kilos y quitar el campo requerido
		enlace.hide();
		etiqueta.show();
		enlace.html("0,00").attr('valor','0');
		etiqueta.html("0,00");
		enlace.removeAttr('data-prodid');
		$("#totalkilos" + item).val("").attr('valor','');
		$("#itemkg" + item).val("");
		$("#grupokilosreq" + item).remove();
		totalizarKilos();
		return;
	}
	// Si es el mismo producto ya configurado, conservar los kilos ingresados
	if(enlace.attr('data-prodid') == prodId){
		return;
	}
	enlace.attr('data-prodid', prodId);
	// Producto nuevo o cambiado: reiniciar kilos a 0
	enlace.html("0,00").attr('valor','0');
	etiqueta.html("0,00");
	$("#totalkilos" + item).val("").attr('valor','');
	$("#itemkg" + item).val("");
	// Quitar el campo requerido de una configuración anterior (si cambió el producto)
	$("#grupokilosreq" + item).remove();
	// arrayDatosProducto es la variable global que llena llenarDatosProd (general.js)
	if(typeof arrayDatosProducto !== 'undefined' && arrayDatosProducto['cont'] > 0 && arrayDatosProducto['id'] == prodId && arrayDatosProducto['stakgguiadesp'] == 1){
		enlace.show();
		etiqueta.hide();
		// Campo oculto requerido (mismo patrón de dteguiadesp): value vacío hasta que se ingresen kilos > 0,
		// la validación del formulario (ignore:"") bloquea el guardar mientras esté vacío
		enlace.after(
			'<div class="form-group" id="grupokilosreq' + item + '" style="margin-bottom:0;">' +
				'<label for="totalkilosreq' + item + '" class="control-label requerido" title="Kilos" style="display:none;">Kilos item ' + item + '</label>' +
				'<input type="text" name="totalkilosreq' + item + '" id="totalkilosreq' + item + '" class="form-control" value="" valor="" fila="' + item + '" style="display:none;" required/>' +
			'</div>'
		);
	}else{
		enlace.hide();
		etiqueta.show();
	}
	totalizarKilos();
}

// Suma los itemkg de todas las filas y actualiza el pie Total Kg
function totalizarKilos(){
	let total = 0;
	$("input[name='itemkg[]']").each(function(){
		let v = parseFloat($(this).val());
		if(!isNaN(v)){
			total += v;
		}
	});
	$("#totalkg").html(MASKLA(total,2));
	$("#totalkg").attr('valor', total);
}

// Clic en el enlace de kilos: abre el modal compartido generales/editarcamponum
// Binding delegado porque las filas se crean dinámicamente con agregarFila()
$(document).on('click', '.editarcampoNum', function(){
	quitarvalidacioneach();
	id = $(this).attr('fila');
	aux_valor = $(this).attr('valor');
	$("#auxeditcampoN").attr('aux_nomcampon',$(this).attr('nomcampo'));
	$("#auxeditcampoN").attr('fila_id',id);
	$("#auxeditcampoN").val(aux_valor.trim());
	$("#myModalEditarCampoNum").modal('show');
});

// Aceptar en el modal: valida y vuelca el valor a los campos de la fila (visible + ocultos que viajan al backend)
$("#btnaceptarMN").click(function(event){
	event.preventDefault();
	if(verificarDato(".valorrequerido"))
	{
		id = $("#auxeditcampoN").attr('fila_id');
		if($("#auxeditcampoN").attr('aux_nomcampon') == "aux_kilos"){
			// Los kilos deben ser mayores a cero: no cierra el modal hasta ingresar un valor válido
			if(!(parseFloat($("#auxeditcampoN").val()) > 0)){
				alertify.error("Los kilos deben ser mayores a cero.");
				return;
			}
			$("#aux_kilos" + id).html($("#auxeditcampoN").val());
			$("#aux_kilos" + id).attr('valor', $("#auxeditcampoN").val());
			$("#totalkilos" + id).val($("#auxeditcampoN").val());
			$("#totalkilos" + id).attr('valor', $("#auxeditcampoN").val());
			// Llenar el campo requerido y revalidarlo para limpiar el error del formulario
			$("#totalkilosreq" + id).val($("#auxeditcampoN").val());
			$("#totalkilosreq" + id).valid();
			$("#itemkg" + id).val($("#auxeditcampoN").val());
			totalizarKilos();
		}
		$("#myModalEditarCampoNum").modal('hide');
	}else{
		alertify.error("Falta incluir informacion");
	}
});

// Validación del valor del modal (misma de dteguiadesp)
function verificarDato(aux_nomclass)
{
	aux_resultado = true;
	$(aux_nomclass).serializeArray().map(function(x){
		aux_tipoval = $("#" + x.name).attr('tipoval');
		if (validacion(x.name,aux_tipoval) == false)
		{
			aux_resultado = false;
		}
	});
	return aux_resultado;
}
// ===== Fin edición de kilos por ítem =====

$("#btnbuscarcliente").click(function(event){
    $("#rut").val("");
	if (!tablaClienteInicializada) {
		configTablaCliente(); // ← AQUÍ recién se inicializa
		tablaClienteInicializada = true;
	}
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
	$("#oc_file").fileinput('clear');
	codigo = $("#rut").val();
	//limpiarCampos();
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
				modulo_id : 13,
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
							$("#giro").val(respuesta.cliente[0].giro);
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
							$("#ids").val("1");
							agregarFila();
							
							$('#additem').show();
							cargardatospantprod();
						
							$(".selectpicker").selectpicker('refresh');
						}else{
							swal({
								title: 'Condición financiera en revisión.',
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
	$('#additem').hide();
	//$("#rut").val(aux_rut);
})


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
	$("#giro").val("");
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
	$("#ot").val("");
	$("#obs").val("");
	$("#oc_id").val("");
	$('.select2').trigger('change');
	$('#tabla-data tbody').html("");
	totalizar();
	totalizarKilos(); // Recalcular Total Kg al limpiar la tabla
}


function agregarFila() {
	//aux_num=parseInt($("#tabla-data >tbody >tr").length);
	aux_nroitem=parseInt($("#tabla-data >tbody >tr").length) + 1;
    aux_num=parseInt($("#ids").val());
    //alert(aux_num);
    aux_num = aux_num + 1;
    let aux_nfila = aux_num;
    $("#ids").val(aux_nfila);
	//'<input type="text" name="unmditem[]" id="unmditem' + aux_nfila + '" class="form-control" value=""  maxlength="4"/>' +
    var htmlTags = '<tr name="fila' + aux_nfila + '" id="fila' + aux_nfila + '" class="proditems" item="' + aux_nfila + '">' +
		'<td id="nroitem' + aux_nfila + '" name="nroitem' + aux_nfila + '" class="nroitem" style="text-align:center">' +
			aux_nroitem +
		'</td>' +
		'<td style="text-align:center" name="producto_idTD' + aux_nfila + '" id="producto_idTD' + aux_nfila + '" >' +
			'<input type="text" name="vlrcodigo[]" id="vlrcodigo' + aux_nfila + '" onblur="onBlurProducto_id(this)" class="form-control numerico itemrequerido vlrcodigo" value="" maxlength="4" onkeyup="buscarProdKeyUp(this,event)" style="text-align:right" item="' + aux_nfila +'" title="Código Producto"/>' +
			'<input type="text" name="producto_id[]" id="producto_id' + aux_nfila + '" class="form-control numerico" value="" maxlength="4" onkeyup="buscarProdKeyUp(this,event)" style="text-align:right;display:none;"/>' +
			'<input type="text" name="nrolindet[]" id="nrolindet' + aux_nfila + '" class="form-control" value="' + aux_nroitem + '" style="display:none;"/>' +
		'</td>' +
		'<td name="cantTD' + aux_nfila + '" id="cantTD' + aux_nfila + '" style="text-align:right" class="subtotalcant" valor="">' +
			'<input type="text" name="qtyitem[]" id="qtyitem' + aux_nfila + '" class="form-control numerico calsubtotalitem itemrequerido" value="" valor="0" valorini="" item="' + aux_nfila + '"style="text-align:right" title="Cantidad producto"/>' +
		'</td>' +
		'<td name="unidadmedida_nombre' + aux_nfila + '" id="unidadmedida_nombre' + aux_nfila + '" valor="">' +
			'<select id="unmditem' + aux_nfila + '" name="unmditem[]" class="form-control select2 itemrequerido" title="Unidad Medida">' +
				$("#unidadmedida_id").html() +
			'</select>' +
		'</td>' +
		'<td name="nombreProdTDDir' + aux_nfila + '" id="nombreProdTDDir' + aux_nfila + '" valor="">' +
			'<textarea name="nmbitem[]" id="nmbitem' + aux_nfila + '" class="form-control itemrequerido" value="" title="Nombre Producto" maxlength="500"></textarea>' +
			'<input type="text" name="dscitem[]" id="dscitem' + aux_nfila + '" class="form-control" value="" style="display:none;"/>' +
		'</td>' +
		'<td style="text-align:right;" class="subtotalkg" valor="0">' +
			// Enlace editable de kilos (mismo patrón de dteguiadesp); se habilita en configurarKilosFila() si la categoría tiene stakgguiadesp=1
			'<span id="aux_kiloslbl' + aux_nfila + '" style="display:none;">0,00</span>' +
			'<a id="aux_kilos' + aux_nfila + '" name="aux_kilos' + aux_nfila + '" class="btn-accion-tabla btn-sm editarcampoNum tooltipsC" title="Editar Kilos" valor="0" fila="' + aux_nfila + '" tipocampo="numerico" nomcampo="aux_kilos" style="display:none;">0,00</a>' +
			'<input type="text" name="totalkilos[]" id="totalkilos' + aux_nfila + '" class="form-control" value="" style="display:none;" valor="" fila="' + aux_nfila + '"/>' +
			'<input type="text" name="itemkg[]" id="itemkg' + aux_nfila + '" class="form-control" value="" style="display:none;"/>' +
		'</td>' +
		'<td name="descuentoTD' + aux_nfila + '" id="descuentoTD' + aux_nfila + '" style="text-align:right;display:none;">' +
			'0%' +
		'</td>' +
		'<td style="text-align:right;display:none;">' + 
			'<input type="text" name="descuento[]" id="descuento' + aux_nfila + '" class="form-control" value="0" style="display:none;"/>' +
		'</td>' +
		'<td style="text-align:right;display:none;">' +
			'<input type="text" name="descuentoval[]" id="descuentoval' + aux_nfila + '" class="form-control" value="0" style="display:none;"/>' +
		'</td>' +
		'<td name="preciounitTD' + aux_nfila + '" id="preciounitTD' + aux_nfila + '" style="text-align:right;">' +
			'<input type="text" name="prcitem[]" id="prcitem' + aux_nfila + '" class="form-control numerico calsubtotalitem itemrequerido" value="" valor="" valorini="" item="' + aux_nfila + '" style="text-align:right" title="Precio Unitario"/>' +
		'</td>' +
		'<td style="display:none;" name="precioxkiloTD' + aux_nfila + '" id="precioxkiloTD' + aux_nfila + '" style="text-align:right">' +
		'</td>' +
		'<td style="text-align:right;display:none;">' +
			'<input type="text" name="precioxkilo[]" id="precioxkilo' + aux_nfila + '" class="form-control" value="0" style="display:none;"/>' +
		'</td>' +
		'<td style="text-align:right;display:none;">' +
			'<input type="text" name="precioxkiloreal[]" id="precioxkiloreal' + aux_nfila + '" class="form-control" value="0" style="display:none;"/>' +
		'</td>' +
		'<td name="subtotalFactDet' + aux_nfila + '" id="subtotalFactDet' + aux_nfila + '" class="subtotalFactDet" style="text-align:right">' +
			'<input type="text" name="montoitem[]" id="montoitem' + aux_nfila + '" class="form-control numerico calpreciounit" value="0" valor="" valorini="" item="' + aux_nfila + '" style="text-align:right" readonly/>' +
		'</td>' +
		'<td name="subtotalSFTD' + aux_nfila + '" id="subtotalSFTD' + aux_nfila + '" class="subtotal" style="text-align:right;display:none;">' +
			'0' +
		'</td>' +
		'<td style="vertical-align:middle;">' + 
			'<a onclick="delitem('+ aux_nfila +')" class="btn-accion-tabla tooltipsC" title="Precio Unitario" id="delitem'+ aux_nfila + '" name="delitem'+ aux_nfila + '">'+
				'<i class="fa fa-fw fa-trash text-danger">'+
			'</a>'+
		'</td>'+
	'</tr>';
    $('#tabla-data tbody').append(htmlTags);
	activarClases();
	totalizar();
	$("#vlrcodigo" + aux_nfila).focus();
	$("unmditem" + aux_nfila).select2({
		tags: true
	  });
	validarItemVacios();
	//$('.select2').trigger('change')
}

function agregarEliminar(fila){
    aux_nfila=parseInt($("#tabla-data >tbody >tr").length);
    if(aux_nfila>=1){
        aux_valorboton = $("#agregar_reg"+fila).attr("data-original-title");
        if(aux_valorboton=='Eliminar'){
            $("#agregar_reg"+fila).attr("data-original-title", "");
            $("#agregar_reg"+fila).children('i').removeClass("fa-minus");
            //$("#agregar_reg"+fila).removeClass("tooltipsC");
            $("#cla_stadel"+fila).val(1);
            //$("#fila" + fila).fadeOut(2000);
            $("#fila" + fila).remove();
			totalizar();
            return 0;
        }
        $("#agregar_reg"+fila).children('i').removeClass("fa-plus");
        $("#agregar_reg"+fila).children('i').addClass("fa-minus");
        $("#agregar_reg"+fila).attr("data-original-title", "Eliminar");
        $("#agregar_reg"+fila).attr("title", "Eliminar");
		$("#itemcompletos").val(""); //AL AGREGAR ITEM ASIGNO "" PARA VALIDAR QUE ESTA VACIO EL ITEN INSERTADO
        agregarFila(fila);
    }
}

function calsubtotalitem(name){
	let i = $(name).attr("item");
	let qtyitem = $("#qtyitem" + i).val() == "" ? 0 : parseFloat($("#qtyitem" + i).val());
	let prcitem = $("#prcitem" + i).val() == "" ? 0 : parseFloat($("#prcitem" + i).val());

	if(qtyitem == 0){
		$("#qtyitem" + i).val("");
	}
	if(prcitem == 0){
		$("#prcitem" + i).val("");
	}

	let aux_subtotal = qtyitem * prcitem;
	aux_subtotal = Math.round(aux_subtotal);
	$("#qtyitem" + i).attr("valor",$("#qtyitem" + i).val());
	$("#cantTD" + i).attr("valor",$("#qtyitem" + i).val());
	$("#prcitem" + i).attr("valor",$("#prcitem" + i).val());
	$("#montoitem" + i).val(aux_subtotal);
	$("#montoitem" + i).attr("valor",aux_subtotal);
	$("#subtotalSFTD" + i).html(aux_subtotal);
	totalizar();
}

function activarClases(){
	$(".numerico").numeric();
	$(".calsubtotalitem").keyup(function(){
		calsubtotalitem(this)
	});
	$(".itemrequerido").change(function(){
		validarItemVacios();
	});

	validarItemVacios();
}

async function copiar_codprod(id,codintprod){
	//$("#myModalBuscarProd").modal('hide');
	//$("#myModal").modal('show');
	$('#myModalBuscarProd').modal('hide');
	let itemAct = $("#itemAct").val();
	//$("#producto_id" + itemAct).val(id);
	$("#vlrcodigo" + itemAct).val(id);
	//$("#vlrcodigo" + itemAct).blur();
	$("#qtyitem" + itemAct).focus();
	$("#qtyitem" + itemAct).select();
	// await: esperar la respuesta del producto para luego configurar la edición de kilos según stakgguiadesp
	await llenarDatosProd($("#vlrcodigo" + itemAct));// buscarDatosProd($("#vlrcodigo" + itemAct));
	configurarKilosFila(itemAct);
	//console.log(arrayDatosProducto);
	//$("#cantM").focus();
}

async function onBlurProducto_id(vlrcodigo){
	objvlrcodigo = $("#" + vlrcodigo["id"]);
	// await: esperar la respuesta del producto para luego configurar la edición de kilos según stakgguiadesp
	await llenarDatosProd(objvlrcodigo);
	configurarKilosFila(objvlrcodigo.attr("item"));
	//console.log(vlrcodigo["id"]);
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
	// El input del modal de kilos (#auxeditcampoN) es required y la validación del form lo revisa aunque esté oculto:
	// se le asigna un valor dummy para que no bloquee el guardar (mismo workaround de dteguiadesp)
	$("#auxeditcampoN").val("1");

	activarClases();
	validarItemVacios();

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

function procesar(id){
    var data = {
        dte_id : id,
        nfila  : id,
        updated_at : $("#updated_at" + id).html(),
        _token: $('input[name=_token]').val()
    };
    var ruta = '/dteguiadespdir/procesar';
    //var ruta = '/guiadesp/dteguiadesp';
    swal({
        title: '¿ Procesar DTE Factura ?',
        text: "Esta acción no se puede deshacer!",
        icon: 'warning',
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
        if (value) {
            ajaxRequest(data,ruta,'procesar');
        }
    });

}

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

function delitem(fila){
	swal({
        title: '¿ Eliminar item: ' + $("#nroitem" + fila).html() + '?',
        text: "Esta acción no se puede deshacer!",
        icon: 'warning',
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
		if(value){
			$("#cla_stadel"+fila).val(1);
			$("#fila" + fila).remove();
			let cont = 1;
			$(".nroitem").each(function() {
				$(this).html(cont++);
			});
			validarItemVacios();
			totalizar();
			totalizarKilos(); // Recalcular Total Kg al eliminar un ítem
		}
	});
}