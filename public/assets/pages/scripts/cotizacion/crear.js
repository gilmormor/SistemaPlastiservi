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
	$("#precioM").numeric({decimalPlaces: 3});
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
		/*
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
			*/
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
		/* San Bernardo Esto sustituido por la funcion cargardatospantprod();
		$(this).val("");
		$(".input-sm").val('');
		data = datos();
		$('#tabla-data-productos').DataTable().ajax.url( "productobuscarpage/" + data.data2 + "&producto_id=" ).load();
		*/

		cargardatospantprod();
	
		//$("#myModal").modal('hide');
		//$("#myModalBuscarProd").modal('show');

		$('#myModal')
			.modal('hide')
			.on('hidden.bs.modal', function (e) {
				$('#myModalBuscarProd').modal('show');

				$(this).off('hidden.bs.modal'); // Remove the 'on' event binding
			});

	});

	$("#VerAcuTec").change(function(){
		cargardatospantprod();
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

//CAPTURE DE PANTALLA Y GENERAR PDF
/*
const $boton = document.querySelector("#create_pdf"), // El botón que desencadena
$objetivo = $("#acuerdotecnicotemp"); //document.body, // A qué le tomamos la foto
$contenedorCanvas = document.querySelector("#contenedorCanvas"); // En dónde ponemos el elemento canvas

// Agregar el listener al botón
$boton.addEventListener("click", () => {
html2canvas($objetivo) // Llamar a html2canvas y pasarle el elemento
  .then(canvas => {
	// Cuando se resuelva la promesa traerá el canvas
	$contenedorCanvas.appendChild(canvas); // Lo agregamos como hijo del div
  },
  windowHeight = 1000,
  );
});
*/

/*
var form = $('#acuerdotecnico'),  
cache_width = form.width(),  
a4 = [595.28, 841.89]; // for a4 size paper width and height  

$('#create_pdf1').on('click', function () {  
   $('body').scrollTop(0);
   createPDF(); 
   //pruebaDivAPdf();
});  

//create pdf  
function createPDF() {  
	getCanvas().then(function (canvas) {  
		var  
		 img = canvas.toDataURL("image/png"),  
		 doc = new jsPDF({  
			 unit: 'px',  
			 format: 'a4'  
		 });  
		doc.addImage(img, 'JPEG', 20, 20);  
		doc.save('Bhavdip-html-to-pdf.pdf');  
		form.width(cache_width);  
	});  
}  

// create canvas object  
function getCanvas() {  
	form.width((a4[0] * 1.33333) - 80).css('max-width', 'none');  
	return html2canvas(form, {  
		imageTimeout: 2000,  
		removeContainer: true  
	});  
}  

function pruebaDivAPdf() {
	var pdf = new jsPDF('p', 'pt', 'letter');
	source = $('#imprimir')[0];

	specialElementHandlers = {
		'#bypassme': function (element, renderer) {
			return true
		}
	};
	margins = {
		top: 80,
		bottom: 60,
		left: 40,
		width: 522
	};

	pdf.fromHTML(
		source, 
		margins.left, // x coord
		margins.top, { // y coord
			'width': margins.width, 
			'elementHandlers': specialElementHandlers
		},

		function (dispose) {
			pdf.save('Prueba.pdf');
		}, margins
	);
}
*/
function insertarTabla(){
	$("#trneto").remove();
	$("#triva").remove();
	$("#trtotal").remove();
	//aux_nfila = 1; 
	aux_nfila = 0;
	$("#tabla-data tr .filaproducto_id").each(function() {
		fila = $(this).attr('fila') ;
		aux_nfila = Number(fila);
	});
	//var aux_nfila = $("#tabla-data tbody tr").length;
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
		if($("#precioM").val()>0){
			aux_precioxkilo = $("#precioM").attr("valor");
			aux_precioxkiloreal = $("#precioM").attr("valor");
		}
	}
	if($("#unidadmedida_idM option:selected").attr('value') == 7){
		aux_precioxkilo = $("#precioM").attr("valor");
		aux_precioxkiloreal = $("#precioM").attr("valor");
	}
	//alert($("#tipoprodM").attr('valor'));
	aux_botonAcuTec = '';
	if($("#tipoprodM").attr('valor') == 1) {
		aux_botonAcuTec = ' <a class="btn-accion-tabla tooltipsC" title="Editar Acuerdo tecnico" onclick="crearEditarAcuTec('+ aux_nfila +')">'+
		'<i id="icoat' + aux_nfila + '" class="fa fa-cog text-red girarimagen"></i> </a>' +
		'<div id="divMostrarImagenat'+ aux_nfila + '" name="divMostrarImagenat'+ aux_nfila + '" style="display:none;">' +
			'<a class="btn-accion-tabla tooltipsC" title="Arte Acuerdo Técnico" onclick="ocultarMostrarFiltro('+ aux_nfila + ')">' +
				'<i id="btnmostrarocultar'+ aux_nfila + '" class="fa fa-plus"></i>' +
			'</a>' +
			'<div id="div_at_imagen'+ aux_nfila + '" name="div_at_imagen'+ aux_nfila + '" style="display: none;">' +
				'<input type="file" name="at_imagen'+ aux_nfila + '" id="at_imagen'+ aux_nfila + '" class="form-control at_imagen" accept="*"/>' +
				'<input type="hidden" name="imagen'+ aux_nfila + '" id="imagen'+ aux_nfila + '" value="">' +
			'</div>' +
		'</div>';
	}
	//aux_botonAcuTec = $("#tipoprodM").attr('valor') == '1' ? 'x' : '';
	let aux_productoId = $("#producto_idM").val();
	let aux_acuerdotecnicoId = $("#acuerdotecnico_id").val();
	let aux_clienteId = $("#cliente_id").val();
	if(aux_acuerdotecnicoId > 0){
		aux_productoId = `<a class="btn-accion-tabla btn-sm tooltipsC" title="" onclick="genpdfAcuTec(${aux_acuerdotecnicoId},${aux_clienteId},1)" data-original-title="Acuerdo Técnico PDF" aria-describedby="tooltip895039">
							${aux_productoId}
						</a>`;
	}
	$("#producto_idTDT"+aux_nfila).html(aux_productoId + aux_botonAcuTec);


    var htmlTags = '<tr name="fila'+ aux_nfila + '" id="fila'+ aux_nfila + '" class="prod_id' + $("#producto_idM").val() + '">'+
			'<td name="producto_idTDT'+ aux_nfila + '" id="producto_idTDT'+ aux_nfila + '" style="text-align:center;" categoriaprod_id="' + $("#categoriaprod_id").val() + '" class="filaproducto_id" fila="'+ aux_nfila + '">'+ 
					aux_productoId + aux_botonAcuTec +
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
			'<td name="unidadmedida_nomnreTD'+ aux_nfila + '" id="unidadmedida_nomnreTD'+ aux_nfila + '">'+ 
				$("#unidadmedida_idM option:selected").html()+
			'</td>'+
			'<td name="nombreProdTD'+ aux_nfila + '" id="nombreProdTD'+ aux_nfila + '" categoriaprod_nombre="' + aux_nombre +'">'+ 
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
			'<td name="longTD'+ aux_nfila + '" id="longTD'+ aux_nfila + '" style="text-align:right">'+ 
				$("#largoM").attr('valor')+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="long[]" id="long'+ aux_nfila + '" class="form-control" value="'+ $("#largoM").attr('valor') +'" style="display:none;"/>'+
			'</td>'+
			'<td name="espesorTD'+ aux_nfila + '" id="espesorTD'+ aux_nfila + '" style="text-align:right">'+ 
				MASKLA($("#espesor1M").attr('valor'),3)+
			'</td>'+
			'<td style="text-align:right;display:none;">'+ 
				'<input type="text" name="espesor[]" id="espesor'+ aux_nfila + '" class="form-control" value="'+ $("#espesor1M").attr('valor') +'" style="display:none;"/>'+
				'<input type="text" name="ancho[]" id="ancho'+ aux_nfila + '" class="form-control" value="'+ $("#anchoM").attr('valor') +'" style="display:none;"/>'+
				'<input type="text" name="obs[]" id="obs'+ aux_nfila + '" class="form-control" value="'+ $("#obsM").val() +'" style="display:none;"/>'+
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
				MASKLA($("#precionetoM").attr("valor"),3) + //MASK(0, $("#precionetoM").attr("valor"), '-##,###,##0.00',1)+
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
				MASKLA($("#totalkilosM").attr("valor"),4) + //MASK(0, $("#totalkilosM").attr("valor"), '-##,###,##0.00',1)+
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
				'<a id="editarRegistro'+ aux_nfila + '" name="editarRegistro'+ aux_nfila + '" class="btn-accion-tabla tooltipsC" title="Editar este registro" onclick="editarRegistro('+ aux_nfila +',' + aux_acuerdotecnicoId + ')">'+
					'<i class="fa fa-fw fa-pencil"></i>'+
				'</a>'+
				'<a class="btn-accion-tabla eliminar tooltipsC" title="Eliminar este registro" onclick="eliminarRegistro('+ aux_nfila +')">'+
				'<i class="fa fa-fw fa-trash text-danger"></i></a>'+
			'</td>'+
		'</tr>'+
		'<tr id="trneto" name="trneto">'+
			'<td colspan="14" style="text-align:right"><b>Neto</b></td>'+
			'<td id="tdneto" name="tdneto" style="text-align:right">0,00</td>'+
		'</tr>'+
		'<tr id="triva" name="triva">'+
			'<td colspan="14" style="text-align:right"><b>IVA ' + $("#aux_iva").val() + '%</b></td>'+
			'<td id="tdiva" name="tdiva" style="text-align:right">0,00</td>'+
		'</tr>'+
		'<tr id="trtotal" name="trtotal">'+
			'<td colspan="14" style="text-align:right"><b>Total</b></td>'+
			'<td id="tdtotal" name="tdtotal" style="text-align:right">0,00</td>'+
		'</tr>';
	
	$('#tabla-data tbody').append(htmlTags);
	activarClases(aux_nfila);
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
			$("#fila"+data['nfila']).remove();
			totalizar();
			//ajaxRequest(data,ruta,'eliminar');
		}
	});
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
					//console.log(respuesta);
					/*
					Swal.fire({
						icon: 'error',
						title: 'Oops...',
						text: 'Something went wrong!',
						footer: '<a href="">Why do I have this issue?</a>'
					  })*/
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
							/*
							fila = $(this).closest("tr");
							form = $(this);
							id = fila.find('td:eq(0)').text();
							//alert(id);
							var data = {
								_token  : $('input[name=_token]').val(),
								id      : id
							};
							if (value) {
								ajaxRequest(data,form.attr('href')+'/'+id+'/anular','anular',form);
							}*/
						});
				}else{
					$("#myModalAcuerdoTecnico").modal('hide');
					$("#acuerdotecnico" + datatemp.nfila).val(datatemp.objtxt); //ACTUALIZO EN LA TABLA EL VALOR DEL CAMPO ACUERDO TECNICO
					//alert($("#acuerdotecnico" + i).val());
					$("#icoat" + datatemp.nfila).attr('class','fa fa-cog text-aqua');
					$("#nombreProdTD" + datatemp.nfila).html($("#at_desc").val());
					$("#diamextmmTD" + datatemp.nfila).html($("#at_ancho").val());
					$("#ancho" + datatemp.nfila).val($("#at_ancho").val());
					$("#longTD" + datatemp.nfila).html($("#at_largo").val());
					$("#espesorTD" + datatemp.nfila).html($("#at_espesor").val());
					$("#cla_nombreTD" + datatemp.nfila).html($("#at_claseprod_id option:selected").html());
					$("#unidadmedida_nomnreTD" + datatemp.nfila).html($("#at_unidadmedida_id option:selected").html());
					$("#unidadmedida_id" + datatemp.nfila).val($("#at_unidadmedida_id option:selected").val());
					//console.log($("#at_unidadmedida_id option:selected").html());
					//console.log($("#at_unidadmedida_id option:selected").val());
					//$("#editarRegistro" + datatemp.nfila).hide();
					//console.log($("#at_impreso").val());
					if($("#at_impreso").val() == 1){
						$("#divMostrarImagenat" + datatemp.nfila).css({'display':'inline'});
					}else{
						$("#divMostrarImagenat" + datatemp.nfila).css({'display':'none'});
						$("#at_imagen" + datatemp.nfila).val("");
						$("#imagen" + datatemp.nfila).val("");
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
			var data = {
				rut: $("#rut").val(),
				_token: $('input[name=_token]').val()
			};
			$.ajax({
				//url: '/cliente/buscarCliId',
				url: '/cliente/buscarClixVenRut',
				type: 'POST',
				data: data,
				success: function (respuesta) {
					//console.log(respuesta.sucursales);
					if((respuesta.cliente) && respuesta.cliente.length>0){
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
									//console.log(respuesta);
									swal({
										title: 'Cliente pertenece a otro Vendedor',
										text: "Cliente: " + respuesta[0].razonsocial + "\nVendedor: " + respuesta[0].vendedor_nombre,
										icon: 'warning',
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
													//$("#giro_idCTM").val(respuesta[0]['giro_id']);
													$("#giro_idCTM").val(1);
													//$("#giro").val(respuesta[0]['giro']);
													//$("#giroCTM").val(respuesta[0]['giro']);
													//$("#formapago_idCTM").val(respuesta[0]['formapago_id']);
													$("#formapago_idCTM").val(1);
													//$("#plazopago_idCTM").val(respuesta[0]['plazopago_id']);
													$("#plazopago_idCTM").val(1);
													$("#comunap_idCTM").val(respuesta[0]['comunap_id']);
													$("#provinciap_idCTM").val(respuesta[0]['provinciap_id']);
													$("#regionp_idCTM").val(respuesta[0]['regionp_id']);
													$("#contactonombreCTM").val(respuesta[0]['contactonombre']);
													//$("#contactoemailCTM").val(respuesta[0]['contactoemail']);
													//$("#contactotelefCTM").val(respuesta[0]['contactotelef']);
													//$("#finanzascontactoCTM").val(respuesta[0]['finanzascontacto']);
													//$("#finanzanemailCTM").val(respuesta[0]['finanzanemail']);
													//$("#finanzastelefonoCTM").val(respuesta[0]['finanzastelefono']);
													$("#sucursal_idCTM").val(respuesta[0]['sucursal_id']);
													$("#observacionesCTM").val(respuesta[0]['observaciones']);
													$("#regionp_idCTM").val($('#comunap_idCTM option:selected').attr("region_id"));
													$("#provinciap_idCTM").val($('#comunap_idCTM option:selected').attr("provincia_id"));
												
													$(".selectpicker").selectpicker('refresh');
													formato_rut($("#rut"));
													$("#myModalClienteTemp").modal('show');
												}else{
													//console.log(respuesta);
													swal({
														title: 'Cliente temporal pertenece a otro vendedor.',
														text: "Cliente: " + respuesta[0].razonsocial + "\nVendedor: " + respuesta[0]['vendedor_nombre'] + " " + respuesta[0]['vendedor_apellido'],
														icon: 'warning',
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
													icon: 'warning',
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
	aux_arrayATs = arrayAcuerdoTecnico();
	var data = {
		id    : $("#id").val(),
		valor : 3,
		obs   : $("#aprobobs").val(),
		updated_at : $("#updated_at").val(),
		arrayATs : aux_arrayATs,
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
			//alert($('input[name=_token]').val());
			aux_continuar = true;
			for(var i=0;i<aux_arrayATs.length;i++){
				//Recorrer array de AT para verificar si fue modificado alguno
				if(aux_arrayATs[i].at_id != undefined){
					//Esta campo dentro del array se crea cuando se modifica el AT
					//
					aux_continuar = false;
				}
			} 
			if(aux_continuar){
				$("#myModalaprobcot").modal('hide');
				ajaxRequest(data,ruta,'aprobarcotsup');	
			}else{
				swal({
					title: 'Se ha detectado modificación en AT, desea continuar ?',
					text: "Esta acción no se puede deshacer!",
					icon: 'warning',
					buttons: {
						cancel: "Cancelar",
						confirm: "Aceptar"
					},
				}).then((value) => {
					if (value) {
						$("#myModalaprobcot").modal('hide');
						ajaxRequest(data,ruta,'aprobarcotsup');	
					}
				});
			}

		}
	});



});

$("#btnrechazarM").click(function(event)
{
	event.preventDefault();
	if(verificarAproRech())
	{
		aux_arrayATs = arrayAcuerdoTecnico();
		var data = {	
			id    : $("#id").val(),
			valor : 4,
			obs   : $("#aprobobs").val(),
			updated_at : $("#updated_at").val(),
			arrayATs : aux_arrayATs,
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
				$("#myModalaprobcot").modal('hide');
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
	v15= true; //validacion('finanzastelefonoCTM','numerico');
	v14= true; //validacion('finanzanemailCTM','email');
	v13= true; //validacion('finanzascontactoCTM','texto');
	v12= true; //validacion('contactotelefCTM','numerico');
	v11= true; //validacion('contactoemailCTM','email');
	v10=validacion('contactonombreCTM','texto');
	v9=validacion('comunap_idCTM','combobox');
	v8= true; //validacion('plazopago_idCTM','combobox');
	v7= true; //validacion('formapago_idCTM','combobox');
	v6= true; //validacion('giroCTM','texto');
	v5= true; //validacion('giro_idCTM','combobox');
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
	//$("#giro_id").val($('#giro_idCTM').val());
	//$("#giro_idD").val($('#giro_idCTM').val());
	$("#giro_id").val(1);
	$("#giro_idD").val(1);
	//$("#giro").val($('#giroCTM').val());
	//$("#formapago_id").val($('#formapago_idCTM').val());
	//$("#formapago_idD").val($('#formapago_idCTM').val());
	//$("#plazopago_id").val($('#plazopago_idCTM').val());
	//$("#plazopago_idD").val($('#plazopago_idCTM').val());
	$("#formapago_id").val(1);
	$("#formapago_idD").val(1);
	$("#plazopago_id").val(1);
	$("#plazopago_idD").val(1);
	$("#comuna_id").val($('#comunap_idCTM').val());
	$("#comuna_idD").val($('#comunap_idCTM').val());
	$("#provincia_id").val($('#provinciap_idCTM').val());
	$("#region_id").val($('#regionp_idCTM').val());

	$("#contacto").val($('#contactonombreCTM').val());
	$("#sucursal_id").val($('#sucursal_idCTM').val());
	
	//$("#observacion").val($("#observacionesCTM").val())

	//$("#comuna_idD option[value='"+ respuesta[0]['comunap_id'] +"']").attr("selected",true);
	//$("#clientedirec_id option").remove();
	activar_controles();
	$('.select2').trigger('change');
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
		//console.log(data);
		localStorage.setItem('datos', JSON.stringify(data));
		var guardado = localStorage.getItem('datos');
		aux_nfila = $("#aux_numfilaAT").val();
		data.objtxt = guardado;
		data.nfila = aux_nfila;
		data._token = $('input[name=_token]').val();


		var ruta = '/acuerdotecnico/buscaratxcampos';
		ajaxRequest(data,ruta,'buscaratxcampos');
		return 0;
		$.ajax({
			url: '/acuerdotecnico/buscaratxcampos',
			type: 'POST',
			data: data,
			success: function (respuesta) {
				if(respuesta.length > 0){
					/*
					Swal.fire({
						icon: 'error',
						title: 'Oops...',
						text: 'Something went wrong!',
						footer: '<a href="">Why do I have this issue?</a>'
					  })*/
					swal({
						title: 'Acuerdo técnico ya existe',
						text: 'Producto Cod: ' + respuesta[0].producto_id + ', ' + respuesta[0].producto_nombre,
						icon: 'warning',
						buttons: {
							cancel: "Cerrar",
							//confirm: "Ver AT"
						},
						}).then((value) => {
							/*
							fila = $(this).closest("tr");
							form = $(this);
							id = fila.find('td:eq(0)').text();
							//alert(id);
							var data = {
								_token  : $('input[name=_token]').val(),
								id      : id
							};
							if (value) {
								ajaxRequest(data,form.attr('href')+'/'+id+'/anular','anular',form);
							}*/
						});
				}else{

				}
				//console.log(respuesta);
				/*
				if(respuesta['cont']>0){
					mostrardatosadUniMed(respuesta);
					if($("#invbodega_idM")){
						llenarselectbodega(respuesta);
						//console.log(respuesta);
						$("#invbodega_idM").val($("#invbodega_idTD"+i).val());
						$("#invbodega_idM").selectpicker('refresh');
						$("#stakilos").val(respuesta['stakilos']);
					}
				}*/
			}
		});
		

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
	//console.log($(this).prop('min'));
	validacion($(this).prop('name'),$(this).attr('tipoval'));
});

$(".valorrequerido").change(function(){
	//alert($(this).parent().attr('class'));
	validacion($(this).prop('name'),$(this).attr('tipoval'));
});

$(".form-horizontal").on("submit", function(event){
	var aux_nfila = $("#tabla-data tbody tr").length - 3;
	//aux_nfila++;
	$("#itemcompletos").val("1");
	let j=0;
	for (i = 1; i <= aux_nfila; i++) {
		//alert($("#acuerdotecnico" + i).val());
		//console.log($("#acuerdotecnico" + i).val());

		if($("#tipoprod" + i).val() != undefined){
			j++;
		}
		if($("#tipoprod" + i).val() == 1){
			if($("#acuerdotecnico" + i).val() == 0 || $("#acuerdotecnico" + i).val() == "null"  || $("#acuerdotecnico" + i).val() == ""){
				event.preventDefault();
				//alertify.error("Falta acuerdo tecnico Item N°: " + i);
				i = aux_nfila+1;
				$("#lblitemcompletos").html("Acuerdo técnico item:" + j);
				$("#itemcompletos").val("");
				break;
			}
			let acuerdotecnico = JSON.parse($("#acuerdotecnico" + i).val());
			if(acuerdotecnico.at_impreso == 1){
				let at_imagen = $("#at_imagen" + i).val();
				if($("#imagen" + i).val() == ""){
					if(at_imagen == 0 || at_imagen == "null" || at_imagen == null || at_imagen == ""){
						$("#lblitemcompletos").html("Arte Acuerdo técnico item:" + j);
						$("#itemcompletos").val("");
						break;
					}	
				}
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


function desvAnchoLargo(aux_valor){
	aux_desv = "";
	if(aux_valor > 0){
		switch(true) {
			case aux_valor <= 50:
				aux_desv = "±1 CM";
				break;
			case aux_valor > 50 && aux_valor <= 150:
				aux_desv = "±2 CM";
				break;
			default:
				aux_desv = "±3 CM";
				break;
		}	
	}
	return aux_desv;
}

function desvEspesor(aux_valor,aux_desc){
	aux_desv = "";
	if(aux_valor > 0){
		if(aux_desc == "Baja" || aux_desc == "Mezcla" || aux_desc == "PP"){
			switch(true) {
				case aux_valor >= 0.010 && aux_valor <= 0.040:
					aux_desv = "±2 µ";
					break;
				case aux_valor >= 0.041 && aux_valor <= 0.080:
					aux_desv = "±3 µ";
					break;
				case aux_valor >= 0.081 && aux_valor <= 0.090:
					aux_desv = "±4 µ";
					break;
				case aux_valor >= 0.091 && aux_valor <= 0.140:
					aux_desv = "±5 µ";
					break;
				//case aux_valor >= 0.141 && aux_valor <= 0.200:
				case aux_valor >= 0.141:
					aux_desv = "±7 µ";
					break;
			}		
		}else{
			switch(true) {
				case aux_valor >= 0.010 && aux_valor <= 0.013:
					aux_desv = "±1 µ";
					break;
				case aux_valor >= 0.014 && aux_valor <= 0.018:
					aux_desv = "±2 µ";
					break;
				case aux_valor >= 0.019 && aux_valor <= 0.030:
					aux_desv = "±3 µ";
					break;
				case aux_valor >= 0.031 && aux_valor <= 0.050:
					aux_desv = "±4 µ";
					break;
				case aux_valor >= 0.051:
					aux_desv = "±5 µ";
					break;
				}
		}
	}
	return aux_desv;

}

function iniciarFileinput(aux_nfila){
	$('#at_imagen' + aux_nfila).fileinput({
		language: 'es',
		allowedFileExtensions: ['jpg', 'jpeg', 'png', 'pdf'],
		maxFileSize: 400,
		initialPreview: [
			// PDF DATA
			//'/storage/imagenes/notaventa/'+$("#imagen").val(),
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
		$('#at_imagen' + aux_nfila).attr("data-initial-preview","");
		$("#imagen" + aux_nfila).val("");
		//alert('entro');
	}).on('fileimageloaded', function(e, params) {
		//console.log('Paso');
		//console.log('File uploaded params', params);
		//console.log($('#at_imagen' + aux_nfila).val());
		$("#imagen" + aux_nfila).val($('#at_imagen' + aux_nfila).val());
	});
}

function activarClases(aux_nfila){
	iniciarFileinput(aux_nfila);
}


function ocultarMostrarFiltro(aux_nfila){
	if($('#div_at_imagen'+ aux_nfila).css('display') == 'none'){
		//$('#botonD').attr("class", "glyphicon glyphicon-chevron-up");
		$('#botonD').attr("title", "Ocultar Filtros");
		$('#btnmostrarocultar' + aux_nfila).removeClass('fa-plus').addClass('fa-minus')
		iniciarFileinput(aux_nfila);
	}else{
		//$('#botonD').attr("class", "glyphicon glyphicon-chevron-down");
		$('#botonD').attr("title", "Mostrar Filtros");
		$('#btnmostrarocultar' + aux_nfila).removeClass('fa-minus').addClass('fa-plus')
	}
	$('#div_at_imagen'+ aux_nfila).slideToggle(500);
	if($("#aux_aprocot").val() != "0"){
		$(".file-caption-main").hide();		
	}
	$(".kv-file-remove").hide();
}

function embalajePlastiservi(){
	let aux_val = $("#at_embalajeplastservi").val();
	if(aux_val == "1" || aux_val == ""){
		$(".embalaje").prop("disabled", true);
		$(".embalaje").val("")
	}else{
		$(".embalaje").prop("disabled", false);
	}
}

function arrayAcuerdoTecnico(){
	var aux_nfila = $("#tabla-data tbody tr").length - 3;
	//aux_nfila++;
	var miArray = [];
	$("#itemcompletos").val("1");
	for (i = 1; i <= aux_nfila; i++) {
		if($("#tipoprod" + i).val() == 1){
			if($("#acuerdotecnico" + i).val() == 0 || $("#acuerdotecnico" + i).val() == "null"  || $("#acuerdotecnico" + i).val() == ""){
				i = aux_nfila+1;
				$("#lblitemcompletos").html("Acuerdo técnico item:" + j);
				$("#itemcompletos").val("");
				break;
			}
			let acuerdotecnico = JSON.parse($("#acuerdotecnico" + i).val());
			// Añadir el objeto al array
			miArray.push(acuerdotecnico);
		}
	}
	return miArray;
}