$(document).ready(function () {
    Biblioteca.validacionGeneral('form-general');
    $('#tabla-data').DataTable({
        'paging'      : true, 
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false,
        'processing'  : true,
        'serverSide'  : true,
        'ajax'        : "usuariopage",
        'columns'     : [
            {data: 'id'},
            {data: 'usuario'},
            {data: 'nombre'},
            {data: 'email'},
            {data: 'rol_nombre'},
            //El boton eliminar esta en comentario Gilmer 23/02/2021
            {defaultContent : 
                "<a href='admin/usuario' class='btn-accion-tabla tooltipsC btnEditar action-buttons' title='Editar este registro'>" + 
                    "<i class='fa fa-fw fa-pencil'></i>" + 
                "</a>"+
                "<a href='usuario' class='btn-accion-tabla btnEliminar tooltipsC action-buttons' title='Eliminar este registro'>"+
                    "<i class='fa fa-fw fa-trash text-danger'></i>"+
                "</a>"
            }
        ],
		"language": {
            //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
		"createdRow": function ( row, data, index ) {
            $(row).attr('id','fila' + data.id);
            $(row).attr('name','fila' + data.id);

/*            aux_text =
            `<a href="${data.rutausuario}" class="ver-usuario">${data.usuario}</a>`;*/
            aux_text =
            `<a class="ver-usuario" href="#" onclick="verfoto('${data.rutausuario}',${data.id})">${data.usuario}</a>`;
            $('td', row).eq(1).html(aux_text);
		}
    });

});

function verfoto(url,id){
    const data = {
        usuario_id : id,
        _token: $('input[name=_token]').val()
    }
    ajaxRequest1(data,url,'verUsuario');

}

function ajaxRequest1(data,url,funcion,form = false) {
    dataorig = data;
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success: function (respuesta) {
            if(funcion=='verUsuario'){
                $('#myModal .modal-body').html(respuesta);
                $("#myModal").modal('show');
            }
        },
        error: function () {
        }
    });
}