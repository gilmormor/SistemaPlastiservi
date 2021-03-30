$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $('.tablas').DataTable({
		'paging'      : true, 
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		'autoWidth'   : false,
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
    });
    $("#notaventa_id").numeric();
    $( "#notaventa_id" ).focus();
});



$("#notaventa_id").blur(function(){
    eliminarFormatoRut($(this));
	codigo = $("#notaventa_id").val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		//totalizar();
        var data = {
            id: codigo,
            _token: $('input[name=_token]').val()
        };
        $.ajax({
            url: '/notaventa/buscarNV',
            type: 'POST',
            data: data,
            success: function (respuesta) {
                if(respuesta.mensaje=="ok"){
                    //alert(respuesta[0]['vendedor_id']);
                    //$("#rut").val(respuesta[0]['rut']);
                    /*
                    formato_rut($("#rut"));
                    $("#razonsocial").val(respuesta[0]['razonsocial']);
                    $("#cliente_id").val(respuesta[0]['id']);
                    $("#descripcion").focus();
                    */
                }else{
                    swal({
                        title: 'Nota Venta no existe.',
                        text: "",
                        icon: 'error',
                        buttons: {
                            confirm: "Aceptar"
                        },
                    }).then((value) => {
                        if (value) {
                            //ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
                            $("#notaventa_id").focus();
                        }
                    });
                }
            }
        });
	}
});

$("#notaventa_id").keyup(function(event){
    if(event.which==113){
        $(this).val("");
        $(".input-sm").val('');
        alert('entro');
        $("#myModalBuscarProd").modal('show');
    }
});
