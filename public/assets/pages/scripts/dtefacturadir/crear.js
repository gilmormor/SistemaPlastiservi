$(document).ready(function () {
	Biblioteca.validacionGeneral('form-general');
	aux_obs = $("#aux_obs").val();
	$("#obs").val(aux_obs);

	var dateToday = new Date(); 
	var date = new Date();
	var ultimoDia = new Date(date.getFullYear(), date.getMonth() + 1, 0);
	
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

	aux_nfilas=parseInt($("#tabla-data >tbody >tr").length);
    //alert(aux_nfilas);
	$("#agregar_reg").click(function()
    {
        agregarFila(2);
    });
	fieldHTML = "<input type='hidden' name='itemAct' id='itemAct'>" //Creo input con campo itemAct=item actual
	$("#DivVerTodosProd").append(fieldHTML)
	totalizar();

});

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
							$("#ids").val("0");
							agregarFila();
							
							$('#botonNewGuia').show();
						
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
	$("#obs").val("");
	$('.select2').trigger('change');
	$('#tabla-data tbody').html("");
	totalizar();
}


function agregarFila() {
	aux_num=parseInt($("#tabla-data >tbody >tr").length);
    aux_num=parseInt($("#ids").val());
    //alert(aux_num);
    aux_num = aux_num + 1;
    aux_nfila = aux_num;
    $("#ids").val(aux_nfila);
	aux_htmlunidades = '<select id="unmditem' + (aux_nfila) + '" name="unmditem[]" class="form-control select2 itemrequerido" required>' +
							$("#unidadmedida_id").html() +
						'</select>';
	//'<input type="text" name="unmditem[]" id="unmditem' + (aux_nfila) + '" class="form-control" value=""  maxlength="4"/>' +
    var htmlTags = '<tr name="fila' + (aux_nfila) + '" id="fila' + (aux_nfila) + '" class="proditems" item="' + aux_nfila + '">' +
		'<td style="text-align:center" style="display:none;">' +
			(aux_nfila) +
			'<input type="text" name="nrolindet[]" id="nrolindet' + (aux_nfila) + '" class="form-control" value="' + (aux_nfila) + '" style="display:none;"/>' +
		'</td>' +
		'<td style="text-align:center" name="producto_idTD' + (aux_nfila) + '" id="producto_idTD' + (aux_nfila) + '" >' +
			'<input type="text" name="vlrcodigo[]" id="vlrcodigo' + (aux_nfila) + '" onblur="onBlurProducto_id(this)" class="form-control numerico itemrequerido" value="" maxlength="4" onkeyup="buscarProd(this,event)" style="text-align:right" item="' + (aux_nfila) +'" required/>' +
			'<input type="text" name="producto_id[]" id="producto_id' + (aux_nfila) + '" class="form-control numerico" value="" maxlength="4" onkeyup="buscarProd(this,event)" style="text-align:right;display:none;"/>' +
		'</td>' +
		'<td name="cantTD' + (aux_nfila) + '" id="cantTD' + (aux_nfila) + '" style="text-align:right" class="subtotalcant" valor="">' +
			'<input type="text" name="qtyitem[]" id="qtyitem' + (aux_nfila) + '" class="form-control numerico calsubtotalitem itemrequerido" value="" valor="0" valorini="" item="' + (aux_nfila) + '"style="text-align:right" required/>' +
		'</td>' +
		'<td name="unidadmedida_nombre' + (aux_nfila) + '" id="unidadmedida_nombre' + (aux_nfila) + '" valor="">' +
			aux_htmlunidades +
		'</td>' +
		'<td name="nombreProdTD' + (aux_nfila) + '" id="nombreProdTD' + (aux_nfila) + '" valor="">' +
			'<input type="text" name="nmbitem[]" id="nmbitem' + (aux_nfila) + '" class="form-control itemrequerido" value="" required/>' +
			'<input type="text" name="dscitem[]" id="dscitem' + (aux_nfila) + '" class="form-control" value="" style="display:none;"/>' +
		'</td>' +
		'<td style="text-align:right;" class="subtotalkg" valor="0">' +
			'<input type="text" name="totalkilos[]" id="totalkilos' + (aux_nfila) + '" class="form-control" value="" style="display:none;" valor="" fila="' + (aux_nfila) + '"/>' +
			'<input type="text" name="itemkg[]" id="itemkg' + (aux_nfila) + '" class="form-control" value="" style="display:none;"/>' +
		'</td>' +
		'<td name="descuentoTD' + (aux_nfila) + '" id="descuentoTD' + (aux_nfila) + '" style="text-align:right;display:none;">' +
			'0%' +
		'</td>' +
		'<td style="text-align:right;display:none;">' + 
			'<input type="text" name="descuento[]" id="descuento' + (aux_nfila) + '" class="form-control" value="0" style="display:none;"/>' +
		'</td>' +
		'<td style="text-align:right;display:none;">' +
			'<input type="text" name="descuentoval[]" id="descuentoval' + (aux_nfila) + '" class="form-control" value="0" style="display:none;"/>' +
		'</td>' +
		'<td name="preciounitTD' + (aux_nfila) + '" id="preciounitTD' + (aux_nfila) + '" style="text-align:right;">' +
			'<input type="text" name="prcitem[]" id="prcitem' + (aux_nfila) + '" class="form-control numerico calsubtotalitem  itemrequerido" value="" valor="" valorini="" item="' + (aux_nfila) + '" style="text-align:right" required/>' +
		'</td>' +
		'<td style="display:none;" name="precioxkiloTD' + (aux_nfila) + '" id="precioxkiloTD' + (aux_nfila) + '" style="text-align:right">' +
		'</td>' +
		'<td style="text-align:right;display:none;">' +
			'<input type="text" name="precioxkilo[]" id="precioxkilo' + (aux_nfila) + '" class="form-control" value="0" style="display:none;"/>' +
		'</td>' +
		'<td style="text-align:right;display:none;">' +
			'<input type="text" name="precioxkiloreal[]" id="precioxkiloreal' + (aux_nfila) + '" class="form-control" value="0" style="display:none;"/>' +
		'</td>' +
		'<td name="subtotalFactDet' + (aux_nfila) + '" id="subtotalFactDet' + (aux_nfila) + '" class="subtotalFactDet" style="text-align:right">' +
			'<input type="text" name="montoitem[]" id="montoitem' + (aux_nfila) + '" class="form-control numerico calpreciounit" value="0" valor="" valorini="" item="' + (aux_nfila) + '" style="text-align:right" readonly/>' +
		'</td>' +
		'<td name="subtotalSFTD' + (aux_nfila) + '" id="subtotalSFTD' + (aux_nfila) + '" class="subtotal" style="text-align:right;display:none;">' +
			'0' +
		'</td>' +
		'<td style="vertical-align:middle;">' + 
			'<a onclick="agregarEliminar('+ aux_nfila +')" class="btn-accion-tabla" title="Agregar" data-original-title="Agregar" id="agregar_reg'+ aux_nfila + '" name="agregar_reg'+ aux_nfila + '" valor="fa-plus">'+
				'<i class="fa fa-fw fa-plus"></i>'+
			'</a>'+
		'</td>'+
	'</tr>';
    $('#tabla-data tbody').append(htmlTags);
	activarClases();
	totalizar();
	$("#vlrcodigo" + aux_nfila).focus();
	$(".select2").select2({
		tags: true
	  });
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
}

function buscarProd(obj,event){
	//console.log(obj.id)
	if(event.which==113){
		//console.log(obj);
		$(obj).val("");
		//console.log($(obj).parent().parent().attr("item"));
		cargardatospantprod();
		$("#itemAct").val($(obj).parent().parent().attr("item")); //Crear input Item actual
		$("#myModalBuscarProd").modal('show');
	}
}

function copiar_codprod(id,codintprod){
	//$("#myModalBuscarProd").modal('hide');
	//$("#myModal").modal('show');
	$('#myModalBuscarProd').modal('hide');
	let itemAct = $("#itemAct").val();
	//$("#producto_id" + itemAct).val(id);
	$("#vlrcodigo" + itemAct).val(id);
	//$("#vlrcodigo" + itemAct).blur();
	$("#qtyitem" + itemAct).focus();
	$("#qtyitem" + itemAct).select();
	llenarDatosProd($("#vlrcodigo" + itemAct));// buscarDatosProd($("#vlrcodigo" + itemAct));
	//console.log(arrayDatosProducto);
	//$("#cantM").focus();
}

function onBlurProducto_id(vlrcodigo){
	objvlrcodigo = $("#" + vlrcodigo["id"]);
	llenarDatosProd(objvlrcodigo);
	//console.log(vlrcodigo["id"]);
}

//FUNCTION CON ASYNC YA QUE ME INTERESA ESPERAR LA RESPUESTA DE LA BUSQUEDA
async function llenarDatosProd(vlrcodigo){
	item = vlrcodigo.attr("item");
	if($("#vlrcodigo" + item).val() != $("#producto_id" + item).val()){
		arrayDatosProducto = await buscarDatosProd(vlrcodigo);
		$("#producto_id" + item).val("");
		$("#nmbitem" + item).val("");
		$("#prcitem" + item).val("0");
		if(arrayDatosProducto['cont'] > 0){
			$("#producto_id" + item).val(arrayDatosProducto["id"]);
			$("#nmbitem" + item).val(arrayDatosProducto["nombre"]);
			$("#prcitem" + item).val(arrayDatosProducto["precio"]);		
		}
		calsubtotalitem($("#vlrcodigo" + item));	
	}
}


$(".form-horizontal").on("submit", function(event){
	$('.itemrequerido').each(function(){
		let id=$(this).attr("id");
		let valor=$(this).val();
		console.log('id: ' + id + 'Valor: ' + valor);
	  });
	//console.log($(".itemrequerido").val());
	event.preventDefault();
});
