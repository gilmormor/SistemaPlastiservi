$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');

    $('#tabla-data-cotizacion').DataTable({
    'paging'      : true, 
    'lengthChange': true,
    'searching'   : true,
    'ordering'    : true,
    'info'        : true,
    'autoWidth'   : false,
    'processing'  : true,
    'serverSide'  : true,
    'ajax'        : "cotizacionpage",
    "order": [[ 0, "desc" ]],
    'columns'     : [
        {data: 'id'},
        {data: 'fechahora'},
        {data: 'razonsocial'},
        {data: 'aprobstatus',className:"ocultar"},
        {data: 'aprobobs',className:"ocultar"},
        {data: 'contador',className:"ocultar"},
        //El boton eliminar esta en comentario Gilmer 23/02/2021
        {defaultContent : 
            "<a href='cotizacion' class='btn-accion-tabla btn-sm tooltipsC btnEnviarNV' title='Enviar a Nota de venta'>"+
                "<span class='glyphicon glyphicon-floppy-save' style='bottom: 0px;top: 2px;'></span>"+
            "</a>"+
            "<a href='cotizacion' class='btn-accion-tabla tooltipsC btnEditar' title='Editar este registro'>"+
                "<i class='fa fa-fw fa-pencil'></i>"+
            "</a>"+
            "<a href='cotizacion' class='btn-accion-tabla btnEliminar tooltipsC' title='Eliminar este registro'>"+
                "<i class='fa fa-fw fa-trash text-danger'></i>"+
            "</a>"}
    ],
    "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
    },
    "createdRow": function ( row, data, index ) {
        if ( data.contador * 1 > 0 ) {
            //console.log(row);
            ///$('tr').addClass('preciomenor');
            //$('td', row).parent().addClass('preciomenor tooltipsC');
            $('td', row).eq(0).html(
                "<a href='#' class='dropdown-toggle tooltipsC' data-toggle='dropdown' title='Precio menor al valor en tabla'>"+
                $('td', row).eq(0).html()+
                "</a>"
            );
            $('td', row).eq(1).html(
                "<a href='#' class='dropdown-toggle tooltipsC' data-toggle='dropdown' title='Precio menor al valor en tabla'>"+
                $('td', row).eq(1).html()+
                "</a>"
            );
            $('td', row).eq(2).html(
                "<a href='#' class='dropdown-toggle tooltipsC' data-toggle='dropdown' title='Precio menor al valor en tabla'>"+
                $('td', row).eq(2).html()+
                "</a>"
            );
            //$('td', row).parent().prop("title","Precio menor al valor en tabla")
        }
    }
    });


});

$(document).on("click", ".btnEnviarNV", function(event){
    event.preventDefault();
    fila = $(this).closest("tr");
    form = $(this);
    id = fila.find('td:eq(0)').text();
    contador = fila.find('td:eq(5)').text();
    aprobstatus = 1;
    if(contador>0){
        aprobstatus = 2;
    }
    var data = {
		id: id,
        aprobstatus : aprobstatus,
        _token: $('input[name=_token]').val()
	};
	var ruta = '/cotizacion/aprobarcotvend/'+id;
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
			ajaxRequest(data,ruta,'aprobarcotvend',form);
		}
	});
    
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
function ajaxRequest(data,url,funcion,form = false) {
	$.ajax({
		url: url,
		type: 'POST',
		data: data,
		success: function (respuesta) {
			if(funcion=='aprobarcotvend'){
				if (respuesta.mensaje == "ok") {
                    form.parents('tr').remove();
					//$("#fila"+data['nfila']).remove();
					Biblioteca.notificaciones('El registro fue procesado con exito', 'Plastiservi', 'success');
				} else {
					if (respuesta.mensaje == "sp"){
						Biblioteca.notificaciones('Registro no tiene permiso procesar.', 'Plastiservi', 'error');
					}else{
						Biblioteca.notificaciones('El registro no pudo ser procesado, hay recursos usandolo', 'Plastiservi', 'error');
					}
				}
			}
            if(funcion=='eliminar'){
                if (respuesta.mensaje == "ok") {
                    form.parents('tr').remove();
                    Biblioteca.notificaciones('El registro fue eliminado correctamente', 'Plastiservi', 'success');
                } else {
                    if (respuesta.mensaje == "sp"){
                        Biblioteca.notificaciones('Usuario no tiene permiso para eliminar.', 'Plastiservi', 'error');
                    }else{
                        Biblioteca.notificaciones('El registro no pudo ser eliminado, hay recursos usandolo', 'Plastiservi', 'error');
                    }
                }
            }
		},
		error: function () {
		}
	});
}
