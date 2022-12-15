$(document).ready(function () {
	Biblioteca.validacionGeneral('form-general');
	//$("#comuna_idD").html($("#comunax").val());
	//$(".selectpicker").selectpicker('refresh');
	//$(".select2").selectmenu('refresh', true);
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
	if(aux_sta==1){
		$( "#rut" ).focus();
	}else{
		$("#direccion").focus();
	}
	//$("#rut").numeric();
	/*
	$('#rut').on('input', function () { 
		this.value = this.value.replace(/[^0-9]/g,'');
	});
	*/
	$("#cantM").numeric();
	$("#precioM").numeric({decimalPlaces: 2});
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
	$("#rut").keyup(function(event){
		if(event.which==113){
			$(this).val("");
			$(".input-sm").val('');
			$("#myModalBusqueda").modal('show');
		}
	});
	$("#btnbuscarcliente").click(function(event){
			var aux_nfila = $("#tabla-data tbody tr").length - 3;
			if(aux_nfila>0){
				swal({
					title: '¿ Desea eliminar productos pre-cargados ?',
					text: "Al cambiar de cliente se eliminaran los productos pre-cargados!",
					icon: 'warning',
					buttons: {
						cancel: "Cancelar",
						confirm: "Aceptar"
					},
				}).then((value) => {
					if (value) {
						for (i = 1; i <= aux_nfila; i++) {
							$("#fila" + i).remove();
						}
						totalizar();
						$("#rut").val("");
						$(".input-sm").val('');
						
						$('#tabla-data-clientes').DataTable().ajax.url( "clientebuscarpage/").load();

						$("#myModalBusqueda").modal('show');		
					}
				});
			}else{
				$("#rut").val("");
				$(".input-sm").val('');
				$("#myModalBusqueda").modal('show');	
			}
	});
	$("#producto_idM").keyup(function(event){
		if(event.which==113){
			$(this).val("");
			$(".input-sm").val('');
			$("#myModal").modal('hide');
			$("#myModalBuscarProd").modal('show');
		}
	});
	$("#btnbuscarproducto").click(function(event){
		
		$(this).val("");
		$(".input-sm").val('');
		data = datos();
		$('#tabla-data-productos').DataTable().ajax.url( "productobuscarpage/" + data.data2 + "&producto_id=" ).load();
	
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
	if($("#unidadmedida_idM option:selected").attr('value') == 7){
		aux_precioxkilo = $("#precioM").attr("valor");
		aux_precioxkiloreal = $("#precioM").attr("valor");
	}
	//alert($("#tipoprodM").attr('valor'));
	aux_botonAcuTec = '';
	if($("#tipoprodM").attr('valor') == 1) {
		aux_botonAcuTec = ' <a class="btn-accion-tabla tooltipsC" title="Acuerdo tecnico" onclick="crearEditarAcuTec('+ aux_nfila +')">'+
		'<i id="icoat' + aux_nfila + '" class="fa fa-cog text-red girarimagen"></i> </a>';
	}
	//aux_botonAcuTec = $("#tipoprodM").attr('valor') == '1' ? 'x' : '';

    var htmlTags = '<tr name="fila'+ aux_nfila + '" id="fila'+ aux_nfila + '">'+
			'<td name="producto_idTDT'+ aux_nfila + '" id="producto_idTDT'+ aux_nfila + '" style="text-align:center;">'+ 
				$("#producto_idM").val() + aux_botonAcuTec +
			'</td>'+
			'<td style="display:none;" name="cotdet_idTD'+ aux_nfila + '" id="cotdet_idTD'+ aux_nfila + '">'+ 
				'0'+
			'</td>'+
			'<td style="display:none;">'+
				'<input type="text" name="cotdet_id[]" id="cotdet_id'+ aux_nfila + '" class="form-control" value="0" style="display:none;"/>'+
			'</td>'+
			'<td name="producto_idTD'+ aux_nfila + '" id="producto_idTD'+ aux_nfila + '" style="display:none;">'+ 
				'<input type="text" name="producto_id[]" id="producto_id'+ aux_nfila + '" class="form-control" value="'+ $("#producto_idM").val() +'" style="display:none;"/>'+
			'</td>'+
			'<td style="display:none;" name="codintprodTD'+ aux_nfila + '" id="codintprodTD'+ aux_nfila + '">'+ 
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
				'<input type="text" name="unidadmedida_id[]" id="unidadmedida_id'+ aux_nfila + '" class="form-control" value="'+ $("#unidadmedida_idM option:selected").attr('value') + '" style="display:none;"/>'+
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
				'<input type="text" name="obs[]" id="obs'+ aux_nfila + '" class="form-control" value="'+ $("#obsM").val() +'" style="display:none;"/>'+
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
				MASKLA($("#precionetoM").attr("valor"),0) + //MASK(0, $("#precionetoM").attr("valor"), '-##,###,##0.00',1)+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="preciounit[]" id="preciounit'+ aux_nfila + '" class="form-control" value="'+ $("#precionetoM").attr("valor") +'" style="display:none;"/>'+
			'</td>'+
			'<td name="precioxkiloTD'+ aux_nfila + '" id="precioxkiloTD'+ aux_nfila + '" style="text-align:right">'+ 
				MASKLA(aux_precioxkilo,0) + //MASK(0, aux_precioxkilo, '-##,###,##0.00',1)+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="precioxkilo[]" id="precioxkilo'+ aux_nfila + '" class="form-control" value="'+ aux_precioxkilo +'" style="display:none;"/>'+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="precioxkiloreal[]" id="precioxkiloreal'+ aux_nfila + '" class="form-control" value="'+ aux_precioxkiloreal +'" style="display:none;"/>'+
			'</td>'+
			'<td name="totalkilosTD'+ aux_nfila + '" id="totalkilosTD'+ aux_nfila + '" style="text-align:right">'+ 
				MASKLA($("#totalkilosM").attr("valor"),2) + //MASK(0, $("#totalkilosM").attr("valor"), '-##,###,##0.00',1)+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="totalkilos[]" id="totalkilos'+ aux_nfila + '" class="form-control" value="'+ $("#totalkilosM").attr("valor") +'" style="display:none;"/>'+
			'</td>'+
			'<td name="subtotalCFTD'+ aux_nfila + '" id="subtotalCFTD'+ aux_nfila + '" class="subtotalCF" style="text-align:right">'+ 
				MASKLA($("#subtotalM").attr("valor"),0) + //MASK(0, $("#subtotalM").attr("valor"), '-#,###,###,##0.00',1)+
			'</td>'+
			'<td class="subtotalCF" style="text-align:right;display:none;">'+ 
				'<input type="text" name="subtotal[]" id="subtotal'+ aux_nfila + '" class="form-control" value="'+ $("#subtotalM").attr("valor") +'" style="display:none;"/>'+
			'</td>'+
			'<td name="subtotalSFTD'+ aux_nfila + '" id="subtotalSFTD'+ aux_nfila + '" class="subtotal" style="text-align:right;display:none;">'+ 
				$("#subtotalM").attr("valor")+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="acuerdotecnico[]" id="acuerdotecnico'+ aux_nfila + '" class="form-control" value="0" style="display:none;"/>'+
			'</td>'+
			'<td style="text-align:right;display:none;">'+
				'<input type="text" name="tipoprod[]" id="tipoprod'+ aux_nfila + '" class="form-control" value="' + $("#tipoprodM").attr('valor') + '" style="display:none;"/>'+
			'</td>'+
			'<td>' + 
				'<a class="btn-accion-tabla tooltipsC" title="Editar este registro" onclick="editarRegistro('+ aux_nfila +')">'+
				'<i class="fa fa-fw fa-pencil"></i>'+
				'</a>'+
				'<a class="btn-accion-tabla eliminar tooltipsC" title="Eliminar este registro" onclick="eliminarRegistro('+ aux_nfila +')">'+
				'<i class="fa fa-fw fa-trash text-danger"></i></a>'+
			'</td>'+
		'</tr>'+
		'<tr id="trneto" name="trneto">'+
			'<td colspan="13" style="text-align:right"><b>Neto</b></td>'+
			'<td id="tdneto" name="tdneto" style="text-align:right">0,00</td>'+
		'</tr>'+
		'<tr id="triva" name="triva">'+
			'<td colspan="13" style="text-align:right"><b>IVA ' + $("#aux_iva").val() + '%</b></td>'+
			'<td id="tdiva" name="tdiva" style="text-align:right">0,00</td>'+
		'</tr>'+
		'<tr id="trtotal" name="trtotal">'+
			'<td colspan="13" style="text-align:right"><b>Total</b></td>'+
			'<td id="tdtotal" name="tdtotal" style="text-align:right">0,00</td>'+
		'</tr>';
	
	$('#tabla-data tbody').append(htmlTags);
	totalizar();
}



$('#vendedor_idD').on('change', function () {
	$("#vendedor_id").val($("#vendedor_idD").val());
});


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
	event.preventDefault();
	var data = {
		id: $("#cotdet_idTD"+i).html(),
		nfila : i
	};
	var ruta = '/cotizacion/eliminarCotizacionDetalle/'+i;
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
			ajaxRequest(data,ruta,'eliminar');
		}
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
						Biblioteca.notificaciones('Registro no puso se actualizado.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no puso se actualizado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
			}
			if(funcion=='buscardetcot'){
				//console.log(respuesta);
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
	//$("#clientedirec_id").prop("disabled",true);
	eliminarFormatoRut($(this));
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
				//url: '/cliente/buscarCliId',
				url: '/cliente/buscarCliRut',
				type: 'POST',
				data: data,
				success: function (respuesta) {
					//console.log(respuesta.cliente[0].razonsocial);
					//console.log(respuesta.sucursales);
					if(respuesta.cliente.length>0){
						/*
						//VALIDACION CLIENTE BLOQUEADO DESABILITADA EL 17-05-2021 POR SOLICITUD DE CRISTIAN GORIGOITIA
						if(respuesta[0]['descripcion']==null){
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
						*/
			
						$("#razonsocial").val(respuesta.cliente[0].razonsocial);
						$("#telefono").val(respuesta.cliente[0].telefono);
						$("#email").val(respuesta.cliente[0].email);
						$("#direccion").val(respuesta.cliente[0].direccion);
						$("#direccioncot").val(respuesta.cliente[0].direccion);
						$("#cliente_id").val(respuesta.cliente[0].id)
						$("#contacto").val(respuesta.cliente[0].contactonombre);
						//$("#vendedor_id").val(respuesta[0]['vendedor_id']);
						//$("#vendedor_idD").val(respuesta[0]['vendedor_id']);
						$("#region_id").val(respuesta.cliente[0].regionp_id);
						//alert($("#region_id").val());
						$("#provincia_id").val(respuesta.cliente[0].provinciap_id);
						$("#giro_id").val(respuesta.cliente[0].giro_id);
						$("#giro_idD").val(respuesta.cliente[0].giro_id);
						$("#giro").val(respuesta.cliente[0].giro);
						$("#comuna_id").val(respuesta.cliente[0].comunap_id);
						$("#comuna_idD").val(respuesta.cliente[0].comunap_id);
						$("#provincia_id").val(respuesta.cliente[0].provinciap_id);
						$("#plazopago_id").val(respuesta.cliente[0].plazopago_id);
						$("#plazopago_idD").val(respuesta.cliente[0].plazopago_id);
						$("#formapago_id").val(respuesta.cliente[0].formapago_id);
						$("#formapago_idD").val(respuesta.cliente[0].formapago_id);

						$("#comuna_idD option[value='"+ respuesta.cliente[0].comunap_id +"']").attr("selected",true);
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
				
						//$("#comuna_idD option[value='101']").attr("selected",true);
/*
						$("#clientedirec_id option").remove();
						$("#sucursal_id option").remove();

						//alert(respuesta[i]['direcciondetalle']);
						$('#clientedirec_id').attr("required", false);
						$('#sucursal_id').attr("required", false);
						if(respuesta[0]['direcciondetalle']!=null){
							$("#clientedirec_id").prop("disabled",false);
							$("#clientedirec_id").prop("readonly",false);	
							//$('#lblclientedirec_id').attr("class", 'requerido');
							$('#clientedirec_id').attr("required", true);
							$("#clientedirec_id").append("<option value=''>Seleccione...</option>")


							$("#sucursal_id").prop("disabled",false);
							$("#sucursal_id").prop("readonly",false);	
							//$('#lblclientedirec_id').attr("class", 'requerido');
							$('#sucursal_id').attr("required", true);
							$("#sucursal_id").append("<option value=''>Seleccione...</option>")
							for(var i=0;i<respuesta.length;i++){
								//alert(respuesta[i]['direccion']);
								$("#clientedirec_id").append("<option provincia_id='" + respuesta[i]['provincia_id'] + "' region_id='" + respuesta[i]['region_id'] + "' comuna_id='" + respuesta[i]['comuna_id'] + "' formapago_id='" + respuesta[i]['formapago_id'] + "' plazopago_id='" + respuesta[i]['plazopago_id'] + "' value='" + respuesta[i]['direc_id'] + "'>" + respuesta[i]['direcciondetalle'] + "</option>")
								$("#sucursal_id").append("<option value='" + respuesta[i]['sucursal_id'] + "'>" + respuesta[i]['sucursalnombre'] + "</option>")
							}	
						}else{
							$("#clientedirec_id").prop("disabled",true);
							$("#clientedirec_id").prop("readonly",true);	
						}
						*/
						activar_controles();
						formato_rut($("#rut"));
						data = datos();
						$('#tabla-data-productos').DataTable().ajax.url( "productobuscarpage/" + data.data2 + "&producto_id=" ).load();
						$(".selectpicker").selectpicker('refresh');
					}else{
						$.ajax({
							url: '/cliente/buscarCli',
							type: 'POST',
							data: data,
							success: function (respuesta) {
								if(respuesta.length>0){
									swal({
										title: 'Cliente pertenece a otro Vendedor',
										text: respuesta.cliente[0].razonsocial,
										icon: 'error',
										buttons: {
											confirm: "Aceptar"
										},
									}).then((value) => {
										if (value) {
											//ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
											//$("#rut").val('');
											$("#rut").focus();
										}
									});
								}else{
									var data = {
										rut: $("#rut").val(),
										_token: $('input[name=_token]').val()
									};
									$.ajax({
										url: '/clientetemp/buscarCliTemp',
										type: 'POST',
										data: data,
										success: function (respuesta) {
											if(respuesta.length>0){
												if(respuesta[0]['vendedor_id']==$("#vendedor_id").val()){
													$("#razonsocialCTM").val(respuesta[0]['razonsocial']);
													$("#direccionCTM").val(respuesta[0]['direccion']);
													$("#telefonoCTM").val(respuesta[0]['telefono']);
													$("#emailCTM").val(respuesta[0]['email']);
													$("#giro_idCTM").val(respuesta[0]['giro_id']);
													$("#giro").val(respuesta[0]['giro']);
													$("#giroCTM").val(respuesta[0]['giro']);
													$("#formapago_idCTM").val(respuesta[0]['formapago_id']);
													$("#plazopago_idCTM").val(respuesta[0]['plazopago_id']);
													$("#comunap_idCTM").val(respuesta[0]['comunap_id']);
													$("#provinciap_idCTM").val(respuesta[0]['provinciap_id']);
													$("#regionp_idCTM").val(respuesta[0]['regionp_id']);
													$("#contactonombreCTM").val(respuesta[0]['contactonombre']);
													$("#contactoemailCTM").val(respuesta[0]['contactoemail']);
													$("#contactotelefCTM").val(respuesta[0]['contactotelef']);
													$("#finanzascontactoCTM").val(respuesta[0]['finanzascontacto']);
													$("#finanzanemailCTM").val(respuesta[0]['finanzanemail']);
													$("#finanzastelefonoCTM").val(respuesta[0]['finanzastelefono']);
													$("#sucursal_idCTM").val(respuesta[0]['sucursal_id']);
													$("#observacionesCTM").val(respuesta[0]['observaciones']);
													$("#regionp_idCTM").val($('#comunap_idCTM option:selected').attr("region_id"));
													$("#provinciap_idCTM").val($('#comunap_idCTM option:selected').attr("provincia_id"));
												
													$(".selectpicker").selectpicker('refresh');
													formato_rut($("#rut"));
													$("#myModalClienteTemp").modal('show');
												}else{
													swal({
														title: 'Cliente temporal pertenece a otro vendedor.',
														text: "",
														icon: 'error',
														buttons: {
															cancel: "Cerrar"
														},
													}).then((value) => {
														if (value) {
															
														}
													});
												}
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
	var ruta = '/cotizacion/aprobarcotsup/'+data['id'];
	swal({
		title: '¿ Está seguro que desea Aprobar la Cotización ?',
		text: "Esta acción no se puede deshacer!",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			ajaxRequest(data,ruta,'aprobarcotsup');
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
		var ruta = '/cotizacion/aprobarcotsup/'+data['id'];
		swal({
			title: '¿ Está seguro que desea Rechazar la Cotización ?',
			text: "Esta acción no se puede deshacer!",
			icon: 'warning',
			buttons: {
				cancel: "Cancelar",
				confirm: "Aceptar"
			},
		}).then((value) => {
			if (value) {
				ajaxRequest(data,ruta,'aprobarcotsup');
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
	v15=validacion('finanzastelefonoCTM','numerico');
	v14=validacion('finanzanemailCTM','email');
	v13=validacion('finanzascontactoCTM','texto');
	v12=validacion('contactotelefCTM','numerico');
	v11=validacion('contactoemailCTM','email');
	v10=validacion('contactonombreCTM','texto');
	v9=validacion('comunap_idCTM','combobox');
	v8=validacion('plazopago_idCTM','combobox');
	v7=validacion('formapago_idCTM','combobox');
	v6=validacion('giroCTM','texto');
	v5=validacion('giro_idCTM','combobox');
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
	$("#giro_id").val($('#giro_idCTM').val());
	$("#giro_idD").val($('#giro_idCTM').val());
	$("#giro").val($('#giroCTM').val());
	$("#formapago_id").val($('#formapago_idCTM').val());
	$("#formapago_idD").val($('#formapago_idCTM').val());
	$("#plazopago_id").val($('#plazopago_idCTM').val());
	$("#plazopago_idD").val($('#plazopago_idCTM').val());
	$("#comuna_id").val($('#comunap_idCTM').val());
	$("#comuna_idD").val($('#comunap_idCTM').val());
	$("#provincia_id").val($('#provinciap_idCTM').val());
	$("#region_id").val($('#regionp_idCTM').val());

	$("#contacto").val($('#contactonombreCTM').val());
	//$("#observacion").val($("#observacionesCTM").val())

	//$("#comuna_idD option[value='"+ respuesta[0]['comunap_id'] +"']").attr("selected",true);
	//$("#clientedirec_id option").remove();
	activar_controles();
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
		localStorage.setItem('datos', JSON.stringify(data));
		var guardado = localStorage.getItem('datos');
		aux_nfila = $("#aux_numfilaAT").val();
		$("#acuerdotecnico" + aux_nfila).val(guardado); //ACTUALIZO EN LA TABLA EL VALOR DEL CAMPO ACUERDO TECNICO
		$("#myModalAcuerdoTecnico").modal('hide');
		//alert($("#acuerdotecnico" + i).val());
		$("#icoat" + aux_nfila).attr('class','fa fa-cog text-aqua');
		//console.log(guardado);
	
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
	validacion($(this).prop('name'),$(this).attr('tipoval'));
});

$(".valorrequerido").change(function(){
	//alert($(this).parent().attr('class'));
	validacion($(this).prop('name'),$(this).attr('tipoval'));
});

$(".form-horizontal").on("submit", function(event){
	var aux_nfila = $("#tabla-data tbody tr").length - 3;
	//aux_nfila++;
	for (i = 1; i <= aux_nfila; i++) {
		//alert($("#acuerdotecnico" + i).val());
		//console.log($("#acuerdotecnico" + i).val());
		if($("#tipoprod" + i).val() == 1){
			if($("#acuerdotecnico" + i).val() == 0 || $("#acuerdotecnico" + i).val() == "null"  || $("#acuerdotecnico" + i).val() == ""){
				event.preventDefault();
				alertify.error("Falta acuerdo tecnico Item N°: " + i);
				i = aux_nfila+1;
			}	
		}
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