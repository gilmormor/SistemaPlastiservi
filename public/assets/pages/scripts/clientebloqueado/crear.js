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
    $( "#cliente_id" ).focus();
    $("#rut").keyup(function(event){
		if(event.which==113){
			$(this).val("");
			$(".input-sm").val('');
			$("#myModalBusqueda").modal('show');
		}
    });
    $("#btnbuscarcliente").click(function(event){
        $(this).val("");
        $(".input-sm").val('');
        $("#myModalBusqueda").modal('show');
    });
    formato_rut($('#rut'));
});

function copiar_rut(id,rut){
	$("#myModalBusqueda").modal('hide');
    $("#cliente_id").val(id);
    $("#rut").val(rut);
	//$("#rut").focus();
	$("#cliente_id").blur();
	$("#razonsocial").focus();
}

$("#cliente_id").blur(function(){
	codigo = $("#cliente_id").val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		//totalizar();
        var data = {
            id: codigo,
            _token: $('input[name=_token]').val()
        };
        $.ajax({
            url: '/cliente/buscarCliID',
            type: 'POST',
            data: data,
            success: function (respuesta) {
                if(respuesta.length>0){
                    //alert(respuesta[0]['vendedor_id']);
                    $("#rut").val(respuesta[0]['rut']);
                    formato_rut($("#rut"));
                    $("#razonsocial").val(respuesta[0]['razonsocial']);
                    $("#cliente_id").val(respuesta[0]['id'])
                    $( "#descripcion" ).focus();
                }else{
                    swal({
                        title: 'Cliente no existe.',
                        text: "Presione F2 para buscar",
                        icon: 'error',
                        buttons: {
                            confirm: "Aceptar"
                        },
                    }).then((value) => {
                        if (value) {
                            //ajaxRequest(form.serialize(),form.attr('action'),'eliminarusuario',form);
                            $("#cliente_id").focus();
                        }
                    });
                }
            }
        });
	}
});