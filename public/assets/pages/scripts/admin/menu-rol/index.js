$('#tabla-data1').DataTable({
    'paging'      : true, 
    'lengthChange': true,
    'searching'   : true,
    'ordering'    : true,
    'aaSorting'   : [],
    'info'        : true,
    'autoWidth'   : false,
    "language": {
        //"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
    }
  });

$('.menu_rol').on('change', function () {
    var data = {
        menu_id: $(this).data('menuid'),
        rol_id: $(this).val(),
        _token: $('input[name=_token]').val()
    };
    if ($(this).is(':checked')) {
        data.estado = 1
    } else {
        data.estado = 0
    }
    ajaxRequest('/admin/menu-rol', data);
});

function ajaxRequest (url, data) {
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success: function (respuesta) {
            tipoMensaje = 'warning';
            if(data.estado == 1){
                tipoMensaje = 'success';
            }
            Biblioteca.notificaciones(respuesta.respuesta, 'Plastiservi', tipoMensaje);
        }
    });
} 