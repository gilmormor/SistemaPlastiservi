$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $( "#nombre" ).focus();
});


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
// Inicializamos el plugin fileinput:
//  traducción al español
//  script para procesar las peticiones de subida
//  desactivar la subida asíncrona
//  máximo de ficheros que se pueden seleccionar	
//  Tamaño máximo en Kb de los ficheros que se pueden seleccionar
//  no mostrar los errores de tipo de archivo (cuando el usuario selecciona un archivo no permitido)
//  tipos de archivos permitidos por su extensión (array definido al principio del script)
  $('#file-es').fileinput({
      language: 'es',
      uploadUrl: '/noconformidad/1',
      uploadAsync: false,
      maxFileCount: 5,
      maxFileSize: 500,
      removeFromPreviewOnError: true,
      allowedFileExtensions : tipos,
      initialPreview: [
        // IMAGE DATA
        "/storage/imagenes/noconformidad/bd.pdf",
        // IMAGE DATA
        "/storage/imagenes/noconformidad/listado prod.pdf",
        // VIDEO DATA
        "/storage/imagenes/noconformidad/menu.jpg",
        // OFFICE WORD DATA
        "/storage/imagenes/noconformidad/vendedor.jpg",
        // OFFICE WORD DATA
        "/storage/imagenes/noconformidad/vendedora.jpg"
    ],
    initialPreviewAsData: true, // identify if you are sending preview data only and not the raw markup
    initialPreviewFileType: 'image', // image is the default and can be overridden in config below
    initialPreviewConfig: [
        {type: "pdf", size: 8000, caption: "bd.pdf", url: "/noconformidad/2", key: "bd.pdf", downloadUrl: false},
         // disable download
        {type: "pdf", size: 8000, caption: "listado prod.pdf", url: "/noconformidad/2", key: "listado prod.pdf", downloadUrl: false} // disable download
    ],
  });
// Evento filecleared del plugin que se ejecuta cuando pulsamos el botón 'Quitar'
//    Vaciamos y ocultamos el div de alerta
  $('#file-es').on('filecleared', function(event) {
    $('div.alert').empty();
    $('div.alert').hide();		
  });
// Evento filebatchuploadsuccess del plugin que se ejecuta cuando se han enviado todos los archivos al servidor
//    Mostramos un resumen del proceso realizado
//    Carpeta donde se han almacenado y total de archivos movidos
//    Nombre y tamaño de cada archivo procesado
//    Totales de archivos por tipo
  $('#file-es').on('filebatchuploadsuccess', function(event, data, previewId, index) {
    var ficheros = data.files;
    var respuesta = data.response;
    var total = data.filescount;
    var mensaje;
    var archivo;
    var total_tipos='';
    alert('entro');
	
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