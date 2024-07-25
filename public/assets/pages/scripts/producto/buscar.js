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
/*
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
        'ajax'        : "productobuscarpage/" + data.data2 + "&producto_id=-1",
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
*/
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
        'ajax'        : "productobuscarpage/" + data.data2 + "&producto_id=",
        'columns'     : [
            {data: 'id'},
            {data: 'nombre',"width": "250px"},
            {data: 'cla_nombre'},
            {data: 'diametro'},
            {data: 'long1'},
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
            //console.log($("#selecmultprod").val());
            if($("#selecmultprod").val()){
                aux_onclick = "llenarlistaprod(" + aux_nfila + "," + data.id + ")";
            }else{
                aux_onclick = "copiar_codprod(" + data.id + ",'')";
                //aux_onclick = "insertarTabla(" + data.id + ",'" + data.nombre + "'," + data.acuerdotecnico_id + ")";
            }


            $(row).attr('name', 'fila' + aux_nfila);
            $(row).attr('id', 'fila' + aux_nfila);
            $(row).attr('prodid', 'tooltip');
            $(row).attr('class', "btn-accion-tabla copiar_id");
            //$(row).attr('data-toggle', data.id);
            //$(row).attr('title', "Click para seleccionar producto");
            //$(row).attr('onClick', aux_onclick + ';');
            for(i=1; i<=10; i++){
                $('td', row).eq(i).attr('onClick',  aux_onclick + ';');
                //$('td', row).eq(i).attr('data-toggle', data.id);
                //$('td', row).eq(i).addClass('tooltipsC');
                $('td', row).eq(i).attr('title', "Click para seleccionar producto");    
            }
            if(data.acuerdotecnico_id != null){
                data.at_usoprevisto = data.at_impresoobs != null ? "UsoPrev: " + data.at_impresoobs : "";
                data.at_impresoobs = data.at_impresoobs != null ? "ObsImp: " + data.at_impresoobs : ""; 
                data.at_tiposelloobs = data.at_tiposelloobs != null ? "ObsSell: " + data.at_tiposelloobs : "";
                data.at_feunidxpaqobs  = data.at_feunidxpaqobs != null ? "UnixEmp: " + data.at_feunidxpaqobs : "";
                data.at_complementonomprod  = data.at_complementonomprod != null ? "CompImp: " + data.at_complementonomprod : "";
                aux_atribAT = `${data.at_usoprevisto} ${data.at_impresoobs} ${data.at_tiposelloobs} ${data.at_feunidxpaqobs} ${data.at_complementonomprod}`;
                aux_atribAT = aux_atribAT.trim();
                aux_atribAT = aux_atribAT == "" ? "Acuerdo Técnico" : aux_atribAT;
                aux_text = 
                `<a style="padding-left: 0px;" class="btn-accion-tabla btn-sm tooltipsC" title="${aux_atribAT}">
                    ${data.id}
                </a>`;
                $('td', row).eq(0).html(aux_text);
                $('td', row).eq(0).attr('onClick', 'genpdfAcuTec(' + data.acuerdotecnico_id + ',1,"myModalBuscarProd");');
            }
            if(data.tipoprod == 1){
                $('td', row).eq(0).addClass('tooltipsC');
                $('td', row).eq(0).attr('title', "Producto base para crear acuerdo técnico");    
                aux_text = 
                    data.id +
                " <i id='icoat1' class='fa fa-cog text-red girarimagen'></i>";
                $('td', row).eq(0).html(aux_text);

            }

            //$(row).attr('id','fila' + data.id);

/*
            if(data.tipoprod == 1){
                aux_text = 
                    data.nombre +
                " <i id='icoat1' class='fa fa-cog text-red girarimagen'></i>";
                $('td', row).eq(1).html(aux_text);
            }

            $('td', row).eq(5).attr('data-order',data.peso);
            $('td', row).eq(5).attr('data-search',data.peso);
            $('td', row).eq(5).html(MASKLA(data.peso,3));

*/
            if(data.cla_nombre == 0 || data.cla_nombre == "" || data.cla_nombre == null){
                $('td', row).eq(2).html("");
                $('td', row).eq(2).attr('data-order',"");
                $('td', row).eq(2).attr('data-search',"");    
            }
            if(data.diametro == 0 || data.diametro == "" || data.diametro == null){
                $('td', row).eq(3).html("");
                $('td', row).eq(3).attr('data-order',"");
                $('td', row).eq(3).attr('data-search',"");    
            }
            //$('td', row).eq(3).html(MASKLA(data.diametro,2));
            $('td', row).eq(3).attr('style','text-align:center');
            if(data.long1 == 0 || data.long1 == "" || data.long1 == null){
                $('td', row).eq(4).html("");
                $('td', row).eq(4).attr('data-order',"");
                $('td', row).eq(4).attr('data-search',"");
            }
            $('td', row).eq(4).attr('style','text-align:center');
            if(data.peso == 0 || data.peso == "" || data.peso == null){
                $('td', row).eq(5).html("");
                $('td', row).eq(5).attr('data-order',"");
                $('td', row).eq(5).attr('data-search',"");    
            }else{
                $('td', row).eq(5).attr('data-order',data.peso);
                $('td', row).eq(5).attr('data-search',data.peso);
                $('td', row).eq(5).html(MASKLA(data.peso,3));
            }
            $('td', row).eq(5).attr('style','text-align:center');
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