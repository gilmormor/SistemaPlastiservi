$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    $('.datepicker').datepicker({
		language: "es",
        autoclose: true,
        clearBtn : true,
		todayHighlight: true
    }).datepicker("setDate");

    // Inputs de filtro: solo enteros, sin decimales ni negativos
    $(".numerico-entero").numeric({ decimal: false, negative: false });
    // Inputs generales con decimales (los del DataTable los maneja claseformatonumerico en draw)
    $(".numerico").numeric();
    // No se inicializa la tabla en document.ready porque causaba un salto visual:
    // el <thead> se renderizaba con la fuente del browser y recién en el evento draw
    // se aplicaba font-size: 12px. La tabla se inicializa únicamente al presionar "Consultar".
});

function claseformatonumerico(clase,numdecimales){
    $(clase).numeric({ negative: false, decimalPlaces: numdecimales });
    $(clase).blur(function(e){
            /* console.log("Blur val(): " + $(this).val());
            console.log("Blur valor: " + $(this).attr('valor'));
            console.log("Blur MASKLA(): " + MASKLA($(this).val(),numdecimales)); */
            //$(this).attr('valor',$(this).val());
            //$(this).val(MASK(0, $(this).val(), '-###,###,###,##0.00',1));
            //$(this).val(MASKLA($(this).val(),numdecimales));
            $(this).val(MASKLA($(this).attr('valor'),numdecimales));
    });
    $(clase).focus(function(e){
        if($(this).attr('valor') != undefined){
            $(this).val($(this).attr('valor'));
        }
    });

    // Evento keydown para .numerico
    $(clase).on("keyup", function(event) {
        // Si el usuario presiona la tecla de coma ","
        if (event.key === ",") {
            // Prevenir la acción solo si aún no hay un punto en el input
            if (!$(this).val().includes(".")) {
                event.preventDefault();
                $(this).val($(this).val() + ".");
            } else {
                event.preventDefault(); // Evita que se escriba la coma si ya hay un punto
            }
        }
        aux_val = $(this).val();
        if($(this).val() == ""){
            aux_val = 0;
        }
        if($(this).val() == 0){
            aux_val = 0;
        }
        $(this).attr('valor',aux_val);
    });


}

function ajaxRequest(data,url,funcion) {
    datatemp = data;
    /*
    if (funcion=='eliminar') {
        console.log(data);
        console.log(url);
        return 0;
    }
    */
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			
			if(funcion=='procesar'){
				if (respuesta.mensaje == "ok") {
                    //genpdfFAC(respuesta.id,"_U");
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
            if(funcion=='consultaranularguiafact'){
				if (respuesta.mensaje == "ok") {
					//alert(respuesta.despachoord.guiadespacho);
					$("#guiadespachoanul").val(respuesta.despachoord.guiadespacho);
					//$(".requeridos").keyup();
					quitarvalidacioneach();
                    $("#guiadesp_id").val(datatemp.guiadesp_id);
                    $("#updated_at").val(datatemp.updated_at);
                    $("#statusM").val('2');
                    $(".selectpicker").selectpicker('refresh');
					$("#myModalanularguiafact").modal('show');
				} else {
					Biblioteca.notificaciones('Registro no encontrado.', 'Plastiservi', 'error');
				}
			}

            if (funcion=='anularfac') {
                if (respuesta.id == "1") {
					$("#fila" + datatemp.dte_id).remove();
                }
                //console.log(respuesta);
                Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
            }
            if (funcion=='eliminar') { //Elimino desde aqui porque debo hacer previamente varias validaciones
                if (respuesta.mensaje == "ok") {
                    var ruta = '/guardaranularguia';
                    delete datatemp._method;
                    ajaxRequest(datatemp,ruta,'guardaranularguia');
                } else {
                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                }
            }
            if (funcion=='validarupdated1') {
                if (respuesta.mensaje == "ok") {
                    var ruta = '/guiadespanul/store';
                    ajaxRequest(datatemp,ruta,'guardarguiadespanul');    
                } else {
                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                }
            }
            if (funcion=='guardarguiadespanul') {
                if (respuesta.mensaje == "ok") {
                    var ruta = '/guardaranularguia';
                    ajaxRequest(datatemp,ruta,'guardaranularguia');
                } else {
                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                }
            }
            if(funcion=='guardaranularguia'){
				if (respuesta.mensaje == "ok") {
					$("#fila" + respuesta.nfila).remove();
					$("#myModalanularguiafact").modal('hide');
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					Biblioteca.notificaciones('Registro no fue guardado.', 'Plastiservi', 'error');
				}
			}
		},
		error: function () {
		}
	});
}

$("#rut").blur(function(){
	codigo = $("#rut").val();
	aux_sta = $("#aux_sta").val();
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
				rut: $("#rut").val(),
				_token: $('input[name=_token]').val()
			};
			$.ajax({
				url: '/cliente/buscarCli',
				type: 'POST',
				data: data,
				success: function (respuesta) {
					if(respuesta.length>0){
						formato_rut($("#rut"));
					}else{
                        formato_rut($("#rut"));
                        swal({
                            title: 'Cliente no existe.',
                            text: "Aceptar para crear cliente temporal",
                            icon: 'error',
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

$("#btnbuscarcliente").click(function(event){
    $("#rut").val("");
    $("#myModalBusqueda").modal('show');
});

// Función para validar el RUT
function validarRUT(rut) {
    const regex = /^(\d{1,8})-([\dkK])$/;
    if (!regex.test(rut)) {
      return false; // Formato inválido
    }

    const [rutSinDigito, digitoVerificador] = rut.split('-');
    const digitoCalculado = calcularDigitoVerificador(rutSinDigito);

    return digitoVerificador.toUpperCase() === digitoCalculado;
}

  // Función para calcular el dígito verificador
function calcularDigitoVerificador(rut) {
    let suma = 0;
    let multiplicador = 2;

    for (let i = rut.length - 1; i >= 0; i--) {
      suma += parseInt(rut.charAt(i)) * multiplicador;
      multiplicador = multiplicador === 7 ? 2 : multiplicador + 1;
    }

    const resto = suma % 11;
    const digito = 11 - resto;

    if (digito === 10) return 'K';
    if (digito === 11) return '0';
    return digito.toString();
}

  // Evento cuando el campo pierde el foco (blur)
$('#rut1').on('blur', function() {
    let rut = $(this).val().trim();

    // Si el campo está vacío no validar ni mostrar error
    if (rut === '') {
        $('#error-message').hide();
        return;
    }

    // Agregar el guion si no está presente
    if (!rut.includes('-') && rut.length >= 8) {
      rut = rut.slice(0, -1) + '-' + rut.slice(-1);
      $(this).val(rut);
    }

    // Validar el RUT
    if (validarRUT(rut)) {
      $('#error-message').hide(); // Ocultar mensaje de error
    } else {
      $('#error-message').show(); // Mostrar mensaje de error
    }
});

  // Evento cuando el campo recibe el foco (focus)
$('#rut1').on('focus', function() {
    let rut = $(this).val().trim();

    // Eliminar el guion si está presente
    if (rut.includes('-')) {
      rut = rut.replace('-', ''); // Eliminar el guion
      $(this).val(rut);
    }
});

// Evento para validar el ingreso de caracteres
$('#rut1').on('input', function() {
    let rut = $(this).val().trim();

    // Solo permitir números y una "K" al final
    rut = rut.replace(/[^0-9kK]/g, ''); // Eliminar caracteres no válidos

    // Si hay una "K", asegurarse de que esté al final
    const indexK = rut.toLowerCase().indexOf('k');
    if (indexK !== -1 && indexK !== rut.length - 1) {
      // Si la "K" no está al final, eliminarla
      rut = rut.replace(/k/gi, '');
    }

    // Si hay una "K", no permitir más caracteres después de ella
    if (rut.toLowerCase().includes('k')) {
      rut = rut.slice(0, rut.toLowerCase().indexOf('k') + 1);
    }

    // Actualizar el valor del campo
    $(this).val(rut);
});

function copiar_rut(id,rut){
	$("#myModalBusqueda").modal('hide');
	$("#rut1").val(rut);
	//$("#rut").focus();
	$("#rut1").blur();
}

//Cuando el usuario hace clic en "Consultar", activa `serverSide: true`
$("#btnconsultar").click(function () {
    /* $("#tabla-data-picking").DataTable().destroy();
    $("#tabla-data-picking").empty(); // Limpia la tabla para evitar errores de redibujado */
    //$('#tabla-data-picking').html("");

    //console.log("Ejecutando consulta AJAX...");
    var data = datosot();
    //var newUrl = "/pickingpage/" + data.data2;
    var newUrl = "/otitemenvprogprodpage/" + data.data2;
    
    
    configurarTabla("#tabla-data-factura",newUrl,true); // Reinicia DataTable con `serverSide: true`
});

function datosot(){
    var data1 = {
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        rut               : eliminarFormatoRutret($("#rut1").val()),
        ot_id             : $("#ot_id").val(),      
        oc_id             : $("#oc_id").val(),
        at_AnchoIni       : $("#at_AnchoIni").val(),
        at_AnchoFin       : $("#at_AnchoFin").val(),
        notaventa_id      : $("#notaventa_id").val(),
        producto_id       : $("#producto_idPxP").val(),
        sucursal_id       : $("#sucursal_id").val(),
        materiaprima_id   : $("#materiaprima_id").val(),
        sta_envprog       : 0,
        _token            : $('input[name=_token]').val()
    };

    var data2 = "?fechad="+data1.fechad +
    "&fechah="+data1.fechah +
    "&rut="+data1.rut +
    "&ot_id="+data1.ot_id +
    "&oc_id="+data1.oc_id +
    "&at_AnchoIni="+data1.at_AnchoIni +
    "&at_AnchoFin="+data1.at_AnchoFin +
    "&notaventa_id="+data1.notaventa_id +
    "&producto_id="+data1.producto_id +
    "&sucursal_id="+data1.sucursal_id +
    "&materiaprima_id="+data1.materiaprima_id +
    "&sta_envprog="+data1.sta_envprog +
    "&_token="+data1._token;

    var data = {
        data1 : data1,
        data2 : data2
    };
    return data;
}

function encabezadoTabla(){
    let html = `
            <thead>
                <tr>
                    <th class="ocultar"></th>
                    <th title='ID OT'>OT</th>
                    <th title='Fecha'>Fecha</th>
                    <th>RUT</th>
                    <th>Razón Social</th>
                    <th title='Orden de Compra'>OC</th>
                    <th title='Nota Venta'>NotaVenta</th>
                    <th title='ID Producto'>ID Prod</th>
                    <th title='Nombre Producto'>Nombre Prod</th>
                    <th title='Unidad de Medida'>Uni Med</th>
                    <th title='Espesor acuerdo tecnico'>Espesor</th>
                    <th title='Espesor Produccion' style="width: 50px;text-align:right;">Esp Prod</th>
                    <th title='Kilos' style="width: 50px;text-align:right;">Kg</th>
                    <th title='Cantidad' style="width: 50px;text-align:right;">Cant</th>
                    <th title='Cantidad a enviar a programacion (saldo pendiente)' style="width: 50px;text-align:right;">Cant Enviar</th>
                    <th title='Observacion'>Obs</th>
                    <th class="ocultar">Obs Bloqueo</th>
                    <th class="ocultar">oc_folder</th>
                    <th class="ocultar">oc_file</th>
                    <th class="ocultar">nombrepdf</th>
                    <th class="ocultar">updated_at</th>
                    <th class="width80">Acción</th>
                </tr>
            </thead>`;
    return html;
}

var tabla;

function configurarTabla(nombreTabla,url,serverSide) {
    // Si ya hay una tabla inicializada, la destruimos
    if ($.fn.DataTable.isDataTable(nombreTabla)) {
        $(nombreTabla).DataTable().destroy();
        $(nombreTabla).empty(); // Limpia la tabla para evitar errores de redibujado
    }

    //Asegura que la tabla tiene el encabezado correcto
    if ($(nombreTabla + " thead").length === 0) {
        $(nombreTabla).append(encabezadoTabla());
    }

    // Configura DataTable con `serverSide` dinámico
    tabla = $(nombreTabla).DataTable({
        'paging'      : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : serverSide, // Se define dinámicamente
        'ajax'        : url, // Se define dinámicamente
        'scrollX'     : true,
        'scrollY'     : 'calc(100vh - 320px)',
        'scrollCollapse': true,
        'order'       : [[ 1, "desc" ]],
        'columns'     : [
            {
                className: 'dt-control ocultar',
                orderable: false,
                data: null,
                defaultContent: '<i class="btn-accion-tabla btn-sm glyphicon glyphicon-triangle-right text-aqua" title="Mostrar Detalle"></i>'
            },
            {data: 'ot_id'}, // 1
            {data: 'fechahora'}, // 2
            {data: 'rut'}, // 3
            {data: 'razonsocial'}, // 4
            {data: 'oc_id'}, // 5
            {data: 'notaventa_id'}, // 6
            {data: 'producto_id'}, // 7
            {data: 'producto_nombre'}, // 8
            {data: 'unidadmedida_nombre'}, // 9
            {data: 'at_espesor'}, // 10
            {data: 'espesorprod'}, // 11
            {data: 'kgprod'}, // 12
            {data: 'cant'}, // 13
            {data: 'cantprod'}, // 14
            {data: 'otdet_obs'}, // 15
            {data: 'clientebloqueado_descripcion',className:"ocultar"}, //16
            {data: 'oc_file',className:"ocultar"}, //17
            {data: 'oc_file',className:"ocultar"}, //18
            {data: null, defaultContent: '', className:"ocultar"}, //19 nombrepdf (no viene del servidor)
            {data: 'updated_at',className:"ocultar"}, //20
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : 
                "<a href='/ot/enviaraprobarinventsal' class='btn-accion-tabla btn-sm btnaprobar' title='Aprobar'>" +
                    "<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>"+
                "</a>"+
                "<a href='ot' class='btn-accion-tabla btnEditar' title='Editar este registro'>" + 
                    "<i class='fa fa-fw fa-pencil'></i>" + 
                "</a>"+
                "<a href='ot' class='btn-accion-tabla btnEliminar' title='Eliminar este registro'>"+
                    "<i class='fa fa-fw fa-trash text-danger'></i>"+
                "</a>"
            }
        ],
        "language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            $(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);
            $(row).attr('updated_at', data.updated_at);
            //"<a href='#' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + "</a>";

            aux_text = 
                `<a class="btn-accion-tabla btn-sm" title="Ver OT: ${data.ot_id}" onclick='genpdf(${data.ot_id},"","ver-pdf-guia-despacho","/ot/exportPdf/${data.ot_id}")'>
                    ${data.ot_id}
                </a>`;
            $('td', row).eq(1).html(aux_text);

            //$('td', row).eq(2).attr('style','font-size: 12px;');
            $('td', row).eq(2).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(2).html(fechaddmmaaaa(aux_fecha));


            aux_text = "";
            if(data.oc_file != "" && data.oc_file != null){
                if(data.notaventa_id != "" && data.notaventa_id != null){
                    aux_text = 
                    `<a class="btn-accion-tabla btn-sm" title="Ver NV: ${data.notaventa_id}" onclick='genpdfNV(${data.notaventa_id},1)'>
                        ${data.notaventa_id}
                    </a>`;
                    $('td', row).eq(6).html(aux_text);
                    aux_text = 
                    `<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm' title='Orden de Compra' onclick='verpdf2("${data.notaventa_id}.pdf",2)'>
                        ${data.oc_id} 
                    </a>`;    
                }else{
                    aux_text = 
                    `<a style='padding-left: 0px;' class='btn-accion-tabla btn-sm' title='Orden de Compra' onclick='verpdf3("${data.oc_file}",2,"oc")'>
                        ${data.oc_id} 
                    </a>`;
                }
                $('td', row).eq(5).html(aux_text);
            }

            aux_text = data.producto_id;
            if(data.acuerdotecnico_id != 0){
                aux_text = 
                `<a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="Acuerdo Técnico" onclick='genpdfAcuTec(${data.acuerdotecnico_id},${data.cliente_id},"")'>
                    ${data.producto_id}
                </a>`;    
            }
            $('td', row).eq(7).html(aux_text);

            $('td', row).eq(9).attr('style','text-align:center');

            aux_text = MASKLA(data.at_espesor,3);
            $('td', row).eq(10).attr('style','text-align:right');
            $('td', row).eq(10).html(aux_text);

            // espesorprod — lleva los data-* con los parámetros del AT para que
            // pesoUnitario() pueda calcular sin llamar al backend.
            // doble: 2 = bolsa (2 capas de film), 1 = film plano (categoriaprod_id=13)
            aux_text =
                `<input type="text" name="espesorprod[]" id="espesorprod${data.id}"
                 class="form-control numerico3d"
                 value="${MASKLA(data.espesorprod,3)}" valor="${data.espesorprod}"
                 item="${data.id}" style="width: 70px;text-align:right;" maxlength="6"
                 data-producto-id="${data.producto_id}"
                 data-ancho="${data.at_ancho        || 0}"
                 data-largo="${data.at_largo        || 0}"
                 data-pe="${data.at_pe              || 0}"
                 data-formatofilm="${data.at_formatofilm     || 0}"
                 data-um-id="${data.at_unidadmedida_id || 0}"
                 data-doble="${data.at_categoriaprod_id == 13 ? 1 : 2}"
                 onfocus="guardarValorPrev(this)"
                 onblur="calcularPeso(this)"
                 valororiginal="${data.espesorprod}" kgoriginal="${data.kgprod}"/>`;
            $('td', row).eq(11).html(aux_text);

            // kgprod — también tiene onfocus/onblur para el cálculo inverso kg→cant
            let kgPendiente = data.kg - (parseFloat(data.kgenvprog) || 0);
            if (kgPendiente < 0) kgPendiente = 0;
            aux_text =
                `<input type="text" name="kgprod[]" id="kgprod${data.id}"
                 class="form-control numerico"
                 value="${MASKLA(kgPendiente,2)}" valor="${kgPendiente}"
                 item="${data.id}" style="width: 100px;text-align:right;" maxlength="15"
                 valororiginal="${kgPendiente}"
                 onfocus="guardarValorPrev(this)"
                 title="Valor original Pendiente : ${MASKLA(kgPendiente,2)} kg"
                 onblur="calcularCant(this)"/>`;
            $('td', row).eq(12).html(aux_text);

            aux_text = MASKLA(data.cant,0);
            $('td', row).eq(13).attr('style','text-align:right');
            $('td', row).eq(13).html(aux_text);

            // cantprod — onfocus guarda el valor previo, onblur recalcula kg→cant
            let cantPendiente = data.cant - (parseFloat(data.cantenvprog) || 0);
            if (cantPendiente < 0) cantPendiente = 0;
            aux_text =
                `<input type="text" name="cantprod[]" id="cantprod${data.id}"
                 class="form-control numerico"
                 value="${MASKLA(cantPendiente,2)}" valor="${cantPendiente}"
                 item="${data.id}" style="width: 100px;text-align:right;" maxlength="15"
                 onfocus="guardarValorPrev(this)"
                 onblur="calcularPeso(this)"
                 valororiginal="${cantPendiente}"
                 data-cant="${data.cant}" data-cantenvprog="${parseFloat(data.cantenvprog)||0}"/>`;
            $('td', row).eq(14).html(aux_text);    

            $('td', row).eq(20).addClass('updated_at');
            $('td', row).eq(20).attr('id','updated_at' + data.id);
            $('td', row).eq(20).attr('name','updated_at' + data.id);

            aux_clienteBloqueado = validarClienteBloqueadoxModulo(data); 
            aux_displaybtnac = ``;
            aux_displaybtnbl = ``;
            if(aux_clienteBloqueado == ""){
                aux_displaybtnac = ``;
                aux_displaybtnbl = `style="display:none;"`;
            }else{
                aux_displaybtnac = `style="display:none;"`;
                aux_displaybtnbl = ``;
            }
            aprobstatus = 1;
            aux_text = 
                `<div class="tools11">
                    <a ${aux_displaybtnbl} class="btn-accion-tabla botonbloq${data.id}" title="Condición financiera en revisión: ${aux_clienteBloqueado}" onclick="llenartablaDataCobranza(${data.id},${data.cliente_id},0,0)">
                        <i class="fa fa-fw fa-lock text-danger fa-lg"></i>
                    </a>
                    <a ${aux_displaybtnac} id="bntaprobnv${data.id}" name="bntaprobnv${data.id}" class="btn-accion-tabla btn-sm action-buttons botonac${data.id}" onclick="procesarRegOt(${data.id},${data.updatednum_at},${data.otupdatednum_at})" title="Enviar a programacion">
                        <i class="fa fa-fw fa-save fa-lg"></i>
                    </a>
                </div>`;
            //$('td', row).eq(12).attr('style','padding-top: 0px;padding-bottom: 0px;');
            $('td', row).eq(21).html(aux_text);

        }
    });
    // Aplicar el plugin numeric a los inputs dinámicos después de cada redibujado de la tabla
    tabla.on('draw', function() {
        claseformatonumerico(".numerico",2);
        claseformatonumerico(".numerico3d",3);
        // font-size ya está definido en el <style> del blade → no es necesario aplicarlo aquí
    });
};

// Aplicar el plugin numeric a los inputs dinámicos después de cada redibujado de la tabla
/* tabla.on('draw', function() {
    claseformatonumerico(".numerico",2);
    claseformatonumerico(".numerico3d",4);
    $('#tabla-data-factura th, #tabla-data-factura td').css('font-size', '12px');

});
 */
/* 

// Add event listener for opening and closing details
tabla.on('click', 'td.dt-control', function (e) {
    let tr = e.target.closest('tr');
    let row = table.row(tr);

    if (row.child.isShown()) {
        // This row is already open - close it
        row.child.hide();
        $(this).html('<i class="btn-accion-tabla btn-sm glyphicon glyphicon-triangle-right text-aqua" title="Mostrar Detalle"></i>');
    }
    else {
        // Open this row
        row.child(format(row.data())).show();
        $(this).html('<i class="btn-accion-tabla btn-sm glyphicon glyphicon-triangle-bottom text-aqua" title="Mostrar Detalle"></i>');
    }
}); */


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

function procesarRegOt(id,updatednum_at,otupdatednum_at) {
    var data = {
        id: id,
        nfila: id,
        modulo_id: 34,
        updatednum_at: updatednum_at,
        otupdatednum_at: otupdatednum_at,
		nombreobjeto: "bntaprobnv" + id,
        espesorprod : $("#espesorprod" + id).attr("valor"),
        kgprod : $("#kgprod" + id).attr("valor"),
        cantprod: $("#cantprod" + id).attr("valor"),
        _token: $('input[name=_token]').val()
    };
    ruta = '/otitemenvprogprod/aprobar';
    mensaje = 'Aprobar';
    staobs = 0;
    if(data.espesorprod <= 0){
        swal({
            title: `Falta incluir Informacion!`,
            text: "Espesor de producción debe ser mayor a 0.",
            buttons: {
                cancel: "Cancelar"
            },
            icon: 'warning',
        });
        return 0;
    }
    if(data.espesorprod >= 1){
        swal({
            title: `Falta incluir Informacion!`,
            text: "Espesor de producción debe ser menor a 1.",
            buttons: {
                cancel: "Cancelar"
            },
            icon: 'warning',
        });
        return 0;
    }
    if(data.kgprod <= 0){
        swal({
            title: `Falta incluir Informacion!`,
            text: "Kg produccion debe ser mayor a 0.",
            buttons: {
                cancel: "Cancelar"
            },
            icon: 'warning',
        });
        return 0;
    }
    if(data.cantprod <= 0){
        swal({
            title: `Falta incluir Informacion!`,
            text: "Cantidad produccion debe ser mayor a 0.",
            buttons: {
                cancel: "Cancelar"
            },
            icon: 'warning',
        });
        return 0;
    }


    // Validar que no exceda el saldo pendiente
    let cantMaxEnviar = parseFloat($("#cantprod" + id).data("cant")) - parseFloat($("#cantprod" + id).data("cantenvprog") || 0);
    if (parseFloat(data.cantprod) > cantMaxEnviar + 0.001) {
        swal({
            title: `Cantidad excede saldo!`,
            text: `Solo puede enviar hasta ${MASKLA(cantMaxEnviar, 2)} unidades (saldo pendiente).`,
            buttons: { cancel: "Cancelar" },
            icon: 'warning',
        });
        return 0;
    }

    // Crear una caja de texto para la observación y un contenedor para el mensaje de error
    swal({
        title: `¿${mensaje}?`,
        text: "Esta acción no se puede deshacer!",
        content: createCustomContent("",staobs), // Función para añadir la caja de texto y mensaje
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
        icon: 'warning',
        onOpen: () => {
            // Enfocar automáticamente en el input
            const inputField = document.getElementById('observacion');
            if (inputField) {
                inputField.focus();
            }
        },
    }).then((value) => {
        if (value) {
            // Obtener el valor de la caja de texto de observaciones
            if(staobs == 1){
                var observacion = document.getElementById('observacion').value.trim();
                if (!observacion ) {
					//console.log($("#" + nombreobjeto).attr('id'));
                    // Mostrar mensaje de error si el campo está vacío
                    procesarRegOt(id, aux_modulo_id, ruta, updatednum_at, mensaje,1,"El campo no puede quedar en blanco",nombreobjeto);
        
                    /* document.getElementById('error-message').textContent = "El campo no puede quedar en blanco";
                    return swal({
                        title: "Error",
                        text: "El campo de observación no puede quedar en blanco",
                        icon: "error",
                        buttons: {
                            confirm: "Aceptar"
                        }
                    }).then((value) => {
                        procesarReg(id, aux_modulo_id, ruta, updatednum_at, mensaje,"El campo no puede quedar en blanco")
                    }); */
                } else {
                    // Limpiar el mensaje de error y enviar la solicitud AJAX
                    document.getElementById('error-message').textContent = "";
                    data.obs = observacion; // Añadir la observación a los datos
                    enviarAProgamacion(data, ruta);
                }
            }else{
                enviarAProgamacion(data, ruta);
            }
        }
    });
	const inputField = document.getElementById('observacion');
	if (inputField) {
		inputField.focus();
	}


}

/**
 * Envía el ítem a programación con manejo de envío parcial vs completo.
 * - sta_envprog=2 (todo enviado): elimina la fila de la tabla.
 * - sta_envprog=1 (parcial): actualiza los inputs con el saldo pendiente.
 */
function enviarAProgamacion(data, ruta) {
    $.ajax({
        url: ruta,
        type: 'POST',
        data: data,
        success: function(respuesta) {
            if (respuesta.id != 0) {
                if (respuesta.sta_envprog == 2) {
                    // Todo enviado: eliminar la fila
                    let element = $("#" + data.nombreobjeto);
                    let table = element.closest('table').DataTable();
                    let row = table.row("#fila" + respuesta.dte_id);
                    if (row && row.child && row.child.isShown()) { row.child.hide(); }
                    $("#fila" + respuesta.dte_id).remove();
                    Biblioteca.notificaciones('Todo enviado a programación con éxito.', 'Plastiservi', 'success');
                } else {
                    // Envío parcial: actualizar saldo pendiente en la fila
                    let cantNuevoPend = parseFloat(respuesta.cant)      - parseFloat(respuesta.cantenvprog);
                    let kgNuevoPend   = parseFloat(respuesta.kg)        - parseFloat(respuesta.kgenvprog);
                    if (cantNuevoPend < 0) cantNuevoPend = 0;
                    if (kgNuevoPend   < 0) kgNuevoPend   = 0;
                    let fid = respuesta.dte_id;
                    $("#cantprod" + fid).val(MASKLA(cantNuevoPend, 2));
                    $("#cantprod" + fid).attr("valor", cantNuevoPend);
                    $("#cantprod" + fid).data("cantenvprog", respuesta.cantenvprog);
                    $("#kgprod"   + fid).val(MASKLA(kgNuevoPend, 2));
                    $("#kgprod"   + fid).attr("valor", kgNuevoPend);
                    Biblioteca.notificaciones(
                        'Envío parcial guardado. Saldo pendiente: ' + MASKLA(cantNuevoPend, 0) + ' unidades.',
                        'Plastiservi', 'success'
                    );
                }
            } else {
                swal({
                    text: respuesta.mensaje,
                    icon: respuesta.tipo_alert || 'error',
                    buttons: { confirm: "Aceptar" }
                });
            }
        },
        error: function() {
            Biblioteca.notificaciones('Error de conexión al intentar guardar.', 'Plastiservi', 'error');
        }
    });
}

/**
 * calcularPeso — recalcula kgprod cuando el usuario cambia espesorprod o cantprod.
 * Se dispara en onblur de ambos inputs.
 * Si el valor no cambió respecto al focus anterior, no hace nada (evita cálculos
 * innecesarios al navegar con Tab sin modificar nada).
 */
function calcularPeso(input) {
    var $input   = $(input);
    var id       = $input.attr('item');

    // No recalcular si el valor no cambió desde el último focus
    if ($input.data('prev-valor') === $input.attr('valor')) return;

    var cantprod = parseFloat($("#cantprod" + id).attr('valor')) || 0;
    var pUnit    = pesoUnitario(id);
    var kgNuevo  = pUnit * cantprod;

    $("#kgprod" + id)
        .val(MASKLA(kgNuevo, 2))
        .attr('valor', parseFloat(kgNuevo).toFixed(2));
}

/**
 * calcularCant — cálculo inverso: recalcula cantprod cuando el usuario
 * cambia kgprod directamente.
 * Se dispara en onblur del input kgprod.
 * Si el peso unitario no es calculable (= 0) no hace nada para evitar división por cero.
 */
function calcularCant(input) {
    var $input = $(input);
    var id     = $input.attr('item');

    // No recalcular si el valor no cambió desde el último focus
    if ($input.data('prev-valor') === $input.attr('valor')) return;

    var kgprod = parseFloat($input.attr('valor')) || 0;
    var pUnit  = pesoUnitario(id);

    // Sin peso unitario calculable no se puede obtener la cantidad
    if (pUnit <= 0) return;

    var cantNueva = kgprod / pUnit;

    $("#cantprod" + id)
        .val(MASKLA(cantNueva, 2))
        .attr('valor', parseFloat(cantNueva).toFixed(2));
}