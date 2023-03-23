$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    aux_nfilas=parseInt($("#dataTables >tbody >tr").length);
    //alert(aux_nfilas);
    agregarFila(aux_nfilas);
    $("#agregar_reg").click(function()
    {
        agregarFila(2);
    });

    $("#nombre").focus();
});

function agregarFila(fila) {
    aux_num=parseInt($("#ids").val());
    //alert(aux_num);
    aux_num = aux_num+1;
    aux_nfila = aux_num;
    $("#ids").val(aux_nfila);
    var htmlTags = '<tr name="fila'+ aux_nfila + '" id="fila'+ aux_nfila + '">'+
        '<td>'+ 
            '<input type="text" name="linea_nombre[]" id="linea_nombre'+ aux_nfila + '" class="form-control" value=""/>'+
        '</td>'+
        '<td>' +
            '<input type="text" name="linea_desc[]" id="linea_desc'+ aux_nfila + '" class="form-control" value=""/>'+
        '</td>'+
        '<td>' + 
            '<input type="text" name="linea_obs[]" id="linea_obs'+ aux_nfila + '" class="form-control" value=""/>'+
        '</td>'+
        '<td style="vertical-align:middle;">' + 
            '<a onclick="agregarEliminar('+ aux_nfila +')" class="btn-accion-tabla" title="Agregar" data-original-title="Agregar" id="agregar_reg'+ aux_nfila + '" name="agregar_reg'+ aux_nfila + '" valor="fa-plus">'+
                '<i class="fa fa-fw fa-plus"></i>'+
            '</a>'+
        '</td>'+
        '<td style="display: none">' +
            '<input type="text" name="linea_id[]" id="linea_id'+ aux_nfila + '" class="form-control" value="0"/>'+
        '</td>'+
    '</tr>';
    $('#dataTables tbody').append(htmlTags);
    $("#linea_nombre"+ aux_nfila).focus();
    //$(".camponumerico").numeric();
}

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
        agregarFila(fila);
    }
}
