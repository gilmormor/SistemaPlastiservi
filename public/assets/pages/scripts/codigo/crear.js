$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#desc").focus();

    /* $('#btn-agregar-detalle').on('click', function () {
        let fila = `
            <tr>
                <td><input type="text" name="detalles[][descdet]" class="form-control" required></td>
                <td><button type="button" class="btn btn-danger btn-sm btn-eliminar-detalle"><i class="fa fa-trash"></i></button></td>
            </tr>
        `;
        $('#tabla-detalles tbody').append(fila);
    });

    $(document).on('click', '.btn-eliminar-detalle', function () {
        const fila = $(this).closest('tr');
        swal({
            title: "¿Eliminar detalle?",
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            buttons: ["Cancelar", "Eliminar"],
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                fila.remove();
            }
        });
    }); */

    // Agregar nuevo detalle
    $('#btn-agregar-detalle').click(function() {
        var index = $('#tabla-detalles tbody tr').length;
        var newRow = '<tr>' +
            '<td>' +
                '<input type="hidden" name="detalles[' + index + '][id]" value="">' +
                '<input type="text" name="detalles[' + index + '][descdet]" class="form-control" required>' +
            '</td>' +
            '<td>' +
                '<button type="button" class="btn btn-danger btn-sm btn-eliminar-detalle"><i class="fa fa-trash"></i></button>' +
            '</td>' +
        '</tr>';
        
        $('#tabla-detalles tbody').append(newRow);
    });

    // Eliminar detalle
    $(document).on('click', '.btn-eliminar-detalle', function() {
        $(this).closest('tr').remove();
        // Reindexar los nombres de los inputs
        $('#tabla-detalles tbody tr').each(function(index) {
            $(this).find('input').each(function() {
                var name = $(this).attr('name');
                name = name.replace(/\[\d+\]/, '[' + index + ']');
                $(this).attr('name', name);
            });
        });
    });
});
