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
            {data: 'opdet_kg'},
            {data: 'opdet_kgrec'},
            {data: 'opdet_kgprod'},
            {data: 'opdet_kgscrap'},
            {data: 'kg'},
            {data: 'kgscrap'},
            {data: 'kgsaldo'},
            {data: 'updatednum_at',className:"ocultar"},
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

            aux_text = 
                `<a id="otdet_id${data.id}" name="otdet_id${data.id}" class="btn-accion-tabla btn-sm" title="Ver OT: ${data.ot_id}" onclick='genpdf(${data.ot_id},"","ver-pdf-ot","/ot/exportPdf/${data.ot_id}")'>
                    ${data.ot_id}
                </a>
                <a id="otdet_id${data.id}" name="otdet_id${data.id}" class="btn-accion-tabla btn-sm" title="Ver OP: ${data.op_id}" onclick='genpdf(${data.op_id},"","ver-pdf-op","/op/exportPdf/${data.op_id}")'>
                    ${data.op_id}-${data.opdet_id}
                </a>`;
            $('td', row).eq(1).html(aux_text);


            $('td', row).eq(5).attr('style','text-align:right');
            $('td', row).eq(5).attr('data-order',data.opdet_kg);
            $('td', row).eq(5).attr('data-search',data.opdet_kg);
            $('td', row).eq(5).html(MASKLA(data.opdet_kg, 2));

            $('td', row).eq(6).attr('style','text-align:right');
            $('td', row).eq(6).attr('data-order',data.opdet_kgrec);
            $('td', row).eq(6).attr('data-search',data.opdet_kgrec);
            $('td', row).eq(6).html(MASKLA(data.opdet_kgrec, 2));

            $('td', row).eq(7).attr('style','text-align:right');
            $('td', row).eq(7).attr('data-order',data.opdet_kgprod);
            $('td', row).eq(7).attr('data-search',data.opdet_kgprod);
            $('td', row).eq(7).html(MASKLA(data.opdet_kgprod, 2));

            $('td', row).eq(8).attr('style','text-align:right');
            $('td', row).eq(8).attr('data-order',data.opdet_kgscrap);
            $('td', row).eq(8).attr('data-search',data.opdet_kgscrap);
            $('td', row).eq(8).html(MASKLA(data.opdet_kgscrap, 2));

            $('td', row).eq(9).attr('style','text-align:right');
            $('td', row).eq(9).attr('data-order',data.kg);
            $('td', row).eq(9).attr('data-search',data.kg);
            $('td', row).eq(9).html(MASKLA(data.kg, 2));

            $('td', row).eq(10).attr('style','text-align:right');
            $('td', row).eq(10).attr('data-order',data.kgscrap);
            $('td', row).eq(10).attr('data-search',data.kgscrap);
            $('td', row).eq(10).html(MASKLA(data.kgscrap, 2));

            $('td', row).eq(11).attr('style','text-align:right');
            $('td', row).eq(11).attr('data-order',data.kgsaldo);
            $('td', row).eq(11).attr('data-search',data.kgsaldo);
            $('td', row).eq(11).html(MASKLA(data.kgsaldo, 2));

            $('td', row).eq(12).attr('updated_at',data.updatednum_at);
            $('td', row).eq(12).addClass('updated_at');

            aux_text = `<a class='btn-accion-tabla btn-sm tooltipsC' title='Aprobar Produccion' onclick='aprobrecproducc(${data.id},${data.updatednum_at})'>
                            <span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>
                        </a>`;
            $('td', row).eq(13).html(aux_text);
        }
      });

});

function aprobrecproducc(id,updatednum_at){
	quitarvalidacioneach();
	$("#id").val(id);
	$("#aprobobs").val("");
	$("#myModalaprobcot").modal('show');
}


$("#btnaprobarM").click(function(event){
	var data = {
		id       : $("#id").val(),
		staaprob : 2,
		obsaprob : $("#aprobobs").val(),
        updated_at : $("#fila" + $("#id").val()).attr("updated_at"),
        _token: $('input[name=_token]').val()
	};
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
					// Servidor pide seleccionar bodega de producción
					_lastAprobData = data;
					_lastAprobUrl  = url;
					mostrarModalBodega(respuesta.bodegas);
					return;
				}

				$("#aprobobs").val("");
				$("#myModalaprobcot").modal('hide');
				Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipmen);
				if(respuesta.resp == 1){
					$("#fila" + data.id).remove();

					// Ofrecer impresión de etiqueta
					if(respuesta.opdetregprod_id){
						var tipoEtiqueta = respuesta.es_ultima_etapa ? 'etiqueta-bodega' : 'etiqueta-etapa';
						var urlEtiqueta  = '/opdetregprodtempaprobsup/' + tipoEtiqueta + '/' + respuesta.opdetregprod_id;
						swal({
							title: 'Registro aprobado',
							text: '¿Desea imprimir la etiqueta?',
							icon: 'success',
							buttons: { cancel: 'No imprimir', confirm: 'Imprimir etiqueta' }
						}).then(function(value){
							if(value){ window.open(urlEtiqueta, '_blank'); }
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
