$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    var data = {
        _token: $('input[name=_token]').val()
    };
    ajaxRequestI('/admin/permiso-rol/encabezadoTabla', data,"encabezadotabla");


});

function ajaxRequestI (url, data,funcion) {
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success: function (respuesta) {
            if(funcion == "darPermiso"){
                tipoMensaje = 'warning';
                if(data.estado == 1){
                    tipoMensaje = 'success';
                }
                Biblioteca.notificaciones(respuesta.respuesta, 'Plastiservi', tipoMensaje);    
            }
            if(funcion == "encabezadotabla"){
                //console.log(respuesta);
                $("#tabla-data").html(respuesta.encabezadotabla)
                campos = [
                    {data: 'id'},
                    {data: 'nombre'},
                ]
                /*
                console.log(respuesta.campos_id.length);
                console.log(respuesta.campos);*/
                for (let i = 0; i < respuesta.campos_id.length; i++) {
                    campos.push({data: respuesta.campos_id[i]})
                }
                //console.log(campos);
                $('#tabla-data').DataTable({
                    'paging'      : true, 
                    'lengthChange': true,
                    'searching'   : true,
                    'ordering'    : true,
                    'info'        : true,
                    'autoWidth'   : false,
                    'processing'  : true,
                    'serverSide'  : true,
                    'ajax'        : "permisorolpage",
                    'columns'     : campos,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
                    },
                    "createdRow": function ( row, data, index ) {
                        $(row).attr('id','fila' + data.id);
                        $(row).attr('name','fila' + data.id);
                        for (let i = 0; i < respuesta.campos_id.length; i++) {
                            campos_id = respuesta.campos_id[i];
                            campos_id = campos_id.trim();
                            nombretd = data.id+campos_id;
                            aux_text = `<input
                            id = ${nombretd}
                            name = ${nombretd}
                            onclick='permiso_rol(\"${nombretd}\",${data.id},${respuesta.rol_id[i]})'
                            type="checkbox"
                            class="permiso_rol"
                            name="permiso_rol[]"
                            data-permisoid=${data.id}
                            value="${respuesta.rol_id[i]}" ${data[campos_id]}>`;
                            $('td', row).eq(i+2).html(aux_text);
                        }        
                    }
                });
            
            }
        }
    });
}
/*
function activarClases(){
    $('.permiso_rol').on('change', function () {
        var data = {
            permiso_id: $(this).data('permisoid'),
            rol_id: $(this).val(),
            _token: $('input[name=_token]').val()
        };
        if ($(this).is(':checked')) {
            data.estado = 1
        } else {
            data.estado = 0
        }
        ajaxRequestI('/admin/permiso-rol', data,"darPermiso");
    });    
}
*/
function permiso_rol(nombretd,permiso_id,rol_id){
    var data = {
        permiso_id: permiso_id,
        rol_id: rol_id,
        _token: $('input[name=_token]').val()
    };
    if ($("#"+nombretd).is(':checked')) {
        data.estado = 1
    } else {
        data.estado = 0
    }
    ajaxRequestI('/admin/permiso-rol', data,"darPermiso");
}