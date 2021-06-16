$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    $('.date-picker').datepicker({
        language: "es",
        format: "MM yyyy",
        viewMode: "years", 
        minViewMode: "months",
        autoclose: true,
        clearBtn : true,
		todayHighlight: true
    }).datepicker("setDate");
    $("#costoV").val(MASK(0, $("#costoV").attr('valor'), '-###,###,###,##0.00',1));
    $("#metacomerkgV").val(MASK(0, $("#metacomerkgV").attr('valor'), '-###,###,###,##0.00',1));
    //$("#annomes").focus();
});

$('#annomes').on('change', function () {
    var data = {
        annomes: annomes($('#annomes').val()),
        categoriaprod_id: $("#categoriaprod_idH").val(),
        _token: $('input[name=_token]').val()
    };
    $("#categoriaprod_id").empty();
    $("#categoriaprod_id").append("<option value=''>Seleccione...</option>");
    $(".grupoprod_id").empty();
    $(".grupoprod_id").append("<option value=''>Seleccione...</option>");
    $.ajax({
        url: '/categoriagrupovalmesfilcat',
        type: 'POST',
        data: data,
        success: function (respuesta) {
            for (i = 0; i < respuesta.length; i++) {
                //alert(i);
                $("#categoriaprod_id").append($("<option>", {
                    value: respuesta[i].id,
                    text: respuesta[i].nombre
                  }));
            }
        }
    });
});

$('.categoriaprod_id').on('change', function () {
    var data = {
        id     : $("#idH").val(),
        annomes:          annomes($('#annomes').val()),
        categoriaprod_id: $(this).val(),
        _token:           $('input[name=_token]').val()
    };
    $(".grupoprod_id").empty();
    $(".grupoprod_id").append("<option value=''>Seleccione...</option>");
    $.ajax({
        url: '/categoriagrupovalmesfilgrupos',
        type: 'POST',
        data: data,
        success: function (grupoprod) {
            for (i = 0; i < grupoprod.length; i++) {
                //alert(i);
                $(".grupoprod_id").append($("<option>", {
                    value: grupoprod[i].id,
                    text: grupoprod[i].gru_nombre
                  }));
                //$(".grupoprod_id").append("<option value='" + grupoprod[i].id + "'>" + grupoprod[i].gru_nombre + "</option>");
            }
        }
    });

});

$("#costoV").blur(function(e){
    if($("#costoV").attr('valor') != undefined){
        $("#costo").val($(this).val());
    }
});

$("#metacomerkgV").blur(function(e){
    if($("#metacomerkgV").attr('valor') != undefined){
        $("#metacomerkg").val($(this).val());
    }
});