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
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
	});
    $( "#notaventa_id" ).focus();
    codigo = $("#notaventa_id").val();
	if( !(codigo == null || codigo.length == 0 || /^\s+$/.test(codigo)))
	{
        $("#vpnv1").attr("onclick","genpdfNV(" + $("#notaventa_id").val() + ",1)");
        $("#vpnv2").attr("onclick","genpdfNV(" + $("#notaventa_id").val() + ",1)");
        $('#vistaprevNV').show();
    }

});



$("#notaventa_id").blur(function(){
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
                    $("#descripcion").focus();
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