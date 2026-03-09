$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
 
        var actionValue = null;

    // detectar que botón se presiona
    $('#form-general button[type=submit]').on('click', function () {
        actionValue = $(this).val();
    });

    $('#form-general').on('submit', function (e) {

        e.preventDefault();

        var form = this;

        var titulo = '';
        var mensaje = '';

        if (actionValue === 'migrate') {
            titulo = "¿Ejecutar migraciones?";
            mensaje = "Se ejecutarán todas las migraciones pendientes.";
        }

        if (actionValue === 'rollback') {
            titulo = "¿Ejecutar rollback?";
            mensaje = "Se revertirán migraciones de la base de datos.";
        }

        swal({
            title: titulo,
            text: mensaje,
            icon: "warning",
            buttons: true,
            dangerMode: true
        }).then(function (ok) {

            if (ok) {

                // agregar el action manualmente
                $('<input>').attr({
                    type: 'hidden',
                    name: 'action',
                    value: actionValue
                }).appendTo(form);

                form.submit();
            }

        });

    });
    
});