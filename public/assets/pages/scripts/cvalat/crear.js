$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#nombre").focus();

    $('#btn-agregar-detalle').click(function() {
        var index = $('#tabla-detalles tbody tr').length;
        var newRow = '<tr>' +
            '<td>' +
                '<input type="hidden" name="detalles[' + index + '][id]" value="">' +
                '<input type="text" name="detalles[' + index + '][nombredet]" class="form-control" required>' +
            '</td>' +
            '<td>' +
                '<input type="text" name="detalles[' + index + '][descdet]" class="form-control" required>' +
            '</td>' +
            '<td>' +
                '<input type="text" name="detalles[' + index + '][ordendet]" class="form-control" required>' +
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
