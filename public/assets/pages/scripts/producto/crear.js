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
    agregarFila(aux_nfilas);
});
$("#peso").blur(function(){
    calcular_precio();
});

function calcular_precio(){
    aux_precio = $(".categoriaprod_id option:selected").attr('precio');
    aux_precioneto = aux_precio * $("#peso").val();
    $("#precioneto").val(Math.round(aux_precioneto));
}

function agregarFila(fila) {
    aux_num=fila; //parseInt($("#ids").val());
    //alert(aux_num);
    aux_num=aux_num+1;
    aux_nfila=aux_num;
    $("#ids").val(aux_nfila);

//    '<input type="text" name="bod_desc[]" id="bod_desc'+ aux_nfila + '" class="form-control" value=""/>'+
/*
    var lsSelects = $('#dataTables').find('select[name="invbodega_id"]');
    var lsContienenValor = [];
    
    $.each(lsSelects, function(index, item){
        if($(item).val() != "0"){
            lsContienenValor.push(item);
        }
    });
    console.log(lsContienenValor);
    */
    var lsContienenValor = [];
    $("#dataTables tr .selectbodega_id").each(function() {
        var seleccion= $(this).children("option:selected").val();
        if(seleccion){
            lsContienenValor.push(seleccion);
        }
	});
    
    var data = {
        array_excluirid: JSON.stringify(lsContienenValor),
        categoriaprod_id : $("#categoriaprod_id").children("option:selected").val(),
        _token: $('input[name=_token]').val()
    };
    aux_option= "";
    $.ajax({
        url: '/invbodega/obtbodegasxsucursal',
        type: 'POST',
        data: data,
        success: function (respuesta) {
            console.log(respuesta);
            for (i = 0; i < respuesta.length; i++) {
                /*
                $("#invbodega_id" + aux_nfila).append($("<option>", {
                    value: respuesta[i].id,
                    text: respuesta[i].nombre
                }));
                */
                aux_option = aux_option + '<option value="' + respuesta[i]['id'] + '">'+ respuesta[i]['nombre'] + "/" + respuesta[i]['bod_desc'] + '</option>';
            }
            console.log(aux_option);
            
            var htmlTags = '<tr name="fila'+ aux_nfila + '" id="fila'+ aux_nfila + '">'+
                '<td>'+ 
                    '<input type="text" name="invstock_id[]" id="invstock_id'+ aux_nfila + '" class="form-control" value="" style="display:none"/>' +
                    '<input type="text" name="invbodega_id[]" id="invbodega_id'+ aux_nfila + '" class="form-control" value="" style="display:none"/>'+
                    '<select name="invbodega_idtmp[]" id="invbodega_idtmp'+ aux_nfila + '" class="form-control selectpicker selectbodega_id" title="Seleccione..." data-live-search="true" onchange="myFunction('+ aux_nfila +')" required>'+
                        aux_option +
                    '</select>'+
                '</td>'+
                '<td>' + 
                    '<input type="text" name="stock[]" id="stock'+ aux_nfila + '" class="form-control camponumerico" value="0.00" disabled style="text-align:right"/>'+
                '</td>'+
                '<td style="vertical-align:middle;">' + 
                    '<a onclick="agregarEliminar('+ aux_nfila +')" class="btn-accion-tabla" title="Agregar" data-original-title="Agregar" id="agregar_reg'+ aux_nfila + '" name="agregar_reg'+ aux_nfila + '" valor="fa-plus">'+
                        '<i class="fa fa-fw fa-plus"></i>'+
                    '</a>'+
                '</td>'+
                '<td style="display: none">' +
                    '<input type="text" name="stock_id[]" id="stock_id'+ aux_nfila + '" class="form-control" value="0"/>'+
                '</td>'+
            '</tr>';
            $('#dataTables tbody').append(htmlTags);
            $("#bod_desc"+ aux_nfila).focus();
            $(".camponumerico").numeric();
            $(".selectpicker").selectpicker('refresh');
        }
    });
}

function agregarEliminar(fila){
    aux_nfila=parseInt($("#dataTables >tbody >tr").length);
    if(aux_nfila>=1){
        aux_valorboton = $("#agregar_reg"+fila).attr("data-original-title");
        if(aux_valorboton=='Eliminar'){
            $("#agregar_reg"+fila).attr("data-original-title", "");
            $("#agregar_reg"+fila).children('i').removeClass("fa-minus");
            //$("#agregar_reg"+fila).removeClass("tooltipsC");
            //$("#cla_stadel"+fila).val(1);
            //$("#fila" + fila).fadeOut(2000);
            $("#fila" + fila).remove();
            return 0;
        }
        $("#agregar_reg"+fila).children('i').removeClass("fa-plus");
        $("#agregar_reg"+fila).children('i').addClass("fa-minus");
        $("#agregar_reg"+fila).attr("data-original-title", "Eliminar");
        $("#agregar_reg"+fila).attr("title", "Eliminar");
        $("#invbodega_idtmp"+fila).attr('disabled',true);
        agregarFila(fila)
    }
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