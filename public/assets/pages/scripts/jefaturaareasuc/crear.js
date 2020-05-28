$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $( "#nombre" ).focus();
    $("#btnjefe").click(function(event){
        $("#myModalJefe").modal('show');
    });
    
});


$('.region_id').on('change', function () {
    var data = {
        region_id: $(this).val(),
        _token: $('input[name=_token]').val()
    };
    $.ajax({
        url: '/sucursal/obtProvincias',
        type: 'POST',
        data: data,
        success: function (provincias) {
            $(".provincia_id").empty();
            $(".provincia_id").append("<option value=''>Seleccione...</option>");
            $(".comuna_id").empty();
            $(".comuna_id").append("<option value=''>Seleccione...</option>");
            $.each(provincias, function(index,value){
                $(".provincia_id").append("<option value='" + index + "'>" + value + "</option>")
            });
        }
    });
});

$('.provincia_id').on('change', function () {
    var data = {
        provincia_id: $(this).val(),
        _token: $('input[name=_token]').val()
    };
    $.ajax({
        url: '/sucursal/obtComunas',
        type: 'POST',
        data: data,
        success: function (comuna) {
            $(".comuna_id").empty();
            $(".comuna_id").append("<option value=''>Seleccione...</option>");
            $.each(comuna, function(index,value){
                $(".comuna_id").append("<option value='" + index + "'>" + value + "</option>")
            });
        }
    });
});

$("#guardarJefe").click(function(event){
    cont=$("#guardarJefe").attr("items");
    aux_vectorJ=[];
    for (i = 0; i < cont; i++) {
        aux_vectorJ[i] = [$("#jefatura"+i).attr('jefatura_id'), $("#personal_idD"+i).val()];
        //alert(aux_vectorJ[i]);
        //alert($("#personal_idD"+i).val());
    }
    var data = {
        cont        : cont,
        aux_vectorJ : aux_vectorJ,
        _token      : $('input[name=_token]').val()
    };
    var ruta = '/jefaturaAreaSuc/asignarjefe/';
    ajaxRequest(data,ruta,'asignarjefe');
    $("#myModalJefe").modal('hide');
});

function ajaxRequest(data,url,funcion) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='asignarjefe'){
				if (respuesta.mensaje == "ok") {
					$("#fila"+data['nfila']).remove();
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
            }
		},
		error: function () {
		}
	});
}