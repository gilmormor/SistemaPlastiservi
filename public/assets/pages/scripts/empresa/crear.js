$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    if($("#actsiscob").val() == '1'){
        $("#aux_actsiscob").prop("checked", true);    
    }else{
        $("#aux_actsiscob").prop("checked", false);    
    }
    if($("#stabloxdeusiscob").val() == '1'){
        $("#aux_stabloxdeusiscob").prop("checked", true);    
    }else{
        $("#aux_stabloxdeusiscob").prop("checked", false);    
    }
    $("#iva").numeric();
    $("#diasprorrogacob").numeric({
        decimal: false, 
        negative : 
        false,maxLength: 2 
    }).on('input', function() {
            var value = parseInt($(this).val(), 10); // Convertir el valor a un número entero
            if (isNaN(value)) {
                $(this).val(''); // Si no es un número válido, borrar el valor del campo
            } else if (value > 30) {
                $(this).val('30'); // Si el valor es mayor que 30, establecerlo en 30
            }
    });
    $( "#nombre" ).focus();
});

$("#aux_actsiscob").change(function() {
    estaSeleccionado = $("#aux_actsiscob").is(":checked");
    $("#actsiscob").val('0');
    if(estaSeleccionado){
        $("#actsiscob").val('1');
    }
});
$("#aux_stabloxdeusiscob").change(function() {
    estaSeleccionado = $("#aux_stabloxdeusiscob").is(":checked");
    $("#stabloxdeusiscob").val('0');
    if(estaSeleccionado){
        $("#stabloxdeusiscob").val('1');
    }
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