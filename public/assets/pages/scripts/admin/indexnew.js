$(document).ready(function () {
    $("#tabla-data").on('submit', '.form-eliminar', function (event) {
        event.preventDefault();
        const form = $(this);
        swal({
            title: '¿ Está seguro que desea eliminar el registro ?',
            text: "Esta acción no se puede deshacer!",
            icon: 'warning',
            buttons: {
                cancel: "Cancelar",
                confirm: "Aceptar"
            },
        }).then((value) => {
            if (value) {
                ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
            }
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
                ajaxRequest(data,'cliente/1','eliminar',form);
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
                        Biblioteca.notificaciones('El registro fue eliminado correctamente', 'Plastiservi', 'success');
                    } else {
                        if (respuesta.mensaje == "sp"){
                            Biblioteca.notificaciones('Usuario no tiene permiso para eliminar.', 'Plastiservi', 'error');
                        }else{
                            if(respuesta.mensaje == "cr"){
                                Biblioteca.notificaciones('Id tiene registros relacionados en otras tablas.', 'Plastiservi', 'error');
                            }else{
                                if(respuesta.mensaje == "ne"){
                                    Biblioteca.notificaciones('No tiene permiso para eliminar.', 'Plastiservi', 'error');
                                }else{
                                    Biblioteca.notificaciones('El registro no pudo ser eliminado, hay recursos usandolo', 'Plastiservi', 'error');
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
    
});

