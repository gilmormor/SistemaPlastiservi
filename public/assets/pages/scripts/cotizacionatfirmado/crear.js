$(document).ready(function () {
	Biblioteca.validacionGeneral('form-general');
	$('.tablas').DataTable({
		'paging'      : true, 
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		'autoWidth'   : false,
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
			"url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
	});
	/*
	$('.form-group').css({'margin-bottom':'0px','margin-left': '0px','margin-right': '0px'});
	$('.table').css({'margin-bottom':'0px','padding-top': '0px','padding-bottom': '0px'});
	$(".box-body").css({'padding-top': '5px','padding-bottom': '0px'});
	$(".box").css({'margin-bottom': '0px'});
	$(".box-header").css({'padding-bottom': '5px'});
	$("#mdialTamanio").css({'width': '50% !important'});
	$(".control-label").css({'padding-top': '2px'});
	*/
	/*
    var styles = {
		backgroundColor : "#ddd",
		fontWeight: ""
	  };
	$( this ).css( styles );*/

	aux_sta = $("#aux_sta").val();
	//$("#rut").numeric();
	/*
	$('#rut').on('input', function () { 
		this.value = this.value.replace(/[^0-9]/g,'');
	});
	*/
	$("#cantM").numeric();
	$("#precioM").numeric({decimalPlaces: 3});
	$(".numerico").numeric();
	$( "#myModal" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$( "#myModalBusqueda" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$( "#myModalBuscarProd" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$( "#myModalClienteTemp" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$(".modal-body label").css("margin-bottom", -2);
	$(".help-block").css("margin-top", -2);
	if($("#aux_fechaphp").val()!=''){
		$("#fechahora").val($("#aux_fechaphp").val());
	}
	//alert($("#aux_sta").val());
/*
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
*/

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


	$("#VerAcuTec").change(function(){
		cargardatospantprod();
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

	if(aux_sta==2){
		totalizar();
	}

	$("#btnguardaraprob").click(function(event){
		$("#myModalaprobcot").modal('show');
	});
	//alert('3'+$("#vendedor_id").val()+'3');
	if($("#vendedor_id").val() == '0'){
		$("#vendedor_idD").removeAttr("disabled");
		$("#vendedor_idD").removeAttr("readonly");
		$("#vendedor_idD").val("");
	}
	formato_rut($('#rut'));
/*
	const $ventanaModal = $("#myModalAcuerdoTecnico");
	$ventanaModal.on("hidden.bs.modal", function(event){
		const formulario = $ventanaModal.find("form");
		console.log(formulario);
		//formulario.prevObject[0].reset();
	});
*/
	
	const $ventanaModal = $("#myModalAcuerdoTecnico");

	$ventanaModal.on("hidden.bs.modal", function(event){
		//document.getElementById('form-acuerdotecnico').reset();
		//$("#form-acuerdotecnico").trigger('reset'); 
		/*const formulario = $ventanaModal.find("form");
		console.log(formulario);
		formulario[0].reset();*/
		$(".form_acutec").serializeArray().map(function(x){
			$("#" + x.name).val("");
		});
	});
	//configTablaProd();

	// Función para manejar la consulta y el click del botón
	function procesarProducto(codigo) {
		return new Promise((resolve) => {
			// Asignar el valor de cada codigo a #producto_idM
			$("#producto_idM").val(codigo);

			// Ejecutar el evento blur y esperar a que termine el AJAX
			$("#producto_idM").blur();

			// Escuchar el fin de la consulta AJAX en blur
			$(document).one('ajaxComplete', function(event, xhr, settings) {
				if (settings.url === '/producto/buscarUnProducto') {
					// Después de que la consulta finalice, resolver la promesa
					resolve();
				}
			});
		});
	}


	// Procesar cada código de manera secuencial
	async function procesarCodigos(codigos) {
		let array_producto_ids = [];
		// Recorrer cada elemento con la clase .filaproducto_id
		$('.filaproducto_id').each(function() {
			// Extraer el contenido HTML del elemento y agregarlo al array
			let producto_id = $(this).html().trim(); // .trim() elimina espacios en blanco
			array_producto_ids.push(producto_id);
		});
	
		for (const codigo of codigos) {
			if (!array_producto_ids.includes(codigo)){
				//await procesarProducto(codigo);
				llenatPantallaPrecios();
				// Ejecutar el evento click del botón #btnGuardarM después de que termine la consulta
				aux_precio = $("#preciosm").val()
				$("#cantM").val(1);
				$("#precioM").val(aux_precio);
				$("#precioxkilorealM").val($("#precioM").val())
				totalizarItem(0);
				/* $("#precionetoM").val(1000);
				$("#cantM").keyup(); */
				$("#btnGuardarM").click();	
			}
		}
	}
});


function ajaxRequest(data,url,funcion) {
	datatemp = data;
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='eliminar'){
				if (respuesta.mensaje == "ok" || data['id']=='0') {
					$("#fila"+data['nfila']).remove();
					Biblioteca.notificaciones('El registro fue eliminado correctamente', 'Plastiservi', 'success');
					totalizar();
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
			if(funcion=='aprobarcotsup'){
				if (respuesta.mensaje == "ok") {
					Biblioteca.notificaciones('El registro fue actualizado correctamente', 'Plastiservi', 'success');
					// *** REDIRECCIONA A UNA RUTA*** 
					var loc = window.location;
					if($("#aprobstatus").val()== "2"){
						window.location = loc.protocol+"//"+loc.hostname+"/cotizacionaprobar";
					}
					if($("#aprobstatus").val()== "5"){
						window.location = loc.protocol+"//"+loc.hostname+"/cotizacionaprobaracutec";
					}
					// ****************************** 
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no fue actualizado.', 'Plastiservi', 'error');
					}else{
						if (respuesta && respuesta.id !== undefined && respuesta.id !== null) {
							if(respuesta.id == 0){
								Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', 'error');
								var loc = window.location;
								aux_paginaredirect = $("#aux_paginaredirect").val();
								window.location = loc.protocol+"//"+loc.hostname+"/" + aux_paginaredirect;
							}
						}
						if($("#aprobstatus").val()== "5"){
							if(respuesta.id !== undefined && respuesta.id == 0){
								swal({
									title: respuesta.mensaje + ": " + respuesta.at.at_desc,
									text: "Ver Acuerdo Técnico?",
									icon: 'warning',
									buttons: {
										cancel: "No",
										confirm: "Si"
									},
								}).then((value) => {
									if (value) {
										genpdfAcuTec(respuesta.at.id,$("#cliente_id").val())
									}
								});	
							}else{
								Biblioteca.notificaciones('El registro no puso se actualizado, hay recursos usandolo', 'Plastiservi', 'error');
							}	
						}
					}
				}
			}
			if(funcion=='buscardetcot'){
				//console.log(respuesta);
			}
			if(funcion=='buscaratxcampos'){
				if(respuesta.length > 0){
					//console.log(respuesta);
					/*
					Swal.fire({
						icon: 'error',
						title: 'Oops...',
						text: 'Something went wrong!',
						footer: '<a href="">Why do I have this issue?</a>'
					  })*/
					swal({
						title: 'Acuerdo técnico ya existe',
						text: 'Producto Cod: ' + respuesta[0].producto_id + ', ' + respuesta[0].producto_nombre,
						icon: 'warning',
						buttons: {
							cancel: "Cerrar",
							confirm: "Ver AT"
						},
						}).then((value) => {
							if(value){
								genpdfAcuTec(respuesta[0].id,$("#cliente_id").val(),1);
							}
							/*
							fila = $(this).closest("tr");
							form = $(this);
							id = fila.find('td:eq(0)').text();
							//alert(id);
							var data = {
								_token  : $('input[name=_token]').val(),
								id      : id
							};
							if (value) {
								ajaxRequest(data,form.attr('href')+'/'+id+'/anular','anular',form);
							}*/
						});
				}else{
					$("#myModalAcuerdoTecnico").modal('hide');
					$("#acuerdotecnico" + datatemp.nfila).val(datatemp.objtxt); //ACTUALIZO EN LA TABLA EL VALOR DEL CAMPO ACUERDO TECNICO
					//alert($("#acuerdotecnico" + i).val());
					$("#icoat" + datatemp.nfila).attr('class','fa fa-cog text-aqua');
					$("#nombreProdTD" + datatemp.nfila).html($("#at_desc").val());
					$("#diamextmmTD" + datatemp.nfila).html($("#at_ancho").val());
					$("#ancho" + datatemp.nfila).val($("#at_ancho").val());
					$("#longTD" + datatemp.nfila).html($("#at_largo").val());
					$("#espesorTD" + datatemp.nfila).html($("#at_espesor").val());
					$("#cla_nombreTD" + datatemp.nfila).html($("#at_claseprod_id option:selected").html());
					$("#unidadmedida_nomnreTD" + datatemp.nfila).html($("#at_unidadmedida_id option:selected").html());
					$("#unidadmedida_id" + datatemp.nfila).val($("#at_unidadmedida_id option:selected").val());
					//console.log($("#at_unidadmedida_id option:selected").html());
					//console.log($("#at_unidadmedida_id option:selected").val());
					//$("#editarRegistro" + datatemp.nfila).hide();
					//console.log($("#at_impreso").val());
					if($("#at_impreso").val() == 1){
						$("#divMostrarImagenat" + datatemp.nfila).css({'display':'inline'});
					}else{
						$("#divMostrarImagenat" + datatemp.nfila).css({'display':'none'});
						$("#at_imagen" + datatemp.nfila).val("");
						$("#imagen" + datatemp.nfila).val("");
					}
				}
			}
		},
		error: function () {
		}
	});
}




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
	//$("#clientedirec_id").prop("disabled",false);
	$("#observacion").prop("disabled",false);
	$("#observacion").prop("readonly",false);
	$("#lugarentrega").prop("disabled",false);
	$("#lugarentrega").prop("readonly",false);	
}

function desactivar_controles(){
	//$("#clientedirec_id").prop("disabled",true);
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

	//$("#clientedirec_id option").remove();

	$("#direccioncot").val('');
	$("#formapago_id").val('');
	$("#formapago_idD").val('');
	$("#plazopago_id").val('');
	$("#plazopago_idD").val('');
	$("#giro_id").val('');
	$("#giro_idD").val('');
	$("#giro").val('');
	
	$("#contacto").val('');
	$("#region_id").val('');
	$("#provincia_id").val('');
	//$("#usuario_id").val('');
	$("#neto").val('');
	$("#iva").val('');
	$("#total").val('');
	totalizar();
}


$('#comunap_idCTM').on('change', function () {
	$("#regionp_idCTM").val($('#comunap_idCTM option:selected').attr("region_id"));
	$("#provinciap_idCTM").val($('#comunap_idCTM option:selected').attr("provincia_id"));
	$(".selectpicker").selectpicker('refresh');
});

$("#btnGuardarCTM").click(function(event)
{
    event.preventDefault();
	if(verificarclientetemp())
	{
		asignarvalorclientetemp();
		$("#myModalClienteTemp").modal('hide');
	}else{
		alertify.error("Falta incluir informacion");
	}
});

function verificarclientetemp()
{
	var v1=true,v2=true,v3=true,v4=true,v5=true,v6=true,v7=true,v8=true,v9=true,v10=true,v11=true,v12=true,v13=true,v14=true,v15=true;
	v16=validacion('sucursal_idCTM','combobox');
	v15= true; //validacion('finanzastelefonoCTM','numerico');
	v14= true; //validacion('finanzanemailCTM','email');
	v13= true; //validacion('finanzascontactoCTM','texto');
	v12= true; //validacion('contactotelefCTM','numerico');
	v11= true; //validacion('contactoemailCTM','email');
	v10=validacion('contactonombreCTM','texto');
	v9=validacion('comunap_idCTM','combobox');
	v8= true; //validacion('plazopago_idCTM','combobox');
	v7= true; //validacion('formapago_idCTM','combobox');
	v6= true; //validacion('giroCTM','texto');
	v5= true; //validacion('giro_idCTM','combobox');
	v4=validacion('emailCTM','email');
	v3=validacion('telefonoCTM','numerico');
	v2=validacion('direccionCTM','texto');
	v1=validacion('razonsocialCTM','texto');

	if (v1===false || v2===false || v3===false || v4===false || v5===false || v6===false || v7===false || v8===false || v9===false || v10===false || v11===false || v12===false || v13===false || v14===false || v15===false || v16===false)
	{
		//$("#exito").hide();
		//$("#error").show();
		return false;
	}else{
		//$("#error").hide();
		//$("#exito").show();
		return true;
	}
}


function asignarvalorclientetemp(){
	$("#razonsocial").val($('#razonsocialCTM').val());
	$("#direccion").val($('#direccionCTM').val());
	$("#direccioncot").val($('#direccionCTM').val());
	$("#telefono").val($('#telefonoCTM').val());
	$("#email").val($('#emailCTM').val());
	//$("#clientetemp_id").val($('#razonsocialCTM').val())
	//$("#giro_id").val($('#giro_idCTM').val());
	//$("#giro_idD").val($('#giro_idCTM').val());
	$("#giro_id").val(1);
	$("#giro_idD").val(1);
	//$("#giro").val($('#giroCTM').val());
	//$("#formapago_id").val($('#formapago_idCTM').val());
	//$("#formapago_idD").val($('#formapago_idCTM').val());
	//$("#plazopago_id").val($('#plazopago_idCTM').val());
	//$("#plazopago_idD").val($('#plazopago_idCTM').val());
	$("#formapago_id").val(1);
	$("#formapago_idD").val(1);
	$("#plazopago_id").val(1);
	$("#plazopago_idD").val(1);
	$("#comuna_id").val($('#comunap_idCTM').val());
	$("#comuna_idD").val($('#comunap_idCTM').val());
	$("#provincia_id").val($('#provinciap_idCTM').val());
	$("#region_id").val($('#regionp_idCTM').val());

	$("#contacto").val($('#contactonombreCTM').val());
	$("#sucursal_id").val($('#sucursal_idCTM').val());
	
	//$("#observacion").val($("#observacionesCTM").val())

	//$("#comuna_idD option[value='"+ respuesta[0]['comunap_id'] +"']").attr("selected",true);
	//$("#clientedirec_id option").remove();
	activar_controles();
	$('.select2').trigger('change');
	$(".selectpicker").selectpicker('refresh');
}


function limpiarclientemp(){
	$("#razonsocialCTM").val('');
	$("#direccionCTM").val('');
	$("#telefonoCTM").val('');
	$("#emailCTM").val('');
	$("#giro_idCTM").val('');
	$("#formapago_idCTM").val('');
	$("#plazopago_idCTM").val('');
	$("#comunap_idCTM").val('');
	$("#provinciap_idCTM").val('');
	$("#regionp_idCTM").val('');
	$("#contactonombreCTM").val('');
	$("#contactoemailCTM").val('');
	$("#contactotelefCTM").val('');
	$("#finanzascontactoCTM").val('');
	$("#finanzanemailCTM").val('');
	$("#finanzastelefonoCTM").val('');
	$("#sucursal_idCTM").val('');
	$("#observacionesCTM").val('');
	$("#regionp_idCTM").val($('#comunap_idCTM option:selected').attr("region_id"));
	$("#provinciap_idCTM").val($('#comunap_idCTM option:selected').attr("provincia_id"));
	$(".selectpicker").selectpicker('refresh');
}


$("#btnAceptarAcuTecTemp").click(function(event)
{
	event.preventDefault();
	if(verificarDato(".valorrequerido"))
	{
		var data = {};
		$(".form_acutec").serializeArray().map(function(x){data[x.name] = x.value;});
		arrayat_certificados = $("#at_certificados").val();
		data.at_certificados = arrayat_certificados.toString();
		//console.log(data);
		localStorage.setItem('datos', JSON.stringify(data));
		var guardado = localStorage.getItem('datos');
		aux_nfila = $("#aux_numfilaAT").val();
		data.objtxt = guardado;
		data.nfila = aux_nfila;
		data._token = $('input[name=_token]').val();


		var ruta = '/acuerdotecnico/buscaratxcampos';
		ajaxRequest(data,ruta,'buscaratxcampos');
	
	}else{
		alertify.error("Falta incluir informacion");
	}
});


function verificarDato(aux_nomclass)
{
	aux_resultado = true;
	$(aux_nomclass).serializeArray().map(function(x){
		aux_tipoval = $("#" + x.name).attr('tipoval');
		if (validacion(x.name,aux_tipoval) == false)
		{
			//return false;
			aux_resultado = false;

		}else{
			//return true;
		}
	});
	$(aux_nomclass).each(function(){
		if(($(this).attr('name') != undefined) && Array.isArray($(this).val()) ){
			aux_array = $(this).val();
			if(aux_array.length == 0){
				aux_name = $(this).attr('name');
				aux_tipoval = $(this).attr('tipoval');
				if (validacion(aux_name,aux_tipoval) == false)
				{
					aux_resultado = false;		
				}				
			}
		}
	});
	return aux_resultado;
}

$(".valorrequerido").keyup(function(){
	//alert($(this).parent().attr('class'));
	//console.log($(this).prop('min'));
	validacion($(this).prop('name'),$(this).attr('tipoval'));
});

$(".valorrequerido").change(function(){
	//alert($(this).parent().attr('class'));
	validacion($(this).prop('name'),$(this).attr('tipoval'));
});

$(".form-horizontal").on("submit", function(event){
	var aux_nfila = $("#tabla-data tbody tr").length - 3;
	//aux_nfila++;
	$("#itemcompletos").val("1");
	let j=0;
	for (i = 1; i <= aux_nfila; i++) {
		//alert($("#acuerdotecnico" + i).val());
		//console.log($("#acuerdotecnico" + i).val());

		if($("#tipoprod" + i).val() != undefined){
			j++;
		}
		/* if($("#tipoprod" + i).val() == 1){
			if($("#acuerdotecnico" + i).val() == 0 || $("#acuerdotecnico" + i).val() == "null"  || $("#acuerdotecnico" + i).val() == ""){
				event.preventDefault();
				//alertify.error("Falta acuerdo tecnico Item N°: " + i);
				i = aux_nfila+1;
				$("#lblitemcompletos").html("Acuerdo técnico item:" + j);
				$("#itemcompletos").val("");
				break;
			}
			let acuerdotecnico = JSON.parse($("#acuerdotecnico" + i).val());
			if(acuerdotecnico.at_impreso == 1){
				let at_imagen = $("#at_imagen" + i).val();
				if($("#imagen" + i).val() == ""){
					if(at_imagen == 0 || at_imagen == "null" || at_imagen == null || at_imagen == ""){
						$("#lblitemcompletos").html("Arte Acuerdo técnico item:" + j);
						$("#itemcompletos").val("");
						break;
					}	
				}
			}
		} */
		/* let at_imagen = $("#at_imagen" + i).val();
		if($("#imagen" + i).val() == ""){
			if(at_imagen == 0 || at_imagen == "null" || at_imagen == null || at_imagen == ""){
				$("#lblitemcompletos").html("Arte Acuerdo técnico item:" + j);
				$("#itemcompletos").val("");
				break;
			}	
		} */
	}
	//console.log($("#acuerdotecnico").val());
	//alert('prueba');
});

function ObsItemCot($id,$i){
	var data = {
		id: $id,
		nfila : $i,
		_token: $('input[name=_token]').val()
	};

    $.ajax({
        url: '/cotizacion/buscardetcot',
        type: 'POST',
        data: data,
        success: function (respuesta) {
			aux_obs = "";
			if(respuesta.obs != null){
				aux_obs = respuesta.obs;
			}
			//var texto = prompt("Observacion:",aux_obs);
			let input = document.createElement("input");
			input.value = aux_obs;
			input.type = 'text';
			input.className = 'swal-content__input';
		
			swal({
				text: "Editar Observación item",
				content: input,
				buttons: {
					cancel: "Cancelar",
					confirm: "Aceptar"
				},
			}).then((value) => {
				if (value) {
					var data = {
						id: $id,
						nfila : $i,
						obs : input.value,
						_token: $('input[name=_token]').val()
					};
				
					$.ajax({
						url: '/cotizacion/updateobsdet',
						type: 'POST',
						data: data,
						success: function (respuesta) {
							//console.log(respuesta.obs);
						}
					});
	
				}
			});
		}
    });
}

$("#at_ancho").blur(function(event){
	$("#at_anchodesv").val(desvAnchoLargo($("#at_ancho").val()));
});

$("#at_largo").blur(function(event){
	$("#at_largodesv").val(desvAnchoLargo($("#at_largo").val()));
});

$("#at_espesor").blur(function(event){
	aux_valor = $("#at_espesor").val();
	aux_desc = $("#at_materiaprima_id option:selected").attr('desc');
	$("#at_espesordesv").val(desvEspesor(aux_valor,aux_desc));
});


function desvAnchoLargo(aux_valor){
	aux_desv = "";
	if(aux_valor > 0){
		switch(true) {
			case aux_valor <= 50:
				aux_desv = "±1 CM";
				break;
			case aux_valor > 50 && aux_valor <= 150:
				aux_desv = "±2 CM";
				break;
			default:
				aux_desv = "±3 CM";
				break;
		}	
	}
	return aux_desv;
}

function desvEspesor(aux_valor,aux_desc){
	aux_desv = "";
	if(aux_valor > 0){
		if(aux_desc == "Baja" || aux_desc == "Mezcla" || aux_desc == "PP"){
			switch(true) {
				case aux_valor >= 0.010 && aux_valor <= 0.040:
					aux_desv = "±2 µ";
					break;
				case aux_valor >= 0.041 && aux_valor <= 0.080:
					aux_desv = "±3 µ";
					break;
				case aux_valor >= 0.081 && aux_valor <= 0.090:
					aux_desv = "±4 µ";
					break;
				case aux_valor >= 0.091 && aux_valor <= 0.140:
					aux_desv = "±5 µ";
					break;
				//case aux_valor >= 0.141 && aux_valor <= 0.200:
				case aux_valor >= 0.141:
					aux_desv = "±7 µ";
					break;
			}		
		}else{
			switch(true) {
				case aux_valor >= 0.010 && aux_valor <= 0.013:
					aux_desv = "±1 µ";
					break;
				case aux_valor >= 0.014 && aux_valor <= 0.018:
					aux_desv = "±2 µ";
					break;
				case aux_valor >= 0.019 && aux_valor <= 0.030:
					aux_desv = "±3 µ";
					break;
				case aux_valor >= 0.031 && aux_valor <= 0.050:
					aux_desv = "±4 µ";
					break;
				case aux_valor >= 0.051:
					aux_desv = "±5 µ";
					break;
				}
		}
	}
	return aux_desv;

}

function iniciarFileinput(aux_nfila){
	$('#at_imagen' + aux_nfila).fileinput({
		language: 'es',
		allowedFileExtensions: ['jpg', 'jpeg', 'png', 'pdf'],
		maxFileSize: 400,
		initialPreview: [
			// PDF DATA
			//'/storage/imagenes/notaventa/'+$("#imagen").val(),
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
		$('#at_imagen' + aux_nfila).attr("data-initial-preview","");
		$("#imagen" + aux_nfila).val("");
		//alert('entro');
	}).on('fileimageloaded', function(e, params) {
		//console.log('Paso');
		//console.log('File uploaded params', params);
		//console.log($('#at_imagen' + aux_nfila).val());
		$("#imagen" + aux_nfila).val($('#at_imagen' + aux_nfila).val());
	});
}

function activarClases(aux_nfila){
	iniciarFileinput(aux_nfila);
}


function ocultarMostrarFiltro(aux_nfila){
	if($('#div_at_imagen'+ aux_nfila).css('display') == 'none'){
		//$('#botonD').attr("class", "glyphicon glyphicon-chevron-up");
		$('#botonD').attr("title", "Ocultar Filtros");
		$('#btnmostrarocultar' + aux_nfila).removeClass('fa-plus').addClass('fa-minus');
		$('#verat_firmado' + aux_nfila).hide();
		
		iniciarFileinput(aux_nfila);
	}else{
		//$('#botonD').attr("class", "glyphicon glyphicon-chevron-down");
		$('#botonD').attr("title", "Mostrar Filtros");
		$('#btnmostrarocultar' + aux_nfila).removeClass('fa-minus').addClass('fa-plus');
		$('#verat_firmado' + aux_nfila).show();
	}
	$('#div_at_imagen'+ aux_nfila).slideToggle(500);
	if($("#aux_aprocot").val() != "0"){
		$(".file-caption-main").hide();		
	}
	$(".kv-file-remove").hide();
}

/* function embalajePlastiservi(){
	let aux_val = $("#at_embalajeplastservi").val();
	if(aux_val == "1" || aux_val == ""){
		$(".embalaje").prop("disabled", true);
		$(".embalaje").val("")
	}else{
		$(".embalaje").prop("disabled", false);
	}
} */

function arrayAcuerdoTecnico(){
	var aux_nfila = $("#tabla-data tbody tr").length - 3;
	//aux_nfila++;
	var miArray = [];
	$("#itemcompletos").val("1");
	for (i = 1; i <= aux_nfila; i++) {
		if($("#tipoprod" + i).val() == 1){
			if($("#acuerdotecnico" + i).val() == 0 || $("#acuerdotecnico" + i).val() == "null"  || $("#acuerdotecnico" + i).val() == ""){
				i = aux_nfila+1;
				$("#lblitemcompletos").html("Acuerdo técnico item:" + j);
				$("#itemcompletos").val("");
				break;
			}
			let acuerdotecnico = JSON.parse($("#acuerdotecnico" + i).val());
			// Añadir el objeto al array
			miArray.push(acuerdotecnico);
		}
	}
	return miArray;
}

