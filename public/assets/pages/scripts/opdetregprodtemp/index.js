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
            {data: 'usuario_nombre'},
            {data: 'operario_nombre'},
            {defaultContent : ``}
        ],
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            $(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);
            $(row).attr('updated_at', data.updatednum_at);

            if(data.aprobstatus == 3){
				aux_text = data.id +
				` <a class='btn-sm tooltipsC' title="${data.aprobobs}">
					<i class="fa fa-fw fa-question-circle text-red"></i>
				</a>`;
				$('td', row).eq(0).html(aux_text);
			}
            $('td', row).eq(0).attr('updated_at', data.updatednum_at);

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

            $('td', row).eq(11).attr('id','updated_at' + data.id);
            $('td', row).eq(11).attr('name','updated_at' + data.id);
            $('td', row).eq(11).attr('updated_at',data.updatednum_at);
            $('td', row).eq(11).addClass('updated_at');

            aux_text = `<a href="/opdetregprodtemp/enviaraprob" class="btn-accion-tabla btn-sm tooltipsC btnaprobar botonac${data.id}" title="Enviar aprobacion" item="${data.id}">
                    <i class="fa fa-fw fa-save fa-lg"></i>
                </a>
                <a href='opdetregprodtemp' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>
                    <i class='fa fa-fw fa-pencil'></i>
                </a>
                <a href='opdetregprodtemp' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar este registro'>
                    <i class='fa fa-fw fa-trash text-danger'></i>
                </a>`;
            $('td', row).eq(14).html(aux_text);
            $('td', row).eq(14).attr('class','action-buttons');

        }
      });

});