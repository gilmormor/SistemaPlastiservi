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
                    <th>Etapa</th>
                    <th>Orden</th>
                    <th title='UM Entrada (deducida de la etapa anterior)'>UM Ent.</th>
                    <th title='UM Salida de esta etapa'>UM Sal.</th>
                    <th title='Requiere capturar kg producidos'>Req Kg</th>
                    <th title='Requiere pasar por Control de Calidad'>Req CC</th>
                    <th title='Consume materia prima en esta etapa'>Usa MP</th>
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
            {data: 'unidadmedida_entrada_nombre'},
            {data: 'unidadmedida_id'},
            {data: 'requiere_kg'},
            {data: 'requiere_cc'},
            {data: 'usa_matprima'},
            {data: 'updatednum_at'}
        ],
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            // Col 2: orden editable
            aux_text =
                `<input type="text" name="orden[]" id="orden${data.id}" class="form-control numerico" value="${data.orden}" valor="${data.orden}" item="${data.id}" style="width: 80px;text-align:right;" maxlength="3" valororiginal="${data.orden}" data-rowid="${data.id}"/>`;
            $('td', row).eq(2).html(aux_text);

            // Col 3: UM entrada (readonly, deducida de etapa anterior)
            $('td', row).eq(3).html(
                `<span style="color:#777;font-style:italic;">${data.unidadmedida_entrada_nombre ?? '—'}</span>`
            );

            // Col 4: UM salida (select editable)
            let select = `<select class="form-control input-sm"
                            name="unidadmedida_id[]" id="unidadmedida_id${data.id}"
                            style="width:130px;" data-rowid="${data.id}">
                            <option value="">Seleccione...</option>`;
            UNIDADMEDIDAS.forEach(function(um) {
                const selected = (um.id == data.unidadmedida_id) ? 'selected' : '';
                select += `<option value="${um.id}" ${selected}>${um.nombre}</option>`;
            });
            select += `</select>`;
            $('td', row).eq(4).html(select);

            // Col 5: Req Kg (checkbox)
            $('td', row).eq(5).html(
                `<input type="checkbox" id="requiere_kg${data.id}" ${data.requiere_kg == 1 ? 'checked' : ''} style="transform:scale(1.3);" data-rowid="${data.id}"/>`
            );
            // Col 6: Req CC (checkbox)
            $('td', row).eq(6).html(
                `<input type="checkbox" id="requiere_cc${data.id}" ${data.requiere_cc == 1 ? 'checked' : ''} style="transform:scale(1.3);" data-rowid="${data.id}"/>`
            );
            // Col 7: Usa MP (checkbox)
            $('td', row).eq(7).html(
                `<input type="checkbox" id="usa_matprima${data.id}" ${data.usa_matprima == 1 ? 'checked' : ''} style="transform:scale(1.3);" data-rowid="${data.id}"/>`
            );

            // Col 8: Acción — botón oculto, se muestra al detectar cambio en la fila
            aux_text =
                `<a name="savefed${data.id}" id="savefed${data.id}" class="btn-accion-tabla btn-sm tooltipsC savefed" title="Guardar" onclick="saveordenetapaprod(${data.id},${data.updatednum_at})" updated_at="${data.updated_at}" style="display:none;">
                    <i class="fa fa-fw fa-save text-red"></i>
                </a>`;
            $('td', row).eq(8).html(aux_text);
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

// Mostrar botón guardar al detectar cualquier cambio en los campos editables de la fila
$(document).on('change', '#tabla-data-OrdenEtapaProd input[data-rowid], #tabla-data-OrdenEtapaProd select[data-rowid]', function() {
    $('#savefed' + $(this).data('rowid')).show();
});

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
                orden           : $("#orden" + id).val(),
                requiere_kg     : $("#requiere_kg" + id).is(':checked')  ? 1 : 0,
                requiere_cc     : $("#requiere_cc" + id).is(':checked')  ? 1 : 0,
                usa_matprima    : $("#usa_matprima" + id).is(':checked') ? 1 : 0,
                updatednum_at   : updatednum_at,
                _token          : $('input[name=_token]').val()
            };
            $.ajax({
                url  : '/areaproduccionsucetapaprod/guardarordenetapaprod',
                type : 'POST',
                data : data,
                success: function(respuesta) {
                    if (respuesta.id != 0) {
                        // Ocultar botón guardar tras guardar exitosamente
                        $('#savefed' + id).hide();
                        Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', 'success');
                    } else {
                        swal({
                            title: 'Error',
                            text : respuesta.mensaje,
                            icon : respuesta.tipo_alert,
                            buttons: { confirm: "Aceptar" },
                        });
                    }
                }
            });
        }
    });
}