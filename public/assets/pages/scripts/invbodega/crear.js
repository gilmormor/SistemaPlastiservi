$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $("#nombre").focus();
    $('.numerico').numeric('.');

    $("#sucursal_id").change(function() {
        if($(this).val() != ''){
            var data = {
                sucursal_id: $(this).val(),
                _token: $('input[name=_token]').val()
            };
            $("#categoriaprod_id").empty();
            $.ajax({
                url: '/categoriaprod/categoriaprodArray',
                type: 'POST',
                data: data,
                success: function (data) {
                    console.log(data);
                    $.each(data, function(id,value){
                        console.log(data[id].id);
                        $("#categoriaprod_id").append('<option value="'+data[id].id+'">'+data[id].nombre+'</option>');
                    });
                    $("#categoriaprod_id").selectpicker('refresh');
                }
            });
        }
    });

});

$(".selectpicker").selectpicker({
	noneSelectedText : "Seleccione...", // by this default "Nothing selected" -->will change to Please Select
	selectAllText : "Selec todo",
	deselectAllText : "Borrar todo",
    size: 8
	});
