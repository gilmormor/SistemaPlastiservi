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
		'ajax'        : "inventsalpage",
		"order": [[ 0, "id" ]],
		'columns'     : [
			{data: 'id'},
			{data: 'fechahora'},
			{data: 'desc'},
			{data: 'obsaprob',className:"ocultar"},
            {defaultContent : 
				"<a href='/inventsal/enviaraprobarinventsal' class='btn-accion-tabla btn-sm tooltipsC btnaprobar' title='Aprobar'>" +
					"<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>"+
				"</a>"+
                "<a href='inventsal' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>" + 
                    "<i class='fa fa-fw fa-pencil'></i>" + 
                "</a>"+
                "<a href='inventsal' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar este registro'>"+
                    "<i class='fa fa-fw fa-trash text-danger'></i>"+
                "</a>"
            }
        	],
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
		},
		"createdRow": function ( row, data, index ) {
			$(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);
			$('td', row).eq(1).attr('data-order',data.fechahora);
            aux_fecha = new Date(data.fechahora);
            $('td', row).eq(1).html(fechaddmmaaaa(aux_fecha));

			if(data.obsaprob != ""){
				aux_text = data.desc +
				" <a class='btn-sm tooltipsC' title='" + data.obsaprob + "'>" +
					"<i class='fa fa-fw fa-question-circle text-red'></i>" + 
				"</a>";
				$('td', row).eq(2).html(aux_text);
			}


		}
	});
	
});
