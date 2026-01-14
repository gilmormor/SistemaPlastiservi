$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    /* $('#at_filefirmado').fileinput({
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
		$('#at_filefirmado').attr("data-initial-preview","");
		$("#imagen").val("");
        $("#at_filefirmado_deleted").val("1");
		//alert('entro');
	}).on('fileimageloaded', function(e, params) {
		//console.log('Paso');
		//console.log('File uploaded params', params);
		//console.log($('#at_filefirmado').val());
		$("#imagen").val($('#at_filefirmado').val());
        $("#at_filefirmado_deleted").val("1");
	}); */
    $('#at_fileimpresofoto').fileinput({
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
		$('#at_fileimpresofoto').attr("data-initial-preview","");
		$("#imagen").val("");
        $("#at_fileimpresofoto_deleted").val("1");
		//alert('entro');
	}).on('fileimageloaded', function(e, params) {
		//console.log('Paso');
		//console.log('File uploaded params', params);
		//console.log($('#at_fileimpresofoto').val());
		$("#imagen").val($('#at_fileimpresofoto').val());
        $("#at_fileimpresofoto_deleted").val("1");
	});
});

$("#btnAceptarAcuTecTemp").click(function(event)
{
	event.preventDefault();
	if(verificarDato(".valorrequerido"))
	{
		var data = {};
		$(".form_acutec").serializeArray().map(function(x){data[x.name] = x.value;});
		arrayat_certificados = $("#at_certificados").val();
        data.acuerdotecnico_idEditAct = $("#acuerdotecnico_id").val();
		data.at_certificados = arrayat_certificados.toString();
		//console.log(data);
		localStorage.setItem('datos', JSON.stringify(data));
		var guardado = localStorage.getItem('datos');
		aux_nfila = $("#acuerdotecnico_id").val();
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
					aux_botones = { 
						cancel: "Cerrar",
						verAT: {
							text: "Ver AT",
							value: "verAT",
						}
					};
					
					if(datatemp.at_impreso == 1){
						/* aux_botones.verCliche = {
							text: "Ver Cliché",
							value: "verCliche",
						}; */

						aux_botones.CrearProdImp = {
							text: "Editar Producto nuevo cliché",
							value: "CrearProdImp",
						};
						if ($("#btncrearAT").length === 0) {
							$("#contenedorBotonesAT").append(`
								<button type="button" id="btncrearAT" class="btn btn-primary" title="Modificar Acuerdo Tecnico?" onclick="seguirProcesoCrearAT()" data-dismiss="modal">
									Editar AT
								</button>
							`);
						}
					}else{
						// Si existe, lo elimino
    					//$("#btncrearAT").remove();
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
							});
					}
					
					//mostrarVentAtExiste(respuesta,"myModalAcuerdoTecnico");
					crearTablaListaAT(respuesta);
					$("#titulomyMostrarAtCliche").html("Acuerdo Técnico ya existe! <span class='glyphicon glyphicon-info-sign has-warning' style='bottom: 0px;top: 2px;'></span>");
					$("#myMostrarAtCliche").modal('show');
						
				}else{
					seguirProcesoCrearAT();
					/* $("#myModalAcuerdoTecnico").modal('hide');
					$("#acuerdotecnico" + datatemp.nfila).val(datatemp.objtxt); //ACTUALIZO EN LA TABLA EL VALOR DEL CAMPO ACUERDO TECNICO
					$("#icoat" + datatemp.nfila).attr('class','fa fa-cog text-aqua');
					$("#nombreProdTD" + datatemp.nfila).html($("#at_desc").val());
					if($("#at_impreso").val() == 1){
						$("#divMostrarImagenat" + datatemp.nfila).css({'display':'inline'});
					}else{
						$("#divMostrarImagenat" + datatemp.nfila).css({'display':'none'});
						$("#at_imagen" + datatemp.nfila).val("");
						$("#imagen" + datatemp.nfila).val("");
					} */
				}
			}
		},
		error: function () {
		}
	});
}

function seguirProcesoCrearAT(){
	$("#myModalAcuerdoTecnico").modal('hide');
	$("#acuerdotecnico" + datatemp.nfila).val(datatemp.objtxt); //ACTUALIZO EN LA TABLA EL VALOR DEL CAMPO ACUERDO TECNICO
	$("#icoat" + datatemp.nfila).attr('class','fa fa-cog text-aqua');
	$("#nombreProdTD" + datatemp.nfila).html($("#at_desc").val());
	if($("#at_impreso").val() == 1){
		$("#divMostrarImagenat" + datatemp.nfila).css({'display':'inline'});
	}else{
		$("#divMostrarImagenat" + datatemp.nfila).css({'display':'none'});
		$("#at_imagen" + datatemp.nfila).val("");
		$("#imagen" + datatemp.nfila).val("");
	}
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