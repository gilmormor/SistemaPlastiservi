$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
	$('#tabla-data-inventsal').DataTable({
		'paging'      : true, 
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		'autoWidth'   : false,
		'processing'  : true,
		'serverSide'  : true,
		'ajax'        : "inventsalaprobarpage",
		"order": [[ 0, "id" ]],
		'columns'     : [
			{data: 'id'},
			{data: 'fechahora'},
			{data: 'desc'},
			{data: 'usuario_nombre'},
			{data: 'id'},
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
			"<a class='btn-accion-tabla btn-sm tooltipsC' title='Entrada Salida de Inv' onclick='genpdfINVENTSAL(" + data.id + ",1)'>"+
				data.id +
			"</a>";
			$('td', row).eq(0).html(aux_text);
			$('td', row).eq(0).attr('data-search',data.id);

			$('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));

			aux_text = 
			"<a class='btn-accion-tabla btn-sm btngenpdfINVENTSAL tooltipsC' title='PDF Entrada Salida Inv'>" +
				"<i class='fa fa-fw fa-file-pdf-o'></i>" +
			"</a>";
			$('td', row).eq(4).html(aux_text);


			aux_text = 
			"<a class='btn-accion-tabla btn-sm tooltipsC' title='Aprobar Entrada Salida Inv' onclick='aprobrecentsalinv(" + data.id + ")'>" +
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

function aprobrecentsalinv(id){
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
        _token: $('input[name=_token]').val()
	};
	var ruta = '/inventsal/aprobinventsal/'+data['id'];
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
			ajaxRequest(data,ruta,'aprobinventsal');
		}
	});

});

$("#btnrechazarM").click(function(event){
	if(verificarAproRech()){
		var data = {
			id       : $("#id").val(),
			staaprob : 3,
			obsaprob : $("#aprobobs").val(),
			_token: $('input[name=_token]').val()
		};
		var ruta = '/inventsal/aprobinventsal/'+data['id'];
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
				ajaxRequest(data,ruta,'aprobinventsal');
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
			if(funcion=='aprobinventsal'){
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
