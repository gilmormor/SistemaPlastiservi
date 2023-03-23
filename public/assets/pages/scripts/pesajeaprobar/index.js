$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
	$('#tabla-data-pesaje').DataTable({
		'paging'      : true, 
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		'autoWidth'   : false,
		'processing'  : true,
		'serverSide'  : true,
		'ajax'        : "pesajeaprobarpage",
		"order": [[ 0, "id" ]],
		'columns'     : [
			{data: 'id'},
			{data: 'fechahora'},
			{data: 'desc'},
			{data: 'id'},
			{data: 'updated_at',className:"ocultar"},
            {defaultContent : 
				""
            }
        	],
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
		},
		"createdRow": function ( row, data, index ) {
			$(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);
			aux_text = 
			"<a class='btn-accion-tabla btn-sm tooltipsC' title='PDF Entrada Inv Pesaje' onclick='genpdfPESAJE(" + data.id + ",1)'>"+
				data.id +
			"</a>";
			$('td', row).eq(0).html(aux_text);
			$('td', row).eq(0).attr('data-search',data.id);

			$('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));
			aux_text = 
			"<a class='btn-accion-tabla btn-sm btngenpdfPESAJE tooltipsC' title='PDF Entrada Inv Pesaje'>" +
				"<i class='fa fa-fw fa-file-pdf-o'></i>" +
			"</a>";
			$('td', row).eq(3).html(aux_text);

			$('td', row).eq(4).html(data.updated_at);
			$('td', row).eq(4).attr("id","updated_at"+data.id);
			$('td', row).eq(4).attr("name","updated_at"+data.id);
			$('td', row).eq(4).addClass("updated_at");

			aux_text = 
			"<a class='btn-accion-tabla btn-sm tooltipsC' title='Aprobar Entrada Salida Inv' onclick='aprobrecPesaje(" + data.id + ")'>" +
				"<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>"+
			"</a>"
			$('td', row).eq(5).html(aux_text);

		}
	});

	$("#btnguardaraprob").click(function(event){
		//alert('Entro');
		$("#myModalaprobcot").modal('show');
	});
	
});

function aprobrecPesaje(id){
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
		updated_at : $("#updated_at" + $("#id").val()).html(),
        _token: $('input[name=_token]').val()
	};
	var ruta = '/pesaje/aprobpesaje/'+data['id'];
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
			ajaxRequest(data,ruta,'aprobpesaje');
		}
	});

});

$("#btnrechazarM").click(function(event){
	if(verificarAproRech()){
		var data = {
			id       : $("#id").val(),
			staaprob : 3,
			obsaprob : $("#aprobobs").val(),
			updated_at : $("#updated_at" + $("#id").val()).html(),
			_token: $('input[name=_token]').val()
		};
		var ruta = '/pesaje/aprobpesaje/'+data['id'];
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
				ajaxRequest(data,ruta,'aprobpesaje');
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
			if(funcion=='aprobpesaje'){
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
