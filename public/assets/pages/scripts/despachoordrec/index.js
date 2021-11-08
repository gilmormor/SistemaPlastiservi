$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    //alert($("#pantalla").val());
    if($("#pantalla").val()=="0"){
        aux_ajaxini = "despachoordrecpage";
        aux_accion =
            "<a href='despachoordrec/enviaraprorecod' class='btn-accion-tabla btn-sm tooltipsC enviarAproRecOD' title='Enviar para Aprobación'>" +
                "<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>" + 
            "</a>" +
            "<a id='btnEditar' href='despachoordrec' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'> " + 
                "<i class='fa fa-fw fa-pencil'></i>" + 
            "</a>" +
            "<a href='despachoordrec' class='btn-accion-tabla btn-sm btnAnular tooltipsC' title='Anular registro'>" +
                "<span class='glyphicon glyphicon-remove' style='bottom: 0px;top: 2px;'></span>" +
            "</a>"
    }else{
        aux_ajaxini = "despachoordrecpageapr";
        aux_accion =
            "<a href='despachoordrec/aprorecod' class='btn-accion-tabla btn-sm tooltipsC AproRecOD' title='Aprobar Rechazo'>" +
                "<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>" + 
            "</a>"
    }
    $('#tabla-data').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : aux_ajaxini,
        'order': [[ 0, "desc" ]],
        'columns'     : [
            {data: 'id'},
            {data: 'documento_id'},
            {data: 'fechahora'},
            {data: 'notaventa_id'},
            {data: 'despachosol_id'},
            {data: 'despachoord_id'},
            {data: 'razonsocial'},
            {data: 'fechahora_aaaammdd',className:"ocultar"},
            {data: 'documento_file',className:"ocultar"},
            {data: 'aprobstatus',className:"ocultar"},
            {data: 'aprobobs',className:"ocultar"},
            {defaultContent : aux_accion}
        ],
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "createdRow": function ( row, data, index ){
            aux_texto = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Id Rechazo OD' onclick='genpdfODRec(" + data.id + ",1)'>" +
                            data.id +
                        "</a>";
            $('td', row).eq(0).html(aux_texto);

            codigo = data.documento_file;
            if( codigo == null || codigo.length == 0 || /^\s+$/.test(codigo) ){
                aux_texto = "";
            }else{
                aux_texto = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Documento de Rechazo' onclick='verdocadj(\"" + data.documento_file + "\",\"despachorechazo\")'>" +
                                data.documento_id +
                            "</a>";
            }
            $('td', row).eq(1).html(aux_texto);
            $('td', row).eq(2).attr('data-order',data.fechahora_aaaammdd);
            aux_fecha = new Date(data.fechahora_aaaammdd);
            $('td', row).eq(2).html(fechaddmmaaaa(aux_fecha));
            aux_texto = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Nota de Venta' onclick='genpdfNV(" + data.notaventa_id + ",1)'>" +
                            data.notaventa_id +
                        "</a>";
            $('td', row).eq(3).html(aux_texto);
            aux_texto = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Solicitud de Despacho' onclick='genpdfSD(" + data.despachosol_id + ",1)'>" +
                            data.despachosol_id +
                        "</a>";
            $('td', row).eq(4).html(aux_texto);
            aux_texto = "<a class='btn-accion-tabla btn-sm tooltipsC' title='Orden de Despacho' onclick='genpdfOD(" + data.despachoord_id + ",1)'>" +
                            data.despachoord_id +
                        "</a>";
            $('td', row).eq(5).html(aux_texto);
            if($("#pantalla").val()=="0"){
                if(data.aprobstatus == "3"){
                    alert('entro:' + data.aprobstatus);
                    aux_texto =
                    "<a href='despachoordrec/enviaraprorecod' class='btn-accion-tabla btn-sm tooltipsC enviarAproRecOD' title='Enviar a Aprobación: " + data.aprobobs + "'>" +
                        "<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;color:red;'></span>" + 
                    "</a>" +
                    "<a id='btnEditar' href='despachoordrec' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'> " + 
                        "<i class='fa fa-fw fa-pencil'></i>" + 
                    "</a>" +
                    "<a href='despachoordrec' class='btn-accion-tabla btn-sm btnAnular tooltipsC' title='Anular registro'>" +
                        "<span class='glyphicon glyphicon-remove' style='bottom: 0px;top: 2px;'></span>" +
                    "</a>"
                    $('td', row).eq(11).html(aux_texto);
                }
            }
        }
    });
});

/*
function enviarAproRecOD(id){ //Eniar a Aprobacion Rechazo de orden de despacho
	var data = {
		id: id,
        _token: $('input[name=_token]').val()
	};
	var ruta = '/despachoordrec/enviaraprorecod';
	swal({
		title: '¿ Está seguro que desea enviar a aprobacion ?',
		text: "Esta acción no se puede deshacer!",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			ajaxRequest(data,ruta,'eliminar');
		}
	});
}
*/
$(document).on("click", ".enviarAproRecOD", function(event){
    event.preventDefault();
    swal({
        title: '¿ Está seguro que desea enviar papa aprobacion ?',
        text: "Esta acción no se puede deshacer!",
        icon: 'warning',
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
        fila = $(this).closest("tr");
        form = $(this);
        id = fila.find('td:eq(0)').text();
        //alert(id);
        var data = {
            _token  : $('input[name=_token]').val(),
            id      : id
        };
        if (value) {
            ajaxRequest(data,form.attr('href'),'eliminar',form);
        }
    });
    
});

$(document).on("click", ".AproRecOD", function(event){
    event.preventDefault();
    fila = $(this).closest("tr");
    form = $(this);
    id = fila.find('td:eq(0)').text();
    $("#id").val(id);
    $("#myModalaprobcot").modal('show');
    /*
    swal({
        title: '¿ Está seguro que desea enviar a aprobacion ?',
        text: "Esta acción no se puede deshacer!",
        icon: 'warning',
        buttons: {
            cancel: "Cancelar",
            confirm: "Aceptar"
        },
    }).then((value) => {
        fila = $(this).closest("tr");
        form = $(this);
        id = fila.find('td:eq(0)').text();
        var data = {
            _token  : $('input[name=_token]').val(),
            id      : id
        };
        if (value) {
            ajaxRequest(data,form.attr('href'),'eliminar',form);
        }
    });
    */
});

$("#btnaprobarM").click(function(event)
{
	event.preventDefault();
	//alert($('input[name=_token]').val());
	var data = {
		id    : $("#id").val(),
		valor : 2,
		obs   : $("#aprobobs").val(),
        _token: $('input[name=_token]').val()
	};
	var ruta = "/despachoordrec/aprorecod";
	swal({
		title: '¿ Está seguro que desea Aprobar ?',
		text: "Esta acción no se puede deshacer!",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			ajaxRequest(data,form.attr('href'),'eliminar',form);
            $("#myModalaprobcot").modal("hide");
		}
	});
});

$("#btnrechazarM").click(function(event)
{
	event.preventDefault();
	//alert($('input[name=_token]').val());
	var data = {
		id    : $("#id").val(),
		valor : 3,
		obs   : $("#aprobobs").val(),
        _token: $('input[name=_token]').val()
	};
	var ruta = "/despachoordrec/aprorecod";
	swal({
		title: '¿ Está seguro que desea Rechazar ?',
		text: "Esta acción no se puede deshacer!",
		icon: 'warning',
		buttons: {
			cancel: "Cancelar",
			confirm: "Aceptar"
		},
	}).then((value) => {
		if (value) {
			ajaxRequest(data,form.attr('href'),'eliminar',form);
            $("#myModalaprobcot").modal("hide");
		}
	});
});

