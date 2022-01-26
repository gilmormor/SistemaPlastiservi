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
    agregarFila(aux_nfilas);

    
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
    var lsSelects = $('#dataTables').find('select[name="sucursal_id"]');
    var lsContienenValor = [];
    
    $.each(lsSelects, function(index, item){
        if($(item).val() != "0"){
            lsContienenValor.push(item);
        }
    });
    console.log(lsContienenValor);
    */
    var lsContienenValor = [];
    $("#dataTables tr .selectsucursal").each(function() {
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
        url: '/sucursal/obtsucursales',
        type: 'POST',
        data: data,
        success: function (respuesta) {
            console.log(respuesta);
            for (i = 0; i < respuesta.length; i++) {
                /*
                $("#sucursal_id" + aux_nfila).append($("<option>", {
                    value: respuesta[i].id,
                    text: respuesta[i].nombre
                }));
                */
                aux_option = aux_option + '<option value="' + respuesta[i]['id'] + '">'+ respuesta[i]['nombre'] + '</option>';
            }
            console.log(aux_option);
            
            var htmlTags = '<tr name="fila'+ aux_nfila + '" id="fila'+ aux_nfila + '">'+
                '<td>'+ 
                    '<select name="sucursal_id[]" id="sucursal_id'+ aux_nfila + '" class="form-control selectpicker selectsucursal" title="Seleccione.." data-live-search="true" required>'+
                    aux_option +
                    '</select>'+
                '</td>'+
                '<td>' +
                    '<input type="text" name="stockmin[]" id="stockmin'+ aux_nfila + '" class="form-control camponumerico" value=""/>'+
                '</td>'+
                '<td>' + 
                    '<input type="text" name="stockmax[]" id="stockmax'+ aux_nfila + '" class="form-control camponumerico" value=""/>'+
                '</td>'+
                '<td>' + 
                    '<input type="text" name="stockubi[]" id="stockubi'+ aux_nfila + '" class="form-control" value=""/>'+
                '</td>'+
                '<td>' + 
                    '<input type="text" name="stock[]" id="stock'+ aux_nfila + '" class="form-control camponumerico" value=""/>'+
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
        $("#sucursal_id"+fila).attr('disabled',true);
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