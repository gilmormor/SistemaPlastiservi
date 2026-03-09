$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    //configTablaProd();
    //console.log("ready buscarnew.js");
});
let tablaInsumoInicializada = false;
let tablaInsumos = null;

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
function configTablaInsumo(){
    aux_nfila = 0;
    data = datos();
    $("#tabla-data-insumos").attr('style','');
    $("#tabla-data-insumos").dataTable().fnDestroy();
    $('#tabla-data-insumos').DataTable({
        'paging'      : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "/insumobuscarpage/" + data.data2 + "&insumo_id=",
        'columns'     : [
            {data: 'id'},
            {data: 'nombre',"width": "250px"},
            {data: 'costounitario'},],
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            aux_nfila++;
            selecmultprod = false;
            //aux_onclick = "llenarlistaprod(" + aux_nfila + "," + data.id + ")";
            //console.log($("#selecmultprod").val());
            if($("#selecmultprod").val()){
                aux_onclick = "llenarlistaprod(" + aux_nfila + "," + data.id + ")";
            }else{
                aux_onclick = `copiar_codinsumo(${data.id},'',${index})`;
                //aux_onclick = "insertarTabla(" + data.id + ",'" + data.nombre + "'," + data.acuerdotecnico_id + ")";
            }


            $(row).attr('name', 'fila' + aux_nfila);
            $(row).attr('id', 'fila' + aux_nfila);
            $(row).attr('prodid', 'tooltip');
            $(row).attr('class', "btn-accion-tabla copiar_id");
            //$(row).attr('data-toggle', data.id);
            //$(row).attr('title', "Click para seleccionar insumo");
            //$(row).attr('onClick', aux_onclick + ';');
            for(i=1; i<=10; i++){
                $('td', row).eq(i).attr('onClick',  aux_onclick + ';');
                //$('td', row).eq(i).attr('data-toggle', data.id);
                //$('td', row).eq(i).addClass('tooltipsC');
                $('td', row).eq(i).attr('title', "Click para seleccionar insumo");    
            }


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