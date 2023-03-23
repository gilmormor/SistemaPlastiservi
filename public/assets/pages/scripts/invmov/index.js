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
		'ajax'        : "invmovpage",
		"order": [[ 0, "id" ]],
		'columns'     : [
			{data: 'id'},
			{data: 'fechahora'},
			{data: 'desc'},
			{data: 'invmovmodulo_nombre'},
			{data: 'idmovmod'},
			{data: 'invmovmodulo_id',className:"ocultar"},
            {defaultContent : 
				"<a class='btn-accion-tabla btn-sm btngenpdfINVMOV tooltipsC' title='PDF Movimiento Inv'>" +
					"<i class='fa fa-fw fa-file-pdf-o'></i>" +
				"</a>"
            }
        ],
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
		},
		"createdRow": function ( row, data, index ) {
			aux_text = 
			"<a class='btn-accion-tabla btn-sm tooltipsC' title='Movimiento de Inv' onclick='genpdfINVMOV(" + data.id + ",1)'>"+
				data.id +
			"</a>";
			$('td', row).eq(0).html(aux_text);
			$('td', row).eq(0).attr('data-search',data.id);

			$(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);
			$('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));
			switch (data.invmovmodulo_id) {
				case 1:
					aux_text = 
					"<a class='btn-accion-tabla btn-sm tooltipsC' title='PDF Entrada Salida de Inv: " + data.idmovmod + "' onclick='genpdfINVENTSAL(" + data.idmovmod + ",1)'>"+
						data.idmovmod +
					"</a>";
					break;
				case 2:
					aux_text = 
					"<a class='btn-accion-tabla btn-sm tooltipsC' title='PDF Orden despacho: " + data.idmovmod + "' onclick='genpdfOD(" + data.idmovmod + ",1)'>"+
						+ data.idmovmod +
					"</a>";
					break;
				case 3:
					aux_text = 
					"<a class='btn-accion-tabla btn-sm tooltipsC' title='PDF Orden despacho: " + data.idmovmod + "' onclick='genpdfOD(" + data.idmovmod + ",1)'>"+
						+ data.idmovmod +
					"</a>";
					break;
				case 4:
					aux_text = 
					"<a class='btn-accion-tabla btn-sm tooltipsC' title='PDF Inicio o cierre de mes: " + data.id + "' onclick='genpdfINVMOV(" + data.id + ",1)'>"+
						data.id +
					"</a>";
					break;
				case 5:
					aux_text = 
					"<a class='btn-accion-tabla btn-sm tooltipsC' title='PDF Solicitud despacho: " + data.idmovmod + "' onclick='genpdfSD(" + data.idmovmod + ",1)'>"+
						+ data.idmovmod +
					"</a>";	
					break;
				case 6:
					aux_texto = "<a class='btn-accion-tabla btn-sm tooltipsC' title='PDF Rechazo Orden despacho: " + data.idmovmod + "' onclick='genpdfODRec(" + data.idmovmod + ",1)'>" +
						data.idmovmod +
					"</a>";
					break;
				default:
					aux_texto = "Falta asignar PDF"
			}
			$('td', row).eq(4).html(aux_text);
		}
	});
	
});
