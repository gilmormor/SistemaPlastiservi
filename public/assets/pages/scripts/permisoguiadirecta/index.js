$(document).ready(function () {
	if($("#permiso").val()=="1"){
		$("#permisoT").prop("checked", true);
	}else{
		$("#permisoT").prop("checked", false);
	}
});

$("#permisoT").click(function(event)
{
    if($("#permisoT").prop("checked")){
		$("#permiso").val('1');
        $("#358id7").prop("checked", true);
        $("#359id7").prop("checked", true);
        $("#361id7").prop("checked", true);
	}else{
		$("#permiso").val('0');
		$("#358id7").prop("checked", false);
		$("#359id7").prop("checked", false);
		$("#361id7").prop("checked", false);
	}

    var data = {
        menu_id: 146,
        rol_id: 7,
        _token: $('input[name=_token]').val()
    };
    if ($(this).is(':checked')) {
        data.estado = 1
    } else {
        data.estado = 0
    }
    ajaxRequestI('/admin/menu-rol', data,"PermisoMenu");

    permiso_rol("358id7",358,7)
    permiso_rol("359id7",359,7)
    permiso_rol("361id7",361,7)

});

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

function ajaxRequestI (url, data,funcion) {
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success: function (respuesta) {
            if(funcion == "PermisoMenu"){
                tipoMensaje = 'warning';
                if(data.estado == 1){
                    tipoMensaje = 'success';
                }
                Biblioteca.notificaciones(respuesta.respuesta, 'Plastiservi', tipoMensaje);    
            }

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
                $("#tabla-data").attr('style','');
                $("#tabla-data").dataTable().fnDestroy();
            
                data = datosPermisoRol();
                $('#tabla-data').DataTable({
                    'paging'      : true, 
                    'lengthChange': true,
                    'searching'   : true,
                    'ordering'    : true,
                    'info'        : true,
                    'autoWidth'   : false,
                    'processing'  : true,
                    'serverSide'  : true,
                    'ajax'        : "permisorolpage/" + data.data2,
                    'columns'     : campos,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
                    },
                    "createdRow": function ( row, data, index ) {
                        $(row).attr('id','fila' + data.id);
                        $(row).attr('name','fila' + data.id);
                        $('td', row).eq(0).attr('class','text-center');
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
                            $('td', row).eq(i+2).attr('class','text-center');
                        }        
                    }
                });
            
            }
        }
    });
}
