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


	$("#mdialTamanio").css({'width': '50% !important'});
	aux_sta = $("#aux_sta").val();
	//$("#rut").numeric();
	$("#cantM").numeric();
	$("#precioM").numeric({decimalPlaces: 3});
	$(".numericop").numeric();
	$( "#myModal" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$( "#myModalBusqueda" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$( "#myModalBuscarProd" ).draggable({opacity: 0.35, handle: ".modal-header"});
	$(".modal-body label").css("margin-bottom", -2);
	$(".help-block").css("margin-top", -2);

	if($("#sucursal_id").val() > 0){
		llenarpesajescarros($("#sucursal_id").val())
	}
	$('.datepicker').datepicker({
		language: "es",
		autoclose: true,
		todayHighlight: true
	}).datepicker("setDate");

	//$('.tooltip').tooltipster();

});


$(".requeridos").keyup(function(){
	//alert($(this).parent().attr('class'));
	validacion($(this).prop('name'),$(this).attr('tipoval'));
});
$(".requeridos").change(function(){
	//alert($(this).parent().attr('class'));
	validacion($(this).prop('name'),$(this).attr('tipoval'));
});

function agregarFila() {
	llenarpesajescarros($("#sucursal_id").val());
	$("#itemcompletos").val("");
	//aux_num=parseInt($("#tabla-data >tbody >tr").length);
	aux_nroitem=parseInt($("#tabla-data >tbody >tr").length) + 1;
    aux_num=parseInt($("#ids").val());
    //alert(aux_num);
    aux_num = aux_num + 1;
    let aux_nfila = aux_num;
    $("#ids").val(aux_nfila);
	aux_turnoselect = $("#turnoselect").html();
	aux_pesajecarroselect = $("#pesajecarroselect").html();
    var htmlTags = `
	<tr name="fila${aux_nfila}" id="fila${aux_nfila}" class="proditems" item="${aux_nfila}">
		<td id="nroitem${aux_nfila}" name="nroitem${aux_nfila}" class="nroitem" style="text-align:center;padding-left: 3px;padding-right: 3px;">
			${aux_nroitem}
		</td>
		<td style="text-align:center;padding-left: 3px;padding-right: 3px;" name="producto_idTD${aux_nfila}" id="producto_idTD${aux_nfila}">
			<input type="text" name="producto_id[]" id="producto_id${aux_nfila}" onblur="onBlurProducto_id(this)" class="form-control numericop itemrequerido tooltipsC" onkeyup="buscarProdKeyUp(this,event)" value="" maxlength="4" style="text-align:right;" valor="" title="Codigo Producto (F2 para Buscar)" item="${aux_nfila}" placeholder="F2 Buscar"/>
			<input type="text" name="pesajedet_id[]" id="pesajedet_id${aux_nfila}" class="form-control" value="" item="${aux_nfila}" style="display:none;"/>
		</td>
		<td style="text-align:center;padding-left: 3px;padding-right: 3px;" name="producto_nombreTD${aux_nfila}" id="producto_nombreTD${aux_nfila}">
			<input type="text" name="producto_nombre[]" id="producto_nombre${aux_nfila}" class="form-control numericop calsubtotalitem tooltipsC" value="" item="${aux_nfila}" title="" readonly disabled/>
		</td>
		<!--
		<td name="unidadmedida_nombreTD${aux_nfila}" id="unidadmedida_nombreTD${aux_nfila}" valor="">
			<input type="text" name="unidadmedida_nombre[]" id="unidadmedida_nombre${aux_nfila}" class="form-control" value="" readonly disabled/>
		</td>
		-->
		<td name="pesounitnomTD${aux_nfila}" id="pesounitnomTD${aux_nfila}" valor="" style="padding-left: 3px;padding-right: 3px;">
			<input type="text" name="pesounitnom[]" id="pesounitnom${aux_nfila}" class="form-control" value="" style="text-align:right;padding-left: 0px;padding-right: 2px;" readonly disabled/>
		</td>
		<td name="areaproduccionsuclinea_idTD${aux_nfila}" id="areaproduccionsuclinea_idTD${aux_nfila}" valor="" style="padding-left: 3px;padding-right: 3px;">
			<select name="areaproduccionsuclinea_id[]" id="areaproduccionsuclinea_id${aux_nfila}" class="form-control areaproduccionsuclinea_id itemrequerido" title="Linea de Producción" item="${aux_nfila}" style="padding-left: 2px;">
			</select>
		</td>

		<td name="turno_idTD${aux_nfila}" id="turno_idTD${aux_nfila}" valor="" style="padding-left: 3px;padding-right: 3px;">
			<select name="turno_id[]" id="turno_id${aux_nfila}" class="form-control select2 itemrequerido" title="Turno"style="padding-left: 2px;">
				${aux_turnoselect}
			</select>
		</td>
		<td name="pesajecarro_idTD${aux_nfila}" id="pesajecarro_idTD${aux_nfila}" valor="" style="padding-left: 3px;padding-right: 3px;">
			<select name="pesajecarro_id[]" id="pesajecarro_id${aux_nfila}" class="form-control select2 pesajecarro_id itemrequerido" title="Carro" item="${aux_nfila}"style="padding-left: 2px;">
				${aux_pesajecarroselect}
			</select>
		</td>
		<td name="taraTD${aux_nfila}" id="taraTD${aux_nfila}" valor="" style="padding-left: 3px;padding-right: 3px;">
			<input type="text" name="tara[]" id="tara${aux_nfila}" class="form-control itemrequerido subtotaltara" value="" style="text-align:right;padding-left: 0px;padding-right: 2px;" title="Tara" readonly disabled/>
		</td>
		<td name="cantTD${aux_nfila}" id="cantTD${aux_nfila}" valor="" style="padding-left: 3px;padding-right: 3px;">
			<input type="text" name="cant[]" id="cant${aux_nfila}" class="form-control numericop calsubtotalitem itemrequerido subtotalcant" value="" item="${aux_nfila}" title="Cantidad Producto" style="text-align:right;padding-left: 0px;padding-right: 2px;"/>
		</td>
		<td name="pesobaltotalTD${aux_nfila}" id="pesobaltotalTD${aux_nfila}" valor="" style="padding-left: 3px;padding-right: 3px;">
			<input type="text" name="pesobaltotal[]" id="pesobaltotal${aux_nfila}" class="form-control numericop calsubtotalitem itemrequerido subtotalpesobaltotal" value="" title="Peso balanza Total" item="${aux_nfila}"  style="text-align:right;padding-left: 0px;padding-right: 2px;"/>
		</td>
		<td name="pesobalprodunitTD${aux_nfila}" id="pesobalprodunitTD${aux_nfila}" valor="" style="padding-left: 3px;padding-right: 3px;">
			<input type="text" name="pesobalprodunit[]" id="pesobalprodunit${aux_nfila}" class="form-control subtotalpesobalprodunit" value=""  style="text-align:right;padding-left: 0px;padding-right: 2px;" readonly disabled/>
		</td>
		<td name="pesobalprodtotalTD${aux_nfila}" id="pesobalprodtotalTD${aux_nfila}" valor="" style="padding-left: 3px;padding-right: 3px;">
			<input type="text" name="pesobalprodtotal[]" id="pesobalprodtotal${aux_nfila}" class="form-control subtotalpesobalprodtotal" value=""  style="text-align:right;padding-left: 0px;padding-right: 2px;" readonly disabled/>
		</td>
		<td name="PesoTotNormaTD${aux_nfila}" id="PesoTotNormaTD${aux_nfila}" valor="" style="padding-left: 3px;padding-right: 3px;">
			<input type="text" name="PesoTotNorma[]" id="PesoTotNorma${aux_nfila}" class="form-control subtotalPesoTotNorma" value="" style="text-align:right;padding-left: 0px;padding-right: 2px;" readonly disabled/>
		</td>
		<td name="DiferenciaKgTD${aux_nfila}" id="DiferenciaKgTD${aux_nfila}" valor="" style="padding-left: 3px;padding-right: 3px;">
			<input type="text" name="DiferenciaKg[]" id="DiferenciaKg${aux_nfila}" class="form-control subtotalDiferenciaKg" value="" style="text-align:right;padding-left: 0px;padding-right: 2px;" readonly disabled/>
		</td>
		<td name="DiferenciaPorcTD${aux_nfila}" id="DiferenciaPorcTD${aux_nfila}" valor="" style="padding-left: 3px;padding-right: 3px;">
			<input type="text" name="DiferenciaPorc[]" id="DiferenciaPorc${aux_nfila}" class="form-control subtotalDiferenciaPorc" value="" style="text-align:right;padding-left: 0px;padding-right: 2px;" readonly disabled/>
		</td>
		<td style="vertical-align:middle;">
			<a onclick="delitem(${aux_nfila})" class="btn-accion-tabla tooltipsC" title="Eliminar item" id="delitem${aux_nfila}" name="delitem${aux_nfila}" style="padding-left: 0px;">
				<i class="fa fa-fw fa-trash text-danger"></i>
			</a>
		</td>
	</tr>`;
    $('#tabla-data tbody').append(htmlTags);
	activarClases();
	totalizarpesaje();
	$("#producto_id" + aux_nfila).focus();
	/*
	$("unmditem" + aux_nfila).select2({
		tags: true
	  });
	*/
	validarItemVacios();
	$("#sucursal_id").prop('readonly', true);
	$('.select2').trigger('change');

	//$('.select2').trigger('change')
}

function activarClases(){
	$(".numericop").numeric();
	$(".calsubtotalitem").keyup(function(){
		calsubtotalitem(this);
	});
	$(".itemrequerido").change(function(){
		validarItemVacios();
	});
	$(".pesajecarro_id").change(function(){
		let item = $("#" + this.id).attr("item");
		let valor = $("#" + this.id).val();
		tara = $("#" + this.id + " option[value='"+ valor +"']").attr("tara");
		$("#tara" + item).val(tara);
		calsubtotalitem(this);
	});
	validarItemVacios();
}

function delitem(fila){
	swal({
        title: '¿ Eliminar item ?',
        text: "Esta acción no se puede deshacer!",
        icon: 'warning',
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
		if(value){
			//$("#cla_stadel"+fila).val(1);
			$("#fila" + fila).remove();
			validarItemVacios();
			totalizarpesaje();
		}
	});
}

function onBlurProducto_id(producto_id){
	objvlrcodigo = $("#" + producto_id["id"]);
	llenarDatosProdL(objvlrcodigo);
	//console.log(vlrcodigo["id"]);
}


//FUNCTION CON ASYNC, YA QUE ME INTERESA ESPERAR LA RESPUESTA DE LA BUSQUEDA
async function llenarDatosProdL(producto_id){
	let item = producto_id.attr("item");
	if($("#producto_id" + item).val() != $("#producto_id" + item).attr("valor")){
		arrayDP = await buscarDatosProdPesaje(producto_id);
		$("#producto_id" + item).val("");
		if(arrayDP['cont'] > 0){
			$("#producto_id" + item).val(arrayDP["id"]);
			$("#producto_id" + item).attr("valor",arrayDP["id"])
			let aux_producto_nombre = `${arrayDP["nombre"]} D:${arrayDP["diametro"]} C:${arrayDP["cla_nombre"]} L:${arrayDP["long"]} TU:${arrayDP["tipounion"]}`;
			$("#producto_nombre" + item).val(aux_producto_nombre);
			$("#producto_nombre" + item).attr("title",aux_producto_nombre);
			//$("#unidadmedida_nombre" + item).val(arrayDP["unidadmedidanombre"].substr(0,4));
			$("#pesounitnom" + item).val(arrayDP["peso"]);
			areaproduccionsucs = arrayDP["areaproduccionsucs"];
			areaproduccionsuclineas = arrayDP["areaproduccionsuclineas"];
			aux_sucursal_id = $("#sucursal_id").val();
			$("#areaproduccionsuclinea_id" + item).empty();
            $("#areaproduccionsuclinea_id" + item).append("<option value='' >Seleccione...</option>");
			for (let i = 0; i < areaproduccionsucs.length; i++) {
				if(areaproduccionsucs[i]["sucursal_id"] == aux_sucursal_id){
					for (let j = 0; j < areaproduccionsuclineas.length; j++){
						if(areaproduccionsuclineas[j]["areaproduccionsuc_id"] == areaproduccionsucs[i]["id"]){
							$("#areaproduccionsuclinea_id" + item).append(`<option value="${areaproduccionsuclineas[j]["id"]}">${areaproduccionsuclineas[j]["nombre"]}</option>`)
						}
					}
				}
			}
		}
		calsubtotalitem($("#producto_id" + item));	
	}
}

function calsubtotalitem(name){
	let i = $(name).attr("item");
	//console.log(i);
	let aux_pesobaltotal = $("#pesobaltotal" + i).val() == "" ? 0 : parseFloat($("#pesobaltotal" + i).val());
	let valor = $("#pesajecarro_id" + i).val();
	let aux_tara = $("#pesajecarro_id" + i + " option[value='"+ valor +"']").attr("tara");
	aux_tara = aux_tara == "" ? 0 : aux_tara;
	let aux_pesobalprodtotal = aux_pesobaltotal - aux_tara
	let aux_cant = $("#cant" + i).val() == "" ? 0 : parseFloat($("#cant" + i).val());
	let aux_pesobalprodunit = 0;
	let aux_PesoTotNorma = aux_cant * parseFloat($("#pesounitnom" + i).val());
	aux_PesoTotNorma = Number(aux_PesoTotNorma.toFixed(2));
	let aux_DiferenciaKg = aux_pesobalprodtotal - aux_PesoTotNorma;
	aux_DiferenciaKg = Number(aux_DiferenciaKg.toFixed(2));
	let aux_DiferenciaPorc = (aux_DiferenciaKg / aux_PesoTotNorma) * 100;
	aux_DiferenciaPorc = Number(aux_DiferenciaPorc.toFixed(2));

	if(aux_tara > 0 && aux_cant > 0 && aux_pesobaltotal > 0){
		if(aux_cant != 0){
			aux_pesobalprodunit = aux_pesobalprodtotal / aux_cant ; 
			aux_pesobalprodunit = Number(aux_pesobalprodunit.toFixed(2))
		}
	}else{
		aux_pesobalprodtotal = 0;
		aux_pesobalprodunit = 0;
		aux_PesoTotNorma = 0;
		aux_DiferenciaKg = 0;
		aux_DiferenciaPorc = 0;
	}
	$("#pesobalprodtotal" + i).val(aux_pesobalprodtotal);
	$("#pesobalprodunit" + i).val(aux_pesobalprodunit);
	$("#PesoTotNorma" + i).val(aux_PesoTotNorma);
	$("#DiferenciaKg" + i).val(aux_DiferenciaKg);
	$("#DiferenciaPorc" + i).val(aux_DiferenciaPorc);
	totalizarpesaje();
}

$("#sucursal_id").change(function(){
	id = $(this).val();
	llenarpesajescarros(id)
});

function llenarpesajescarros(id){
	if($("#pesajecarroselect option").length == 0){
		var data = {
			id: id,
			_token: $('input[name=_token]').val()
		};
		//console.log(data);
		//return 0;
		$.ajax({
			url: '/pesajecarro/listar',
			type: 'POST',
			data: data,
			success: function (pesajecarros) {
				$("#pesajecarroselect").empty();
				$("#pesajecarroselect").append("<option value='' tara='0'>Seleccione...</option>");
				pesajecarros.forEach(function(pesajecarros) {
					$("#pesajecarroselect").append(`<option value="${pesajecarros.id}" tara="${pesajecarros.tara}">${pesajecarros.nombre}</option>`)
				});
			}
		});	
	}

}

function cargardatospantprod(){

}

function copiar_codprod(id,codintprod){
	$('#myModalBuscarProd').modal('hide');
	let item = $("#itemAct").val();
	$("#producto_nombre" + item).val("");
	$("#producto_nombre" + item).attr("title","");
	//$("#unidadmedida_nombre" + item).val("");
	$("#pesounitnom" + item).val(""); 

	$("#producto_id" + item).val(id);
	$("#producto_id" + item).focus();
	//console.log(itemAct);
	//llenarDatosProd($("#producto_id" + itemAct));// buscarDatosProd($("#vlrcodigo" + itemAct));
}

$(".form-horizontal").on("submit", function(event){
	activarClases();
	validarItemVacios();
});

function totalizarpesaje(){
	total_tara = 0;
	total_cant = 0;
	total_pesobaltotal = 0;
	total_pesobalprodtotal = 0;
	total_pesobalprodunit = 0;
	total_PesoTotNorma = 0;
	total_DiferenciaKg = 0;
	total_DiferenciaPorc = 0;
	$("#tabla-data tr .subtotaltara").each(function() {
		//valor = $(this).html() ;
		valor = $(this).val();
		valor = valor.replace(/,/g, ""); //Elimina comas al valor con formato
		//alert(valor);
		valorNum = parseFloat(valor);
		total_tara += valorNum;
	});
	$("#tabla-data tr .subtotalcant").each(function() {
		//valor = $(this).html() ;
		valor = $(this).val();
		valor = valor.replace(/,/g, ""); //Elimina comas al valor con formato
		//alert(valor);
		valorNum = parseFloat(valor);
		if(isNaN(valorNum)){
			valorNum = 0;
		}
		total_cant += valorNum;
	});
	$("#tabla-data tr .subtotalpesobaltotal").each(function() {
		//valor = $(this).html() ;
		valor = $(this).val();
		valor = valor.replace(/,/g, ""); //Elimina comas al valor con formato
		//alert(valor);
		valorNum = parseFloat(valor);
		if(isNaN(valorNum)){
			valorNum = 0;
		}
		total_pesobaltotal += valorNum;
	});
	
	$("#tabla-data tr .subtotalpesobalprodunit").each(function() {
		//valor = $(this).html() ;
		valor = $(this).val();
		valor = valor.replace(/,/g, ""); //Elimina comas al valor con formato
		//alert(valor);
		valorNum = parseFloat(valor);
		if(isNaN(valorNum)){
			valorNum = 0;
		}
		total_pesobalprodunit += valorNum;
	});
	$("#tabla-data tr .subtotalpesobalprodtotal").each(function() {
		//valor = $(this).html() ;
		valor = $(this).val();
		valor = valor.replace(/,/g, ""); //Elimina comas al valor con formato
		//alert(valor);
		valorNum = parseFloat(valor);
		if(isNaN(valorNum)){
			valorNum = 0;
		}
		total_pesobalprodtotal += valorNum;
	});
	$("#tabla-data tr .subtotalPesoTotNorma").each(function() {
		//valor = $(this).html() ;
		valor = $(this).val();
		valor = valor.replace(/,/g, ""); //Elimina comas al valor con formato
		//alert(valor);
		valorNum = parseFloat(valor);
		if(isNaN(valorNum)){
			valorNum = 0;
		}
		total_PesoTotNorma += valorNum;
	});
	$("#tabla-data tr .subtotalDiferenciaKg").each(function() {
		//valor = $(this).html() ;
		valor = $(this).val();
		valor = valor.replace(/,/g, ""); //Elimina comas al valor con formato
		//alert(valor);
		valorNum = parseFloat(valor);
		if(isNaN(valorNum)){
			valorNum = 0;
		}
		total_DiferenciaKg += valorNum;
	});
	$("#tabla-data tr .subtotalDiferenciaPorc").each(function() {
		//valor = $(this).html() ;
		valor = $(this).val();
		valor = valor.replace(/,/g, ""); //Elimina comas al valor con formato
		//alert(valor);
		valorNum = parseFloat(valor);
		if(isNaN(valorNum)){
			valorNum = 0;
		}
		total_DiferenciaPorc += valorNum;
	});

	$("#tdtotal_tara").html("0");
	$("#tdtotal_cant").html("0");
	$("#tdtotal_pesobaltotal").html("0");
	$("#tdtotal_pesobalprodunit").html("0");
	$("#tdtotal_pesobalprodtotal").html("0");
	$("#tdtotal_PesoTotNorma").html("0");
	$("#tdtotal_DiferenciaKg").html("0");
	$("#tdtotal_DiferenciaPorc").html("0");
	
	if(total_pesobalprodtotal == 0){
		$("#total").val("");
		//$("tfoot").hide();
		$("#foottotal").hide();
		
	}else{
		$("#tdtotal_tara").html(MASKLA(total_tara,2));
		$("#tdtotal_cant").html(MASKLA(total_cant,2));
		$("#tdtotal_pesobaltotal").html(MASKLA(total_pesobaltotal,2));
		$("#tdtotal_pesobalprodunit").html(MASKLA(total_pesobalprodunit,2));
		$("#tdtotal_pesobalprodtotal").html(MASKLA(total_pesobalprodtotal,2));
		$("#tdtotal_PesoTotNorma").html(MASKLA(total_PesoTotNorma,2));
		$("#tdtotal_DiferenciaKg").html(MASKLA(total_DiferenciaKg,2));
		$("#tdtotal_DiferenciaPorc").html(MASKLA(total_DiferenciaPorc,2));

		$("#total").val(total_pesobalprodtotal);
		//$("tfoot").show();
		$("#foottotal").show();
	}
}

async function buscarDatosProdPesaje(producto_id){
	codigo = producto_id.val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		aux_cliente_id = null;
		if($("#cliente_id").val()){
			aux_cliente_id = $("#cliente_id").val();
		}
		var data = {
			id: codigo,
			cliente_id : aux_cliente_id,
			_token: $('input[name=_token]').val()
		};
		return resul = await $.ajax({
			url: '/producto/buscarUnProducto',
			type: 'POST',
			data: data,
			success: function (respuesta) {
				//console.log(respuesta);
				if(respuesta['cont']>0){
					if(respuesta['estado'] == 0){
						swal({
							title: 'Producto inactivo.',
							text: "Producto existe pero está Inactivo.",
							icon: 'error',
							buttons: {
								confirm: "Aceptar"
							},
						}).then((value) => {
							if (value) {
								$("#producto_idM").focus();
							}
						});
					}
				}else{
					producto_id.val("");
					swal({
						title: `Código producto ${codigo} no existe.`,
						text: "Presione F2 para buscar",
						icon: 'error',
						buttons: {
							confirm: "Aceptar"
						},
					}).then((value) => {
						if (value) {
							producto_id.focus();
						}
					});
				}
				return respuesta;
			}
		});
		//console.log(resul);
	}else{
		return [];
	}
}