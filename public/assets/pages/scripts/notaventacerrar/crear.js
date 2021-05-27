$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    $("#notaventa_id").numeric();
    $( "#notaventa_id" ).focus();

    $('.datepicker').datepicker({
		language: "es",
        autoclose: true,
        clearBtn : true,
		todayHighlight: true
    }).datepicker("setDate");

    consultarcerrarNV(datos());
    $("#btnconsultarcerrarNV").click(function()
    {
        consultarcerrarNV(datos());
    });

});


function configurarTabla(aux_tabla){
    $(aux_tabla).DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        "order"       : [[ 0, "desc" ]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
    });    
}

$("#notaventa_id").focus(function(){
    $('#vistaprevNV').hide();

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
                    $("#observacion").focus();
                    $("#vpnv1").attr("onclick","genpdfNV(" + $("#notaventa_id").val() + ",1)");
                    $("#vpnv2").attr("onclick","genpdfNV(" + $("#notaventa_id").val() + ",1)");
                    $('#vistaprevNV').show();
                    //$("#vistaprevNV").css('display','block');
                    //$('#paso2time').css('display','block');
                }else{
                    aux_mensaje = respuesta.mensaje;
                    if( respuesta.mensaje=="no" ){
                        aux_mensaje = "no existe";
                    }
                    swal({
                        title: 'Nota Venta ' + aux_mensaje,
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
        buscarnotaventa();
    }
});

$("#btnbuscarNotaVenta").click(function(event){
    buscarnotaventa();
});

function buscarnotaventa(){
    $("#notaventa_id").val("");
    $("#myModalBuscarNotaVenta").modal('show');

}

function datos(){
    var data = {
        fechad            : $("#fechad").val(),
        fechah            : $("#fechah").val(),
        fechaestdesp      : $("#fechaestdesp").val(),
        rut               : eliminarFormatoRutret($("#rut").val()),
        vendedor_id       : $("#vendedor_id").val(),
        oc_id             : $("#oc_id").val(),
        giro_id           : $("#giro_id").val(),
        areaproduccion_id : $("#areaproduccion_id").val(),
        tipoentrega_id    : $("#tipoentrega_id").val(),
        notaventa_id      : $("#notaventa_id").val(),
        aprobstatus       : $("#aprobstatus").val(),
        comuna_id         : $("#comuna_id").val(),
        id                : $("#id").val(),
        filtro            : 1,
        _token            : $('input[name=_token]').val()
    };
    return data;
}


function consultarcerrarNV(data){
    $.ajax({
        url: '/despachosol/reportesoldespcerrarNV',
        type: 'POST',
        data: data,
        success: function (datos) {
            if(datos['tabla'].length>0){
                $("#tablaconsulta").html(datos['tabla']);
                configurarTabla('.tablascons');
            }
        }
    });
}

function copiar_notaventaid(id){
	$("#myModalBuscarNotaVenta").modal('hide');
	$("#notaventa_id").val(id);
	//$("#notaventa_id").blur();
    $("#notaventa_id").blur();
	$("#observacion").focus();
}
