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
	aux_sta = $("#aux_sta").val();
	//$("#rut").numeric();
	$("#cantM").numeric();
	$("#precioM").numeric({decimalPlaces: 3});
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

	totalizarcantkg();

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
		validacion('producto_idM','textootro');
		if(event.which==113){
			$(this).val("");
			$(".input-sm").val('');
			$("#myModal").modal('hide');
			$("#myModalBuscarProd").modal('show');
		}
	});
	$("#btnbuscarproducto").click(function(e){
		//e.preventDefault();
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


	if($("#vendedor_id").val() == '0'){
		$("#vendedor_idD").removeAttr("disabled");
		$("#vendedor_idD").removeAttr("readonly");
		$("#vendedor_idD").val("");
	}
	$("#vendedor_idD").change(function(){
		//alert($("#vendedor_idD").val());
		$("#vendedor_id").val($("#vendedor_idD").val());
	});

	$(".kv-file-remove").hide();
	$(".file-drag-handle").hide();
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
	//codintprod = $("#codintprodM").val();
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
	if($("#unidadmedida_idM option:selected").attr('value') == 7){
		aux_precioxkilo = $("#precioM").attr("valor");
		aux_precioxkiloreal = $("#precioM").attr("valor");		
	}

    var htmlTags = '<tr name="fila'+ aux_nfila + '" id="fila'+ aux_nfila + '">'+
			'<td style="display:none;" name="NVdet_idTD'+ aux_nfila + '" id="NVdet_idTD'+ aux_nfila + '">'+ 
				'0'+
			'</td>'+
			'<td style="display:none;">'+
				'<input type="text" name="NVdet_id[]" id="NVdet_id'+ aux_nfila + '" class="form-control" value="0" style="display:none;"/>'+
			'</td>'+
			'<td  style="display:none;" name="producto_idTD'+ aux_nfila + '" id="producto_idTD'+ aux_nfila + '">'+ 
				'<input type="text" name="producto_id[]" id="producto_id'+ aux_nfila + '" class="form-control" value="'+ $("#producto_idM").val() +'" style="display:none;"/>'+
			'</td>'+
			'<td name="producto_idValor'+ aux_nfila + '" id="producto_idValor'+ aux_nfila + '">'+ 
				$("#producto_idM").val() +
			'</td>'+
			'<td name="nombreProdTD'+ aux_nfila + '" id="nombreProdTD'+ aux_nfila + '">'+ 
				aux_nombre+
			'</td>'+
			'<td style="display:none;">'+ 
				'<input type="text" name="unidadmedida_id[]" id="unidadmedida_id'+ aux_nfila + '" class="form-control" value="' + $("#unidadmedida_idM option:selected").attr('value') + '" style="display:none;"/>'+
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
				$("#espesor1M").attr('valor')+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="espesor[]" id="espesor'+ aux_nfila + '" class="form-control" value="'+ $("#espesor1M").attr('valor') +'" style="display:none;"/>'+
				'<input type="text" name="ancho[]" id="ancho'+ aux_nfila + '" class="form-control" value="'+ $("#anchoM").attr('valor') +'" style="display:none;"/>'+
			'</td>'+
			'<td name="longTD'+ aux_nfila + '" id="longTD'+ aux_nfila + '" style="text-align:right">'+ 
				$("#largoM").attr('valor')+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="long[]" id="long'+ aux_nfila + '" class="form-control" value="'+ $("#largoM").attr('valor') +'" style="display:none;"/>'+
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
			'<td name="invbodega_idTXT'+ aux_nfila + '" id="invbodega_idTXT'+ aux_nfila + '">'+ 
				$("#invbodega_idM option:selected").html()+
			'</td>'+
			'<td name="invmovtipo_idTXT'+ aux_nfila + '" id="invmovtipo_idTXT'+ aux_nfila + '">'+ 
				$("#invmovtipo_idM option:selected").html()+
			'</td>'+
			'<td name="totalkilosTD'+ aux_nfila + '" id="totalkilosTD'+ aux_nfila + '" style="text-align:right">'+ 
				MASKLA($("#totalkilosM").attr("valor"),2) + //MASK(0, $("#totalkilosM").attr("valor"), '-##,###,##0.00',1)+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="totalkilos[]" id="totalkilos'+ aux_nfila + '" class="form-control subtotalkg" value="'+ $("#totalkilosM").attr("valor") +'" style="display:none;" valor="'+ $("#totalkilosM").attr("valor") +'"/>'+
				'<input type="text" name="invbodega_idTD[]" id="invbodega_idTD'+ aux_nfila + '" class="form-control" value="'+ $("#invbodega_idM").val() +'" style="display:none;"/>'+
				'<input type="text" name="invmovtipo_idTD[]" id="invmovtipo_idTD'+ aux_nfila + '" class="form-control" value="'+ $("#invmovtipo_idM").val() +'" style="display:none;"/>'+
			'</td>'+
			'<td name="cantTD'+ aux_nfila + '" id="cantTD'+ aux_nfila + '" style="text-align:right" valor="' + $("#cantM").val() + '">'+ 
				MASKLA($("#cantM").val(),2) +
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="cant[]" id="cant'+ aux_nfila + '" class="form-control subtotalcant" value="'+ $("#cantM").val() +'" style="display:none;" valor="'+ $("#cantM").val() +'"/>'+
			'</td>'+
			'<td>' + 
				'<a href="#" class="btn-accion-tabla tooltipsC" title="Editar este registro" onclick="editarRegistro('+ aux_nfila +')">'+
				'<i class="fa fa-fw fa-pencil"></i>'+
				'</a>'+
				'<a href="#" class="btn-accion-tabla eliminar tooltipsC" title="Eliminar este registro" onclick="eliminarRegistro('+ aux_nfila +')">'+
				'<i class="fa fa-fw fa-trash text-danger"></i></a>'+
			'</td>'+
		'</tr>'+
		'<tr id="trtotal" name="trtotal">'+
			'<td colspan="10" style="text-align:right"><b>Total</b></td>'+
			'<td id="tdtotalkg" name="tdtotalkg" style="text-align:right"><b>0,00</b></td>'+
			'<td id="tdtotalcant" name="tdtotalcant" style="text-align:right"><b>0,00</b></td>'+
		'</tr>';
	
	$('#tabla-data tbody').append(htmlTags);
	totalizarcantkg();
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
	totalizarcantkg();
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
				//url: '/cliente/buscarCliId',
				url: '/cliente/buscarCliRut',
				type: 'POST',
				data: data,
				success: function (respuesta) {
					if(respuesta.cliente.length>0){
						//alert(respuesta[0]['vendedor_id']);
						if(respuesta.cliente[0].descripcion==null){
							$("#razonsocial").val(respuesta.cliente[0].razonsocial);
							$("#telefono").val(respuesta.cliente[0].telefono);
							$("#email").val(respuesta.cliente[0].email);
							$("#direccion").val(respuesta.cliente[0].direccion);
							$("#direccioncot").val(respuesta.cliente[0].direccion);
							$("#cliente_id").val(respuesta.cliente[0].id)
							$("#contacto").val(respuesta.cliente[0].contactonombre);
							/*
							$("#vendedor_id").val(respuesta[0]['vendedor_id']);
							$("#vendedor_idD").val(respuesta[0]['vendedor_id']);
							*/
							$("#region_id").val(respuesta.cliente[0].regionp_id);
							//alert($("#region_id").val());
							$("#provincia_id").val(respuesta.cliente[0].provinciap_id);
							$("#comuna_id").val(respuesta.cliente[0].comunap_id);
							$("#comuna_idD").val(respuesta.cliente[0].comunap_id);
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
	
	
							/*
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
							*/
							activar_controles();
	
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
$(".requeridos").change(function(){
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


$('#form-general').submit(function(event) {
	//event.preventDefault();
	//alert('prueba');
	//return 0;
	//console.log($("#oc_file").val());
	/*
	if($("#imagen").val() ==""){
		$("#imagen").val($('#oc_file').val());
	}
	console.log($("#imagen").val());
	$('#group_oc_id').removeClass('has-error');
	$('#group_oc_file').removeClass('has-error');
	$('#oc_id').prop('required', false);
	$("#oc_file-error").hide();
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
		//$('#oc_file').prop('required', true);
		return false;
	}
	$("#cotizacion_id").prop('disabled', false);
	$("#clientedirec_id").prop('disabled', false);
	$("#plazoentrega").prop('disabled', false);
	$("#lugarentrega").prop('disabled', false);
	$("#tipoentrega_id").prop('disabled', false);
	*/
    //Rest of code
})

/*
$(document).on('click','.fileinput-remove-button', function(){

    //your code here
	alert('entro');
	$('#oc_file').attr("data-initial-preview","");

 });
*/
$("#botonNewProd").click(function(event)
{
	event.preventDefault();
	limpiarInputOT();
	quitarverificar();
	$("#aux_sta").val('1');
	$("#invmovtipo_idM").val($("#invmovtipo_id").val());
	$("#myModal").modal('show');
	$("#direccionM").focus();
	$(".selectpicker").selectpicker('refresh');
});