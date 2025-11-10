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
            {data: 'op_id'},
            {data: 'opdet_id'},
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

            $('td', row).eq(4).attr('style','text-align:right');
            $('td', row).eq(4).attr('data-order',data.opdet_kg);
            $('td', row).eq(4).attr('data-search',data.opdet_kg);
            $('td', row).eq(4).html(MASKLA(data.opdet_kg, 2));

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
            $('td', row).eq(8).attr('data-order',data.kg);
            $('td', row).eq(8).attr('data-search',data.kg);
            $('td', row).eq(8).html(MASKLA(data.kg, 2));

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

            aux_text = `<a class='btn-accion-tabla btn-sm tooltipsC' title='Aprobar Produccion' onclick='aprobrecproducc(${data.id},${data.updatednum_at})'>
                            <span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>
                        </a>`;
            $('td', row).eq(12).html(aux_text);
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

function ajaxRequest(data,url,funcion) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='aprobarproducc'){
				$("#aprobobs").val("");
				$("#myModalaprobcot").modal('hide');
				Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipmen);
				if(respuesta.resp == 1){
					$("#fila" + data.id).remove();
				}

				// *** REDIRECCIONA A UNA RUTA*** 
				/*
				var loc = window.location;
				window.location = loc.protocol+"//"+loc.hostname+"/notaventaaprobar";
				*/
				// ****************************** 
			}
		},
		error: function () {
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
