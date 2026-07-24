$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    // Mostrar/ocultar campos decimales y unidad según el tipo seleccionado
    function actualizarCamposPorTipo() {
        var tipo = $('#tipo').val();
        if (tipo === 'boolean') {
            $('#row-decimales').hide();
            $('#row-unidad').hide();
        } else if (tipo === 'text') {
            $('#row-decimales').hide();
            $('#row-unidad').show();
        } else {
            $('#row-decimales').show();
            $('#row-unidad').show();
        }
    }

    $('#tipo').on('change', actualizarCamposPorTipo);
    actualizarCamposPorTipo(); // ejecutar al cargar para editar
});
