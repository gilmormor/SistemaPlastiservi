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
        'ajax'        : "areaproduccionpage",
        'columns'     : [
            {data: 'id'},
            {data: 'nombre'},
            {defaultContent : 
                ""
            }
        ],
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            aux_text = 
                `<a id="bntorden${data.id}" name="bntorden${data.id}" class="btn-accion-tabla btn-sm tooltipsC" onclick="ordenproduccion(${data.id})" title="Editar Orden Etapas de Produccion">
                    <span class="glyphicon glyphicon-sort-by-attributes" style="bottom: 0px;top: 2px;"></span>
                </a>
                <a href='areaproduccion' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>
                    <i class='fa fa-fw fa-pencil'></i>
                </a>
                <a href='areaproduccion' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar este registro'>
                    <i class='fa fa-fw fa-trash text-danger'></i>
                </a>`;
            $('td',row).eq(2).html(aux_text);
        }
      });

});

function ordenproduccion(id){
    $(this).val("");
    $(".input-sm").val('');
    //data = datosproducto();
    nombreTabla = '#tabla-data-OrdenEtapaProd';

    $(nombreTabla).DataTable().ajax.url( "areaproduccionetapaprodpage/?areaproduccion_id=" + id ).load();
    $('#myModalOrdenEtapaProd').modal('show');
}