$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $( "#nombre" ).focus();
    /*
    $("#diamextmm").blur(function(){
        //$("#diamextpg").val($(this).val()*0.039370);
        $("#diamextpg").val(mmAPg($(this).val()));
    });
    */
    
    $(".numerico").numeric();

    aux_nfilas=parseInt($("#dataTables >tbody >tr").length);
    //alert(aux_nfilas);
    if($("#aux_sta").val() == 2){
        //agregarFila(aux_nfilas);
    }

    
});


$('.categoriaprod_id').on('change', function () {
    $(".claseprod_id").empty();
    $(".claseprod_id").append("<option value=''>Seleccione...</option>");
    //alert($(this).val());
    var data = {
        categoriaprod_id: $(this).val(),
        _token: $('input[name=_token]').val()
    };
    $.ajax({
        url: '/producto/obtClaseProd',
        type: 'POST',
        data: data,
        success: function (claseprod) {
            calcular_precio();
            for (i = 0; i < claseprod.length; i++) {
                $(".claseprod_id").append("<option value='" + claseprod[i].id + "'>" + claseprod[i].cla_nombre + "</option>");
            }
            /*
            $.each(claseprod, function(index,value){
                $(".claseprod_id").append("<option value='" + index + "'>" + value + "</option>")
            });
            */
        }
    });
    $(".grupoprod_id").empty();
    $(".grupoprod_id").append("<option value=''>Seleccione...</option>");
    $.ajax({
        url: '/producto/obtGrupoProd',
        type: 'POST',
        data: data,
        success: function (grupoprod) {
            calcular_precio();
            for (i = 0; i < grupoprod.length; i++) {
                $(".grupoprod_id").append("<option value='" + grupoprod[i].id + "'>" + grupoprod[i].gru_nombre + "</option>");
            }
        }
    });
    if($("#aux_sta").val() == 1){
        $("#dataTables > tbody").empty();
    }else{
        $("#dataTables").find("tr").last().remove();
    }
});
$("#peso").blur(function(){
    calcular_precio();
});

function calcular_precio(){
    aux_precio = $(".categoriaprod_id option:selected").attr('precio');
    aux_precioneto = aux_precio * $("#peso").val();
    $("#precioneto").val(Math.round(aux_precioneto));
}

$('#annomes').on('change', function () {
    var data = {
        annomes: annomes($('#annomes').val()),
        categoriaprod_id: $("#categoriaprod_idH").val(),
        _token: $('input[name=_token]').val()
    };
    $("#categoriaprod_id").empty();
    $("#categoriaprod_id").append("<option value=''>Seleccione...</option>");
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


function myFunction(i){
    $("#invbodega_id" + i).val($("#invbodega_idtmp" + i + " option:selected").attr('value'));
}