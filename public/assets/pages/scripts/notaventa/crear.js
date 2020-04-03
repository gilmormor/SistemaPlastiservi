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
	$("#precioM").numeric();
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
			$(this).val("");
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
		todayHighlight: true
	}).datepicker("setDate");

	//$('.tooltip').tooltipster();

	if(aux_sta==2 || aux_sta==3){
		totalizar();
	}

	$("#btnguardaraprob").click(function(event){
		//alert('Entro');
		$("#myModalaprobcot").modal('show');
	});

	$('#oc_file').fileinput({
        language: 'es',
        maxFileSize: 4000,
        showUpload: false,
        showClose: false,
        initialPreviewAsData: true,
        dropZoneEnabled: false,
        theme: "fa",
	});

	if($("#vendedor_id").val() == '0'){
		$("#vendedor_idD").removeAttr("disabled");
		$("#vendedor_idD").removeAttr("readonly");
		$("#vendedor_idD").val("");
	}

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
		$("#direccionM").focus();	
	}
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
	$("#preciounitTD"+i).html(MASK(0, $("#precionetoM").attr('valor'), '-##,###,##0.00',1));
	$("#preciounit"+i).val($("#precionetoM").attr('valor'));
	aux_precioxkilo = $("#precioM").attr("valor");
	if($("#pesoM").val()==0)
	{
		aux_precioxkilo = 0; //$("#precioM").attr("valor");
	}
	//$("#precioxkiloTD"+i).html(MASK(0, $("#precioM").attr('valor'), '-##,###,##0.00',1)); //$("#precioxkiloTD"+i).html(MASK(0, $("#precioM").val(), '-##,###,##0.00',1));
	$("#precioxkiloTD"+i).html(MASK(0, aux_precioxkilo, '-##,###,##0.00',1)); //$("#precioxkiloTD"+i).html(MASK(0, $("#precioM").val(), '-##,###,##0.00',1));
	$("#precioxkilo"+i).val($("#precioM").attr('valor'));
	$("#totalkilosTD"+i).html(MASK(0, $("#totalkilosM").attr('valor'), '-##,###,##0.00',1));
	$("#totalkilos"+i).val($("#totalkilosM").attr('valor'));
	$("#subtotalCFTD"+i).html(MASK(0, $("#subtotalM").attr('valor'), '-##,###,##0.00',1));
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
	$("#subtotalM").val(MASK(0, $("#subtotal"+i).val(), '-##,###,##0.00',1));
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
		id: $("#NVdet_idTD"+i).html(),
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
					$("#diamextmmM").val(respuesta[0]['diamextmm']);
					$("#espesorM").val(respuesta[0]['espesor']);
					$("#longM").val(respuesta[0]['long']);
					$("#pesoM").val(respuesta[0]['peso']);
					$("#tipounionM").val(respuesta[0]['tipounion']);
					$("#precioM").val(respuesta[0]['precio']);
					$("#precioM").attr('valor',respuesta[0]['precio']);
					$("#precioxkilorealM").val(respuesta[0]['precio']);
					$("#precioxkilorealM").attr(respuesta[0]['precio']);
					$("#precionetoM").val(respuesta[0]['precioneto']);
					$("#precionetoM").attr(respuesta[0]['precioneto']);
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

function totalizarItem(aux_estprec){
	if($("#pesoM").val()==0){
		aux_peso = 1;
	}else{
		aux_peso = $("#pesoM").val();
	}
	if(aux_estprec==1)
	{
		precioneto = $("#precionetoM").val();
		precio = $("#precioxkilorealM").val();
		$("#precionetoM").val(Math.round(precioneto));
		$("#precioM").val(precio);
	}else{
		precioneto = $("#precioM").val() * aux_peso;
		$("#precionetoM").val(Math.round(precioneto));
		$("#descuentoM").val('1');
		$(".selectpicker").selectpicker('refresh');
	}

	aux_tk = $("#cantM").val() * aux_peso;
	if($("#pesoM").val()>0){	
		$("#totalkilosM").val(MASK(0, aux_tk.toFixed(2), '-##,###,##0.00',1));
		$("#totalkilosM").attr('valor',aux_tk.toFixed(2));
	}
	aux_total = ($("#cantM").val() * aux_peso * $("#precioM").val()) * ($("#descuentoM").val());
	$("#subtotalM").val(MASK(0, aux_total.toFixed(2), '-##,###,##0.00',1));
	$("#subtotalM").attr('valor',aux_total.toFixed(2));
	aux_precdesc = $("#precioM").val() * $("#descuentoM").val();
	$("#precioM").val(aux_precdesc);
	$("#precioM").attr('valor',aux_precdesc);
	aux_precioUnit = aux_precdesc * aux_peso;
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