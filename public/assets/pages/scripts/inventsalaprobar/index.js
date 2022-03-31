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
            {defaultContent : 
				"<a href='/inventsal/aprobinventsal' class='btn-accion-tabla btn-sm tooltipsC btnaprobar' title='Aprobar'>" +
					"<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>"+
				"</a>"+
				"<a class='btn-accion-tabla btn-sm btngenpdfINVENTSAL tooltipsC' title='PDF Entrada Salida Inv'>" +
					"<i class='fa fa-fw fa-file-pdf-o'></i>" +
				"</a>"
            }
        	],
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
		},
		"createdRow": function ( row, data, index ) {
			$(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);
			aux_text = 
			"<a class='btn-accion-tabla btn-sm tooltipsC' title='Entrasa Salida de Inv' onclick='genpdfINVENTSAL(" + data.id + ",1)'>"+
				data.id +
			"</a>";
			$('td', row).eq(0).html(aux_text);
			$('td', row).eq(0).attr('data-search',data.id);

			$('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));
		}
	});
	
});
