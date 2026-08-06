$(document).ready(function () {
    toggleBodegasPermitidas();
    $("#modulo").on('change', function () {
        toggleBodegasPermitidas();
    });
});

function toggleBodegasPermitidas() {
    if ($("#modulo").val() == 'inventsalaprobar') {
        $("#div_invbodega_id").show();
    } else {
        $("#div_invbodega_id").hide();
        $("#invbodega_id").val(null);
        $("#invbodega_id").selectpicker('refresh');
    }
}
