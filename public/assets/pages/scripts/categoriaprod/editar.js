$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    agregarFila(1);
    $("#agregar_reg").click(function()
    {
        agregarFila(2);
    });
    $( "#nombre" ).focus();

});

function agregarFila(fila) {

    aux_num=parseInt($("#ids").val());
    aux_num=aux_num+1;
    aux_nfila=aux_num;
    $("#ids").val(aux_nfila);
    if(aux_nfila>=1){
        aux_valorboton = $("#agregar_reg"+fila).attr("data-original-title");
        if(aux_valorboton=='Eliminar'){
            $("#agregar_reg"+fila).attr("data-original-title", "");
            $("#agregar_reg"+fila).children('i').removeClass("fa-minus");
            //$("#agregar_reg"+fila).removeClass("tooltipsC");
            $("#fila" + fila).remove();
            return 0;
        }
        $("#agregar_reg"+fila).children('i').removeClass("fa-plus");
        $("#agregar_reg"+fila).children('i').addClass("fa-minus");
        $("#agregar_reg"+fila).attr("data-original-title", "Eliminar");
        $("#agregar_reg"+fila).attr("title", "Eliminar");


    }
   
    var htmlTags = '<tr name="fila'+ aux_nfila + '" id="fila'+ aux_nfila + '">'+
        '<td>'+ 
            '<input type="text" name="cla_nombre[]" id="cla_nombre'+ aux_nfila + '" class="form-control" value=""/>'+
        '</td>'+
        '<td>' +
            '<input type="text" name="cla_descripcion[]" id="cla_descripcion'+ aux_nfila + '" class="form-control" value=""/>'+
        '</td>'+
        '<td>' + 
            '<input type="text" name="cla_longitud[]" id="cla_longitud'+ aux_nfila + '" class="form-control" value=""/>'+
        '</td>'+
        '<td>' + 
            '<a onclick="agregarFila('+ aux_nfila +')" class="btn-accion-tabla" title="Agregar" data-original-title="Agregar" id="agregar_reg'+ aux_nfila + '" name="agregar_reg'+ aux_nfila + '" valor="fa-plus">'+
                '<i class="fa fa-fw fa-plus"></i>'+
            '</a>'+
        '</td>'+
    '</tr>';
    $('#dataTables tbody').append(htmlTags);
    $("#cla_nombre"+ aux_nfila).focus();
 }

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