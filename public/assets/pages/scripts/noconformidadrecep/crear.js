$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    
    $('.datepicker').datepicker({
		language: "es",
		autoclose: true,
		todayHighlight: true
    }).datepicker("setDate");

    id = $("#idhide").val();
    prevImagen(id);
    paso2(id);
/*
    $("#file-ess").fileinput({
        language: 'es',
        uploadUrl: '/noconformidadup/'+$("#idhide").val(), 
        uploadAsync: false,
        minFileCount: 1,
        maxFileCount: 20,
        showUpload: false, 
        showRemove: false,
        initialPreviewAsData: true,
        initialPreviewFileType: 'image'
        }).on("filebatchselected", function(event, files) {
        
            $("#file-ess").fileinput("upload");
        
        });
*/
        //$("#file-ess").fileinput("refresh");
/*
        initialPreview: [
        // IMAGE DATA
        "/storage/imagenes/noconformidad/Sin título.jpg",
        // IMAGE DATA
        "/storage/imagenes/noconformidad/sample-2.jpg",
        // OFFICE WORD DATA
        "/storage/imagenes/noconformidad/pago raul romero.pdf",
        ],
        initialPreviewAsData: true,
        initialPreviewFileType: 'image',
        initialPreviewConfig: [
            {type: "image", caption: "Sin título.jpg", size: 827000, width: "120px", url: "/delImagen_noconformidad/11", key: 1},
            {type: "image", caption: "sample-2.jpg", size: 549000, width: "120px", url: "/delImagen_noconformidad/11", key: 2},
            {type: "pdf", size: 8000, caption: "pago raul romero.pdf", url: "/delImagen_noconformidad/11", key: 3, downloadUrl: false},
        ]
*/


    // Tipos de archivos admitidos por su extensión
    //var tipos = ['docx','xlsx','pptx','pdf','jpg','bmp','png'];
    var tipos = ['pdf','jpg','bmp','png'];
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
    // Inicializamos el plugin fileinput:
    //  traducción al español
    //  script para procesar las peticiones de subida
    //  desactivar la subida asíncrona
    //  máximo de ficheros que se pueden seleccionar	
    //  Tamaño máximo en Kb de los ficheros que se pueden seleccionar
    //  no mostrar los errores de tipo de archivo (cuando el usuario selecciona un archivo no permitido)
    //  tipos de archivos permitidos por su extensión (array definido al principio del script)
    /*
    $('#file-ess11').fileinput({
        language: 'es',
        uploadUrl: '/noconformidadup/' + id,
        uploadAsync: false,
        maxFileCount: 5,
        maxFileSize: 500,
        showUpload: true,
        showRemove: false,
        removeFromPreviewOnError: true,
        allowedFileExtensions : tipos,
        initialPreviewAsData: true, // identify if you are sending preview data only and not the raw markup
        initialPreviewFileType: 'image', // image is the default and can be overridden in config below
    });
    */
    // Evento filecleared del plugin que se ejecuta cuando pulsamos el botón 'Quitar'
    //    Vaciamos y ocultamos el div de alerta
    $('#file-ess11').on('filecleared', function(event) {
        $('div.alert').empty();
        $('div.alert').hide();		
    });
    // Evento filebatchuploadsuccess del plugin que se ejecuta cuando se han enviado todos los archivos al servidor
    //    Mostramos un resumen del proceso realizado
    //    Carpeta donde se han almacenado y total de archivos movidos
    //    Nombre y tamaño de cada archivo procesado
    //    Totales de archivos por tipo
    $('#file-ess11').on('filebatchuploadsuccess', function(event, data, previewId, index) {
        var ficheros = data.files;
        var respuesta = data.response;
        var total = data.filescount;
        var mensaje;
        var archivo;
        var total_tipos='';
        
        reset_contadores(); // Resetamos los contadores de tipo de archivo
        // Comenzamos a crear el mensaje que se mostrará en el DIV de alerta
        mensaje='<p>'+total+ ' ficheros almacenados en la carpeta: '+respuesta.dirupload+'<br><br>';
        mensaje+='Ficheros procesados:</p><ul>';
        // Procesamos la lista de ficheros para crear las líneas con sus nombres y tamaños
        for(var i=0;i<ficheros.length;i++) {
        if(ficheros[i]!=undefined) {
            archivo=ficheros[i];				
            tam=archivo.size / 1024;
            mensaje+='<li>'+archivo.name+' ('+Math.ceil(tam)+'Kb)'+'</li>';
            contadores_tipos(archivo.name);  // Incrementamos el contador para el tipo de archivo subido
        } 
        };
            
        mensaje+='</ul><br/>';
        // Línea que muestra el total de ficheros por tipo que se han subido
        for(var i=0; i<contadores.length; i++)  total_tipos+='('+contadores[i]+') '+tipos[i]+', ';
        // Apaño para eliminar la coma y el espacio (, ) que se queda en el último procesado
        total_tipos=total_tipos.substr(0,total_tipos.length-2);
        mensaje+='<p>'+total_tipos+'</p>';
        // Si el total de archivos indicados por el plugin coincide con el total que hemos recibido en la respuesta del script PHP
        // mostramos mensaje de proceso correcto
        if(respuesta.total==total) mensaje+='<p>Coinciden con el total de archivos procesados en el servidor.</p>';
        else mensaje+='<p>No coinciden los archivos enviados con el total de archivos procesados en el servidor.</p>';
        // Una vez creado todo el mensaje lo cargamos en el DIV de alerta y lo mostramos
        $('div.alert').html(mensaje);
        $('div.alert').show();
    });
    // Ocultamos el div de alerta donde se muestra un resumen del proceso
    $('div.alert').hide();

});

$("#Prueba").click(function(event)
{
    event.preventDefault();
    id = $("#idhide").val();
    prevImagen(id);
});

function prevImagen(id){
    sta_val=$("#funcvalidarai").val();
    var data = {
        id     : id
    };
    var ruta = '/noconformidadprevImg/' + id + '/' + sta_val;
    ajaxRequest(data,ruta,'prevImagen');    
}

function paso2(id){
    
    $(".input-sm").val('');
    $("#titulomodal").html("No Conformidad Id: " + id);
    $("#lbldatos").html("Acción Inmediata")
    var data = {
        id     : id,
        _token : $('input[name=_token]').val()
    };
    var ruta = '/noconformidadrecep/buscar/' + id;
    ajaxRequest(data,ruta,'paso2');
}

function buscarpasos(id,noconformidad){
    if(noconformidad==null){
        var data = {
            id     : id,
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

function cumplimientoSN(aux_val){
    event.preventDefault();
    /*
    if(aux_val==0){
        $("#obsaccioninmediata").val($("#accioninmediata").val());
        $("#obsanalisisdecausa").val($("#analisisdecausa").val());
        $("#obsaccioncorrectiva").val($("#accorrec").val());
        $("#myModalincumplimientonc").modal('show');
    }
    if(aux_val==1){
        id = $("#idhide").val();
        var data = {
            id           : id,
            cumplimiento : aux_val,
            _token       : $('input[name=_token]').val()
        };
        var ruta = '/noconformidadrecep/cumplimiento/' + id;
        funcion = 'cumplimiento';
        ejecutarAjax(data,ruta,funcion);    
    }
    */
    id = $("#idhide").val();
    var data = {
        id           : id,
        cumplimiento : aux_val,
        _token       : $('input[name=_token]').val()
    };
    var ruta = '/noconformidadrecep/cumplimiento/' + id;
    funcion = 'cumplimiento';
    ejecutarAjax(data,ruta,funcion); 
}


$("#btnguardarincumplimiento").click(function(event)
{
    event.preventDefault();
	if(verificar('obsaccioninmediata','texto') && verificar('obsanalisisdecausa','texto') && verificar('obsaccioncorrectiva','texto'))
	{
        //$("#myModalDatos").modal('hide');
        id = $("#idhide").val();
        var data = {
            id               : id,
            cumplimiento     : 0,
            accioninmediata  : $("#obsaccioninmediata").val(),
            analisisdecausa  : $("#obsanalisisdecausa").val(),
            accorrec         : $("#obsaccioncorrectiva").val(),
            _token : $('input[name=_token]').val()
        };
        var ruta = '/noconformidadrecep/incumplimiento/' + id;
        funcion = 'incumplimiento';
        ejecutarAjax(data,ruta,funcion);
	}else{
		alertify.error("Falta incluir informacion");
	}
});


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

$("#guardarfechaguardado").click(function(event)
{
    event.preventDefault();
    if(verificar('fechaguardado','texto'))
	{
        id = $("#idhide").val();
        var data = {
            id            : id,
            fechaguardado : $("#fechaguardado").val(),
            _token : $('input[name=_token]').val()
        };
        var ruta = '/noconformidadrecep/actfechaguardado/' + id;
        funcion = 'guardarfechaguardado';
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
                ocultarcumplimiento();

                inactAI('');
                inactvalAI();
                inactACausa();
                inactACorr();
                inactfechacompromiso();
                inactcumplimiento()

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
                
                buscarpasos(data['id']);

                $("#myModalDatos").modal('show');
                //$("#paso2time").show();

                $('#paso2time').css('display','block');
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
                    buscarpasos(data['id']);
				}
            }
            if(funcion=='apre'){
				if (respuesta.mensaje == "ok") {
                    buscarpasos(data['id']);
				}
            }
            if(funcion=='cumplimiento' || funcion=='incumplimiento'){
				if (respuesta.mensaje == "ok") {
                    buscarpasos(data['id']);
                    $("#myModalincumplimientonc").modal('hide');
				}
            }
            if(funcion=='guardarACausa'){
				if (respuesta.mensaje == "ok") {
                    buscarpasos(data['id']);
				}
            }
            if(funcion=='guardarACorr'){
				if (respuesta.mensaje == "ok") {
                    buscarpasos(data['id']);
				}
            }
            if(funcion=='guardarfechacompromiso'){
				if (respuesta.mensaje == "ok") {
                    buscarpasos(data['id']);
				}
            }
            if(funcion=='guardarfechaguardado'){
				if (respuesta.mensaje == "ok") {
                    buscarpasos(data['id']);
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
            if(funcion=='prevImagen'){
                //alert(respuesta);
                sta_val = $("#funcvalidarai").val();
                if(respuesta.i>0){
                    $("#file-ess").fileinput({
                        language: 'es',
                        uploadUrl: '/noconformidadup/'+$("#idhide").val()+'/'+sta_val,
                        uploadAsync: false,
                        minFileCount: 1,
                        maxFileCount: 5,
                        maxFileSize: 500,
                        showUpload: false, 
                        showRemove: false,
                        allowedFileExtensions: ["pdf","jpg","bmp","png"],
                        overwriteInitial: respuesta.overwriteInitial,
                        initialPreview: respuesta.initialPreview,
                        initialPreviewConfig: respuesta.initialPreviewConfig,    
                        initialPreviewAsData: true,
                        initialPreviewFileType: 'image'
                        }).on("filebatchselected", function(event, files) {
                            $("#file-ess").fileinput("upload");
                        });
                }else{
                    $("#file-ess").fileinput({
                        language: 'es',
                        uploadUrl: '/noconformidadup/'+$("#idhide").val()+'/'+sta_val,
                        uploadAsync: false,
                        minFileCount: 1,
                        maxFileCount: 5,
                        maxFileSize: 500,
                        showUpload: false, 
                        showRemove: false,
                        allowedFileExtensions: ["pdf","jpg","bmp","png"],
                        initialPreviewAsData: true,
                        initialPreviewFileType: 'image'
                        }).on("filebatchselected", function(event, files) {
                        
                            $("#file-ess").fileinput("upload");
                        });
                }
                if($("#funcvalidarai").val()=='1'){
                    inactivarsubirarchivos();                    
                }
            }

            if (respuesta.mensaje == "ok") {
                Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
            } else {
                if (respuesta.mensaje == "sp"){
                    Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
                }else{
                    if(respuesta.mensaje=="img"){

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

function actAI(){
    $("#accioninmediatatxt").html('<a href="#">Acción Inmediata </a>');
    $("#accioninmediata").prop("readonly",false);
    $("#accioninmediata").fadeIn(500);
    $("#guardarAI").fadeIn(500);
    $("#linebodyai1").fadeIn(500);
    $("#linebodyai2").fadeIn(500);
    if($("#funcvalidarai").val()!='0'){
        inactAI('');
    }
}


function inactAI(aux_ac){
    //SI AANALISIS DE CAUDA ESTA EN BLANCO ACTIVE EL ENLACE A VALIDAR NO CONFORMIDAD, SIEMPRE y CUANDO LA CONSULRA VENGA DE NoConformidadValidarController
    if(aux_ac==null || aux_ac==""){
        $("#accioninmediatatxt").html('<a href="#"  ' + '>Acción Inmediata: </a>' + $("#accioninmediata").val());
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
    if($("#funcvalidarai").val()!='0'){
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

function actACausa(){
    //alert($("#funcvalidarai").val());
    if($("#funcvalidarai").val()=='0'){
        $("#analisisdecausatxt").html('<a href="#">Analisis de causa</a>');
        $("#analisisdecausa").prop("readonly",false);
        $("#analisisdecausa").fadeIn(500);
        $("#guardarAC").fadeIn(500);
        $("#linebodyac1").fadeIn(500);
        $("#linebodyac2").fadeIn(500);    
    }else{
        inactACausa();
    }
}
function inactACausa(){
    $("#analisisdecausatxt").html('<a href="#">Analisis de causa: </a>' + $("#analisisdecausa").val());
    $("#analisisdecausa").prop("readonly",true);
    $("#analisisdecausa").fadeOut(500);
    $("#guardarAC").fadeOut(500);
    $("#linebodyac1").fadeOut(500);
    $("#linebodyac2").fadeOut(500);
}


function actACorr(){
    if($("#funcvalidarai").val()=='0'){
        $("#accorrectxt").html('<a href="#">Acción correctiva</a>');
        $("#accorrec").prop("readonly",false);
        $("#accorrec").fadeIn(500);
        $("#guardarACorr").fadeIn(500);
        $("#linebodyacorr1").fadeIn(500);
        $("#linebodyacorr2").fadeIn(500);
        activarsubirarchivos();
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
/*
    $("#file-ess").prop("readonly",true);
    $("#file-ess").prop('disabled',true);
*/
    inactivarsubirarchivos();
}

function actfechacompromiso(){
    if($("#funcvalidarai").val()=='0'){
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

function actfechaguardado(){
    if($("#funcvalidarai").val()=='0'){
        $("#fechaguardadotxt").html('<a href="#">Fecha de Guardado</a>');
        //$("#fechacompromiso").prop("readonly",false);
        var f = new Date();
        $("#fechaguardado").val(fechaddmmaaaa(f));
        $("#fechaguardado").fadeIn(500);
        $("#guardarfechaguardado").fadeIn(500);
        $("#linebodyfechaguardado1").fadeIn(500);
        $("#linebodyfechaguardado2").fadeIn(500);    
    }else{
        inactfechaguardado();
    }
}
function inactfechaguardado(){
    $("#fechaguardadotxt").html('<a href="#">Fecha Guardado: </a>' + $("#fechaguardado").val());
    $("#fechaguardado").prop("readonly",true);
    $("#fechaguardado").fadeOut(500);
    $("#guardarfechaguardado").fadeOut(500);
    $("#linebodyfechaguardado1").fadeOut(500);
    $("#linebodyfechaguardado2").fadeOut(500);
}

function actcumplimiento(){
    if($("#funcvalidarai").val()=='2'){
        $("#cumplimientotxt").html('<a href="#">Validar cumplimiento</a>');
        /*$("#cumplimiento").prop("readonly",false);
        $("#cumplimiento").fadeIn(500);*/
        $("#guardarcumplimiento").fadeIn(500);
        $("#linebodycumplimiento1").fadeIn(500);
        $("#linebodycumplimiento2").fadeIn(500);
    }else{
        inactcumplimiento();
    }
}

function inactcumplimiento(){
    $("#cumplimientotxt").html('<a href="#">Validar cumplimiento: </a>' + $("#cumplimiento").val());
    $("#cumplimiento").prop("readonly",true);
    $("#cumplimiento").fadeOut(500);
    $("#guardarcumplimiento").fadeOut(500);
    $("#linebodycumplimiento1").fadeOut(500);
    $("#linebodycumplimiento2").fadeOut(500);    
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
}

function ocultarcumplimiento(){
    $(".cumplimiento").hide();
}


function ocultarfechacompromiso(){
    $(".fechacompromiso").hide();
}
function ocultarfechaguardado(){
    $(".fechaguardado").hide();
}

function ocultarcumplimiento(){
    $(".cumplimiento").hide();
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


function actdatosacausa(fecha,analisisdecausa){
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
    $(".fechaguardado").fadeIn(500);
}

function actdatosfechaguardado(fecha,fechaguardado){
    $("#fechafechaguardado").html(fecha.toLocaleDateString("es-ES", options));
    $("#horafechaguardado").html('<i class="fa fa-clock-o"></i> ' + fecha.toLocaleTimeString('en-US'));
    $("#fechaguardado").val(fechaddmmaaaa(fecha));
    $(".cumplimiento").fadeIn(500);
    //alert($("#fechaguardado").val());
}

function actdatoscumplimiento(fecha,cumplimiento){
    $("#fechacumplimiento").html(fecha.toLocaleDateString("es-ES", options));
    $("#horacumplimiento").html('<i class="fa fa-clock-o"></i> ' + fecha.toLocaleTimeString('en-US'));
    if(cumplimiento=="1")
        $("#cumplimiento").val('Si');
    if(cumplimiento=="0")
        $("#cumplimiento").val('No');
    //Cuando es = -6 es porque el dueño de la NC marco como incumplimiento de la misma
    if(cumplimiento == -6){
        $("#cumplimiento").val('No: Revalidación');
        $("#cumplimientotxt").html('<a href="#">Validar cumplimiento: </a>' + $("#cumplimiento").val());
    } 
        

    //alert($("#obsvalai").val());
    //$(".acausa").fadeIn(500);
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
    //alert($("#funcvalidarai").val());
    inactAI('');
    inactvalAI();
    inactACausa();
    inactACorr();
    inactfechacompromiso()
    inactcumplimiento();

    noconformidad=respuesta.noconformidad;
    if(noconformidad.accioninmediata==null || noconformidad.accioninmediata==""){
        $("#fechaai").html('.::.  <i class="fa fa-calendar"></i>  .::.');
        $("#horaai").html('<i class="fa fa-clock-o"></i> ');
        actAI();
        inactvalAI();
    }else{
        var fecha = new Date(noconformidad.accioninmediatafec);
        actdatosai(fecha,noconformidad.accioninmediata);
        if(noconformidad.cumplimiento===0){// Si es === 0 entonces hay incumplimiento 
            actAI();
            ocultarobsvalai();
            ocultarACausa();
        }else{
            if(noconformidad.obsvalai==null || noconformidad.obsvalai==""){
                $("#fechavalai").html('.::.  <i class="fa fa-calendar"></i>  .::.');
                $("#horavalai").html('<i class="fa fa-clock-o"></i> ');
                actAI();
                
                $(".acausa").hide();
                actvalAI();
                //ocultarobsvalai();
                
                if($("#funcvalidarai").val()=='1'){
                    actvalAI();
                }else{
                    inactvalAI();
                    $("#obsvalaitxt").html('<a href="#">Validación No conformidad: </a>Esperando Validación de supervisor.');                
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
                    if(noconformidad.cumplimiento===-1){
                        ocultarACausa();
                        actdatosvalai(fecha,noconformidad.obsvalai);
                        actvalAI();
                    }else{
                        if(noconformidad.analisisdecausa==null || noconformidad.analisisdecausa==""){
                            $("#fechaac").html('.::.  <i class="fa fa-calendar"></i>  .::.');
                            $("#horaac").html('<i class="fa fa-clock-o"></i> ');
                            //actAI();
                            actACausa();
                        }else{
                            inactAI(noconformidad.analisisdecausa);
                            var fecha = new Date(noconformidad.analisisdecausafec);
                            actdatosacausa(fecha,noconformidad.analisisdecausa);
                            if(noconformidad.cumplimiento===-2){
                                inactACorr();
                                ocultaracorrect();
                                actACausa();
                            }else{
                                if(noconformidad.accorrec==null || noconformidad.accorrec==""){
                                    $("#fechaacorr").html('.::.  <i class="fa fa-calendar"></i>  .::.');
                                    $("#horaacorr").html('<i class="fa fa-clock-o"></i> ');
                                    actACausa();
                                    actACorr();
                                }else{
                                    inactACausa();
                                    var fecha = new Date(noconformidad.accorrecfec);
                                    actdatosACorr(fecha,noconformidad.accorrec);
                                    //actfechacompromiso();
                                    if(noconformidad.cumplimiento===-3){
                                        inactfechacompromiso();
                                        ocultarfechacompromiso();
                                        actACorr();
                                    }else{
                                        if(noconformidad.fechacompromiso==null || noconformidad.fechacompromiso==""){
                                            $("#fechafechacompromiso").html('.::.  <i class="fa fa-calendar"></i>  .::.');
                                            $("#horafechacompromiso").html('<i class="fa fa-clock-o"></i> ');
                                            actACorr();
                                            actfechacompromiso();
                                        }else{
                                            inactACorr();
                                            var fecha = new Date(noconformidad.fechacompromisofec);
                                            actdatosfechacompromiso(fecha,respuesta.feccomp);
                                            actfechacompromiso();
                                            if(noconformidad.cumplimiento===-4){
                                                inactfechaguardado();
                                                ocultarfechaguardado();
                                                actfechacompromiso();
                                            }else{
                                                if(noconformidad.fechaguardado==null || noconformidad.fechaguardado==""){
                                                    $("#fechafechaguardado").html('.::.  <i class="fa fa-calendar"></i>  .::.');
                                                    $("#horafechaguardado").html('<i class="fa fa-clock-o"></i> ');
                                                    //actfechacompromiso();
                                                    actfechaguardado();
                                                }else{
                                                    inactfechacompromiso();
                                                    var fecha = new Date(noconformidad.fechaguardado);
                                                    actdatosfechaguardado(fecha,noconformidad.fechaguardado);
                                                    //actfechaguardado();
                                                    inactfechaguardado();
                                                    if(noconformidad.cumplimiento===-5){
                                                        inactcumplimiento();
                                                        ocultarcumplimiento();
                                                        actfechaguardado();
                                                    }else{
                                                        if(noconformidad.cumplimiento==null){
                                                            //alert('prueba');
                                                            $("#fechacumplimiento").html('.::.  <i class="fa fa-calendar"></i>  .::.');
                                                            $("#horacumplimiento").html('<i class="fa fa-clock-o"></i> ');
                                                            //actcumplimiento();
                                                            if($("#funcvalidarai").val()=='2'){
                                                                actcumplimiento();
                                                            }else{
                                                                inactcumplimiento();
                                                                $("#cumplimientotxt").html('<a href="#">Validar Cumplimiento: </a>Esperando Validación del Dueño NC.');                
                                                            }                        
                                                        }else{
                                                            var fecha = new Date(noconformidad.fechacumplimiento);
                                                            actdatoscumplimiento(fecha,noconformidad.cumplimiento);
                                                            actcumplimiento();
                                                            inactcumplimiento();
                                                            if(noconformidad.cumplimiento===-6){
                                                                actcumplimiento();
                                                                actdatoscumplimiento(fecha,noconformidad.cumplimiento);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
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


function activarsubirarchivos(){
    $(".kv-file-remove").show();
    $(".input-group").show();
    $(".fileinput-remove").show();
    $(".form-text").show();
}
function inactivarsubirarchivos(){
    $(".kv-file-remove").hide();
    $(".input-group").hide();
    $(".fileinput-remove").hide();
    $(".form-text").hide();
}

function fechaddmmaaaa(f){
    dia = f.getDate();
    d = dia.toString();
    d = d.padStart(2, 0);
    mes = f.getMonth();
    m = mes.toString();
    m = m.padStart(2, 0);
    fecha = d + "/" + m + "/" + f.getFullYear();
    return fecha; 
}
