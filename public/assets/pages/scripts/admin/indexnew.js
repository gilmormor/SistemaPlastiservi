$(document).ready(function () {

    $(document).on("click", ".btnEditar", function(event){	
        event.preventDefault();
        opcion = 2;//editar
        fila = $(this).closest("tr");	        
        id = fila.find('td:eq(0)').text();
        updated_at = "";
        if (fila.find('td:eq(0)').attr("updated_at") !== undefined){
            updated_at = "&" + fila.find('td:eq(0)').attr("updated_at");
        }
        form = $(this);
        var loc = window.location;
        //alert(loc.protocol+"//"+loc.hostname+"/"+form.attr('href')+"/"+id+"/editar");
        window.location = loc.protocol+"//"+loc.hostname+"/"+form.attr('href')+"/"+id+updated_at+"/editar";
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
        updated_at = $("#updated_at"+id).html();
        //alert(id);
        var data = {
            _token  : $('input[name=_token]').val(),
            _method : 'delete',
            id      : id,
            updated_at: updated_at
        };
        if (value) {
            ajaxRequest(data,form.attr('href')+'/'+id,'eliminar',form);
        }
    });
    
});

$(document).on("click", ".btnAnular", function(event){
    event.preventDefault();
    swal({
        title: '¿ Está seguro que desea anular el registro ?',
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
        aux_updated_at = "";
        if(fila.find('.updated_at').text()){
            aux_updated_at = fila.find('.updated_at').text();
        }
        //alert(id);
        var data = {
            _token     : $('input[name=_token]').val(),
            updated_at : aux_updated_at,
            id         : id
        };
        /* Santa Ester
            _token  : $('input[name=_token]').val(),
            id      : id,
            updated_at : $("#updated_at" + id).html()
        */
        if (value) {
            ajaxRequest(data,form.attr('href')+'/'+id+'/anular','anular',form);
        }
    });
    
});

$(document).on("click", ".btnaprobar", function(event){
    event.preventDefault();
    swal({
        title: '¿ Está seguro que desea aprobar el registro ?',
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
        aux_updated_at = "";
        if(fila.find('.updated_at').text()){
            aux_updated_at = fila.find('.updated_at').text();
        }

        //alert(id);
        var data = {
            _token     : $('input[name=_token]').val(),
            id         : id,
            updated_at : aux_updated_at
        };
        if (value) {
            ajaxRequest(data,form.attr('href')+'/'+id,'btnaprobar',form);
        }
    });
    
});


function ajaxRequest(data,url,funcion,form = false) {
    dataIni = data;
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success: function (respuesta) {
            if(funcion=='eliminar'){
                if (respuesta.mensaje == "ok") {
                    form.parents('tr').remove();
                    Biblioteca.notificaciones('El registro fue procesado correctamente.', 'Plastiservi', 'success');
                } else {
                    if (respuesta.mensaje == "sp"){
                        Biblioteca.notificaciones('Usuario no tiene permiso para eliminar.', 'Plastiservi', 'error');
                    }else{
                        if(respuesta.mensaje == "cr"){
                            Biblioteca.notificaciones('No puede ser procesado: ID tiene registros relacionados en otras tablas.', 'Plastiservi', 'error');
                        }else{
                            if(respuesta.mensaje == "ne"){
                                Biblioteca.notificaciones('No tiene permiso para eliminar.', 'Plastiservi', 'error');
                            }else{
                                
                                switch (respuesta.id) {
                                    case 0:
                                        Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                                        break;
                                    case 1:
                                        Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                                        break;
                                    default:
                                        Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo.', 'Plastiservi', 'error');
                                }
                            }
                        }
                    }
                }
            }
            if(funcion=='anular'){
                if (respuesta.error == "1"){
                    aux_texto = "<a class='btn-accion-tabla btn-sm tooltipsC' data-toggle='tooltip' data-original-title='Anulada'>" +
                                    "<span class='glyphicon glyphicon-remove text-danger'></span>" +
                                "</a>";
                    form.parents('td').html(aux_texto);
                }
                Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
            }
            if(funcion=='verUsuario'){
                $('#myModal .modal-body').html(respuesta);
                $("#myModal").modal('show');
            }
            if(funcion=='btnaprobar'){
                if ('error' in respuesta){
                    if (respuesta.error == 0){
                        form.parents('tr').remove();
                    }
                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                }else{
                    switch (respuesta.mensaje) {
                        case 'ok':
                            form.parents('tr').remove();
                            Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');    
                            break;
                        case 'sp':
                            form.parents('tr').remove();
                            Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
                            break;
                        case 'MensajePersonalizado':
                            Biblioteca.notificaciones(respuesta.menper, 'Plastiservi', 'error');
                            break;
                        default:
                            switch (respuesta.id) {
                                case 0:
                                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                                    break;
                                case 1:
                                    Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
                                    break;
                                default:
                                    Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
                                    break;
                                }
                    }
                }
            }
            if(funcion == "pregunta"){
                if (respuesta.respuesta == 1) {
                    if(dataIni.removerItem){
                        form.parents('tr').remove();
                    }
                }
                Biblioteca.notificaciones(respuesta.mensaje, 'Plastiservi', respuesta.tipo_alert);
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

$(document).on("click", ".btnVar", function(event){
    event.preventDefault();
    swal({
        title: '¿ Desea continuar ?',
        text: "Esta acción no se puede deshacer!",
        icon: 'warning',
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
        fila = $(this).closest("tr");
        form = $(this);
        accion = form.attr("accion") ? form.attr("accion") : "pregunta";
        aux_updated_at = form.attr("updated_at");
        id = fila.find('td:eq(0)').text();
        var data = {
            id          : id,
            updated_at  : aux_updated_at,
            removerItem : true,
            _token      : $('input[name=_token]').val()
        };
        if (value) {
            ajaxRequest(data,form.attr('href'),accion,form);
        }
    });
    
});