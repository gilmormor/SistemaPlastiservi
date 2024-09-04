$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    $('#tabla-datadesc').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "notaventadevolvervenpage",
        "order": [[ 0, "desc" ]],
        'columns'     : [
            {data: 'id'},
            {data: 'cotizacion_id'},
            {data: 'fechahora'},
            {data: 'razonsocial'},
            {data: 'nombrevendedor'},
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : ""+
                "<a class='btn-accion-tabla btn-sm verpdf1' title='Nota de venta' data-toggle='tooltip'>"+
                "<i class='fa fa-fw fa-file-pdf-o'></i></a>"+
                "<a class='btn-accion-tabla btn-sm verpdf2' title='Precio x Kg' data-toggle='tooltip'>"+
                "<i class='fa fa-fw fa-file-pdf-o'></i></a>"},
            {defaultContent : ""+
                "<a class='btn btn-primary btn-xs tooltipsC btndevnvven' title='Devolver a vendedor'>"+
                "<i class='fa fa-fw fa-reply'></i></a>"},
        ],
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ) {
            $(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);
            $(row).attr('updated_at',data.updated_at);
            codigo = data.cotizacion_id;
            if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)){
                aux_text = "";
            }else{
                aux_text = 
                "<a href='#' class='tooltipsC' title='Cotización' onclick='genpdfCOT(\"" + data.cotizacion_id + "\",1)'>" + data.cotizacion_id + "</a>";
            }
            $('td', row).eq(1).html(aux_text);

            aux_text = 
                "<a class='btn-accion-tabla btn-sm verpdf1 tooltipsC' title='Nota de venta: " + data.id + "' >"+
                "<i class='fa fa-fw fa-file-pdf-o'></i></a>"+
                "<a class='btn-accion-tabla btn-sm verpdf2 tooltipsC' title='Precio x Kg: " + data.id + "' >"+
                "<i class='fa fa-fw fa-file-pdf-o'></i></a>";
            $('td', row).eq(5).html(aux_text);

            aux_text = 
                "<a class='btn btn-primary btn-xs tooltipsC btndevnvven' title='Devolver a vendedor: " + data.id + "' >"+
                "<i class='fa fa-fw fa-reply'></i></a>";
            $('td', row).eq(6).html(aux_text);
        }
      });

      $('#tabla-dataanularnv').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "anularnotaventapage",
        'columns'     : [
            {data: 'id'},
            {data: 'cotizacion_id'},
            {data: 'fechahora'},
            {data: 'razonsocial'},
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : ""+
                "<a class='btn-accion-tabla btn-sm verpdf1' title='Nota de venta' data-toggle='tooltip'>"+
                "<i class='fa fa-fw fa-file-pdf-o'></i></a>"+
                "<a class='btn-accion-tabla btn-sm verpdf2' title='Precio x Kg' data-toggle='tooltip'>"+
                "<i class='fa fa-fw fa-file-pdf-o'></i></a>"},
            {defaultContent : ""+
                "<a class='btn btn-primary btn-xs tooltipsC btndevnvven' title='Devolver a vendedor'>"+
                "<i class='fa fa-fw fa-reply'></i></a>"},
        ],
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
      });

});


function devNVvend(id,nfila){
    swal({
        title: 'Devolver Nota Venta Nro: ' + id + ' ?',
        text: "",
        icon: 'warning',
        buttons: {
            confirm: "Aceptar",
            cancel: "Cancelar"
        },
    }).then((value) => {
        if (value) {
            var data = {
                id: id,
                updated_at: $("#fila" + id).attr("updated_at"),
                _token: $('input[name=_token]').val()
            };
        
            $.ajax({
                url: '/notaventadevolvend/actualizarreg',
                type: 'POST',
                data: data,
                success: function (respuesta) {
                    if (respuesta.mensaje == "ok") {
                        nfila.remove();
                        //$("#fila"+nfila).remove();
                        Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
                    } else {
                        if (respuesta.mensaje == "sp"){
                            Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
                        }else{
                            Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', 'error');
                        }
                    }
                }
            });
        
        }
    });


};

function datos(){
    var data = {
        id: $("#fechad").val(),
        fechah: $("#fechah").val(),
        categoriaprod_id: $("#categoriaprod_id").val(),
        giro_id: $("#giro_id").val(),
        rut: eliminarFormatoRutret($("#rut").val()),
        vendedor_id: $("#vendedor_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        _token: $('input[name=_token]').val()
    };
    return data;
}

function anularNV(id,nfila){
    swal({
        title: 'Anular Nota Venta Nro: ' + id + ' ?',
        text: "",
        icon: 'warning',
        buttons: {
            confirm: "Aceptar",
            cancel: "Cancelar"
        },
    }).then((value) => {
        if (value) {
            var data = {
                id: id,
                _token: $('input[name=_token]').val()
            };
        
            $.ajax({
                url: '/notaventadevolvend/anular/actualizanular',
                type: 'POST',
                data: data,
                success: function (respuesta) {
                    if (respuesta.mensaje == "ok") {
                        $("#fila"+nfila).remove();
                        Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
                    } else {
                        if (respuesta.mensaje == "sp"){
                            Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
                        }else{
                            Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
                        }
                    }
                }
            });
        
        }
    });


};

$(document).on("click", ".verpdf1", function(){
    //alert('entro');
    opcion = 2;//editar
    fila = $(this).closest("tr");	        
    id = fila.find('td:eq(0)').text();
    genpdfNV(id,"1")
    //alert('Id: '+id);
    // *** REDIRECCIONA A UNA RUTA*** 
    //var loc = window.location;
    //window.location = loc.protocol+"//"+loc.hostname+"/cliente/"+id+"/editar";
    // ****************************** 
});

$(document).on("click", ".verpdf2", function(){
    //alert('entro');
    opcion = 2;//editar
    fila = $(this).closest("tr");	        
    id = fila.find('td:eq(0)').text();
    genpdfNV(id,"2")
    //alert('Id: '+id);
    // *** REDIRECCIONA A UNA RUTA*** 
    //var loc = window.location;
    //window.location = loc.protocol+"//"+loc.hostname+"/cliente/"+id+"/editar";
    // ****************************** 
});

$(document).on("click", ".btndevnvven", function(){	
    opcion = 2;//editar
    fila = $(this).closest("tr");	        
    id = fila.find('td:eq(0)').text();
    devNVvend(id,fila);
    //alert('Id: '+id);
    // *** REDIRECCIONA A UNA RUTA*** 
});

