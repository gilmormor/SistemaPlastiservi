$(document).ready(function () {
	//Biblioteca.validacionGeneral('form-general');
	var screen = $('#loading-screen');
    configureLoadingScreen(screen);
	
	$('#tabla-data-productos').DataTable( {
        "language": {
			"decimal": ",",
			"emptyTable": "No hay información",
			"info": "Mostrando _START_ a _END_ de _TOTAL_ Registros",
			"infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
			"infoFiltered": "(Filtrado de _MAX_ total registros)",
			"infoPostFix": "",
			"thousands": ".",
			"lengthMenu": "Mostrar _MENU_ registros",
			"loadingRecords": "Cargando...",
			"processing": "Procesando...",
			"search": "Buscar:",
			"zeroRecords": "Sin resultados encontrados",
			"paginate": {
				"first": "Primero",
				"last": "Ultimo",
				"next": "Siguiente",
				"previous": "Anterior"
			}

		},
		stateSave: false
	} );


	$('#tabla-data-productos tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Buscar '+title+'" />' );
    } );
 
    // DataTable
    var table = $('#tabla-data-productos').DataTable();
 
    // Apply the search
    table.columns().every( function () {
        var that = this;
 
        $( 'input', this.footer() ).on( 'keyup change clear', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
	} );
	
	$('#myModalpdf').on('show.bs.modal', function () {
		$('#myModalpdf .modal-body').css('height',$( window ).height()*0.75);
		});

	$('#myModalverpdf').on('show.bs.modal', function () {
		$('#myModalpdf .modal-body').css('height',$( window ).height()*0.75);
	});

	//*******************************************************************
	// Validar campos numericos de pantalla agregar_conveniosofitasa.php
    $('.numerico').numeric('.');
	$('.numerico4d').numeric('.');
    //*******************************************************************

	$(".numerico").blur(function(e){
		if($(this).attr('valor') != undefined){
			$(this).attr('valor',$(this).val());
			$(this).val(MASK(0, $(this).val(), '-###,###,###,##0.00',1));
		}
	});
	$(".numerico").focus(function(e){
		if($(this).attr('valor') != undefined){
			$(this).val($(this).attr('valor'));
		}
	});

	$(".numerico4d").blur(function(e){
		if($(this).attr('valor') != undefined){
			$(this).attr('valor',$(this).val());
			$(this).val(MASK(0, $(this).val(), '-###,###,##0.0000',1));
		}
	});
	$(".numerico4d").focus(function(e){
		if($(this).attr('valor') != undefined){
			$(this).val($(this).attr('valor'));
		}
	});
	$("#espesor1M").blur(function(e){
		$("#espesorM").val($("#espesor1M").val());
	});
	$("#largoM").blur(function(e){
		$("#longM").val($("#largoM").attr('valor'));
	});
	

/*
 	$(".numerico").on({
		"focus": function (event) {
			$(event.target).select();
		},
		"keyup": function (event) {
		$(event.target).val(function (index, value ) {
			return value.replace(/\D/g, "")
				.replace(/([0-9])([0-9]{2})$/, '$1,$2')
				.replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
		});
		}
	});
*/

});


function validacion(campo,tipo)
{
	var a=0;
	//columnas = $('#'+campo).parent().parent().attr("class");
	columnas = $('#'+campo).parent().attr("classorig");
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
		case "textootro": 
			columnas = $('#'+campo).parent().parent().attr("classorig");
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
		case "numerico": 
			codigo = document.getElementById(campo).value;
			cajatexto = document.getElementById(campo).value;
			var caract = new RegExp(/^[+]?([0-9]+(?:[\.][0-9]*)?|\.[0-9]+)$/);

			if( codigo == null || codigo==0 || codigo.length == 0 || /^\s+$/.test(codigo) ) {
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().attr("class", columnas+" has-error has-feedback");
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
					$('#'+campo).parent().attr("class", columnas+" has-error has-feedback");
					$('#'+campo).parent().children('span').text("Solo permite valores numericos").show();
					$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback'></span>");
					$('#'+campo).focus();
					return false;
				}else
				{
					$("#glypcn"+campo).remove();
					$('#'+campo).parent().attr("class", columnas+" has-success has-feedback");
					$('#'+campo).parent().children('span').hide();
					$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'></span>");
					return true;				
				}
			}
			case "numericootro": 
			columnas = $('#'+campo).parent().parent().attr("classorig");
			codigo = document.getElementById(campo).value;
			cajatexto = document.getElementById(campo).value;
			var caract = new RegExp(/^[0-9]+$/);

			if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo) ) {
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().parent().attr("class", columnas+" has-error has-feedback");
				$('#'+campo).parent().parent().children('span').text("Campo obligatorio").show();
				$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback'></span>");
				$('#'+campo).focus();
				return false;
			}
			else
			{
				if(caract.test(cajatexto) == false)
				{
					$("#glypcn"+campo).remove();
					$('#'+campo).parent().parent().attr("class", columnas+" has-error has-feedback");
					$('#'+campo).parent().parent().children('span').text("Solo permite valores numericos").show();
					$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback'></span>");
					$('#'+campo).focus();
					return false;
				}else
				{
					$("#glypcn"+campo).remove();
					$('#'+campo).parent().parent().parent().attr("class", columnas+" has-success has-feedback");
					$('#'+campo).parent().parent().children('span').hide();
					$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'></span>");
					return true;				
				}
			}

		break 
		case "combobox": 
			columnas = $('#'+campo).parent().parent().attr("classorig");
			codigo = document.getElementById(campo).value;
			//alert($('#'+campo + ' option:selected').text());
			//alert(campo);
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
			columnas = $('#'+campo).parent().parent().attr("classorig");
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
				$('#'+campo).parent().attr("class", columnas+" has-error has-feedback");
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
					$('#'+campo).parent().attr("class", columnas+" has-error has-feedback");
					$('#'+campo).parent().children('span').text("Correo no valido").show();
					$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback'></span>");
					$('#'+campo).focus();
					return false;
				}else
				{
					$("#glypcn"+campo).remove();
					$('#'+campo).parent().attr("class", columnas+" has-success has-feedback");
					$('#'+campo).parent().children('span').hide();
					$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'></span>");
					return true;				
				}
			} 
		break 
		default: 
			$("#glypcn"+campo).remove();
			$('#'+campo).parent().attr("class", columnas+"");
			$('#'+campo).parent().children('span').hide();
			//$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'></span>");
			return true;				

	}
}

function quitarValidacion(campo,tipo)
{
	var a=0;
	//columnas = $('#'+campo).parent().parent().attr("class");
	columnas = $('#'+campo).parent().attr("classorig");
	switch (tipo) 
	{ 
		case "texto": 
			$("#glypcn"+campo).remove();
			$('#'+campo).parent().attr("class", columnas);
			$('#'+campo).parent().children('span').hide();
			$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon'></span>");
			return true;
		break 
		case "textootro": 
			$("#glypcn"+campo).remove();
			$('#'+campo).parent().parent().attr("class", columnas);
			$('#'+campo).parent().parent().children('span').hide();
			$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon'></span>");
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
		case "numericootro": 
			columnas = $('#'+campo).parent().parent().attr("classorig");
			codigo = document.getElementById(campo).value;
			$("#glypcn"+campo).remove();
			$('#'+campo).parent().parent().parent().attr("class", columnas);
			$('#'+campo).parent().parent().children('span').hide();
			$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon'></span>");
			return true;

		break 
		case "combobox": 
			columnas = $('#'+campo).parent().parent().attr("classorig");
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


function quitarvalidacioneach(){
	$(".requeridos").each(function() {
		quitarValidacion($(this).prop('name'),$(this).attr('tipoval'));
	});
	blanquearcamposrequeridos();
}

function blanquearcamposrequeridos(){
	$(".requeridos").each(function() {
		$(this).val('');
	});
	$(".selectpicker").selectpicker('refresh');
}

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


function formato_rut(rut)
{
	var sRut1 = rut.val();      //contador de para saber cuando insertar el . o la -
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
	rut.val(sRut.toUpperCase());
}

function eliminarFormatoRut(rut){
    var rut1 = rut.val();
	var rutR = "";
    for(i=0; i<=rut1.length ; i++){
        if(!isNaN(rut1[i]) || rut1[i]=="K"){
            rutR = rutR + rut1[i]
        }
    }
    rut.val(rutR);
}


function llevarMayus(valor){
    //return valor.toUpperCase();
    valor.value = valor.value.toUpperCase();

}

function eliminarFormatoRutret(rut){
    var rut1 = rut;
	var rutR = "";
    for(i=0; i<=rut1.length ; i++){
        if(!isNaN(rut1[i]) || rut1[i]=="K"){
            rutR = rutR + rut1[i]
        }
	}
	return rutR;
}

//Llevar de milimetros a pulgadas 
function mmAPg(aux_valor){
	switch (aux_valor) {
		case "16":
			return '5/8"';
			break;
		case "20":
			return '1/2"';
			break;
		case "21.20":
			return '1/2"';
			break;
		case "25":
			return '3/8"';
			break;
		case "26.60":
			return '3/4"';
			break;
		case "32":
			return '1"';
			break;
		case "33.30":
			return '1"';
			break;
		case "40":
			return '1 1/4"';
			break;
		case "42":
			return '1 1/4"';
			break;
		case "48":
			return '1 1/2"';
			break;
		case "50":
			return '1 1/2"';
			break;
		case "60.20":
			return '2"';
			break;
		case "63":
			return '2"';
			break;
		case "72.80":
			return '2 1/2"';
			break;
		case "75":
			return '2 1/2"';
			break;
		case "88.70":
			return '3"';
			break;
		case "90":
			return '3"';
			break;
		case "110":
			return '4"';
			break;
		case "114.10":
			return '4"';
			break;
		case "125":
			return '4 1/2"';
			break;
		case "140":
			return '5"';
			break;
		case "160":
			return '6"';
			break;
		case "168":
			return '6"';
			break;
		case "180":
			return '7"';
			break;
		case "200":
			return '8"';
			break;
		case "218.70":
			return '8"';
			break;
		case "250":
			return '8"';
			break;
	default:
		return '';
	}
}

//FUNCIONES DE COTIZACION Y NOTA DE VENTA
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
	//alert(aux_peso);
	aux_tk = $("#cantM").val() * aux_peso;
	if($("#pesoM").val()>0){	
		$("#totalkilosM").val(MASK(0, aux_tk.toFixed(2), '-##,###,##0.00',1));
		$("#totalkilosM").attr('valor',aux_tk.toFixed(2));
	}
	//aux_total = ($("#cantM").val() * aux_peso * $("#precioM").val()) * ($("#descuentoM").val());
	aux_total = ($("#cantM").val() * $("#precionetoM").val()) * ($("#descuentoM").val());
	$("#subtotalM").val(MASK(0, aux_total.toFixed(2), '-#,###,###,##0.00',1));
	$("#subtotalM").attr('valor',aux_total.toFixed(2));
	aux_precdesc = $("#precioM").val() * $("#descuentoM").val();
//	$("#precioM").val(MASK(0, aux_precdesc, '-##,###,##0.00',1));
	$("#precioM").val(aux_precdesc);
	$("#precioM").attr('valor',aux_precdesc);

	aux_precioUnit = aux_precdesc * aux_peso;
	//$("#precionetoM").val(MASK(0, Math.round(aux_precioUnit), '-##,###,##0.00',1));
	$("#precionetoM").val(Math.round(aux_precioUnit));
	$("#precionetoM").attr('valor',Math.round(aux_precioUnit));
	if($("#unidadmedida_idM option:selected").attr('value') == 7){
		aux_cant = MASK(0, $("#cantM").val(), '-#,###,###,##0.00',1);
		$("#totalkilosM").val(aux_cant);
		$("#totalkilosM").attr('valor',$("#cantM").val());
	}

}

function insertarModificar(){
	if($("#aux_sta").val()=="1"){
		insertarTabla();
	}else{
		modificarTabla($("#aux_numfila").val());
	}
	$("#myModal").modal('hide');
}

function modificarTabla(i){
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
/*
	$("#longTD"+i).html($("#longM").val());
	$("#long"+i).val($("#longM").val());
*/
	$("#longTD"+i).html($("#largoM").attr('valor'));
	$("#long"+i).val($("#largoM").attr('valor'));
	$("#ancho"+i).val($("#anchoM").attr('valor'));
	$("#largo"+i).val($("#largoM").attr('valor'));
	$("#espesorTD"+i).html($("#espesor1M").attr('valor'));
	$("#espesor"+i).val($("#espesor1M").attr('valor'));
	$("#obs"+i).val($("#obsM").val());
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
	if($("#unidadmedida_idM option:selected").attr('value') == 7){
		aux_precioxkilo = $("#precioM").attr("valor");
	}
	$("#precioxkiloTD"+i).html(MASK(0, aux_precioxkilo, '-##,###,##0.00',1)); //$("#precioxkiloTD"+i).html(MASK(0, $("#precioM").val(), '-##,###,##0.00',1));
	$("#precioxkilo"+i).val(aux_precioxkilo);
	$("#totalkilosTD"+i).html(MASK(0, $("#totalkilosM").attr('valor'), '-##,###,##0.00',1));
	$("#totalkilos"+i).val($("#totalkilosM").attr('valor'));
	$("#subtotalCFTD"+i).html(MASK(0, $("#subtotalM").attr('valor'), '-#,###,###,##0.00',1));
	$("#subtotal"+i).val($("#subtotalM").attr('valor'));
	$("#subtotalSFTD"+i).html($("#subtotalM").attr('valor'));

	$("#unidadmedida_id"+i).val($("#unidadmedida_idM option:selected").attr('value'));
	totalizar();

}

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

function totalizar(){
	total_neto = 0;
	total_kg = 0;
	$("#tabla-data tr .subtotal").each(function() {
		valor = $(this).html() ;
		valorNum = parseFloat(valor);
		total_neto += valorNum;
	});
	$("#tabla-data tr .subtotalkg").each(function() {
		valor = $(this).html() ;
		valor = valor.replace(/,/g, ""); //Elimina comas al valor con formato
		//alert(valor);
		valorNum = parseFloat(valor);
		total_kg += valorNum;
	});
	aux_totalkgform = MASK(0, total_kg, '-##,###,##0.00',1)

	aux_porciva = $("#aux_iva").val()
	aux_porciva = parseFloat(aux_porciva);
	aux_iva = Math.round(total_neto * (aux_porciva/100));
	aux_total = total_neto + aux_iva;
	aux_netoform = MASK(0, total_neto, '-#,###,###,##0.00',1)
	aux_ivaform = MASK(0, aux_iva, '-#,###,###,##0.00',1)
	aux_tdtotalform = MASK(0, aux_total, '-#,###,###,##0.00',1)
	
	//$("#tdneto").html(total_neto.toFixed(2));
	$("#totalkg").html(aux_totalkgform);
	$("#tdneto").html(aux_netoform);
	$("#tdiva").html(aux_ivaform);
	$("#tdtotal").html(aux_tdtotalform);

	$("#neto").val(total_neto);
	$("#iva").val(aux_iva);
	if(aux_total == 0){
		$("#total").val("");
	}else{
		$("#total").val(aux_total);
	}
}

function totalizardespacho(){
	total_neto = 0;
	total_kg = 0;

	$("#tabla-data tr .subtotal").each(function() {
		valor = $(this).html() ;
		//alert(valor);
		valorNum = parseFloat(valor);
		total_neto += valorNum;
	});
	
	$("#tabla-data tr .subtotalkg").each(function() {
		valor = $(this).html() ;
		valor = valor.replace(/,/g, ""); //Elimina comas al valor con formato
		//alert(valor);
		valorNum = parseFloat(valor);
		total_kg += valorNum;
	});
	//alert(total_neto);
	aux_totalkgform = MASK(0, total_kg, '-##,###,##0.00',1)
	//alert(aux_totalkgform);
	aux_porciva = $("#aux_iva").val()
	aux_porciva = parseFloat(aux_porciva);
	aux_iva = Math.round(total_neto * (aux_porciva/100));
	aux_total = total_neto + aux_iva;
	aux_netoform = MASK(0, total_neto, '-#,###,###,##0.00',1)
	aux_ivaform = MASK(0, aux_iva, '-#,###,###,##0.00',1)
	aux_tdtotalform = MASK(0, aux_total, '-#,###,###,##0.00',1)
	
	//$("#tdneto").html(total_neto.toFixed(2));
	$("#totalkg").html(aux_totalkgform);
	$("#tdneto").html(aux_netoform);
	$("#tdiva").html(aux_ivaform);
	$("#tdtotal").html(aux_tdtotalform);

	$("#neto").val(total_neto);
	$("#iva").val(aux_iva);
	if(aux_total == 0){
		$("#total").val("");
	}else{
		$("#total").val(aux_total);
	}
}


$('.region_id').on('change', function () {
	llenarProvincia(this,0);
});

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
	$("#anchoM").val('');
	$("#anchoM").attr('valor','');
	$("#largoM").val('');
	$("#largoM").attr('valor','');
	$("#espesor1M").val('');
	$("#espesor1M").attr('valor','');
	$("#obsM").val('');
    $(".selectpicker").selectpicker('refresh');
}

function verificar()
{
	var v1=0,v2=0,v3=0,v4=0,v5=0,v6=0,v7=0,v8=0,v9=0,v10=0,v11=0,v12=0,v13,v14=0;
	
	v6=validacion('unidadmedida_idM','combobox');
	v5=validacion('precionetoM','texto');
	v4=validacion('precioM','numerico');
	v3=validacion('descuentoM','combobox');
	v2=validacion('cantM','numerico');
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
	
	//quitarValidacion('producto_idM','texto');
	quitarValidacion('descuentoM','combobox');
	quitarValidacion('cantM','texto');
	quitarValidacion('precioM','texto');
	quitarValidacion('precionetoM','texto');
	quitarValidacion('producto_idM','textootro');
	quitarValidacion('unidadmedida_idM','combobox');

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
	$("#precionetoM").val($("#preciounit"+i).val());
	//$("#precionetoM").val(MASK(0, $("#preciounit"+i).val(), '-##,###,##0.00',1));
	$("#precioM").attr('valor',$("#precioxkilo"+i).val());
	$("#precioM").val($("#precioxkilo"+i).val());
	//$("#precioM").val(MASK(0, $("#precioxkilo"+i).val(), '-##,###,##0.00',1));
	$("#totalkilosM").attr('valor',$("#totalkilos"+i).val());
	$("#totalkilosM").val(MASK(0, $("#totalkilos"+i).val(), '-#,###,###,##0.00',1));
	$("#subtotalM").attr('valor',$("#subtotal"+i).val());
	$("#subtotalM").val(MASK(0, $("#subtotal"+i).val(), '-#,###,###,##0.00',1));
	$("#cla_nombreM").val($.trim( $("#cla_nombreTD"+i).html() ));
	$("#tipounionM").val($("#tipounion"+i).val());
	$("#diamextmmM").val($("#diamextmm"+i).val());
	$("#espesorM").val($("#espesor"+i).val());
	$("#espesor1M").val($("#espesor"+i).val());
	$("#espesor1M").attr('valor',$("#espesor"+i).val());
	$("#longM").val($("#long"+i).val());
	$("#pesoM").val($("#peso"+i).val());
	$("#unidadmedida_idM").val($("#unidadmedida_id"+i).val());

	$("#anchoM").val($("#ancho"+i).val());
	$("#anchoM").attr('valor',$("#ancho"+i).val());
	$("#largoM").val($("#long"+i).val());
	$("#largoM").attr('valor',$("#long"+i).val());
	$("#obsM").val($("#obs"+i).val());

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
				mostrardatosadUniMed(respuesta);
			}
		}
	});


	$(".selectpicker").selectpicker('refresh');
    $("#myModal").modal('show');
}
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
//FIN FUNCIONES DE COTIZACION Y NOTA DE VENTA


$(document).on("click", ".btngenpdfCot1", function(){
    fila = $(this).closest("tr");
	form = $(this);        
	if(form.attr('col')){
		id = fila.find('td:eq('+form.attr('col')+')').text();
	}else{
		id = fila.find('td:eq(0)').text();
	}
	genpdfCOT(id,1);
});
function genpdfCOT(id,stareport){ //GENERAR PDF COTIZACION
	$('#contpdf').attr('src', '/cotizacion/'+id+'/'+stareport+'/exportPdfM');
	$("#myModalpdf").modal('show')
}


function genpdfNV(id,stareport){ //GENERAR PDF NOTA DE VENTA
	$('#contpdf').attr('src', '/notaventa/'+id+'/'+stareport+'/exportPdf');
	$("#myModalpdf").modal('show')
}

$(document).on("click", ".btngenpdfNV1", function(){	
    fila = $(this).closest("tr");
	form = $(this);
	if(form.attr('col')){
		id = fila.find('td:eq('+form.attr('col')+')').text();
	}else{
		id = fila.find('td:eq(0)').text();
	}
	genpdfNV(id,1);
});

$(document).on("click", ".btngenpdfNV2", function(){	
    fila = $(this).closest("tr");	        
	if(form.attr('col')){
		id = fila.find('td:eq('+form.attr('col')+')').text();
	}else{
		id = fila.find('td:eq(0)').text();
	}
	genpdfNV(id,2);
});


function genpdfSD(id,stareport){ //GENERAR PDF Solicitud de Despacho
	$('#contpdf').attr('src', '/despachosol/'+id+'/'+stareport+'/exportPdf');
	$("#myModalpdf").modal('show')
}
function genpdfVPOD(id,stareport){ //GENERAR PDF Vista Previa Orden Despacho
	$('#contpdf').attr('src', '/despachosol/'+id+'/'+stareport+'/vistaprevODPdf');
	$("#myModalpdf").modal('show')
}


function genpdfOD(id,stareport){ //GENERAR PDF Orden de Despacho
	$('#contpdf').attr('src', '/despachoord/'+id+'/'+stareport+'/exportPdf');
	$("#myModalpdf").modal('show')
}

function pdfSolDespPrev(id,stareport){ //GENERAR PDF Solicitud despacho previo
	$('#contpdf').attr('src', '/despachosol/'+id+'/'+stareport+'/pdfSolDespPrev');
	$("#myModalpdf").modal('show')
}


$("#myModalpdf").on("hidden.bs.modal", function () {
	$('#contpdf').attr('src', 'about:blank');
});

$("#precionetoM").blur(function(event){
	if($("#pesoM").val()==0){
		aux_preciokilo = $("#precionetoM").val();
	}else{
		aux_preciokilo = $("#precionetoM").val()/$("#pesoM").val();
		$("#precioM").val(aux_preciokilo.toFixed(2));
		$("#precioM").attr('valor',aux_preciokilo.toFixed(2));	
	}
	totalizarItem(0);
});

//FUNCIONES NOTA DE VENTA CONSULTA
function verpdf2(nameFile,stareport){ //GENERAR PDF NOTA DE VENTA
	if(nameFile==""){
		swal({
			title: 'Archivo Orden de Compra no fue Adjuntado a la Nota de Venta.',
			text: "",
			icon: 'error',
			buttons: {
				confirm: "Cerrar",
			},
		}).then((value) => {
		});
	}else{
		$('#contpdf').attr('src', '/storage/imagenes/notaventa/'+nameFile);
		if((nameFile.indexOf(".pdf") > -1) || (nameFile.indexOf(".PDF") > -1) || (nameFile.indexOf(".jpg") > -1) || (nameFile.indexOf(".bmp") > -1) || (nameFile.indexOf(".png") > -1)){
			$("#myModalpdf").modal('show');
		}
	}
	

}
//

function listarorddespxNV(id,producto_id = null){
	var data = {
        id: id,
		producto_id : producto_id,
        _token: $('input[name=_token]').val()
    };
    $.ajax({
        url: '/despachoord/listarorddespxnv',
        type: 'POST',
        data: data,
        success: function (respuesta) {
			$("#tablalistarorddesp").html(respuesta.tabla);
			//$("#tablaconsulta").html(datos['tabla']);
			configurarTabla('#tabladespachoorddet');
			$("#myModalTablaOD").modal('show');
        }
    });
}

function configurarTablageneral(aux_tabla){
	$(aux_tabla).DataTable({
		'paging'      : true, 
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		'autoWidth'   : false,
		"order"       : [[ 0, "desc" ]],
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
		}
	});    
}

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
					$("#diamextmmM").val(respuesta[0]['diametro']);
					if(respuesta[0]['espesor'] == 0){
						$("#espesorM").val('');
						$("#espesor1M").val('');
						$("#espesor1M").attr('valor','');
					}else{
						$("#espesorM").val(respuesta[0]['espesor']);
						$("#espesor1M").val(respuesta[0]['espesor']);
						$("#espesor1M").attr('valor',respuesta[0]['espesor']);
					}
					$("#longM").val(respuesta[0]['long']);
					if(respuesta[0]['long'] == 0){
						$("#largoM").val('');
						$("#largoM").attr('valor','');
					}else{
						$("#largoM").val(respuesta[0]['long']);
						$("#largoM").attr('valor',respuesta[0]['long']);	
					}
					$("#pesoM").val(respuesta[0]['peso']);
					$("#tipounionM").val(respuesta[0]['tipounion']);
					$("#precioM").val(respuesta[0]['precio']);
					$("#precioM").attr('valor',respuesta[0]['precio']);
					$("#precioxkilorealM").val(respuesta[0]['precio']);
					$("#precioxkilorealM").attr('valor',respuesta[0]['precio']);
					$("#precionetoM").val(respuesta[0]['precioneto']);
					$("#precionetoM").attr('valor',respuesta[0]['precioneto']);
					//alert(respuesta[0]['precio']);

					$("#unidadmedida_idM").val(respuesta[0]['unidadmedidafact_id']);
					$("#anchoM").val('');
					$("#anchoM").attr('valor','');
					$("#obsM").val('');
					mostrardatosadUniMed(respuesta);

					$(".selectpicker").selectpicker('refresh');
					
					//$("#cantM").change();
					quitarverificar();
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

function validardatoscant(){
	validacion('producto_idM','textootro');
	//validacion('cantM','texto');
	validacion('precioM','texto');
	validacion('precionetoM','texto');
	validacion('unidadmedida_idM','combobox');

}

//AL HACER CLIC EN BOTON INCLUIR NUEVO PRODUCTO. COTIZACION NOTA DE VENTA ETC
$("#botonNewProd").click(function(event)
{
	clientedirec_id = $("#clientedirec_id").val();
	aux_rut = $("#rut").val();
	if(aux_rut==""){
		mensaje('Debes Incluir RUT del cliente','','error');
		return 0;
	}else{
		limpiarInputOT();
		quitarverificar();
		$("#aux_sta").val('1');
		$("#myModal").modal('show');
		$("#direccionM").focus();	
	}
});

function mostrardatosadUniMed(respuesta){
	if(respuesta[0]['mostdatosad'] == 0){
		$(".mostdatosad1").css({'display':'none'});
		$(".mostdatosad0").css({'display':'block'});
	}else{
		$(".mostdatosad0").css({'display':'none'});
		$(".mostdatosad1").css({'display':'block'});
	}
	
	if(respuesta[0]['mostunimed'] == 0){
		$("#mostunimed1").css({'display':'none'});
		$("#mostunimed0").css({'display':'block'});
	}else{
		$("#mostunimed0").css({'display':'none'});
		$("#mostunimed1").css({'display':'block'});
	}
	$("#unidadmedida_textoM").val(respuesta[0]['unidadmedidanombre']);	
}

$("#selectmultprod").click(function(event){

	//var cells = [];
	/*
	var rows = $("#tabla-data-productos").dataTable().fnGetData();
	for(var i=0;i<rows.length;i++)
	{
		//console.log(rows[i]);
		//console.log($(rows[i]).children());
		//$(rows[i]).children("td").each(function () {
		$(rows[i]).each(function () {
				console.log($(this).text());
		});
	}
	*/
});


function llenarlistaprod(i,producto_id){
	aux_id = $("#producto_idPxP").val();
	if( aux_id == null || aux_id.length == 0 || /^\s+$/.test(aux_id) ){
        $("#producto_idPxP").val(producto_id);
    }else{
		cadenaADividir = $("#producto_idPxP").val();
		arraynew = cadenaADividir.split(',')
		console.log(arraynew);
		arraynew1 = addRemoveItemArray ( arraynew, producto_id );
		console.log(arraynew1);
		$("#producto_idPxP").val(arraynew1.toString());
	
        //$("#producto_idPxP").val(aux_id + "," + producto_id);
    }
	$("#producto_idBP").html('Cod Prod: ' + $("#producto_idPxP").val());
}

function addRemoveItemArray ( arr, item ) {
    var i = arr.indexOf( item.toString() );
    if(i !== -1){
		arr.splice( i, 1 );
	}else{
		arr.push(item);
	}
	return arr;
};
