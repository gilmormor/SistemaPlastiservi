$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    //aux_nfilas=parseInt($("#dataTables tr:last td").length);
    aux_nfilas=parseInt($("#dataTables >tbody >tr").length);
    aux_nfilasG=parseInt($("#dataTables >tbody >tr").length);
    //alert(aux_nfilas);
    agregarFila(aux_nfilas);
    agregarFilaG(aux_nfilasG);
    $("#agregar_reg").click(function()
    {
        agregarFila(2);
    });
    $("#nombre").focus();
    $(".camponumerico").numeric();
    /*
    $("#precio").on({
		"focus": function (event) {
			$(event.target).select();
		},
		"keyup": function (event) {
		$(event.target).val(function (index, value ) {
			return value.replace(/\D/g, "")
				.replace(/([0-9])([0-9]{2})$/, '$1.$2')
				.replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ",");
		});
		}
	});
    */
    if($("#mostdatosad").val() == '1'){
        $("#aux_mostdatosad").prop("checked", true);    
    }else{
        $("#aux_mostdatosad").prop("checked", false);    
    }
    if($("#mostunimed").val() == '1'){
        $("#aux_mostunimed").prop("checked", true);    
    }else{
        $("#aux_mostunimed").prop("checked", false);    
    }
});

$("#aux_mostdatosad").change(function() {
    estaSeleccionado = $("#aux_mostdatosad").is(":checked");
    $("#mostdatosad").val('0');
    if(estaSeleccionado){
        $("#mostdatosad").val('1');
    }
});
$("#aux_mostunimed").change(function() {
    estaSeleccionado = $("#aux_mostunimed").is(":checked");
    $("#mostunimed").val('0');
    if(estaSeleccionado){
        $("#mostunimed").val('1');
    }
});

function agregarEliminar(fila){
    aux_nfila=parseInt($("#dataTables >tbody >tr").length);
    if(aux_nfila>=1){
        aux_valorboton = $("#agregar_reg"+fila).attr("data-original-title");
        if(aux_valorboton=='Eliminar'){
            $("#agregar_reg"+fila).attr("data-original-title", "");
            $("#agregar_reg"+fila).children('i').removeClass("fa-minus");
            //$("#agregar_reg"+fila).removeClass("tooltipsC");
            $("#cla_stadel"+fila).val(1);
            //$("#fila" + fila).fadeOut(2000);
            $("#fila" + fila).remove();
            return 0;
        }
        $("#agregar_reg"+fila).children('i').removeClass("fa-plus");
        $("#agregar_reg"+fila).children('i').addClass("fa-minus");
        $("#agregar_reg"+fila).attr("data-original-title", "Eliminar");
        $("#agregar_reg"+fila).attr("title", "Eliminar");
        agregarFila(fila)
    }
}

function agregarEliminarG(fila){
    aux_nfila=parseInt($("#tablagrupos >tbody >tr").length);
    if(aux_nfila>=1){
        aux_valorboton = $("#agregar_regG"+fila).attr("data-original-title");
        if(aux_valorboton=='Eliminar'){
            $("#agregar_regG"+fila).attr("data-original-title", "");
            $("#agregar_regG"+fila).children('i').removeClass("fa-minus");
            //$("#agregar_reg"+fila).removeClass("tooltipsC");
            $("#cla_stadel"+fila).val(1);
            //$("#fila" + fila).fadeOut(2000);
            $("#filaG" + fila).remove();
            return 0;
        }
        $("#agregar_regG"+fila).children('i').removeClass("fa-plus");
        $("#agregar_regG"+fila).children('i').addClass("fa-minus");
        $("#agregar_regG"+fila).attr("data-original-title", "Eliminar");
        $("#agregar_regG"+fila).attr("title", "Eliminar");
        agregarFilaG(fila)
    }
}

function agregarFila(fila) {
    aux_num=parseInt($("#ids").val());
    //alert(aux_num);
    aux_num=aux_num+1;
    aux_nfila=aux_num;
    $("#ids").val(aux_nfila);

    /*
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
    */
   
    var htmlTags = '<tr name="fila'+ aux_nfila + '" id="fila'+ aux_nfila + '">'+
        '<td>'+ 
            '<input type="text" name="cla_nombre[]" id="cla_nombre'+ aux_nfila + '" class="form-control" value=""/>'+
        '</td>'+
        '<td>' +
            '<input type="text" name="cla_descripcion[]" id="cla_descripcion'+ aux_nfila + '" class="form-control" value=""/>'+
        '</td>'+
        '<td>' + 
            '<input type="text" name="cla_longitud[]" id="cla_longitud'+ aux_nfila + '" class="form-control camponumerico" value=""/>'+
        '</td>'+
        '<td style="vertical-align:middle;">' + 
            '<a onclick="agregarEliminar('+ aux_nfila +')" class="btn-accion-tabla" title="Agregar" data-original-title="Agregar" id="agregar_reg'+ aux_nfila + '" name="agregar_reg'+ aux_nfila + '" valor="fa-plus">'+
                '<i class="fa fa-fw fa-plus"></i>'+
            '</a>'+
        '</td>'+
        '<td style="display: none">' +
            '<input type="text" name="cla_id[]" id="cla_id'+ aux_nfila + '" class="form-control" value="0"/>'+
        '</td>'+
    '</tr>';
    $('#dataTables tbody').append(htmlTags);
    $("#cla_nombre"+ aux_nfila).focus();
    $(".camponumerico").numeric();
}

function agregarFilaG(fila) {
    aux_num=parseInt($("#idsG").val());
    //alert(aux_num);
    aux_num=aux_num+1;
    aux_nfila=aux_num;
    $("#idsG").val(aux_nfila);

    var htmlTags = '<tr name="filaG'+ aux_nfila + '" id="filaG'+ aux_nfila + '">'+
        '<td>'+ 
            '<input type="text" name="gru_nombre[]" id="gru_nombre'+ aux_nfila + '" class="form-control" value=""/>'+
        '</td>'+
        '<td>' +
            '<input type="text" name="gru_descripcion[]" id="gru_descripcion'+ aux_nfila + '" class="form-control" value=""/>'+
        '</td>'+
        '<td style="vertical-align:middle;">' + 
            '<a onclick="agregarEliminarG('+ aux_nfila +')" class="btn-accion-tabla" title="Agregar" data-original-title="Agregar" id="agregar_regG'+ aux_nfila + '" name="agregar_regG'+ aux_nfila + '" valor="fa-plus">'+
                '<i class="fa fa-fw fa-plus"></i>'+
            '</a>'+
        '</td>'+
        '<td style="display: none">' +
            '<input type="text" name="gru_id[]" id="gru_id'+ aux_nfila + '" class="form-control" value="0"/>'+
        '</td>'+
    '</tr>';
    $('#tablagrupos tbody').append(htmlTags);
    $("#gru_nombre"+ aux_nfila).focus();
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