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
    $( "#rut" ).focus();
    $("#rut").focus(function(){
        eliminarFormatoRut($(this));
    });
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
	$("#rut").blur();
	$("#razonsocial").focus();
}

$("#rut").blur(function(){
    eliminarFormatoRut($(this));
	codigo = $("#rut").val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
		//totalizar();
        var data = {
            rut: codigo,
            _token: $('input[name=_token]').val()
        };
        $.ajax({
            url: '/cliente/buscarCliId',
            type: 'POST',
            data: data,
            success: function (respuesta) {
                if(respuesta.length>0){
                    //alert(respuesta[0]['vendedor_id']);
                    //$("#rut").val(respuesta[0]['rut']);
                    formato_rut($("#rut"));
                    $("#razonsocial").val(respuesta[0]['razonsocial']);
                    $("#cliente_id").val(respuesta[0]['id']);
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
                            $("#rut").focus();
                        }
                    });
                }
            }
        });
	}
});