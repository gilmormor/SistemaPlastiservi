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
    /* console.log(
        'Se actualizó producto_id' + fila, 
        'con el texto:', texto
    ); */
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
                    <th class="ocultar">RUT</th>
                    <th>Razón Social</th>
                    <th title='Orden de Compra'>OC</th>
                    <th title='Nota Venta'>NV</th>
                    <th title='ID Producto'>ID Prod</th>
                    <th title='Nombre Producto'>Nombre Prod</th>
                    <th title='Unidad de Medida'>Uni Med</th>
                    <th title='Espesor acuerdo tecnico'>Espesor/<br>EspProd</th>
                    <th class="ocultar" title='Espesor Produccion'>Espesor Prod</th>
                    <th title='Cantidad' style="text-align:right;">Cant</th>
                    <th title='Cantidad ya enviada a programar' style="text-align:right;">CantEnvProg</th>
                    <th title='Kg Produccion' style="text-align:right;">KgProd</th>
                    <th title='Total Kg Programados' style="text-align:right;">KgProg</th>
                    <th title='Kg enviados a programación (disponible para programar)' style="text-align:right;">KgEnvProg</th>
                    <th title='Kilos' style="text-align:right;">Kg</th>
                    <th title='Observacion General'>ObsGen</th>
                    <th title='Observacion'>Obs</th>
                    <th title='Prioridad'>Prioridad</th>
                    <th>Acción</th>
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
        // scrollX/scrollY dan el encabezado fijo, pero parten la tabla en dos: una
        // para el encabezado y otra para el cuerpo. Para que no se desalineen, el
        // ancho de cada columna se declara UNA sola vez en columnDefs (abajo) y
        // DataTables lo aplica a ambas. No poner anchos a mano en los <th> ni en los
        // inputs: competirian con estos y volveria el desfase.
        'scrollX'     : true,
        'scrollY'     : 'calc(100vh - 320px)',
        'scrollCollapse': true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        // Ancho de cada columna, la unica fuente: DataTables lo replica en la tabla
        // del encabezado y en la del cuerpo, que es lo que las mantiene cuadradas.
        // Las columnas ocultas (3, 11 y 21 a 25) no llevan ancho: .ocultar las colapsa.
        'columnDefs'  : [
            { targets: 0 , width: '25px' },
            { targets: 1 , width: '50px' },
            { targets: 2 , width: '85px' },
            { targets: 4 , width: '190px' },
            { targets: 5 , width: '75px' },
            { targets: 6 , width: '60px' },
            { targets: 7 , width: '55px' },
            { targets: 8 , width: '160px' },
            { targets: 9 , width: '55px' },
            { targets: 10, width: '75px' },
            { targets: 12, width: '75px' },
            { targets: 13, width: '85px' },
            { targets: 14, width: '80px' },
            { targets: 15, width: '80px' },
            { targets: 16, width: '85px' },
            { targets: 17, width: '110px' },
            { targets: 18, width: '110px' },
            { targets: 19, width: '160px' },
            { targets: 20, width: '115px' },
            { targets: 21, width: '95px' },
        ],
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
            {data: 'rut',className:"ocultar"}, // 3
            {data: 'razonsocial'}, // 4
            {data: 'oc_id'}, // 5
            {data: 'notaventa_id'}, // 6
            {data: 'producto_id'}, // 7
            {data: 'producto_nombre'}, // 8
            {data: 'unidadmedida_nombre'}, // 9
            {data: 'at_espesor'}, // 10
            {data: 'espesorprod',className:"ocultar"}, // 11
            {data: 'cant'}, // 12
            {data: 'cantenvprog'}, // 13 — ya enviado a programar
            {data: 'kgprod'}, // 13
            {data: 'kgprog'}, // 14
            {data: 'kgenvprog'}, // 15 — kg enviados a programación
            {data: 'kgprod'}, // 16
            {data: 'otdet_obs'}, // 17
            {data: 'otdet_obs'}, // 18
            {data: 'otdet_obs'}, // 19
            {data: 'otdet_obs'}, // 20
            {data: 'clientebloqueado_descripcion',className:"ocultar"}, //21
            {data: 'oc_file',className:"ocultar"}, //22 — oc_folder
            {data: 'oc_file',className:"ocultar"}, //23 — oc_file
            {data: null, defaultContent:'', className:"ocultar"}, //24 — nombrepdf (sin dato en servidor)
            {data: 'updated_at',className:"ocultar"}, //25
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
            //console.log(data.detetapaprod_array);
            //"<a href='#' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + "</a>";

            aux_text = 
                `<a id="otdet_id${data.id}" name="otdet_id${data.id}" class="btn-accion-tabla btn-sm" title="Ver OT: ${data.ot_id}" onclick='genpdf(${data.ot_id},"","ver-pdf-ot","/ot/exportPdf/${data.ot_id}")'>
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
            $('td', row).eq(7).attr('name',"producto_id"+data.id);
            $('td', row).eq(7).attr('id',"producto_id"+data.id);
            $('td', row).eq(7).attr('file'+data.id,data.id);
            $('td', row).eq(7).attr('aux_obsext',aux_obsext);
            $('td', row).eq(7).attr('aux_obsimp',aux_obsimp);
            $('td', row).eq(7).attr('aux_obssell',aux_obssell);
            $('td', row).eq(7).html(aux_text);

            $('td', row).eq(9).attr('style','text-align:center');

            aux_text = MASKLA(data.at_espesor,3);
            aux_textespprod = MASKLA(data.espesorprod,3);
            $('td', row).eq(10).attr('style','text-align:right');
            $('td', row).eq(10).html(aux_text + '/<br>' + aux_textespprod);

            aux_text = MASKLA(data.espesorprod,3);
            $('td', row).eq(11).attr('style','text-align:right');
            $('td', row).eq(11).html(aux_text);


            aux_text = MASKLA(data.cant,0);
            $('td', row).eq(12).attr('style','text-align:right');
            $('td', row).eq(12).attr('title','Cantidad Produccion');
            $('td', row).eq(12).html(aux_text);

            // Col 13 — Cantidad ya enviada a programar (otdet.cantenvprog). Puede
            // venir nula en items que todavia no se enviaron; se muestra 0.
            aux_text = MASKLA(parseFloat(data.cantenvprog) || 0, 0);
            $('td', row).eq(13).attr('style','text-align:right');
            $('td', row).eq(13).attr('title','Cantidad ya enviada a programar');
            $('td', row).eq(13).html(aux_text);

            aux_text = MASKLA(data.kgprod,2);
            $('td', row).eq(14).attr('id','kgprodOrig' + data.id);
            $('td', row).eq(14).attr('name','kgprodOrig' + data.id);
            $('td', row).eq(14).attr('style','text-align:right');
            $('td', row).eq(14).attr('title','Kilos Produccion.');
            $('td', row).eq(14).attr('valor',data.kgprod);
            $('td', row).eq(14).html(aux_text);

            aux_text = MASKLA(data.kgprog,2);
            $('td', row).eq(15).attr('id','kgprog' + data.id);
            $('td', row).eq(15).attr('name','kgprog' + data.id);
            $('td', row).eq(15).attr('style','text-align:right');
            $('td', row).eq(15).attr('title','Kilos Programados.');
            $('td', row).eq(15).attr('valor',data.kgprog);
            $('td', row).eq(15).html(aux_text);

            // Col 15 — KgEnvProg (kg enviados a programación, disponible para programar)
            var aux_kgenvprog = parseFloat(data.kgenvprog) || 0;
            var aux_kgdisponible = aux_kgenvprog - parseFloat(data.kgprog || 0);
            aux_kgdisponible = (aux_kgdisponible < 0) ? 0 : aux_kgdisponible;
            $('td', row).eq(16).attr('style','text-align:right; font-weight:bold; color:#1a6b1a;');
            $('td', row).eq(16).attr('id','kgenvprog' + data.id);
            $('td', row).eq(16).attr('valor', aux_kgenvprog);
            $('td', row).eq(16).html(MASKLA(aux_kgenvprog, 2));

            // Col 16 — Input kg a programar (limitado a kgenvprog - kgprog)
            aux_kgprod = aux_kgdisponible;
            aux_text =
                `<input type="text" name="kgprod[]" id="kgprod${data.id}" class="form-control numerico requerido${data.id}" value="${MASKLA(aux_kgprod,2)}" valor="${aux_kgprod}" valorOriginal="${data.kgprod}" kgenvprog="${aux_kgenvprog}" item="${data.id}" style="width:100%;text-align:right;" maxlength="15"/>`;
            $('td', row).eq(17).html(aux_text);


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

            // Col 19 — Select Prioridad
            var aux_text = `<select class="form-control requerido${data.id}" name="prioridad${data.id}" id="prioridad${data.id}" data-width="100%" data-size="5">`;
            aux_text += `<option value="">...</option>`;
            for (let i = 1; i < 4; i++) {
                aux_text += `<option value="${i}">${i}</option>`;
            }
            aux_text += `</select>`;
            $('td', row).eq(20).html(aux_text);

            // Col 18 — Textarea Observación
            aux_text =
                `<textarea name="obs[]" id="obs${data.id}" class="form-control" value="" item="${data.id}" style="width:100%;" placeholder="Observación" maxlength="100"></textarea>`;
            $('td', row).eq(19).html(aux_text);

            // Col 20 — Botones acción
            aux_text =
                `<div class="tools11">
                    <a ${aux_displaybtnbl} class="btn-accion-tabla botonbloq${data.id}" title="Condición financiera en revisión: ${aux_clienteBloqueado}" onclick="llenartablaDataCobranza(${data.id},${data.cliente_id},0,0)">
                        <i class="fa fa-fw fa-lock text-danger fa-lg"></i>
                    </a>
                    <a ${aux_displaybtnac} id="bntaprobnv${data.id}" name="bntaprobnv${data.id}" class="btn-accion-tabla btn-sm action-buttons botonac${data.id}" onclick="procesarRegOt(${data.id},${data.updatednum_at},${data.otupdatednum_at})" title="Enviar a Producción">
                        <i class="fa fa-fw fa-save fa-lg"></i>
                    </a>
                    <a id="bntcerrarprog${data.id}" name="bntcerrarprog${data.id}" class="btn-accion-tabla btn-sm action-buttons botonac${data.id}" onclick="cerrarOpDet(${data.id},${data.updatednum_at},${data.otupdatednum_at})" title="Cerrar programación">
                        <i class="fa fa-fw fa-close text-danger fa-lg"></i>
                    </a>
                </div>`;
            $('td', row).eq(21).html(aux_text);

            $('td', row).eq(25).addClass('updated_at');
            $('td', row).eq(25).attr('id','updated_at' + data.id);
            $('td', row).eq(25).attr('name','updated_at' + data.id);

            activarLimpiezaCampoRequerido(data.id);


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
    //console.log(d);
    let tablaHtml = `
    <div style="display: flex; align-items: flex-start;"> <!-- Contenedor Flex (flecha al principio) -->
        <div style="margin-left: 20px;">&#8627;</div> <!-- Flecha desplazada un poco a la derecha -->
            <table id='etapaprod${d.id}' name='etapaprod${d.id}' class='table display AllDataTables table-hover table-condensed tablascons'>
                <thead>
                    <tr>
                    <th>Cod</th>
                    <th>Etapa Produccion</th>
                    <th>Maquina</th>
                    <th>Observacion</th>
                    </tr>
                </thead>
                <tbody>`;
                if (d.detetapaprod_array && d.detetapaprod_array.trim() !== "") {
                    d.detetapaprod_array.split(";").forEach(registro => {
                        const [apsucetapaprod_id, etapaprod_id, etapaprod_nombre] = registro.split("|");
                        let aux_idep = `${d.id}-${apsucetapaprod_id}`;
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
                                <td id="epid${aux_idep}" name="epid${aux_idep}" apsucetapaprod_id="${apsucetapaprod_id}">${apsucetapaprod_id}</td>
                                <td id="epnombre${aux_idep}" name="epnombre${aux_idep}">${etapaprod_nombre}</td>
                                <td>${aux_text}</td>
                                <td><textarea id="epobs${aux_idep}" name="epobs${aux_idep}" class="form-control edit-obs" style="width: 220px;min-width:220px;" placeholder="Observación" maxlength="100" fila="${d.id}" nameobsg="aux_obsext">${aux_obsext}</textarea></td>
                            </tr>`;
                        //$('.selectpicker').selectpicker();
                    });
                } else {
                    // ❌ No hay etapas → tabla vacía (el botón agregará filas)
                    tablaHtml += `
                        <tr id="fila-vacia-${d.id}">
                            <td colspan="4" class="text-center text-muted">Sin etapas de producción</td>
                        </tr>`;
                }
    aux_updated_at = $("#fila" + d.id).attr('updated_at');
    aux_botoneditarep = ``;
    if($("#aux_et").val() == 1){
        // Se pasa también d.id (otdet_id) para que al guardar se sepa qué fila actualizar
        aux_botoneditarep = `
                <button type="button" class="btn btn-sm btn-primary mt-2"
                    onclick="editaretapasprod(${d.acuerdotecnico_id}, ${d.id})" title="Editar etapas de producción">
                    + Editar etapas
                </button>
            `;
    }
    tablaHtml += `
                </tbody>
            </table>
                ${aux_botoneditarep}
        </div>
    </div>`;
    activarLimpiezaCampoRequerido(d.id);
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

function agregarFila(idTabla) {
    const tbody = document.querySelector(`#etapaprod${idTabla} tbody`);
    if (!tbody) return;

    // Si existe alguna fila 'nueva' sin guardar, no permitir agregar otra
    const existeSinGuardar = tbody.querySelector('tr[data-new]:not([data-guardado])');
    if (existeSinGuardar) {
        swal({
            text: "Debe guardar la última etapa antes de agregar una nueva.",
            icon: 'error',
            buttons: { confirm: "Aceptar" }
        }).then(() => {});
        return;
    }

    // quitar mensaje "sin etapas" si existe
    const filaVacia = document.querySelector(`#fila-vacia-${idTabla}`);
    if (filaVacia) filaVacia.remove();

    // id único para la fila nueva
    let aux_idep = `${idTabla}-new${Date.now()}`;

    //ajaxRequest(datatemp,ruta,'etapasfaltantesprod');

    let nuevaFila = `
        <tr id="fila-${aux_idep}" data-new="true">
            <td>
                <input type="text" id="epid${aux_idep}" name="epid${aux_idep}" 
                       class="form-control" placeholder="Cod etapa">
            </td>
            <td id="epnombre${aux_idep}" name="epnombre${aux_idep}"></td>
            <td></td>
            <td>
                <textarea id="epobs${aux_idep}" name="epobs${aux_idep}" 
                          class="form-control edit-obs" style="width: 250px;" 
                          placeholder="Observación" maxlength="100" 
                          fila="${idTabla}" nameobsg="aux_obsext"></textarea>
            </td>
            <td>
                <button type="button" class="btn btn-success btn-sm" onclick="guardarFila('${aux_idep}')">💾 Guardar</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="cancelarFila('${aux_idep}', ${idTabla})">✖ Cancelar</button>
            </td>
        </tr>
    `;
    tbody.insertAdjacentHTML("beforeend", nuevaFila);
}

// -- guardarFila(aux_idep) (valida y marca la fila como guardada)
function guardarFila(aux_idep) {
    const inputId = document.querySelector(`#epid${aux_idep}`);
    const textareaObs = document.querySelector(`#epobs${aux_idep}`);
    const fila = document.querySelector(`#fila-${aux_idep}`);
    if (!fila) {
        swal({ text: "Fila no encontrada.", icon: 'error', buttons: { confirm: "Aceptar" } }).then(()=>{});
        return;
    }

    if (!inputId || !inputId.value.trim()) {
        swal({
            text: "Debe ingresar el código de la etapa (Columna 1).",
            icon: 'error',
            buttons: { confirm: "Aceptar" }
        }).then(() => { if (inputId) inputId.focus(); });
        return;
    }

    // Aquí puedes enviar la data al servidor vía AJAX/fetch. Ejemplo comentado:
    // fetch('/ruta/guardar-etapa', { method: 'POST', body: JSON.stringify({ etapaprod_id: inputId.value, observacion: textareaObs.value, ... }) })
    //   .then(res => res.json()).then(resp => { /* comprobar éxito */ })

    // --- Simulamos guardado exitoso en frontend ---
    // 1) transformar el input en texto en la celda de código
    const celdaCod = inputId.closest('td');
    const codigo = inputId.value.trim();
    celdaCod.innerHTML = codigo;

    // 2) marcar observación como readonly
    if (textareaObs) {
        textareaObs.setAttribute('readonly', 'true');
        textareaObs.classList.add('text-muted');
    }

    // 3) marcar fila como guardada y quitar data-new (opcional)
    fila.dataset.guardado = "true";
    fila.removeAttribute('data-new');

    // 4) deshabilitar botones de acción
    const btnGuardar = fila.querySelector('button.btn-success');
    const btnCancelar = fila.querySelector('button.btn-secondary');
    if (btnGuardar) btnGuardar.disabled = true;
    if (btnCancelar) btnCancelar.disabled = true;

    // 5) si tienes un catálogo para autocompletar nombre de etapa, aquí podrías buscar y llenar epnombre...
    // por ejemplo: document.querySelector(`#epnombre${aux_idep}`).innerText = buscarNombrePorId(codigo) || "";

    swal({
        text: "Etapa guardada correctamente ✅",
        icon: 'success',
        buttons: { confirm: "Aceptar" }
    }).then(() => {});
}

// -- cancelarFila(aux_idep, idTabla) (quita la fila nueva)
function cancelarFila(aux_idep, idTabla) {
    const fila = document.querySelector(`#fila-${aux_idep}`);
    if (fila) fila.remove();

    const tbody = document.querySelector(`#etapaprod${idTabla} tbody`);
    if (tbody && tbody.querySelectorAll('tr').length === 0) {
        // volver a mostrar el mensaje "Sin etapas"
        tbody.insertAdjacentHTML('beforeend', `<tr id="fila-vacia-${idTabla}"><td colspan="5" class="text-center text-muted">Sin etapas de producción</td></tr>`);
    }
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
	let filaId = id; // O dinámico según el contexto
	let row = tabla.row('#fila' + filaId);

	if (!row.child.isShown()) {
		row.child(format(row.data())).show();

		// Opcional: cambia también el ícono de la celda td.dt-control si quieres
		$('#fila' + filaId).find('td.dt-control').html(
			'<i class="btn-accion-tabla btn-sm glyphicon glyphicon-triangle-bottom text-aqua" title="Mostrar Detalle"></i>'
		);
	}
    // Validar que la tabla de etapas de produccion tenga al menos un registro real.
    // Se excluye fila-vacia-{id} que es el placeholder "Sin etapas de producción".
    if ($('#etapaprod' + id + ' tbody tr').not('#fila-vacia-' + id).length === 0) {
        swal({
            title: `Falta incluir Informacion!`,
            text: "Debe asignar etapas de produccion.",
            buttons: {
                cancel: "Cancelar"
            },
            icon: 'warning',
        });
        return 0;
    }

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
            kgprodOriginal : $("#kgprod" + id).attr("valorOriginal"),
            kgprog      : $("#kgprog" + id).attr("valor"),
            statusCerrar_otDet : 0,
            /* extrusora : $("#extrusora" + id).val(),
            impresora : $("#impresora" + id).val(),
            selladora : $("#selladora" + id).val(),
            obsext: $("#producto_id" + id).attr("aux_obsext"),
            obsimp: $("#producto_id" + id).attr("aux_obsimp"),
            obssell: $("#producto_id" + id).attr("aux_obssell"), */
            prioridad : $("#prioridad" + id).val(),
            EtapasProduccion : aux_apetapaprod,
            _token: $('input[name=_token]').val()
        };
        //return recogerEtapas(data);
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
                buttons: { cancel: "Cancelar" },
                icon: 'warning',
            });
            return 0;
        }
        // Validar que no supere los kg disponibles para programar (kgenvprog - kgprog)
        var aux_kgenvprog  = parseFloat($("#kgprod" + id).attr("kgenvprog") || 0);
        var aux_kgprogAcum = parseFloat($("#kgprog" + id).attr("valor") || 0);
        var aux_kgDisponible = aux_kgenvprog - aux_kgprogAcum;
        if(data.kgprod > aux_kgDisponible + 0.001){
            divPadre.addClass("error");
            swal({
                title: `Cantidad supera lo disponible`,
                text: `Kg a programar (${MASKLA(data.kgprod,2)}) supera el saldo disponible (${MASKLA(aux_kgDisponible,2)} kg).\nKg enviados a prog.: ${MASKLA(aux_kgenvprog,2)} — Kg ya programados: ${MASKLA(aux_kgprogAcum,2)}`,
                buttons: { cancel: "Cancelar" },
                icon: 'error',
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

        data.kgprog = parseFloat(data.kgprog);
        data.kgprod = parseFloat(data.kgprod);
        data.kgprodOriginal = parseFloat(data.kgprodOriginal);
        //console.log(((data.kgprog + data.kgprod) >= data.kgprodOriginal));
        /* if((data.kgprog + data.kgprod) >= data.kgprodOriginal){
            swal({
                title: `¿Cerrar OtDet ${id} !?`,
                text: `Valor de Kilos Programados + Kilos Producción es mayor o igual a Kilos Producción Original.`,
                buttons: {
                    cancel: "Cancelar",
                    confirm: "Aceptar"
                },
                icon: 'warning',
            }).then((value) => {
                if (value) {
                    // Obtener el valor de la caja de texto de observaciones
                    //ajaxRequestGeneral(data, ruta, 'procesarDTE');
                }
            });
        }
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
        }); */
        let mostrarPrimerSwal = (data.kgprog + data.kgprod) >= data.kgprodOriginal;

        let promesa;

        if (mostrarPrimerSwal) {
            promesa = swal({
                title: `¿Cerrar OtDet ${id}?`,
                text: `Valor de Kilos Programados + Kilos Producción es mayor o igual a Kilos Producción Original.`,
                buttons: {
                    cancel: "Cancelar",
                    confirm: "Aceptar"
                },
                icon: 'warning',
            }).then((value) => {
                if (value) {
                    data.statusCerrar_otDet = 1;
                }
        });
        } else {
            // Si no se muestra el primer swal, crea una promesa resuelta inmediatamente
            promesa = Promise.resolve();
        }

        promesa.then(() => {
            // Este swal siempre se ejecuta
            return swal({
                title: `¿${mensaje}?`,
                text: "Esta acción no se puede deshacer!",
                buttons: {
                    cancel: "Cancelar",
                    confirm: "Aceptar"
                },
                icon: 'warning',
            });
        }).then((value) => {
            if (value) {
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
        //return 0;
    }


}

function cerrarOpDet(id,updatednum_at,otupdatednum_at) {
	let filaId = id; // O dinámico según el contexto
	let row = tabla.row('#fila' + filaId);

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
        kgprodOriginal : $("#kgprod" + id).attr("valorOriginal"),
        kgprog      : $("#kgprog" + id).attr("valor"),
        statusCerrar_otDet : 1,
        _token: $('input[name=_token]').val()
    };
    //return recogerEtapas(data);
    ruta = '/otitemprogramacion/cerraropdet';
    mensaje = 'Cerrar OtDet';


    /* return swal({
        title: `¿${mensaje}?`,
        text: "Esta acción no se puede deshacer!",
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
        icon: 'warning',
    }).then((value) => {
        if (value) {
            ajaxRequestGeneral(data, ruta, 'procesarDTE');
        }
    }); */

    return swal({
        title: `¿${mensaje}?`,
        text: "Esta acción no se puede deshacer!",
        icon: 'warning',
        content: {
            element: "input",
            attributes: {
                placeholder: "Escribe un motivo...",
                type: "text",
            },
        },
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
        if (value === null) return;

        if (!value.trim()) {
            swal("Error", "Debes ingresar un texto", "error");
            return;
        }

        data.obs = value;
        ajaxRequestGeneral(data, ruta, 'procesarDTE');
    });
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

	if ($('#etapaprod' + id).length === 0) {
		swal({
			title: `Falta incluir Información`,
			text: "Mariquito.",
			icon: 'warning',
			buttons: { cancel: "Cancelar" }
		});
		return false;
	}

	$('.requerido' + id).each(function () {
		let $el = $(this);
		let valor = $.trim($el.val());
		let tipo = $el.prop('tagName').toLowerCase();

		// Limpia errores previos
		$el.removeClass('error-input');
		$el.closest('.bootstrap-select').removeClass('has-error');
		$el.next('.error-msg').remove();

		let campoInvalido = (
			valor === '' || valor === '0,00' || valor === '0.00' || valor === '0' ||
			(tipo === 'select' && $el.prop('selectedIndex') === 0)
		);

		if (campoInvalido) {
			esValido = false;

			// Aplica estilo visible al componente visual (selectpicker o normal)
			if ($el.hasClass('selectpicker')) {
				$el.closest('.bootstrap-select').addClass('has-error');
				if ($el.closest('.bootstrap-select').next('.error-msg').length === 0) {
					$el.closest('.bootstrap-select').after('<span class="error-msg">Este campo es obligatorio</span>');
				}
			} else {
				$el.addClass('error-input');
				$el.after('<span class="error-msg">Este campo es obligatorio</span>');
			}
		}
	});

	return esValido;
}

function activarLimpiezaCampoRequerido(idFila) {
	// Selecciona solo campos de esa fila
	$(document).on('input change', '.requerido' + idFila, function () {
		let $el = $(this);
		let valor = $.trim($el.val());
		let tipo = $el.prop('tagName').toLowerCase();

		let campoValido = (
			valor !== '' && valor !== '0' && valor !== '0.00' && valor !== '0,00' &&
			(tipo !== 'select' || $el.prop('selectedIndex') !== 0)
		);

		if (campoValido) {
			$el.removeClass('error-input');
			$el.next('.error-msg').remove();

			//if ($el.hasClass('selectpicker')) {
				$el.closest('.bootstrap-select').removeClass('has-error');
				$el.closest('.bootstrap-select').next('.error-msg').remove();
			//}
		}
	});
}

function recogerEtapas(idFila) {
    // idFila = 120 en tu ejemplo → #etapaprod120
    const etapas = $("#etapaprod" + idFila + " tbody tr").map(function () {
        const $tr = $(this);

        // 1) Código y nombre están en las dos primeras celdas
        const apsucetapaprod_id     = $.trim($tr.find("td:eq(0)").text());   // «1», «2», «3»…
        //const apsucetapaprod_id     = $.trim($tr.find("td:eq(0)").attr("apsucetapaprod_id"));   // «1», «2», «3»…
        const nombre  = $.trim($tr.find("td:eq(1)").text());   // «Programacion», «Mezclas»…

        // 2) Si hay <select>, tomamos su valor; si no existe vale 0
        const $select = $tr.find("select");
        const maquina_id = $select.length ? $select.val() : 0;

        // 3) Observación (puede venir vacía)
        const observacion = $.trim($tr.find("textarea").val() || "");
        return { apsucetapaprod_id, nombre, maquina_id, observacion };
    }).get();   // -> array de objetos plano
    return etapas;
}

// Variables de estado del modal (accesibles desde el handler del botón Guardar)
var _etapasAtId    = null; // acuerdotecnico_id activo en el modal
var _etapasOtdetId = null; // otdet_id que originó la apertura (para refrescar la fila)

/**
 * editaretapasprod — abre el modal de asignación de etapas.
 *
 * Hace GET a /acuerdotecnicoetapaprod/{id}/modal-data, que devuelve:
 *   - etapaprods: todas las etapas disponibles para el producto
 *   - seleccionadas: IDs de las ya asignadas (para pre-marcar los checkboxes)
 *
 * @param {number} acuerdotecnico_id  - ID del acuerdo técnico del producto
 * @param {number} otdet_id           - ID del otdet que inició la acción (para refrescar su fila)
 */
function editaretapasprod(acuerdotecnico_id, otdet_id) {
    _etapasAtId    = acuerdotecnico_id;
    _etapasOtdetId = otdet_id;

    // Limpiar contenido previo y mostrar cargando
    $('#modalEtapasProdBody').html('<p class="text-muted text-center"><i class="fa fa-spinner fa-spin"></i> Cargando etapas...</p>');
    $('#modalEtapasProdInfo').text('');
    $('#modalEtapasProd').modal('show');

    $.ajax({
        url  : '/acuerdotecnicoetapaprod/' + acuerdotecnico_id + '/modal-data',
        type : 'GET',
        success: function (resp) {
            // Info del producto en el encabezado del modal
            $('#modalEtapasProdInfo').html(
                '<strong>Producto:</strong> ' + resp.nombre_producto +
                ' &nbsp;|&nbsp; <strong>ID Prod:</strong> ' + resp.producto_id +
                ' &nbsp;|&nbsp; <strong>AT:</strong> ' + resp.at_id
            );

            if (resp.etapaprods.length === 0) {
                $('#modalEtapasProdBody').html(
                    '<p class="text-warning">No hay etapas de producción configuradas para este producto.</p>'
                );
                return;
            }

            // Construir select multiple tipo selectpicker (Bootstrap Select),
            // igual al usado en los filtros de esta misma pantalla.
            // data-actions-box añade los botones "Seleccionar todo / Ninguno".
            var htmlBody = '<label>Etapas de Producción</label>' +
                           '<select id="selectEtapasProd" ' +
                                   'class="selectpicker form-control" ' +
                                   'multiple ' +
                                   'data-live-search="true" ' +
                                   'data-actions-box="true" ' +
                                   'title="Seleccione las etapas...">' ;

            resp.etapaprods.forEach(function (e) {
                // pre-seleccionar las etapas que el producto ya tiene asignadas
                var selected = (resp.seleccionadas.indexOf(e.areaproduccionsucetapaprod_id) !== -1)
                               ? 'selected' : '';
                htmlBody += '<option value="' + e.areaproduccionsucetapaprod_id + '" ' + selected + '>'
                          + e.etapaprod_nombre + ' — ' + e.sucursal_nombre
                          + '</option>';
            });

            htmlBody += '</select>';

            $('#modalEtapasProdBody').html(htmlBody);

            // Inicializar el selectpicker después de inyectar el HTML en el DOM
            //$('#selectEtapasProd').selectpicker();
            $("#selectEtapasProd").selectpicker({
                noneSelectedText : "Seleccione...", // by this default "Nothing selected" -->will change to Please Select
                selectAllText : "Seleccionar todo",
                deselectAllText : "Borrar todo",
                noneResultsText: "No se encontraron resultados para {0}", // Texto cuando no hay coincidencias
                });

        },
        error: function () {
            $('#modalEtapasProdBody').html(
                '<p class="text-danger">Error al cargar las etapas. Intente nuevamente.</p>'
            );
        }
    });
}

/**
 * Handler del botón "Guardar etapas" del modal.
 *
 * Recoge los checkboxes marcados, hace POST a /actualizar-ajax,
 * y en éxito actualiza en memoria el detetapaprod_array de TODAS las filas
 * del DataTable que compartan el mismo acuerdotecnico_id (puede haber varias OTs
 * del mismo producto). Si la fila tiene el detalle expandido, lo re-renderiza.
 */
$('#btnGuardarEtapas').click(function () {
    // Recoger los IDs de las opciones seleccionadas en el select multiple
    var ids = $('#selectEtapasProd').val() || [];

    // Deshabilitar el botón mientras se guarda para evitar doble clic
    var $btn = $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

    $.ajax({
        url  : '/acuerdotecnicoetapaprod/' + _etapasAtId + '/actualizar-ajax',
        type : 'POST',
        data : { apsucetapaprod_id: ids, _token: $('input[name=_token]').val() },
        success: function (resp) {
            $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Guardar etapas');

            if (resp.resp !== 1) {
                Biblioteca.notificaciones(resp.mensaje, 'Plastiservi', 'error');
                return;
            }

            $('#modalEtapasProd').modal('hide');
            Biblioteca.notificaciones(resp.mensaje, 'Plastiservi', 'success');

            // Actualizar en memoria TODAS las filas con el mismo acuerdotecnico_id.
            // Puede haber múltiples ítems de OTs distintas para el mismo producto.
            tabla.rows().every(function () {
                var rowData = this.data();
                if (rowData.acuerdotecnico_id != _etapasAtId) return;

                // rowData es referencia al objeto interno de DataTables: modificarlo
                // directamente actualiza el dato sin re-renderizar la fila (que destruiría
                // elementos personalizados como prioridad{id} y bntcerrarprog{id}).
                rowData.detetapaprod_array = resp.detetapaprod_array;

                // Si el detalle está expandido → re-renderizarlo con los datos nuevos
                if (this.child.isShown()) {
                    this.child(format(rowData)).show();
                }
            });
        },
        error: function () {
            $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Guardar etapas');
            Biblioteca.notificaciones('Error de conexión al guardar.', 'Plastiservi', 'error');
        }
    });
});
