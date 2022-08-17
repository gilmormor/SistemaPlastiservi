$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    configTablaProd();

});

function configTablaProd(){
    aux_nfila = 0;
    $("#tabla-data-productos").attr('style','')
    $("#tabla-data-productos").dataTable().fnDestroy();

    $('#tabla-data-productos').DataTable({
        'paging'      : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "productobuscarpage",
        'columns'     : [
            {data: 'id'},
            {data: 'nombre'},
            {data: 'diametro'},
            {data: 'cla_nombre'},
            {data: 'long'},
            {data: 'peso'},
            {data: 'tipounion'},
            {data: 'precioneto'},
            {data: 'precio'}
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            aux_nfila++;
            selecmultprod = true;
            //aux_onclick = "llenarlistaprod(" + aux_nfila + "," + data.id + ")";
            aux_onclick = "copiar_codprod(" + data.id + ",'')";

            $(row).attr('name', 'fila' + aux_nfila);
            $(row).attr('id', 'fila' + aux_nfila);
            $(row).attr('prodid', 'tooltip');
            $(row).attr('class', "btn-accion-tabla copiar_id");
            $(row).attr('data-toggle', data.id);
            $(row).attr('title', "Click para seleccionar producto");
            $(row).attr('onClick', aux_onclick + ';');

            //$(row).attr('id','fila' + data.id);

            $('td', row).eq(5).attr('data-order',data.peso);
            $('td', row).eq(5).attr('data-search',data.peso);
            $('td', row).eq(5).html(MASKLA(data.peso,3));


            $('td', row).eq(7).attr('data-order',data.precioneto);
            $('td', row).eq(7).attr('data-search',data.precioneto);
            $('td', row).eq(7).attr('style','text-align:right');
            $('td', row).eq(7).html(MASKLA(data.precioneto,0));

            $('td', row).eq(8).attr('data-order',data.precio);
            $('td', row).eq(8).attr('data-search',data.precio);
            $('td', row).eq(8).attr('style','text-align:right');
            $('td', row).eq(8).html(MASKLA(data.precio,0));

            /*
            $('td', row).eq(8).attr('data-order',data.precioneto);
            $('td', row).eq(8).attr('data-search',data.precioneto);
            $('td', row).eq(8).attr('style','text-align:right');
            $('td', row).eq(8).html(MASKLA(data.precioneto,2));
            */
            $("#totalreg").val(aux_nfila);

        },
        initComplete: function () {
            // Apply the search
            this.api()
                .columns()
                .every(function () {
                    var that = this;
 
                    $('input', this.footer()).on('keyup change clear', function () {
                        if (that.search() !== this.value) {
                            that.search(this.value).draw();
                        }
                    });
                });
        },
    });
}