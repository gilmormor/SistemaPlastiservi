$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
	configurarTablageneral('#tabla-datadesc')
});


function devNVvend(id){
    var data = {
        id: id,
        _token: $('input[name=_token]').val()
    };

	$.ajax({
        url: '/notaventadevolvend/actualizarreg',
        type: 'POST',
        data: data,
        success: function (datos) {
			/*
            if(datos['tabla'].length>0){
                $("#tablaconsulta").html(datos['tabla']);
                configurarTabla('.tablascons');
			}
			*/
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