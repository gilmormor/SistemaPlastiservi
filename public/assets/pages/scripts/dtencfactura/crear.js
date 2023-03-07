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

	fieldHTML = "<input type='hidden' name='itemAct' id='itemAct'>"; //Creo input con campo itemAct=item actual
	$("#DivVerTodosProd").append(fieldHTML);
	blanquearDatos();
	$("#codref").val("");

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
					totalizarNc();*/
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
				if(respuesta.length>0){
					$('#centroeconomico_id').val(respuesta[0].centroeconomico_id); // Select the option with a value of '1'
					$('#vendedor_id').val(respuesta[0].vendedor_id); // Select the option with a value of '1'
					
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


$("#nrodoctoF").focus(function(){
	blanquearDatos();
});

$("#nrodoctoF").blur(function(){
	buscardocumento(0);
	blanquearDatos();
	codigo = $("#nrodoctoF").val();
	//limpiarCampos();
	aux_sta = $("#aux_sta").val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		var data = {
			nrodocto: codigo,
			tdfoliocontrol_id : $("#tdfoliocontrol_id").val(),
			_token: $('input[name=_token]').val()
		};
		$.ajax({
			//url: '/cliente/buscarCliId',
			url: '/dtencfactura/consdte_dtedet',
			type: 'POST',
			data: data,
			success: function (respuesta) {
				bandera = true;
				if(respuesta.dte.length>0){
					if(respuesta.dtefacdet.length>0){
						llenarDatosCliente(respuesta);
						llenarItemFact(respuesta.dtefacdet);
						bandera = false;
					}
				}
				if(bandera){
					swal({
						//title: aux_mensaje,
						text: respuesta.mensaje,
						icon: 'warning',
						buttons: {
							confirm: "Aceptar"
						},
					}).then((value) => {
						if (value) {
							//ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
							$("#nrodoctoF").focus();
						}
					});
				}
			}
		});
	}
});

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
		let aux_nmbitem = data[i].nmbitem.replace('"', "'");
		let aux_nmbitemhtml = data[i].nmbitem.replace('"', "'");
		let aux_txt  = "";
		//for (j = 0; j < data[i].dtedetAsociados.length; j++)
		for (j = 0; j < data[i].dtedetAsociados.length; j++){
			//console.log(data[i].dtedetAsociados);
			if(data[i].dtedetAsociados[j].foliocontrol_id == 5){
				aux_txt += " <a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Nota Crédito' onclick='genpdfNC(" + data[i].dtedetAsociados[j].nrodocto + ",\"\")'>" +
							"NC:" + data[i].dtedetAsociados[j].nrodocto +
						"</a>";
			}else{
				aux_txt += " <a style='padding-left: 0px;' class='btn-accion-tabla btn-sm tooltipsC' title='Nota Débito' onclick='genpdfND(" + data[i].dtedetAsociados[j].nrodocto + ",\"\")'>" +
							"ND:" + data[i].dtedetAsociados[j].nrodocto +
						"</a>";
			}
			//genpdfNC(respuesta.nrodocto,"")
		}
		aux_nmbitemhtml += aux_txt;
		htmlTags = `<tr name="fila${data[i].id}" id="fila${data[i].id}" class="proditems ${data[i].id}" item="${data[i].id}">
			<td style="text-align:center">
				<div id="nroitem${data[i].id}" name="nroitem${data[i].id}" class="nroitem">
					${data[i].nrolindet}
				</div>				
				<input type="text" name="det_id[]" id="det_id${data[i].id}" class="form-control" value="0" style="display:none;"/>
				<input type="text" name="dtedetorigen_id[]" id="dtedetorigen_id${data[i].id}" class="form-control" value="${data[i].id}" style="display:none;"/> <!--ID origen del registro detalle-->
				<input type="text" name="nrolindet[]" id="nrolindet${data[i].id}" class="form-control" value="${data[i].nrolindet}" style="display:none;"/>
				<input type="text" name="dtedet_id[]" id="dtedet_id${data[i].id}" class="form-control" value="${data[i].dtedet_id}" style="display:none;"/>
				<input type="text" name="obsdet[]" id="obsdet${data[i].id}" class="form-control" value="${data[i].obsdet}" style="display:none;"/>
				<input type="text" name="unidadmedida_id[]" id="unidadmedida_id${data[i].id}" class="form-control" value="${data[i].unidadmedida_id}" style="display:none;"/>
			</td>
			<td style="text-align:center" name="producto_idTD${data[i].id}" id="producto_idTD${data[i].id}">
				<div id="lblvlrcodigo${data[i].id}" name="lblvlrcodigo${data[i].id}" class="tooltipsC" title="Codigo de Producto">
					${data[i].producto_id}
				</div>
				<input type="text" name="vlrcodigo[]" id="vlrcodigo${data[i].id}" class="form-control numerico itemrequerido" value="${data[i].producto_id}" item="${data[i].id}" onblur="onBlurProducto_id(this)" onkeyup="buscarProdKeyUp(this,event)" style="text-align:right;display:none;" title="Código producto"/>
				<input type="text" name="producto_id[]" id="producto_id${data[i].id}" class="form-control" value="${data[i].producto_id}" style="display:none;"/>
			</td>
			<td name="cantTD${data[i].id}" id="cantTD${data[i].id}" style="text-align:right" class="subtotalcant" valor="${data[i].qtyitem}">
				<input type="text" name="qtyitem[]" id="qtyitem${data[i].id}" class="form-control numerico calsubtotalitem" value="${data[i].qtyitem}" valor="${data[i].qtyitem}" valorini="${data[i].qtyitem}" item="${data[i].id}" style="text-align:right" title="Cantidad"/>
			</td>
			<td name="unidadmedida_nombre${data[i].id}" id="unidadmedida_nombre${data[i].id}" valor="${data[i].unmditem}">
					<div id="lblunmditem${data[i].id}" name="lblunmditem${data[i].id}" class="tooltipsC" title="Unidad Medida">
						${data[i].unmditem}
					</div>
					<select id="unmditemselect${data[i].id}" name="unmditemselect[]" class="form-control select2 itemrequerido" title="Unidad Medida" style="display:none;">
						${$("#unidadmedida_id").html()}
					</select>
					<input type="text" name="unmditem[]" id="unmditem${data[i].id}" class="form-control" value="${data[i].unmditem}" style="display:none;"/>
			</td>
			<td name="NPTD${data[i].id}" id="NPTD${data[i].id}" valor="${aux_nmbitem}">
				<p name="nombreProdTD${data[i].id}" id="nombreProdTD${data[i].id}" valor="${aux_nmbitem}">
					${aux_nmbitemhtml}
				</p>
				<input type="text" name="nmbitem[]" id="nmbitem${data[i].id}" class="form-control itemrequerido" value="${aux_nmbitem}" style="display:none;" title="Nombre producto"/>
				<input type="text" name="dscitem[]" id="dscitem${data[i].id}" class="form-control" value="${data[i].dscitem}" style="display:none;"/>
			</td>
			<td name="subtotalkg${data[i].id}" id="subtotalkg${data[i].id}" style="text-align:right;" class="subtotalkg" valor="${data[i].itemkg}">
					${MASKLA(data[i].itemkg,2)}
			</td>
			<td name="descuentoTD${data[i].id}" id="descuentoTD${data[i].id}" style="text-align:right;display:none;">
				0%
			</td>
			<td style="text-align:right;display:none;"> 
				<input type="text" name="descuento[]" id="descuento${data[i].id}" class="form-control" value="0" style="display:none;"/>
				<input type="text" name="totalkilos[]" id="totalkilos${data[i].id}" class="form-control" value="${data[i].itemkg}" style="display:none;" valor="${data[i].itemkg}" fila="${data[i].id}"/>
				<input type="text" name="itemkg[]" id="itemkg${data[i].id}" class="form-control" value="${data[i].itemkg}" style="display:none;"/>
			</td>
			<td style="text-align:right;display:none;">
				<input type="text" name="descuentoval[]" id="descuentoval${data[i].id}" class="form-control" value="0" style="display:none;"/>
			</td>
			<td name="preciounitTD${data[i].id}" id="preciounitTD${data[i].id}" style="text-align:right;">
				<input type="text" name="prcitem[]" id="prcitem${data[i].id}" class="form-control numerico calsubtotalitem" value="${data[i].prcitem}" valor="${data[i].prcitem}" valorini="${data[i].prcitem}" item="${data[i].id}" style="text-align:right" title="Precio Unitario"/>
			</td>
			<td style="display:none;" name="precioxkiloTD${data[i].id}" id="precioxkiloTD${data[i].id}" style="text-align:right">
				${data[i].precioxkilo}
			</td>
			<td name="subtotalFactDet${data[i].id}" id="subtotalFactDet${data[i].id}" class="subtotalFactDet" style="text-align:right">
				<input type="text" name="montoitem[]" id="montoitem${data[i].id}" class="form-control numerico calpreciounit" value="${data[i].montoitem}" valor="${data[i].montoitem}" valorini="${data[i].montoitem}" item="${data[i].id}" style="text-align:right" readonly/>
			</td>
			<td name="subtotalSFTD${data[i].id}" id="subtotalSFTD${data[i].id}" class="subtotal" item="${data[i].id}" style="text-align:right;display:none;">
				${data[i].montoitem}
			</td>
			<td name="accion${data[i].id}" id="accion${data[i].id}" style="text-align:center">
				<a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="Editar" onclick="editarItem(${data[i].id})">
					<i class="fa fa-fw fa-pencil-square-o"></i>
				</a>
				<a class="btn-accion-tabla eliminar tooltipsC" title="Eliminar item" onclick="eliminarRegistro(${data[i].id})">
					<i class="fa fa-fw fa-trash text-danger"></i>
				</a>
			</td>
		</tr>`;
		$('#tabla-data tbody').append(htmlTags);
		$(`#unmditemselect${data[i].id} > option[value=${data[i].unidadmedida_id}]`).attr("selected",true);
	}
	totalizar();
	totalizarNc();
	$("#totalini").val($("#tdtotalmodificado").attr("valor"));
	$("#totalini").attr("valor",$("#tdtotalmodificado").attr("valor"));
	aux_totalini = $("#totalini").attr("valor");
	aux_totalini = parseFloat(aux_totalini);
	$("#tdtotalrestante").html(MASKLA(aux_totalini,0));
	$("#tdtotalrestante").attr("valor",$("#tdtotalmodificado").attr("valor"));
	activarClases();

	validarlistcodrefNc();
	$("#tablaoriginal").val($('#tabla-data tbody').html());
}


function verGD(nrodocto){
	genpdfGD(nrodocto,"","");
}
function blanquearDatos(){
	$("#rut").val("");
	$("#dte_id").val("");
	$("#updated_at").val("");
	$("#razonsocial").val("");
	$("#telefono").val("");
	$("#email").val("");
	$("#direccion").val("");
	$("#comuna_nombre").val("");
	//$("#provincia_nombre").val("");
	$("#direccioncot").val("");
	$("#cliente_id").val("")
	$("#contacto").val("");
	$("#region_id").val("");
	$("#provincia_id").val("");
	$("#comuna_id").val("");
	$("#comuna_idD").val("");
	$("#formapago_desc").val("");
	$("#plazopago").val("");
	$("#fchemis").val("");
	$("#fchvenc").val("");
	$("#vendedor_id").val("");
	$("#centroeconomico_id").val("");
	$("#hep").val("");
	$("#indtraslado").val("");
	$("#obsfac").val("");
	$("#obs").val("");
	$("#obs").attr("readonly",true);
	$('#tabla-data tbody').html("");
	$("#dtencdet").html("");
	$("#tdtotaloriginal").html("");
	$("#tdtotaloriginal").attr("valor","");
	$("#tdtotalmodificado").html("");
	$("#tdtotalmodificado").attr("valor","");
	$("#tablaoriginal").val("");
	$("#vendedor_id").attr("disabled","true");
	$("#centroeconomico_id").attr("disabled","true");
	$("#codref").attr("disabled","true");
	totalizarNc();
	$("#codref").val("");
	$('.select2').trigger('change');
}

function llenarDatosCliente(respuesta){
	$("#rut").val(respuesta.dte[0].rut);

	formato_rut($("#rut"));
	$("#dte_id").val(respuesta.dte[0].dte_id);
	$("#updated_at").val(respuesta.dte[0].updated_at);
	$("#razonsocial").val(respuesta.dte[0].razonsocial);
	$("#telefono").val(respuesta.dte[0].telefono);
	$("#email").val(respuesta.dte[0].email.toLowerCase());
	$("#direccion").val(respuesta.dte[0].direccion);

	$("#comuna_nombre").val(respuesta.dte[0].comuna_nombre);
	//$("#provincia_nombre").val(respuesta.dte[0].provincia_nombre);


	$("#direccioncot").val(respuesta.dte[0].direccion);
	$("#cliente_id").val(respuesta.dte[0].id)
	$("#contacto").val(respuesta.dte[0].contactonombre);
	//$("#vendedor_id").val(respuesta[0]['vendedor_id']);
	//$("#vendedor_idD").val(respuesta[0]['vendedor_id']);
	$("#region_id").val(respuesta.dte[0].regionp_id);
	$("#provincia_id").val(respuesta.dte[0].provinciap_id);
	$("#comuna_id").val(respuesta.dte[0].comunap_id);
	$("#comuna_idD").val(respuesta.dte[0].comunap_id);

	$("#vendedor_idD").val(respuesta.dte[0].vendedor_id);

	$("#formapago_desc").val(respuesta.dte[0].formapago_desc);
	$("#plazopago").val(respuesta.dte[0].plazopago_dias);
	
	aux_fecha = new Date(respuesta.dte[0].fchemis);
	$("#fchemis").val(fechaddmmaaaa(aux_fecha));
	aux_fecha = new Date(respuesta.dte[0].fchvenc);
	$("#fchvenc").val(fechaddmmaaaa(aux_fecha));

	$('#centroeconomico_id').val(respuesta.dte[0].centroeconomico_id); // Select the option with a value of '1'
	$('#vendedor_id').val(respuesta.dte[0].vendedor_id); // Select the option with a value of '1'
	$('.select2').trigger('change');

	$("#hep").val(respuesta.dte[0].hep);
	$("#obsfac").val(respuesta.dte[0].obs);
	$("#obs").attr("readonly",false);
	$('#centroeconomico_id').attr("readonly",false);
	$('#vendedor_id').attr("readonly",false);
	$('#centroeconomico_id').attr("disabled",false);
	$('#vendedor_id').attr("disabled",false);
	$('#indtraslado').val(respuesta.dte[0].indtraslado);
	$('#codref').attr("readonly",false);
	$('#codref').attr("disabled",false);

	$("#tdtotaloriginal").html(MASKLA(respuesta.totaloriginal,0));
	$("#tdtotaloriginal").attr("valor",respuesta.totaloriginal);
	$("#tdtotalmodificado").html(MASKLA(respuesta.totalmodificado,0));
	$("#tdtotalmodificado").attr("valor",respuesta.totalmodificado);

	let id_str = respuesta.dte[0].nrodocto.toString();
	id_str = respuesta.dte[0].nombrepdf + id_str.padStart(8, "0");

	aux_txt = 	`DTE: <a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="DTE" onclick="genpdfFAC('${id_str}','')">
					${respuesta.dte[0].nrodocto}
				</a>`;
	$("#dtencdet").html(aux_txt);


}


function eliminarRegistro(i){
	let item = $("#nroitem" + i).html();
	swal({
		title: `¿ Seguro desea eliminar item ${item}?`,
		text: "",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			$("#fila"+i).remove();
			Biblioteca.notificaciones('Registro eliminado. Debes actualizar o guardar para que los cambios surtan efecto.', 'Plastiservi', 'success');
			totalizarNc();
		}
	});
}

function mensajeEliminarRegistro(data){
	$("#fila"+data['nfila']).remove();
	Biblioteca.notificaciones('Registro eliminado. Debes actualizar o guardar para que los cambios surtan efecto.', 'Plastiservi', 'success');
	totalizarNc();
}

function calsubtotalitem(name){
	let i = $(name).attr("item");
	let qtyitem = $("#qtyitem" + i).val() == "" ? 0 : parseFloat($("#qtyitem" + i).val());
	let prcitem = $("#prcitem" + i).val() == "" ? 0 : parseFloat($("#prcitem" + i).val());

	if(qtyitem == 0){
		$("#qtyitem" + i).val("0");
	}
	if(prcitem == 0){
		$("#prcitem" + i).val("0");
	}

	let aux_subtotal = qtyitem * prcitem;
	$("#qtyitem" + i).attr("valor",$("#qtyitem" + i).val());
	$("#cantTD" + i).attr("valor",$("#qtyitem" + i).val());
	$("#prcitem" + i).attr("valor",$("#prcitem" + i).val());
	$("#montoitem" + i).val(aux_subtotal);
	$("#montoitem" + i).attr("valor",aux_subtotal);
	$("#subtotalSFTD" + i).html(aux_subtotal);
	totalizarNc();
	respableservaloresini(name);
}

function calpreciounit(name){
	let i = $(name).attr("item");
	let montoitem = $("#montoitem" + i).val() == "" ? 0 : parseFloat($("#montoitem" + i).val());
	let qtyitem =   $("#qtyitem" + i).val() == "" ? 0 : parseFloat($("#qtyitem" + i).val());
	if((montoitem == 0) || (qtyitem == 0)){
		$("#montoitem" + i).val("0");
	}
	let aux_preciounit = 0;
	if(montoitem > 0  && qtyitem > 0){
		aux_preciounit = $("#montoitem" + i).val() / $("#qtyitem" + i).val();
	}
	aux_preciounit = Math.ceil(aux_preciounit);
	$("#prcitem" + i).val(aux_preciounit);
	$("#prcitem" + i).attr("valor",aux_preciounit);
	$("#montoitem" + i).attr("valor",$("#montoitem" + i).val());
	$("#subtotalSFTD" + i).html($("#montoitem" + i).val());
	totalizarNc();
	respableservaloresini(i);
	calsubtotalitem(name);
}

function respableservaloresini(name){
	let i = $(name).attr("item");
	let aux_totalini = parseFloat($("#tdtotaloriginal").attr("valor"));
	let aux_totalfin = parseFloat($("#tdneto").attr("valor"));
	//let aux_totalfin = parseFloat($("#total").val());
	
	//console.log(aux_totalini);
	//console.log(aux_totalfin);
	let qtyitem = $("#qtyitem" + i).val();
	let prcitem = $("#prcitem" + i).val();
	let montoitem = $("#montoitem" + i).val();
	if((aux_totalfin >= aux_totalini) || qtyitem == "0" || prcitem == "0" || montoitem == "0"){
		$("#qtyitem" + i).val($("#qtyitem" + i).attr("valorini"));
		$("#qtyitem" + i).attr("valor",$("#qtyitem" + i).attr("valorini"));
		$("#cantTD" + i).attr("valor",$("#qtyitem" + i).attr("valorini"));
		$("#prcitem" + i).val($("#prcitem" + i).attr("valorini"));
		$("#prcitem" + i).attr("valor",$("#prcitem" + i).attr("valorini"));
		$("#montoitem" + i).val($("#montoitem" + i).attr("valorini"));
		$("#montoitem" + i).attr("valor",$("#montoitem" + i).attr("valorini"));
		$("#subtotalSFTD" + i).html($("#montoitem" + i).attr("valorini"));
		$(name).focus();
		$(name).select();
		totalizarNc();
	}		
}


$("#tdfoliocontrol_id").change(function(){
	if($("#tdfoliocontrol_id").val() != ""){
		$('#nrodoctoF').attr("readonly",false);
	}else{
		$('#nrodoctoF').attr("readonly",true);
	}
	//blanquearDatos();
});

$("#codref").change(function(){
	let aux_val = $(this).val();
	$('#tabla-data tbody').html($("#tablaoriginal").val());
	activarClases();
	if(aux_val == 1){
		let $i = 1;
		$("#tabla-data tr .subtotal").each(function() {
			let item = $(this).attr("item");
			$("#qtyitem" + item).attr("readonly","true");
			$("#prcitem" + item).attr("readonly","true");
			$("#montoitem" + item).attr("readonly","true");
			$("#accion" + item).html("");
			$i++;
		});
		//buscardocumento(aux_val,this); //paso una bandera = 1 y el objeto this que corresponde a codref
	}
	if(aux_val == 2){
		let $i = 1;
		$("#tabla-data tr .subtotal").each(function() {
			let item = $(this).attr("item");
			//console.log(item);
			$("#qtyitem" + item).val(0);
			$("#prcitem" + item).val(0);
			$("#montoitem" + item).val(0);
			$("#subtotalSFTD" + item).html(0);
			
			$("#cantTD" + item).attr("valor","0");
			$("#precioxkiloTD" + item).html("0");
			$("#subtotalkg" + item).html("0");
			$("#subtotalkg" + item).attr("valor","0");
			$("#totalkilos" + item).val(0);
			$("#itemkg" + item).val(0);
			$("#unmditem" + item).val(".");
			$("#unidadmedida_nombre" + item).html(".");
			$("#nmbitem" + item).show();
			$("#nombreProdTD" + item).hide();
			$("#qtyitem" + item).attr("readonly","true");
			$("#prcitem" + item).attr("readonly","true");
			$("#montoitem" + item).attr("readonly","true");
			$("#accion" + item).html("");
			if($i> 1){
				$("#fila" + item).remove();
			}
			$i++;
		});
		//buscardocumento(aux_val,this); //paso una bandera = 1 y el objeto this que corresponde a codref
	}
	totalizar();
	if(aux_val == 2){
		$("#total").val(1);
	}
});


$("#form-general").on('submit', function (event) {
	/*
	swal({
		title: "Generar documento DTE?",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			let tdtotaloriginal = parseFloat($("#tdtotaloriginal").attr("valor"));
			let tdtotalmodificado = parseFloat($("#tdtotalmodificado").attr("valor"));
			let tdneto = parseFloat($("#tdneto").attr("valor"));
			if(tdneto == tdtotalmodificado){
				swal({
					text: 'Total neto no puede ser igual al Saldo Neto.',
					icon: 'warning',
					buttons: {
						confirm: "Aceptar"
					},
				});
				//Biblioteca.notificaciones('Total neto no puede ser igual al Saldo Neto.', 'Plastiservi', 'error');
				event.preventDefault();
			}
			if(tdneto > tdtotalmodificado){
				swal({
					text: 'Total neto no puede ser mayor al Saldo Neto.',
					icon: 'warning',
					buttons: {
						confirm: "Aceptar"
					},
				});
				//Biblioteca.notificaciones('Total neto no puede ser mayor al Saldo Neto.', 'Plastiservi', 'error');
				event.preventDefault();
			}
		}else{
			event.preventDefault();
		}
	});
	*/
	activarClases();
	validarItemVacios();
	let tdtotaloriginal = parseFloat($("#tdtotaloriginal").attr("valor"));
	let tdtotalmodificado = parseFloat($("#tdtotalmodificado").attr("valor"));
	let tdneto = parseFloat($("#tdneto").attr("valor"));
	/*
	if(tdneto == tdtotalmodificado){
		swal({
			text: 'Total neto no puede ser igual al Saldo Neto.',
			icon: 'warning',
			buttons: {
				confirm: "Aceptar"
			},
		});
		//Biblioteca.notificaciones('Total neto no puede ser igual al Saldo Neto.', 'Plastiservi', 'error');
		event.preventDefault();
	}
	*/
	if(tdneto > tdtotalmodificado){
		swal({
			text: 'Total neto no puede ser mayor al Saldo Neto.',
			icon: 'warning',
			buttons: {
				confirm: "Aceptar"
			},
		});
		//Biblioteca.notificaciones('Total neto no puede ser mayor al Saldo Neto.', 'Plastiservi', 'error');
		event.preventDefault();
	}

});

function buscardocumento(aux_band,thiscodref){
	blanquearDatos();
	codigo = $("#nrodoctoF").val();
	//limpiarCampos();
	aux_sta = $("#aux_sta").val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		var data = {
			nrodocto: codigo,
			tdfoliocontrol_id : $("#tdfoliocontrol_id").val(),
			_token: $('input[name=_token]').val()
		};
		$.ajax({
			//url: '/cliente/buscarCliId',
			url: '/dtencfactura/consdte_dtedet',
			type: 'POST',
			data: data,
			success: function (respuesta) {
				bandera = true;
				if(respuesta.dte.length>0){
					if(respuesta.dtefacdet.length>0){
						llenarDatosCliente(respuesta);
						//console.log(respuesta.dtefacdet);
						llenarItemFact(respuesta.dtefacdet);
						bandera = false;
						if(aux_band == 3){
							//$(thiscodref).val(3);
						}
					}
				}
				if(bandera){
					swal({
						//title: aux_mensaje,
						text: respuesta.mensaje,
						icon: 'warning',
						buttons: {
							confirm: "Aceptar"
						},
					}).then((value) => {
						if (value) {
							//ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
							$("#nrodoctoF").focus();
						}
					});
				}
			}
		});
	}
}

function activarClases(){
	$(".numerico").numeric();
	$(".calsubtotalitem").keyup(function(){
		calsubtotalitem(this)
	});
	$(".calpreciounit").keyup(function(){
		calpreciounit(this)
	});
	$(".itemrequerido").change(function(){
		validarItemVacios();
	});

	validarItemVacios();

}

function copiar_codprod(id,codintprod){
	//$("#myModalBuscarProd").modal('hide');
	//$("#myModal").modal('show');
	$('#myModalBuscarProd').modal('hide');
	let itemAct = $("#itemAct").val();
	console.log(itemAct);
	//$("#producto_id" + itemAct).val(id);
	$("#vlrcodigo" + itemAct).val(id);
	//$("#vlrcodigo" + itemAct).blur();
	$("#qtyitem" + itemAct).focus();
	$("#qtyitem" + itemAct).select();
	llenarDatosProd($("#vlrcodigo" + itemAct));// buscarDatosProd($("#vlrcodigo" + itemAct));
	//console.log(arrayDatosProducto);
	//$("#cantM").focus();
}

const editarItem = (item) => {
	$("#lblvlrcodigo" + item).hide();
	$("#vlrcodigo" + item).show();
	$("#lblunmditem" + item).hide();
	$("#unmditemselect" + item).show();
	$("#nombreProdTD" + item).hide();
	$("#nmbitem" + item).show();
//	$(`#unmditemselect${item} > option[value=${$("#unidadmedida_id" + item).val()}]`).attr("selected",true);
	
	//buscarProd(item);
}

function onBlurProducto_id(vlrcodigo){
	objvlrcodigo = $("#" + vlrcodigo["id"]);
	llenarDatosProd(objvlrcodigo);
	let i = objvlrcodigo.attr("item");
	$("#qtyitem" + i).focus();
	//console.log(vlrcodigo["id"]);
}
