$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    $('#tabla-data-cotizacion').DataTable({
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

    function generateBarcode(codbar,i){
        var value = codbar; //"7626513231424"; //$("#barcodeValue").val();
        var btype = "ean13" //$("input[name=btype]:checked").val();
        var renderer = "css"; //$("input[name=renderer]:checked").val();

        var settings = {
            output:renderer,
            bgColor: "#FFFFFF", // $("#bgColor").val(),
            color: "#000000", // $("#color").val(),
            barWidth: "1", //$("#barWidth").val(),
            barHeight: "50", //$("#barHeight").val(),
            moduleSize: "3", //$("#moduleSize").val(),
            posX: "5", //$("#posX").val(),
            posY: "5", //$("#posY").val(),
            addQuietZone: "1" //$("#quietZoneSize").val()
        };
        /*
        if ($("#rectangular").is(':checked') || $("#rectangular").attr('checked')){
            value = {code:value, rect: true};
        }
        */
        //alert(value);
        if (renderer == 'canvas'){
            clearCanvas();
            $("#barcodeTarget").hide();
            $("#canvasTarget").show().barcode(value, btype, settings);
        } else {
            $("#canvasTarget").hide();
            $("#barcodeTarget" + i).html("").show().barcode(value, btype, settings);
        }
    }

    $("#btnGuardarM").click(function()
    {
        generateBarcode();
    });

    aux_nfila=parseInt($("#tabla-data >tbody >tr").length);
    for(i=1; i<=aux_nfila; i++){
        codbar = $("#barcodeTarget" + i).html();
        generateBarcode(codbar,i);
    }
    //alert(aux_nfila);

});

function aprobarcotvend(i,id,aprobstatus){
	event.preventDefault();
	//alert($('input[name=_token]').val());
	var data = {
		id: id,
        nfila : i,
        aprobstatus : aprobstatus,
        _token: $('input[name=_token]').val()
	};
	var ruta = '/cotizacion/aprobarcotvend/'+i;
	swal({
		title: '¿ Está seguro que desea hacer nota de venta ?',
		text: "Esta acción no se puede deshacer!",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			ajaxRequest(data,ruta,'aprobarcotvend');
		}
	});
}
function ajaxRequest(data,url,funcion) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='aprobarcotvend'){
				if (respuesta.mensaje == "ok") {
					$("#fila"+data['nfila']).remove();
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
			}
		},
		error: function () {
		}
	});
}
