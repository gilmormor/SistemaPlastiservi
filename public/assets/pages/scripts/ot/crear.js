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

	//totalizar();
	var dateToday = new Date(); 
	$("#fechaestdesp").datepicker({
		language: "es",
		autoclose: true,
		minDate: dateToday,
		startDate: new Date(),
		todayHighlight: true
	}).datepicker("setDate");
	activarClases();
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
                    //genpdfFAC(respuesta.nrodocto,"_U");
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
				modulo_id : 15,
				_token: $('input[name=_token]').val()
			};
			$.ajax({
				//url: '/cliente/buscarCliId',
				url: '/cliente/buscarCliRut',
				type: 'POST',
				data: data,
				success: function (respuesta) {
					if(respuesta.cliente.length>0){
						if(respuesta.cliente[0].descripcion==null){
							formato_rut($("#rut"));

							$("#razonsocial").val(respuesta.cliente[0].razonsocial);
							$("#telefono").val(respuesta.cliente[0].telefono);
							$("#email").val(respuesta.cliente[0].email);
							$("#direccion").val(respuesta.cliente[0].direccion);
							

							$("#comuna_nombre").val(respuesta.cliente[0].comuna_nombre);


							$("#direccioncot").val(respuesta.cliente[0].direccion);
							$("#cliente_id").val(respuesta.cliente[0].id)
							$("#contacto").val(respuesta.cliente[0].contactonombre);
							$("#region_id").val(respuesta.cliente[0].regionp_id);
							$("#provincia_id").val(respuesta.cliente[0].provinciap_id);
							$("#comuna_id").val(respuesta.cliente[0].comunap_id);
							$("#comuna_idD").val(respuesta.cliente[0].comunap_id);

							$("#ids").val("1");
							agregarFila();
							
							$('#additem').show();
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


function verGD(nrodocto){
	genpdfGD(nrodocto,"","");
}


function blanquearDatos(){
	$("#razonsocial").val("");
	$("#telefono").val("");
	$("#email").val("");
	$("#direccion").val("");
	$("#comuna_nombre").val("");
	$("#direccioncot").val("");
	$("#cliente_id").val("")
	$("#contacto").val("");
	$("#region_id").val("");
	$("#provincia_id").val("");
	$("#comuna_id").val("");
	$("#comuna_idD").val("");
	$("#obs").val("");
	$("#oc_id").val("");
	$('.select2').trigger('change');
	$('#tabla-data tbody').html("");
	totalizar();
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
		'<td id="nroitem' + aux_nfila + '" name="nroitem' + aux_nfila + '" class="nroitem new-item" style="text-align:center;vertical-align:middle;">' +
			aux_nroitem +
		'</td>' +
		'<td style="text-align:center" name="producto_idTD' + aux_nfila + '" id="producto_idTD' + aux_nfila + '" >' +
			'<input type="text" name="vlrcodigo[]" id="vlrcodigo' + aux_nfila + '" onblur="onBlurProducto_id(this)" class="form-control numericofilanew' + aux_nfila + ' itemrequerido calcularkgprod inikgitem" value="" maxlength="4" onkeyup="buscarProdKeyUp(this,event)" style="text-align:right" item="' + aux_nfila +'" title="Código Producto (Presione F2 para buscar Producto)" at_peso="0"/>' +
			'<input type="text" name="producto_id[]" id="producto_id' + aux_nfila + '" class="form-control numerico" value="" maxlength="4" onkeyup="buscarProdKeyUp(this,event)" style="text-align:right;display:none;"/>' +
			'<input type="text" name="nrolindet[]" id="nrolindet' + aux_nfila + '" class="form-control" value="' + aux_nroitem + '" style="display:none;"/>' +
			'<input type="text" name="otdet_id[]" id="otdet_id' + aux_nfila + '" class="form-control" value="" style="display:none;"/>' +
		'</td>' +
		'<td name="cantTD' + aux_nfila + '" id="cantTD' + aux_nfila + '" style="text-align:right" class="subtotalcant" valor="">' +
			'<input type="text" name="cant[]" id="cant' + aux_nfila + '" class="form-control" value="0" style="display:none;" valor="0" fila="' + aux_nfila + '"/>' +
			'<input type="text" name="qtyitem[]" id="qtyitem' + aux_nfila + '" class="form-control numericofilanew' + aux_nfila + ' calsubtotalitem itemrequerido calcularkgprod" value="0" valor="0" valorini="" item="' + aux_nfila + '"style="text-align:right" title="Cantidad producto" maxlength="10"/>' +
		'</td>' +
		'<td name="unidadmedida_nombre' + aux_nfila + '" id="unidadmedida_nombre' + aux_nfila + '" valor="">' +
			'<input type="text" name="unidadmedidainp_id[]" id="unidadmedidainp_id' + aux_nfila + '" value="" style="display:none;"/>' +
			'<select id="unmditem' + aux_nfila + '" name="unmditem[]" class="form-control select2 itemrequerido" title="Unidad Medida" readonly disabled>' +
				$("#unidadmedida_id").html() +
			'</select>' +
		'</td>' +
		'<td name="nombreProdTDDir' + aux_nfila + '" id="nombreProdTDDir' + aux_nfila + '" valor="">' +
			'<input type="text" name="nmbitem[]" id="nmbitem' + aux_nfila + '" class="form-control itemrequerido" value="" title="Nombre Producto" readonly/>' +
			'<input type="text" name="dscitem[]" id="dscitem' + aux_nfila + '" class="form-control" value="" style="display:none;"/>' +
		'</td>' +
		'<td>' +
			'<input type="text" name="otdet_obs[]" id="otdet_obs' + aux_nfila + '" class="form-control" value="" maxlength="100"/>' +
		'</td>' +
		'<td name="kgTD' + aux_nfila + '" id="kgTD' + aux_nfila + '" style="text-align:right;" class="" valor="0">' +
			'<input type="text" name="totalkilos[]" id="totalkilos' + aux_nfila + '" class="form-control" value="0" style="display:none;" valor="0" fila="' + aux_nfila + '"/>' +
			'<input type="text" name="itemkg[]" id="itemkg' + aux_nfila + '" class="form-control numericofilanew' + aux_nfila + ' itemrequerido subtotalkg calsubtotalitem kgitemkeyup" value="0" valor="0" item="' + aux_nfila + '" style="text-align:right" maxlength="15"/>' +
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
		'<td name="preciounitTD' + aux_nfila + '" id="preciounitTD' + aux_nfila + '" style="text-align:right;display:none;">' +
			'<input type="text" name="prcitem[]" id="prcitem' + aux_nfila + '" class="form-control numerico calsubtotalitem itemrequerido1" value="" valor="" valorini="" item="' + aux_nfila + '" style="text-align:right" title="Precio Unitario"/>' +
		'</td>' +
		'<td style="display:none;" name="precioxkiloTD' + aux_nfila + '" id="precioxkiloTD' + aux_nfila + '" style="text-align:right">' +
		'</td>' +
		'<td style="text-align:right;display:none;">' +
			'<input type="text" name="precioxkilo[]" id="precioxkilo' + aux_nfila + '" class="form-control" value="0" style="display:none;"/>' +
		'</td>' +
		'<td style="text-align:right;display:none;">' +
			'<input type="text" name="precioxkiloreal[]" id="precioxkiloreal' + aux_nfila + '" class="form-control" value="0" style="display:none;"/>' +
		'</td>' +
		'<td name="subtotalFactDet' + aux_nfila + '" id="subtotalFactDet' + aux_nfila + '" class="subtotalFactDet" style="text-align:right;display:none;">' +
			'<input type="text" name="montoitem[]" id="montoitem' + aux_nfila + '" class="form-control numerico calpreciounit" value="0" valor="" valorini="" item="' + aux_nfila + '" style="text-align:right" readonly/>' +
		'</td>' +
		'<td name="subtotalSFTD' + aux_nfila + '" id="subtotalSFTD' + aux_nfila + '" class="subtotal" style="text-align:right;display:none;">' +
			'0' +
		'</td>' +
		'<td style="vertical-align:middle;">' + 
			'<a onclick="delitem('+ aux_nfila +')" class="btn-accion-tabla tooltipsC" title="Eliminar" id="delitem'+ aux_nfila + '" name="delitem'+ aux_nfila + '">'+
				'<i class="fa fa-fw fa-trash text-danger">'+
			'</a>'+
		'</td>'+
	'</tr>';
    $('#tabla-data tbody').append(htmlTags);
	activarClases(aux_nfila);
	totalizar();
	$("#foottotal").show();
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
	aux_qtyitem = $("#qtyitem" + i).val();
	aux_qtyitem = aux_qtyitem.replace(/\./g, '').replace(',', '.')

	aux_itemkg = $("#itemkg" + i).val();
	aux_itemkg = aux_itemkg.replace(/\./g, '').replace(',', '.')
	//console.log(aux_itemkg);


	let qtyitem = aux_qtyitem == "" ? 0 : parseFloat(aux_qtyitem);
	let prcitem = $("#prcitem" + i).val() == "" ? 0 : parseFloat($("#prcitem" + i).val());
	let kgitem = aux_itemkg == "" ? 0 : parseFloat(aux_itemkg);
	

	if(qtyitem == 0){
		$("#qtyitem" + i).val("");
	}
	if(prcitem == 0){
		$("#prcitem" + i).val("");
	}
	

	let aux_subtotal = qtyitem * prcitem;
	aux_subtotal = Math.round(aux_subtotal);
	$("#qtyitem" + i).attr("valor",qtyitem);
	$("#totalkilos" + i).attr("valor",kgitem);
	$("#totalkilos" + i).val(kgitem);
	$("#cant" + i).attr("valor",qtyitem);
	$("#cant" + i).val(qtyitem);
	$("#cantTD" + i).attr("valor",qtyitem);
	$("#prcitem" + i).attr("valor",$("#prcitem" + i).val());
	$("#montoitem" + i).val(aux_subtotal);
	$("#montoitem" + i).attr("valor",aux_subtotal);
	$("#subtotalSFTD" + i).html(aux_subtotal);
	$("#kgTD" + i).attr("valor",kgitem);
	//console.log("Unidad: " + $("#unmditem" + i).val());
	totalizar();
	$("#foottotal").show();
}

function calcularkgprod(name){
	let i = $(name).attr("item");
	let qtyitem = $("#qtyitem" + i).attr("valor") == "" ? 0 : parseFloat($("#qtyitem" + i).attr("valor"));
	if($("#unmditem" + i).val() == 7){
		$("#itemkg" + i).val(MASKLA(qtyitem,2));
		$("#itemkg" + i).attr("valor",qtyitem);
		$("#totalkilos" + i).val(qtyitem);
		$("#totalkilos" + i).attr("valor",qtyitem);

	}else{
		aux_at_peso = $("#vlrcodigo" + i).attr("at_peso");
		kgitem = parseFloat((qtyitem * aux_at_peso).toFixed(2));
		$("#itemkg" + i).val(MASKLA(kgitem,2));
		$("#itemkg" + i).attr("valor",kgitem);
		$("#totalkilos" + i).val(kgitem);
		$("#totalkilos" + i).attr("valor",kgitem);
	}
	/* if($("#itemkg" + i).attr("valor") == 0 || $("#itemkg" + i).attr("valor") == "0" || $("#itemkg" + i).attr("valor",qtyitem) == ""){
		$("#itemkg" + i).val("");
	} */
	//console.log(kgitem);
	totalizar();
	$("#foottotal").show();
}

function inikgitem(name){
	let i = $(name).attr("item");
	if($("#vlrcodigo" + i).val() != $("#vlrcodigo" + i).attr("producto_id")){
		$("#qtyitem" + i).val(0)
		$("#cantTD" + i).val(0)
		$("#cant" + i).val(0)
		
		$("#itemkg" + i).val("");
		$("#itemkg" + i).attr("valor",0);
		$("#totalkilos" + i).val(0);
		$("#totalkilos" + i).attr("valor",0);
	
	}
}


function numericoCeroaBlanco(name){
	let i = $(name).attr("item");
	let valor = $(name).val() == "" ? 0 : parseFloat($(name).val());

	if(valor == 0){
		$(name).val("");
	}
}

function activarClases(aux_nfila){
	$(".kgitemkeyup").keyup(function(){
		kgitemkeyup(this);
	});
	$(".numericofilanew" + aux_nfila).numeric();
	$(".numericofilanew" + aux_nfila).blur(function(e){
		if($(this).attr('valor') != undefined){
			$(this).attr('valor',$(this).val());
			//$(this).val(MASK(0, $(this).val(), '-###,###,###,##0.00',1));
			$(this).val(MASKLA($(this).val(),2));

		}
	});
	$(".numericofilanew" + aux_nfila).focus(function(e){
		if($(this).attr('valor') != undefined){
			$(this).val($(this).attr('valor'));
		}
	});

	$(".calsubtotalitem").blur(function(){
		calsubtotalitem(this)
	});
	$(".itemrequerido").change(function(){
		validarItemVacios();
	});
	$(".numericoCeroaBlanco").keyup(function(){
		numericoCeroaBlanco(this)
	});
	$(".calcularkgprod").blur(function(){
		calcularkgprod(this)
	});
	$(".inikgitem").blur(function(){
		inikgitem(this)
	});
	validarItemVacios();
}

function kgitemkeyup(name){
	let i = $(name).attr("item");
	let kgitem = $("#itemkg" + i).attr("valor") == "" ? 0 : parseFloat($("#itemkg" + i).attr("valor"));

	$("#itemkg" + i).attr("valor",kgitem);
	$("#totalkilos" + i).val(kgitem);
	$("#totalkilos" + i).attr("valor",kgitem);
	totalizar();
	$("#foottotal").show();
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

$(".form-horizontal").on("submit", function(event){
	//console.log($("#itemcompletos").val());
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
    var ruta = '/dtefacturadir/procesar';
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
			$("#foottotal").show();
		}
	});
}