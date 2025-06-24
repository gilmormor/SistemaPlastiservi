$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    $('.datepicker').datepicker({
		language: "es",
        autoclose: true,
        clearBtn : true,
		todayHighlight: true
    }).datepicker("setDate");

    $(".numerico").numeric();
    nombreTabla = "#tabla-data-factura";
    $(nombreTabla).append(encabezadoTabla());
    configurarTabla(nombreTabla,"",false)

});
$(document).on('input', '.edit-obs', function() {
    var fila = $(this).attr('fila');
    var prop = $(this).attr('nameobsg');
    var texto = $(this).val();
    
    $('#producto_id' + fila).attr(prop, texto);
    
    // Debug: Ver en consola qué se está actualizando
    console.log(
        'Se actualizó producto_id' + fila, 
        'con el texto:', texto
    );
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
    var newUrl = "/otitemprogramacionpage/" + data.data2;
    
    
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
                    <th></th>
                    <th title='ID OT'>OT</th>
                    <th title='Fecha'>Fecha</th>
                    <th>RUT</th>
                    <th class="width200">Razón Social</th>
                    <th title='Orden de Compra'>OC</th>
                    <th title='Nota Venta'>NV</th>
                    <th title='ID Producto'>ID Prod</th>
                    <th class="width150" title='Nombre Producto'>Nombre Prod</th>
                    <th title='Unidad de Medida'>Uni Med</th>
                    <th title='Espesor acuerdo tecnico'>Espesor</th>
                    <th title='Espesor Produccion'>Espesor Prod</th>
                    <th title='Cantidad' style="width: 50px;text-align:right;">Cant</th>
                    <th title='Kilos' style="width: 50px;text-align:right;">Kg</th>
                    <th title='Observacion General'>ObsGen</th>
                    <th title='Observacion'>Obs</th>
                    <th class="width80">Acción</th>
                    <th title='Extrusora'>Ex</th>
                    <th title='Impresora'>Imp</th>
                    <th title='Selladora'>Sella</th>
                    <th title='Orden Atencion'>Orden</th>
                    <th class="ocultar">Obs Bloqueo</th>
                    <th class="ocultar">oc_folder</th>
                    <th class="ocultar">oc_file</th>
                    <th class="ocultar">nombrepdf</th>
                    <th class="ocultar">updated_at</th>
                </tr>
            </thead>
            <tfoot>
            </tfoot>

        `;
    return html;
}

var tabla;

var maquinas = JSON.parse($("#maquinas").val());
//console.log(maquinas);


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
        'scrollx'     : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : serverSide, // Se define dinámicamente
        'ajax'        : url, // Se define dinámicamente
        'order'       : [[ 1, "desc" ]],
        'columns'     : [
            {
                className: 'dt-control',
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
            {data: 'cantprod'}, // 12
            {data: 'kgprod'}, // 13
            {data: 'otdet_obs'}, // 14
            {data: 'otdet_obs'}, // 15
            {data: 'otdet_obs'}, // 16
            {data: 'otdet_obs'}, // 17
            {data: 'otdet_obs'}, // 18
            {data: 'otdet_obs'}, // 19
            {data: 'otdet_obs'}, // 20
            {data: 'clientebloqueado_descripcion',className:"ocultar"}, //21
            {data: 'oc_file',className:"ocultar"}, //22
            {data: 'oc_file',className:"ocultar"}, //23
            {data: 'updated_at',className:"ocultar"}, //24
/*             {defaultContent : 
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
 */        ],
        "language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            $(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);
            $(row).attr('updated_at', data.updated_at);
            $('td', row).eq(0).attr("id","aux_detalle" + data.id);
            $('td', row).eq(0).attr("name","aux_detalle" + data.id);
            $('td', row).eq(0).attr("etapaprod",data.detetapaprod_array);
            //"<a href='#' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + "</a>";

            aux_text = 
                `<a class="btn-accion-tabla btn-sm" title="Ver OT: ${data.ot_id}" onclick='genpdf(${data.ot_id},"","ver-pdf-guia-despacho","/ot/exportPdf/${data.ot_id}")'>
                    ${data.ot_id}-${data.id}
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
                `<a style="padding-left: 0px; display: inline-block;" class="btn-accion-tabla btn-sm tooltipsC" title="Acuerdo Técnico" onclick='genpdfAcuTec(${data.acuerdotecnico_id},${data.cliente_id},"")'>
                    ${data.producto_id}
                </a>`;    
            }
            if(data.at_impresofoto != "" && data.at_impresofoto != null){
                aux_text += 
                    `<a style="display: inline-block;" class="btn-accion-tabla btn-sm tooltipsC" title="Ver Imagen" onclick='verpdf2(\"at/${data.at_impresofoto}\",2,"","ver-arte-acuerdo-tecnico")'>
                        <i class="fa fa-fw fa-photo"></i>
                    </a>`;
            }
            aux_obsext = "";
            if(data.at_materiaprimaobs){
                aux_obsext = data.at_materiaprimaobs;
            }
            aux_obssell = "";
            if(data.at_tiposelloobs){
                aux_obssell = data.at_tiposelloobs;
            }
            aux_obsimp = "";
            if(data.at_impresoobs){
                aux_obsimp = data.at_impresoobs;
            }
        
            $('td', row).eq(7).attr('style','font-size: 12px; white-space: nowrap;');
            $('td', row).eq(7).attr('name',"producto_id"+data.id)
            $('td', row).eq(7).attr('id',"producto_id"+data.id)
            $('td', row).eq(7).attr('file'+data.id,data.id)
            $('td', row).eq(7).attr('aux_obsext',aux_obsext);
            $('td', row).eq(7).attr('aux_obsimp',aux_obsimp);
            $('td', row).eq(7).attr('aux_obssell',aux_obssell);
            $('td', row).eq(7).html(aux_text);

            $('td', row).eq(9).attr('style','text-align:center');

            aux_text = MASKLA(data.at_espesor,3);
            $('td', row).eq(10).attr('style','text-align:right');
            $('td', row).eq(10).html(aux_text);

            aux_text = MASKLA(data.espesorprod,3);
            $('td', row).eq(11).attr('style','text-align:right');
            $('td', row).eq(11).html(aux_text);


            aux_text = MASKLA(data.cantprod,0);
            $('td', row).eq(12).attr('style','text-align:right');
            $('td', row).eq(12).html(aux_text);

            aux_text = 
                `<input type="text" name="kgprod[]" id="kgprod${data.id}" class="form-control numerico requerido${data.id}" value="${MASKLA(data.kgprod,2)}" valor="${data.kgprod}" item="${data.id}" style="width: 100px;text-align:right;" maxlength="15"/>`;
            $('td', row).eq(13).html(aux_text);


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
                `<textarea name="obs[]" id="obs${data.id}" class="form-control" value="" item="${data.id}" style="width: 150px;" placeholder="Observación" maxlength="100"></textarea>`;
            $('td', row).eq(15).html(aux_text);

                        aux_text = 
                `<div class="tools11">
                    <a ${aux_displaybtnbl} class="btn-accion-tabla botonbloq${data.id}" title="Condición financiera en revisión: ${aux_clienteBloqueado}" onclick="llenartablaDataCobranza(${data.id},${data.cliente_id},0,0)">
                        <i class="fa fa-fw fa-lock text-danger fa-lg"></i>
                    </a>
                    <a ${aux_displaybtnac} id="bntaprobnv${data.id}" name="bntaprobnv${data.id}" class="btn-accion-tabla btn-sm action-buttons botonac${data.id}" onclick="procesarRegOt(${data.id},${data.updatednum_at},${data.otupdatednum_at})" title="Enviar a Producción">
                        <i class="fa fa-fw fa-save fa-lg"></i>
                    </a>
                </div>`;
            //$('td', row).eq(12).attr('style','padding-top: 0px;padding-bottom: 0px;');
            $('td', row).eq(16).html(aux_text);


            
            // Generar el select con las extrusoras en la columna 14 (otdet_obs)
            var aux_text = `<select class="form-control selectpicker" onchange="recibirChange(this)" data-nombre="Extrusora" name="extrusora${data.id}" id="extrusora${data.id}" data-width="100%" data-size="5">`;
            aux_text += `<option value="0">...</option>`;
            maquinas.forEach(function (maquina) {
                if(maquina.maquinagrupo_id == 1){
                    aux_text += `<option value="${maquina.id}">${maquina.nombre}</option>`;
                }
                
            });
            aux_text += `</select>`;
            $('td', row).eq(17).html(aux_text); // Insertar el select en la celda correspondiente


            // Generar el select con las impresoras en la columna 14 (otdet_obs)
            var aux_text = `<select class="form-control selectpicker" data-nombre="Impresora" onchange="recibirChange(this)" name="impresora${data.id}" id="impresora${data.id}" data-width="100%" data-size="5">`;
            if(data.at_impreso == 1){
                aux_text += `<option value="0">...</option>`;
                maquinas.forEach(function (maquina) {
                    if(maquina.maquinagrupo_id == 2 && data.at_impreso == 1){
                        aux_text += `<option value="${maquina.id}">${maquina.nombre}</option>`;
                    }
                    
                });    
            }else{
                aux_text += `<option value="X">X</option>`;
            }
            aux_text += `</select>`;
            $('td', row).eq(18).html(aux_text); // Insertar el select en la celda correspondiente

            // Generar el select con las selladoras en la columna 14 (otdet_obs)
            var aux_text = `<select class="form-control selectpicker" data-nombre="Selladora" onchange="recibirChange(this)" name="selladora${data.id}" id="selladora${data.id}" data-width="100%" data-size="5">`;
            if(data.at_sello == "Sin Sello"){
                aux_text += `<option value="X">X</option>`;
            }else{
                aux_text += `<option value="0">...</option>`;
                maquinas.forEach(function (maquina) {
                    if(maquina.maquinagrupo_id == 3){
                        aux_text += `<option value="${maquina.id}">${maquina.nombre}</option>`;
                    }
                    
                });

            }
            aux_text += `</select>`;
            $('td', row).eq(19).html(aux_text); // Insertar el select en la celda correspondiente

            // Generar el select con las Orden Atencion en la columna 14 (otdet_obs)
            var aux_text = `<select class="form-control selectpicker" name="ordenaten${data.id}" id="ordenaten${data.id}" data-width="100%" data-size="5">`;
            for (let i = 1; i < 11; i++) {
                aux_text += `<option value="${i}">${i}</option>`;
                
            }
            aux_text += `</select>`;
            $('td', row).eq(30).html(aux_text); // Insertar el select en la celda correspondiente
            
            
            $('td', row).eq(24).addClass('updated_at');
            $('td', row).eq(24).attr('id','updated_at' + data.id);
            $('td', row).eq(24).attr('name','updated_at' + data.id);


        }
    });
    // Aplicar el plugin numeric a los inputs dinámicos después de cada redibujado de la tabla
    tabla.on('draw', function() {
        //$('.selectpicker').selectpicker();
        $('.selectpicker').selectpicker({
            container: 'body' // Hace que el dropdown no se esconda dentro del DataTable
        });
        claseformatonumerico(".numerico",2);
        claseformatonumerico(".numerico3d",3);
    });

    // Add event listener for opening and closing details

    
};

$("#tabla-data-factura").on('click', 'td.dt-control', function (e) {
    let tr = e.target.closest('tr');
    let row = tabla.row(tr);

    if (row.child.isShown()) {
        // This row is already open - close it
        d = row.data();
        //$("#producto_id" + d.id).attr(aux_obsext)
        $("#producto_id"+d.id).attr('aux_obsext',$("#obsext" + d.id).val());
        $("#producto_id"+d.id).attr('aux_obsimp',$("#obsimp" + d.id).val());
        $("#producto_id"+d.id).attr('aux_obssell',$("#obssell" + d.id).val());
    
        row.child.hide();
        $(this).html('<i class="btn-accion-tabla btn-sm glyphicon glyphicon-triangle-right text-aqua" title="Mostrar Detalle"></i>');
    }
    else {
        // Open this row
        row.child(format(row.data())).show();
        $(this).html('<i class="btn-accion-tabla btn-sm glyphicon glyphicon-triangle-bottom text-aqua" title="Mostrar Detalle"></i>');
    }
});

// Formatting function for row details - modify as you need
function format(d) {
    // Descomponer el campo nvdetalle
    //console.log(d.nvdetalle);
    let tablaHtml = `
    <div style="display: flex; align-items: flex-start;"> <!-- Contenedor Flex (flecha al principio) -->
        <div style="margin-left: 20px;">&#8627;</div> <!-- Flecha desplazada un poco a la derecha -->
    <table id='etapaprod${d.id}' name='etapaprod${d.id}' class='table display AllDataTables table-hover table-condensed tablascons' style='width: 50%;'>
    <thead>
        <tr>
        <th>Cod</th>
        <th>Etapa Produccion</th>
        <th>Maquina</th>
        <th>Observacion</th>
        </tr>
    </thead>
    <tbody>`;
    
    d.detetapaprod_array.split(";").forEach(registro => {
        const [etapaprod_id, etapaprod_nombre] = registro.split("|");
        let aux_idep = `${d.id}-${etapaprod_id}`;
        let aux_contmaq = 0;
        // Generar el select con las extrusoras en la columna 14 (otdet_obs)
        var aux_text = `<select class="form-control requerido${d.id}" onchange="recibirChange(this)" data-nombre="${etapaprod_nombre} ${etapaprod_id}" name="maquina_id${aux_idep}" id="maquina_id${aux_idep}" data-width="100%" data-size="5">`;
        aux_text += `<option value="0">...</option>`;
        maquinas.forEach(function (maquina) {
            if(maquina.etapaprod_id == etapaprod_id){
                aux_contmaq++;
                aux_text += `<option value="${maquina.id}">${maquina.nombre}</option>`;
            }
            
        });
        aux_text += `</select>`;
        if(aux_contmaq == 0){
            aux_text = `<span class="text-danger">No hay máquinas asignadas</span>`;
        }
        tablaHtml += `
            <tr>
                <td id="epid${aux_idep}" name="epid${aux_idep}">${etapaprod_id}</td>
                <td id="epnombre${aux_idep}" name="epnombre${aux_idep}">${etapaprod_nombre}</td>
                <td>${aux_text}</td>
                <td><textarea id="epobs${aux_idep}" name="epobs${aux_idep}" class="form-control edit-obs" style="width: 250px;" placeholder="Observación" maxlength="100" fila="${d.id}" nameobsg="aux_obsext">${aux_obsext}</textarea></td>
            </tr>`;
        //$('.selectpicker').selectpicker();
    });

    tablaHtml += `
        </tbody>
        </table>
    </div>`;

    return tablaHtml;

    aux_obsext = $("#producto_id"+d.id).attr('aux_obsext');
    aux_obsimp = $("#producto_id"+d.id).attr('aux_obsimp');
    aux_obssell = $("#producto_id"+d.id).attr('aux_obssell');


    tableHtml =
    `
            <div style="display: flex; align-items: flex-start;"> <!-- Contenedor Flex (flecha al principio) -->
                <div style="margin-left: 20px;">&#8627;</div> <!-- Flecha desplazada un poco a la derecha -->
                </div>
            </div>
            <div class="col-xs-12 col-md-9 col-sm-12">
                <div class="col-xs-12 col-md-12 col-sm-12">
                    <label>Cod Prod: 
                        <a style="padding-left: 0px; display: inline-block;" class="btn-accion-tabla btn-sm tooltipsC" title="Acuerdo Técnico" onclick='genpdfAcuTec(${d.acuerdotecnico_id},${d.cliente_id},"")'>
                            ${d.producto_id}
                        </a>
                        <span style="font-weight: normal;">${d.producto_nombre}</span></label>
                    </label>
                </div>
            </div>
            <div class="col-xs-12 col-md-9 col-sm-12">
                <div class="col-xs-12 col-md-2 col-sm-2 text-right">
                    <label>Obs Extrusión:</label>
                </div>
                <div class="col-xs-12 col-md-10 col-sm-10">
                    <textarea name="obsext${d.id}" id="obsext${d.id}" class="form-control edit-obs" style="width: 250px;" placeholder="Observación" maxlength="100" fila="${d.id}" nameobsg="aux_obsext">${aux_obsext}</textarea>
                </div>
            </div>`
        if($("#impresora" + d.id).val() != "X"){
            tableHtml +=
            `
            <div class="col-xs-12 col-md-9 col-sm-12">
                <div class="col-xs-12 col-md-2 col-sm-2 text-right">
                    <label>Obs Impresion:</label>
                </div>
                <div class="col-xs-12 col-md-10 col-sm-10">
                    <textarea name="obsimp${d.id}" id="obsimp${d.id}" class="form-control edit-obs" style="width: 250px;" placeholder="Observación" maxlength="100" fila="${d.id}" nameobsg="aux_obsimp">${aux_obsimp}</textarea>
                </div>
            </div>
            `

        }
        if($("#selladora" + d.id).val() != "X"){
            tableHtml +=
            `
            <div class="col-xs-12 col-md-9 col-sm-12">
                <div class="col-xs-12 col-md-2 col-sm-2 text-right">
                    <label>Obs Sellado:</label>
                </div>
                <div class="col-xs-12 col-md-10 col-sm-10">
                    <textarea name="obssell${d.id}" id="obssell${d.id}" class="form-control edit-obs" style="width: 250px;" placeholder="Observación" maxlength="100" fila="${d.id}" nameobsg="aux_obssell">${aux_obssell}</textarea>
                </div>
            </div>`;
        }

    //console.log(tableHtml);
    // Devolver la tabla HTML
    return tableHtml;
    
}

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
    if(validarCamposRequeridos(id)){
        aux_apetapaprod = recogerEtapas(id);
        //return recogerEtapas(id);
        $("#aux_detalle" + id).on('click');
        var data = {
            otdet_id: id,
            nfila: id,
            modulo_id: 34,
            updatednum_at: updatednum_at,
            otupdatednum_at: otupdatednum_at,
            nombreobjeto: "bntaprobnv" + id,
            obs         : $("#obs" + id).val(),
            kgprod      : $("#kgprod" + id).attr("valor"),
            /* extrusora : $("#extrusora" + id).val(),
            impresora : $("#impresora" + id).val(),
            selladora : $("#selladora" + id).val(),
            obsext: $("#producto_id" + id).attr("aux_obsext"),
            obsimp: $("#producto_id" + id).attr("aux_obsimp"),
            obssell: $("#producto_id" + id).attr("aux_obssell"), */
            ordenaten : $("#ordenaten" + id).val(),
            apetapaprod : aux_apetapaprod,
            _token: $('input[name=_token]').val()
        };
        ruta = '/otitemprogramacion/aprobar';
        mensaje = 'Aprobar';
        staobs = 0;
        //validarCampos(id);
        divPadre = $("#kgprod" + id);
        divPadre.removeClass("error");
        if(data.kgprod == 0){
            divPadre.addClass("error");
            swal({
                title: `Falta incluir Informacion!`,
                text: "Kg debe ser mayor a 0.",
                buttons: {
                    cancel: "Cancelar"
                },
                icon: 'warning',
            });
            return 0;
        }

        /* if(validarDatos("extrusora" + id) == 0){
            return 0;
        }
        if(validarDatos("impresora" + id) == 0){
            return 0;
        }
        if(validarDatos("selladora" + id) == 0){
            return 0;
        } */
        // Crear una caja de texto para la observación y un contenedor para el mensaje de error
        swal({
            title: `¿${mensaje}?`,
            text: "Esta acción no se puede deshacer!",
            buttons: {
                cancel: "Cancelar",
                confirm: "Aceptar"
            },
            icon: 'warning',
        }).then((value) => {
            if (value) {
                // Obtener el valor de la caja de texto de observaciones
                ajaxRequestGeneral(data, ruta, 'procesarDTE');
            }
        });
    }else{
        swal({
            title: `Falta incluir Informacion!`,
            text: "Debe completar los campos requeridos.",
            buttons: {
                cancel: "Cancelar"
            },
            icon: 'warning',
        });
        return 0;
    }


}

function recibirChange(aux_this) {
    //console.log(aux_this);
    //console.log(aux_this.id);
    validarDatos(aux_this.id);
}

function validarDatos(id){
    //divPadre = $("#" + id).closest("div");
    divPadre = $("#" + id);
    divPadre.removeClass("error");
    if($("#" + id).val() == 0){
        divPadre.addClass("error");
        swal({
            title: `Falta incluir Informacion!`,
            text: $("#"+ id).attr("data-nombre") + " debe ser mayor a 0.",
            buttons: {
                cancel: "Cancelar"
            },
            icon: 'warning',
        });
        return 0;
    }
    return 1;
}

function validarCamposRequeridos(id) {
    let esValido = true;
    // Validar campos requeridos
    // Selecciona todos los campos con la clase 'requerido' y el id
    $('.requerido' + id).each(function() {
        let $el = $(this);
        let valor = $.trim($el.val());
        let tipo = $el.prop('tagName').toLowerCase();

        // Eliminar mensajes y estilos anteriores
        $el.removeClass('error-input');
        $el.next('.error-msg').remove();

        if (valor === '' || valor === '0,00' || valor === '0.00' || valor === '0' || (tipo === 'select' && $el.prop('selectedIndex') === 0)) {
            $el.addClass('error-input');
            $el.after('<span class="error-msg">Este campo es obligatorio</span>');
            esValido = false;
        }
    });

    return esValido;
}

function recogerEtapas(idFila) {
    // idFila = 120 en tu ejemplo → #etapaprod120
    const etapas = $("#etapaprod" + idFila + " tbody tr").map(function () {
        const $tr = $(this);

        // 1) Código y nombre están en las dos primeras celdas
        const apetapaprod_id     = $.trim($tr.find("td:eq(0)").text());   // «1», «2», «3»…
        const nombre  = $.trim($tr.find("td:eq(1)").text());   // «Programacion», «Mezclas»…

        // 2) Si hay <select>, tomamos su valor; si no existe vale 0
        const $select = $tr.find("select");
        const maquina_id = $select.length ? $select.val() : 0;

        // 3) Observación (puede venir vacía)
        const observacion = $.trim($tr.find("textarea").val() || "");
        return { apetapaprod_id, nombre, maquina_id, observacion };
    }).get();   // -> array de objetos plano
    return etapas;
}