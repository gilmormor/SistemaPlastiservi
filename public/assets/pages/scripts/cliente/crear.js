$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
	$(".numericopositivosindec").numeric({decimalPlaces: 2, negative : false });
    $( "#rut" ).focus();
    //$("#rut").numeric();
	$( "#myModal" ).draggable({opacity: 0.35, handle: ".modal-header"});
	/*
	$(".modal-body label").css("margin-bottom", -2);
	$(".help-block").css("margin-top", -2);
	$('.form-group').css({'margin-bottom':'0px','margin-left': '0px','margin-right': '0px'});
	*/
	//alert($("#aux_sta").val());
	if($("#mostrarguiasfacturas").val()=="1"){
		$("#mostrarguiasfacturasT").prop("checked", true);
	}else{
		$("#mostrarguiasfacturasT").prop("checked", false);
	}
	aux_obs = $("#aux_observaciones").val();
	$("#observaciones").val(aux_obs);
});

$("#mostrarguiasfacturasT").click(function(event)
{
    if($("#mostrarguiasfacturasT").prop("checked")){
		$("#mostrarguiasfacturas").val('1');
	}else{
		$("#mostrarguiasfacturas").val('0');
	}
});

function formato_rut(rut)
{
    var sRut1 = rut.value;      //contador de para saber cuando insertar el . o la -
    var nPos = 0; //Guarda el rut invertido con los puntos y el guión agregado
    var sInvertido = ""; //Guarda el resultado final del rut como debe ser
    var sRut = "";
    for(var i = sRut1.length - 1; i >= 0; i-- )
    {
        sInvertido += sRut1.charAt(i);
        if (i == sRut1.length - 1 )
            sInvertido += "-";
        else if (nPos == 3)
        {
            sInvertido += ".";
            nPos = 0;
        }
        nPos++;
    }
    for(var j = sInvertido.length - 1; j>= 0; j-- )
    {
        if (sInvertido.charAt(sInvertido.length - 1) != ".")
            sRut += sInvertido.charAt(j);
        else if (j != sInvertido.length - 1 )
            sRut += sInvertido.charAt(j);
    }
    //Pasamos al campo el valor formateado
    //rut.value = sRut.toUpperCase();
}

function eliminarFormatoRut(rut){
    var rut1 = rut.value;
    var rutR = "";
    for(i=0; i<=rut1.length ; i++){
        if(!isNaN(rut1[i])){
            rutR = rutR + rut1[i]
        }
    }
    //$("#rut").val(rutR);
}

$("#botonNuevaDirec").click(function(event)
{
    event.preventDefault();
    limpiarInputOT();
	quitarverificar();
	$("#aux_sta").val('1')
    $("#myModal").modal('show');
    $("#direcciondetalleM").focus();
});
$("#btnGuardarM").click(function(event)
{
    event.preventDefault();
	if(verificar())
	{
		//alert('Guardar');
		if($("#aux_sta").val()=="1"){
			insertarTabla();
		}else{
			modificarTabla($("#aux_numfila").val());
		}
		
		$("#myModal").modal('hide');
	}else{
		alertify.error("Falta incluir informacion");
	}
});

function modificarTabla(i){
	//alert($("#sucursal_idM").val());
	$("#aux_sta").val('0')
	$("#labeldir"+i).html($("#direcciondetalleM").val());
	$("#direcciondetalle"+i).val($("#direcciondetalleM").val());
	$("#region_id"+i).val($("#region_idM").val());
	$("#provincia_id"+i).val($("#provincia_idM").val());
	$("#comuna_id"+i).val($("#comuna_idM").val());
}

function insertarTabla(){
	//aux_nfila = 1; 
	var aux_nfila = $("#tabla-data tbody tr").length;
	aux_nfila++;
	//alert(aux_nfila);
    var htmlTags = '<tr name="fila'+ aux_nfila + '" id="fila'+ aux_nfila + '">'+
		'<td>'+ 
			'0'+
			'<input type="text" name="direccion_id[]" id="direccion_id'+ aux_nfila + '" class="form-control" value="0" style="display:none;"/>'+
		'</td>'+
		'<td>'+ 
			$("#direcciondetalleM").val()+
            '<input type="text" name="direcciondetalle[]" id="direcciondetalle'+ aux_nfila + '" class="form-control" value="'+ $("#direcciondetalleM").val() +'" style="display:none;"/>'+
        '</td>'+
        '<td style="display:none;">' +
            '<input type="text" name="region_id[]" id="region_id'+ aux_nfila + '" class="form-control" value="'+ $("#region_idM").val() +'"/>'+
        '</td>'+
        '<td style="display:none;">' + 
            '<input type="text" name="provincia_id[]" id="provincia_id'+ aux_nfila + '" class="form-control" value="'+ $("#provincia_idM").val() +'" style="display:none;"/>'+
        '</td>'+
        '<td style="display:none;">' + 
            '<input type="text" name="comuna_id[]" id="comuna_id'+ aux_nfila + '" class="form-control" value="'+ $("#comuna_idM").val() +'" style="display:none;"/>'+
        '</td>'+
		'<td>' + 
			'<a class="btn-accion-tabla tooltipsC" title="Editar este registro" onclick="editarRegistro('+ aux_nfila +')">'+
			'<i class="fa fa-fw fa-pencil"></i>'+
			'</a>'+
			'<button type="submit" class="btn-accion-tabla eliminar tooltipsC" title="Eliminar este registro">'+
				'<i class="fa fa-fw fa-trash text-danger"></i>'+
			'</button>'+
        '</td>'+
    '</tr>';
    $('#tabla-data tbody').append(htmlTags);
	/*
	'<a onclick="agregarFila('+ aux_nfila +')" class="btn-accion-tabla" title="Eliminar" data-original-title="Eliminar" id="agregar_reg'+ aux_nfila + '" name="agregar_reg'+ aux_nfila + '" valor="fa-minus">'+
	'<i class="fa fa-fw fa-minus"></i>'+
	'</a>'+
	*/

}



/*
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
*/
$('#comunap_id').on('change', function () {
	$("#regionp_id").val($('#comunap_id option:selected').attr("region_id"));
	$("#provinciap_id").val($('#comunap_id option:selected').attr("provincia_id"));
	$(".selectpicker").selectpicker('refresh');
});

$('#comuna_idM').on('change', function () {
	$("#region_idM").val($('#comuna_idM option:selected').attr("region_id"));
	$("#provincia_idM").val($('#comuna_idM option:selected').attr("provincia_id"));
	$(".selectpicker").selectpicker('refresh');
});


//VALIDACION DE CAMPOS
function limpiarInputOT(){
	$("#direcciondetalleM").val('');
	$("#region_idM").val('');
	/*$("#provincia_idM").val('');
	$("#provincia_idM").empty();
	$("#comuna_idM").val('')
	$("#comuna_idM").empty();*/
    $(".selectpicker").selectpicker('refresh');
}

function verificar()
{
	var v1=0,v2=0,v3=0,v4=0,v5=0,v6=0,v7=0,v8=0,v9=0,v10=0,v11=0,v12=0,v13,v14=0;
	v5=validacion('comuna_idM','combobox','col-xs-12 col-sm-12');
	v3=validacion('provincia_idM','combobox','col-xs-12 col-sm-12');
	v2=validacion('region_idM','comboboxmult','col-xs-12 col-sm-12');
	v1=validacion('direcciondetalleM','texto','col-xs-12 col-sm-12');

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
	quitarValidacion('direcciondetalleM','texto','col-xs-12 col-sm-12');
	quitarValidacion('region_idM','combobox','col-xs-12 col-sm-12');
	quitarValidacion('provincia_idM','combobox','col-xs-12 col-sm-12');
	quitarValidacion('comuna_idM','combobox','col-xs-12 col-sm-12');
}

function validacion(campo,tipo,columnas)
{
	var a=0;
	//columnas = $('#'+campo).parent().parent().attr("class");
	switch (tipo) 
	{ 
		case "texto": 
			codigo = document.getElementById(campo).value;
			if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo) ) {
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().attr("class", columnas+" has-error has-feedback");
				$('#'+campo).parent().children('span').text("Campo obligatorio").show();
				$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback check'></span>");
				$('#'+campo).focus();
				return false;
			}
			else
			{
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().attr("class", columnas+" has-success has-feedback");
				$('#'+campo).parent().children('span').hide();
				$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'></span>");
				return true;
			}

		break 
		case "numerico": 
			codigo = document.getElementById(campo).value;
			cajatexto = document.getElementById(campo).value;
			var caract = new RegExp(/^[0-9]+$/);

			if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo) ) {
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().parent().attr("class", columnas+" has-error has-feedback");
				$('#'+campo).parent().children('span').text("Campo obligatorio").show();
				$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback'></span>");
				$('#'+campo).focus();
				return false;
			}
			else
			{
				if(caract.test(cajatexto) == false)
				{
					$("#glypcn"+campo).remove();
					$('#'+campo).parent().parent().attr("class", columnas+" has-error has-feedback");
					$('#'+campo).parent().children('span').text("Solo permite valores numericos").show();
					$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback'></span>");
					$('#'+campo).focus();
					return false;
				}else
				{
					$("#glypcn"+campo).remove();
					$('#'+campo).parent().parent().attr("class", columnas+" has-success has-feedback");
					$('#'+campo).parent().children('span').hide();
					$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'></span>");
					return true;				
				}
			}

		break 
		case "combobox": 
			codigo = document.getElementById(campo).value;
			if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo) ) {
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().parent().attr("class", columnas+" has-error has-feedback");
				$('#'+campo).parent().parent().children('span').text("Campo obligatorio").show();
				$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback check'></span>");
				$('#'+campo).focus();
				return false;
			}
			else
			{
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().parent().attr("class", columnas+" has-success has-feedback");
				$('#'+campo).parent().parent().children('span').hide();
				$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'></span>");
				return true;
			}

		break
		case "comboboxmult": 
			codigo = document.getElementById(campo).value;
			if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo) ) {
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().parent().attr("class", columnas+" has-error has-feedback");
				$('#'+campo).parent().parent().children('span').text("Campo obligatorio").show();
				$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback check'></span>");
				$('#'+campo).focus();
				return false;
			}
			else
			{
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().parent().attr("class", columnas+" has-success has-feedback");
				$('#'+campo).parent().parent().children('span').hide();
				$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'></span>");
				return true;
			}

		break 
		case "email": 
			cajatexto = document.getElementById(campo).value;
			var caract = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/);
			if( cajatexto == null || cajatexto.length == 0 || /^\s+$/.test(cajatexto) )
			{
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().parent().attr("class", columnas+" has-error has-feedback");
				$('#'+campo).parent().children('span').text("Campo obligatorio").show();
				$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback'></span>");
				$('#'+campo).focus();
				return false;
			}
			else
			{
				if(caract.test(cajatexto) == false)
				{
					$("#glypcn"+campo).remove();
					$('#'+campo).parent().parent().attr("class", columnas+" has-error has-feedback");
					$('#'+campo).parent().children('span').text("Correo no valido").show();
					$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback'></span>");
					$('#'+campo).focus();
					return false;
				}else
				{
					$("#glypcn"+campo).remove();
					$('#'+campo).parent().parent().attr("class", columnas+" has-success has-feedback");
					$('#'+campo).parent().children('span').hide();
					$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'></span>");
					return true;				
				}
			} 
		break 
		default: 
		
	}
}

function quitarValidacion(campo,tipo,columnas)
{
	var a=0;
	//columnas = $('#'+campo).parent().parent().attr("class");
	switch (tipo) 
	{ 
		case "texto": 
			$("#glypcn"+campo).remove();
			$('#'+campo).parent().attr("class", columnas);
			$('#'+campo).parent().children('span').hide();
			$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon'></span>");
			return true;
		break 
		case "numerico": 
			codigo = document.getElementById(campo).value;
			$("#glypcn"+campo).remove();
			$('#'+campo).parent().parent().attr("class", columnas);
			$('#'+campo).parent().children('span').hide();
			$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon'></span>");
			return true;

		break 
		case "combobox": 
			$("#glypcn"+campo).remove();
			$('#'+campo).parent().parent().attr("class", columnas);
			$('#'+campo).parent().parent().children('span').hide();
			$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon'></span>");
			return true;

		break 
		case "email": 
			$("#glypcn"+campo).remove();
			$('#'+campo).parent().parent().attr("class", columnas);
			$('#'+campo).parent().children('span').hide();
			$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon'></span>");
		break 
		default: 
		
	}
}

function editarRegistro(i){
	//alert($("#direccion"+i).val());
	//event.preventDefault();
    limpiarInputOT();
	quitarverificar();
	$("#aux_sta").val('0');

	$("#aux_numfila").val(i);
	$("#direcciondetalleM").val($("#direcciondetalle"+i).val());
	/*$("#provincia_idM").empty();
	$("#comuna_idM").empty();*/
	
	$("#comuna_idM").val($("#comuna_id"+i).val());
	$("#region_idM").val($("#region_id"+i).val());
	$("#provincia_idM").val($("#provincia_id"+i).val());
	//llenarProvincia("#region_id"+i,i);
	
	//$("#sucursal_idM").val($("#sucursal_id"+i).val());
	$(".selectpicker").selectpicker('refresh');
    $("#myModal").modal('show');
    $("#direcciondetalleM").focus();
}

function eliminarRegistro(i){
	//alert($('input[name=_token]').val());
	var data = {
		direccion_id: $("#direccion_id"+i).val(),
		nfila : i
	};
	var ruta = '/cliente/eliminarClienteDirec/'+i;
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
				if (respuesta.mensaje == "ok") {
					$("#fila"+data['nfila']).remove();
					Biblioteca.notificaciones('El registro fue eliminado correctamente', 'Plastiservi', 'success');
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
		},
		error: function () {
		}
	});
}

$("#rut").blur(function(){
	var str = $(this).val(); 
	if(str!=$("#aux_rut").val()){
		//alert(str.charAt(str.length - 1));
		if(!dgv(str.substr(0, str.length-1))){
			swal({
				title: 'Dígito verificador no es Válido.',
				text: "",
				icon: 'warning',
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
			/*
			if(!validarRut($("#rut").val())){
				swal({
					title: 'RUT no es Válido.',
					text: "",
					icon: 'warning',
					buttons: {
						confirm: "Aceptar"
					},
				}).then((value) => {
					if (value) {
						//ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
						$("#rut").focus();
					}
				});	
				return 0;
			}
			*/
			codigo = $("#rut").val();
			//limpiarCampos();
			if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
			{
				//totalizar();
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
							swal({
								title: 'Cliente ya existe.',
								text: "Razón Social: " + respuesta[0]['razonsocial'],
								icon: 'warning',
								buttons: {
									confirm: "Aceptar"
								},
							}).then((value) => {
								if (value) {
									//ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
									$("#rut").focus();
								}
							});
						}else{
							$.ajax({
								url: '/clientetemp/buscarCliTemp',
								type: 'POST',
								data: data,
								success: function (respuesta) {
									if(respuesta.length>0){
										aux_contiz = "";
										for (var i = 0; i < (respuesta.length - 1); i++) {
											aux_contiz = aux_contiz + respuesta[i].cotizacion_id + ",";
										}
										aux_contiz = aux_contiz + respuesta[i].cotizacion_id + ".";
										swal({
											title: "Cliente temporal",
											text: "Cliente temporal debe ser validado en el Menú: Archivos Maestros->Clientes->Validar Cliente. Tomar en cuenta que para validar un cliente temporal la cotizacion debe estar aprobada." + "\nCotizacion Nro: " + aux_contiz,
											icon: 'warning',
											buttons: {
												confirm: "Aceptar"
											},
										}).then((value) => {
											if (value) {
												//ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
												$("#rut").focus();
											}
										});
									}else{
										//alert('entro');
									}
								}
							});
						}
					}
				});
			}
		}		
	}
});

function dgv(T)    //digito verificador
{  
      var M=0,S=1;
	  for(;T;T=Math.floor(T/10))
      S=(S+T%10*(9-M++%6))%11;
	  //return S?S-1:'k';
      
	  aux_digver=(S?S-1:'K');
	  var str = $("#rut").val(); 
	  aux_ultdig=(str.charAt(str.length - 1));
	  if(aux_digver==aux_ultdig){
		  return true;
	  }
	  return false;
 }