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
    $('.numericoblanco').numeric('.');
    //*******************************************************************

	$(".numerico").blur(function(e){
		if($(this).attr('valor') != undefined){
			$(this).attr('valor',$(this).val());
			//$(this).val(MASK(0, $(this).val(), '-###,###,###,##0.00',1));
			$(this).val(MASKLA($(this).val(),2));

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
	$(".numericoblanco").blur(function(e){
		if($(this).attr('valor') != undefined){
			$(this).attr('valor',$(this).val());
		}
	});
	
	$(".numericoblanco").focus(function(e){
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

	$( ".modal" ).draggable({opacity: 0.80, handle: ".modal-header"});

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
			//console.log($('#'+campo).prop('min'));
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
				if(caract.test(cajatexto) == false){
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
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().parent().attr("class", columnas+" has-success has-feedback");
				$('#'+campo).parent().parent().children('span').hide();
				$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'></span>");
				return true;

				/*
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
				*/
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
		case "number": 
			codigo = document.getElementById(campo).value;
			if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo) ) {
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().attr("class", columnas+" has-error has-feedback");
				$('#'+campo).parent().children('span').text("Campo obligatorio").show();
				$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback check'></span>");
				$('#'+campo).focus();
				return false;
			}else{
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().attr("class", columnas+" has-success has-feedback");
				$('#'+campo).parent().children('span').hide();
				$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'></span>");

				aux_respuesta = true;
				aux_valor = parseInt($('#'+campo).val(), 10);
				aux_min = parseInt($('#'+campo).attr("min1"), 10);
				aux_max = parseInt($('#'+campo).attr("max1"), 10);
				$("#glypcn"+campo).remove();
				$('#'+campo).parent().attr("class", columnas+" has-success has-feedback");
				$('#'+campo).parent().children('span').hide();
				if(aux_min !== undefined && aux_min !== null && aux_min !== ""){
					if(aux_valor < aux_min){
						$('#'+campo).parent().attr("class", columnas+" has-error has-feedback");
						$('#'+campo + "-error").parent().children('span').hide();
						$('#'+campo).parent().children('span').text("Por favor, escribe un valor mayor o igual a " + aux_min +  ".").show();
						$('#'+campo).focus();
						aux_respuesta = false;
					}else{
						$("#glypcn"+campo).remove();
						$('#'+campo).parent().attr("class", columnas+" has-success has-feedback");
						$('#'+campo).parent().children('span').hide();
						aux_respuesta = true;				
					}
				}
				if(aux_max !== undefined && aux_max !== null && aux_max !== ""){
					if(aux_valor > aux_max){
						$('#'+campo).parent().attr("class", columnas+" has-error has-feedback");
						$('#'+campo + "-error").parent().children('span').hide();
						$('#'+campo).parent().children('span').text("Por favor, escribe un valor menor o igual a " + aux_max +  ".").show();
						$('#'+campo).focus();
						aux_respuesta = false;
					}else{
						$("#glypcn"+campo).remove();
						$('#'+campo).parent().attr("class", columnas+" has-success has-feedback");
						$('#'+campo).parent().children('span').hide();
						aux_respuesta = true;				
					}
				}
				return aux_respuesta;


			}
/*
			aux_respuesta = true;
			aux_valor = parseInt($('#'+campo).val(), 10);
			aux_min = parseInt($('#'+campo).attr("min1"), 10);
			aux_max = parseInt($('#'+campo).attr("max1"), 10);
			$("#glypcn"+campo).remove();
			$('#'+campo).parent().attr("class", columnas+" has-success has-feedback");
			$('#'+campo).parent().children('span').hide();
			if(aux_min !== undefined && aux_min !== null && aux_min !== ""){
				if(aux_valor < aux_min){
					$('#'+campo).parent().attr("class", columnas+" has-error has-feedback");
					$('#'+campo + "-error").parent().children('span').hide();
					$('#'+campo).parent().children('span').text("Por favor, escribe un valor mayor o igual a " + aux_min +  ".").show();
					$('#'+campo).focus();
					aux_respuesta = false;
				}else{
					$("#glypcn"+campo).remove();
					$('#'+campo).parent().attr("class", columnas+" has-success has-feedback");
					$('#'+campo).parent().children('span').hide();
					aux_respuesta = true;				
				}
			}
			if(aux_max !== undefined && aux_max !== null && aux_max !== ""){
				if(aux_valor > aux_max){
					$('#'+campo).parent().attr("class", columnas+" has-error has-feedback");
					$('#'+campo + "-error").parent().children('span').hide();
					$('#'+campo).parent().children('span').text("Por favor, escribe un valor menor o igual a " + aux_max +  ".").show();
					$('#'+campo).focus();
					aux_respuesta = false;
				}else{
					$("#glypcn"+campo).remove();
					$('#'+campo).parent().attr("class", columnas+" has-success has-feedback");
					$('#'+campo).parent().children('span').hide();
					aux_respuesta = true;				
				}
			}
			return aux_respuesta;
			*/
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
			//$('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon'></span>");
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
			/*
			$('#'+campo).parent().parent().append("<span id='glypcn"+campo+"' class='glyphicon'></span>");
			*/
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
		//$("#precionetoM").val(Math.round(precioneto));
		$("#precioM").val(precio);
	}else{
		aux_staAT = $("#acuerdotecnico_id").val();
		aux_tipoProd = $("#tipoprodM").attr('valor');
		aux_UM = $("#unidadmedida_idM").val();
		if(aux_staAT > 0 || aux_tipoProd == 1){
			if(aux_UM != 7){
				$("#precioM").attr("valor",$("#precioM").val());
				aux_total = ($("#cantM").val() * $("#precionetoM").val()) * ($("#descuentoM").val());
				aux_total = Math.round(aux_total);
				$("#subtotalM").val(MASK(0, aux_total.toFixed(2), '-#,###,###,##0.00',1));
				$("#subtotalM").attr('valor',aux_total.toFixed(2));
				return 0;
			}
		}
	
		precioneto = $("#precioM").val() * aux_peso;
		//$("#precionetoM").val(Math.round(precioneto));
		$("#descuentoM").val('1');
		$(".selectpicker").selectpicker('refresh');
	}
		if($("#precionetoM").prop("disabled")){
			$("#precionetoM").val(precioneto);
		}else{
			$("#precionetoM").val(Math.round(precioneto));
		}	
	//alert(aux_peso);
	aux_tk = $("#cantM").val() * aux_peso;
	if($("#pesoM").val()>0){	
		$("#totalkilosM").val(MASK(0, aux_tk.toFixed(4), '-##,###,##0.00',1));
		$("#totalkilosM").attr('valor',aux_tk.toFixed(4));
	}else{
		//if($("#unidadmedida_idM option:selected").attr('value') == 7){
		if($("#unidadmedida_idM").val() == 7){
			aux_cant = MASK(0, $("#cantM").val(), '-#,###,###,##0.00',1);
			$("#totalkilosM").val(aux_cant);
			$("#totalkilosM").attr('valor',$("#cantM").val());
		}else{
			if ($("#producto_idM").attr("acuerdotecnico_id") == 0){
				$("#totalkilosM").val(0.00);
				$("#totalkilosM").attr('valor','0.00');	
			}
		}
	}
	//aux_total = ($("#cantM").val() * aux_peso * $("#precioM").val()) * ($("#descuentoM").val());
	aux_total = ($("#cantM").val() * $("#precionetoM").val()) * ($("#descuentoM").val());
	aux_total = Math.round(aux_total);
	$("#subtotalM").val(MASK(0, aux_total.toFixed(2), '-#,###,###,##0.00',1));
	$("#subtotalM").attr('valor',aux_total.toFixed(2));
	aux_precdesc = $("#precioM").val() * $("#descuentoM").val();
//	$("#precioM").val(MASK(0, aux_precdesc, '-##,###,##0.00',1));
	$("#precioM").val(aux_precdesc);
	$("#precioM").attr('valor',aux_precdesc);
	//EN COMENTARIO DESDE EL 12/12/2023, PARA EVITAR QUE CAMBIE EL VALOR DE precionetoM
	aux_precioUnit = aux_precdesc * aux_peso;
	//$("#precionetoM").val(MASK(0, Math.round(aux_precioUnit), '-##,###,##0.00',1));
		if($("#precionetoM").prop("disabled")){
		$("#precionetoM").val(aux_precioUnit);
		$("#precionetoM").attr('valor',aux_precioUnit);
	}else{
		$("#precionetoM").val(Math.round(aux_precioUnit));
		$("#precionetoM").attr('valor',Math.round(aux_precioUnit));		
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
	$("#aux_sta").val('0');
	//alert($("#tipoprodM").attr('valor'));
	aux_botonAcuTec = '';
	if($("#tipoprodM").attr('valor') == 1) {
		//alert("1: " + $("#producto_idM").val() + ", 2: " + $("#producto_id" + $("#aux_numfila").val()).val());
		aux_botonAcuTec = ' <a class="btn-accion-tabla tooltipsC" title="Editar Acuerdo tecnico" onclick="crearEditarAcuTec('+ i +')">'+
		'<i id="icoat' + i + '" class="fa fa-cog text-red girarimagen"></i> </a>';
	}else{
		$("#acuerdotecnico"+i).val("null");
		$("#tipoprod"+i).val("");
	}
	if($("#producto_idM").val() != $("#producto_id" + $("#aux_numfila").val()).val()){
		let aux_productoId = $("#producto_idM").val();
		let aux_acuerdotecnicoId = $("#acuerdotecnico_id").val();
		let aux_clienteId = $("#cliente_id").val();
		if(aux_acuerdotecnicoId > 0){
			aux_productoId = `<a class="btn-accion-tabla btn-sm tooltipsC" title="" onclick="genpdfAcuTec(${aux_acuerdotecnicoId},${aux_clienteId},1)" data-original-title="Acuerdo Técnico PDF" aria-describedby="tooltip895039">
								${aux_productoId}
							</a>`;
		}
		$("#producto_idTDT"+i).html(aux_productoId + aux_botonAcuTec);
	}

	$("#producto_id"+i).val($("#producto_idM").val());
	$("#producto_idValor"+i).html($("#producto_idM").val());
	
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
	$("#preciounitTD"+i).html(MASKLA($("#precionetoM").attr('valor'),3)); //$("#preciounitTD"+i).html(MASK(0, $("#precionetoM").attr('valor'), '-##,###,##0.00',1));
	$("#preciounit"+i).val($("#precionetoM").attr('valor'));
	aux_precioxkilo = $("#precioM").attr("valor");
	if($("#pesoM").val()==0)
	{
		aux_precioxkilo = 0; //$("#precioM").attr("valor");
		if($("#precioM").val()>0){
			aux_precioxkilo = $("#precioM").attr("valor");
		}

	}
	if($("#unidadmedida_idM option:selected").attr('value') == 7){
		aux_precioxkilo = $("#precioM").attr("valor");
	}else{

	}
	$("#precioxkiloTD"+i).html(MASKLA(aux_precioxkilo,0)); //$("#precioxkiloTD"+i).html(MASK(0, aux_precioxkilo, '-##,###,##0.00',1)); //$("#precioxkiloTD"+i).html(MASK(0, $("#precioM").val(), '-##,###,##0.00',1));
	$("#precioxkilo"+i).val(aux_precioxkilo);
	$("#totalkilosTD"+i).html(MASKLA($("#totalkilosM").attr('valor'),2)); //$("#totalkilosTD"+i).html(MASK(0, $("#totalkilosM").attr('valor'), '-##,###,##0.00',1));
	$("#totalkilos"+i).val($("#totalkilosM").attr('valor'));
	$("#totalkilos"+i).attr("valor",$("#totalkilosM").attr('valor'));
	$("#subtotalCFTD"+i).html(MASKLA($("#subtotalM").attr('valor'),0)); //$("#subtotalCFTD"+i).html(MASK(0, $("#subtotalM").attr('valor'), '-#,###,###,##0.00',1));
	$("#subtotal"+i).val($("#subtotalM").attr('valor'));
	$("#subtotalSFTD"+i).html($("#subtotalM").attr('valor'));

	$("#unidadmedida_id"+i).val($("#unidadmedida_idM option:selected").attr('value'));
	$("#unidadmedida_nomnreTD"+i).html($("#unidadmedida_idM option:selected").html());

	if($("#invmovtipo_idM")){
		$("#invbodega_idTXT"+i).html($("#invbodega_idM option:selected").html());
		$("#invbodega_idTD"+i).val($("#invbodega_idM").val());	
		$("#invmovtipo_idTXT"+i).html($("#invmovtipo_idM option:selected").html());
		$("#invmovtipo_idTD"+i).val($("#invmovtipo_idM").val());
		$("#cant"+i).attr('valor',$("#cantM").val());
		totalizarcantkg();
	}

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
	total_cant = 0;
	$("#tabla-data tr .subtotal").each(function() {
		valor = $(this).html() ;
		valorNum = parseFloat(valor);
		total_neto += valorNum;
	});
	$("#tabla-data tr .subtotalkg").each(function() {
		//valor = $(this).html() ;
		valor = $(this).attr("valor");
		valor = valor.replace(/,/g, ""); //Elimina comas al valor con formato
		//alert(valor);
		valorNum = parseFloat(valor);
		total_kg += valorNum;
	});
	$("#tabla-data tr .subtotalcant").each(function() {
		//valor = $(this).html() ;
		valor = $(this).attr("valor");
		valor = valor.replace(/,/g, ""); //Elimina comas al valor con formato
		//alert(valor);
		valorNum = parseFloat(valor);
		if(isNaN(valorNum)){
			valorNum = 0;
		}
		total_cant += valorNum;
	});

	aux_totalkgform = MASKLA(total_kg,2); //MASK(0, total_kg, '-##,###,##0.00',1)
	aux_totalcantform = MASKLA(total_cant,2); //MASK(0, total_kg, '-##,###,##0.00',1)
	let aux_foliocontrol_id = "";
	aux_p = $("#foliocontrol_id").val();
	if($("#foliocontrol_id").val() === 'undefined' || $("#foliocontrol_id").val() == null){
		aux_foliocontrol_id = 1;
	}else{
		aux_foliocontrol_id = $("#foliocontrol_id").val();
	}
	if($("#dtefoliocontrol_id").val() == 5 || $("#dtefoliocontrol_id").val() == 6){
		if($("#tdfoliocontrol_id").val() == 7){
			aux_foliocontrol_id = 7;	
		}
	}
	if(aux_foliocontrol_id == 7){
		aux_porciva = 0;
		aux_iva = 0;
	}else{
		aux_porciva = $("#aux_iva").val();
		aux_porciva = parseFloat(aux_porciva);
		aux_iva = Math.round(total_neto * (aux_porciva/100));	
	}
	aux_total = total_neto + aux_iva;
	aux_netoform = MASKLA(total_neto,0); //MASK(0, total_neto, '-#,###,###,##0.00',1)
	aux_ivaform = MASKLA(aux_iva,0); //MASK(0, aux_iva, '-#,###,###,##0.00',1)
	aux_tdtotalform = MASKLA(aux_total,0); //MASK(0, aux_total, '-#,###,###,##0.00',1)
	
	//$("#tdneto").html(total_neto.toFixed(2));
	$("#Tcant").html(aux_totalcantform);
	$("#totalkg").html(aux_totalkgform);
	$("#tdneto").html(aux_netoform);
	$("#tdneto").attr("valor",total_neto);
	$("#tdiva").html(aux_ivaform);
	$("#tdtotal").attr("valor",aux_total);
	$("#tdtotal").html(aux_tdtotalform);

	$("#neto").val(total_neto);
	$("#iva").val(aux_iva);
	if(aux_total == 0){
		$("#total").val("");
		//$("tfoot").hide();
		$("#foottotal").hide();
		
	}else{
		$("#total").val(aux_total);
		//$("tfoot").show();
		$("#foottotal").show();
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
		//valor = $(this).html() ;
		valor = $(this).attr("valor") ;
		valor = valor.replace(/,/g, ""); //Elimina comas al valor con formato
		//alert(valor);
		valorNum = parseFloat(valor);
		total_kg += valorNum;
	});
	//alert(total_neto);
	aux_totalkgform = MASKLA(total_kg,2); //MASK(0, total_kg, '-##,###,##0.00',1);
	//alert(aux_totalkgform);
	//alert(aux_totalkgform);
	aux_porciva = $("#aux_iva").val()
	aux_porciva = parseFloat(aux_porciva);
	aux_iva = Math.round(total_neto * (aux_porciva/100));
	aux_total = total_neto + aux_iva;
	aux_netoform = MASKLA(total_neto,0); //MASK(0, total_neto, '-#,###,###,##0.00',1);
	aux_ivaform = MASKLA(aux_iva,0); //MASK(0, aux_iva, '-#,###,###,##0.00',1);
	aux_tdtotalform = MASKLA(aux_total,0); //MASK(0, aux_total, '-#,###,###,##0.00',1);
	
	//$("#tdneto").html(total_neto.toFixed(2));
	$("#totalkg").html(aux_totalkgform);
	$("#tdneto").html(aux_netoform);
	$("#tdneto").attr("valor",total_neto);
	$("#tdiva").html(aux_ivaform);
	$("#tdtotal").attr("valor",aux_total);
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
	$("#stockM").val('');
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
	$("#stakilos").val('0');
	$("#tipoprodM").val('');
	$("#categoriaprod_id").val('');
	$("#acuerdotecnico_id").val('');
	
	if($("#invbodega_idM")){
		$("#invbodega_idM").empty();
	}
	if($("#invmovtipo_idM")){
		$("#invmovtipo_idM").val("");
	}

    $(".selectpicker").selectpicker('refresh');
}

function verificar()
{
	var v1=0,v2=0,v3=0,v4=0,v5=0,v6=0,v7=0,v8=0,v9=0,v10=0,v11=0,v12=0,v13,v14=0;
	
	v7=validacion('unidadmedida_idM','combobox');
	v6=validacion('precionetoM','numerico');
	if($("#stakilos").val() == "1"){
		v5=validacion('totalkilosM','numerico');
		v4=validacion('precioM','numerico');	
	}
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
	/*
	$(".requeridos").each(function() {
		quitarValidacion($(this).prop('name'),$(this).attr('tipoval'));
	});
*/
	quitarValidacion('descuentoM','combobox');
	quitarValidacion('cantM','texto');
	quitarValidacion('precioM','texto');
	quitarValidacion('precionetoM','texto');
	quitarValidacion('producto_idM','textootro');
	quitarValidacion('unidadmedida_idM','combobox');

	if($("#invbodega_idM")){
		quitarValidacion('invbodega_idM','combobox');
	}
	if($("#invmovtipo_idM")){
		quitarValidacion('invmovtipo_idM','combobox');
	}
}

function editarRegistro(i,aux_acuerdotecnicoId = 0){
	//alert($("#direccion"+i).val());
	//event.preventDefault();
	$("#producto_idM").prop("disabled", false);
	$("#btnbuscarproducto").prop("disabled", false);
	//console.log(aux_acuerdotecnicoId);
	if(aux_acuerdotecnicoId > 0){
		$("#producto_idM").attr("disabled", "disabled");
		$("#btnbuscarproducto").attr("disabled", "disabled");
	}
    limpiarInputOT();
	quitarverificar();
	$("#aux_sta").val('0');

	$("#aux_numfila").val(i);

	$("#producto_idM").val($("#producto_id"+i).val());
	//$("#producto_idM").blur();

	$("#precioxkilorealM").attr('valor',$("#precioxkiloreal"+i).val());
	$("#precioxkilorealM").val(MASK(0, $("#precioxkiloreal"+i).val(), '-##,###,##0.00',1));
	$("#codintprodM").val($.trim($("#codintprodTD"+i).html()));
	$("#nombreprodM").val($.trim($("#nombreProdTD"+i).html()));

	$("#cantM").val($("#cant"+i).val());
	$("#descuentoM").val($.trim($("#descuentoval"+i).val()));
	$("#precionetoM").attr('valor',$("#preciounit"+i).val());
	$("#precionetoM").val($("#preciounit"+i).val());
	//$("#precionetoM").val(MASK(0, $("#preciounit"+i).val(), '-##,###,##0.00',1));
	$("#precioM").attr('valor',$("#precioxkilo"+i).val());
	$("#precioM").val($("#precioxkilo"+i).val());
	//$("#precioM").val(MASK(0, $("#precioxkilo"+i).val(), '-##,###,##0.00',1));
	$("#totalkilosM").attr('valor',$("#totalkilos"+i).val());
	//$("#totalkilosM").val(MASK(0, $("#totalkilos"+i).val(), '-#,###,###,##0.00',1));
	$("#totalkilosM").val($("#totalkilos"+i).val(),2);
	
	$("#subtotalM").attr('valor',$("#subtotal"+i).val());
	//$("#subtotalM").val(MASK(0, $("#subtotal"+i).val(), '-#,###,###,##0.00',1));
	$("#subtotalM").val(MASKLA($("#subtotal"+i).val(), 2));
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
	$("#tipoprodM").val($("#tipoprod"+i).val());
	$("#tipoprodM").attr('valor',$("#tipoprod"+i).val())

	$("#invmovtipo_idM").val($("#invmovtipo_idTD"+i).val());

	var data = {
		id: $("#producto_idM").val(),
		_token: $('input[name=_token]').val()
	};
	$.ajax({
		url: '/producto/buscarUnProducto',
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(respuesta['cont']>0){
				mostrardatosadUniMed(respuesta);
				$("#acuerdotecnico_id").val(respuesta['acuerdotecnico_id'])
				if($("#invbodega_idM")){
					llenarselectbodega(respuesta);
					//console.log(respuesta);
					llenarcampostockM(respuesta);
					$("#invbodega_idM").val($("#invbodega_idTD"+i).val());
					$("#invbodega_idM").selectpicker('refresh');
					$("#stakilos").val(respuesta['stakilos']);
				}
				$("#producto_idM").attr("acuerdotecnico_id",respuesta.acuerdotecnico_id ? respuesta.acuerdotecnico_id : 0);
				if(respuesta.acuerdotecnico_id != null){
					console.log(respuesta["acuerdotecnico"]["at_unidadmedida_id"]);
					$("#unidadmedida_idM").val(respuesta["acuerdotecnico"]["at_unidadmedida_id"]);
					$("#at_unidadmedida_idM").val(respuesta["acuerdotecnico"]["at_unidadmedida_id"]);
					$(".selectpicker").selectpicker('refresh');
					$("#unidadmedida_idM").attr("disabled",true);
					$("#unidadmedida_idM").attr("readonly");
					if($("#unidadmedida_idM").val() == 7){
						$("#totalkilosM").prop("disabled", true);
						$("#totalkilosM").prop("readonly", true);
					}else{
						$("#totalkilosM").prop("disabled", false);	
						$("#totalkilosM").prop("readonly", false);		
					}
				}else{
					$("#unidadmedida_idM").attr("disabled",false);
					$("#unidadmedida_idM").attr("readonly",false);	
					$("#totalkilosM").prop("disabled", true);	
					$("#totalkilosM").prop("readonly", true);	
				}
				$(".selectpicker").selectpicker('refresh');
				activarCajasPreciokgUni();
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
function genpdfCOT(id,stareport,aux_venmodant = ""){ //GENERAR PDF COTIZACION
	$("#venmodant").val("");
	if(aux_venmodant!=""){
		$("#" + aux_venmodant).modal('hide');
		$("#venmodant").val(aux_venmodant);
	}
	$('#contpdf').attr('src', '/cotizacion/'+id+'/'+stareport+'/exportPdfM');
	$("#myModalpdf").modal('show')
}


function genpdfNV(id,stareport,aux_venmodant = ""){ //GENERAR PDF NOTA DE VENTA
	$("#venmodant").val(""); //Ventana Modal Anterior
	if(aux_venmodant!=""){
		$("#" + aux_venmodant).modal('hide');
		$("#venmodant").val(aux_venmodant);
	}
	$('#contpdf').attr('src', '/notaventa/'+id+'/'+stareport+'/exportPdf');
	$("#myModalpdf").modal('show')
}

function genpdfGDI(id,stareport){ //GENERAR PDF GUIA DESPACHO INTERNA
	let queryString = '?timestamp=' + new Date().getTime();
	$('#contpdf').attr('src', '/guiadespint/'+id+'/'+stareport+'/exportPdf' + queryString);
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
	form = $(this);
	if(form.attr('col')){
		id = fila.find('td:eq('+form.attr('col')+')').text();
	}else{
		id = fila.find('td:eq(0)').text();
	}
	genpdfNV(id,2);
});

$(document).on("click", ".btngenpdfINVMOV", function(){
    fila = $(this).closest("tr");
	form = $(this);
	if(form.attr('col')){
		id = fila.find('td:eq('+form.attr('col')+')').text();
	}else{
		id = fila.find('td:eq(0)').text();
	}
	genpdfINVMOV(id,1);
});

$(document).on("click", ".btngenpdfINVENTSAL", function(){
    fila = $(this).closest("tr");
	form = $(this);
	if(form.attr('col')){
		id = fila.find('td:eq('+form.attr('col')+')').text();
	}else{
		id = fila.find('td:eq(0)').text();
	}
	genpdfINVENTSAL(id,1);
});

$(document).on("click", ".btngenpdfPESAJE", function(){
    fila = $(this).closest("tr");
	form = $(this);
	if(form.attr('col')){
		id = fila.find('td:eq('+form.attr('col')+')').text();
	}else{
		id = fila.find('td:eq(0)').text();
	}
	genpdfPESAJE(id,1);
});

//San Bernardo //function genpdfSD(id,stareport){ //GENERAR PDF Solicitud de Despacho
function genpdfSD(id,stareport,aux_venmodant = ""){ //GENERAR PDF Solicitud de Despacho
	$("#venmodant").val("");
	if(aux_venmodant!=""){
		$("#" + aux_venmodant).modal('hide');
		$("#venmodant").val(aux_venmodant);
	}
	$('#contpdf').attr('src', '/despachosol/'+id+'/'+stareport+'/exportPdf');
	$("#myModalpdf").modal('show')
}
function genpdfVPOD(id,stareport){ //GENERAR PDF Vista Previa Orden Despacho
	$('#contpdf').attr('src', '/despachosol/'+id+'/'+stareport+'/vistaprevODPdf');
	$("#myModalpdf").modal('show')
}


function genpdfOD(id,stareport,aux_venmodant = ""){ //GENERAR PDF Orden de Despacho
	$("#venmodant").val("");
	if(aux_venmodant!=""){
		$("#" + aux_venmodant).modal('hide');
		$("#venmodant").val(aux_venmodant);
	}

	if($("#myModalTablaOD")){
		$("#myModalTablaOD").modal('hide');
	}
	$('#contpdf').attr('src', '/despachoord/'+id+'/'+stareport+'/exportPdf');
	$("#myModalpdf").modal('show')
}

function pdfSolDespPrev(id,stareport){ //GENERAR PDF Solicitud despacho previo
	$('#contpdf').attr('src', '/despachosol/'+id+'/'+stareport+'/pdfSolDespPrev');
	$("#myModalpdf").modal('show')
}

function genpdfODRec(id,stareport,aux_venmodant = ""){ //GENERAR PDF Orden de Despacho Rechazo
	$("#venmodant").val("");
	if(aux_venmodant!=""){
		$("#" + aux_venmodant).modal('hide');
		$("#venmodant").val(aux_venmodant);
	}

	if($("#myModalTablaOD")){
		$("#myModalTablaOD").modal('hide');
	}
	$('#contpdf').attr('src', '/despachoordrec/'+id+'/'+stareport+'/exportPdf');
	$("#myModalpdf").modal('show')
}

function genpdfINVMOV(id,stareport){ //GENERAR PDF MOVIMIENTO DE INVENTARIO INVMOV
	var data = "?id=" + id +
    "&stareport="+stareport
	$('#contpdf').attr('src', '/invmov/exportPdf/' + data);
	$("#myModalpdf").modal('show')
}

function genpdfINVENTSAL(id,stareport){ //GENERAR PDF INVENTARIO ENTRADA SALIDA
	var data = "?id=" + id +
    "&stareport="+stareport
	$('#contpdf').attr('src', '/inventsal/exportPdf/' + data);
	//console.log($('#contpdf'));
	$("#myModalpdf").modal('show')
}

function genpdfPESAJE(id,stareport){ //GENERAR PDF PESAJE
	var data = "?id=" + id +
    "&stareport="+stareport
	$('#contpdf').attr('src', '/pesaje/exportPdf/' + data);
	//console.log($('#contpdf'));
	$("#myModalpdf").modal('show')
}
function genpdfGD(id,nombre,aux_venmodant = "",aux_slug = "ver-pdf-guia-despacho"){ //GENERAR PDF Guia Despacho
	var data = {
		slug: aux_slug,
		_token: $('input[name=_token]').val()
	};
	$.ajax({
		url: '/generales_valpermiso',
		type: 'POST',
		data: data,
		success: function (respuesta) {
			//console.log(respuesta);
			if(respuesta.resp){
				$("#venmodant").val("");
				if(aux_venmodant!=""){
					$("#" + aux_venmodant).modal('hide');
					$("#venmodant").val(aux_venmodant);
				}
				let id_str = id.toString();
				id_str = id_str.padStart(8, "0");
				let queryString = '?timestamp=' + new Date().getTime();
				$('#contpdf').attr('src', '/storage/facturacion/dte/procesados/DTE_T52FE'+id_str+nombre+'.pdf' + queryString);
				$("#myModalpdf").modal('show');
			}else{
				swal({
					title: respuesta.mensaje,
					text:  respuesta.mensaje2,
					icon: 'error',
					buttons: {
						confirm: "Cerrar",
					},
				}).then((value) => {
				});
			}
		}
	});
}

function genpdfFAC(id,nombre,aux_venmodant = "",aux_slug = "ver-pdf-factura"){ //GENERAR PDF Factura
	var data = {
		slug: aux_slug,
		_token: $('input[name=_token]').val()
	};
	$.ajax({
		url: '/generales_valpermiso',
		type: 'POST',
		data: data,
		success: function (respuesta) {
			//console.log(respuesta);
			if(respuesta.resp){
				$("#venmodant").val("");
				if(aux_venmodant!=""){
					$("#" + aux_venmodant).modal('hide');
					$("#venmodant").val(aux_venmodant);
				}
				//let id_str = id.toString();
				//id_str = id_str.padStart(8, "0");
				//console.log(id);
				let queryString = '?timestamp=' + new Date().getTime();
				$('#contpdf').attr('src', '/storage/facturacion/dte/procesados/'+id+nombre+'.pdf' + queryString);
				$("#myModalpdf").modal('show');			
			}else{
				swal({
					title: respuesta.mensaje,
					text:  respuesta.mensaje2,
					icon: 'error',
					buttons: {
						confirm: "Cerrar",
					},
				}).then((value) => {
				});
			}
		}
	});
}

function genpdfNC(id,nombre,aux_venmodant = ""){ //GENERAR PDF Factura
	$("#venmodant").val("");
	if(aux_venmodant!=""){
		$("#" + aux_venmodant).modal('hide');
		$("#venmodant").val(aux_venmodant);
	}
	let id_str = id.toString();
	id_str = id_str.padStart(8, "0");
	let queryString = '?timestamp=' + new Date().getTime();
	$('#contpdf').attr('src', '/storage/facturacion/dte/procesados/DTE_T61FE'+id_str+nombre+'.pdf' + queryString);
	$("#myModalpdf").modal('show');
}

function genpdfND(id,nombre,aux_venmodant = ""){ //GENERAR PDF Factura
	$("#venmodant").val("");
	if(aux_venmodant!=""){
		$("#" + aux_venmodant).modal('hide');
		$("#venmodant").val(aux_venmodant);
	}
	let id_str = id.toString();
	id_str = id_str.padStart(8, "0");
	let queryString = '?timestamp=' + new Date().getTime();
	$('#contpdf').attr('src', '/storage/facturacion/dte/procesados/DTE_T56FE'+id_str+nombre+'.pdf' + queryString);
	$("#myModalpdf").modal('show');
}


$("#myModalpdf").on("hidden.bs.modal", function () {
	$('#contpdf').attr('src', 'about:blank');
});
$("#precionetoM").blur(function(event){
	if($("#pesoM").val()==0){
		if($("#unidadmedida_idM option:selected").attr('value') == 7){
			aux_preciokilo = $("#precionetoM").val();
		}
	}else{
		aux_preciokilo = $("#precionetoM").val()/$("#pesoM").val();
		$("#precioM").val(aux_preciokilo.toFixed(2));
		$("#precioM").attr('valor',aux_preciokilo.toFixed(2));	
	}
	totalizarItem(0);
});

//FUNCIONES VER DOCUMENTO ADJUNTO ODEN DE COMPRA
function verpdf2(nameFile,stareport,aux_venmodant = "",aux_slug = "ver-pdf-orden-de-compra"){ 
	if(nameFile==""){
		swal({
			title: 'Archivo Orden de Compra no se Adjuntó a la Nota de Venta.',
			text: "",
			icon: 'error',
			buttons: {
				confirm: "Cerrar",
			},
		}).then((value) => {
		});
	}else{
		var data = {
			slug: aux_slug,
			_token: $('input[name=_token]').val()
		};
		$.ajax({
			url: '/generales_valpermiso',
			type: 'POST',
			data: data,
			success: function (respuesta) {
				//console.log(respuesta);
				if(respuesta.resp){
					// Genera una cadena de consulta única utilizando la marca de tiempo actual
					let queryString = '?timestamp=' + new Date().getTime();
					// Concatena la cadena queryString de consulta al atributo src del iframe
					let aux_nameFile = "";
					let aux_folder = "";
					if(nameFile.includes("/")){
						aux_folder = nameFile.split("/")[0];
						aux_nameFile = nameFile.split("/")[1];
					}else{
						aux_nameFile = nameFile;
						aux_folder = "notaventa";	
					}
					$('#contpdf').attr('src', '/storage/imagenes/' + aux_folder + '/'+aux_nameFile + queryString);
					if((aux_nameFile.indexOf(".pdf") > -1) || (aux_nameFile.indexOf(".PDF") > -1) || (aux_nameFile.indexOf(".jpg") > -1) || (aux_nameFile.indexOf(".bmp") > -1) || (aux_nameFile.indexOf(".png") > -1) || (aux_nameFile.indexOf(".jpeg") > -1)){
						$("#venmodant").val("");
						if(aux_venmodant!=""){
							$("#" + aux_venmodant).modal('hide');
							$("#venmodant").val(aux_venmodant);
						}
						$("#myModalpdf").modal('show');
					}	
				}else{
					swal({
						title: respuesta.mensaje,
						text:  respuesta.mensaje2,
						icon: 'error',
						buttons: {
							confirm: "Cerrar",
						},
					}).then((value) => {
					});
				}
			}
		});
	}
	

}

//FUNCIONES VER DOCUMENTO ADJUNTO ODEN DE COMPRA
function verpdf3(nameFile,stareport,ruta,aux_venmodant = ""){ 
	if(nameFile==""){
		swal({
			title: 'Archivo Orden de Compra no se Adjuntó a la Nota de Venta.',
			text: "",
			icon: 'error',
			buttons: {
				confirm: "Cerrar",
			},
		}).then((value) => {
		});
	}else{
		var data = {
			slug: 'ver-pdf-orden-de-compra',
			_token: $('input[name=_token]').val()
		};
		$.ajax({
			url: '/generales_valpermiso',
			type: 'POST',
			data: data,
			success: function (respuesta) {
				//console.log(respuesta);
				if(respuesta.resp){
					// Genera una cadena de consulta única utilizando la marca de tiempo actual
					let queryString = '?timestamp=' + new Date().getTime();
					// Concatena la cadena queryString de consulta al atributo src del iframe
					if(ruta == ""){
						$('#contpdf').attr('src', '/storage/imagenes/notaventa/'+nameFile + queryString);
					}else{
						$('#contpdf').attr('src', '/storage/imagenes/' + ruta + '/'+nameFile + queryString);
					}
					if((nameFile.indexOf(".pdf") > -1) || (nameFile.indexOf(".PDF") > -1) || (nameFile.indexOf(".jpg") > -1) || (nameFile.indexOf(".bmp") > -1) || (nameFile.indexOf(".png") > -1)){
						$("#venmodant").val("");
						if(aux_venmodant!=""){
							$("#" + aux_venmodant).modal('hide');
							$("#venmodant").val(aux_venmodant);
						}
						$("#myModalpdf").modal('show');
					}	
				}else{
					swal({
						title: respuesta.mensaje,
						text:  respuesta.mensaje2,
						icon: 'error',
						buttons: {
							confirm: "Cerrar",
						},
					}).then((value) => {
					});
				}
			}
		});
	}
	

}

$('#myModalpdf').on('hidden.bs.modal', function (event) {
	aux_venmodant = $("#venmodant").val();
	if(aux_venmodant != ""){
		$("#" + aux_venmodant).modal('show');
	}
})

//

//FUNCIONES VER DOCUMENTO ADJUNTO RECHAZO ORDEN DESPACHO
function verdocadj(nameFile,carpera){
	if(nameFile==""){
		swal({
			title: 'Archivo no fue Adjuntado.',
			text: "",
			icon: 'error',
			buttons: {
				confirm: "Cerrar",
			},
		}).then((value) => {
		});
	}else{
		let queryString = '?timestamp=' + new Date().getTime();
		$('#contpdf').attr('src', '/storage/imagenes/' + carpera + '/'+nameFile + queryString);
		if((nameFile.indexOf(".pdf") > -1) || (nameFile.indexOf(".PDF") > -1) || (nameFile.indexOf(".jpg") > -1) || (nameFile.indexOf(".bmp") > -1) || (nameFile.indexOf(".png") > -1)){
			$("#myModalpdf").modal('show');
		}
	}
	

}

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
	limpiarInputOT();
	$("#producto_idM").val(codigo);
	aux_sta = $("#aux_sta").val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		//totalizar();
		aux_actionform = $("#form-general").attr("action");
		aux_mostrarTodo = 1; // BUSCO TODOS LOS PRODUCTOS
		if(aux_actionform.includes("notaventa")){
			aux_mostrarTodo = 0; // BUSCO SOLO LOS PRODUCTOS tipoprod = 0
		}
		var data = {
			id: $("#producto_idM").val(),
			mostrarTodo : aux_mostrarTodo,
			_token: $('input[name=_token]').val()
		};
		$.ajax({
			url: '/producto/buscarUnProducto',
			type: 'POST',
			data: data,
			success: function (respuesta) {
				if(respuesta['cont']>0){
					$("#producto_idM").attr("acuerdotecnico_id",respuesta.acuerdotecnico_id ? respuesta.acuerdotecnico_id : 0);
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
								//ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
								$("#producto_idM").focus();
							}
						});
						return 0;	
					}
					//console.log(respuesta['nombre']);
					$("#nombreprodM").val(respuesta['nombre']);
					$("#codintprodM").val(respuesta['codintprod']);
					$("#cla_nombreM").val(respuesta['cla_nombre']);
					$("#diamextmmM").val(respuesta['diametro']);
					if(respuesta['espesor'] == 0){
						$("#espesorM").val('');
						$("#espesor1M").val('');
						$("#espesor1M").attr('valor','');
					}else{
						$("#espesorM").val(respuesta['espesor']);
						$("#espesor1M").val(respuesta['espesor']);
						$("#espesor1M").attr('valor',respuesta['espesor']);
					}
					$("#longM").val(respuesta['long']);
					if(respuesta['long'] == 0){
						$("#largoM").val('');
						$("#largoM").attr('valor','');
					}else{
						$("#largoM").val(respuesta['long']);
						$("#largoM").attr('valor',respuesta['long']);	
					}
					aux_peso = respuesta['peso'];
					aux_peso = aux_peso.toFixed(3);
					$("#pesoM").val(aux_peso);
					$("#tipounionM").val(respuesta['tipounion']);
					$("#precioM").val(respuesta['precio']);
					$("#precioM").attr('valor',respuesta['precio']);
					$("#precioxkilorealM").val(respuesta['precio']);
					$("#precioxkilorealM").attr('valor',respuesta['precio']);
					$("#precionetoM").val(respuesta['precioneto']);
					$("#precionetoM").attr('valor',respuesta['precioneto']);
					//alert(respuesta['precio']);

					$("#unidadmedida_idM").val(respuesta['unidadmedidafact_id']);
					$("#anchoM").val('');
					$("#anchoM").attr('valor','');
					if(respuesta['at_ancho'] != null){
						$("#anchoM").val(respuesta['at_ancho']);
						$("#anchoM").attr('valor',respuesta['at_ancho']);	
						$("#diamextmmM").val(respuesta['at_ancho']);
						$("#diamextmmM").attr('valor',respuesta['at_ancho']);	
					}
					if(respuesta['at_largo'] != null){
						$("#longM").val(respuesta['at_largo']);
						$("#longM").attr('valor',respuesta['at_largo']);	
						$("#largoM").val(respuesta['at_largo']);
						$("#largoM").attr('valor',respuesta['at_largo']);
					}
					if(respuesta['at_espesor'] != null){
						$("#espesorM").val(respuesta['at_espesor']);
						$("#espesorM").attr('valor',respuesta['at_espesor']);	
						$("#espesor1M").val(respuesta['at_espesor']);
						$("#espesor1M").attr('valor',respuesta['at_espesor']);
					}
					/*
					if(respuesta['at_tiposello_desc'] != null){
						$("#cla_nombreM").val(respuesta['at_tiposello_desc']);
						$("#cla_nombreM").attr('valor',respuesta['at_tiposello_desc']);	
					}
					*/
					$("#obsM").val('');
					$("#tipoprodM").attr('valor',respuesta['tipoprod']);
					$("#stakilos").val(respuesta['stakilos']);
					$("#categoriaprod_id").val(respuesta['categoriaprod_id']);
					$("#acuerdotecnico_id").val(respuesta['acuerdotecnico_id']);
					$("#at_unidadmedida_idM").val(respuesta['at_unidadmedida_id']);
					activarCajasPreciokgUni();
					mostrardatosadUniMed(respuesta);
					llenarselectbodega(respuesta);

					llenarcampostockM(respuesta);

					$(".selectpicker").selectpicker('refresh');					
					//$("#cantM").change();
					quitarverificar();
					$("#producto_idM").keyup();
					$("#cantM").focus();
					totalizarItem(1);
					if($("#precionetoM").attr("valor")>0){
						$("#precionetoM").blur();
					}					
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
							$("#producto_idM").focus();
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
	$("#producto_idM").prop("disabled",false);
	$("#btnbuscarproducto").prop("disabled",false);
	clientedirec_id = $("#clientedirec_id").val();
	aux_rut = $("#rut").val();
	aux_sucursal = $("#sucursal_id option:selected").attr('value');
	if(aux_rut==""){
		mensaje('Debes Incluir RUT del cliente','','error');
		return 0;
	}else{
		if(aux_sucursal==""){
			mensaje('Debes Seleccionar una sucursal','','error');
			return 0;
		}else{
			//$("#tabla-data-productos").dataTable().fnDestroy();
			$('#tabla-data-productos tbody').html("");
			limpiarInputOT();
			quitarverificar();
			$("#aux_sta").val('1');
			$("#myModal").modal('show');
			$("#direccionM").focus();
		}
	}
});

//AL HACER CLIC EN BOTON INCLUIR NUEVO PRODUCTO. COTIZACION NOTA DE VENTA ETC, PRODUCTOS POR CLIENTE
$("#botonNewProdxCli").click(function(event)
{
	/*
	data = datos();
	$('#tabla-data-productos').DataTable().ajax.url( "productobuscarpage/" + data.data2 ).load();
*/
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
	if(respuesta['mostdatosad'] == 0){
		$(".mostdatosad1").css({'display':'none'});
		$(".mostdatosad0").css({'display':'block'});
	}else{
		$(".mostdatosad0").css({'display':'none'});
		$(".mostdatosad1").css({'display':'block'});
	}
	
	if(respuesta['mostunimed'] == 0){
		$("#mostunimed1").css({'display':'none'});
		$("#mostunimed0").css({'display':'block'});
	}else{
		$("#mostunimed0").css({'display':'none'});
		$("#mostunimed1").css({'display':'block'});
	}
	$("#unidadmedida_textoM").val(respuesta['unidadmedidanombre']);	
	$(".selectpicker").selectpicker('refresh');
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
	$("#divprodselec").show();
	aux_array = $("#productos").val();
	$("#productos").html("");
	for(var i = 0; i < aux_array.length; i++){
		$("#productos").append("<option value='" + aux_array[i] + "' selected>" + aux_array[i] + "</option>")
	}
	if(aux_array.indexOf(producto_id.toString()) == -1){
		$("#productos").append("<option value='" + producto_id + "' selected>" + producto_id + "</option>")
	}
	aux_array = $("#productos").val();
	aux_array = aux_array.sort((a,b) => parseInt(a) > parseInt(b) ? 1 : -1);
	$("#productos").html("");
	for(var i = 0; i < aux_array.length; i++){
		$("#productos").append("<option value='" + aux_array[i] + "' selected>" + aux_array[i] + "</option>")
	}
}

//ACEPTAR LOS PRODUCTOS SELECCIONADOS Y ASIGNAR EL VALOR AL CAMPO DE BUSQUEDA
$("#aceptarmbp").click(function(){
	$("#producto_idPxP").val($("#productos").val());
});

function addRemoveItemArray ( arr, item ) {
    var i = arr.indexOf( item.toString() );
    if(i !== -1){
		arr.splice( i, 1 );
	}else{
		arr.push(item);
	}
	return arr;
};



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

function mesanno(annomes){
    mes = annomes.substr(4,2);
    switch (mes) {
        case "01":
            mes = "Enero";
            break;
        case "02":
            mes = "Febrero";
            break;
        case "03":
            mes = "Marzo";
            break;
        case "04":
            mes = "Abril";
            break;
        case "05":
            mes = "Mayo";
            break;
        case "06":
            mes = "Junio";
            break;
        case "07":
            mes = "Julio";
            break;
        case "08":
            mes = "Agosto";
            break;
        case "09":
            mes = "Septiembre";
            break;
        case "10":
            mes = "Octubre";
            break;
        case "11":
            mes = "Noviembre";
            break;
        case "12":
            mes = "Diciembre";
            break;
    }
    resultado = mes + " " + annomes.substr(0,4);
    return resultado;
}

function annomes(mesanno){
    arraymesanno = mesanno.split(' ');
    switch (arraymesanno[0]) {
        case "Enero":
            mes = "01";
            break;
        case "Febrero":
            mes = "02";
            break;
        case "Marzo":
            mes = "03";
            break;
        case "Abril":
            mes = "04";
            break;
        case "Mayo":
            mes = "05";
            break;
        case "Junio":
            mes = "06";
            break;
        case "Julio":
            mes = "07";
            break;
        case "Agosto":
            mes = "08";
            break;
        case "Septiembre":
            mes = "09";
            break;
        case "Octubre":
            mes = "10";
            break;
        case "Noviembre":
            mes = "11";
            break;
        case "Diciembre":
            mes = "12";
            break;
    }
    resultado = arraymesanno[1] + mes;
    return resultado;
}


$("#unidadmedida_idM").change(function(){
	$("#totalkilosM").val(0.00);
	$("#totalkilosM").attr('valor','0.00');
	activarCajasPreciokgUni();
	totalizarItem(0);
});

function MASKLA(num,dec){
	//num = round(aux_cerodec,dec);
	aux_num = new Intl.NumberFormat("de-DE").format(num);
	if(dec>0){
		aux_repcero = '0'.repeat(dec);
		if(aux_num.indexOf(",") == -1){
			aux_num = aux_num + ","+aux_repcero;
		}else{
			aux_num = aux_num + aux_repcero;
			aux_pos = aux_num.indexOf(",");
			aux_num = aux_num.substr(0,(aux_pos+1+dec));
		}	
	}
	return aux_num;
}

$(".selectpicker").selectpicker({
	noneSelectedText : "Seleccione...", // by this default "Nothing selected" -->will change to Please Select
	selectAllText : "Selec todo",
	deselectAllText : "Borrar todo",
	});

function fechaddmmaaaa(f){
	dia = f.getDate();
	d = dia.toString();
	d = d.padStart(2, 0);
	mes = f.getMonth();
	m = f.toLocaleString('es', { month: '2-digit' }); //mes.toString();
	m = m.padStart(2, 0);
	fecha = d + "/" + m + "/" + f.getFullYear();
	
	return fecha; 
}

function totalizarcantkg(){
	total_cant = 0;
	total_kg = 0;
	$("#tabla-data tr .subtotalcant").each(function() {
		valor = $(this).attr("valor"); //$(this).html() ;
		valorNum = parseFloat(valor);
		total_cant += valorNum;
	});
	$("#tabla-data tr .subtotalkg").each(function() {
		valor = $(this).attr("valor");
		valorNum = parseFloat(valor);
		total_kg += valorNum;
	});
	aux_totalkgform = MASKLA(total_kg,2); //MASK(0, total_kg, '-##,###,##0.00',1)
	aux_total_cant = MASKLA(total_cant,2); //MASK(0, total_neto, '-#,###,###,##0.00',1)
	aux_total = total_cant;
	//$("#tdneto").html(total_neto.toFixed(2));
	$("#tdtotalkg").html("<b>" + aux_totalkgform + "</b>");
	$("#tdtotalcant").html("<b>" + aux_total_cant + "</b>");

	if(aux_total == 0){
		$("#total").val("");
	}else{
		$("#total").val(aux_total_cant);
	}
}

$("#btnGuardarInvM").click(function(event)
{
	event.preventDefault();

	if(verificarlote())
	{
		//$("#invmovtipo_idM").val()
		aux_tipomov = $("#invmovtipo_idM option:selected").attr('tipomov');
		if(aux_tipomov < 0){
			var data = {
				producto_id : $("#producto_idM").val(),
				invbodega_id : $("#invbodega_idM").val(),
				tipo : 1,
				_token: $('input[name=_token]').val()
			};
			$.ajax({
				url: '/invbodegaproducto/consexistencia',
				type: 'POST',
				data: data,
				success: function (respuesta) {
					aux_stock = 0
					if(respuesta.cont>0){
						aux_stock = respuesta.stock.cant;
					}
					aux_cantM = $("#cantM").val();
					if(aux_stock >= aux_cantM){
						insertarModificar();
					}else{
						swal({
							title: 'Producto no tiene Stock suficiente.',
							text: "Bodega: " + $("#invbodega_idM option:selected").html() + "\nStock: " + aux_stock,
							icon: 'info',
							buttons: {
								confirm: "Aceptar"
							},
						}).then((value) => {
							if (value) {
								$("#cantM").focus();
							}
						});
	
					}
				}
			});
		}else{
			insertarModificar();
		}
	}else{
		alertify.error("Falta incluir informacion");
	}
});

function verificarlote(){
	aux_valido = true;
	$(".requeridos").each(function() {
		if($(this).prop('name')){
			//alert($(this).prop('name'));
			if(validacion($(this).prop('name'),$(this).attr('tipoval')) == false){
				aux_valido = false;
			}
		}
	});
	return aux_valido;

}

function llenarselectbodega(respuesta){
	if($("#invbodega_idM")){
		$("#invbodega_idM").empty();
		$.each(respuesta['bodegas'], function(id,value){
			//console.log(respuesta['bodegas'][id]);
			$("#invbodega_idM").append(`<option value="${respuesta['bodegas'][id].id}">${respuesta['bodegas'][id].nombre} ${respuesta['bodegas'][id].sucursal_nombre} / Stock: ${respuesta['bodegas'][id].stock}</option>`);
		});
		$("#invbodega_idM").selectpicker('refresh');
	}	
}

function sumbod(i,y,aux_orig){
	aux_stockcant = parseFloat($("#stockcantTD" + y).html());
	if(aux_orig == "OD"){
		if($("#invcant" + y).attr("stadespsinstock") == "0"){
			if ($("#invcant" + y).val() > aux_stockcant){
				$("#invcant" + y).val(aux_stockcant);
			}	
		}
	}
	totalSolDe = 0;
	totalSolDeStock = 0;
	$("#tabla-bod tr .bod" + i).each(function() {
		//SUMAR STOCK DISPONIBLE PARA EL APARTADO EN BODEGA SOLICITUD DE DESPACHO
		//SUMAR TOTAL UTILIZADO DE APARTADO DE BODEDA SOLICITUD DE DESPACHO
		if($(this).attr("nomabrbod") == "SolDe"){
			if($(this).val() == ""){
				valor = "0";
			}else{
				valor = $(this).val();
			}
			valorNum = parseFloat(valor);
			totalSolDe += valorNum;	
			if($(this).attr("stockvalororig") == ""){
				valor = "0";
			}else{
				valor = $(this).attr("stockvalororig");
			}
			valorNum = parseFloat(valor);
			totalSolDeStock += valorNum;	
		}
	});
	//console.log(totalSolDe);
	total = 0;
	$("#tabla-bod tr .bod" + i).each(function() {
		if($(this).val() == ""){
			valor = "0";
		}else{
			valor = $(this).val() ;
		}
		valorNum = parseFloat(valor);
		total += valorNum;
		/*
		aux_saldo = parseFloat($("#saldocantOrigF" + i).html());
		if(total > aux_saldo){
			dif = aux_saldo - (total - valor);
			$(this).val(dif);			
			total = aux_saldo;
		}
		*/
	});
	if($("#invcant" + y).attr("nomabrbod") == "SolDe"){
		aux_saldo = parseFloat($("#saldocantOrigF" + i).html());
	}else{
		aux_saldo = parseFloat($("#saldocantOrigF" + i).html());
		aux_saldo = aux_saldo - totalSolDeStock + totalSolDe;
	}
	if(total > aux_saldo){
		if($("#invcant" + y).val() == ""){
			valor = "0";
		}else{
			valor = $("#invcant" + y).val() ;
		}
		dif = aux_saldo - (total - valor);
		if(dif <=0 ){
			$("#invcant" + y).val("");	
		}else{
			$("#invcant" + y).val(dif);
		}
		//console.log(dif);
		total = aux_saldo;
	}
	if(total < 0){
		total = 0;
	}

	aux_invcant = $("#invcant" + y).val();
	aux_invcant = parseFloat(aux_invcant);
	if(aux_invcant <= 0){
		$("#invcant" + y).val("")
	}
	$("#cantord" + i).val(total);
	$("#cantsol" + i).val(total);
	actSaldo(i);
}

function sumbodrec(i,y){
	total = 0;
	$("#tabla-bodrec tr .bodrec" + i).each(function() {
		if($(this).val() == ""){
			valor = "0";
		}else{
			valor = $(this).val() ;
		}
		valorNum = parseFloat(valor);
		total += valorNum;
		/*
		aux_saldo = parseFloat($("#saldocantOrigF" + i).html());
		if(total > aux_saldo){
			dif = aux_saldo - (total - valor);
			$(this).val(dif);			
			total = aux_saldo;
		}
		*/
	});
	//console.log(total);
	//aux_saldo = parseFloat($("#saldocantOrigF" + i).html());
	aux_saldo = parseFloat($("#cantTD" + i).html()) - parseFloat($("#cantorddespF" + i).html());
	//console.log(aux_saldo);
	//console.log(aux_saldo);
	if(total > aux_saldo){
		//console.log("entro");
		if($("#invcant" + y).val() == ""){
			valor = "0";
		}else{
			valor = $("#invcant" + y).val() ;
		}
		dif = aux_saldo - (total - valor);
		$("#invcant" + y).val(dif);
		//console.log(dif);
		total = aux_saldo;
	}

	$("#cantord" + i).val(total);
	actSaldo(i);
}

function crearEditarAcuTec(i){
	$("#at_certificados").val("");
	$('.scrollg').animate({

		scrollTop: 0

	}, 2000);

	$("#at_claseprod_id").empty();
    $("#at_claseprod_id").append("<option value=''>Seleccione...</option>");
    //alert($(this).val());
	var palabraEnVariable = $("#nombreProdTD"  + i).attr("categoriaprod_nombre");
	var palabraBuscada = "Film Strech";
	// Convertir ambas palabras a minúsculas antes de comparar
	if (palabraEnVariable.toLowerCase().includes(palabraBuscada.toLowerCase())) {
		// Agregar una clase al elemento
		$("#at_formatofilm").addClass("valorrequerido");
		$("#div_at_formatofilm").css('display','block');
		$("#at_formatofilm").val("");
	} else {
		// Eliminar una clase del elemento
		$("#at_formatofilm").removeClass("valorrequerido");
		$("#div_at_formatofilm").css('display','none');
		$("#at_formatofilm").val("0");
	}
    var data = {
        categoriaprod_id: $("#producto_idTDT" + i).attr("categoriaprod_id"),
        _token: $('input[name=_token]').val()
    };
    $.ajax({
        url: '/producto/obtClaseProd',
        type: 'POST',
        data: data,
        success: function (claseprod) {
            for (i = 0; i < claseprod.length; i++) {
                $("#at_claseprod_id").append("<option value='" + claseprod[i].id + "'>" + claseprod[i].cla_nombre + "</option>");
            }
			if(acuerdotecnico){
				$("#at_claseprod_id").val(acuerdotecnico["at_claseprod_id"]);
			}
			$(".selectpicker").selectpicker('refresh');
            /*
            $.each(claseprod, function(index,value){
                $(".claseprod_id").append("<option value='" + index + "'>" + value + "</option>")
            });
            */
        }
    });


	$(".selectpicker").selectpicker('refresh');
	$("#aux_numfilaAT").val(i);
	$(".form_acutec").each(function(){
		$(this).val("");
		//alert($(this).attr('name'));
		if($(this).attr('name') == "at_certificados"){
			$(this).val([]);
		}
	});
	if (palabraEnVariable.toLowerCase().includes(palabraBuscada.toLowerCase())) {
		$("#at_formatofilm").val("");
	} else {
		$("#at_formatofilm").val("0");
	}
	var acuerdotecnico = JSON.parse($("#acuerdotecnico" + i).val());
	//console.log(acuerdotecnico);
	for (const property in acuerdotecnico) {
		if((property != 'id') && (property != 'updated_at')){ //Para evitar que cambie el valor del campo id o updated_at del formulario aprobar cotizacion
			if( property == 'at_certificados'){
				let str = acuerdotecnico[property];
				let arr = str.split(','); 
				$("#" + property).val(arr);
			}else{
				$("#" + property).val(acuerdotecnico[property]);
			}	
		}else{
			if(property == 'id'){
				$("#at_id").val(acuerdotecnico[property]);
			}
		}
	}
	$("#at_anchoum_id").val(1);
	$("#at_largoum_id").val(1);
	$("#at_fuelleum_id").val(1);
	$("#at_espesorum_id").val(2);
	$(".valorrequerido").each(function(){
		quitarValidacion($(this).prop('name'),$(this).attr('tipoval'));
	});
	$("#lbltitAT1").html("Acuerdo Tecnico: " + $("#nombreProdTD" + i).html())
	//$("#lbltitAT2").html("Acuerdo Tecnico: " + $("#nombreProdTD" + i).html())
	if($("#tipoprod"+i).val() == 1){		 
		aux_tituloAT = "<FONT SIZE=3 style='color:red;'>Nuevo</font> Acuerdo Tecnico: " + $("#nombreProdTD" + i).attr("categoriaprod_nombre");
		$("#lbltitAT1").html(aux_tituloAT);
		//$("#lbltitAT2").html(aux_tituloAT);
	}
	$("#at_tiposello_id").val(1);
	$("#at_unidadmedida_id").val($("#unidadmedida_id" + i).val())
	$("#at_unidadmedida_nombre").val($("#at_unidadmedida_id option:selected").html())
	$(".selectpicker").selectpicker('refresh');
	embalajePlastiservi();
    $("#myModalAcuerdoTecnico").modal('show');
}

/*
function genpdfAcuTec(id){ //GENERAR PDF Acuerdo Tecnico
	if($("#contpdf")){
		$("#contpdf").modal('hide');
	}
	$('#contpdf').attr('src', '/producto/'+id+'/acutecexportPdf');
	$("#myModalpdf").modal('show')
}
*/

$("#btnbuscarproductogen").click(function(event){
    //$(this).val("");
    $(".input-sm").val('');
    aux_id = $("#producto_idPxP").val();
    if( aux_id == null || aux_id.length == 0 || /^\s+$/.test(aux_id) ){
        $("#divprodselec").hide();
        $("#productos").html("");
    }else{
        arraynew = aux_id.split(',')
        $("#productos").html("");
        for(var i = 0; i < arraynew.length; i++){
            $("#productos").append("<option value='" + arraynew[i] + "' selected>" + arraynew[i] + "</option>")
        }
        $("#divprodselec").show();
    }
    $("#myModalBuscarProd").modal('show');
});


function redirigirARuta(ruta){
	setTimeout(function(){
		// *** REDIRECCIONA A UNA RUTA*** 
		var loc = window.location;
		window.location = loc.protocol+"//"+loc.hostname+"/"+ruta;
		// ******************************
	}, 2500,ruta);
}

function llenarselectGD(i,dte_id,nrodocto){
	let strdte_id = $("#selectguiadesp").val();
	strdte_id = strdte_id.trim();
	let arrdte_id = strdte_id.split(','); 
	if(strdte_id == ""){
		arrdte_id.pop();
	}
	let str = dte_id.toString();
	indice = arrdte_id.indexOf(str);
	if(indice != -1){
		arrdte_id.splice(indice, 1);
	}else{
		arrdte_id.push(dte_id);
	}
	//arrdte_id.toString();
	arrdte_id = arrdte_id.sort((a,b) => parseInt(a) > parseInt(b) ? 1 : -1);

	$("#selectguiadesp").val(arrdte_id.toString());
	for (i = 0; i < arrdte_id.length; i++) {
		aux_text = 
		"<a class='btn-accion-tabla btn-sm tooltipsC' title='Guia Despacho: " + data.nrodocto + "' onclick='genpdfGD(" + data.nrodocto + ",\"\",\"\")'>"+
			+ data.nrodocto +
		"</a>";

	}

	let aux_selectguiadesp = $("#selectguiadesp").val();
	aux_selectguiadesp = aux_selectguiadesp.trim();
	if(aux_selectguiadesp == ""){
		$("#btnaceptarGD").attr('disabled', true);
	}else{
		$("#btnaceptarGD").attr('disabled', false);
	}
}


function delguiadespfactdet(nrodocto,id,dte_id){ //Borrar guias de despacho del detalle de facturar Guias despacho
	swal({
		title: '¿ Seguro desea eliminar ?',
		text: "Se eliminaran todos los item asociados a la guia: " + nrodocto,
			icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			$("." + nrodocto).remove();
			llenarselectGD(0,dte_id,nrodocto)		

			totalizar();
			//SI EL NETO TOTAL DE FACTURA EN MENOR O IGUAL A CERO BORRO TODOS LOS DATOS
			//SI EL NETO ES MAYOR A CERO ENTONCES VOELVO A EJECUTAR EL BOTON QUE AGREGA PRODUCTOS A LA FACTURA
			if($("#tdneto").attr("valor") <= 0){
				//**ELIMINAR VALORES EN FACTURA: VENDEDOR, CENTRO ECONOMICO,  HEP, notped */
				$("#fchvenc").val("");
				$("#vendedor_id").val("");
				$("#centroeconomico_id").val("");
				$("#hep").val("");
				$("#notped").val("");
				$("#obs").val("");
				$("#lblocnv_id").html("OC");
				$("#ocnv_id").val("");
				$("#ocnv_id").attr("disabled", true);
				$("#oc_id").attr("disabled", true);
				$("#notaventa_id").val("");
				$('#group_oc_file').hide()
				$('.select2').trigger('change');
				//** */
			}else{
				$("#btnaceptarGD").click();
			}
			/*
			if($("#ocnv_id")){
				$("#btnaceptarGD").click();
			}
			*/
		}
	});
}

function sumarDias(fecha, dias){
	fecha.setDate(fecha.getDate() + dias);
	return fecha;
}


$("#VerAcuTec").change(function(){
	cargardatospantprod();
});

$("#VerTodosProd").change(function(){
	primerfiltrobuscarprod();
});

function cargardatospantprod(){
	$('#DivchVerAcuTec').show()
	$(this).val("");
	$(".input-sm").val('');
	data = datos();
	let aux_tipoprod = "0";
	if($("#tipoprod").val()){ //0=PRODUCTO NORMAL, 1=PRODUCTO TRANSACCIONAL PARA HACER ACUERDO TECNICO, 2=PRODUCTO PARA HACER FACTURA DIRECTA
        aux_tipoprod = $("#tipoprod").val();
    }
	$("#lbltipoprod").html("Productos");
	$("#lblVerAcuTec").attr("data-original-title","Ver Productos Base para crear Acuerdo Técnico");

	if($("#VerAcuTec").prop("checked")){
		aux_tipoprod = "1";
		$("#VerTodosProd").prop("checked",false);
		$("#lbltipoprod").html("Productos Base para crear Acuerdo Técnico");
		$("#lblVerAcuTec").attr("data-original-title","Ver Productos existentes");		
	}
	if($("#VerTodosProd").prop("checked")){
		aux_tipoprod = "0";
		$("#VerAcuTec").prop("checked",false);
		$("#lbltipoprod").html("Productos");
		$("#lblVerAcuTec").attr("data-original-title","Ver Productos Base para crear Acuerdo Técnico");		
	}
	if(data.data1.sucursal_id){
		let posicion1 = data.data1.sucursal_id.indexOf('1');//Para visualizar el status de Productos base para acuerdo tecnico Santa Ester
		let posicion3 = data.data1.sucursal_id.indexOf('3');//Para visualizar el status de Productos base para acuerdo tecnico Puerto Montt
		if(posicion1 >= 0 || posicion3 >= 0){
			$("#staprodxcli").css({'display':'block'});
		}
	}
	if(typeof aux_staprodxcli !== 'undefined' && aux_staprodxcli){
		$("#staprodxcli").css({'display':'block'});
		$("#DivVerTodosProd").css({'display':'none'});
		$("#DivchVerAcuTec").css({'display':'none'});
		if(aux_DivVerTodosProd){
			$("#DivVerTodosProd").css({'display':'block'});
		}
		if(aux_DivchVerAcuTec){
			$("#DivchVerAcuTec").css({'display':'block'});
		}
	}
	//console.log(aux_tipoprod);
	$('#tabla-data-productos').DataTable().ajax.url( "productobuscarpage/" + data.data2 + "&producto_id=&tipoprod=" + aux_tipoprod ).load();
}

function primerfiltrobuscarprod(){
	//$('#DivchVerAcuTec').show()
	$(this).val("");
	$(".input-sm").val('');
	data = datos();
	aux_tipoprod = "0";

	$("#lblTitVerTosdosProd").html("Productos X Cliente");
	$("#lblVerTodosProd").attr("data-original-title","Ver Productos X Cliente");

	if($("#VerTodosProd").prop("checked")){
		aux_tipoprod = "";
		$("#VerAcuTec").prop("checked",false);
		$("#lbltipoprod").html("Productos");
		$("#lblVerAcuTec").attr("data-original-title","Ver Productos Base para crear Acuerdo Técnico");
	
		$("#lblTitVerTosdosProd").html("Todos los productos");
		$("#lblVerTodosProd").attr("data-original-title","Ver Productos X Cliente");

		var data1 = {
			cliente_id  : "",
			sucursal_id : $("#sucursal_id").val(),
			_token      : $('input[name=_token]').val()
		};
	
		var data2 = "?cliente_id="+data1.cliente_id +
		"&sucursal_id="+data1.sucursal_id
	
		var data = {
			data1 : data1,
			data2 : data2
		};
	}
	$('#tabla-data-productos').DataTable().ajax.url( "productobuscarpage/" + data.data2 + "&producto_id=&tipoprod=" + aux_tipoprod ).load();
}

function genpdfAcuTecTemp(id,cliente_id,aux_venmodant = ""){ //GENERAR PDF Acuerdo Tecnico Temporar y final
	//console.log(id);
	//console.log(cliente_id);
	let data = "?id="+id +
    "&cliente_id="+cliente_id

	$("#venmodant").val(""); //Ventana Modal Anterior
	if(aux_venmodant!=""){
		$("#" + aux_venmodant).modal('hide');
		$("#venmodant").val(aux_venmodant);
	}
	$('#contpdf').attr('src', '/acuerdotecnicotemp/exportPdf/' + data);

	$("#myModalpdf").modal('show')
	//$("#modal-bodymymodadpdf").attr("style","height: 75%");
}

function genpdfAcuTec(id,cliente_id,aux_venmodant = ""){ //GENERAR PDF Acuerdo Tecnico Temporar y final
	//console.log(id);
	//console.log(cliente_id);
	let data = "?id="+id +
    "&cliente_id="+cliente_id

	$("#venmodant").val(""); //Ventana Modal Anterior
	if(aux_venmodant!=""){
		$("#" + aux_venmodant).modal('hide');
		$("#venmodant").val(aux_venmodant);
	}
	$('#contpdf').attr('src', '/acuerdotecnico/exportPdf/' + data);

	$("#myModalpdf").modal('show')
	//$("#modal-bodymymodadpdf").attr("style","height: 75%");
}


$("#totalkilosM").blur(function(e){
	if($(this).attr('valor') != undefined){
		$(this).attr('valor',$(this).val());
	}
});

//FUNCION PARA TOTALIZAR NOTA DE CREDITO Y DEBITO
function totalizarNc(){
	totalizar();
	validarlistcodrefNc();
}

//FUNCTION PARA VALIDAR LO QUE SE MUESTRA EN EL SELECT CODREF
function validarlistcodrefNc(){
	$('#codref option').remove();
	$("#codref").append("<option value=''>Seleccione...</option>");
	let foliocontrol_id = $("foliocontrol_id").val();
	let tdfoliocontrol_id = $("#tdfoliocontrol_id").val();
	if($("#tdtotaloriginal").attr("valor") == $("#tdtotalmodificado").attr("valor")){
		let aux_bandera = false;
		let dtefoliocontrol_id = $("#dtefoliocontrol_id").val();
		let foliocontrol_id = $("#foliocontrol_id").val();
		/*
		if((dtefoliocontrol_id == 5 && foliocontrol_id == 1) || (dtefoliocontrol_id == 5 && foliocontrol_id == 6)){
			aux_bandera = true;
		}else{

		}
		*/
		$("#codref").append("<option value='1'>Anula Documento de Referencia</option>");
	}
	if(tdfoliocontrol_id == 1 || tdfoliocontrol_id == 7){
		$("#codref").append("<option value='2'>Corrige Texto Documento Referencia</option>");
		$("#codref").append("<option value='3' selected>Corrige montos</option>");
	}
	$(".selectpicker").selectpicker('refresh');
}

//FUNCION PARA TOTALIZAR NOTA DE CREDITO Y DEBITO
function totalizarNd(){
	totalizar();
	validarlistcodrefNd();
}

//FUNCTION PARA VALIDAR LO QUE SE MUESTRA EN EL SELECT CODREF
function validarlistcodrefNd(){
	$('#codref option').remove();
	$("#codref").append("<option value=''>Seleccione...</option>");
	if($("#tdfoliocontrol_id").val() == 1 || $("#tdfoliocontrol_id").val() == 7){
		$("#codref").append("<option value='3' selected>Corrige montos</option>");
	}else{
		$("#codref").append("<option value='1'>Anula Documento de Referencia</option>");
	}
	//$("#codref").val("");
	//$('.select2').trigger('change');
	//$(".selectpicker").selectpicker('refresh');
}

async function buscarDatosProd(producto_id){
	codigo = producto_id.val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		var data = {
			id: codigo,
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

$("#foliocontrol_id").change(function(){
	totalizar();
});

function buscarProdKeyUp(obj,event){
	//console.log(obj)
	if(event.which==113){
		//console.log(obj);
		$(obj).val("");
		//console.log($(obj).parent().parent().attr("item"));
		cargardatospantprod();
		$("#itemAct").val($(obj).parent().parent().attr("item")); //Crear input Item actual
		$("#myModalBuscarProd").modal('show');
	}
}

function buscarProd(item){
	//console.log($(obj).parent().parent().attr("item"));
	cargardatospantprod();
	$("#itemAct").val(item); //Crear input Item actual
	$("#myModalBuscarProd").modal('show');
}

//FUNCTION CON ASYNC, YA QUE ME INTERESA ESPERAR LA RESPUESTA DE LA BUSQUEDA
async function llenarDatosProd(vlrcodigo){
	let item = vlrcodigo.attr("item");
	//console.log(item);
	if($("#vlrcodigo" + item).val() != $("#producto_id" + item).val()){
		arrayDatosProducto = await buscarDatosProd(vlrcodigo);
		//console.log(arrayDatosProducto);
		$("#producto_id" + item).val("");
		$("#nmbitem" + item).val("");
		$("#prcitem" + item).val("0");
		if(arrayDatosProducto['cont'] > 0){
			$("#lblproducto_id" + item).html(arrayDatosProducto["id"]);
			$("#vlrcodigo" + item).val(arrayDatosProducto["id"]);
			$("#producto_id" + item).val(arrayDatosProducto["id"]);
			$("#nombreProdTD" + item).html(arrayDatosProducto["nombre"]);
			$("#nmbitem" + item).val(arrayDatosProducto["nombre"]);
			//$("#prcitem" + item).val(arrayDatosProducto["precio"]);
			$("#prcitem" + item).val("");
			//console.log(arrayDatosProducto["acuerdotecnico"]);
			if(arrayDatosProducto["acuerdotecnico"] != null){
				//console.log(arrayDatosProducto["acuerdotecnico"].at_ancho);
				at_ancho = arrayDatosProducto["acuerdotecnico"].at_ancho;
				at_largo = arrayDatosProducto["acuerdotecnico"].at_largo;
				at_espesor = arrayDatosProducto["acuerdotecnico"].at_espesor;
				at_ancho = (at_ancho === null || at_ancho === undefined || at_ancho === "") ? "0.00" : at_ancho;
				at_largo = (at_largo === null || at_largo === undefined || at_largo === "") ? "0.00" : at_largo;
				at_espesor = (at_espesor === null || at_espesor === undefined || at_espesor === "") ? "0.00" : MASKLA(at_espesor,3);
				//$aux_nombreprod = $aux_nombreprod . " " . $at_ancho . "x" . $at_largo . "x" . $at_espesor;

				aux_formatofilm = arrayDatosProducto["acuerdotecnico"].at_formatofilm > 0 ? " " + MASKLA(arrayDatosProducto["acuerdotecnico"].at_formatofilm,2)  + "Kg." : "";
				aux_color = (arrayDatosProducto["at_color_nombre"] === null || arrayDatosProducto["at_color_nombre"] === undefined || arrayDatosProducto["at_color_nombre"] === "") ? "" : " " + arrayDatosProducto["at_color_nombre"];
				aux_at_complementonomprod = arrayDatosProducto["acuerdotecnico"].at_complementonomprod === null ? "" : " " + arrayDatosProducto["acuerdotecnico"].at_complementonomprod;
				aux_atribAcuTec = arrayDatosProducto["at_materiaprima_nombre"] + aux_color + aux_at_complementonomprod + aux_formatofilm;
				//CONCATENAR TODO LOS CAMPOS NECESARIOS PARA QUE SE FORME EL NOMBRE DEL RODUCTO EN LA GUIA
				aux_nombreprod = arrayDatosProducto["categoriaprod_nombre"] + " " + aux_atribAcuTec + " " + at_ancho + "x" + at_largo + "x" + at_espesor;
			}else{
				//console.log("Sin Acuerdo");
				//console.log(arrayDatosProducto);
				aux_cla_nombre = arrayDatosProducto["cla_descripcion"];
				aux_cla_nombre = aux_cla_nombre == "N/A" ? "" : " " + aux_cla_nombre; // str_replace("N/A","",arrayDatosProducto["cla_descripcion"]);
				aux_diametro = arrayDatosProducto["diametro"];
				aux_diametro = (aux_diametro === null || aux_diametro === undefined || aux_diametro === "" || aux_diametro === "0") ? "" : " D:" + arrayDatosProducto["diametro"];
				aux_long = arrayDatosProducto["long"]  ? " L:" + arrayDatosProducto["long"] : "";
				aux_tipounion = "";
				if(!(arrayDatosProducto["tipounion"] === "S/C" || arrayDatosProducto["tipounion"] === "S/U")){
					aux_tipounion = " " + arrayDatosProducto["tipounion"];
				}                                        
				aux_nombreprod = arrayDatosProducto["nombre"] + aux_diametro + aux_long + aux_cla_nombre + aux_tipounion;

			}
			$("#nmbitem" + item).val(aux_nombreprod);
			/*
			if(arrayDatosProducto["acuerdotecnico"]){
				$at_ancho = arrayDatosProducto["acuerdotecnico"].at_ancho;
				$at_largo = $producto->acuerdotecnico->at_largo;
				$at_espesor = $producto->acuerdotecnico->at_espesor;
				$at_ancho = empty($at_ancho) ? "0.00" : $at_ancho;
				$at_largo = empty($at_largo) ? "0.00" : $at_largo;
				$at_espesor = empty($at_espesor) ? "0.00" : $at_espesor;
				//$aux_nombreprod = $aux_nombreprod . " " . $at_ancho . "x" . $at_largo . "x" . $at_espesor;

				$AcuTec = $producto->acuerdotecnico;
				$aux_formatofilm = $AcuTec->at_formatofilm > 0 ? " " . number_format($AcuTec->at_formatofilm, 2, ',', '.') . "Kg." : "";
				$aux_color =  empty($AcuTec->color->descripcion) ? "" : " " . $AcuTec->color->descripcion;
				$aux_at_complementonomprod = empty($AcuTec->at_complementonomprod) ? "" : " " . $AcuTec->at_complementonomprod;
				$aux_atribAcuTec = $AcuTec->materiaprima->descfact . $aux_color . $aux_at_complementonomprod . $aux_formatofilm;
				//CONCATENAR TODO LOS CAMPOS NECESARIOS PARA QUE SE FORME EL NOMBRE DEL RODUCTO EN LA GUIA
				$aux_nombreprod = nl2br($producto->categoriaprod->nombre . " " . $aux_atribAcuTec . " " . $at_ancho . "x" . $at_largo . "x" . number_format($AcuTec->at_espesor, 3, ',', '.'));
			}else{
				//CUANDO LA CLASE TRAE N/A=NO APLICA CAMBIO ESTO POR EMPTY ""
				$aux_cla_nombre =str_replace("N/A","",$producto->claseprod->cla_descripcion);
				$aux_diametro = $producto->diametro > 0 ? " D:" . $producto->diametro : "";
				$aux_long = $producto->long ? " L:" . $producto->long : "";
				$aux_tipounion = "";
				if(!($producto->tipounion === "S/C" or $producto->tipounion === "S/U")){
					$aux_tipounion = $producto->tipounion;
				}                                        
				$aux_nombreprod = $aux_nombreprod . $aux_diametro . $aux_long . " " . $aux_cla_nombre. " " . $aux_tipounion;
			}
			*/

			
		}
		calsubtotalitem($("#vlrcodigo" + item));	
	}
}

function validarItemVacios(){
	//VALIDAR QUE LOS ITEM DE PRODUCTOS NO QUEDEN EN BLANCO
	//$(".itemrequerido").change(function(){
		//console.log("entro");
		$('.itemrequerido').each(function(){
			let id = $(this).attr("id");
			let valor = $(this).val();
			$("#itemcompletos").val("");
			if( (valor == null || valor.length == 0 || /^\s+$/.test(valor)) || valor == ""){
				//$("#itemcompletos").val("");
				let item = $(this).parent().parent().attr("item");
				$("#lblitemcompletos").html($(this).attr("title") + " item: " + $("#nroitem" + item).html());
				//$(this).focus();
				return false;
			}else{
				$("#itemcompletos").val("1");
			}
			//console.log('id: ' + id + '  Valor: ' + valor);
		});	
	//});
}

function volverGenDTE(dte_id){
	var data = {
        dte_id : dte_id,
        updated_at : $("#updated_at" + dte_id).html(),
        _token: $('input[name=_token]').val()
    };
    var ruta = '/dteguiadesp/volverGenDTE';
    //var ruta = '/guiadesp/dteguiadesp';
    swal({
        title: '¿ Generar DTE ?',
        text: "Esta acción no se puede deshacer!",
        icon: 'warning',
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
        if (value) {
            ajaxRequestGeneral(data,ruta,'volverGenDTE');
        }
    });

}

function ajaxRequestGeneral(data,url,funcion) {
	datatemp = data;
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='volverGenDTE'){
				swal({
					title: respuesta.titulo,
					text: respuesta.mensaje,
					icon: respuesta.tipo_alert,
					buttons: {
						confirm: "Aceptar"
					},
				}).then((value) => {
				});
			}
			if(funcion=='procesarDTE'){
				if (respuesta.id != 0) {
                    //genpdfFAC(respuesta.nrodocto,"_U");
                    $("#fila"+respuesta.nfila).remove();
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
                    swal({
						title: respuesta.title,
						text: respuesta.mensaje,
						icon: respuesta.tipo_alert,
						buttons: {
							confirm: "Aceptar"
						},
					}).then((value) => {
					});
					//Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
				}
			}
			if(funcion=='anulardte'){
				if (respuesta.id == 1) {
                    //genpdfND(respuesta.nrodocto,"_U");
                    $("#fila"+datatemp.nfila).remove();
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
                    swal({
						title: respuesta.title,
						text: respuesta.mensaje,
						icon: 'error',
						buttons: {
							confirm: "Cerrar"
						},
					});
					//Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
				}
			}
			if(funcion=='liberarguiadesp'){
				if (respuesta.id == 1) {
                    //genpdfND(respuesta.nrodocto,"_U");
                    $("#fila"+datatemp.nfila).remove();
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
                    swal({
						title: respuesta.title,
						text: respuesta.mensaje,
						icon: 'error',
						buttons: {
							confirm: "Cerrar"
						},
					});
					//Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
				}
				
			}
		},
		error: function () {
		}
	});
}

function anulardte(id){
    var data = {
        dte_id : id,
        nfila  : id,
        updated_at : $("#updated_at" + id).html(),
        _token: $('input[name=_token]').val()
    };
    var ruta = '/dtefactura/anular';
    //var ruta = '/guiadesp/dteguiadesp';
    swal({
        title: '¿ Anular DTE ?',
        text: "Esta acción no se puede deshacer!",
        icon: 'warning',
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
        if (value) {
            ajaxRequestGeneral(data,ruta,'anulardte');
        }
    });
}

function procesarDTE(id){
    var data = {
        dte_id : id,
        nfila  : id,
        updated_at : $("#updated_at" + id).html(),
        _token: $('input[name=_token]').val()
    };
    var ruta = '/dtefactura/procesar';
    //var ruta = '/guiadesp/dteguiadesp';
    swal({
        title: '¿ Procesar DTE ?',
        text: "Esta acción no se puede deshacer!",
        icon: 'warning',
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
        if (value) {
            ajaxRequestGeneral(data,ruta,'procesarDTE');
        }
    });

}

//funcion rellena con ceros a la izquierda
function zfill(number, width) {
    var numberOutput = Math.abs(number); /* Valor absoluto del número */
    var length = number.toString().length; /* Largo del número */ 
    var zero = "0"; /* String de cero */  
    
    if (width <= length) {
        if (number < 0) {
             return ("-" + numberOutput.toString()); 
        } else {
             return numberOutput.toString(); 
        }
    } else {
        if (number < 0) {
            return ("-" + (zero.repeat(width - length)) + numberOutput.toString()); 
        } else {
            return ((zero.repeat(width - length)) + numberOutput.toString()); 
        }
    }
}

function validarRut(rut) {
	// Eliminar cualquier caracter que no sea número o K
	rut = rut.replace(/[^0-9kK]/g, '');
	
	// Verificar que el rut tenga 9 dígitos
	if (rut.length !== 9) {
	  return false;
	}
	
	// Extraer el dígito verificador del rut
	var dv = rut.charAt(8);
	
	// Verificar que el dígito verificador sea numérico o K
	if (!/^[0-9kK]{1}$/.test(dv)) {
	  return false;
	}
	
	// Verificar que los 8 primeros dígitos sean numéricos
	var rutNumerico = parseInt(rut.substring(0, 8));
	if (isNaN(rutNumerico)) {
	  return false;
	}
	
	// Calcular el dígito verificador esperado
	var suma = 0;
	var factor = 2;
	for (var i = 7; i >= 0; i--) {
	  suma += parseInt(rut.charAt(i)) * factor;
	  factor = factor === 7 ? 2 : factor + 1;
	}
	var dvEsperado = 11 - (suma % 11);
	if (dvEsperado === 11) {
	  dvEsperado = 0;
	} else if (dvEsperado === 10) {
	  dvEsperado = 'K';
	}
	
	// Comparar el dígito verificador calculado con el dígito verificador del rut
	return dv.toString().toLowerCase() === dvEsperado.toString().toLowerCase();
}

function validarInputRut(event) {
	// Obtener el input del usuario
	var rutInput = event.target.value;
	
	// Eliminar cualquier caracter que no sea número o K
	rutInput = rutInput.replace(/[^0-9kK]/g, '');
	
	// Si el input tiene más de 9 caracteres, recortarlo a 9
	if (rutInput.length > 9) {
	  rutInput = rutInput.substring(0, 9);
	}
	
	// Asignar el input validado al input del usuario
	event.target.value = rutInput;
}


function activarCajasPreciokgUni(){
	console.log("entro cambio unidad");
	$(".selectpicker").selectpicker('refresh');
	$("#precioM").prop("disabled", false);
	$("#precionetoM").prop("disabled", false);
	$("#precioM").attr('staAT',0);
	$("#unidadmedida_idM").prop("disabled", false);
	$("#totalkilosM").prop("disabled", true);	
	$("#totalkilosM").prop("readonly", true);	
	/*
	$("#acuerdotecnico_id").val(respuesta['acuerdotecnico_id']);
	$("#tipoprodM").val(respuesta['tipoprod'])
	*/
	aux_staAT = $("#acuerdotecnico_id").val();
	aux_tipoProd = $("#tipoprodM").attr('valor');
	if(aux_staAT > 0 || aux_tipoProd == 1){
		if(aux_staAT > 0){
			$("#unidadmedida_idM").prop("disabled", true);
			console.log($("#at_unidadmedida_idM").val());
			$("#unidadmedida_idM").val($("#at_unidadmedida_idM").val())
			if($("#at_unidadmedida_idM").val() != 7){
				$("#totalkilosM").prop("disabled", false);
				$("#totalkilosM").prop("readonly", false);
			}
		}else{
			$("#unidadmedida_idM").prop("disabled", false);
		}
		aux_UM = $("#unidadmedida_idM").val(); 
		$("#precioM").attr('staAT',1);
		if(aux_UM == 7){
			if($("#pesoM").val() == 0){
				$("#precionetoM").prop("disabled", true);
			}
			$("#totalkilosM").prop("disabled", true);	
			$("#totalkilosM").prop("readonly", true);	
		}else{
			$("#precionetoM").prop("disabled", false);
			$("#totalkilosM").prop("disabled", false);
			$("#totalkilosM").prop("readonly", false);	
		}
	}
}

function ordentablaGen(aux_tabla){
    var tabla = aux_tabla; //$('#tablacotizacion').DataTable();
    var ordenActual = tabla.order(); //obtengo el array del orden actual de la tabla
	aux_orden = "";
    if(ordenActual == undefined){
        aux_orden = "0,'1','asc'";
    }else{
        var encabezado = tabla.column(ordenActual[0][0]).header(); //en la posicion [0][0] esta el num de columna
        var nombreCampo = encabezado.getAttribute('nombrecampo'); //a traves del atributo nombrecampo que asigne en php al crear la tabla en los encabezados
        if(ordenActual[0][2]){
            ordenActual[0][2] = nombreCampo;
        }else{
            ordenActual[0].push(nombreCampo); //inserto a array el nombre de campo por el cual quiero ordenar en la consulta SQL en php
        }
		aux_orden = ordenActual[0];
        //$("#orden").val(ordenActual[0]); //Actualizo el valor del elemento orden para que lo envie a php    
    }
	return aux_orden;
}

function descXMLfac1(nombreArchivo){ //GENERAR PDF Factura
	/*
	var data = {
        nombreArchivo : nombreArchivo,
		_token: $('input[name=_token]').val()
    };
	$.ajax({
		url: '/dtefactura/descargar-xml',
		type: 'POST',
		data: data,
		success: function() {
			// La descarga se inicia automáticamente.
		},
		error: function() {
			console.log("Error al descargar el archivo XML.");
		}
	});
	*/
	/*
	let queryString = '?timestamp=' + new Date().getTime();
	$('#contpdf').attr('src', '/storage/facturacion/dte/procesados/DTE_T33FE00000303.xml' + queryString);
	$("#myModalpdf").modal('show');
	*/
	/*
	var loc = window.location;
	window.location = loc.protocol+"//"+loc.hostname+"/storage/facturacion/dte/procesados/DTE_T33FE00000303.xml";
	*/
	/*
	var urlDescarga = 'dtefactura/descargar-xml?nombreArchivo=' + nombreArchivo; // Ajusta la URL según tu configuración
    var a = document.createElement('a');
    a.href = urlDescarga;
    a.download = nombreArchivo;
    a.style.display = 'none';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
	*/

	var form = document.getElementById('descargaForm');
	var nombreArchivoInput = document.getElementById('nombreArchivo');
	nombreArchivoInput.value = nombreArchivo;
	
	form.submit();

}

function descXML(nombreArchivo) {
    var urlDescarga = '/dtefactura/descargar-xml'; // Ajusta la URL según tu configuración
	var data = {
        nombreArchivo : nombreArchivo,
		_token: $('input[name=_token]').val()
    };

    $.ajax({
        type: 'POST',
        url: urlDescarga,
        data: data,
		xhrFields: {
            responseType: 'blob' // Establece responseType como blob
        },
        success: function(data, textStatus, jqXHR) {
            var blob = data;
            var a = document.createElement('a');
            a.href = window.URL.createObjectURL(blob);
            a.download = nombreArchivo;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        },
        error: function(jqXHR, textStatus, errorThrown) {
			if(errorThrown == "Not Found"){
					errorThrown = 'Archivo no existe!';
			}
			swal({
				title: "Error al descargar el archivo",
				text: "Archivo no existe!",
				icon: 'info',
				buttons: {
					confirm: "Aceptar"
				},
			});
            console.log('Error al descargar el archivo: ' + errorThrown);
        }
    });
}


//FUNCION DESCARGAR ARCHIVO TXT XML
function descArcTXT(nameFile,aux_venmodant = ""){ 
	if(nameFile==""){
		swal({
			title: 'Falta nombre de archivo.',
			text: "",
			icon: 'error',
			buttons: {
				confirm: "Cerrar",
			},
		}).then((value) => {
		});
	}else{
		var data = {
			slug: 'descargar-xml',
			_token: $('input[name=_token]').val()
		};
		$.ajax({
			url: '/generales_valpermiso',
			type: 'POST',
			data: data,
			success: function (respuesta) {
				//console.log(respuesta);
				if(respuesta.resp){
					var urlDescarga = '/dtefactura/descargar-xml'; // Ajusta la URL según tu configuración
					var data = {
						nombreArchivo : nameFile,
						_token: $('input[name=_token]').val()
					};
					$.ajax({
						type: 'POST',
						url: urlDescarga,
						data: data,
						xhrFields: {
							responseType: 'blob' // Establece responseType como blob
						},
						success: function(data, textStatus, jqXHR) {
							var blob = data;
							var a = document.createElement('a');
							a.href = window.URL.createObjectURL(blob);
							a.download = nameFile;
							document.body.appendChild(a);
							a.click();
							document.body.removeChild(a);
						},
						error: function(jqXHR, textStatus, errorThrown) {
							if(errorThrown == "Not Found"){
									errorThrown = 'Archivo no existe!';
							}
							swal({
								title: "Error al descargar el archivo",
								text: "Archivo no existe!",
								icon: 'info',
								buttons: {
									confirm: "Aceptar"
								},
							});
							console.log('Error al descargar el archivo: ' + errorThrown);
						}
					});
				}else{
					swal({
						title: respuesta.mensaje,
						text:  respuesta.mensaje2,
						icon: 'error',
						buttons: {
							confirm: "Cerrar",
						},
					}).then((value) => {
					});
				}
			}
		});
	}
}

function llenarcampostockM(respuesta){
	if ($('#sucursal_id').length) {
		tuObjeto = respuesta["bodegas"];
		for (var clave in tuObjeto) {
			if (tuObjeto.hasOwnProperty(clave)) {
				var objetoInterior = tuObjeto[clave];							
				// Haces algo con el objetoInterior
				//console.log(objetoInterior.id, objetoInterior.nombre, objetoInterior.desc);
				if(objetoInterior.sucursal_id == $('#sucursal_id').val()){
					$("#stockM").val(0);					
					if(objetoInterior.stock >= 0){
						$("#stockM").val(objetoInterior.stock);
					}
				}
			}
		}
	}	
}

function formDataToObject(formData) {
    var object = {};
    formData.forEach(function(value, key){
        object[key] = value;
    });
    return object;
}

function indtrasladoObj(indtraslado){
	data = {
		desc : "Venta",
		letra : "V"
	}
	if(indtraslado == 6){
		data = {
			desc : "Traslado",
			letra : "T"
		}
	}
	return data;
}

function fechaactual(){
	// Obtén la fecha actual
	var fechaActual = new Date();

	// Obtiene el día, mes y año
	var dia = fechaActual.getDate();
	var mes = fechaActual.getMonth() + 1; // Los meses comienzan desde cero, por lo que se suma 1
	var anio = fechaActual.getFullYear();

	// Formatea el día y el mes para asegurarse de que tengan dos dígitos
	dia = (dia < 10) ? '0' + dia : dia;
	mes = (mes < 10) ? '0' + mes : mes;

	// Crea la cadena de fecha en el formato deseado
	var fechaFormateada = dia + '/' + mes + '/' + anio;
	return fechaFormateada;
}

function ajustarcolumnaexcel(worksheet,columna){
    const columnB = worksheet.getColumn(columna);
    let maxLengthB = 0;
    columnB.eachCell({ includeEmpty: true }, (cell) => {
        const length = cell.value ? cell.value.toString().length : 0;
        if (length > maxLengthB) {
        maxLengthB = length;
        }
    });
    columnB.width = maxLengthB < 10 ? 10 : maxLengthB;
}

function getColumnLetter(columnIndex) {
    let columnLetter = '';
    while (columnIndex > 0) {
      const remainder = (columnIndex - 1) % 26;
      columnLetter = String.fromCharCode(65 + remainder) + columnLetter;
      columnIndex = Math.floor((columnIndex - 1) / 26);
    }
    return columnLetter;
}