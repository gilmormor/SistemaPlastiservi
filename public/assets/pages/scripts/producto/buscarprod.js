$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    configTablaProd();
});

function datos(){
    var data1 = {
        cliente_id  : $("#cliente_id").val(),
        sucursal_id : $("#sucursal_id").val(),
        _token      : $('input[name=_token]').val()
    };

    var data2 = "?cliente_id="+data1.cliente_id +
    "&sucursal_id="+data1.sucursal_id

    var data = {
        data1 : data1,
        data2 : data2
    };
    return data;
}

function configTablaProd(){
    aux_nfila = 0;
    data = datos();
    $("#tabla-data-productos").attr('style','');
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
        'ajax'        : "producto/productobuscarpage/" + data.data2 + "&producto_id=",
        'columns'     : [
            {data: 'id'},
            {data: 'nombre'},
            {data: 'diametro'},
            {data: 'cla_nombre'},
            {data: 'long'},
            {data: 'peso'},
            {data: 'tipounion'},
            {data: 'precioneto'},
            {data: 'precio'},
            {data: 'tipoprod',className:"ocultar"},
			{data: 'acuerdotecnico_id',className:"ocultar"}
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            aux_nfila++;
            selecmultprod = false;
            //aux_onclick = "llenarlistaprod(" + aux_nfila + "," + data.id + ")";
            if($("#selecmultprod").val()){
                aux_onclick = "llenarlistaprod(" + aux_nfila + "," + data.id + ")";
            }else{
                aux_onclick = "copiar_codprod(" + data.id + ",'')";
                //aux_onclick = "insertarTabla(" + data.id + ",'" + data.nombre + "'," + data.acuerdotecnico_id + ")";
            }

            $(row).attr('name', 'fila' + aux_nfila);
            $(row).attr('id', 'fila' + aux_nfila);
            //$(row).attr('prodid', 'tooltip');
            $(row).attr('class', "btn-accion-tabla copiar_id");
            //$(row).attr('data-toggle', data.id);
            $(row).attr('title', "Click para seleccionar producto");
            $(row).attr('onClick', aux_onclick + ';');

            //$(row).attr('id','fila' + data.id);


            if(data.tipoprod == 1){
                aux_text = 
                    data.nombre +
                " <i id='icoat1' class='fa fa-cog text-red girarimagen'></i>";
                $('td', row).eq(1).html(aux_text);
            }

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