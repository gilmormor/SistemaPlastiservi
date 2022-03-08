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
                    //console.log(data);
                    $.each(data, function(id,value){
                        //console.log(data[id].nombre);
                        $("#categoriaprod_id").append('<option value="'+data[id].id+'">'+data[id].nombre+'</option>');
                    });
                }
            });
        }
    });

});
