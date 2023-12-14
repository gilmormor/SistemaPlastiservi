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
	$('.form-group').css({'margin-bottom':'0px','margin-left': '0px','margin-right': '0px'});
	$('.table').css({'margin-bottom':'0px','padding-top': '0px','padding-bottom': '0px'});
	$(".box-body").css({'padding-top': '5px','padding-bottom': '0px'});
	$(".box").css({'margin-bottom': '0px'});
	$(".box-header").css({'padding-bottom': '5px'});
	$("#mdialTamanio").css({'width': '50% !important'});
	$(".control-label").css({'padding-top': '2px'});
	
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
	$("#precioM").numeric();
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
	$("#btnbuscarproducto").click(function(event){
		$(this).val("");
		$(".input-sm").val('');
		$("#myModal").modal('hide');
		$("#myModalBuscarProd").modal('show');
	});


	$("#precioM").keyup(function(event){
		totalizarItem(0);
	});

	$('.datepicker').datepicker({
		language: "es",
		autoclose: true,
		clearBtn : true,
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

});

$("#botonNewProd").click(function(event)
{
	clientedirec_id = $("#clientedirec_id").val();
	aux_rut = $("#rut").val();
	if(aux_rut==""){
		mensaje('Debes Incluir RUT del cliente','','error');
		return 0;
	}else{
		event.preventDefault();
		limpiarInputOT();
		quitarverificar();
		$("#aux_sta").val('1');
		$("#myModal").modal('show');
		$("#direccionM").focus();	
	}
	/*
	if(clientedirec_id==""){
		mensaje('Debes seleccionar una dirección','','error');
		return 0;
	}
	
	if(clientedirec_id!="" && aux_rut!=""){
		event.preventDefault();
		limpiarInputOT();
		quitarverificar();
		$("#aux_sta").val('1');
		$("#myModal").modal('show');
		$("#direccionM").focus();	
	}
	*/
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
	alert('entro');
	/*
	$(".input-sm").val('');
	$("#myModal").modal('hide');
	$("#myModalBuscarProd").modal('show');
	*/
/*
	$('#myModal')
		   .modal('hide')
		   .on('hidden.bs.modal', function (e) {
			   $('#myModalBuscarProd').modal('show');

			   $(this).off('hidden.bs.modal'); // Remove the 'on' event binding
		   });
*/
});

$("#btnGuardarM").click(function(event)
{
	event.preventDefault();
	//alert('entro');
	if(verificar())
	{
		//alert($("#aux_sta").val());
		
		aux_precioxkilo = parseFloat($("#precioM").attr('valor')); //parseFloat($("#precioM").val());
		aux_precioxkiloreal = parseFloat($("#precioxkilorealM").val());
		if(aux_precioxkilo<aux_precioxkiloreal){
			swal({
				title: 'Precio menor al valor en tabla. Desea continuar?',
				text: "",
				icon: 'warning',
				buttons: {
					cancel: "Cancelar",
					confirm: "Aceptar"
				},
			}).then((value) => {
				if (value) {
					
					insertarModificar();
				}
			});
		}else{
			insertarModificar();
		}

	}else{
		alertify.error("Falta incluir informacion");
	}
});

function insertarModificar(){
	if($("#aux_sta").val()=="1"){
		insertarTabla();
	}else{
		modificarTabla($("#aux_numfila").val());
	}
	$("#myModal").modal('hide');
}


function modificarTabla(i){
	//alert($("#sucursal_idM").val());
	$("#aux_sta").val('0')
	$("#producto_id"+i).val($("#producto_idM").val());

	$("#codintprodTD"+i).html($("#codintprodM").val());
	$("#codintprod"+i).val($("#codintprodM").val());
	$("#cantTD"+i).html($("#cantM").val());
	$("#cant"+i).val($("#cantM").val());
	$("#nombreProdTD"+i).html($("#nombreprodM").val());
	$("#cla_nombreTD"+i).html($("#cla_nombreM").val());
	$("#diamextmmTD"+i).html($("#diamextmmM").val());
	$("#diamextmm"+i).val($("#diamextmmM").val());
	$("#espesorTD"+i).html($("#espesorM").val());
	$("#espesor"+i).val($("#espesorM").val());
	$("#longTD"+i).html($("#longM").val());
	$("#long"+i).val($("#longM").val());
	$("#pesoTD"+i).html($("#pesoM").val());
	$("#peso"+i).val($("#pesoM").val());
	$("#tipounionTD"+i).html($("#tipounionM").val());
	$("#tipounion"+i).val($("#tipounionM").val());
	$("#descuentoTD"+i).html($("#descuentoM option:selected").html());
	$("#descuento"+i).val($("#descuentoM option:selected").attr('porc'));
	$("#descuentoval"+i).val($("#descuentoM option:selected").attr('value'));
	$("#preciounitTD"+i).html(MASK(0, $("#precionetoM").attr('valor'), '-##,###,##0.000',1));
	$("#preciounit"+i).val($("#precionetoM").attr('valor'));
	$("#precioxkiloTD"+i).html(MASK(0, $("#precioM").attr('valor'), '-##,###,##0.000',1)); //$("#precioxkiloTD"+i).html(MASK(0, $("#precioM").val(), '-##,###,##0.00',1));
	$("#precioxkilo"+i).val($("#precioM").attr('valor'));
	$("#totalkilosTD"+i).html(MASK(0, $("#totalkilosM").attr('valor'), '-##,###,##0.00',1));
	$("#totalkilos"+i).val($("#totalkilosM").attr('valor'));
	$("#subtotalCFTD"+i).html(MASK(0, $("#subtotalM").attr('valor'), '-#,###,###,##0.00',1));
	$("#subtotal"+i).val($("#subtotalM").attr('valor'));
	$("#subtotalSFTD"+i).html($("#subtotalM").attr('valor'));
	totalizar();

}

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
	//alert(aux_descuento);

    var htmlTags = '<tr name="fila'+ aux_nfila + '" id="fila'+ aux_nfila + '">'+
			'<td name="cotdet_idTD'+ aux_nfila + '" id="cotdet_idTD'+ aux_nfila + '">'+ 
				'0'+
			'</td>'+
			'<td style="display:none;">'+
				'<input type="text" name="cotdet_id[]" id="cotdet_id'+ aux_nfila + '" class="form-control" value="0" style="display:none;"/>'+
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
				MASK(0, $("#precionetoM").attr("valor"), '-##,###,##0.000',1)+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="preciounit[]" id="preciounit'+ aux_nfila + '" class="form-control" value="'+ $("#precionetoM").attr("valor") +'" style="display:none;"/>'+
			'</td>'+
			'<td name="precioxkiloTD'+ aux_nfila + '" id="precioxkiloTD'+ aux_nfila + '" style="text-align:right">'+ 
				MASK(0, $("#precioM").attr("valor"), '-##,###,##0.000',1)+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="precioxkilo[]" id="precioxkilo'+ aux_nfila + '" class="form-control" value="'+ $("#precioM").attr("valor") +'" style="display:none;"/>'+
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
				MASK(0, $("#subtotalM").attr("valor"), '-#,###,###,##0.00',1)+
			'</td>'+
			'<td class="subtotalCF" style="text-align:right;display:none;">'+ 
				'<input type="text" name="subtotal[]" id="subtotal'+ aux_nfila + '" class="form-control" value="'+ $("#subtotalM").attr("valor") +'" style="display:none;"/>'+
			'</td>'+
			'<td name="subtotalSFTD'+ aux_nfila + '" id="subtotalSFTD'+ aux_nfila + '" class="subtotal" style="text-align:right;display:none;">'+ 
				$("#subtotalM").attr("valor")+
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

function totalizar(){
	total_neto = 0;

	$("#tabla-data tr .subtotal").each(function() {
		valor = $(this).html() ;
		valorNum = parseFloat(valor);
		total_neto += valorNum;
	});
	aux_porciva = $("#aux_iva").val()
	aux_porciva = parseFloat(aux_porciva);
	aux_iva = total_neto * (aux_porciva/100);
	aux_total = total_neto + aux_iva;
	aux_netoform = MASK(0, total_neto, '-##,###,##0.00',1)
	aux_ivaform = MASK(0, aux_iva, '-##,###,##0.00',1)
	aux_tdtotalform = MASK(0, aux_total, '-##,###,##0.00',1)
	
	//$("#tdneto").html(total_neto.toFixed(2));
	$("#tdneto").html(aux_netoform);
	$("#tdiva").html(aux_ivaform);
	$("#tdtotal").html(aux_tdtotalform);

	$("#neto").val(total_neto);
	$("#iva").val(aux_iva);
	$("#total").val(aux_total);
}

$('.region_id').on('change', function () {
	llenarProvincia(this,0);
});

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

function llenarComuna(obj,i){
	var data = {
        provincia_id: $(obj).val(),
        _token: $('input[name=_token]').val()
    };
    $.ajax({
        url: '/sucursal/obtComunas',
        type: 'POST',
        data: data,
        success: function (comuna) {
            $("#comuna_idM").empty();
            //$(".comuna_id").append("<option value=''>Seleccione...</option>");
            $.each(comuna, function(index,value){
                $("#comuna_idM").append("<option value='" + index + "'>" + value + "</option>")
            });
			$(".selectpicker").selectpicker('refresh');
			if(i>0){
				$("#comuna_idM").val($("#comuna_id"+i).val());
			}
			$(".selectpicker").selectpicker('refresh');
        }
    });
}


//VALIDACION DE CAMPOS
function limpiarInputOT(){
	$("#precioxkilorealM").val('');
	$("#producto_idM").val('');
	$("#codintprodM").val('');
	$("#nombreprodM").val('');
	$("#cantM").val('');
	$("#descuentoM").val('1');
	$("#totalkilosM").val('');
	$("#totalkilosM").attr('valor','0.00');
	$("#subtotalM").val('');
	$("#subtotalM").attr('valor','0.00');
	$("#cla_nombreM").val('');
	$("#diamextmmM").val('');
	$("#espesorM").val('');
	$("#longM").val('');
	$("#pesoM").val('');
	$("#tipounionM").val('');
	$("#precionetoM").val('');
	$("#precionetoM").attr('valor','0.00');
	$("#precioM").val('');
	$("#precioM").attr('valor','0.00');
    $(".selectpicker").selectpicker('refresh');
}

function verificar()
{
	var v1=0,v2=0,v3=0,v4=0,v5=0,v6=0,v7=0,v8=0,v9=0,v10=0,v11=0,v12=0,v13,v14=0;
	
	v3=validacion('descuentoM','combobox');
	v2=validacion('cantM','texto');
	v1=validacion('producto_idM','textootro');

	if (v1===false || v2===false || v3===false || v4===false || v5===false || v6===false || v7===false || v8===false || v9===false || v10===false || v11===false || v12===false || v13===false || v14===false)
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

function quitarverificar(){
	quitarValidacion('descuentoM','combobox');
	quitarValidacion('cantM','texto');
	quitarValidacion('producto_idM','textootro');
}




function editarRegistro(i){
	//alert($("#direccion"+i).val());
	event.preventDefault();
    limpiarInputOT();
	quitarverificar();
	$("#aux_sta").val('0');

	$("#aux_numfila").val(i);

	$("#precioxkilorealM").attr('valor',$("#precioxkiloreal"+i).val());
	$("#precioxkilorealM").val(MASK(0, $("#precioxkiloreal"+i).val(), '-##,###,##0.00',1));
	$("#codintprodM").val($.trim($("#codintprodTD"+i).html()));
	$("#nombreprodM").val($.trim($("#nombreProdTD"+i).html()));
	$("#producto_idM").val($("#producto_id"+i).val());


	$("#cantM").val($("#cant"+i).val());
	$("#descuentoM").val($.trim($("#descuentoval"+i).val()));
	$("#precionetoM").attr('valor',$("#preciounit"+i).val());
	$("#precionetoM").val(MASK(0, $("#preciounit"+i).val(), '-##,###,##0.00',1));
	$("#precioM").attr('valor',$("#precioxkilo"+i).val());
	$("#precioM").val(MASK(0, $("#precioxkilo"+i).val(), '-##,###,##0.00',1));
	$("#totalkilosM").attr('valor',$("#totalkilos"+i).val());
	$("#totalkilosM").val(MASK(0, $("#totalkilos"+i).val(), '-##,###,##0.00',1));
	$("#subtotalM").attr('valor',$("#subtotal"+i).val());
	$("#subtotalM").val(MASK(0, $("#subtotal"+i).val(), '-#,###,###,##0.00',1));
	$("#cla_nombreM").val($.trim( $("#cla_nombreTD"+i).html() ));
	$("#tipounionM").val($("#tipounion"+i).val());
	$("#diamextmmM").val($("#diamextmm"+i).val());
	$("#espesorM").val($("#espesor"+i).val());
	$("#longM").val($("#long"+i).val());
	$("#pesoM").val($("#peso"+i).val());
	

	$(".selectpicker").selectpicker('refresh');
    $("#myModal").modal('show');
}

function eliminarRegistro(i){
	event.preventDefault();
	//alert($('input[name=_token]').val());
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
    				window.location = loc.protocol+"//"+loc.hostname+"/cotizacionaprobar";
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

function copiar_codprod(id,codintprod){
	$("#myModalBuscarProd").modal('hide');
	$("#myModal").modal('show');
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
				url: '/cliente/buscarCli',
				type: 'POST',
				data: data,
				success: function (respuesta) {
					if(respuesta.length>0){
						$("#razonsocial").val(respuesta[0]['razonsocial']);
						$("#telefono").val(respuesta[0]['telefono']);
						$("#email").val(respuesta[0]['email']);
						$("#direccion").val(respuesta[0]['direccion']);
						$("#direccioncot").val(respuesta[0]['direccion']);
						$("#cliente_id").val(respuesta[0]['id'])
						$("#contacto").val(respuesta[0]['contactonombre']);
						//$("#vendedor_id").val(respuesta[0]['vendedor_id']);
						//$("#vendedor_idD").val(respuesta[0]['vendedor_id']);
						$("#region_id").val(respuesta[0]['regionp_id']);
						//alert($("#region_id").val());
						$("#provincia_id").val(respuesta[0]['provinciap_id']);
						$("#giro_id").val(respuesta[0]['giro_id']);
						$("#giro_idD").val(respuesta[0]['giro_id']);
						$("#comuna_id").val(respuesta[0]['comunap_id']);
						$("#comuna_idD").val(respuesta[0]['comunap_id']);
						$("#provincia_id").val(respuesta[0]['provinciap_id']);
						$("#plazopago_id").val(respuesta[0]['plazopago_id']);
						$("#plazopago_idD").val(respuesta[0]['plazopago_id']);
						$("#formapago_id").val(respuesta[0]['formapago_id']);
						$("#formapago_idD").val(respuesta[0]['formapago_id']);

						$("#comuna_idD option[value='"+ respuesta[0]['comunap_id'] +"']").attr("selected",true);
				
						//$("#comuna_idD option[value='101']").attr("selected",true);

						$("#clientedirec_id option").remove();
						//alert(respuesta[i]['direcciondetalle']);
						$('#clientedirec_id').attr("required", false);
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
						}else{
							$("#clientedirec_id").prop("disabled",true);
							$("#clientedirec_id").prop("readonly",true);	
						}
						activar_controles();
						formato_rut($("#rut"));
						$(".selectpicker").selectpicker('refresh');
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

function totalizarItem(aux_estprec){
	if(aux_estprec==1)
	{
		precioneto = $("#precionetoM").val();
		precio = $("#precioxkilorealM").val();
		$("#precionetoM").val(Math.round(precioneto));
		$("#precioM").val(precio);
	}else{
		precioneto = $("#precioM").val() * $("#pesoM").val();
		$("#precionetoM").val(Math.round(precioneto));
		$("#descuentoM").val('1');
		$(".selectpicker").selectpicker('refresh');
	}
	aux_tk = $("#cantM").val()*$("#pesoM").val();
	$("#totalkilosM").val(MASK(0, aux_tk.toFixed(2), '-##,###,##0.00',1));
	$("#totalkilosM").attr('valor',aux_tk.toFixed(2));
	aux_total = ($("#cantM").val() * $("#pesoM").val() * $("#precioM").val()) * ($("#descuentoM").val());
	$("#subtotalM").val(MASK(0, aux_total.toFixed(2), '-#,###,###,##0.00',1));
	$("#subtotalM").attr('valor',aux_total.toFixed(2));
	aux_precdesc = $("#precioM").val() * $("#descuentoM").val();
	$("#precioM").val(aux_precdesc);
	$("#precioM").attr('valor',aux_precdesc);
	aux_precioUnit = aux_precdesc * $("#pesoM").val();
	$("#precionetoM").val(MASK(0, Math.round(aux_precioUnit), '-##,###,##0.00',1));
	$("#precionetoM").attr('valor',Math.round(aux_precioUnit));
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
	v15=validacion('sucursal_idCTM','combobox');
	v14=validacion('finanzastelefonoCTM','numerico');
	v13=validacion('finanzanemailCTM','email');
	v12=validacion('finanzascontactoCTM','texto');
	v11=validacion('contactotelefCTM','numerico');
	v10=validacion('contactoemailCTM','email');
	v9=validacion('contactonombreCTM','texto');
	v8=validacion('comunap_idCTM','combobox');
	v7=validacion('plazopago_idCTM','combobox');
	v6=validacion('formapago_idCTM','combobox');
	v5=validacion('giro_idCTM','combobox');
	v4=validacion('emailCTM','email');
	v3=validacion('telefonoCTM','numerico');
	v2=validacion('direccionCTM','texto');
	v1=validacion('razonsocialCTM','texto');

	if (v1===false || v2===false || v3===false || v4===false || v5===false || v6===false || v7===false || v8===false || v9===false || v10===false || v11===false || v12===false || v13===false || v14===false || v15===false)
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
	$("#clientedirec_id option").remove();
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