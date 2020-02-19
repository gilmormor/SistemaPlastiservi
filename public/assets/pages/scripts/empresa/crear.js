$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#iva").numeric();
    $( "#nombre" ).focus();
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