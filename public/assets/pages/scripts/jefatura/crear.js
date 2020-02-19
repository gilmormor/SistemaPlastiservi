$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $( "#nombre" ).focus();
});

$("#addRow").click(function()
{
    event.preventDefault();
    agregarFila();
});
function agregarFila(){
    //var nuevoTr = "<tr bgcolor='FFFDC1'><th>Gilmer</th><th>Moreno Moreno</th></tr>";
    var nuevoTr = "<tr><th>Gilmer</th><th>Moreno Moreno</th></tr>";
    $("#tabla-data").append(  nuevoTr );
}