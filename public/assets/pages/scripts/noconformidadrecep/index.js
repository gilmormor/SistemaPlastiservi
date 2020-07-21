$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $('.datepicker').datepicker({
		language: "es",
		autoclose: true,
		todayHighlight: true
    }).datepicker("setDate");
});

function paso2(id,i){
    
    $(".input-sm").val('');
    $("#titulomodal").html("No Conformidad Id: " + id);
    $("#lbldatos").html("Acción Inmediata")
    var data = {
        id     : id,
        i      : i,
        _token : $('input[name=_token]').val()
    };
    var ruta = '/noconformidadrecep/buscar/' + id;
    ajaxRequest(data,ruta,'paso2');
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
	if(verificar('accioninmediata','texto'))
	{
        id = $("#idhide").val();
        var data = {
            id            : id,
            accioninmediata : $("#accioninmediata").val(),
            _token : $('input[name=_token]').val()
        };
        var ruta = '/noconformidadrecep/actai/' + id;
        funcion = 'guardarAI';
        ejecutarAjax(data,ruta,funcion);
	}else{
		alertify.error("Falta incluir informacion");
	}
	
});

function apre(aux_val){
    event.preventDefault();
	if(verificar('obsvalai','texto'))
	{
        id = $("#idhide").val();
        var data = {
            id       : id,
            obsvalai : $("#obsvalai").val(),
            stavalai : aux_val,
            _token   : $('input[name=_token]').val()
        };
        var ruta = '/noconformidadrecep/actobsvalai/' + id;
        funcion = 'apre';
        ejecutarAjax(data,ruta,funcion);
	}else{
		alertify.error("Falta incluir informacion");
	}

}


$("#guardarACausa").click(function(event)
{
    event.preventDefault();
	if(verificar('analisisdecausa','texto'))
	{
        //$("#myModalDatos").modal('hide');
        id = $("#idhide").val();
        var data = {
            id            : id,
            analisisdecausa : $("#analisisdecausa").val(),
            _token : $('input[name=_token]').val()
        };
        var ruta = '/noconformidadrecep/actacausa/' + id;
        funcion = 'guardarACausa';
        ejecutarAjax(data,ruta,funcion);
	}else{
		alertify.error("Falta incluir informacion");
	}
	
});

$("#guardarACorr").click(function(event)
{
    event.preventDefault();
	if(verificar('accorrec','texto'))
	{
        id = $("#idhide").val();
        var data = {
            id            : id,
            accorrec : $("#accorrec").val(),
            _token : $('input[name=_token]').val()
        };
        var ruta = '/noconformidadrecep/actacorr/' + id;
        funcion = 'guardarACorr';
        ejecutarAjax(data,ruta,funcion);
	}else{
		alertify.error("Falta incluir informacion");
	}
	
});


$("#guardarfechacompromiso").click(function(event)
{
    event.preventDefault();
    if(verificar('fechacompromiso','texto'))
	{
        id = $("#idhide").val();
        var data = {
            id            : id,
            fechacompromiso : $("#fechacompromiso").val(),
            _token : $('input[name=_token]').val()
        };
        var ruta = '/noconformidadrecep/actfeccomp/' + id;
        funcion = 'guardarfechacompromiso';
        ejecutarAjax(data,ruta,funcion);
	}else{
		alertify.error("Falta incluir informacion");
	}
	
});

function ejecutarAjax(data,ruta,funcion){
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
            ajaxRequest(data,ruta,funcion);
        }
    });
}

$(".requeridos").keyup(function(){
	//alert($(this).parent().attr('class'));
	validacion($(this).prop('name'),$(this).attr('tipoval'));
});
function verificar(nomcampo,tipo)
{
	var v1=0;
	
	v1=validacion(nomcampo,tipo);
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
            if(funcion=='paso2'){
                $("#ihide").val(data['i']);
                $("#idhide").val(data['id']);
                //$("#motivonc_id").val(respuesta.motivonc);
                var fecha = new Date(respuesta.noconformidad.fechahora);
                var options = { year: 'numeric', month: 'short', day: 'numeric' };

                $("#accioninmediata").val('');
                $("#analisisdecausa").val('');
                $("#accorrec").val('');

                ocultarobsvalai()
                ocultarACausa();
                ocultaracorrect();
                ocultarfechacompromiso();

                inactAI('');
                inactvalAI();
                inactAC();
                inactACorr();
                inactfechacompromiso();

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
                
                buscarpasos(data['id'],data['i'],respuesta);

                $("#myModalDatos").modal('show');
                //$(".selectpicker").selectpicker('refresh');
                validacion('accioninmediata','');
                return 0;
            }
            if(funcion=='buscarpasos'){
                validarpasos(respuesta);
                return 0;
            }
            if(funcion=='guardarAI'){
				if (respuesta.mensaje == "ok") {
                    i = $("#ihide").val();
                    $('#accioninmediata' + i).attr("class","btn btn-warning btn-sm tooltipsC");
                    $('#iconoai' + i).attr("class","glyphicon glyphicon-ok");
                    buscarpasos(data['id'],data['i']);
				}
            }
            if(funcion=='apre'){
				if (respuesta.mensaje == "ok") {
                    i = $("#ihide").val();
                    buscarpasos(data['id'],data['i']);
				}
            }

            if(funcion=='guardarACausa'){
				if (respuesta.mensaje == "ok") {
                    buscarpasos(data['id'],data['i']);
				}
            }
            if(funcion=='guardarACorr'){
				if (respuesta.mensaje == "ok") {
                    buscarpasos(data['id'],data['i']);
				}
            }
            if(funcion=='guardarfechacompromiso'){
				if (respuesta.mensaje == "ok") {
                    buscarpasos(data['id'],data['i']);
				}
            }
            if(funcion=='btnaprobarAI'){
				if (respuesta.mensaje == "ok") {
                    $("#myModalValidarai").modal('hide');
                    $("#myModalDatos").modal('hide');
                    //buscarpasos(data['id'],data['i']);
				}
            }
            if(funcion=='buscarAI'){
				if (respuesta.mensaje == "ok") {
                    $("#funcvalidarai").val('class="tooltipsC" title="Validar Accion Inmediata No conformidad" onclick="validarai()"');
                    $("#obsvalai").val(respuesta.noconformidad.obsvalai);
                    //$("#obsvalai").val('');
                    verificar('obsvalai','');
                    //$("#myModalDatos").modal('hide');
                    $("#myModalValidarai").modal('show');
                    //$( "#dialog" ).dialog();  
                    return 0;              
				}
            }

            if (respuesta.mensaje == "ok") {
                Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
            } else {
                if (respuesta.mensaje == "sp"){
                    Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
                }else{
                    Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
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

function actAI(){
    $("#accioninmediatatxt").html('<a href="#">Acción Inmediata </a>');
    $("#accioninmediata").prop("readonly",false);
    $("#accioninmediata").fadeIn(500);
    $("#guardarAI").fadeIn(500);
    $("#linebodyai1").fadeIn(500);
    $("#linebodyai2").fadeIn(500);
    if($("#funcvalidarai").val()!=''){
        inactAI('');
    }
}


function inactAI(aux_ac){
    //SI AANALISIS DE CAUDA ESTA EN BLANCO ACTIVE EL ENLACE A VALIDAR NO CONFORMIDAD, SIEMPRE y CUANDO LA CONSULRA VENGA DE NoConformidadValidarController
    if(aux_ac==null || aux_ac==""){
        $("#accioninmediatatxt").html('<a href="#"  ' + $("#funcvalidarai").val() + '>Acción Inmediata: </a>' + $("#accioninmediata").val());
    }else{
        $("#accioninmediatatxt").html('<a href="#">Acción Inmediata: </a>' + $("#accioninmediata").val());
    }
    $("#accioninmediata").prop("readonly",true);
    $("#accioninmediata").fadeOut(500);
    $("#guardarAI").fadeOut(500);
    $("#linebodyai1").fadeOut(500);
    $("#linebodyai2").fadeOut(500);
    $("#circuloedidAI").attr('class', 'fa fa-edit bg-blue')
    //alert('respuesta.noconformidad.accioninmediata');
}

function actvalAI(){
    if($("#funcvalidarai").val()!=""){
        $("#obsvalaitxt").html('<a href="#">Validación No conformidad </a>');
        $("#obsvalai").prop("readonly",false);
        $("#obsvalai").fadeIn(500);
        $("#guardarvalAI").fadeIn(500);
        $("#linebodyvalai1").fadeIn(500);
        $("#linebodyvalai2").fadeIn(500);
    
    }else{
        inactvalAI();
    }
}

function inactvalAI(){
    $("#obsvalaitxt").html('<a href="#">Validación No conformidad: </a>' + $("#obsvalai").val());
    $("#obsvalai").prop("readonly",true);
    $("#obsvalai").fadeOut(500);
    $("#guardarvalAI").fadeOut(500);
    $("#linebodyvalai1").fadeOut(500);
    $("#linebodyvalai2").fadeOut(500);    
}

function actAC(){
    if($("#funcvalidarai").val()==''){
        $("#analisisdecausatxt").html('<a href="#">Analisis de causa</a>');
        $("#analisisdecausa").prop("readonly",false);
        $("#analisisdecausa").fadeIn(500);
        $("#guardarAC").fadeIn(500);
        $("#linebodyac1").fadeIn(500);
        $("#linebodyac2").fadeIn(500);    
    }else{
        inactAC();
    }
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
    if($("#funcvalidarai").val()==''){
        $("#accorrectxt").html('<a href="#">Acción correctiva</a>');
        $("#accorrec").prop("readonly",false);
        $("#accorrec").fadeIn(500);
        $("#guardarACorr").fadeIn(500);
        $("#linebodyacorr1").fadeIn(500);
        $("#linebodyacorr2").fadeIn(500);
    }else{
        inactACorr();
    }
}
function inactACorr(){
    $("#accorrectxt").html('<a href="#">Acción correctiva: </a>' + $("#accorrec").val());
    $("#accorrec").prop("readonly",true);
    $("#accorrec").fadeOut(500);
    $("#guardarACorr").fadeOut(500);
    $("#linebodyacorr1").fadeOut(500);
    $("#linebodyacorr2").fadeOut(500);    
}

function actfechacompromiso(){
    if($("#funcvalidarai").val()==''){
        $("#fechacompromisotxt").html('<a href="#">Fecha de compromiso</a>');
        //$("#fechacompromiso").prop("readonly",false);
        $("#fechacompromiso").fadeIn(500);
        $("#guardarfechacompromiso").fadeIn(500);
        $("#linebodyfeccomp1").fadeIn(500);
        $("#linebodyfeccomp2").fadeIn(500);    
    }else{
        inactfechacompromiso();
    }
}
function inactfechacompromiso(){
    $("#fechacompromisotxt").html('<a href="#">Fecha de compromiso: </a>' + $("#fechacompromiso").val());
    $("#fechacompromiso").prop("readonly",true);
    $("#fechacompromiso").fadeOut(500);
    $("#guardarfechacompromiso").fadeOut(500);
    $("#linebodyfeccomp1").fadeOut(500);
    $("#linebodyfeccomp2").fadeOut(500);
    //$(".fechacompromiso").fadeOut(500);
    //$("#fechacompromiso").val('');
}


function ocultaracorrect(){
    $(".acorrect").hide();
    $("#accioninmediata").prop("readonly",false);
    $("#accioninmediata").fadeIn(500);
    $("#guardarAI").fadeIn(500);
    $(".linebodyai").fadeIn(500);
}

function ocultarobsvalai(){
    $(".obsvalai").hide();
    /*
    $("#accioninmediata").prop("readonly",false);
    $("#accioninmediata").fadeIn(500);
    $("#guardarAI").fadeIn(500);
    $(".linebodyai").fadeIn(500);*/
}


function ocultarfechacompromiso(){
    $(".fechacompromiso").hide();
}

var options = { year: 'numeric', month: 'short', day: 'numeric', literal: '/' };
function actdatosai(fecha,accioninmediata){
    $("#fechaai").html(fecha.toLocaleDateString("es-ES", options));
    $("#horaai").html('<i class="fa fa-clock-o"></i> ' + fecha.toLocaleTimeString('en-US'));
    $("#accioninmediata").val(accioninmediata);
    $(".obsvalai").fadeIn(500);
    $(".acausa").fadeIn(500);
}

function actdatosvalai(fecha,obsvalai){
    $("#fechavalai").html(fecha.toLocaleDateString("es-ES", options));
    $("#horavalai").html('<i class="fa fa-clock-o"></i> ' + fecha.toLocaleTimeString('en-US'));
    $("#obsvalai").val(obsvalai);
    //alert($("#obsvalai").val());
    //$(".acausa").fadeIn(500);
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
    $(".fechacompromiso").fadeIn(500);
}

function actdatosfechacompromiso(fecha,fechacompromiso){
    $("#fechafechacompromiso").html(fecha.toLocaleDateString("es-ES", options));
    $("#horafechacompromiso").html('<i class="fa fa-clock-o"></i> ' + fecha.toLocaleTimeString('en-US'));
    $("#fechacompromiso").val(fechacompromiso);
}

function banquearcampos(){
    $("#fechaai").html('');
    $("#horaai").html('<i class="fa fa-clock-o"></i> ');
    $("#accioninmediata").val('');

    $("#fechavalai").html('');
    $("#horavalai").html('<i class="fa fa-clock-o"></i> ');
    $("#obsvalai").val('');

    $("#fechaac").html('');
    $("#horaac").html('<i class="fa fa-clock-o"></i> ');
    $("#analisisdecausa").val('');

    $("#fechaacorr").html('');
    $("#horaacorr").html('<i class="fa fa-clock-o"></i> ');
    $("#accorrec").val('');

    $("#fechafechacompromiso").html('');
    $("#horafechacompromiso").html('<i class="fa fa-clock-o"></i> ');
    $("#fechacompromiso").val('');

}

function validarpasos(respuesta){
    inactAI('');
    inactvalAI();
    inactAC();
    inactACorr();
    inactfechacompromiso()

    noconformidad=respuesta.noconformidad;
    if(noconformidad.accioninmediata==null || noconformidad.accioninmediata==""){
        $("#fechaai").html('.::.  <i class="fa fa-calendar"></i>  .::.');
        $("#horaai").html('<i class="fa fa-clock-o"></i> ');
        actAI();
        inactvalAI();
    }else{
        var fecha = new Date(noconformidad.accioninmediatafec);
        actdatosai(fecha,noconformidad.accioninmediata);
        if(noconformidad.obsvalai==null || noconformidad.obsvalai==""){
            $("#fechavalai").html('.::.  <i class="fa fa-calendar"></i>  .::.');
            $("#horavalai").html('<i class="fa fa-clock-o"></i> ');
            actAI();
            
            $(".acausa").hide();
            actvalAI();
            //ocultarobsvalai();
            //alert('entro');
            
            if($("#funcvalidarai").val()==''){
                inactvalAI();
                $("#obsvalaitxt").html('<a href="#">Validación No conformidad: </a>Esperando Validación de supervisor.');
            }else{
                actvalAI();
            }
        }else{
            inactAI(noconformidad.analisisdecausa);
            var fecha = new Date(noconformidad.fechavalai);
            //alert(noconformidad.obsvalai);
            actdatosvalai(fecha,noconformidad.obsvalai);
            inactvalAI();
            if(noconformidad.stavalai=='0'){
                ocultarACausa();
            }else{
                if(noconformidad.analisisdecausa==null || noconformidad.analisisdecausa==""){
                    $("#fechaac").html('.::.  <i class="fa fa-calendar"></i>  .::.');
                    $("#horaac").html('<i class="fa fa-clock-o"></i> ');
                    //actAI();
                    actAC();
                }else{
                    inactAI(noconformidad.analisisdecausa);
                    var fecha = new Date(noconformidad.analisisdecausafec);
                    actdatosac(fecha,noconformidad.analisisdecausa);
                    if(noconformidad.accorrec==null || noconformidad.accorrec==""){
                        $("#fechaacorr").html('.::.  <i class="fa fa-calendar"></i>  .::.');
                        $("#horaacorr").html('<i class="fa fa-clock-o"></i> ');
                        actAC();
                        actACorr();
                    }else{
                        inactAC();
                        var fecha = new Date(noconformidad.accorrecfec);
                        actdatosACorr(fecha,noconformidad.accorrec);
                        //actfechacompromiso();
                        if(noconformidad.fechacompromiso==null || noconformidad.fechacompromiso==""){
                            $("#fechafechacompromiso").html('.::.  <i class="fa fa-calendar"></i>  .::.');
                            $("#horafechacompromiso").html('<i class="fa fa-clock-o"></i> ');
                            actACorr();
                            actfechacompromiso();
                        }else{
                            //inactACorr();
                            var fecha = new Date(noconformidad.fechacompromisofec);
                            actdatosfechacompromiso(fecha,respuesta.feccomp);
                            actfechacompromiso();
                        }
                    }
                }
            }

        }
    }    
}

function validarai(){
    event.preventDefault();
	verificar('obsvalai','');
    id = $("#idhide").val();
    $("#funcvalidarai").val('');
    var data = {
        id     : $("#idhide").val(),
        i      : $("#ihide").val(),
        _token : $('input[name=_token]').val()
    };
    var ruta = '/noconformidadrecep/buscar/' + id;
    funcion = 'buscarAI';
    ajaxRequest(data,ruta,funcion);
}

function guardarvalai(aux_status){
    event.preventDefault();
	if(verificar('obsvalai','texto'))
	{
        id = $("#idhide").val();
        var data = {
            id       : $("#idhide").val(),
            stavalai : aux_status,
            obsvalai : $("#obsvalai").val(),
            _token : $('input[name=_token]').val()
        };
        var ruta = '/noconformidadrecep/actvalai/' + id;
        funcion = 'btnaprobarAI';
        ejecutarAjax(data,ruta,funcion);
	}else{
		alertify.error("Falta incluir informacion");
	}
}


// Tipos de archivos admitidos por su extensión
var tipos = ['docx','xlsx','pptx','pdf','jpg','bmp','png'];
// Contadores de archivos subidos por tipo
  var contadores=[0,0,0,0];
// Reinicia los contadores de tipos subidos
  var reset_contadores = function() {
    for(var i=0; i<tipos.length;i++) {
       contadores[i]=0;
    }
  };
// Incrementa el contador de tipo según la extensión del archivo subido	
  var contadores_tipos = function(archivo) {
    for(var i=0; i<tipos.length;i++) {
      if(archivo.indexOf(tipos[i])!=-1) {
        contadores[i]+=1;
        break;	
      }
    }
  };
  $('div.alert').hide();