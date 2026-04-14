$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $(".numerico").numeric();
    nombreTabla = "#tabla-data-OrdenEtapaProd";
    $(nombreTabla).append(encabezadoTablabuscar());
    configurarTablabuscar(nombreTabla,"areaproduccionsucetapaprod/sucetapaprodpage/?areaproduccion_id=0",false)
    //console.log("UNIDADMEDIDAS: " + UNIDADMEDIDAS);
    //console.log(JSON.stringify(UNIDADMEDIDAS, null, 2));
    /* unidadesMedida = JSON.parse(UNIDADMEDIDAS);
    console.log(unidadesMedida); */
});

function encabezadoTablabuscar(){
    let html = `
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Orden</th>
                    <th title='Unidad Medida'>UnidMed</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tfoot>
            </tfoot>
        `;
    return html;
}

function configurarTablabuscar(nombreTabla,url,serverSide) {
    // Si ya hay una tabla inicializada, la destruimos
    if ($.fn.DataTable.isDataTable(nombreTabla)) {
        $(nombreTabla).DataTable().destroy();
        $(nombreTabla).empty(); // Limpia la tabla para evitar errores de redibujado
    }

    //Asegura que la tabla tiene el encabezado correcto
    if ($(nombreTabla + " thead").length === 0) {
        $(nombreTabla).append(encabezadoTablabuscar());
    }

    // Configura DataTable con `serverSide` dinámico
    tabla = $(nombreTabla).DataTable({
        'paging'      : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : serverSide, // Se define dinámicamente
        'ajax'        : url,
        'order'       : [[ 2, "asc" ]],
        'columns'     : [
            {data: 'id'},
            {data: 'etapaprod_nombre'},
            {data: 'orden'},
            {data: 'unidadmedida_id'},
            {data: 'updatednum_at'}
        ],
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            //aux_nfila++;

            aux_text = 
                `<input type="text" name="orden[]" id="orden${data.id}" class="form-control numerico" value="${data.orden}" valor="${data.orden}" item="${data.id}" style="width: 100px;text-align:right;" maxlength="3" valororiginal="${data.orden}"/>`;
            $('td', row).eq(2).html(aux_text);

            // Campo unidadmedida_id (select Bootstrap 3)
            let select = `<select class="form-control input-sm" 
                            name="unidadmedida_id[]" id="unidadmedida_id${data.id}" 
                            style="width:150px;">
                            <option value="">Seleccione...</option>`;

            UNIDADMEDIDAS.forEach(function(um) {
                const selected = (um.id == data.unidadmedida_id) ? 'selected' : '';
                select += `<option value="${um.id}" ${selected}>${um.nombre}</option>`;
            });
            select += `</select>`;

            $('td', row).eq(3).html(select);
            
            aux_text = 
                `<a name="savefed${data.id}" id="savefed${data.id}" class="btn-accion-tabla btn-sm tooltipsC savefed" title="Guardar" onclick="saveordenetapaprod(${data.id},${data.updatednum_at})" updated_at="${data.updated_at}">
                    <i class="fa fa-fw fa-save text-red"></i>
                </a>`;
            $('td', row).eq(4).html(aux_text);
        }
    });
    // Aplicar el plugin numeric a los inputs dinámicos después de cada redibujado de la tabla
    tabla.on('draw', function() {
        claseformatonumerico(".numerico",0);
        //claseformatonumerico(".numerico3d",3);
        //$('#tabla-data-factura th, #tabla-data-factura td').css('font-size', '12px');

    });
};

function claseformatonumerico(clase,numdecimales){
    $(clase).numeric({ negative: false, decimalPlaces: numdecimales });
    $(clase).blur(function(e){
            /* console.log("Blur val(): " + $(this).val());
            console.log("Blur valor: " + $(this).attr('valor'));
            console.log("Blur MASKLA(): " + MASKLA($(this).val(),numdecimales)); */
            //$(this).attr('valor',$(this).val());
            //$(this).val(MASK(0, $(this).val(), '-###,###,###,##0.00',1));
            //$(this).val(MASKLA($(this).val(),numdecimales));
            $(this).val($(this).attr('valor'));
    });
    $(clase).focus(function(e){
        if($(this).attr('valor') != undefined){
            $(this).val($(this).attr('valor'));
        }
    });

    // Evento keydown para .numerico
    $(clase).on("keyup", function(event) {
        // Si el usuario presiona la tecla de coma ","
        if (event.key === ",") {
            // Prevenir la acción solo si aún no hay un punto en el input
            if (!$(this).val().includes(".")) {
                event.preventDefault();
                $(this).val($(this).val() + ".");
            } else {
                event.preventDefault(); // Evita que se escriba la coma si ya hay un punto
            }
        }
        aux_val = $(this).val();
        if($(this).val() == ""){
            aux_val = 0;
        }
        if($(this).val() == 0){
            aux_val = 0;
        }
        $(this).attr('valor',aux_val);
    });


}

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

function saveordenetapaprod(id,updatednum_at){
    swal({
        title: '¿ Seguro desea actualizar el registro ?',
        text: "Esta acción no se puede deshacer!",
        icon: 'warning',
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
        if (value) {
            var data = {
                areaproduccionsucetapaprod_id : id,
                unidadmedida_id : $("#unidadmedida_id" + id).val(),
                orden : $("#orden" + id).val(),
                updatednum_at  : updatednum_at,
                _token : $('input[name=_token]').val()
            };
            var ruta = '/areaproduccionsucetapaprod/guardarordenetapaprod'; //Guardar Fecha estimada de despacho
            ajaxRequestGeneral(data,ruta,'procesarDTE');
        }
    });
}