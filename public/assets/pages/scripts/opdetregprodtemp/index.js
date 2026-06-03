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
            {data: 'op_id'},
            {data: 'opdet_id'},
            {data: 'razonsocial'},
            {data: 'opdet_kgrec'},
            {data: 'opdet_kgprod'},
            {data: 'opdet_kgscrap'},
            {data: 'kgprod'},
            {data: 'kgscrap'},
            {data: 'kgsaldo'},
            {data: 'updatednum_at',className:"ocultar"},
            {data: 'usuario_nombre'},
            {data: 'maquina_nombre'},
            {data: 'operario_nombre'},
            {defaultContent : ``},
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

            // Columna 5 (opdet_kgrec): desplazada +1 por la nueva columna 'ot' en posicion 1
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

            $('td', row).eq(11).attr('id','updated_at' + data.id);
            $('td', row).eq(11).attr('name','updated_at' + data.id);
            $('td', row).eq(11).attr('updated_at',data.updatednum_at);
            $('td', row).eq(11).addClass('updated_at');

            // Columna 15: Estado del cierre de la UM de salida
            const cantprodNum = parseFloat(data.cantprod) || 0;
            const umSalNombre = data.unidadmedidasal_nombre ? data.unidadmedidasal_nombre : 'UM salida';
            if (data.rollo_cerrado == 1 || cantprodNum > 0) {
                $('td', row).eq(15).html(
                    `<span class="label" style="background-color:#5cb85c;color:#fff;padding:4px 8px;font-size:11px;" title="Este registro cerro ${cantprodNum} ${umSalNombre}(s) de salida de la etapa">✔ ${umSalNombre} Cerrado: ${cantprodNum}</span>`
                );
            } else {
                $('td', row).eq(15).html(
                    `<span class="label" style="background-color:#f0ad4e;color:#fff;padding:4px 8px;font-size:11px;" title="Registro parcial: kg procesados que aun no completan un(a) ${umSalNombre} de salida de la etapa">⚠ ${umSalNombre} Abierto (parcial)</span>`
                );
            }

            // Columna 16: Acciones con flags de orden
            const pEnviar   = (data.puede_enviar_aprob == 1 || data.puede_enviar_aprob === true) ? 1 : 0;
            const pEliminar = (data.puede_eliminar     == 1 || data.puede_eliminar     === true) ? 1 : 0;

            const btnEnviar = pEnviar
                ? `<a href="/opdetregprodtemp/enviaraprob" class="btn-accion-tabla btn-sm tooltipsC btnaprobar botonac${data.id}" title="Enviar aprobacion" item="${data.id}">
                        <i class="fa fa-fw fa-save fa-lg"></i>
                    </a>`
                : `<a class="btn-accion-tabla btn-sm tooltipsC" title="No se puede enviar: existe un registro anterior del mismo OpDet aun sin enviar" style="opacity:0.35;cursor:not-allowed;pointer-events:none;">
                        <i class="fa fa-fw fa-save fa-lg text-muted"></i>
                    </a>`;

            const btnEliminar = pEliminar
                ? `<a href='opdetregprodtemp' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar este registro'>
                        <i class='fa fa-fw fa-trash text-danger'></i>
                    </a>`
                : `<a class='btn-accion-tabla tooltipsC' title='No se puede eliminar: existe un registro posterior del mismo OpDet o ya fue aprobado' style="opacity:0.35;cursor:not-allowed;pointer-events:none;">
                        <i class='fa fa-fw fa-trash text-muted'></i>
                    </a>`;

            aux_text = `${btnEnviar}
                <a href='opdetregprodtemp' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>
                    <i class='fa fa-fw fa-pencil'></i>
                </a>
                ${btnEliminar}`;
            $('td', row).eq(16).html(aux_text);
            $('td', row).eq(16).attr('class','action-buttons');

        }
      });

});