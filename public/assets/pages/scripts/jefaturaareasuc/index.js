$(document).ready(function () {
    
});

$('#susursal_id').on('change', function () {
    var data = {
        sucursal_id: $(this).val(),
        _token: $('input[name=_token]').val()
    };
    $.ajax({
        url: '/jefaturaAreaSuc/ObtAreas',
        type: 'POST',
        data: data,
        success: function (respuesta) {
            $("#area_id").empty();
            $("#area_id").append("<option value=''>Seleccione...</option>");
            /*$(".comuna_id").empty();
            $(".comuna_id").append("<option value=''>Seleccione...</option>");*/
            $.each(respuesta, function(index,value){
                $("#area_id").append("<option value='" + index + "'>" + value + "</option>")
            });
        }
    });
});