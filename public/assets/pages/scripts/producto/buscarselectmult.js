$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    configTablaProdSelectMult();

    var table = $('#tabla-data-productos-selectmult').DataTable();
    // Función que verifica y actualiza el estado del checkbox #SelecAll
    function actualizarEstadoSelectAll() {
        // Obtiene la información actual de la página
        var info = table.page.info();

        // Selecciona solo los checkboxes marcados en las filas visibles
        var checkboxesMarcados = table.rows({ page: 'current' }).nodes().to$().find('.columna11Checkbox:checked');
        
        // Cuenta cuántos están marcados
        var numeroMarcados = checkboxesMarcados.length;

        // Número de elementos en la página actual
        var numeroElementosEnPagina = info.end - info.start;

        // Verifica si todos los elementos en la página están marcados
        if (numeroElementosEnPagina === numeroMarcados) {
            // Marca el checkbox SelectAll
            $('#SelecAll').prop('checked', true);
        } else {
            // Desmarca el checkbox SelectAll
            $('#SelecAll').prop('checked', false);
        }
    }

    // Llama a la función en el evento page.dt
    table.on('page.dt', function() {
        actualizarEstadoSelectAll();
    });

    // Llama a la función en el evento draw.dt
    table.on('draw.dt', function() {
        actualizarEstadoSelectAll();
    });

    // Puedes llamar a la función también al cargar la página
    actualizarEstadoSelectAll();


    
});
let productosSeleccionados = [];
function datosSelectMult(){
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
function configTablaProdSelectMult(){
    //aux_arrayprod_id = obtenerValoresSelect("productossm");
    aux_nfila = 0;
    data = datosSelectMult();
    $("#tabla-data-productos-selectmult").attr('style','');
    $("#tabla-data-productos-selectmult").dataTable().fnDestroy();
    $('#tabla-data-productos-selectmult tfoot th').each(function () {
        var title = $(this).text();
        $(this).html('<input type="text" placeholder="Buscar ' + title + '" />');
    });
    $('#tabla-data-productos-selectmult').DataTable({
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
			{data: 'acuerdotecnico_id',className:"ocultar"},
        ],
        "order": [[1, 'asc']], // Ordenar por la columna 2 (índice 2) en orden ascendente
        "columnDefs": [
            { "orderable": false, "targets": 0 } // Desactiva el ordenamiento en la columna 0
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            aux_nfila++;
            selecmultprod = false;
            //console.log($("#selecmultprod").val());

            $(row).attr('name', 'producto_idbm' + data.id);
            $(row).attr('id', 'producto_idbm' + data.id);
            $(row).attr('prodid', 'tooltip');
            $(row).attr('class', "btn-accion-tabla tdproducto_id");
            $(row).attr('tdproducto_id', data.id);
            
            //$(row).attr('data-toggle', data.id);
            //$(row).attr('title', "Click para seleccionar producto");

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
                $('td', row).eq(1).html(aux_text);
                $('td', row).eq(1).attr('onClick', 'genpdfAcuTec(' + data.acuerdotecnico_id + ',1,"myModalBuscarProd");');
            }
            if(data.tipoprod == 1){
                $('td', row).eq(1).addClass('tooltipsC');
                $('td', row).eq(1).attr('title', "Producto base para crear acuerdo técnico");    
                aux_text = 
                    data.id +
                " <i id='icoat1' class='fa fa-cog text-red girarimagen'></i>";
                $('td', row).eq(1).html(aux_text);

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
                $('td', row).eq(3).html("");
                $('td', row).eq(3).attr('data-order',"");
                $('td', row).eq(3).attr('data-search',"");    
            }
            if(data.diametro == 0 || data.diametro == "" || data.diametro == null){
                $('td', row).eq(4).html("");
                $('td', row).eq(4).attr('data-order',"");
                $('td', row).eq(4).attr('data-search',"");    
            }
            //$('td', row).eq(4).html(MASKLA(data.diametro,2));
            $('td', row).eq(4).attr('style','text-align:center');
            if(data.long1 == 0 || data.long1 == "" || data.long1 == null){
                $('td', row).eq(5).html("");
                $('td', row).eq(5).attr('data-order',"");
                $('td', row).eq(5).attr('data-search',"");
            }
            $('td', row).eq(5).attr('style','text-align:center');
            if(data.peso == 0 || data.peso == "" || data.peso == null){
                $('td', row).eq(6).html("");
                $('td', row).eq(6).attr('data-order',"");
                $('td', row).eq(6).attr('data-search',"");    
            }else{
                $('td', row).eq(6).attr('data-order',data.peso);
                $('td', row).eq(6).attr('data-search',data.peso);
                $('td', row).eq(6).html(MASKLA(data.peso,3));
            }
            $('td', row).eq(6).attr('style','text-align:center');
            $('td', row).eq(8).attr('data-order',data.precioneto);
            $('td', row).eq(8).attr('data-search',data.precioneto);
            $('td', row).eq(8).attr('style','text-align:right');
            $('td', row).eq(8).html(MASKLA(data.precioneto,0));

            $('td', row).eq(9).attr('data-order',data.precio);
            $('td', row).eq(9).attr('data-search',data.precio);
            $('td', row).eq(9).attr('style','text-align:right');
            $('td', row).eq(9).html(MASKLA(data.precio,0));

            $("#totalreg").val(aux_nfila);

            /* let str = data.id.toString();
            indice = aux_arrayprod_id.indexOf(str);
            let aux_checked = "";
            if(indice != -1){
                aux_checked = "checked";
                //$("#llenarselGD" + data.id).prop("checked", true);
            } */

            // Busca si el producto_id ya existe en el select
            let aux_checked = "";
            if(buscarEnArray(data.id,$("#producto_idsm").val())){
                aux_checked = "checked";
            }

            aux_text = 
            `<div class="checkbox" style="padding-top: 0px;">
                <div>
                    <label style="font-size: 1.0em">
                        <input type="checkbox" class="columna11Checkbox" id="llenarProd_id${data.id}" name="llenarProd_id[]" onclick="llenarlistaprodSelecMult(${aux_nfila},${data.id},this)" ${aux_checked} producto_id="${data.id}">
                        <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                    </label>
                    <input type="text" name="producto_idcheck[]" id="producto_idcheck${(data.id)}" class="form-control" value="${data.id}" style=display:none;"/>
                </div>
            </div>`;

            $('td', row).eq(0).html(aux_text);
            $('td', row).eq(0).attr('style','text-align:center');

            $(row).attr('nombre', data.nombre);
            $(row).attr('cla_nombre', data.cla_nombre);
            $(row).attr('diametro', data.diametro);
            $(row).attr('espesor', data.espesor);
            if(data.espesor == 0){
                $(row).attr('espesor', "");
            }else{
                $(row).attr('espesor', data.espesor);
            }
            $(row).attr('long1', data.long1);
            if(data.long == 0){
                $(row).attr('long', "");
            }else{
                $(row).attr('long', data.long);
            }
            aux_peso = data.peso;
            aux_peso = aux_peso.toFixed(3);
            $(row).attr('peso', aux_peso);
            $(row).attr('tipounion', data.tipounion);
            $(row).attr('precio', data.precio);
            $(row).attr('precioneto', data.precioneto);


            $(row).attr('unidadmedidafact_id', data.unidadmedidafact_id);
            $(row).attr('at_ancho', "");
            if(data.at_ancho != null){
                $(row).attr('at_ancho', data.at_ancho);
            }
            $(row).attr('at_largo', "");
            if(data.at_largo != null){
                $(row).attr('at_largo', data.at_largo);
            }
            $(row).attr('at_espesor', "");
            if(data.at_espesor != null){
                $(row).attr('at_espesor', data.at_espesor);
            }
            $(row).attr('tipoprod', data.tipoprod);
            $(row).attr('stakilos', data.stakilos);
            $(row).attr('categoriaprod_id', data.categoriaprod_id);
            $(row).attr('acuerdotecnico_id', data.acuerdotecnico_id);
            $(row).attr('at_unidadmedida_id', data.at_unidadmedida_id);

        },
        initComplete: function () {
            // Apply the search
            this.api()
                .columns()
                .every(function () {
                    var that1 = this;
 
                    $('input', this.footer()).on('keyup change clear', function () {
                        if (that1.search() !== this.value) {
                            that1.search(this.value).draw();
                        }
                    });
                });
        },
    });
}

$('#SelecAll').on('click', function() {
    var table = $('#tabla-data-productos-selectmult').DataTable();
    var isChecked = $(this).is(':checked');

    // Recorre todas las filas visibles en la tabla
    table.rows().every(function() {
        var checkbox = $(this.node()).find('.columna11Checkbox');
        
        // Marca o desmarca el checkbox
        checkbox.prop('checked', !isChecked);
        /* aux_producto_id = checkbox.attr('producto_id');
        llenarlistaprod(aux_producto_id,aux_producto_id,checkbox);
        console.log(checkbox.attr('producto_id')); */
        checkbox.click();

        // Dispara manualmente el evento click del checkbox
        //$checkbox.click();
    });
});

var screenLoad = $('#loading-screen');

// Añadir funciones manuales para manejar otros procesos de JavaScript
// Asignar funciones a variables globales
function showLoadingScreen() {
    screenLoad.fadeIn();
};

function hideLoadingScreen() {
    screenLoad.fadeOut();
};
