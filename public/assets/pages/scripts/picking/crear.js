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
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
	});

	$("#mdialTamanio").css({'width': '50% !important'});

	formato_rut($('#rut'));
	aux_sta = $("#aux_sta").val();
	//$("#rut").numeric();
	$("#cantM").numeric();
	$("#precioM").numeric({decimalPlaces: 3});
	$(".numerico").numeric({ negative : false });
	$( "#myModal" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$( "#myModalBusqueda" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$( "#myModalBuscarProd" ).draggable({opacity: 0.35, handle: ".modal-header"});
	//$(".modal-body label").css("margin-bottom", -2);
	//$(".help-block").css("margin-top", -2);
	if($("#aux_fechaphp").val()!=''){
		$("#fechahora").val($("#aux_fechaphp").val());
	}

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
		totalizarItem(0);
	});

	$("#descuentoM").change(function(){
		totalizarItem(1);
		//$("#cantM").change();
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
		$("#vendedor_id").val($("#vendedor_idD").val());
	});

	i = 1;
    $(".proditems").each(function()
    {
        //console.log($(this).attr('id'));
		total = 0;
		$("#tabla-bod tr .bod" + i).each(function() {
			if($(this).val() == ""){
				valor = "0";
			}else{
				valor = $(this).val() ;
			}
			valorNum = parseFloat(valor);
			total += valorNum;
			aux_sumabod = $(this).attr("onkeyup");
			var F=new Function (aux_sumabod);
			F();
			return false;
		});
		//$("#cantord" + i).val(total);
		//console.log(total);
		i++;
    });
	elibodsob();

});

function insertarTabla(){
	$("#trneto").remove();
	$("#triva").remove();
	$("#trtotal").remove();
	var aux_nfila = $("#tabla-data tbody tr").length;
	aux_nfila++;
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
			'<td name="despachosoldet_id'+ aux_nfila + '" id="despachosoldet_id'+ aux_nfila + '">'+ 
				'0'+
			'</td>'+
			'<td style="display:none;">'+
				'<input type="text" name="NVdet_id[]" id="NVdet_id'+ aux_nfila + '" class="form-control" value="0" style="display:none;"/>'+
			'</td>'+
			'<td name="producto_idTD'+ aux_nfila + '" id="producto_idTD'+ aux_nfila + '" style="display:none;">'+ 
				'<input type="text" name="producto_id[]" id="producto_id'+ aux_nfila + '" class="form-control" value="'+ $("#producto_idM").val() +'" style="display:none;"/>'+
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
				MASKLA($("#precionetoM").attr("valor"),3)+ //MASK(0, $("#precionetoM").attr("valor"), '-##,###,##0.00',1)+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="preciounit[]" id="preciounit'+ aux_nfila + '" class="form-control" value="'+ $("#precionetoM").attr("valor") +'" style="display:none;"/>'+
			'</td>'+
			'<td name="precioxkiloTD'+ aux_nfila + '" id="precioxkiloTD'+ aux_nfila + '" style="text-align:right">'+ 
				MASKLA(aux_precioxkilo,3)+ //MASK(0, aux_precioxkilo, '-##,###,##0.00',1)+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="precioxkilo[]" id="precioxkilo'+ aux_nfila + '" class="form-control" value="'+ aux_precioxkilo +'" style="display:none;"/>'+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="precioxkiloreal[]" id="precioxkiloreal'+ aux_nfila + '" class="form-control" value="'+ aux_precioxkiloreal +'" style="display:none;"/>'+
			'</td>'+
			'<td name="totalkilosTD'+ aux_nfila + '" id="totalkilosTD'+ aux_nfila + '" style="text-align:right" valor="' + $("#totalkilosM").attr("valor") +'">'+ 
				MASKLA($("#totalkilosM").attr("valor"),2)+ //MASK(0, $("#totalkilosM").attr("valor"), '-##,###,##0.00',1)+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="totalkilos[]" id="totalkilos'+ aux_nfila + '" class="form-control" value="'+ $("#totalkilosM").attr("valor") +'" style="display:none;"/>'+
			'</td>'+
			'<td name="subtotalCFTD'+ aux_nfila + '" id="subtotalCFTD'+ aux_nfila + '" class="subtotalCF" style="text-align:right">'+ 
				MASKLA($("#subtotalM").attr("valor"),0)+ //MASK(0, $("#subtotalM").attr("valor"), '-#,###,###,##0.00',1)+
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
		id: $("#despachosoldet_id"+i).html(),
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
	$("#rut").prop("disabled",true);
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
	$("#clientedirec_id").prop('disabled', false);
	$("#plazoentrega").prop('disabled', false);
	$("#lugarentrega").prop('disabled', false);
	$("#tipoentrega_id").prop('disabled', false);
    //Rest of code
})

function actSaldo(i){
	aux_cantord = $.trim($("#cantord" + i).val());
	$("#cantord" + i).val(aux_cantord);
	aux_cantTD = $.trim($("#cantTD" + i).html());
	cantorddespF = $.trim($("#cantorddespF" + i).html());
	aux_saldo = aux_cantTD - cantorddespF - aux_cantord;
	$("#saldocantF" + i).html(aux_saldo);
	$("#cantorddesp" + i).val($("#cantord" + i).val());
	if(aux_cantord.length == 0 || aux_cantord == "" || aux_cantord == null){
		$("#llenarCantOrd" + i).prop("checked", false);
	}else{
		$("#llenarCantOrd" + i).prop("checked", true);
	}
	aux_totalkilosTD = aux_cantord * $("#peso" + i).val();
	
	if($("#peso" + i).val() == 0){
		//alert(aux_cantord);
		aux_subtotalCFTD = Math.round(aux_cantord * $("#preciounit" + i).val());
	}else{
		aux_subtotalCFTD = Math.round(aux_cantord * $("#preciounit" + i).val());
	}
	//aux_subtotalCFTD = aux_cantord * $("#peso" + i).val() * $("#precioxkilo" + i).val();
	$("#totalkilosTD" + i).attr("valor",aux_totalkilosTD);
	$("#totalkilosTD" + i).html(MASKLA(aux_totalkilosTD,2)); //$("#totalkilosTD" + i).html(MASK(0, aux_totalkilosTD, '-##,###,##0.00',1));
	$("#subtotalCFTD" + i).html(MASKLA(aux_subtotalCFTD,0)); //$("#subtotalCFTD" + i).html(MASK(0, aux_subtotalCFTD, '-#,###,###,##0.00',1));
	$("#subtotalSFTD" + i).html(aux_subtotalCFTD);
	
	//alert(aux_saldo);
	if(aux_saldo < 0){
		aux_cantTD = $.trim($("#cantTD" + i).html());
		cantorddespF = $.trim($("#cantorddespF" + i).html());
		aux_saldo = aux_cantTD - cantorddespF;
		$("#cantord" + i ).val(aux_saldo);
		$("#saldocantF" + i).html('0');
		aux_cant = parseFloat($("#cant" + i).val());
		aux_cantorddesp = parseFloat($("#cantorddespF" + i).html());
		$("#cantord" + i).val(aux_saldo);
		
		aux_totalkilosTD = aux_saldo * $("#peso" + i).val();
		aux_subtotalCFTD = Math.round(aux_saldo * $("#preciounit" + i).val());
		$("#totalkilosTD" + i).attr('valor',aux_totalkilosTD);
		$("#totalkilosTD" + i).html(MASKLA(aux_totalkilosTD,2)); //$("#totalkilosTD" + i).html(MASK(0, aux_totalkilosTD, '-##,###,##0.00',1));
		$("#subtotalCFTD" + i).html(MASKLA(aux_subtotalCFTD,0)); //$("#subtotalCFTD" + i).html(MASK(0, aux_subtotalCFTD, '-#,###,###,##0.00',1));
		$("#subtotalSFTD" + i).html(aux_subtotalCFTD);
	}
	//console.log($("#cantord" + i).val());
	sumcant();
	totalizardespacho();
}

function llenarCantOrd(i){
	saldo = $("#saldocantF" + i).html();
	estaSeleccionado = $("#llenarCantOrd" + i).is(":checked");
	if (estaSeleccionado){
		$("#cantord" + i).val($.trim(saldo));
	}else{
		$("#cantord" + i).val('');
	}
	actSaldo(i);
	sumcant();
}

$("#marcarTodo").change(function() {
	estaSeleccionado = $("#marcarTodo").is(":checked");
	sumarcant(estaSeleccionado);
	totalizardespacho();
});

function sumarcant(estaSeleccionado){
	nFilas = $("#tabla-data tr").length - 4;
	$("#cantordTotal").val('');
	for (var i = 1; i <= nFilas; i++) {
		aux_cantTD = $.trim($("#cantTD" + i).html());
		cantorddespF = $.trim($("#cantorddespF" + i).html());
		aux_saldo = aux_cantTD - cantorddespF;
		if (estaSeleccionado){
			$("#cantord" + i).val($.trim(aux_saldo));
		}else{
			$("#cantord" + i).val('');
		}
		$("#llenarCantOrd" + i).prop("checked", estaSeleccionado);
		actSaldo(i);
	}
	sumcant();
}

function sumcant(){
	aux_total = 0;
	$("#tabla-data tr .cantordsum").each(function() {
		valor = $(this).val();
		valorNum = parseFloat(valor);
		if(isNaN( valorNum )){
			valorNum = 0;
		}
		aux_total += valorNum;
	});
	if(aux_total==0){
		$("#cantordTotal").val('');
	}else{
		$("#cantordTotal").val(aux_total);
	}
	
}

function elibodsob(){ //ELIMINAR BODEGAS SOBRANTES
	aux_total = 0;
	//return 0;
	$("#tabla-data tr .cantordsum").each(function() {
		fila = $(this).attr('fila');
		aux_CsaldocantF = $("#saldocantF" + fila).html();
		aux_saldocantF = parseFloat(aux_CsaldocantF);
		cant_id = $(this).attr('id');
		//console.log(cant_id);
		valor = $(this).val();
		aux_valorcant = parseFloat(valor);
		aux_staelibod = false;
		$("#tabla-data tr ." + cant_id).each(function() {
			aux_numfilabod = $(this).attr('filabod');
			aux_stockcantTD = $("#stockcantTD" + aux_numfilabod).html();
			aux_stockcantTD = parseFloat(aux_stockcantTD);
			if(aux_stockcantTD <= 0){
				$("#invcant" + aux_numfilabod).val('');
				$("#invcant" + aux_numfilabod).attr("readonly","readonly");
				if($(this).attr('nomabrbod') == "SolDe"){
					$("#fbod" + aux_numfilabod).remove();
				}
			}
			/*
			if($(this).attr('nomabrbod') == "SolDe"){
				valor = $(this).val();
				aux_valorbod = parseFloat(valor);
				if((aux_valorcant <= aux_valorbod) && (aux_valorbod > 0) && (aux_saldocantF == 0)){
					aux_staelibod = true;
				}
				if(aux_valorbod == 0){
					$("#fila" + $(this).attr('filabod')).remove();
				}
			}
			*/
		});
		/*
		if(aux_staelibod){
			$("#tabla-data tr ." + cant_id).each(function() {
				if($(this).attr('nomabrbod') != "SolDe"){
					$("#fila" + $(this).attr('filabod')).remove();
				}
			});

		}
		*/
		//console.log(aux_staelibod);
	});	
}