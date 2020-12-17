$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
	configurarTablageneral('#tabla-datadesc')
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
                _token: $('input[name=_token]').val()
            };
        
            $.ajax({
                url: '/notaventadevolvend/actualizarreg',
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