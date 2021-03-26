$(document).ready(function () {

    $(document).on("click", ".btnEditar", function(){	
        event.preventDefault();
        opcion = 2;//editar
        fila = $(this).closest("tr");	        
        id = fila.find('td:eq(0)').text();
        form = $(this);
        var loc = window.location;
        //alert(loc.protocol+"//"+loc.hostname+"/"+form.attr('href')+"/"+id+"/editar");
        window.location = loc.protocol+"//"+loc.hostname+"/"+form.attr('href')+"/"+id+"/editar";
    });
     

    
});

$(document).on("click", ".btnEliminar", function(event){
    event.preventDefault();
    swal({
        title: '¿ Está seguro que desea eliminar el registro ?',
        text: "Esta acción no se puede deshacer!",
        icon: 'warning',
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
        fila = $(this).closest("tr");
        form = $(this);
        id = fila.find('td:eq(0)').text();
        //alert(id);
        var data = {
            _token  : $('input[name=_token]').val(),
            _method : 'delete',
            id      : id
        };
        if (value) {
            ajaxRequest(data,form.attr('href')+'/1','eliminar',form);
        }
    });
    
});


function ajaxRequest(data,url,funcion,form = false) {
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success: function (respuesta) {
            if(funcion=='eliminar'){
                if (respuesta.mensaje == "ok") {
                    form.parents('tr').remove();
                    Biblioteca.notificaciones('El registro fue eliminado correctamente.', 'Plastiservi', 'success');
                } else {
                    if (respuesta.mensaje == "sp"){
                        Biblioteca.notificaciones('Usuario no tiene permiso para eliminar.', 'Plastiservi', 'error');
                    }else{
                        if(respuesta.mensaje == "cr"){
                            Biblioteca.notificaciones('No puede ser eliminado: ID tiene registros relacionados en otras tablas.', 'Plastiservi', 'error');
                        }else{
                            if(respuesta.mensaje == "ne"){
                                Biblioteca.notificaciones('No tiene permiso para eliminar.', 'Plastiservi', 'error');
                            }else{
                                Biblioteca.notificaciones('El registro no pudo ser eliminado, hay recursos usandolo.', 'Plastiservi', 'error');
                            }
                        }
                    }
                }
            }
            if(funcion=='verUsuario'){
                $('#myModal .modal-body').html(respuesta);
                $("#myModal").modal('show');
            }
        },
        error: function () {
        }
    });
}

$(".ver-usuario").click(function(event)
{
    event.preventDefault();
    const url = $(this).attr('href');
    const data = {
        _token: $('input[name=_token]').val()
    }
    ajaxRequest(data,url,'verUsuario');
    //$("#myModal").modal('show');
});
