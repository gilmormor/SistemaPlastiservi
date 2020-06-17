$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
});

function accioninmediata(id,i){
    $(".input-sm").val('');
    $("#titulomodal").html("No Conformidad Id: " + id);
    $("#lbldatos").html("Acción Inmediata")
    var data = {
        id     : id,
        i      : i,
        _token : $('input[name=_token]').val()
    };
    var ruta = '/noconformidadrecep/buscar/' + id;
    ajaxRequest(data,ruta,'accioninmediata');
}

function buscarpasos(id,i,noconformidad){
    if(noconformidad==null){
        var data = {
            id     : id,
            i      : i,
            _token : $('input[name=_token]').val()
        };
        var ruta = '/noconformidadrecep/buscar/' + id;
        ajaxRequest(data,ruta,'buscarpasos');
    
    }else{
        validarpasos(noconformidad);
    }
}

$("#guardarAI").click(function(event)
{
    event.preventDefault();
	if(verificarAI())
	{
        //$("#myModalDatos").modal('hide');
        id = $("#idhide").val();
        var data = {
            id            : id,
            accioninmediata : $("#accioninmediata").val(),
            _token : $('input[name=_token]').val()
        };
        var ruta = '/noconformidadrecep/actai/' + id;

        swal({
			title: '¿ Está seguro que desea Guardar ?',
			text: "Esta acción no se puede deshacer!",
			icon: 'warning',
			buttons: {
				cancel: "Cancelar",
				confirm: "Aceptar"
			},
		}).then((value) => {
			if (value) {
                ajaxRequest(data,ruta,'guardarAI');
			}
		});

	}else{
		alertify.error("Falta incluir informacion");
	}
	
});


$("#guardarACausa").click(function(event)
{
    event.preventDefault();
	if(verificarACausa())
	{
        //$("#myModalDatos").modal('hide');
        id = $("#idhide").val();
        var data = {
            id            : id,
            analisisdecausa : $("#analisisdecausa").val(),
            _token : $('input[name=_token]').val()
        };
        var ruta = '/noconformidadrecep/actacausa/' + id;

        swal({
			title: '¿ Está seguro que desea Guardar ?',
			text: "Esta acción no se puede deshacer!",
			icon: 'warning',
			buttons: {
				cancel: "Cancelar",
				confirm: "Aceptar"
			},
		}).then((value) => {
			if (value) {
                ajaxRequest(data,ruta,'guardarACausa');
			}
		});

	}else{
		alertify.error("Falta incluir informacion");
	}
	
});

$("#guardarACorr").click(function(event)
{
    event.preventDefault();
	if(verificarACorr())
	{
        //$("#myModalDatos").modal('hide');
        id = $("#idhide").val();
        var data = {
            id            : id,
            accorrec : $("#accorrec").val(),
            _token : $('input[name=_token]').val()
        };
        var ruta = '/noconformidadrecep/actacorr/' + id;

        swal({
			title: '¿ Está seguro que desea Guardar ?',
			text: "Esta acción no se puede deshacer!",
			icon: 'warning',
			buttons: {
				cancel: "Cancelar",
				confirm: "Aceptar"
			},
		}).then((value) => {
			if (value) {
                ajaxRequest(data,ruta,'guardarACorr');
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
function verificarAI()
{
	var v1=0;
	
	v1=validacion('accioninmediata','texto');
	if (v1===false)
	{
		return false;
	}else{
		return true;
	}
}

function verificarACausa()
{
	var v1=0;
	v1=validacion('analisisdecausa','texto');
	if (v1===false)
	{
		return false;
	}else{
		return true;
	}
}

function verificarACorr()
{
	var v1=0;
	v1=validacion('accorrec','texto');
	if (v1===false)
	{
		return false;
	}else{
		return true;
	}
}

function ajaxRequest(data,url,funcion) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
            if(funcion=='accioninmediata'){
                $("#ihide").val(data['i']);
                $("#idhide").val(data['id']);
                //$("#motivonc_id").val(respuesta.motivonc);
                var fecha = new Date(respuesta.noconformidad.fechahora);
                var options = { year: 'numeric', month: 'short', day: 'numeric' };

                $("#accioninmediata").val('');
                $("#analisisdecausa").val('');
                $("#accorrec").val('');

                ocultarACausa();
                ocultaracorrect();

                $("#fechanc").html(fecha.toLocaleDateString("es-ES", options));
                $("#horanc").html('<i class="fa fa-clock-o"></i> ' + fecha.toLocaleTimeString('en-US'));
                $("#motivonc_id").html('<a href="#">Motivo de la no conformidad: </a>' + respuesta.motivonc);
                $("#puntonormativo").html('<a href="#">Punto normativo: </a>' + respuesta.noconformidad.puntonormativo);
                
                $("#formadeteccionnc").html('<a href="#">Forma detección: </a>' + respuesta.formadeteccionnc);
                $("#hallazgo").html('<a href="#">Hallazgo: </a>' + respuesta.noconformidad.hallazgo);

                $("#jefaturas").html('<a href="#">Area responsable: </a>' + respuesta.jefaturas.join(", "));
                $("#certificados").html('<a href="#">Norma: </a>' + respuesta.certificados.join(", "));

                $("#puntonorma").html('<a href="#">Punto de la norma: </a>' + respuesta.noconformidad.puntonorma);
                $("#responsables").html('<a href="#">Responsable: </a>' + respuesta.responsables.join(", "));

                $("#analisisdecausa").val(respuesta.noconformidad.analisisdecausa);
                $("#accorrec").val(respuesta.noconformidad.accorrec);
                
                inactAI();
                inactAC();
                inactACorr();
                buscarpasos(data['id'],data['i'],respuesta.noconformidad);

                $("#myModalDatos").modal('show');
                //$(".selectpicker").selectpicker('refresh');
                validacion('accioninmediata','');
            }
            if(funcion=='buscarpasos'){
                validarpasos(respuesta.noconformidad);
            }
            if(funcion=='guardarAI'){
				if (respuesta.mensaje == "ok") {
                    Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
                    i = $("#ihide").val();
                    $('#accioninmediata' + i).attr("class","btn btn-warning btn-sm tooltipsC");
                    $('#iconoai' + i).attr("class","glyphicon glyphicon-ok");
                    buscarpasos(data['id'],data['i']);
                    /*
                    mostrarACausa();
                    var fecha = new Date();
                    actdatosai(fecha,$("#accioninmediata").val());*/
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
            }
            if(funcion=='guardarACausa'){
				if (respuesta.mensaje == "ok") {
                    Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
                    buscarpasos(data['id'],data['i']);
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
            }
            if(funcion=='guardarACorr'){
				if (respuesta.mensaje == "ok") {
                    Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
                    buscarpasos(data['id'],data['i']);
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
            }
        },
		error: function () {
		}
	});
}

function ocultarACausa(){
    $(".acausa").hide();
}
function mostrarACausa(){
    $(".acausa").show();
}

function actAI(){
    $("#accioninmediatatxt").html('<a href="#">Acción Inmediata </a>');
    $("#accioninmediata").prop("readonly",false);
    $("#accioninmediata").fadeIn(500);
    $("#guardarAI").fadeIn(500);
    $("#linebodyai1").fadeIn(500);
    $("#linebodyai2").fadeIn(500);
}


function inactAI(){
    $("#accioninmediatatxt").html('<a href="#">Acción Inmediata: </a>' + $("#accioninmediata").val());
    $("#accioninmediata").prop("readonly",true);
    $("#accioninmediata").fadeOut(500);
    $("#guardarAI").fadeOut(500);
    $("#linebodyai1").fadeOut(500);
    $("#linebodyai2").fadeOut(500);
    //alert('respuesta.noconformidad.accioninmediata');
}

function actAC(){
    $("#analisisdecausatxt").html('<a href="#">Analisis de causa</a>');
    $("#analisisdecausa").prop("readonly",false);
    $("#analisisdecausa").fadeIn(500);
    $("#guardarAC").fadeIn(500);
    $("#linebodyac1").fadeIn(500);
    $("#linebodyac2").fadeIn(500);
}
function inactAC(){
    $("#analisisdecausatxt").html('<a href="#">Analisis de causa: </a>' + $("#analisisdecausa").val());
    $("#analisisdecausa").prop("readonly",true);
    $("#analisisdecausa").fadeOut(500);
    $("#guardarAC").fadeOut(500);
    $("#linebodyac1").fadeOut(500);
    $("#linebodyac2").fadeOut(500);
}


function actACorr(){
    $("#accorrectxt").html('<a href="#">Acción correctiva</a>');
    $("#accorrec").prop("readonly",false);
    $("#accorrec").fadeIn(500);
    $("#guardarACorr").fadeIn(500);
    $("#linebodyacorr1").fadeIn(500);
    $("#linebodyacorr2").fadeIn(500);
}
function inactACorr(){
    $("#accorrectxt").html('<a href="#">Acción correctiva</a>' + $("#accorrec").val());
    $("#accorrec").prop("readonly",true);
    $("#accorrec").fadeOut(500);
    $("#guardarACorr").fadeOut(500);
    $("#linebodyacorr1").fadeOut(500);
    $("#linebodyacorr2").fadeOut(500);
}

function ocultaracorrect(){
    $(".acorrect").hide();
    $("#accioninmediata").prop("readonly",false);
    $("#accioninmediata").fadeIn(500);
    $("#guardarAI").fadeIn(500);
    $(".linebodyai").fadeIn(500);
}
function mostraracorrect(){
    $(".acorrect").show();
    $("#accioninmediatatxt").html('<a href="#">Acción Inmediata: </a>' + $("#accioninmediata").val());
    $("#accioninmediata").prop("readonly",true);
    $("#accioninmediata").fadeOut(500);
    $("#guardarAI").fadeOut(500);
    $(".linebodyai").fadeOut(500);
}


function mostrarcDocAcorrect(){
    $(".docacorrect").show();
    $("#analisisdecausatxt").html('<a href="#">Acción Inmediata: </a>' + $("#analisisdecausa").val());
    $("#analisisdecausa").prop("readonly",true);
    $("#analisisdecausa").fadeOut(500);
    $("#guardarACausa").fadeOut(500);
    $(".linebodyac").fadeOut(500);
}
/*
function ocultarAC(){
    $(".acausa").hide();
    $("#accioninmediata").prop("readonly",false);
    $("#accioninmediata").fadeIn(500);
    $("#guardarAI").fadeIn(500);
    $(".linebodyai").fadeIn(500);
}*/

var options = { year: 'numeric', month: 'short', day: 'numeric' };
function actdatosai(fecha,accioninmediata){
    $("#fechaai").html(fecha.toLocaleDateString("es-ES", options));
    $("#horaai").html('<i class="fa fa-clock-o"></i> ' + fecha.toLocaleTimeString('en-US'));
    $("#accioninmediata").val(accioninmediata);
    $(".acausa").fadeIn(500);
}

function actdatosac(fecha,analisisdecausa){
    $("#fechaac").html(fecha.toLocaleDateString("es-ES", options));
    $("#horaac").html('<i class="fa fa-clock-o"></i> ' + fecha.toLocaleTimeString('en-US'));
    $("#analisisdecausa").val(analisisdecausa);
    $(".acorrect").fadeIn(500);
}

function actdatosACorr(fecha,accorrec){
    $("#fechaacorr").html(fecha.toLocaleDateString("es-ES", options));
    $("#horaacorr").html('<i class="fa fa-clock-o"></i> ' + fecha.toLocaleTimeString('en-US'));
    $("#accorrec").val(accorrec);
    //$(".acorrect").fadeIn(500);
}

function validarpasos(noconformidad){
    if(noconformidad.accioninmediata==null || noconformidad.accioninmediata==""){
        $("#fechaai").html('.::.  <i class="fa fa-calendar"></i>  .::.');
        $("#horaai").html('<i class="fa fa-clock-o"></i> ');
        actAI();
    }else{
        var fecha = new Date(noconformidad.accioninmediatafec);
        actdatosai(fecha,noconformidad.accioninmediata);
        if(noconformidad.analisisdecausa==null || noconformidad.analisisdecausa==""){
            $("#fechaac").html('.::.  <i class="fa fa-calendar"></i>  .::.');
            $("#horaac").html('<i class="fa fa-clock-o"></i> ');
            actAI();
            actAC();
        }else{
            inactAI();
            var fecha = new Date(noconformidad.analisisdecausafec);
            actdatosac(fecha,noconformidad.analisisdecausa);
            actACorr();
            if(noconformidad.accorrec==null || noconformidad.accorrec==""){
                $("#fechaacorr").html('.::.  <i class="fa fa-calendar"></i>  .::.');
                $("#horaacorr").html('<i class="fa fa-clock-o"></i> ');
                actAC();
            }else{
                inactAC();
                var fecha = new Date(noconformidad.accorrecfec);
                actdatosACorr(fecha,noconformidad.accorrec);
            }
        }                        
    }    
}
