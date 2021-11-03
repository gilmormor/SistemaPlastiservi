$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    //consultar(datos());
    $('.numerico').numeric('.');
    $("#btnconsultar").click(function()
    {
        consultar(datos());
    });

    $("#btnpdf1").click(function()
    {
        consultarpdf(datos());
    });

    //alert(aux_nfila);
    $('.datepicker').datepicker({
		language: "es",
        autoclose: true,
        clearBtn : true,
		todayHighlight: true
    }).datepicker("setDate");
    
    configurarTabla('.tablas');

});

function configurarTabla(aux_tabla){
    $(aux_tabla).DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        "order"       : [[ 0, "asc" ]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        }
    });    
}

function datos(){
    var data = {
        despachosol_id    : $("#despachosol_id").val(),
        _token            : $('input[name=_token]').val()
    };
    return data;
}

function consultar(data){
    if(verificarDatos())
	{
        $.ajax({
            url: '/reportmovsoldesp/reporte',
            type: 'POST',
            data: data,
            success: function (datos) {
                $("#tablaconsulta").html('');
                if(datos['tabla'].length>0){
                    $("#tablaconsulta").html(datos['tabla']);
                    configurarTabla('.tablascons');
                }else{
                    alertify.error("Información no encontrada.");
                }
            }
        });
    }else{
		alertify.error("Falta incluir informacion");
	}
}
function verificarDatos()
{
	var v1=0;
	v1=validacion('despachosol_id','texto');
	if (v1===false)
	{
		return false;
	}else{
		return true;
	}
}


function consultarpdf(data){
    $.ajax({
        url: '/notaventaconsulta/exportPdf',
        type: 'GET',
        data: data,
        success: function (datos) {
            $("#midiv").html(datos);
            /*
            if(datos['tabla'].length>0){
                $("#tablaconsulta").html(datos['tabla']);
                configurarTabla();
            }
            */
        }
    });
}
