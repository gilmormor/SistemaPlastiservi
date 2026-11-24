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
        'ajax'        : "acuerdotecnicoetapaprodpage",
        'columns'     : [
            {data: 'id'},
            /* Codigo del producto: al hacer clic abre el acuerdo tecnico en el
               visor, igual que en las demas pantallas. El id que recibe
               genpdfAcuTec es el del acuerdo tecnico, no el del producto. */
            {data: 'producto_id', render: function (dato, tipo, fila) {
                if (tipo !== 'display') { return dato; }
                return "<a class='btn-accion-tabla tooltipsC' style='cursor:pointer;' " +
                       "title='Ver el acuerdo tecnico de este producto' " +
                       'onclick=\'genpdfAcuTec(' + fila.id + ',0,1,"")\'>' +
                       dato + '</a>';
            }},
            {data: 'nombre_producto'},
            {defaultContent : 
                "<a href='acuerdotecnicoetapaprod' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>"+
                    "<i class='fa fa-fw fa-pencil'></i>" +
                "</a>"}
        ],
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
      });

});