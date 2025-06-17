$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');


    $(".numerico").numeric();
    nombreTabla = "#tabla-data-areaproduccionsucfase";
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

function ajaxRequest(data,url,funcion) {
    datatemp = data;
    /*
    if (funcion=='eliminar') {
        console.log(data);
        console.log(url);
        return 0;
    }
    */
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			
			if(funcion=='procesar'){
				if (respuesta.mensaje == "ok") {
                    //genpdfFAC(respuesta.id,"_U");
                    $("#fila"+datatemp.nfila).remove();
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
                    swal({
						//title: 'Error',
						text: respuesta.mensaje,
						icon: 'error',
						buttons: {
							confirm: "Aceptar"
						},
					}).then((value) => {
					});
					//Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
				}
			}
            if(funcion=='consultaranularguiafact'){
				if (respuesta.mensaje == "ok") {
					//alert(respuesta.despachoord.guiadespacho);
					$("#guiadespachoanul").val(respuesta.despachoord.guiadespacho);
					//$(".requeridos").keyup();
					quitarvalidacioneach();
                    $("#guiadesp_id").val(datatemp.guiadesp_id);
                    $("#updated_at").val(datatemp.updated_at);
                    $("#statusM").val('2');
                    $(".selectpicker").selectpicker('refresh');
					$("#myModalanularguiafact").modal('show');
				} else {
					Biblioteca.notificaciones('Registro no encontrado.', 'Plastiservi', 'error');
				}
			}

            if (funcion=='anularfac') {
                if (respuesta.id == "1") {
					$("#fila" + datatemp.dte_id).remove();
                }
                //console.log(respuesta);
                Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
            }
            if (funcion=='eliminar') { //Elimino desde aqui porque debo hacer previamente varias validaciones
                if (respuesta.mensaje == "ok") {
                    var ruta = '/guardaranularguia';
                    delete datatemp._method;
                    ajaxRequest(datatemp,ruta,'guardaranularguia');
                } else {
                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                }
            }
            if (funcion=='validarupdated1') {
                if (respuesta.mensaje == "ok") {
                    var ruta = '/guiadespanul/store';
                    ajaxRequest(datatemp,ruta,'guardarguiadespanul');    
                } else {
                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                }
            }
            if (funcion=='guardarguiadespanul') {
                if (respuesta.mensaje == "ok") {
                    var ruta = '/guardaranularguia';
                    ajaxRequest(datatemp,ruta,'guardaranularguia');
                } else {
                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                }
            }
            if(funcion=='guardaranularguia'){
				if (respuesta.mensaje == "ok") {
					$("#fila" + respuesta.nfila).remove();
					$("#myModalanularguiafact").modal('hide');
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					Biblioteca.notificaciones('Registro no fue guardado.', 'Plastiservi', 'error');
				}
			}
		},
		error: function () {
		}
	});
}



//Cuando el usuario hace clic en "Consultar", activa `serverSide: true`
$("#btnconsultar").click(function () {
    /* $("#tabla-data-picking").DataTable().destroy();
    $("#tabla-data-picking").empty(); // Limpia la tabla para evitar errores de redibujado */
    //$('#tabla-data-picking').html("");

    //console.log("Ejecutando consulta AJAX...");
    var data = datosareaproduccionsucfase();
    //var newUrl = "/pickingpage/" + data.data2;
    var newUrl = "/areaproduccionsucfasepage/" + data.data2;
    
    
    configurarTabla("#tabla-data-areaproduccionsucfase",newUrl,true); // Reinicia DataTable con `serverSide: true`
});

function datosareaproduccionsucfase(){
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
                    <th title='Area'>Area</th>
                    <th title='Sucursal'>Sucursal</th>
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
        'order'       : [[ 3, "asc" ]],
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
            {defaultContent : 
                `<a href='areaproduccionsucfase' class='btn-accion-tabla btnEditar' title='Editar este registro'>
                    <i class='fa fa-fw fa-pencil'></i>
                </a>`
            }
        ],
        "language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            $(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);
            //$('td', row).eq(0).attr("updated_at",data.updatednum_at);
            
            //"<a href='#' onclick='verpdf2(\"" + data.oc_file + "\",2)'>" + data.oc_id + "</a>";
        }
    });
    // Aplicar el plugin numeric a los inputs dinámicos después de cada redibujado de la tabla
    tabla.on('draw', function() {
        claseformatonumerico(".numerico",0);
    });

    // Add event listener for opening and closing details

    
};

/* $("#tabla-data-areaproduccionsucfase").on('click', 'td.dt-control', function (e) {
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