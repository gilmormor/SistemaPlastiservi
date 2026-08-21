$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $('#tabla-data').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : RUTA_AJAX,
		'order'       : [[ 0, "desc" ]],
        'columns'     : [
            {data: 'id'},
			{data: 'ot_id'},
            {data: 'producto_id'},
            {data: 'producto_nombre'},
            {data: 'razonsocial'},
            {data: 'opdet_kgrec'},
            {data: 'opdet_kgprod'},
            {data: 'opdet_kgscrap'},
            {data: 'kgprod'},
            {data: 'kgscrap'},
            {data: 'kgsaldo'},
            {data: 'updatednum_at',className:"ocultar"},
            {data: 'maquina_nombre'},
            {data: 'operario_nombre'},
            {defaultContent : ``},
            {defaultContent : ``
            }
        ],
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            $(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);
            $(row).attr('updated_at', data.updatednum_at);

            // R1: resaltar filas de muestra física para CC
            if (data.es_muestra == 1) {
                $(row).css('background', '#fff3e0');
                var idCell = $('td', row).eq(0);
                idCell.html(idCell.text()
                    + '<br><span class="label" style="background:#e65100;color:#fff;font-size:10px;padding:2px 5px;">'
                    + '<i class="fa fa-flask"></i> MUESTRA CC</span>');
            }

            /* aux_text = 
                `<a id="otdet_id${data.id}" name="otdet_id${data.id}" class="btn-accion-tabla btn-sm" title="Ver OT: ${data.ot_id}" onclick='genpdf(${data.ot_id},"","ver-pdf-ot","/ot/exportPdf/${data.ot_id}")'>
                    ${data.ot_id}
                </a>
                <a id="otdet_id${data.id}" name="otdet_id${data.id}" class="btn-accion-tabla btn-sm" title="Ver OP: ${data.op_id}" onclick='genpdf(${data.op_id},"","ver-pdf-op","/op/exportPdf/${data.op_id}")'>
                    ${data.op_id}-${data.opdet_id}
                </a>`; */

			aux_textNV = "";
			if(data.notaventa_id){
				aux_textNV = `<a class="btn-accion-tabla btn-sm tooltipsC" title="Nota Venta" onclick="genpdfNV(${data.notaventa_id},1)" style="padding-left: 0px;">
					${data.notaventa_id}
				</a>`;
			}
			aux_text = aux_textNV + `
				<a class="btn-accion-tabla btn-sm tooltipsC" title="Orden Trabajo" onclick='genpdf(${data.ot_id},"","ver-pdf-ot","/ot/exportPdf/${data.ot_id}")' style="padding-left: 0px;">
					${data.ot_id}
				</a>
				<a class="btn-accion-tabla btn-sm tooltipsC" title="Orden Trabajo Detalle" onclick='genpdf(${data.ot_id},"","ver-pdf-ot","/ot/exportPdf/${data.ot_id}")' style="padding-left: 0px;">
					${data.otdet_id}
				</a>
				<a class="btn-accion-tabla btn-sm tooltipsC" title="Orden Produccion" ${data.op_id}" onclick='genpdf(${data.op_id},"","ver-pdf-op","/op/exportPdf/${data.op_id}")' style="padding-left: 0px;">
					${data.op_id}
				</a>
				<a class="btn-accion-tabla btn-sm tooltipsC" title="Orden Produccion Detalle ${data.opdet_id}" onclick='genpdf(${data.op_id},"","ver-pdf-op","/op/exportPdf/${data.op_id}")' style="padding-left: 0px;">
					${data.id}
				</a>`
            $('td', row).eq(1).html(aux_text);

			if(data.acuerdotecnico_id != null){
				//$('td', row).eq(0).html(aux_text);
				if (data.hasOwnProperty('cliente_id')) {
					aux_cliente_id = data.cliente_id;
				} else {
					aux_cliente_id = 0;
				}
				aux_text = 
				`<a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="Acuerdo Técnico" onclick='genpdfAcuTec(${data.acuerdotecnico_id},${aux_cliente_id},"")'>
					${data.producto_id}
				</a>`;
				if(data.at_impresofoto != "" && data.at_impresofoto != null){
					aux_text += 
						`<a class="btn-accion-tabla btn-sm tooltipsC" title="Ver Imagen" onclick='verpdf2(\"at/${data.at_impresofoto}\",2,"","ver-arte-acuerdo-tecnico")'>
							<i class="fa fa-fw fa-photo"></i>
						</a>`;
				}
				$('td', row).eq(2).html(aux_text);
				//$('td', row).eq(0).attr('onClick', 'genpdfAcuTec(' + data.acuerdotecnico_id + ',' + aux_cliente_id +',"");');
			}

            $('td', row).eq(5).attr('style','text-align:right');
            $('td', row).eq(5).attr('data-order',data.opdet_kgrec);
            $('td', row).eq(5).attr('data-search',data.opdet_kgrec);
            $('td', row).eq(5).html(MASKLA(data.opdet_kgrec, 2));

            $('td', row).eq(6).attr('style','text-align:right');
            $('td', row).eq(6).attr('data-order',data.opdet_kgprod);
            $('td', row).eq(6).attr('data-search',data.opdet_kgprod);
            $('td', row).eq(6).html(MASKLA(data.opdet_kgprod, 2));

            $('td', row).eq(7).attr('style','text-align:right');
            $('td', row).eq(7).attr('data-order',data.opdet_kgscrap);
            $('td', row).eq(7).attr('data-search',data.opdet_kgscrap);
            $('td', row).eq(7).html(MASKLA(data.opdet_kgscrap, 2));

            $('td', row).eq(8).attr('style','text-align:right');
            $('td', row).eq(8).attr('data-order',data.kgprod);
            $('td', row).eq(8).attr('data-search',data.kgprod);
            $('td', row).eq(8).html(MASKLA(data.kgprod, 2));

            $('td', row).eq(9).attr('style','text-align:right');
            $('td', row).eq(9).attr('data-order',data.kgscrap);
            $('td', row).eq(9).attr('data-search',data.kgscrap);
            $('td', row).eq(9).html(MASKLA(data.kgscrap, 2));

            $('td', row).eq(10).attr('style','text-align:right');
            $('td', row).eq(10).attr('data-order',data.kgsaldo);
            $('td', row).eq(10).attr('data-search',data.kgsaldo);
            $('td', row).eq(10).html(MASKLA(data.kgsaldo, 2));

            $('td', row).eq(11).attr('updated_at',data.updatednum_at);
            $('td', row).eq(11).addClass('updated_at');

            // Columnas 12-13: Maquina y Operario (se renderizan automaticamente por datatables)

            // Columna 14: Estado del cierre de la UM de salida
            const cantprodNum = parseFloat(data.cantprod) || 0;
            const umSalNombre = data.unidadmedidasal_nombre ? data.unidadmedidasal_nombre : 'UM salida';
            if (data.rollo_cerrado == 1 || cantprodNum > 0) {
                $('td', row).eq(14).html(
                    `<span class="label" style="background-color:#5cb85c;color:#fff;padding:4px 8px;font-size:11px;" title="Este registro cerro ${cantprodNum} ${umSalNombre}(s) de salida de la etapa">✔ ${umSalNombre} Cerrado: ${cantprodNum}</span>`
                );
            } else {
                $('td', row).eq(14).html(
                    `<span class="label" style="background-color:#f0ad4e;color:#fff;padding:4px 8px;font-size:11px;" title="Registro parcial: kg procesados que aun no completan un(a) ${umSalNombre} de salida de la etapa">⚠ ${umSalNombre} Abierto (parcial)</span>`
                );
            }

            // Columna 15: puede_aprobar / puede_rechazar llegan como 0/1 desde SQL
            const pAprob = (data.puede_aprobar == 1 || data.puede_aprobar === true) ? 1 : 0;
            const pRech  = (data.puede_rechazar == 1 || data.puede_rechazar === true) ? 1 : 0;
            aux_text = `<a class='btn-accion-tabla btn-sm tooltipsC' title='Aprobar / Rechazar Produccion' onclick='aprobrecproducc(${data.id},${data.updatednum_at},${pAprob},${pRech})'>
                            <span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>
                        </a>`;
            $('td', row).eq(15).html(aux_text);
        }
      });

});

function aprobrecproducc(id,updatednum_at,puedeAprobar,puedeRechazar){
	quitarvalidacioneach();
	$("#id").val(id);
	$("#aprobobs").val("");

	// Resetear el select de bodega (oculto hasta que se carguen datos)
	$('#divBodegaAprobSup').hide();
	$('#invbodega_id_aprob').html('<option value="">-- Cargando... --</option>');

	// Aplicar reglas de orden ASC/DESC sobre los botones del modal.
	// Por defecto ambos botones visibles/habilitados.
	$("#btnaprobarM").prop('disabled', false).show();
	$("#btnrechazarM").prop('disabled', false).show();
	$("#avisoOrden").remove();

	let avisos = [];
	if (puedeAprobar === 0) {
		$("#btnaprobarM").prop('disabled', true);
		avisos.push("No se puede aprobar: existe un registro anterior pendiente del mismo OpDet. Apruebe primero el anterior.");
	}
	if (puedeRechazar === 0) {
		$("#btnrechazarM").prop('disabled', true);
		avisos.push("No se puede rechazar: existe un registro posterior (pendiente o aprobado) del mismo OpDet. Rechace primero el posterior.");
	}
	if (avisos.length > 0) {
		const html = `<div id="avisoOrden" class="alert alert-warning" style="margin:10px 0;font-size:12px;"><strong>Atencion:</strong><br>${avisos.join('<br>')}</div>`;
		$("#aprobobs").before(html);
	}

	// Cargar bodegas configuradas para la etapa de este registro
	$.get('/opdetregprodtempaprobsup/' + id + '/bodegas', function(resp) {
		if (resp.count === 1) {
			// Una sola bodega: preseleccionar silenciosamente sin mostrar el select
			$('#invbodega_id_aprob')
				.html('<option value="' + resp.bodegas[0].id + '">' + resp.bodegas[0].nombre + '</option>')
				.val(resp.bodegas[0].id);
		} else if (resp.count > 1) {
			// Múltiples bodegas: mostrar select para que el usuario elija
			var opts = '<option value="">-- Seleccione bodega --</option>';
			resp.bodegas.forEach(function(b) {
				opts += '<option value="' + b.id + '">' + b.nombre + '</option>';
			});
			$('#invbodega_id_aprob').html(opts);
			$('#divBodegaAprobSup').show();
		} else {
			// Sin bodegas configuradas: limpiar select
			$('#invbodega_id_aprob').html('');
		}
	}).fail(function() {
		$('#invbodega_id_aprob').html('');
	});

	$("#myModalaprobcot").modal('show');
}


$("#btnaprobarM").click(function(event){
	// Validar bodega si el select está visible (múltiples bodegas)
	var invbodega_id_val = $('#invbodega_id_aprob').val();
	if ($('#divBodegaAprobSup').is(':visible') && !invbodega_id_val) {
		alertify.error('Debe seleccionar la bodega de producción destino.');
		return;
	}

	var data = {
		id       : $("#id").val(),
		staaprob : 2,
		obsaprob : $("#aprobobs").val(),
        updated_at : $("#fila" + $("#id").val()).attr("updated_at"),
        _token: $('input[name=_token]').val()
	};
	// Incluir bodega si está disponible (1 bodega auto-seleccionada o elegida por el usuario)
	if (invbodega_id_val) {
		data['invbodega_id'] = invbodega_id_val;
	}
	var ruta = '/opdetregprodtempaprobsup/aprob/'+data['id'];
	swal({
		title: '¿ Seguro desea Aprobar ?',
		text: "Esta acción no se puede deshacer!",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			ajaxRequest(data,ruta,'aprobarproducc');
		}
	});

});

$("#btnrechazarM").click(function(event){
	if(verificarAproRech()){
		var data = {
			id       : $("#id").val(),
			staaprob : 3,
			obsaprob : $("#aprobobs").val(),
            updated_at : $("#fila" + $("#id").val()).attr("updated_at"),
			_token: $('input[name=_token]').val()
		};
		var ruta = '/opdetregprodtempaprobsup/aprob/'+data['id'];
		swal({
			title: '¿ Seguro desea Rechazar ?',
			text: "Esta acción no se puede deshacer!",
			icon: 'warning',
			buttons: {
				cancel: "Cancelar",
				confirm: "Aceptar"
			},
		}).then((value) => {
			if (value) {
				ajaxRequest(data,ruta,'aprobarproducc');
			}
		});
	}else{
		alertify.error("Falta incluir informacion");
	}
});

// Datos de la última petición de aprobación (para reenviar con bodega seleccionada)
var _lastAprobData = null;
var _lastAprobUrl  = null;

function ajaxRequest(data,url,funcion) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='aprobarproducc'){
				if(respuesta.resp == 2){
					// Fallback: el servidor solicitó bodega pero no llegó en el request.
					// Cerrar el modal Bootstrap antes de mostrar el swal para evitar que quede oculto.
					_lastAprobData = data;
					_lastAprobUrl  = url;
					$("#myModalaprobcot").modal('hide');
					setTimeout(function(){
						mostrarModalBodega(respuesta.bodegas);
					}, 400);
					return;
				}

				$("#aprobobs").val("");
				$("#myModalaprobcot").modal('hide');
				Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipmen);
				if(respuesta.resp == 1){
					$("#fila" + data.id).remove();

					// Ofrecer impresión de etiqueta
					if(respuesta.opdetregprod_id){
						// Misma etiqueta (tamaño térmico compacto) para todas las etapas,
						// incluida la última — 'etiqueta-bodega' es un formato más grande
						// que no cabe en la etiqueta física.
						var urlEtiqueta  = '/opdetregprodtempaprobsup/etiqueta-etapa/' + respuesta.opdetregprod_id;
						swal({
							title: 'Registro aprobado',
							text: '¿Desea ver la etiqueta?',
							icon: 'success',
							buttons: { cancel: 'No', confirm: 'Ver etiqueta' }
						}).then(function(value){
							if(value){
								// Mostrar en el mismo modal de etiqueta usado en op/seguimiento
								// (no abrir una pestaña/ventana nueva del navegador).
								$('#modalEtiquetaId').text('#' + respuesta.opdetregprod_id);
								$('#ifrEtiquetaEtapa').attr('src', urlEtiqueta);
								$('#modalEtiquetaEtapa').modal('show');
							}
						});
					}
				}
			}
		},
		error: function () {
			Biblioteca.notificaciones('Error de conexión.', 'Plastiservi', 'error');
		}
	});
}

function mostrarModalBodega(bodegas) {
	// Construir opciones del select
	var options = bodegas.map(function(b){
		return '<option value="' + b.id + '">' + b.nombre + '</option>';
	}).join('');
	swal({
		title: 'Seleccionar bodega de producción',
		content: {
			element: 'select',
			attributes: { id: 'swal-select-bodega', className: 'form-control', innerHTML: options }
		},
		buttons: { cancel: 'Cancelar', confirm: 'Confirmar' }
	}).then(function(value){
		if(value){
			var bodegaId = document.getElementById('swal-select-bodega').value;
			_lastAprobData['invbodega_id'] = bodegaId;
			ajaxRequest(_lastAprobData, _lastAprobUrl, 'aprobarproducc');
		}
	});
}

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
