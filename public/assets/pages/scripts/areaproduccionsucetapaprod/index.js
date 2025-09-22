$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');


    $(".numerico").numeric();
    nombreTabla = "#tabla-data-areaproduccionsucetapaprod";
    $(nombreTabla).append(encabezadoTabla());
    configurarTabla(nombreTabla,"",false)

});
$(document).on('input', '.edit-obs', function() {
    var fila = $(this).attr('fila');
    var prop = $(this).attr('nameobsg');
    var texto = $(this).val();
    
    $('#producto_id' + fila).attr(prop, texto);
    
    // Debug: Ver en consola qué se está actualizando
    console.log(
        'Se actualizó producto_id' + fila, 
        'con el texto:', texto
    );
});

function claseformatonumerico(clase,numdecimales){
    $(clase).numeric({ negative: false, decimalPlaces: numdecimales });
    $(clase).blur(function(e){
            /* console.log("Blur val(): " + $(this).val());
            console.log("Blur valor: " + $(this).attr('valor'));
            console.log("Blur MASKLA(): " + MASKLA($(this).val(),numdecimales)); */
            //$(this).attr('valor',$(this).val());
            //$(this).val(MASK(0, $(this).val(), '-###,###,###,##0.00',1));
            //$(this).val(MASKLA($(this).val(),numdecimales));
            $(this).val(MASKLA($(this).attr('valor'),numdecimales));
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



//Cuando el usuario hace clic en "Consultar", activa `serverSide: true`
$("#btnconsultar").click(function () {
    /* $("#tabla-data-picking").DataTable().destroy();
    $("#tabla-data-picking").empty(); // Limpia la tabla para evitar errores de redibujado */
    //$('#tabla-data-picking').html("");

    //console.log("Ejecutando consulta AJAX...");
    var data = datosareaproduccionsucetapaprod();
    //var newUrl = "/pickingpage/" + data.data2;
    var newUrl = "/areaproduccionsucetapaprodpage/" + data.data2;
    
    
    configurarTabla("#tabla-data-areaproduccionsucetapaprod",newUrl,true); // Reinicia DataTable con `serverSide: true`
});

function datosareaproduccionsucetapaprod(){
    var data1 = {
        sucursal_id       : $("#sucursal_id").val(),
        _token            : $('input[name=_token]').val()
    };

    var data2 = "?sucursal_id="+data1.sucursal_id +
    "&_token="+data1._token;

    var data = {
        data1 : data1,
        data2 : data2
    };
    return data;
}

function encabezadoTabla(){
    let html = `
            <thead>
                <tr>
                    <th>ID</th>
                    <th title='Area de Produccion'>Area de produccion</th>
                    <th title='Sucursal'>Sucursal</th>
                    <th title='Etapas de Produccion'>Etapas de Produccion</th>
                    <th class="width80">Acción</th>
                </tr>
            </thead>
            <tfoot>
            </tfoot>
        `;
    return html;
}

var tabla;

function configurarTabla(nombreTabla,url,serverSide) {
    // Si ya hay una tabla inicializada, la destruimos
    if ($.fn.DataTable.isDataTable(nombreTabla)) {
        $(nombreTabla).DataTable().destroy();
        $(nombreTabla).empty(); // Limpia la tabla para evitar errores de redibujado
    }

    //Asegura que la tabla tiene el encabezado correcto
    if ($(nombreTabla + " thead").length === 0) {
        $(nombreTabla).append(encabezadoTabla());
    }

    // Configura DataTable con `serverSide` dinámico
    tabla = $(nombreTabla).DataTable({
        'paging'      : true,
        'scrollx'     : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : serverSide, // Se define dinámicamente
        'ajax'        : url, // Se define dinámicamente
        'order'       : [[ 0, "asc" ]],
        'columns'     : [
            /* {
                className: 'dt-control ocultar',
                orderable: false,
                data: null,
                defaultContent: '<i class="btn-accion-tabla btn-sm glyphicon glyphicon-triangle-right text-aqua" title="Mostrar Detalle"></i>'
            }, */
            {data: 'id'}, // 1
            {data: 'areaproduccion_nombre'}, // 2
            {data: 'sucursal_nombre'}, // 3
            {data: 'etapaprod_nombre'}, // 4
            {defaultContent : 
                ``
            }
        ],
        "language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            $(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);

            aux_text = 
                `<a id="bntorden${data.id}" name="bntorden${data.id}" class="btn-accion-tabla btn-sm tooltipsC" onclick="ordenetapaprod(${data.id})" title="Editar Orden Etapas de Produccion">
                    <span class="glyphicon glyphicon-sort-by-attributes" style="bottom: 0px;top: 2px;"></span>
                </a>
                <a href='areaproduccionsucetapaprod' class='btn-accion-tabla btnEditar' title='Editar este registro'>
                    <i class='fa fa-fw fa-pencil'></i>
                </a>`;
            $('td',row).eq(4).html(aux_text);

        }
    });
    // Aplicar el plugin numeric a los inputs dinámicos después de cada redibujado de la tabla
    tabla.on('draw', function() {
        claseformatonumerico(".numerico",0);
    });

    // Add event listener for opening and closing details

    
};

/* $("#tabla-data-areaproduccionsucetapaprod").on('click', 'td.dt-control', function (e) {
    let tr = e.target.closest('tr');
    let row = tabla.row(tr);

    if (row.child.isShown()) {
        // This row is already open - close it
        d = row.data();
        //$("#producto_id" + d.id).attr(aux_obsext)
        $("#producto_id"+d.id).attr('aux_obsext',$("#obsext" + d.id).val());
        $("#producto_id"+d.id).attr('aux_obsimp',$("#obsimp" + d.id).val());
        $("#producto_id"+d.id).attr('aux_obssell',$("#obssell" + d.id).val());
    
        row.child.hide();
        $(this).html('<i class="btn-accion-tabla btn-sm glyphicon glyphicon-triangle-right text-aqua" title="Mostrar Detalle"></i>');
    }
    else {
        // Open this row
        row.child(format(row.data())).show();
        $(this).html('<i class="btn-accion-tabla btn-sm glyphicon glyphicon-triangle-bottom text-aqua" title="Mostrar Detalle"></i>');
    }
});

// Formatting function for row details - modify as you need
function format(d) {
    tableHtml =
    `
            <div style="display: flex; align-items: flex-start;"> <!-- Contenedor Flex (flecha al principio) -->
                <div style="margin-left: 20px;">&#8627;</div> <!-- Flecha desplazada un poco a la derecha -->
                </div>
            </div>`;
    //console.log(tableHtml);
    // Devolver la tabla HTML
    return tableHtml;
    
} */

function ordenetapaprod(id){
    $(this).val("");
    $(".input-sm").val('');
    //data = datosproducto();
    nombreTabla = '#tabla-data-OrdenEtapaProd';

    $(nombreTabla).DataTable().ajax.url( "areaproduccionsucetapaprod/sucetapaprodpage/?areaproduccion_id=" + id ).load();
    $('#myModalOrdenEtapaProd').modal('show');
}